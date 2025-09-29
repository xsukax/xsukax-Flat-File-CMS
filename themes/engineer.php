<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($pageTitle)?></title>
<meta name="description" content="<?=htmlspecialchars($is_home ? SITE_DESC : extract_excerpt($content, 160))?>">
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="<?=SITE_URL?>/rss.xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#0a1929;color:#e3e8ef;font:400 15px/1.6 Roboto,sans-serif;position:relative;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background-image:linear-gradient(rgba(26,115,232,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(26,115,232,0.03) 1px,transparent 1px);background-size:20px 20px;pointer-events:none;z-index:0;}
a{color:#1a73e8;text-decoration:none;border-bottom:1px solid transparent;transition:all 0.2s;}
a:hover{border-bottom-color:#1a73e8;color:#4dabf7;}
.container{position:relative;z-index:1;}
.header{background:rgba(13,27,42,0.95);border-bottom:2px solid #1a73e8;backdrop-filter:blur(10px);position:sticky;top:0;z-index:100;}
.header-inner{max-width:1400px;margin:0 auto;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;border-left:4px solid #ffa726;}
.brand{font:700 18px/1 'Roboto Mono',monospace;color:#1a73e8;text-transform:uppercase;letter-spacing:2px;border:none;}
.brand::before{content:'[';color:#ffa726;margin-right:4px;}
.brand::after{content:']';color:#ffa726;margin-left:4px;}
.brand:hover{color:#4dabf7;border:none;}
.nav{display:flex;gap:16px;align-items:center;}
.nav a{padding:8px 16px;background:rgba(26,115,232,0.1);border:1px solid #1a73e8;font:500 13px/1 'Roboto Mono',monospace;color:#1a73e8;text-transform:uppercase;letter-spacing:1px;}
.nav a:hover{background:rgba(26,115,232,0.2);border-bottom-color:#1a73e8;}
.wrapper{max-width:1400px;margin:0 auto;padding:32px 24px;}
.hero{background:rgba(13,27,42,0.8);border:2px solid #1a73e8;border-left:6px solid #ffa726;padding:32px;margin-bottom:24px;position:relative;}
.hero::before{content:'SPEC-001';position:absolute;top:8px;right:12px;font:500 10px/1 'Roboto Mono',monospace;color:#546e7a;letter-spacing:1px;}
.hero h1{font:700 32px/1.2 'Roboto Mono',monospace;color:#1a73e8;margin-bottom:12px;text-transform:uppercase;letter-spacing:2px;}
.hero p{font-size:15px;color:#b0bec5;line-height:1.7;}
.filters{background:rgba(13,27,42,0.8);border:1px solid #1a73e8;padding:20px;margin-bottom:24px;}
.filter-title{font:600 11px/1 'Roboto Mono',monospace;color:#ffa726;margin-bottom:12px;text-transform:uppercase;letter-spacing:2px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{padding:6px 14px;background:rgba(26,115,232,0.1);border:1px solid #1a73e8;font:500 12px/1 'Roboto Mono',monospace;color:#1a73e8;text-transform:uppercase;letter-spacing:1px;}
.chip:hover{background:rgba(26,115,232,0.2);border-bottom-color:#1a73e8;}
.chip.active{background:#1a73e8;color:#0a1929;border-color:#1a73e8;}
.posts{margin:24px 0;}
.post-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(450px,1fr));gap:24px;}
.post-card{background:rgba(13,27,42,0.8);border:1px solid #37474f;border-left:4px solid #1a73e8;padding:24px;position:relative;transition:all 0.3s;}
.post-card::before{content:attr(data-index);position:absolute;top:8px;right:12px;font:500 10px/1 'Roboto Mono',monospace;color:#546e7a;}
.post-card:hover{border-color:#1a73e8;background:rgba(26,115,232,0.05);transform:translateX(4px);}
.post-header{margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #263238;}
.post-title{font:600 18px/1.3 'Roboto Mono',monospace;color:#e3e8ef;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;}
.post-title a{color:#e3e8ef;border:none;}
.post-title a:hover{color:#1a73e8;}
.post-meta{font:400 11px/1 'Roboto Mono',monospace;color:#78909c;text-transform:uppercase;letter-spacing:1px;}
.post-meta::before{content:'DATE: ';color:#546e7a;}
.excerpt{font-size:14px;color:#b0bec5;line-height:1.7;margin-bottom:16px;font-family:'Roboto',sans-serif;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:4px 10px;background:rgba(255,167,38,0.1);border:1px solid #ffa726;font:500 10px/1 'Roboto Mono',monospace;color:#ffa726;text-transform:uppercase;letter-spacing:1px;}
.tag:hover{background:rgba(255,167,38,0.2);border-bottom-color:#ffa726;}
.pagination{display:flex;gap:8px;justify-content:center;align-items:center;margin:32px 0;flex-wrap:wrap;}
.page-link{padding:8px 14px;background:rgba(13,27,42,0.8);border:1px solid #1a73e8;font:600 12px/1 'Roboto Mono',monospace;color:#1a73e8;text-transform:uppercase;}
.page-link:hover{background:rgba(26,115,232,0.1);border-bottom-color:#1a73e8;}
.page-link.active{background:#1a73e8;color:#0a1929;border-color:#1a73e8;}
.page-link.disabled{opacity:0.3;pointer-events:none;}
.single{padding:24px 0;}
.back{display:inline-block;padding:10px 20px;background:rgba(13,27,42,0.8);border:1px solid #1a73e8;font:600 12px/1 'Roboto Mono',monospace;color:#1a73e8;margin-bottom:24px;text-transform:uppercase;letter-spacing:1px;}
.back:hover{background:rgba(26,115,232,0.1);border-bottom-color:#1a73e8;}
.back::before{content:'< ';}
.content{background:rgba(13,27,42,0.8);border:2px solid #1a73e8;border-left:6px solid #ffa726;padding:40px;position:relative;}
.content::before{content:'DOC-' attr(data-id);position:absolute;top:12px;right:16px;font:500 11px/1 'Roboto Mono',monospace;color:#546e7a;letter-spacing:1px;}
.content h1{font:700 28px/1.3 'Roboto Mono',monospace;color:#1a73e8;margin:0 0 24px;text-transform:uppercase;letter-spacing:2px;padding-bottom:16px;border-bottom:2px solid #1a73e8;}
.content h2{font:700 22px/1.3 'Roboto Mono',monospace;color:#4dabf7;margin:32px 0 16px;text-transform:uppercase;letter-spacing:1px;}
.content h3{font:600 18px/1.3 'Roboto Mono',monospace;color:#90caf9;margin:24px 0 12px;text-transform:uppercase;letter-spacing:1px;}
.content p{margin:16px 0;line-height:1.7;color:#e3e8ef;}
.content ul,.content ol{margin:16px 0;padding-left:28px;line-height:1.7;color:#e3e8ef;}
.content li{margin:8px 0;}
.content li::marker{color:#1a73e8;}
.content blockquote{margin:20px 0;padding:16px 20px;border-left:4px solid #ffa726;background:rgba(255,167,38,0.05);color:#b0bec5;}
.content pre{background:rgba(0,0,0,0.5);border:1px solid #1a73e8;border-left:4px solid #ffa726;padding:20px;overflow-x:auto;margin:20px 0;font-family:'Roboto Mono',monospace;}
.content code{background:rgba(26,115,232,0.1);padding:3px 8px;border:1px solid #1a73e8;font:500 13px/1 'Roboto Mono',monospace;color:#4dabf7;}
.content pre code{background:none;padding:0;border:none;color:#90caf9;}
.content img{max-width:100%;height:auto;border:2px solid #1a73e8;margin:20px 0;}
.content table{width:100%;border-collapse:collapse;margin:20px 0;font-family:'Roboto Mono',monospace;font-size:13px;}
.content th,.content td{padding:12px;border:1px solid #37474f;text-align:left;}
.content th{background:rgba(26,115,232,0.1);color:#1a73e8;font-weight:700;text-transform:uppercase;letter-spacing:1px;}
.footer{background:rgba(13,27,42,0.95);border-top:2px solid #1a73e8;margin-top:48px;padding:32px 0;}
.footer-inner{max-width:1400px;margin:0 auto;padding:0 24px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:32px;margin-bottom:24px;}
.footer-section{padding:20px;border-left:3px solid #1a73e8;background:rgba(26,115,232,0.03);}
.footer-section h3{font:700 14px/1 'Roboto Mono',monospace;color:#ffa726;margin-bottom:12px;text-transform:uppercase;letter-spacing:2px;}
.footer-section p,.footer-section a{font-size:13px;color:#b0bec5;line-height:1.8;display:block;font-family:'Roboto Mono',monospace;}
.footer-section a{border:none;}
.footer-section a:hover{color:#1a73e8;}
.theme-selector{margin-top:16px;padding:16px;background:rgba(0,0,0,0.3);border:1px solid #1a73e8;}
.theme-selector label{display:block;font:600 11px/1 'Roboto Mono',monospace;color:#ffa726;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px;}
.theme-selector select{width:100%;padding:10px 12px;background:rgba(13,27,42,0.9);border:1px solid #1a73e8;color:#e3e8ef;font:500 13px/1 'Roboto Mono',monospace;}
.theme-selector button{width:100%;margin-top:10px;padding:10px 16px;background:#1a73e8;color:#0a1929;border:none;font:700 12px/1 'Roboto Mono',monospace;cursor:pointer;text-transform:uppercase;letter-spacing:1px;}
.theme-selector button:hover{background:#4dabf7;}
.footer-bottom{text-align:center;color:#546e7a;font:500 11px/1 'Roboto Mono',monospace;padding-top:24px;border-top:1px solid #263238;text-transform:uppercase;letter-spacing:1px;}
.empty{text-align:center;padding:80px 20px;background:rgba(13,27,42,0.8);border:2px solid #1a73e8;border-left:6px solid #ffa726;}
.empty h2{font:700 24px/1 'Roboto Mono',monospace;color:#1a73e8;margin-bottom:16px;text-transform:uppercase;letter-spacing:2px;}
.empty p{font-size:15px;color:#b0bec5;margin-bottom:24px;}
.empty a{display:inline-block;padding:14px 28px;background:#1a73e8;color:#0a1929;font:700 13px/1 'Roboto Mono',monospace;border:none;text-transform:uppercase;letter-spacing:1px;}
.empty a:hover{background:#4dabf7;}
@media (max-width:768px){.wrapper,.footer-inner{padding:16px;}.post-grid{grid-template-columns:1fr;}.hero,.post-card,.content{padding:20px;}.nav{display:none;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="container">
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">MAP</a>
      <a href="/admin.php">ADMIN</a>
    </nav>
  </div>
</header>

<main>
  <?php if($is_home): ?>
    <div class="wrapper">
      <section class="hero">
        <h1><?=htmlspecialchars(SITE_NAME)?></h1>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
      </section>
      
      <?php if(!empty($allTags)): ?>
      <div class="filters">
        <div class="filter-title">Filter Parameters</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>"><?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No Data Available</h2>
            <p>System requires initialization. Create first entry to begin operations.</p>
            <a href="/admin.php">Initialize System</a>
          </div>
        <?php else: ?>
          <div class="post-grid">
            <?php $index = ($page - 1) * 12 + 1; foreach($posts as $post): ?>
              <article class="post-card" data-index="<?=sprintf('%03d', $index++)?>">
                <div class="post-header">
                  <div class="post-title"><a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a></div>
                  <div class="post-meta"><?=htmlspecialchars($post['date'])?></div>
                </div>
                <div class="excerpt"><?=htmlspecialchars($post['excerpt'])?></div>
                <?php if(!empty($post['tags'])): ?>
                <div class="tags">
                  <?php foreach($post['tags'] as $t): ?>
                    <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
          
          <?php if($totalPages > 1): ?>
          <div class="pagination">
            <?php if($page > 1): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page - 1])?>" class="page-link">PREV</a>
            <?php else: ?>
              <span class="page-link disabled">PREV</span>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
              <?php if($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $i])?>" class="page-link <?=$i === $page ? 'active' : ''?>"><?=sprintf('%02d', $i)?></a>
              <?php elseif(abs($i - $page) == 3): ?>
                <span class="page-link disabled">...</span>
              <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page + 1])?>" class="page-link">NEXT</a>
            <?php else: ?>
              <span class="page-link disabled">NEXT</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    </div>
  <?php else: ?>
    <div class="wrapper">
      <section class="single">
        <a href="/" class="back">Return</a>
        <article class="content" data-id="<?=sprintf('%04d', crc32($slug))?>">
          <div class="post-meta" style="margin-bottom:20px;font-size:12px;"><?=get_post_date($file)?> | <?=number_format(strlen($content))?> BYTES</div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:32px;padding-top:20px;border-top:1px solid #263238;">
            <?php foreach($postMeta['tags'] as $t): ?>
              <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </article>
      </section>
    </div>
  <?php endif; ?>
</main>

<footer class="footer">
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-section">
        <h3>System Info</h3>
        <p><?=htmlspecialchars(SITE_NAME)?></p>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
        <form method="post" class="theme-selector">
          <label for="theme">Theme Config</label>
          <select name="theme" id="theme">
            <?php foreach($availableThemes as $themeSlug => $themeName): ?>
              <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>><?=htmlspecialchars($themeName)?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="change_theme">Apply</button>
        </form>
      </div>
      <div class="footer-section">
        <h3>Quick Access</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">v1.0 | <?=date('Y')?> | <?=htmlspecialchars(SITE_NAME)?> | Powered by xsukax CMS</div>
  </div>
</footer>
</div>
</body>
</html>