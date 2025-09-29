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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#f0f2f5;color:#050505;font:400 15px/1.5 Inter,-apple-system,sans-serif;}
a{color:#1877f2;text-decoration:none;}
a:hover{text-decoration:underline;}
.header{background:#fff;border-bottom:1px solid #dadde1;position:sticky;top:0;z-index:100;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.header-inner{max-width:1250px;margin:0 auto;padding:0 16px;height:56px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-size:28px;font-weight:700;color:#1877f2;}
.brand:hover{text-decoration:none;}
.nav{display:flex;gap:12px;align-items:center;}
.nav a{padding:8px 16px;background:#e4e6eb;border-radius:6px;font-size:14px;font-weight:600;color:#050505;}
.nav a:hover{background:#d8dadf;text-decoration:none;}
.nav .admin-btn{background:#1877f2;color:#fff;}
.nav .admin-btn:hover{background:#166fe5;}
.container{max-width:680px;margin:0 auto;padding:16px;}
.hero{background:#fff;border-radius:8px;padding:20px;margin-bottom:12px;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.hero h1{font-size:24px;font-weight:700;margin-bottom:8px;}
.hero p{font-size:15px;color:#65676b;}
.filters{background:#fff;border-radius:8px;padding:12px 16px;margin-bottom:12px;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.filter-title{font-size:13px;font-weight:600;color:#65676b;margin-bottom:8px;text-transform:uppercase;}
.filter-chips{display:flex;gap:6px;flex-wrap:wrap;}
.chip{padding:6px 12px;background:#e4e6eb;border-radius:20px;font-size:13px;font-weight:500;color:#050505;}
.chip:hover{background:#d8dadf;text-decoration:none;}
.chip.active{background:#1877f2;color:#fff;}
.posts{margin:12px 0;}
.post-card{background:#fff;border-radius:8px;padding:16px;margin-bottom:12px;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.post-card:hover{box-shadow:0 2px 8px rgba(0,0,0,0.12);}
.post-header{display:flex;gap:12px;margin-bottom:12px;}
.post-avatar{width:40px;height:40px;background:linear-gradient(135deg,#1877f2,#42a5f5);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px;}
.post-info{flex:1;}
.post-title{font-size:15px;font-weight:600;color:#050505;margin-bottom:2px;}
.post-title a{color:#050505;}
.post-title a:hover{text-decoration:underline;}
.post-meta{font-size:12px;color:#65676b;}
.excerpt{font-size:15px;color:#050505;line-height:1.5;margin-bottom:12px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:4px 8px;background:#e7f3ff;border-radius:4px;font-size:12px;color:#1877f2;font-weight:500;}
.tag:hover{background:#d0e8ff;text-decoration:none;}
.pagination{display:flex;gap:8px;justify-content:center;align-items:center;margin:20px 0;flex-wrap:wrap;}
.page-link{padding:8px 12px;background:#fff;border-radius:6px;font-size:14px;font-weight:600;color:#050505;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.page-link:hover{background:#f0f2f5;text-decoration:none;}
.page-link.active{background:#1877f2;color:#fff;}
.page-link.disabled{opacity:0.5;pointer-events:none;}
.single{padding:16px 0;}
.back{display:inline-block;padding:8px 16px;background:#e4e6eb;border-radius:6px;font-size:14px;font-weight:600;margin-bottom:12px;}
.back:hover{background:#d8dadf;text-decoration:none;}
.content{background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.content h1{font-size:28px;font-weight:700;margin:0 0 16px;color:#050505;}
.content h2{font-size:24px;font-weight:600;margin:24px 0 12px;color:#050505;}
.content h3{font-size:20px;font-weight:600;margin:20px 0 10px;color:#050505;}
.content p{margin:12px 0;line-height:1.6;color:#050505;}
.content ul,.content ol{margin:12px 0;padding-left:28px;line-height:1.6;}
.content li{margin:6px 0;}
.content blockquote{margin:16px 0;padding:12px 16px;border-left:3px solid #1877f2;background:#e7f3ff;}
.content pre{background:#f0f2f5;border-radius:6px;padding:12px;overflow-x:auto;margin:12px 0;}
.content code{background:#f0f2f5;padding:2px 6px;border-radius:4px;font-size:14px;}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:8px;margin:12px 0;}
.footer{background:#fff;border-top:1px solid #dadde1;margin-top:24px;padding:20px 0;}
.footer-inner{max-width:680px;margin:0 auto;padding:0 16px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:16px;}
.footer-section h3{font-size:14px;font-weight:600;margin-bottom:8px;color:#050505;}
.footer-section p,.footer-section a{font-size:13px;color:#65676b;line-height:1.8;display:block;}
.footer-section a:hover{color:#1877f2;}
.theme-selector{margin-top:12px;padding:12px;background:#f0f2f5;border-radius:8px;}
.theme-selector label{display:block;font-size:12px;font-weight:600;color:#65676b;margin-bottom:6px;}
.theme-selector select{width:100%;padding:8px 12px;background:#fff;border:1px solid #ccd0d5;border-radius:6px;font-size:14px;color:#050505;font-family:inherit;}
.theme-selector button{width:100%;margin-top:8px;padding:8px 12px;background:#1877f2;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;}
.theme-selector button:hover{background:#166fe5;}
.footer-bottom{text-align:center;color:#65676b;font-size:13px;padding-top:16px;border-top:1px solid #dadde1;}
.empty{text-align:center;padding:60px 20px;background:#fff;border-radius:8px;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
.empty h2{font-size:20px;color:#050505;margin-bottom:8px;}
.empty p{font-size:15px;color:#65676b;margin-bottom:16px;}
.empty a{display:inline-block;padding:10px 20px;background:#1877f2;color:#fff;border-radius:6px;font-weight:600;}
.empty a:hover{background:#166fe5;text-decoration:none;}
@media (max-width:768px){.container,.footer-inner{padding:0 8px;}.hero,.post-card,.filters,.content{border-radius:0;margin-left:-8px;margin-right:-8px;}.nav{display:none;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand">xsukax Flat-File CMS</a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">Sitemap</a>
      <a href="/admin.php" class="admin-btn">Admin</a>
    </nav>
  </div>
</header>

<main>
  <?php if($is_home): ?>
    <div class="container">
      <section class="hero">
        <h1><?=htmlspecialchars(SITE_NAME)?></h1>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
      </section>
      
      <?php if(!empty($allTags)): ?>
      <div class="filters">
        <div class="filter-title">Filter by Tags</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Posts</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>">#<?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No posts yet</h2>
            <p>Start creating amazing content</p>
            <a href="/admin.php">Create First Post</a>
          </div>
        <?php else: ?>
          <?php foreach($posts as $post): ?>
            <article class="post-card">
              <div class="post-header">
                <div class="post-avatar"><?=strtoupper(substr($post['title'], 0, 1))?></div>
                <div class="post-info">
                  <div class="post-title"><a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a></div>
                  <div class="post-meta"><?=htmlspecialchars($post['date'])?></div>
                </div>
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
      </section>
    </div>
  <?php else: ?>
    <div class="container">
      <section class="single">
        <a href="/" class="back">← Back to Feed</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:12px;color:#65676b;"><?=get_post_date($file)?></div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:20px;">
            <?php foreach($postMeta['tags'] as $t): ?>
              <a href="/?tag=<?=urlencode($t)?>" class="tag">#<?=htmlspecialchars($t)?></a>
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
        <h3><?=htmlspecialchars(SITE_NAME)?></h3>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
        <form method="post" class="theme-selector">
          <label for="theme">Choose Theme</label>
          <select name="theme" id="theme">
            <?php foreach($availableThemes as $themeSlug => $themeName): ?>
              <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>><?=htmlspecialchars($themeName)?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="change_theme">Apply Theme</button>
        </form>
      </div>
      <div class="footer-section">
        <h3>Quick Links</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">&copy; <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?> · Powered by xsukax CMS</div>
  </div>
</footer>
</body>
</html>