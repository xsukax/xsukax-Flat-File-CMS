<?php declare(strict_types=1);
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: text/html; charset=UTF-8');

const POSTS_DIR = __DIR__ . '/Posts';
const CONFIG_FILE = __DIR__ . '/../config.php';

if (!is_dir(POSTS_DIR)) { @mkdir(POSTS_DIR, 0775, true); }

function get_config(): array {
  $defaults = [
    'SITE_URL' => 'https://yourdomain.com',
    'SITE_NAME' => 'xsukax Flat-File CMS',
    'SITE_DESC' => 'A modern, elegant flat-file CMS for professional blogs',
    'POSTS_PER_PAGE' => 12,
    'ADMIN_FILE' => '../admin.hash'
  ];
  if (file_exists(CONFIG_FILE)) {
    $config = @include CONFIG_FILE;
    return is_array($config) ? array_merge($defaults, $config) : $defaults;
  }
  return $defaults;
}

function save_config(array $config): bool {
  $content = "<?php\nreturn " . var_export($config, true) . ";\n";
  return @file_put_contents(CONFIG_FILE, $content, LOCK_EX) !== false;
}

$config = get_config();
define('SITE_URL', $config['SITE_URL']);
define('ADMIN_FILE', __DIR__ . '/' . ltrim($config['ADMIN_FILE'], './'));

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
  $meta = ['tags' => [], 'created' => null];
  if (preg_match('/<!--META\s*(.*?)\s*META-->/s', $content, $matches)) {
    $metaLines = explode("\n", trim($matches[1]));
    foreach ($metaLines as $line) {
      if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === 'tags' && $value) {
          $meta['tags'] = array_filter(array_map('trim', explode(',', $value)));
        } elseif ($key === 'created' && $value) {
          $meta['created'] = (int)$value;
        }
      }
    }
  }
  return $meta;
}

function get_post_content(string $content): string {
  return preg_replace('/<!--META\s*.*?\s*META-->/s', '', $content);
}

function create_post_with_meta(string $content, array $tags, ?int $created = null): string {
  $tagsStr = implode(', ', $tags);
  $created = $created ?: time();
  return "<!--META\ntags: $tagsStr\ncreated: $created\nMETA-->\n" . $content;
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
    $rawContent = @file_get_contents($file);
    if (!$rawContent) continue;
    $meta = parse_post_meta($rawContent);
    $time = $meta['created'] ?: @filemtime($file);
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
  
  if ($action === 'save_settings') {
    $newConfig = [
      'SITE_URL' => trim((string)($_POST['site_url'] ?? '')),
      'SITE_NAME' => trim((string)($_POST['site_name'] ?? '')),
      'SITE_DESC' => trim((string)($_POST['site_desc'] ?? '')),
      'POSTS_PER_PAGE' => max(1, (int)($_POST['posts_per_page'] ?? 12)),
      'ADMIN_FILE' => trim((string)($_POST['admin_file'] ?? '../admin.hash'))
    ];
    
    if (save_config($newConfig)) {
      $_SESSION['flash'] = 'Settings saved successfully.';
    } else {
      $_SESSION['flash'] = 'Failed to save settings.';
    }
    redirect('admin.php?settings=1');
  }
  
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
    
    $oldContent = @file_get_contents($path);
    $oldMeta = parse_post_meta($oldContent);
    $created = $oldMeta['created'];
    
    $body = create_post_with_meta($content, $tags, $created);
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
    $created = $meta['created'] ?: @filemtime($f);
    $posts[$n] = [
      'name' => $n,
      'title' => extract_title($cleanContent),
      'tags' => $meta['tags'],
      'date' => $created,
      'size' => @filesize($f)
    ]; 
  }
  uasort($posts, fn($a, $b) => $b['date'] - $a['date']);
}

$dashPage = max(1, (int)($_GET['dpage'] ?? 1));
$postsPerDashPage = 12;
$totalDashPosts = count($posts);
$totalDashPages = max(1, (int)ceil($totalDashPosts / $postsPerDashPage));
$dashPage = min($dashPage, $totalDashPages);
$dashOffset = ($dashPage - 1) * $postsPerDashPage;
$paginatedPosts = array_slice($posts, $dashOffset, $postsPerDashPage, true);

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
<html lang="en" data-color-mode="auto" data-light-theme="light" data-dark-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>xsukax Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php if($page==='edit'): ?><link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet"><?php endif; ?>
<style>
:root{--color-canvas-default:#ffffff;--color-canvas-subtle:#f6f8fa;--color-border-default:#d0d7de;--color-border-muted:#d8dee4;--color-fg-default:#24292f;--color-fg-muted:#57606a;--color-accent-fg:#0969da;--color-accent-emphasis:#0969da;--color-success-fg:#1a7f37;--color-danger-fg:#cf222e;--color-success-emphasis:#1f883d;--color-danger-emphasis:#d1242f;--color-btn-bg:#f6f8fa;--color-btn-border:#d0d7de;--color-btn-hover-bg:#f3f4f6;--color-btn-hover-border:#d1d9e0;--color-btn-primary-bg:#1f883d;--color-btn-primary-text:#ffffff;--color-btn-primary-hover-bg:#1a7f37;--color-btn-danger-bg:#d1242f;--color-btn-danger-text:#ffffff;--color-btn-danger-hover-bg:#c0212c;}
@media (prefers-color-scheme:dark){:root{--color-canvas-default:#0d1117;--color-canvas-subtle:#161b22;--color-border-default:#30363d;--color-border-muted:#21262d;--color-fg-default:#e6edf3;--color-fg-muted:#7d8590;--color-accent-fg:#2f81f7;--color-accent-emphasis:#2f81f7;--color-success-fg:#3fb950;--color-danger-fg:#f85149;--color-success-emphasis:#238636;--color-danger-emphasis:#da3633;--color-btn-bg:#21262d;--color-btn-border:#30363d;--color-btn-hover-bg:#30363d;--color-btn-hover-border:#8b949e;--color-btn-primary-bg:#238636;--color-btn-primary-text:#ffffff;--color-btn-primary-hover-bg:#2ea043;--color-btn-danger-bg:#da3633;--color-btn-danger-text:#ffffff;--color-btn-danger-hover-bg:#b62324;}}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--color-canvas-default);color:var(--color-fg-default);font:400 14px/1.5 -apple-system,BlinkMacSystemFont,'Segoe UI','Noto Sans',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;}
a{color:var(--color-accent-fg);text-decoration:none;}
a:hover{text-decoration:underline;}
.header{background:var(--color-canvas-default);border-bottom:1px solid var(--color-border-default);padding:16px 0;}
.header-inner{max-width:1280px;margin:0 auto;padding:0 32px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-size:16px;font-weight:600;color:var(--color-fg-default);}
.nav{display:flex;gap:8px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:5px 16px;background:var(--color-btn-bg);border:1px solid var(--color-btn-border);border-radius:6px;font-size:14px;font-weight:500;color:var(--color-fg-default);cursor:pointer;text-decoration:none;transition:all 0.2s;}
.btn:hover{background:var(--color-btn-hover-bg);border-color:var(--color-btn-hover-border);text-decoration:none;}
.btn-primary{background:var(--color-btn-primary-bg);border-color:var(--color-btn-primary-bg);color:var(--color-btn-primary-text);}
.btn-primary:hover{background:var(--color-btn-primary-hover-bg);border-color:var(--color-btn-primary-hover-bg);}
.btn-danger{background:var(--color-btn-danger-bg);border-color:var(--color-btn-danger-bg);color:var(--color-btn-danger-text);}
.btn-danger:hover{background:var(--color-btn-danger-hover-bg);border-color:var(--color-btn-danger-hover-bg);}
.btn-sm{padding:3px 12px;font-size:12px;}
.main{max-width:1280px;margin:0 auto;padding:32px;}
.login-container{max-width:340px;margin:80px auto;padding:0;}
.login-box{background:var(--color-canvas-default);border:1px solid var(--color-border-default);border-radius:6px;padding:16px;}
.login-header{text-align:center;margin-bottom:16px;}
.login-header h1{font-size:24px;font-weight:300;letter-spacing:-0.5px;}
.login-body{padding:20px;}
.form-group{margin-bottom:16px;}
.form-label{display:block;margin-bottom:8px;font-weight:600;font-size:14px;}
.form-control{width:100%;padding:5px 12px;border:1px solid var(--color-border-default);border-radius:6px;background:var(--color-canvas-default);color:var(--color-fg-default);font-size:14px;line-height:20px;}
.form-control:focus{outline:none;border-color:var(--color-accent-emphasis);box-shadow:0 0 0 3px rgba(9,105,218,0.3);}
@media (prefers-color-scheme:dark){.form-control:focus{box-shadow:0 0 0 3px rgba(47,129,247,0.3);}}
.form-control[readonly]{background:var(--color-canvas-subtle);color:var(--color-fg-muted);}
.form-hint{font-size:12px;color:var(--color-fg-muted);margin-top:6px;}
.page-header{padding-bottom:16px;margin-bottom:24px;border-bottom:1px solid var(--color-border-default);}
.page-header-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.page-title{font-size:24px;font-weight:400;}
.page-actions{display:flex;gap:8px;flex-wrap:wrap;}
.flash{padding:16px;margin-bottom:16px;border:1px solid;border-radius:6px;}
.flash-success{background:rgba(31,136,61,0.1);border-color:var(--color-success-emphasis);color:var(--color-success-fg);}
.flash-error{background:rgba(209,36,47,0.1);border-color:var(--color-danger-emphasis);color:var(--color-danger-fg);}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{background:var(--color-canvas-default);border:1px solid var(--color-border-default);border-radius:6px;padding:16px;}
.stat-title{font-size:12px;color:var(--color-fg-muted);margin-bottom:4px;}
.stat-value{font-size:24px;font-weight:600;color:var(--color-fg-default);}
.box{background:var(--color-canvas-default);border:1px solid var(--color-border-default);border-radius:6px;margin-bottom:16px;}
.box-header{padding:16px;border-bottom:1px solid var(--color-border-default);display:flex;align-items:center;justify-content:space-between;}
.box-title{font-size:14px;font-weight:600;}
.box-body{padding:16px;}
.table-wrapper{overflow-x:auto;}
.table{width:100%;border-collapse:collapse;}
.table th{text-align:left;padding:8px 16px;font-weight:600;font-size:12px;color:var(--color-fg-muted);border-bottom:1px solid var(--color-border-default);}
.table td{padding:8px 16px;border-top:1px solid var(--color-border-default);}
.table tr:first-child td{border-top:none;}
.table-actions{display:flex;gap:8px;flex-wrap:wrap;}
.post-title-cell{font-weight:500;}
.post-meta{font-size:12px;color:var(--color-fg-muted);margin-top:4px;}
.empty-state{text-align:center;padding:48px 16px;color:var(--color-fg-muted);}
.empty-state h3{font-size:20px;margin-bottom:8px;color:var(--color-fg-default);}
.editor-container{margin:16px 0;}
.editor-wrapper{border:1px solid var(--color-border-default);border-radius:6px;overflow:hidden;}
.ql-toolbar.ql-snow{border:none;border-bottom:1px solid var(--color-border-default);background:var(--color-canvas-subtle);}
.ql-container.ql-snow{border:none;font-size:16px;}
.ql-editor{min-height:400px;}
.editor-footer{margin-top:8px;font-size:12px;color:var(--color-fg-muted);}
.tags-section{margin-bottom:16px;}
.tags-label{font-size:14px;font-weight:600;margin-bottom:8px;display:block;}
.tags-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;padding:16px;background:var(--color-canvas-subtle);border:1px solid var(--color-border-default);border-radius:6px;max-height:200px;overflow-y:auto;}
.tag-checkbox{display:flex;align-items:center;gap:8px;}
.tag-checkbox input{width:16px;height:16px;cursor:pointer;}
.tag-checkbox label{font-size:14px;cursor:pointer;user-select:none;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:100;align-items:center;justify-content:center;}
.modal.show{display:flex;}
.modal-dialog{background:var(--color-canvas-default);border:1px solid var(--color-border-default);border-radius:6px;max-width:440px;width:90%;padding:24px;}
.modal-header{margin-bottom:16px;}
.modal-title{font-size:20px;font-weight:600;}
.modal-body{margin-bottom:24px;color:var(--color-fg-muted);line-height:1.5;}
.modal-footer{display:flex;gap:8px;justify-content:flex-end;}
.pagination{display:flex;gap:8px;align-items:center;justify-content:center;margin:16px 0;flex-wrap:wrap;}
.page-link{padding:5px 12px;background:var(--color-btn-bg);border:1px solid var(--color-btn-border);border-radius:6px;font-size:14px;color:var(--color-fg-default);font-weight:500;text-decoration:none;}
.page-link:hover{background:var(--color-btn-hover-bg);border-color:var(--color-btn-hover-border);text-decoration:none;}
.page-link.active{background:var(--color-accent-emphasis);color:#fff;border-color:var(--color-accent-emphasis);}
.page-link.disabled{opacity:0.5;cursor:not-allowed;pointer-events:none;}
@media (max-width:768px){.main{padding:16px;}.page-header-row{flex-direction:column;align-items:stretch;}.page-actions{flex-direction:column;}.stats-grid{grid-template-columns:1fr;}.table{font-size:12px;}.table th,.table td{padding:8px;}.tags-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="admin.php" class="brand">xsukax Admin</a>
    <?php if($logged): ?>
    <nav class="nav">
      <a href="/" class="btn btn-sm" target="_blank">View Site</a>
      <a href="?logout=1" class="btn btn-sm">Sign out</a>
    </nav>
    <?php endif; ?>
  </div>
</header>

<main class="main">
  <?php if($flash): ?>
  <div class="flash <?=strpos($flash,'successfully')!==false?'flash-success':'flash-error'?>">
    <?=htmlspecialchars($flash)?>
  </div>
  <?php endif; ?>

  <?php if(!$logged): ?>
    <div class="login-container">
      <div class="login-box">
        <div class="login-header">
          <h1>Sign in to xsukax</h1>
        </div>
        <div class="login-body">
          <form method="post">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required autofocus>
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%">Sign in</button>
          </form>
          <p style="text-align:center;margin-top:16px;font-size:12px;color:var(--color-fg-muted)">Default password: admin123</p>
        </div>
      </div>
    </div>

  <?php elseif($page==='settings'): ?>
    <div class="page-header">
      <div class="page-header-row">
        <h1 class="page-title">Settings</h1>
        <a href="admin.php" class="btn">← Back to dashboard</a>
      </div>
    </div>
    
    <div style="max-width:800px">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title">Site Settings</h2>
        </div>
        <div class="box-body">
          <form method="post">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="save_settings">
            <div class="form-group">
              <label class="form-label">Site URL</label>
              <input type="text" name="site_url" class="form-control" value="<?=htmlspecialchars($config['SITE_URL'])?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Site Name</label>
              <input type="text" name="site_name" class="form-control" value="<?=htmlspecialchars($config['SITE_NAME'])?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Site Description</label>
              <input type="text" name="site_desc" class="form-control" value="<?=htmlspecialchars($config['SITE_DESC'])?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Posts Per Page</label>
              <input type="number" name="posts_per_page" class="form-control" value="<?=htmlspecialchars((string)$config['POSTS_PER_PAGE'])?>" min="1" max="100" required>
            </div>
            <div class="form-group">
              <label class="form-label">Admin Hash File Path</label>
              <input type="text" name="admin_file" class="form-control" value="<?=htmlspecialchars($config['ADMIN_FILE'])?>" required>
              <p class="form-hint">Relative to the site root directory</p>
            </div>
            <button class="btn btn-primary" type="submit">Save Settings</button>
          </form>
        </div>
      </div>
      
      <div class="box">
        <div class="box-header">
          <h2 class="box-title">Change Password</h2>
        </div>
        <div class="box-body">
          <form method="post">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
              <label class="form-label">Current Password</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="new_password" class="form-control" required minlength="6">
              <p class="form-hint">Must be at least 6 characters</p>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input type="password" name="confirm_password" class="form-control" required minlength="6">
            </div>
            <button class="btn btn-primary" type="submit">Update Password</button>
          </form>
        </div>
      </div>
    </div>

  <?php elseif($page==='create'): ?>
    <form method="post" id="createForm">
      <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
      <input type="hidden" name="action" value="create">
      
      <div class="page-header">
        <div class="page-header-row">
          <h1 class="page-title">Create new post</h1>
          <div class="page-actions">
            <a href="admin.php" class="btn">Cancel</a>
            <button class="btn btn-primary" type="submit">Create post</button>
          </div>
        </div>
      </div>
      
      <div style="max-width:900px">
        <div class="box">
          <div class="box-body">
            <div class="form-group">
              <label class="form-label">Post title</label>
              <input type="text" name="title" class="form-control" required placeholder="Enter post title" autofocus>
              <p class="form-hint">A URL-friendly slug will be generated from this title</p>
            </div>
            
            <?php if(!empty($allExistingTags)): ?>
            <div class="tags-section">
              <label class="tags-label">Select existing tags</label>
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
              <label class="form-label">Add new tags</label>
              <input type="text" name="new_tags" class="form-control" placeholder="design, technology, tutorial">
              <p class="form-hint">Comma-separated. Will be added to any selected tags above</p>
            </div>
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
      
      <div class="page-header">
        <div class="page-header-row">
          <h1 class="page-title"><?=htmlspecialchars($editTitle)?></h1>
          <div class="page-actions">
            <a href="/?p=<?=rawurlencode($edit)?>" class="btn btn-sm" target="_blank">Preview</a>
            <a href="admin.php" class="btn">Dashboard</a>
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn btn-danger" type="button" onclick="showModal('deleteModal')">Delete</button>
          </div>
        </div>
      </div>
      
      <div style="max-width:1100px">
        <div class="box">
          <div class="box-body">
            <?php if(!empty($allExistingTags)): ?>
            <div class="tags-section">
              <label class="tags-label">Tags</label>
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
              <label class="form-label">Add new tags</label>
              <input type="text" name="new_tags" class="form-control" placeholder="design, technology, tutorial">
              <p class="form-hint">Comma-separated. Will be added to selected tags above</p>
            </div>
            
            <div class="editor-container">
              <div class="editor-wrapper">
                <div id="editor"><?=$editBody?></div>
              </div>
              <div class="editor-footer">
                Created: <?=date('F j, Y \a\t g:i A', $editMeta['created'] ?: @filemtime($editPath))?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    
    <div class="modal" id="deleteModal">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3 class="modal-title">Delete post?</h3>
        </div>
        <div class="modal-body">
          Are you sure you want to delete "<strong><?=htmlspecialchars($editTitle)?></strong>"? This cannot be undone.
        </div>
        <div class="modal-footer">
          <button class="btn" onclick="hideModal('deleteModal')">Cancel</button>
          <form method="post" style="display:inline;">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="slug" value="<?=htmlspecialchars($edit)?>">
            <button class="btn btn-danger" type="submit">Delete post</button>
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
    <div class="page-header">
      <div class="page-header-row">
        <h1 class="page-title">Dashboard</h1>
        <div class="page-actions">
          <a href="?settings=1" class="btn">Settings</a>
          <a href="?create=1" class="btn btn-primary">New post</a>
        </div>
      </div>
    </div>
    
    <?php if($analytics): ?>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-title">Total posts</div>
        <div class="stat-value"><?=$analytics['total']?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Today</div>
        <div class="stat-value"><?=$analytics['today']?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">This week</div>
        <div class="stat-value"><?=$analytics['thisWeek']?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">This month</div>
        <div class="stat-value"><?=$analytics['thisMonth']?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Total size</div>
        <div class="stat-value"><?=format_bytes($analytics['totalSize'])?></div>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="box">
      <div class="box-header">
        <h2 class="box-title">Posts</h2>
      </div>
      
      <?php if(empty($posts)): ?>
        <div class="empty-state">
          <h3>No posts yet</h3>
          <p>Create your first post to get started</p>
        </div>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Size</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($paginatedPosts as $post): ?>
              <tr>
                <td>
                  <div class="post-title-cell"><?=htmlspecialchars($post['title'])?></div>
                  <div class="post-meta">
                    <?=htmlspecialchars($post['name'])?>.xfc
                    <?php if(!empty($post['tags'])): ?>
                      • <?=count($post['tags'])?> tag<?=count($post['tags'])>1?'s':''?>
                    <?php endif; ?>
                  </div>
                </td>
                <td><?=date('M j, Y', $post['date'])?></td>
                <td><?=format_bytes($post['size'])?></td>
                <td>
                  <div class="table-actions">
                    <a href="?edit=<?=rawurlencode($post['name'])?>" class="btn btn-sm">Edit</a>
                    <a href="/?p=<?=rawurlencode($post['name'])?>" class="btn btn-sm" target="_blank">View</a>
                    <button class="btn btn-sm btn-danger" type="button" onclick="showDeleteModal('<?=htmlspecialchars($post['name'], ENT_QUOTES)?>', '<?=htmlspecialchars($post['title'], ENT_QUOTES)?>')">Delete</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <?php if($totalDashPages > 1): ?>
        <div class="pagination">
          <?php if($dashPage > 1): ?>
            <a href="?dpage=<?=$dashPage - 1?>" class="page-link">← Previous</a>
          <?php else: ?>
            <span class="page-link disabled">← Previous</span>
          <?php endif; ?>
          
          <?php for($i = 1; $i <= $totalDashPages; $i++): ?>
            <a href="?dpage=<?=$i?>" class="page-link <?=$i === $dashPage ? 'active' : ''?>"><?=$i?></a>
          <?php endfor; ?>
          
          <?php if($dashPage < $totalDashPages): ?>
            <a href="?dpage=<?=$dashPage + 1?>" class="page-link">Next →</a>
          <?php else: ?>
            <span class="page-link disabled">Next →</span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    
    <div class="modal" id="dashboardDeleteModal">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3 class="modal-title">Delete post?</h3>
        </div>
        <div class="modal-body" id="deleteModalText"></div>
        <div class="modal-footer">
          <button class="btn" onclick="hideModal('dashboardDeleteModal')">Cancel</button>
          <form method="post" id="dashboardDeleteForm" style="display:inline;">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="slug" id="deleteSlug">
            <button class="btn btn-danger" type="submit">Delete post</button>
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
      document.getElementById('deleteModalText').innerHTML = 'Are you sure you want to delete "<strong>' + title + '</strong>"? This cannot be undone.';
      showModal('dashboardDeleteModal');
    }
    </script>
  <?php endif; ?>
</main>
</body>
</html>
