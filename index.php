<?php declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

const POSTS_DIR = __DIR__ . '/Posts';
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
  $base = realpath(POSTS_DIR);
  if (!$base) return null;
  $dir = realpath(dirname($path)) ?: dirname($path);
  if (strpos($dir . DIRECTORY_SEPARATOR, $base . DIRECTORY_SEPARATOR) !== 0) return null;
  return $path;
}

function get_post_date(string $file): string {
  return date('F j, Y', filemtime($file));
}

function extract_title(string $content): string {
  if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
    return trim($matches[1]);
  }
  return 'Untitled Post';
}

function extract_excerpt(string $content, int $length = 160): string {
  $content = preg_replace('/^#+\s+.*$/m', '', $content);
  $content = strip_tags($content);
  $content = preg_replace('/\s+/', ' ', trim($content));
  return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
}

$slug = sanitize_slug($_GET['p'] ?? '');
$is_home = empty($slug) || $slug === 'home';
$file = null;
$content = '';
$status = 200;

if (!$is_home) {
  $file = safe_post_path($slug);
  if (!$file || !is_file($file)) {
    $status = 404;
    $content = "# Post Not Found\n\nThe requested post could not be found.";
  } else {
    $content = (string)file_get_contents($file);
  }
  http_response_code($status);
}

$posts = [];
foreach (glob(POSTS_DIR . '/*.md') as $f) {
  $name = basename($f, '.md');
  $postContent = file_get_contents($f);
  $posts[] = [
    'slug' => $name,
    'title' => extract_title($postContent),
    'date' => get_post_date($f),
    'excerpt' => extract_excerpt($postContent),
    'timestamp' => filemtime($f)
  ];
}
usort($posts, fn($a, $b) => $b['timestamp'] - $a['timestamp']);

$pageTitle = $is_home ? 'xsukax Flat-File CMS' : extract_title($content) . ' — xsukax';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($pageTitle)?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--bg:#ffffff;--fg:#1d1d1f;--muted:#86868b;--link:#007aff;--accent:#007aff;--card:#f5f5f7;--border:#d2d2d7;--code:#f5f5f7;--shadow:0 2px 8px rgba(0,0,0,0.08);}
@media (prefers-color-scheme: dark){:root{--bg:#000000;--fg:#f5f5f7;--muted:#86868b;--link:#0a84ff;--accent:#0a84ff;--card:#1c1c1e;--border:#38383a;--code:#2c2c2e;--shadow:0 2px 8px rgba(255,255,255,0.05);}}
*{box-sizing:border-box;}
html,body{height:100%;margin:0;padding:0;}
body{background:var(--bg);color:var(--fg);font:400 16px/1.6 'Inter',-apple-system,BlinkMacSystemFont,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;}
a{color:var(--link);text-decoration:none;transition:opacity 0.2s ease;}
a:hover{opacity:0.7;}
.header{background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;}
@media (prefers-color-scheme: dark){.header{background:rgba(0,0,0,0.8);}}
.header-inner{max-width:1200px;margin:0 auto;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-weight:600;font-size:20px;color:var(--fg);}
.brand strong{color:var(--accent);}
.admin-link{background:var(--accent);color:white;padding:10px 20px;border-radius:22px;font-weight:500;font-size:14px;transition:all 0.2s ease;}
.admin-link:hover{opacity:0.9;transform:translateY(-1px);}
.container{max-width:1200px;margin:0 auto;padding:0 24px;}
.hero{padding:120px 0 80px 0;text-align:center;background:linear-gradient(135deg, var(--card) 0%, var(--bg) 100%);}
.hero h1{font-size:56px;font-weight:700;margin:0 0 20px 0;line-height:1.1;letter-spacing:-0.02em;}
.hero p{font-size:22px;color:var(--muted);margin:0;max-width:600px;margin:0 auto;line-height:1.5;}
.posts-section{padding:80px 0;}
.section-title{text-align:center;font-size:36px;font-weight:700;margin-bottom:60px;color:var(--fg);}
.posts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:32px;}
.post-card{background:var(--card);border-radius:20px;padding:32px;box-shadow:var(--shadow);border:1px solid var(--border);transition:all 0.3s ease;}
.post-card:hover{transform:translateY(-8px);box-shadow:0 16px 32px rgba(0,0,0,0.15);}
@media (prefers-color-scheme: dark){.post-card:hover{box-shadow:0 16px 32px rgba(255,255,255,0.1);}}
.post-card h3{margin:0 0 12px 0;font-size:24px;font-weight:600;line-height:1.3;}
.post-card h3 a{color:var(--fg);}
.post-date{color:var(--muted);font-size:14px;margin-bottom:16px;font-weight:500;}
.post-excerpt{color:var(--muted);line-height:1.6;font-size:16px;}
.post-content{padding:80px 0;}
.post-content .markdown{max-width:800px;margin:0 auto;}
.back-link{display:inline-flex;align-items:center;gap:8px;color:var(--link);margin-bottom:40px;font-weight:500;font-size:16px;}
.back-link:before{content:'←';font-size:18px;}
.markdown h1{font-size:48px;font-weight:700;line-height:1.1;margin:0 0 32px 0;color:var(--fg);letter-spacing:-0.02em;}
.markdown h2{font-size:36px;font-weight:600;line-height:1.2;margin:48px 0 24px 0;color:var(--fg);}
.markdown h3{font-size:28px;font-weight:600;line-height:1.3;margin:40px 0 20px 0;color:var(--fg);}
.markdown p{margin:24px 0;line-height:1.7;font-size:18px;}
.markdown ul,.markdown ol{margin:24px 0;padding-left:32px;line-height:1.7;font-size:18px;}
.markdown li{margin:8px 0;}
.markdown blockquote{margin:32px 0;padding:24px;background:var(--card);border-left:4px solid var(--accent);border-radius:12px;font-style:italic;}
.markdown pre{background:var(--code);padding:24px;border-radius:12px;overflow-x:auto;margin:32px 0;border:1px solid var(--border);}
.markdown code{background:var(--code);padding:4px 8px;border-radius:6px;font-family:ui-monospace,SFMono-Regular,Monaco,Consolas,monospace;font-size:16px;}
.markdown pre code{background:transparent;padding:0;}
.markdown table{width:100%;border-collapse:collapse;margin:32px 0;background:var(--card);border-radius:12px;overflow:hidden;box-shadow:var(--shadow);}
.markdown th,.markdown td{padding:16px;text-align:left;border-bottom:1px solid var(--border);}
.markdown th{background:var(--card);font-weight:600;color:var(--fg);}
.markdown tr:last-child td{border-bottom:none;}
.mermaid{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;margin:32px 0;overflow:auto;}
.footer{background:var(--card);border-top:1px solid var(--border);padding:60px 0;margin-top:120px;}
.footer-inner{text-align:center;color:var(--muted);font-size:14px;}
.empty-state{text-align:center;padding:120px 24px;color:var(--muted);}
.empty-state h2{font-size:32px;margin-bottom:20px;font-weight:600;color:var(--fg);}
.empty-state p{font-size:18px;margin-bottom:24px;}
.empty-state a{background:var(--accent);color:white;padding:12px 24px;border-radius:24px;font-weight:500;display:inline-block;}
@media (max-width:768px){.header-inner{padding:0 16px;}.container{padding:0 16px;}.hero{padding:80px 0 60px 0;}.hero h1{font-size:40px;}.hero p{font-size:18px;}.posts-grid{grid-template-columns:1fr;gap:24px;}.post-card{padding:24px;}.markdown h1{font-size:36px;}.markdown h2{font-size:28px;}.markdown h3{font-size:22px;}.markdown p,.markdown ul,.markdown ol{font-size:16px;}.posts-section{padding:60px 0;}.section-title{font-size:28px;margin-bottom:40px;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><strong>xsukax</strong> Flat-File CMS</a>
    <a href="/admin.php" class="admin-link">Admin</a>
  </div>
</header>

<main>
  <?php if ($is_home): ?>
    <section class="hero">
      <div class="container">
        <h1>Welcome to <strong>xsukax</strong></h1>
        <p>A modern, elegant flat-file CMS for professional blogs</p>
      </div>
    </section>
    
    <section class="posts-section">
      <div class="container">
        <?php if (empty($posts)): ?>
          <div class="empty-state">
            <h2>No posts yet</h2>
            <p>Ready to start blogging?</p>
            <a href="/admin.php">Create your first post</a>
          </div>
        <?php else: ?>
          <h2 class="section-title">Latest Posts</h2>
          <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
              <article class="post-card">
                <h3><a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a></h3>
                <div class="post-date"><?=htmlspecialchars($post['date'])?></div>
                <div class="post-excerpt"><?=htmlspecialchars($post['excerpt'])?></div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php else: ?>
    <section class="post-content">
      <div class="container">
        <a href="/" class="back-link">Back to Posts</a>
        <article id="content" class="markdown"></article>
      </div>
    </section>
  <?php endif; ?>
</main>

<footer class="footer">
  <div class="footer-inner">
    <div class="container">
      &copy; <?=date('Y')?> <strong>xsukax</strong> Flat-File CMS — Simple. Fast. Elegant.
    </div>
  </div>
</footer>

<?php if (!$is_home): ?>
<textarea id="md-src" hidden><?=htmlspecialchars($content)?></textarea>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
(function(){
  const src = document.getElementById('md-src');
  const out = document.getElementById('content');
  if (!src || !out) return;
  
  marked.setOptions({gfm:true, breaks:true, headerIds:true, mangle:false});
  let html = marked.parse(src.value || src.textContent || '');
  html = DOMPurify.sanitize(html, {USE_PROFILES:{html:true, svg:true, svgFilters:true}});
  out.innerHTML = html;
  
  out.querySelectorAll('pre > code').forEach(code => {
    const classes = code.className || '';
    if (/\b(language-)?mermaid\b/i.test(classes)) {
      const graph = code.textContent;
      const pre = code.parentElement;
      const div = document.createElement('div');
      div.className = 'mermaid';
      div.textContent = graph;
      pre.replaceWith(div);
    }
  });
  
  out.querySelectorAll('a[href]').forEach(a => {
    const href = a.getAttribute('href') || '';
    try {
      const u = new URL(href, location.href);
      if (u.origin !== location.origin) { a.target = '_blank'; a.rel = 'noopener'; }
    } catch {}
  });
  
  if (window.mermaid) {
    const prefersDark = window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches;
    mermaid.initialize({startOnLoad:false, securityLevel:'loose', theme: prefersDark ? 'dark' : 'default'});
    mermaid.run();
  }
})();
</script>
<?php endif; ?>
</body>
</html>