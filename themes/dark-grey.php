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
<link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--wa-green:#00a884;--wa-green-dark:#008f70;--wa-dark:#111b21;--wa-dark-light:#1f2c33;--wa-dark-lighter:#2a3942;--wa-gray:#8696a0;--wa-light:#d1d7db;--wa-white:#ffffff;--wa-message-sent:#005c4b;--wa-message-received:#1f2c33;}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--wa-dark);color:var(--wa-light);font-family:'Segoe UI',-apple-system,sans-serif;font-size:15px;line-height:1.4;}
a{color:var(--wa-green);text-decoration:none;}
a:hover{text-decoration:underline;}
.header{background:var(--wa-dark-light);border-bottom:1px solid var(--wa-dark-lighter);padding:10px 0;position:sticky;top:0;z-index:100;}
.header-inner{max-width:1600px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;}
.brand{display:flex;align-items:center;gap:12px;font-size:18px;font-weight:500;color:var(--wa-white);}
.brand:hover{text-decoration:none;opacity:0.9;}
.brand:before{content:"💬";font-size:20px;}
.nav{display:flex;gap:15px;align-items:center;}
.nav a{color:var(--wa-gray);font-size:14px;padding:6px 10px;border-radius:4px;transition:all 0.2s;}
.nav a:hover{background:var(--wa-dark-lighter);text-decoration:none;color:var(--wa-light);}
.admin-link{background:var(--wa-green);color:var(--wa-white)!important;padding:6px 14px!important;border-radius:4px;font-weight:500!important;}
.admin-link:hover{background:var(--wa-green-dark)!important;text-decoration:none!important;}
.container{max-width:1600px;margin:0 auto;padding:0 20px;}
.hero{background:var(--wa-dark-light);border-radius:8px;padding:30px;margin:20px 0;text-align:center;border:1px solid var(--wa-dark-lighter);}
.hero h1{font-size:28px;font-weight:500;margin-bottom:8px;color:var(--wa-white);}
.hero p{font-size:15px;color:var(--wa-gray);}
.filters{background:var(--wa-dark-light);border:1px solid var(--wa-dark-lighter);border-radius:8px;padding:15px;margin:20px 0;}
.filter-title{font-size:13px;font-weight:600;text-transform:uppercase;color:var(--wa-gray);margin-bottom:10px;letter-spacing:0.5px;}
.filter-chips{display:flex;gap:6px;flex-wrap:wrap;}
.chip{padding:6px 12px;background:var(--wa-dark-lighter);border:1px solid var(--wa-dark-lighter);border-radius:18px;font-size:13px;color:var(--wa-light);transition:all 0.2s;}
.chip:hover{background:var(--wa-dark);border-color:var(--wa-green);text-decoration:none;}
.chip.active{background:var(--wa-green);color:var(--wa-white);border-color:var(--wa-green);}
.posts{padding:20px 0;}
.posts-grid{display:grid;gap:12px;}
.post-card{background:var(--wa-dark-light);border:1px solid var(--wa-dark-lighter);border-radius:8px;padding:16px;transition:all 0.2s;position:relative;}
.post-card:hover{border-color:var(--wa-green);background:var(--wa-dark-lighter);}
.post-card:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--wa-green);border-radius:8px 0 0 8px;opacity:0;transition:opacity 0.2s;}
.post-card:hover:before{opacity:1;}
.post-card h2{font-size:16px;font-weight:500;margin-bottom:6px;color:var(--wa-white);}
.post-card h2 a{color:var(--wa-white);}
.post-card h2 a:hover{color:var(--wa-green);text-decoration:none;}
.post-meta{display:flex;gap:10px;align-items:center;margin-bottom:8px;color:var(--wa-gray);font-size:12px;}
.excerpt{color:var(--wa-gray);line-height:1.4;margin-bottom:10px;font-size:13px;}
.tags{display:flex;gap:4px;flex-wrap:wrap;}
.tag{padding:2px 8px;background:var(--wa-dark-lighter);border:1px solid var(--wa-dark-lighter);border-radius:12px;font-size:11px;color:var(--wa-gray);font-weight:500;}
.tag:hover{background:var(--wa-dark);border-color:var(--wa-green);text-decoration:none;color:var(--wa-light);}
.pagination{display:flex;gap:6px;align-items:center;justify-content:center;margin:30px 0;flex-wrap:wrap;}
.page-link{padding:6px 12px;background:var(--wa-dark-light);border:1px solid var(--wa-dark-lighter);border-radius:4px;font-size:13px;color:var(--wa-light);font-weight:500;min-width:36px;text-align:center;}
.page-link:hover{background:var(--wa-dark-lighter);border-color:var(--wa-green);text-decoration:none;}
.page-link.active{background:var(--wa-green);color:var(--wa-white);border-color:var(--wa-green);}
.page-link.disabled{opacity:0.4;cursor:not-allowed;pointer-events:none;}
.single{padding:20px 0;}
.back{display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;font-weight:500;padding:6px 12px;background:var(--wa-dark-light);border:1px solid var(--wa-dark-lighter);border-radius:4px;color:var(--wa-light);}
.back:hover{background:var(--wa-dark-lighter);border-color:var(--wa-green);text-decoration:none;}
.back:before{content:"←";}
.content{background:var(--wa-dark-light);border:1px solid var(--wa-dark-lighter);border-radius:8px;padding:25px;max-width:900px;margin:0 auto;}
.content h1{font-size:22px;font-weight:500;margin:0 0 15px;padding-bottom:8px;border-bottom:1px solid var(--wa-dark-lighter);color:var(--wa-white);}
.content h2{font-size:18px;font-weight:500;margin:25px 0 12px;padding-bottom:6px;border-bottom:1px solid var(--wa-dark-lighter);color:var(--wa-white);}
.content h3{font-size:16px;font-weight:500;margin:20px 0 10px;color:var(--wa-white);}
.content p{margin:12px 0;line-height:1.5;color:var(--wa-light);}
.content ul,.content ol{margin:12px 0;padding-left:25px;line-height:1.5;}
.content li{margin:6px 0;color:var(--wa-light);}
.content blockquote{margin:12px 0;padding:0 15px;border-left:3px solid var(--wa-green);color:var(--wa-gray);}
.content pre{background:var(--wa-dark);border:1px solid var(--wa-dark-lighter);border-radius:6px;padding:15px;overflow-x:auto;margin:12px 0;}
.content code{background:var(--wa-dark);padding:2px 6px;border-radius:3px;font-size:85%;font-family:'Courier New',monospace;color:var(--wa-light);}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:6px;border:1px solid var(--wa-dark-lighter);margin:12px 0;}
.footer{background:var(--wa-dark-light);border-top:1px solid var(--wa-dark-lighter);padding:30px 0;margin-top:50px;}
.footer-inner{max-width:1600px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;gap:30px;flex-wrap:wrap;}
.footer-col h3{font-size:15px;font-weight:500;margin-bottom:10px;color:var(--wa-white);}
.footer-col p,.footer-col a{color:var(--wa-gray);font-size:13px;line-height:1.6;}
.footer-col a{display:block;}
.footer-col a:hover{color:var(--wa-green);text-decoration:none;}
.theme-selector{margin-top:15px;}
.theme-selector label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:var(--wa-gray);}
.theme-selector select{padding:6px 30px 6px 10px;background:var(--wa-dark);border:1px solid var(--wa-dark-lighter);border-radius:4px;font-size:13px;color:var(--wa-light);cursor:pointer;width:100%;}
.theme-selector select:hover{border-color:var(--wa-green);}
.theme-selector button{margin-top:8px;padding:6px 14px;background:var(--wa-green);color:var(--wa-white);border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;width:100%;}
.theme-selector button:hover{background:var(--wa-green-dark);}
.footer-bottom{text-align:center;color:var(--wa-gray);font-size:12px;margin-top:25px;padding-top:20px;border-top:1px solid var(--wa-dark-lighter);width:100%;}
.empty{text-align:center;padding:50px 20px;color:var(--wa-gray);background:var(--wa-dark-light);border-radius:8px;border:1px solid var(--wa-dark-lighter);}
.empty h2{font-size:18px;margin-bottom:10px;color:var(--wa-white);}
.empty p{font-size:14px;margin-bottom:15px;}
.empty a{display:inline-block;padding:8px 16px;background:var(--wa-green);color:var(--wa-white);border-radius:4px;font-weight:500;}
.empty a:hover{background:var(--wa-green-dark);text-decoration:none;}
@media (max-width:768px){.header-inner,.container,.footer-inner{padding:0 15px;}.nav{display:none;}.hero{padding:20px;}.hero h1{font-size:22px;}.posts-grid{grid-template-columns:1fr;}.content{padding:20px;}.footer-inner{flex-direction:column;gap:20px;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">Sitemap</a>
      <a href="/admin.php" class="admin-link">Admin</a>
    </nav>
  </div>
</header>

<main>
  <?php if($is_home): ?>
    <section class="hero">
      <div class="container">
        <h1><?=htmlspecialchars(SITE_NAME)?></h1>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
      </div>
    </section>
    
    <?php if(!empty($allTags)): ?>
    <div class="container">
      <div class="filters">
        <div class="filter-title">Filter by Tags</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Posts</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>">#<?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
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
                  <span>📅 <?=htmlspecialchars($post['date'])?></span>
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
          
          <?php if($totalPages > 1): ?>
          <div class="pagination">
            <?php if($page > 1): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page - 1])?>" class="page-link">← Previous</a>
            <?php else: ?>
              <span class="page-link disabled">← Previous</span>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
              <?php if($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $i])?>" class="page-link <?=$i === $page ? 'active' : ''?>"><?=$i?></a>
              <?php elseif(abs($i - $page) == 3): ?>
                <span class="page-link disabled">...</span>
              <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page + 1])?>" class="page-link">Next →</a>
            <?php else: ?>
              <span class="page-link disabled">Next →</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>
  <?php else: ?>
    <section class="single">
      <div class="container">
        <a href="/" class="back">Back to Posts</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:16px;">
            <span>📅 <?=get_post_date($file)?></span>
          </div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:24px;">
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
        <h3>💬 <?=htmlspecialchars(SITE_NAME)?></h3>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
        
        <form method="post" class="theme-selector">
          <label for="theme">Choose Theme:</label>
          <select name="theme" id="theme">
            <?php foreach($availableThemes as $themeSlug => $themeName): ?>
              <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>>
                <?=htmlspecialchars($themeName)?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="change_theme">Apply Theme</button>
        </form>
      </div>
      <div class="footer-col">
        <h3>Resources</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?> • Powered by xsukax Flat-File CMS
    </div>
  </div>
</footer>
</body>
</html>