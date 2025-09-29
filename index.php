<?php declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');

const POSTS_DIR = __DIR__ . '/Posts';
const SITE_URL = 'https://yourdomain.com';
const SITE_NAME = 'xsukax Flat-File CMS';
const SITE_DESC = 'A modern, elegant flat-file CMS for professional blogs';

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
  $path = POSTS_DIR . '/' . $slug . '.xfc';
  $base = realpath(POSTS_DIR);
  if (!$base) return null;
  $dir = realpath(dirname($path)) ?: dirname($path);
  if (strpos($dir . DIRECTORY_SEPARATOR, $base . DIRECTORY_SEPARATOR) !== 0) return null;
  return $path;
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

function get_post_date(string $file): string {
  return date('F j, Y', @filemtime($file));
}

function extract_title(string $content): string {
  if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
    return strip_tags($matches[1]);
  }
  return 'Untitled Post';
}

function extract_excerpt(string $content, int $length = 160): string {
  $content = strip_tags($content);
  $content = preg_replace('/\s+/', ' ', trim($content));
  return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
}

function get_all_posts(?string $tagFilter = null): array {
  $posts = [];
  $files = @glob(POSTS_DIR . '/*.xfc');
  if (!$files) return [];
  
  foreach ($files as $f) {
    if (!is_file($f)) continue;
    $name = basename($f, '.xfc');
    $postContent = @file_get_contents($f);
    if ($postContent === false) continue;
    
    $meta = parse_post_meta($postContent);
    $cleanContent = get_post_content($postContent);
    
    if ($tagFilter && !in_array($tagFilter, $meta['tags'])) continue;
    
    $posts[] = [
      'slug' => $name,
      'title' => extract_title($cleanContent),
      'content' => $cleanContent,
      'tags' => $meta['tags'],
      'date' => get_post_date($f),
      'excerpt' => extract_excerpt($cleanContent),
      'timestamp' => @filemtime($f),
      'url' => SITE_URL . '/?p=' . urlencode($name)
    ];
  }
  usort($posts, fn($a, $b) => $b['timestamp'] - $a['timestamp']);
  return $posts;
}

// Sitemap
if (isset($_SERVER['REQUEST_URI']) && preg_match('/sitemap\.xml$/i', $_SERVER['REQUEST_URI'])) {
  header('Content-Type: application/xml; charset=UTF-8');
  $posts = get_all_posts();
  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
  echo '  <url><loc>' . htmlspecialchars(SITE_URL . '/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>' . "\n";
  foreach ($posts as $post) {
    echo '  <url><loc>' . htmlspecialchars($post['url']) . '</loc><lastmod>' . date('Y-m-d', $post['timestamp']) . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
  }
  echo '</urlset>';
  exit;
}

// RSS
if (isset($_SERVER['REQUEST_URI']) && preg_match('/(rss|feed)\.xml$/i', $_SERVER['REQUEST_URI'])) {
  header('Content-Type: application/rss+xml; charset=UTF-8');
  $posts = array_slice(get_all_posts(), 0, 20);
  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>' . "\n";
  echo '  <title>' . htmlspecialchars(SITE_NAME) . '</title><link>' . htmlspecialchars(SITE_URL) . '</link>' . "\n";
  echo '  <description>' . htmlspecialchars(SITE_DESC) . '</description><language>en-us</language>' . "\n";
  foreach ($posts as $post) {
    echo '  <item><title>' . htmlspecialchars($post['title']) . '</title><link>' . htmlspecialchars($post['url']) . '</link>' . "\n";
    echo '    <guid>' . htmlspecialchars($post['url']) . '</guid><description>' . htmlspecialchars($post['excerpt']) . '</description>' . "\n";
    echo '    <pubDate>' . date('r', $post['timestamp']) . '</pubDate></item>' . "\n";
  }
  echo '</channel></rss>';
  exit;
}

header('Content-Type: text/html; charset=UTF-8');

$slug = sanitize_slug($_GET['p'] ?? '');
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$is_home = empty($slug) || $slug === 'home';
$file = null;
$content = '';
$postMeta = [];
$status = 200;

if (!$is_home) {
  $file = safe_post_path($slug);
  if (!$file || !is_file($file)) {
    $status = 404;
    $content = "<h1>Post Not Found</h1><p>The requested post could not be found.</p>";
  } else {
    $rawContent = @file_get_contents($file);
    $postMeta = parse_post_meta($rawContent);
    $content = get_post_content($rawContent);
  }
  http_response_code($status);
}

$posts = get_all_posts($tag ?: null);
$pageTitle = $is_home ? SITE_NAME : extract_title($content) . ' – xsukax';

$allTags = [];
foreach (get_all_posts() as $p) {
  $allTags = array_merge($allTags, $p['tags']);
}
$allTags = array_values(array_unique($allTags));
sort($allTags);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($pageTitle)?></title>
<meta name="description" content="<?=htmlspecialchars($is_home ? SITE_DESC : extract_excerpt($content, 160))?>">
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="<?=SITE_URL?>/rss.xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--bg:#ffffff;--fg:#1d1d1f;--muted:#86868b;--link:#007aff;--accent:#007aff;--card:#f5f5f7;--border:#d2d2d7;--shadow:0 2px 8px rgba(0,0,0,0.08);}
@media (prefers-color-scheme:dark){:root{--bg:#000000;--fg:#f5f5f7;--muted:#86868b;--link:#0a84ff;--accent:#0a84ff;--card:#1c1c1e;--border:#38383a;--shadow:0 2px 8px rgba(255,255,255,0.05);}}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--fg);font:400 16px/1.6 'Inter',-apple-system,sans-serif;-webkit-font-smoothing:antialiased;}
a{color:var(--link);text-decoration:none;transition:opacity 0.2s;}
a:hover{opacity:0.7;}
.header{background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;}
@media (prefers-color-scheme:dark){.header{background:rgba(0,0,0,0.8);}}
.header-inner{max-width:1200px;margin:0 auto;padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-weight:700;font-size:18px;}
.brand strong{color:var(--accent);}
.nav{display:flex;gap:20px;align-items:center;}
.nav a{font-size:14px;font-weight:500;color:var(--fg);}
.admin-btn{background:var(--accent);color:white!important;padding:8px 18px;border-radius:20px;font-weight:600;}
.container{max-width:1200px;margin:0 auto;padding:0 20px;}
.hero{padding:80px 0 60px;text-align:center;}
.hero h1{font-size:48px;font-weight:700;line-height:1.1;margin-bottom:16px;}
.hero p{font-size:20px;color:var(--muted);max-width:600px;margin:0 auto;}
.filters{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;margin:40px 0;}
.filter-section{margin-bottom:0;}
.filter-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);margin-bottom:12px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{padding:6px 14px;background:var(--bg);border:1px solid var(--border);border-radius:16px;font-size:13px;font-weight:500;transition:all 0.2s;}
.chip:hover,.chip.active{background:var(--accent);color:white;border-color:var(--accent);}
.posts{padding:40px 0 80px;}
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:28px;}
.post-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;transition:all 0.3s;}
.post-card:hover{transform:translateY(-4px);box-shadow:0 12px 24px rgba(0,0,0,0.1);}
.post-card h2{font-size:22px;font-weight:600;line-height:1.3;margin-bottom:12px;}
.post-card h2 a{color:var(--fg);}
.post-meta{display:flex;gap:10px;align-items:center;margin-bottom:14px;flex-wrap:wrap;}
.date{font-size:13px;color:var(--muted);font-weight:500;}
.excerpt{color:var(--muted);line-height:1.6;margin-bottom:12px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:4px 10px;background:var(--bg);border:1px solid var(--border);border-radius:12px;font-size:11px;color:var(--muted);font-weight:600;}
.tag:hover{border-color:var(--accent);color:var(--accent);}
.single{padding:60px 0;}
.back{display:inline-flex;align-items:center;gap:6px;margin-bottom:32px;font-weight:500;}
.back:before{content:'←';}
.content{max-width:800px;margin:0 auto;}
.content h1{font-size:42px;font-weight:700;line-height:1.1;margin:0 0 20px;}
.content h2{font-size:32px;font-weight:600;margin:40px 0 20px;}
.content h3{font-size:24px;font-weight:600;margin:32px 0 16px;}
.content p{margin:20px 0;line-height:1.7;font-size:17px;}
.content ul,.content ol{margin:20px 0;padding-left:28px;line-height:1.7;}
.content li{margin:8px 0;}
.content blockquote{margin:28px 0;padding:20px;background:var(--card);border-left:4px solid var(--accent);border-radius:8px;}
.content img{max-width:100%;height:auto;border-radius:12px;margin:24px 0;}
.footer{background:var(--card);border-top:1px solid var(--border);padding:40px 0;margin-top:80px;}
.footer-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;gap:40px;}
.footer-col h3{font-size:16px;font-weight:600;margin-bottom:12px;}
.footer-col p,.footer-col a{color:var(--muted);font-size:14px;line-height:1.8;}
.footer-col a{display:block;}
.footer-col a:hover{color:var(--accent);}
.footer-bottom{text-align:center;color:var(--muted);font-size:13px;margin-top:32px;padding-top:32px;border-top:1px solid var(--border);}
.empty{text-align:center;padding:100px 20px;color:var(--muted);}
.empty h2{font-size:28px;margin-bottom:16px;color:var(--fg);}
.empty p{font-size:16px;margin-bottom:24px;}
.empty a{background:var(--accent);color:white;padding:12px 24px;border-radius:24px;font-weight:600;display:inline-block;}
@media (max-width:768px){.nav{display:none;}.hero h1{font-size:36px;}.hero p{font-size:18px;}.filters{padding:20px;}.filter-chips{gap:6px;}.chip{font-size:12px;padding:5px 12px;}.posts-grid{grid-template-columns:1fr;gap:20px;}.post-card{padding:22px;}.post-card h2{font-size:20px;}.content h1{font-size:32px;}.content h2{font-size:26px;}.content p{font-size:16px;}.footer-inner{flex-direction:column;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><strong>xsukax</strong> CMS</a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">Sitemap</a>
      <a href="/admin.php" class="admin-btn">Admin</a>
    </nav>
  </div>
</header>

<main>
  <?php if($is_home): ?>
    <section class="hero">
      <div class="container">
        <h1>Welcome to <strong>xsukax</strong></h1>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
      </div>
    </section>
    
    <?php if(!empty($allTags)): ?>
    <div class="container">
      <div class="filters">
        <div class="filter-section">
          <div class="filter-title">Filter by Tags</div>
          <div class="filter-chips">
            <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Posts</a>
            <?php foreach(array_slice($allTags,0,15) as $t): ?>
              <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>">#<?=htmlspecialchars($t)?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <section class="posts">
      <div class="container">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No posts yet</h2>
            <p>Start creating amazing content</p>
            <a href="/admin.php">Create First Post</a>
          </div>
        <?php else: ?>
          <div class="posts-grid">
            <?php foreach($posts as $post): ?>
              <article class="post-card">
                <h2><a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a></h2>
                <div class="post-meta">
                  <span class="date"><?=htmlspecialchars($post['date'])?></span>
                </div>
                <div class="excerpt"><?=htmlspecialchars($post['excerpt'])?></div>
                <?php if(!empty($post['tags'])): ?>
                <div class="tags">
                  <?php foreach($post['tags'] as $t): ?>
                    <a href="/?tag=<?=urlencode($t)?>" class="tag">#<?=htmlspecialchars($t)?></a>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php else: ?>
    <section class="single">
      <div class="container">
        <a href="/" class="back">Back to Posts</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:20px;">
            <span class="date"><?=get_post_date($file)?></span>
          </div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:32px;">
            <?php foreach($postMeta['tags'] as $t): ?>
              <a href="/?tag=<?=urlencode($t)?>" class="tag">#<?=htmlspecialchars($t)?></a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </article>
      </div>
    </section>
  <?php endif; ?>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer-inner">
      <div class="footer-col">
        <h3><strong>xsukax</strong> CMS</h3>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
      </div>
      <div class="footer-col">
        <h3>Resources</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; <?=date('Y')?> <strong>xsukax</strong> Flat-File CMS
    </div>
  </div>
</footer>
</body>
</html>
