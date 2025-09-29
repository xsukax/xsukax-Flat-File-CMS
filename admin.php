<?php declare(strict_types=1);
session_start();
header('Content-Type: text/html; charset=UTF-8');

const POSTS_DIR = __DIR__ . '/Posts';
const ADMIN_FILE = __DIR__ . '/admin.hash';
if (!is_dir(POSTS_DIR)) { @mkdir(POSTS_DIR, 0775, true); }

function sanitize_slug(string $s): string {
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9\-_]/', '-', $s);
  $s = preg_replace('/-+/', '-', $s);
  $s = trim($s, '-');
  return $s ?: 'home';
}

function safe_post_path(string $slug): ?string {
  $slug = sanitize_slug($slug);
  $path = POSTS_DIR . '/' . $slug . '.md';
  $base = realpath(POSTS_DIR) ?: POSTS_DIR;
  $dir = realpath(dirname($path)) ?: dirname($path);
  if (strpos($dir . DIRECTORY_SEPARATOR, rtrim($base,'/\\') . DIRECTORY_SEPARATOR) !== 0) return null;
  return $path;
}

function redirect(string $to): never { header('Location: '.$to, true, 303); exit; }

function get_admin_hash(): string {
  if (!file_exists(ADMIN_FILE)) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    file_put_contents(ADMIN_FILE, $hash, LOCK_EX);
    @chmod(ADMIN_FILE, 0600);
    return $hash;
  }
  return trim(file_get_contents(ADMIN_FILE));
}

function update_admin_hash(string $password): void {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  file_put_contents(ADMIN_FILE, $hash, LOCK_EX);
  @chmod(ADMIN_FILE, 0600);
}

function extract_title(string $content): string {
  if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
    return trim($matches[1]);
  }
  return 'Untitled Post';
}

function format_bytes(int $bytes): string {
  if ($bytes >= 1024*1024) return round($bytes/(1024*1024), 1) . ' MB';
  if ($bytes >= 1024) return round($bytes/1024, 1) . ' KB';
  return $bytes . ' B';
}

function get_analytics(): array {
  $posts = glob(POSTS_DIR.'/*.md');
  $total = count($posts);
  $totalSize = 0;
  $today = 0;
  $thisWeek = 0;
  $thisMonth = 0;
  $dayStart = strtotime('today');
  $weekStart = strtotime('monday this week');
  $monthStart = strtotime('first day of this month');
  
  foreach ($posts as $file) {
    $time = filemtime($file);
    $totalSize += filesize($file);
    if ($time >= $dayStart) $today++;
    if ($time >= $weekStart) $thisWeek++;
    if ($time >= $monthStart) $thisMonth++;
  }
  
  return ['total' => $total, 'today' => $today, 'thisWeek' => $thisWeek, 'thisMonth' => $thisMonth, 'totalSize' => $totalSize];
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
    
    $body = "# $title\n\nStart writing your post here...";
    file_put_contents($path, str_replace("\r\n","\n",$body), LOCK_EX);
    @chmod($path, 0664);
    $_SESSION['flash']='Post created successfully.';
    redirect('admin.php?edit='.$slug);
  }
  
  if ($action === 'save') {
    $slug = sanitize_slug((string)($_POST['slug'] ?? ''));
    $title = trim((string)($_POST['title'] ?? ''));
    $body = (string)($_POST['body'] ?? '');
    $path = safe_post_path($slug);
    
    if (!$path || !is_file($path)) { 
      $_SESSION['flash']='Post not found.'; 
      redirect('admin.php'); 
    }
    
    if ($title) {
      $body = preg_replace('/^#\s+.*$/m', "# $title", $body, 1);
      if (!preg_match('/^#\s+/', $body)) {
        $body = "# $title\n\n" . $body;
      }
    }
    
    file_put_contents($path, str_replace("\r\n","\n",$body), LOCK_EX);
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
foreach (glob(POSTS_DIR.'/*.md') as $f) { 
  $n = basename($f,'.md'); 
  $content = file_get_contents($f);
  $posts[$n] = [
    'name' => $n,
    'title' => extract_title($content),
    'date' => filemtime($f),
    'size' => filesize($f)
  ]; 
}
uasort($posts, fn($a, $b) => $b['date'] - $a['date']);

$page = '';
if ($logged) {
  if (($_GET['settings'] ?? '') === '1') $page = 'settings';
  elseif (($_GET['create'] ?? '') === '1') $page = 'create';
  elseif (!empty($_GET['edit'])) $page = 'edit';
  else $page = 'dashboard';
}

$edit = ($page === 'edit') ? sanitize_slug((string)($_GET['edit'] ?? '')) : '';
$editPath = $edit ? safe_post_path($edit) : null;
$editBody = ($edit && $editPath && is_file($editPath)) ? (string)file_get_contents($editPath) : '';
$editTitle = $editBody ? extract_title($editBody) : '';
$analytics = $logged ? get_analytics() : null;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>xsukax Admin Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--bg:#ffffff;--fg:#1d1d1f;--muted:#86868b;--link:#007aff;--accent:#007aff;--card:#f5f5f7;--border:#d2d2d7;--code:#f5f5f7;--shadow:0 2px 8px rgba(0,0,0,0.08);--success:#30d158;--danger:#ff3b30;--warning:#ff9500;}
@media (prefers-color-scheme: dark){:root{--bg:#000000;--fg:#f5f5f7;--muted:#86868b;--link:#0a84ff;--accent:#0a84ff;--card:#1c1c1e;--border:#38383a;--code:#2c2c2e;--shadow:0 2px 8px rgba(255,255,255,0.05);}}
*{box-sizing:border-box;}
html,body{height:100%;margin:0;padding:0;}
body{background:var(--bg);color:var(--fg);font:400 14px/1.5 'Inter',-apple-system,BlinkMacSystemFont,sans-serif;-webkit-font-smoothing:antialiased;}
a{color:var(--link);text-decoration:none;transition:opacity 0.2s ease;}
a:hover{opacity:0.8;}
.header{background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;}
@media (prefers-color-scheme: dark){.header{background:rgba(0,0,0,0.8);}}
.header-inner{height:64px;padding:0 24px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-weight:600;font-size:18px;color:var(--fg);}
.brand strong{color:var(--accent);}
.header-actions{display:flex;gap:12px;align-items:center;}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 16px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--fg);cursor:pointer;text-decoration:none;font-weight:500;font-size:14px;transition:all 0.2s ease;white-space:nowrap;}
.btn:hover{background:var(--border);transform:translateY(-1px);}
.btn.primary{background:var(--accent);color:white;border-color:var(--accent);}
.btn.success{background:var(--success);color:white;border-color:var(--success);}
.btn.danger{background:var(--danger);color:white;border-color:var(--danger);}
.btn.small{padding:6px 12px;font-size:12px;}
.btn.large{padding:14px 24px;font-size:16px;font-weight:600;}
.main{padding:24px;max-width:1400px;margin:0 auto;}
.login-form{max-width:400px;margin:80px auto;padding:32px;background:var(--card);border-radius:16px;box-shadow:var(--shadow);border:1px solid var(--border);}
.login-form h2{margin:0 0 24px 0;text-align:center;font-weight:600;font-size:24px;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;margin-bottom:8px;font-weight:500;}
.form-group input,.form-group textarea{width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--fg);font-size:14px;transition:border-color 0.2s ease;font-family:inherit;}
.form-group input:focus,.form-group textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,122,255,0.1);}
.form-group input[readonly]{background:var(--card);color:var(--muted);}
.form-hint{font-size:12px;color:var(--muted);margin-top:4px;}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;}
.page-title{font-size:32px;font-weight:700;margin:0;}
.analytics-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:24px;margin-bottom:40px;}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;text-align:center;transition:transform 0.2s ease;}
.stat-card:hover{transform:translateY(-2px);}
.stat-number{font-size:32px;font-weight:700;color:var(--accent);margin-bottom:8px;line-height:1;}
.stat-label{font-size:13px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:500;}
.content-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:24px;}
.card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.card-title{font-size:18px;font-weight:600;margin:0;}
.posts-table{width:100%;border-collapse:collapse;}
.posts-table th{text-align:left;padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:0.5px;}
.posts-table td{padding:16px;border-bottom:1px solid var(--border);}
.posts-table tr:hover{background:var(--bg);}
.post-title{font-weight:500;color:var(--fg);font-size:16px;}
.post-meta{color:var(--muted);font-size:12px;margin-top:4px;}
.post-actions{display:flex;gap:8px;}
.editor-container{display:grid;grid-template-columns:1fr 1fr;gap:24px;height:calc(100vh - 300px);min-height:500px;}
.editor-panel{display:flex;flex-direction:column;}
.editor-textarea{flex:1;width:100%;padding:16px;border:1px solid var(--border);border-radius:8px;background:var(--code);color:var(--fg);font:14px/1.5 ui-monospace,SFMono-Regular,Monaco,Consolas,monospace;resize:none;transition:border-color 0.2s ease;}
.editor-textarea:focus{outline:none;border-color:var(--accent);}
.preview-panel{border:1px solid var(--border);border-radius:8px;padding:16px;background:var(--card);overflow:auto;}
.editor-header{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:24px;}
.editor-title-section{display:grid;grid-template-columns:1fr auto;gap:16px;align-items:end;}
.editor-actions{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;margin-top:24px;display:flex;gap:12px;align-items:center;justify-content:space-between;}
.flash{margin:0 0 24px 0;padding:12px 16px;border-left:4px solid var(--accent);background:rgba(0,122,255,0.1);border-radius:8px;}
.flash.success{border-left-color:var(--success);background:rgba(48,209,88,0.1);}
.flash.danger{border-left-color:var(--danger);background:rgba(255,59,48,0.1);}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted);}
.empty-state h3{font-size:20px;margin-bottom:12px;color:var(--fg);}
.empty-state p{margin-bottom:20px;}
.actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap;}
.create-form{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:32px;max-width:700px;}
.create-form h3{margin:0 0 24px 0;font-size:20px;font-weight:600;}
.settings-container{max-width:600px;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;}
.modal.show{display:flex;}
.modal-content{background:var(--card);border-radius:16px;padding:32px;max-width:400px;width:90%;box-shadow:0 20px 40px rgba(0,0,0,0.15);}
.modal-title{font-size:18px;font-weight:600;margin:0 0 16px 0;}
.modal-text{color:var(--muted);margin-bottom:24px;line-height:1.5;}
.modal-actions{display:flex;gap:12px;justify-content:flex-end;}
.markdown h1,.markdown h2,.markdown h3{line-height:1.3;margin-top:24px;}
.markdown h1{font-size:28px;font-weight:700;}
.markdown h2{font-size:24px;font-weight:600;}
.markdown h3{font-size:20px;font-weight:600;}
.markdown p{margin:16px 0;line-height:1.6;}
.markdown ul,.markdown ol{padding-left:24px;margin:16px 0;}
.markdown blockquote{margin:16px 0;padding:16px;border-left:4px solid var(--accent);background:rgba(0,122,255,0.1);border-radius:8px;}
.markdown pre{background:var(--code);padding:16px;border-radius:8px;overflow:auto;margin:16px 0;}
.markdown code{background:var(--code);padding:4px 6px;border-radius:4px;font-family:ui-monospace,SFMono-Regular,Monaco,Consolas,monospace;}
.markdown pre code{background:transparent;padding:0;}
.markdown table{width:100%;border-collapse:collapse;margin:16px 0;border-radius:8px;overflow:hidden;border:1px solid var(--border);}
.markdown th,.markdown td{padding:12px;text-align:left;border-bottom:1px solid var(--border);}
.markdown th{background:var(--card);font-weight:600;}
.mermaid{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px;margin:16px 0;overflow:auto;}
@media (max-width:1200px){.editor-container{grid-template-columns:1fr;height:auto;}.editor-textarea{height:400px;}}
@media (max-width:768px){.header-inner{padding:0 16px;}.main{padding:16px;}.page-header{flex-direction:column;align-items:flex-start;gap:16px;}.analytics-grid{grid-template-columns:1fr 1fr;}.card-header{flex-direction:column;align-items:flex-start;gap:12px;}.posts-table{font-size:12px;}.posts-table td{padding:12px 8px;}.post-actions{flex-direction:column;gap:4px;}.editor-title-section{grid-template-columns:1fr;}.editor-actions{flex-direction:column;align-items:stretch;gap:8px;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <div class="brand"><strong>xsukax</strong> Admin</div>
    <?php if ($logged): ?>
      <div class="header-actions">
        <a href="/" class="btn" target="_blank">📖 View Site</a>
        <a href="?logout=1" class="btn">🚪 Logout</a>
      </div>
    <?php endif; ?>
  </div>
</header>

<main class="main">
  <?php if ($flash): ?><div class="flash <?=strpos($flash, 'successfully') !== false ? 'success' : 'danger'?>"><?=htmlspecialchars($flash)?></div><?php endif; ?>
  
  <?php if (!$logged): ?>
    <form method="post" class="login-form">
      <h2>Admin Login</h2>
      <input type="hidden" name="action" value="login">
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter admin password" autofocus>
      </div>
      <button class="btn primary" type="submit" style="width:100%;">Log in</button>
      <p style="text-align:center;margin-top:16px;font-size:12px;color:var(--muted);">Default password: admin123</p>
    </form>

  <?php elseif ($page === 'settings'): ?>
    <div class="page-header">
      <h1 class="page-title">Settings</h1>
      <a href="admin.php" class="btn">← Back to Dashboard</a>
    </div>
    
    <div class="settings-container">
      <div class="content-card">
        <div class="card-header">
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
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required minlength="6">
          </div>
          <button class="btn primary" type="submit">🔐 Change Password</button>
        </form>
      </div>
    </div>

  <?php elseif ($page === 'create'): ?>
    <div class="page-header">
      <h1 class="page-title">Create New Post</h1>
      <a href="admin.php" class="btn">← Back to Dashboard</a>
    </div>
    
    <form method="post" class="create-form">
      <h3>New Post</h3>
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <input type="hidden" name="action" value="create">
      
      <div class="form-group">
        <label>Post Title</label>
        <input type="text" id="post-title" name="title" placeholder="Enter your post title" required autofocus>
        <div class="form-hint">The slug will be generated automatically from the title</div>
      </div>
      
      <div class="form-group">
        <label>Generated Slug</label>
        <input type="text" id="post-slug" readonly placeholder="slug-will-appear-here">
        <div class="form-hint">This is the URL-friendly version of your title</div>
      </div>
      
      <button class="btn primary large" type="submit">✨ Create Post</button>
    </form>

  <?php elseif ($page === 'edit'): ?>
    <div class="page-header">
      <h1 class="page-title">Editing Post</h1>
      <div class="actions">
        <a href="/?p=<?=rawurlencode($edit)?>" class="btn" target="_blank">👁️ View Post</a>
        <a href="admin.php" class="btn">← Back to Dashboard</a>
      </div>
    </div>
    
    <form method="post">
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="slug" value="<?=htmlspecialchars($edit)?>">
      
      <div class="editor-header">
        <div class="editor-title-section">
          <div class="form-group" style="margin:0;">
            <label>Post Title</label>
            <input type="text" name="title" value="<?=htmlspecialchars($editTitle)?>" placeholder="Enter post title" required>
          </div>
          <div style="display:flex;align-items:center;gap:12px;">
            <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;white-space:nowrap;">
              <input type="checkbox" id="live-preview"> Live Preview
            </label>
          </div>
        </div>
      </div>
      
      <div class="content-card">
        <div class="editor-container">
          <div class="editor-panel">
            <h3 style="margin:0 0 12px 0;font-size:14px;color:var(--muted);text-transform:uppercase;">Markdown Editor</h3>
            <textarea class="editor-textarea" id="md-editor" name="body" spellcheck="false" placeholder="Write your post content here using Markdown..."><?=htmlspecialchars($editBody)?></textarea>
          </div>
          <div class="editor-panel">
            <h3 style="margin:0 0 12px 0;font-size:14px;color:var(--muted);text-transform:uppercase;">Live Preview</h3>
            <div class="preview-panel markdown">
              <div id="content"></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="editor-actions">
        <div style="display:flex;gap:12px;">
          <button class="btn success large" type="submit">💾 Save Post</button>
          <button class="btn" type="button" onclick="clearEditor()">🗑️ Clear Content</button>
        </div>
        <div style="color:var(--muted);font-size:12px;">
          Slug: <?=htmlspecialchars($edit)?> • Size: <?=format_bytes(strlen($editBody))?>
        </div>
      </div>
    </form>
    
    <textarea id="md-src" hidden><?=htmlspecialchars($editBody)?></textarea>

  <?php else: ?>
    <div class="page-header">
      <h1 class="page-title">Dashboard</h1>
      <div class="actions">
        <a href="?create=1" class="btn primary">✍️ New Post</a>
        <a href="?settings=1" class="btn">⚙️ Settings</a>
      </div>
    </div>

    <div class="analytics-grid">
      <div class="stat-card">
        <div class="stat-number"><?=$analytics['total']?></div>
        <div class="stat-label">Total Posts</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?=format_bytes($analytics['totalSize'])?></div>
        <div class="stat-label">Total Size</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?=$analytics['thisMonth']?></div>
        <div class="stat-label">This Month</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?=$analytics['thisWeek']?></div>
        <div class="stat-label">This Week</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?=$analytics['today']?></div>
        <div class="stat-label">Today</div>
      </div>
    </div>

    <div class="content-card">
      <div class="card-header">
        <h2 class="card-title">All Posts (<?=count($posts)?>)</h2>
        <a href="?create=1" class="btn primary small">+ New Post</a>
      </div>
      
      <?php if (empty($posts)): ?>
        <div class="empty-state">
          <h3>No posts yet</h3>
          <p>Create your first post to get started with your blog.</p>
          <a href="?create=1" class="btn primary">✍️ Create First Post</a>
        </div>
      <?php else: ?>
        <table class="posts-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Modified</th>
              <th>Size</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $slug => $info): ?>
              <tr>
                <td>
                  <div class="post-title"><?=htmlspecialchars($info['title'])?></div>
                  <div class="post-meta"><?=htmlspecialchars($slug)?></div>
                </td>
                <td><?=date('M j, Y \a\t g:i A', $info['date'])?></td>
                <td><?=format_bytes($info['size'])?></td>
                <td>
                  <div class="post-actions">
                    <a href="?edit=<?=rawurlencode($slug)?>" class="btn small">✏️ Edit</a>
                    <a href="/?p=<?=rawurlencode($slug)?>" class="btn small" target="_blank">👁️ View</a>
                    <button class="btn small danger" onclick="showDeleteModal('<?=htmlspecialchars($slug, ENT_QUOTES)?>', '<?=htmlspecialchars($info['title'], ENT_QUOTES)?>')">🗑️ Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3 class="modal-title">Delete Post</h3>
    <p class="modal-text">Are you sure you want to delete "<span id="deletePostTitle"></span>"? This action cannot be undone.</p>
    <div class="modal-actions">
      <button class="btn" onclick="hideDeleteModal()">Cancel</button>
      <form method="post" style="display:inline;" id="deleteForm">
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="slug" id="deleteSlug">
        <button class="btn danger" type="submit">Delete</button>
      </form>
    </div>
  </div>
</div>

<script>
// Auto-generate slug from title
<?php if ($page === 'create'): ?>
const titleInput = document.getElementById('post-title');
const slugInput = document.getElementById('post-slug');

if(titleInput && slugInput){
  titleInput.addEventListener('input', function(){
    const title = this.value;
    const slug = title
      .toLowerCase()
      .replace(/[^a-z0-9\s\-_]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '');
    slugInput.value = slug || 'untitled';
  });
}
<?php endif; ?>

// Modal functions
function showDeleteModal(slug, title){
  document.getElementById('deletePostTitle').textContent = title;
  document.getElementById('deleteSlug').value = slug;
  document.getElementById('deleteModal').classList.add('show');
}

function hideDeleteModal(){
  document.getElementById('deleteModal').classList.remove('show');
}

document.getElementById('deleteModal').addEventListener('click', function(e){
  if(e.target === this) hideDeleteModal();
});

document.addEventListener('keydown', function(e){
  if(e.key === 'Escape') hideDeleteModal();
});
</script>

<?php if ($logged && $page === 'edit'): ?>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
function render(md){
  if (!window.marked) return;
  marked.setOptions({gfm:true,breaks:true,headerIds:true,mangle:false});
  let html=marked.parse(md||'');
  html=DOMPurify.sanitize(html,{USE_PROFILES:{html:true,svg:true,svgFilters:true}});
  const out=document.getElementById('content');
  if(!out) return;
  out.innerHTML=html;
  
  out.querySelectorAll('pre>code').forEach(code=>{
    const c=code.className||'';
    if(/\b(language-)?mermaid\b/i.test(c)){
      const graph=code.textContent;
      const pre=code.parentElement;
      const div=document.createElement('div');
      div.className='mermaid';
      div.textContent=graph;
      pre.replaceWith(div);
    }
  });
  
  if(window.mermaid){
    const prefersDark=window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches;
    mermaid.initialize({startOnLoad:false,securityLevel:'loose',theme:prefersDark?'dark':'default'});
    mermaid.run();
  }
}

function clearEditor(){
  const editor = document.getElementById('md-editor');
  const preview = document.getElementById('content');
  if(editor && preview){
    editor.value = '';
    preview.innerHTML = '';
  }
}

const src=document.getElementById('md-src');
if(src){ render(src.value||src.textContent||''); }

const live=document.getElementById('live-preview');
const ed=document.getElementById('md-editor');
if(live&&ed){
  live.addEventListener('change',()=>{
    if(live.checked){
      render(ed.value);
      ed.addEventListener('input',onInput);
    } else {
      ed.removeEventListener('input',onInput);
    }
  });
  function onInput(){ render(ed.value); }
}
</script>
<?php endif; ?>
</body>
</html>