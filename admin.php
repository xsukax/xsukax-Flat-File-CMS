<?php declare(strict_types=1);
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: text/html; charset=UTF-8');

const POSTS_DIR = __DIR__ . '/Posts';
const ADMIN_FILE = '../admin.hash';
const SITE_URL = 'https://yourdomain.com';

if (!is_dir(POSTS_DIR)) { @mkdir(POSTS_DIR, 0775, true); }

function sanitize_slug(string $s): string {
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9\-_]/', '-', $s);
  $s = preg_replace('/-+/', '-', $s);
  return trim($s, '-') ?: 'home';
}

function safe_post_path(string $slug): ?string {
  $slug = sanitize_slug($slug);
  $path = POSTS_DIR . '/' . $slug . '.xfc';
  $base = realpath(POSTS_DIR) ?: POSTS_DIR;
  $dir = realpath(dirname($path)) ?: dirname($path);
  if (strpos($dir . DIRECTORY_SEPARATOR, rtrim($base,'/\\') . DIRECTORY_SEPARATOR) !== 0) return null;
  return $path;
}

function redirect(string $to): never { header('Location: '.$to, true, 303); exit; }

function get_admin_hash(): string {
  if (!file_exists(ADMIN_FILE)) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    @file_put_contents(ADMIN_FILE, $hash, LOCK_EX);
    @chmod(ADMIN_FILE, 0600);
    return $hash;
  }
  return trim(@file_get_contents(ADMIN_FILE));
}

function update_admin_hash(string $password): void {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  @file_put_contents(ADMIN_FILE, $hash, LOCK_EX);
  @chmod(ADMIN_FILE, 0600);
}

function parse_post_meta(string $content): array {
  $meta = ['tags' => []];
  if (preg_match('/<!--META\s*(.*?)\s*META-->/s', $content, $matches)) {
    $metaLines = explode("\n", trim($matches[1]));
    foreach ($metaLines as $line) {
      if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === 'tags' && $value) {
          $meta['tags'] = array_filter(array_map('trim', explode(',', $value)));
        }
      }
    }
  }
  return $meta;
}

function get_post_content(string $content): string {
  return preg_replace('/<!--META\s*.*?\s*META-->/s', '', $content);
}

function create_post_with_meta(string $content, array $tags): string {
  $tagsStr = implode(', ', $tags);
  return "<!--META\ntags: $tagsStr\nMETA-->\n" . $content;
}

function extract_title(string $content): string {
  if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
    return strip_tags($matches[1]);
  }
  return 'Untitled Post';
}

function format_bytes(int $bytes): string {
  if ($bytes >= 1024*1024) return round($bytes/(1024*1024), 1) . ' MB';
  if ($bytes >= 1024) return round($bytes/1024, 1) . ' KB';
  return $bytes . ' B';
}

function get_analytics(): array {
  $files = @glob(POSTS_DIR.'/*.xfc');
  if (!$files) return ['total'=>0,'today'=>0,'thisWeek'=>0,'thisMonth'=>0,'totalSize'=>0];
  
  $total = count($files);
  $totalSize = 0;
  $today = $thisWeek = $thisMonth = 0;
  $dayStart = strtotime('today');
  $weekStart = strtotime('monday this week');
  $monthStart = strtotime('first day of this month');
  
  foreach ($files as $file) {
    if (!is_file($file)) continue;
    $time = @filemtime($file);
    $totalSize += @filesize($file);
    if ($time >= $dayStart) $today++;
    if ($time >= $weekStart) $thisWeek++;
    if ($time >= $monthStart) $thisMonth++;
  }
  
  return compact('total','today','thisWeek','thisMonth','totalSize');
}

function get_all_existing_tags(): array {
  $allTags = [];
  $files = @glob(POSTS_DIR . '/*.xfc');
  if (!$files) return [];
  
  foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = @file_get_contents($file);
    if ($content) {
      $meta = parse_post_meta($content);
      $allTags = array_merge($allTags, $meta['tags']);
    }
  }
  
  $allTags = array_values(array_unique($allTags));
  sort($allTags);
  return $allTags;
}

if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
function require_csrf(): void { if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) { http_response_code(400); exit('Bad CSRF'); } }

$logged = !empty($_SESSION['admin_ok']);
if (($_GET['logout'] ?? '') === '1') { session_destroy(); redirect('admin.php'); }

if (($_POST['action'] ?? '') === 'login') {
  $password = (string)($_POST['password'] ?? '');
  if (password_verify($password, get_admin_hash())) { 
    session_regenerate_id(true); 
    $_SESSION['admin_ok']=1; 
    redirect('admin.php'); 
  }
  $_SESSION['flash'] = 'Invalid password.'; 
  redirect('admin.php');
}

if ($logged && $_SERVER['REQUEST_METHOD'] === 'POST') {
  require_csrf();
  $action = (string)($_POST['action'] ?? '');
  
  if ($action === 'change_password') {
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    
    if (!password_verify($current, get_admin_hash())) {
      $_SESSION['flash'] = 'Current password is incorrect.';
    } elseif ($new !== $confirm) {
      $_SESSION['flash'] = 'New passwords do not match.';
    } elseif (strlen($new) < 6) {
      $_SESSION['flash'] = 'Password must be at least 6 characters.';
    } else {
      update_admin_hash($new);
      $_SESSION['flash'] = 'Password changed successfully.';
    }
    redirect('admin.php?settings=1');
  }
  
  if ($action === 'create') {
    $title = trim((string)($_POST['title'] ?? ''));
    $selectedTags = $_POST['existing_tags'] ?? [];
    $newTagsStr = trim((string)($_POST['new_tags'] ?? ''));
    
    $tags = array_filter($selectedTags);
    if ($newTagsStr) {
      $newTags = array_filter(array_map('trim', explode(',', $newTagsStr)));
      $tags = array_merge($tags, $newTags);
    }
    $tags = array_values(array_unique($tags));
    
    $slug = sanitize_slug($title);
    
    if (!$title) {
      $_SESSION['flash'] = 'Title is required.';
      redirect('admin.php?create=1');
    }
    
    $path = safe_post_path($slug);
    if (!$path) { $_SESSION['flash']='Invalid title/slug.'; redirect('admin.php?create=1'); }
    if (file_exists($path)) { 
      $_SESSION['flash']='A post with this title already exists.'; 
      redirect('admin.php?edit='.$slug); 
    }
    
    $htmlContent = "<h1>" . htmlspecialchars($title) . "</h1><p>Start writing your post here...</p>";
    $body = create_post_with_meta($htmlContent, $tags);
    @file_put_contents($path, $body, LOCK_EX);
    @chmod($path, 0664);
    $_SESSION['flash']='Post created successfully.';
    redirect('admin.php?edit='.$slug);
  }
  
  if ($action === 'save') {
    $slug = sanitize_slug((string)($_POST['slug'] ?? ''));
    $content = (string)($_POST['content'] ?? '');
    $selectedTags = $_POST['existing_tags'] ?? [];
    $newTagsStr = trim((string)($_POST['new_tags'] ?? ''));
    
    $tags = array_filter($selectedTags);
    if ($newTagsStr) {
      $newTags = array_filter(array_map('trim', explode(',', $newTagsStr)));
      $tags = array_merge($tags, $newTags);
    }
    $tags = array_values(array_unique($tags));
    
    $path = safe_post_path($slug);
    
    if (!$path || !is_file($path)) { 
      $_SESSION['flash']='Post not found.'; 
      redirect('admin.php'); 
    }
    
    $body = create_post_with_meta($content, $tags);
    @file_put_contents($path, $body, LOCK_EX);
    $_SESSION['flash']='Post saved successfully.';
    redirect('admin.php?edit='.$slug);
  }
  
  if ($action === 'delete') {
    $slug = sanitize_slug((string)($_POST['slug'] ?? ''));
    $path = safe_post_path($slug);
    if ($path && is_file($path)) { 
      @unlink($path); 
      $_SESSION['flash']='Post deleted successfully.'; 
    }
    redirect('admin.php');
  }
}

$flash = $_SESSION['flash'] ?? ''; 
unset($_SESSION['flash']);

$posts = [];
$files = @glob(POSTS_DIR.'/*.xfc');
if ($files) {
  foreach ($files as $f) { 
    if (!is_file($f)) continue;
    $n = basename($f,'.xfc'); 
    $rawContent = @file_get_contents($f);
    if (!$rawContent) continue;
    $meta = parse_post_meta($rawContent);
    $cleanContent = get_post_content($rawContent);
    $posts[$n] = [
      'name' => $n,
      'title' => extract_title($cleanContent),
      'tags' => $meta['tags'],
      'date' => @filemtime($f),
      'size' => @filesize($f)
    ]; 
  }
  uasort($posts, fn($a, $b) => $b['date'] - $a['date']);
}

$page = '';
if ($logged) {
  if (($_GET['settings'] ?? '') === '1') $page = 'settings';
  elseif (($_GET['create'] ?? '') === '1') $page = 'create';
  elseif (!empty($_GET['edit'])) $page = 'edit';
  else $page = 'dashboard';
}

$edit = ($page === 'edit') ? sanitize_slug((string)($_GET['edit'] ?? '')) : '';
$editPath = $edit ? safe_post_path($edit) : null;
$editRawBody = ($edit && $editPath && is_file($editPath)) ? @file_get_contents($editPath) : '';
$editMeta = $editRawBody ? parse_post_meta($editRawBody) : ['tags' => []];
$editBody = $editRawBody ? get_post_content($editRawBody) : '';
$editTitle = $editBody ? extract_title($editBody) : '';
$analytics = $logged ? get_analytics() : null;
$allExistingTags = $logged ? get_all_existing_tags() : [];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>xsukax Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php if($page==='edit'): ?><link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet"><?php endif; ?>
<style>
:root{--bg:#fff;--fg:#1d1d1f;--muted:#86868b;--accent:#007aff;--card:#f5f5f7;--border:#d2d2d7;--success:#30d158;--danger:#ff3b30;}
@media (prefers-color-scheme:dark){:root{--bg:#000;--fg:#f5f5f7;--accent:#0a84ff;--card:#1c1c1e;--border:#38383a;}}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--fg);font:400 14px/1.5 'Inter',-apple-system,sans-serif;-webkit-font-smoothing:antialiased;}
a{color:var(--accent);text-decoration:none;}
a:hover{opacity:0.8;}
.header{background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;}
@media (prefers-color-scheme:dark){.header{background:rgba(0,0,0,0.8);}}
.header-inner{height:60px;padding:0 20px;display:flex;align-items:center;justify-content:space-between;max-width:1400px;margin:0 auto;}
.brand{font-weight:700;font-size:16px;}
.nav{display:flex;gap:12px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--fg);cursor:pointer;font-weight:500;font-size:13px;transition:all 0.2s;text-decoration:none;}
.btn:hover{background:var(--border);transform:translateY(-1px);}
.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent);}
.btn.success{background:var(--success);color:#fff;border-color:var(--success);}
.btn.danger{background:var(--danger);color:#fff;border-color:var(--danger);}
.btn.sm{padding:6px 12px;font-size:12px;}
.btn.lg{padding:12px 24px;font-size:15px;font-weight:600;}
.main{padding:24px 20px;max-width:1400px;margin:0 auto;}
.login{max-width:400px;margin:80px auto;padding:32px;background:var(--card);border:1px solid var(--border);border-radius:16px;}
.login h2{font-size:24px;margin-bottom:24px;text-align:center;}
.form-group{margin-bottom:18px;}
.form-group label{display:block;margin-bottom:6px;font-weight:500;font-size:13px;}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--fg);font:inherit;}
.form-group input:focus,.form-group select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,122,255,0.1);}
.form-group input[readonly]{background:var(--card);color:var(--muted);}
.hint{font-size:12px;color:var(--muted);margin-top:4px;}
.page-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:16px;}
.page-title{font-size:28px;font-weight:700;}
.page-actions{display:flex;flex-direction:column;gap:12px;align-items:flex-end;}
.page-actions-row{display:flex;gap:12px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:20px;margin-bottom:32px;}
.stat{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center;}
.stat-num{font-size:28px;font-weight:700;color:var(--accent);margin-bottom:6px;}
.stat-label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;}
.card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:24px;}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.card-title{font-size:18px;font-weight:600;}
.table{width:100%;border-collapse:collapse;}
.table th{text-align:left;padding:12px 14px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;}
.table td{padding:14px;border-bottom:1px solid var(--border);}
.table tr:hover{background:var(--bg);}
.post-title{font-weight:500;font-size:15px;}
.post-meta{color:var(--muted);font-size:12px;margin-top:4px;}
.actions{display:flex;gap:6px;flex-wrap:wrap;}
.editor-wrap{height:500px;background:#fff;border:1px solid var(--border);border-radius:8px;margin:20px 0;}
.ql-toolbar.ql-snow{border:1px solid var(--border);border-bottom:none;border-radius:8px 8px 0 0;background:var(--card);}
.ql-container.ql-snow{border:1px solid var(--border);border-top:none;border-radius:0 0 8px 8px;font-size:16px;}
.ql-editor{min-height:450px;line-height:1.6;}
.editor-footer{color:var(--muted);font-size:12px;margin-top:12px;text-align:right;}
.flash{padding:12px 16px;border-left:4px solid var(--accent);background:rgba(0,122,255,0.1);border-radius:8px;margin-bottom:20px;}
.flash.success{border-left-color:var(--success);background:rgba(48,209,88,0.1);}
.flash.error{border-left-color:var(--danger);background:rgba(255,59,48,0.1);}
.empty{text-align:center;padding:60px 20px;color:var(--muted);}
.empty h3{font-size:20px;margin-bottom:12px;color:var(--fg);}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;}
.modal.show{display:flex;}
.modal-content{background:var(--card);border-radius:16px;padding:32px;max-width:400px;width:90%;border:1px solid var(--border);}
.modal-title{font-size:18px;font-weight:600;margin-bottom:16px;}
.modal-text{color:var(--muted);margin-bottom:24px;line-height:1.6;}
.modal-actions{display:flex;gap:12px;justify-content:flex-end;}
.tags-section{margin-bottom:20px;}
.tags-section-title{font-size:13px;font-weight:600;margin-bottom:12px;color:var(--fg);}
.tags-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;max-height:200px;overflow-y:auto;padding:12px;background:var(--bg);border:1px solid var(--border);border-radius:8px;}
.tag-checkbox{display:flex;align-items:center;gap:6px;}
.tag-checkbox input[type="checkbox"]{width:16px;height:16px;cursor:pointer;}
.tag-checkbox label{font-size:13px;cursor:pointer;user-select:none;}
@media (prefers-color-scheme:dark){.ql-toolbar.ql-snow{background:var(--card);border-color:var(--border);}.ql-container.ql-snow{border-color:var(--border);}.ql-editor{background:var(--bg);color:var(--fg);}}
@media (max-width:768px){.main{padding:16px;}.page-head{flex-direction:column;align-items:flex-start;}.page-actions{align-items:stretch;width:100%;}.page-actions-row{flex-direction:column;}.stats{grid-template-columns:1fr 1fr;gap:12px;}.table{font-size:12px;}.table td{padding:10px 8px;}.actions{flex-direction:column;gap:4px;width:100%;}.tags-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <div class="brand"><strong>xsukax</strong> Admin</div>
    <?php if($logged): ?>
      <nav class="nav">
        <a href="/" class="btn" target="_blank">View Site</a>
        <a href="?logout=1" class="btn">Logout</a>
      </nav>
    <?php endif; ?>
  </div>
</header>

<main class="main">
  <?php if($flash): ?><div class="flash <?=strpos($flash,'successfully')!==false?'success':'error'?>"><?=htmlspecialchars($flash)?></div><?php endif; ?>
  
  <?php if(!$logged): ?>
    <form method="post" class="login">
      <h2>Admin Login</h2>
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter password" autofocus>
      </div>
      <button class="btn primary" type="submit" style="width:100%">Log in</button>
      <p style="text-align:center;margin-top:16px;font-size:12px;color:var(--muted)">Default: admin123</p>
    </form>

  <?php elseif($page==='settings'): ?>
    <div class="page-head">
      <h1 class="page-title">Settings</h1>
      <a href="admin.php" class="btn">← Dashboard</a>
    </div>
    
    <div style="max-width:800px">
      <div class="card">
        <div class="card-head">
          <h2 class="card-title">Change Password</h2>
        </div>
        <form method="post">
          <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
          <input type="hidden" name="action" value="change_password">
          <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required minlength="6">
            <p class="hint">Minimum 6 characters</p>
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required minlength="6">
          </div>
          <button class="btn primary" type="submit">Update Password</button>
        </form>
      </div>
    </div>

  <?php elseif($page==='create'): ?>
    <form method="post" id="createForm">
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <input type="hidden" name="action" value="create">
      
      <div class="page-head">
        <h1 class="page-title">Create New Post</h1>
        <div class="page-actions">
          <div class="page-actions-row">
            <a href="admin.php" class="btn">← Dashboard</a>
          </div>
          <div class="page-actions-row">
            <button class="btn success lg" type="submit">✓ Create Post</button>
          </div>
        </div>
      </div>
      
      <div style="max-width:800px">
        <div class="card">
          <div class="form-group">
            <label>Post Title *</label>
            <input type="text" name="title" required placeholder="Enter post title" autofocus>
            <p class="hint">A URL-friendly slug will be generated from the title</p>
          </div>
          
          <?php if(!empty($allExistingTags)): ?>
          <div class="tags-section">
            <div class="tags-section-title">Select Existing Tags:</div>
            <div class="tags-grid">
              <?php foreach($allExistingTags as $tag): ?>
                <div class="tag-checkbox">
                  <input type="checkbox" name="existing_tags[]" value="<?=htmlspecialchars($tag)?>" id="tag-<?=htmlspecialchars($tag)?>">
                  <label for="tag-<?=htmlspecialchars($tag)?>"><?=htmlspecialchars($tag)?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
          
          <div class="form-group">
            <label>Add New Tags</label>
            <input type="text" name="new_tags" placeholder="technology, design, coding">
            <p class="hint">Comma-separated tags (will be added to existing tags above)</p>
          </div>
        </div>
      </div>
    </form>

  <?php elseif($page==='edit' && $edit && $editPath && is_file($editPath)): ?>
    <form method="post" id="editForm">
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="slug" value="<?=htmlspecialchars($edit)?>">
      <input type="hidden" name="content" id="contentField">
      
      <div class="page-head">
        <h1 class="page-title">Edit: <?=htmlspecialchars($editTitle)?></h1>
        <div class="page-actions">
          <div class="page-actions-row">
            <a href="/?p=<?=rawurlencode($edit)?>" class="btn" target="_blank">👁 Preview</a>
            <a href="admin.php" class="btn">← Dashboard</a>
          </div>
          <div class="page-actions-row">
            <button class="btn success lg" type="submit">✓ Save Changes</button>
            <button class="btn danger lg" type="button" onclick="showModal('deleteModal')">🗑 Delete Post</button>
          </div>
        </div>
      </div>
      
      <div style="max-width:1000px">
        <div class="card">
          <?php if(!empty($allExistingTags)): ?>
          <div class="tags-section">
            <div class="tags-section-title">Select Tags:</div>
            <div class="tags-grid">
              <?php foreach($allExistingTags as $tag): ?>
                <div class="tag-checkbox">
                  <input type="checkbox" name="existing_tags[]" value="<?=htmlspecialchars($tag)?>" id="tag-<?=htmlspecialchars($tag)?>" <?=in_array($tag, $editMeta['tags']) ? 'checked' : ''?>>
                  <label for="tag-<?=htmlspecialchars($tag)?>"><?=htmlspecialchars($tag)?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
          
          <div class="form-group">
            <label>Add New Tags</label>
            <input type="text" name="new_tags" placeholder="technology, design, coding">
            <p class="hint">Comma-separated tags (will be added to selected tags above)</p>
          </div>
          
          <div class="editor-wrap">
            <div id="editor"><?=$editBody?></div>
          </div>
          
          <div class="editor-footer">
            Last modified: <?=date('F j, Y \a\t g:i A', @filemtime($editPath))?>
          </div>
        </div>
      </div>
    </form>
    
    <div class="modal" id="deleteModal">
      <div class="modal-content">
        <h3 class="modal-title">Delete Post?</h3>
        <p class="modal-text">This action cannot be undone. Are you sure you want to delete "<?=htmlspecialchars($editTitle)?>"?</p>
        <div class="modal-actions">
          <button class="btn" onclick="hideModal('deleteModal')">Cancel</button>
          <form method="post" style="display:inline;">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="slug" value="<?=htmlspecialchars($edit)?>">
            <button class="btn danger" type="submit">Yes, Delete</button>
          </form>
        </div>
      </div>
    </div>
    
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
    const quill = new Quill('#editor', {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          ['blockquote', 'code-block'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'script': 'sub'}, { 'script': 'super' }],
          [{ 'indent': '-1'}, { 'indent': '+1' }],
          ['link', 'image'],
          ['clean']
        ]
      }
    });
    
    document.getElementById('editForm').onsubmit = function() {
      document.getElementById('contentField').value = quill.root.innerHTML;
      return true;
    };
    
    function showModal(id) {
      document.getElementById(id).classList.add('show');
    }
    
    function hideModal(id) {
      document.getElementById(id).classList.remove('show');
    }
    </script>

  <?php else: ?>
    <div class="page-head">
      <h1 class="page-title">Dashboard</h1>
      <div style="display:flex;gap:12px;">
        <a href="?settings=1" class="btn">⚙️ Settings</a>
        <a href="?create=1" class="btn primary">+ New Post</a>
      </div>
    </div>
    
    <?php if($analytics): ?>
    <div class="stats">
      <div class="stat">
        <div class="stat-num"><?=$analytics['total']?></div>
        <div class="stat-label">Total Posts</div>
      </div>
      <div class="stat">
        <div class="stat-num"><?=$analytics['today']?></div>
        <div class="stat-label">Today</div>
      </div>
      <div class="stat">
        <div class="stat-num"><?=$analytics['thisWeek']?></div>
        <div class="stat-label">This Week</div>
      </div>
      <div class="stat">
        <div class="stat-num"><?=$analytics['thisMonth']?></div>
        <div class="stat-label">This Month</div>
      </div>
      <div class="stat">
        <div class="stat-num"><?=format_bytes($analytics['totalSize'])?></div>
        <div class="stat-label">Total Size</div>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="card">
      <div class="card-head">
        <h2 class="card-title">All Posts</h2>
      </div>
      
      <?php if(empty($posts)): ?>
        <div class="empty">
          <h3>No posts yet</h3>
          <p>Get started by creating your first post</p>
        </div>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Date</th>
              <th>Size</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($posts as $post): ?>
            <tr>
              <td>
                <div class="post-title"><?=htmlspecialchars($post['title'])?></div>
                <div class="post-meta">
                  <?=htmlspecialchars($post['name'])?>.xfc
                  <?php if(!empty($post['tags'])): ?>
                    • <?=count($post['tags'])?> tags
                  <?php endif; ?>
                </div>
              </td>
              <td><?=date('M j, Y', $post['date'])?></td>
              <td><?=format_bytes($post['size'])?></td>
              <td>
                <div class="actions">
                  <a href="?edit=<?=rawurlencode($post['name'])?>" class="btn sm primary">✏️ Edit</a>
                  <a href="/?p=<?=rawurlencode($post['name'])?>" class="btn sm" target="_blank">👁 View</a>
                  <button class="btn sm danger" type="button" onclick="showDeleteModal('<?=htmlspecialchars($post['name'], ENT_QUOTES)?>', '<?=htmlspecialchars($post['title'], ENT_QUOTES)?>')">🗑 Delete</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    
    <div class="modal" id="dashboardDeleteModal">
      <div class="modal-content">
        <h3 class="modal-title">Delete Post?</h3>
        <p class="modal-text" id="deleteModalText"></p>
        <div class="modal-actions">
          <button class="btn" onclick="hideModal('dashboardDeleteModal')">Cancel</button>
          <form method="post" id="dashboardDeleteForm" style="display:inline;">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="slug" id="deleteSlug">
            <button class="btn danger" type="submit">Yes, Delete</button>
          </form>
        </div>
      </div>
    </div>
    
    <script>
    function showModal(id) {
      document.getElementById(id).classList.add('show');
    }
    
    function hideModal(id) {
      document.getElementById(id).classList.remove('show');
    }
    
    function showDeleteModal(slug, title) {
      document.getElementById('deleteSlug').value = slug;
      document.getElementById('deleteModalText').textContent = 'This action cannot be undone. Are you sure you want to delete "' + title + '"?';
      showModal('dashboardDeleteModal');
    }
    </script>
  <?php endif; ?>
</main>
</body>
</html>
