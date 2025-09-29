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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#f8f9fa;color:#212529;font:400 16px/1.6 Poppins,-apple-system,sans-serif;}
a{color:#0066ff;text-decoration:none;transition:all 0.3s;}
a:hover{color:#0052cc;}
.header{background:#fff;border-bottom:1px solid #e9ecef;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
.header-inner{max-width:1200px;margin:0 auto;padding:0 20px;height:70px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-size:26px;font-weight:800;background:linear-gradient(135deg,#0066ff 0%,#00d4ff 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.brand:hover{opacity:0.8;}
.nav{display:flex;gap:8px;align-items:center;}
.nav a{padding:10px 20px;border-radius:6px;font-size:14px;font-weight:600;color:#495057;transition:all 0.3s;}
.nav a:hover{background:#f8f9fa;color:#212529;}
.nav .cta-btn{background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;box-shadow:0 4px 12px rgba(255,107,53,0.3);}
.nav .cta-btn:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(255,107,53,0.4);}
.container{max-width:1200px;margin:0 auto;padding:0 20px;}
.hero{background:linear-gradient(135deg,#0066ff 0%,#00d4ff 100%);color:#fff;padding:60px 20px;text-align:center;margin-bottom:40px;}
.hero h1{font-size:48px;font-weight:800;margin-bottom:16px;line-height:1.2;}
.hero p{font-size:20px;opacity:0.95;max-width:700px;margin:0 auto 32px;}
.hero-cta{display:inline-block;padding:16px 40px;background:#ff6b35;color:#fff;border-radius:50px;font-size:18px;font-weight:700;box-shadow:0 8px 20px rgba(255,107,53,0.4);transition:all 0.3s;}
.hero-cta:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(255,107,53,0.5);}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin:40px 0;padding:0 20px;}
.stat-card{background:#fff;border-radius:12px;padding:24px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e9ecef;}
.stat-number{font-size:36px;font-weight:800;color:#0066ff;margin-bottom:8px;}
.stat-label{font-size:14px;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
.filters{background:#fff;border-radius:12px;padding:24px;margin-bottom:30px;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e9ecef;}
.filter-title{font-size:14px;font-weight:700;color:#6c757d;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;}
.filter-chips{display:flex;gap:10px;flex-wrap:wrap;}
.chip{padding:8px 20px;background:#f8f9fa;border:2px solid transparent;border-radius:50px;font-size:14px;font-weight:600;color:#495057;transition:all 0.3s;}
.chip:hover{background:#e9ecef;border-color:#dee2e6;}
.chip.active{background:linear-gradient(135deg,#0066ff 0%,#00d4ff 100%);color:#fff;border-color:transparent;box-shadow:0 4px 12px rgba(0,102,255,0.3);}
.posts{margin:40px 0;}
.section-title{font-size:32px;font-weight:800;color:#212529;margin-bottom:24px;text-align:center;}
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;}
.post-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e9ecef;transition:all 0.3s;}
.post-card:hover{transform:translateY(-6px);box-shadow:0 12px 24px rgba(0,0,0,0.1);}
.post-banner{height:180px;background:linear-gradient(135deg,#0066ff 0%,#00d4ff 100%);display:flex;align-items:center;justify-content:center;font-size:48px;font-weight:800;color:#fff;}
.post-body{padding:24px;}
.post-title{font-size:22px;font-weight:700;color:#212529;margin-bottom:12px;line-height:1.3;}
.post-title a{color:#212529;}
.post-title a:hover{color:#0066ff;}
.post-meta{font-size:13px;color:#6c757d;margin-bottom:16px;font-weight:500;}
.excerpt{font-size:15px;color:#495057;line-height:1.6;margin-bottom:16px;}
.tags{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;}
.tag{padding:4px 12px;background:#e7f3ff;border-radius:50px;font-size:12px;color:#0066ff;font-weight:600;}
.tag:hover{background:#cfe7ff;}
.read-more{display:inline-block;padding:10px 24px;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border-radius:50px;font-size:14px;font-weight:700;transition:all 0.3s;}
.read-more:hover{transform:translateX(4px);box-shadow:0 4px 12px rgba(255,107,53,0.3);}
.pagination{display:flex;gap:8px;justify-content:center;align-items:center;margin:40px 0;flex-wrap:wrap;}
.page-link{padding:10px 18px;background:#fff;border:2px solid #e9ecef;border-radius:8px;font-size:15px;font-weight:600;color:#495057;transition:all 0.3s;}
.page-link:hover{background:#0066ff;color:#fff;border-color:#0066ff;}
.page-link.active{background:#0066ff;color:#fff;border-color:#0066ff;box-shadow:0 4px 12px rgba(0,102,255,0.3);}
.page-link.disabled{opacity:0.4;pointer-events:none;}
.single{padding:40px 0;}
.back{display:inline-block;padding:12px 24px;background:#f8f9fa;border-radius:8px;font-size:15px;font-weight:600;margin-bottom:30px;color:#495057;}
.back:hover{background:#e9ecef;}
.back::before{content:'← ';}
.content{background:#fff;border-radius:12px;padding:48px;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e9ecef;}
.content h1{font-size:40px;font-weight:800;margin:0 0 24px;color:#212529;line-height:1.2;}
.content h2{font-size:32px;font-weight:700;margin:32px 0 16px;color:#212529;}
.content h3{font-size:24px;font-weight:600;margin:24px 0 12px;color:#212529;}
.content p{margin:16px 0;line-height:1.8;color:#495057;}
.content ul,.content ol{margin:16px 0;padding-left:32px;line-height:1.8;color:#495057;}
.content li{margin:8px 0;}
.content blockquote{margin:24px 0;padding:20px 24px;border-left:4px solid #0066ff;background:#f8f9fa;border-radius:8px;color:#495057;}
.content pre{background:#2d3748;color:#fff;border-radius:8px;padding:20px;overflow-x:auto;margin:20px 0;}
.content code{background:#f8f9fa;padding:3px 8px;border-radius:4px;font-size:14px;color:#ff6b35;}
.content pre code{background:none;padding:0;color:#fff;}
.content img{max-width:100%;height:auto;border-radius:12px;margin:24px 0;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.cta-banner{background:linear-gradient(135deg,#0066ff 0%,#00d4ff 100%);color:#fff;padding:48px;border-radius:12px;text-align:center;margin:48px 0;}
.cta-banner h2{font-size:32px;font-weight:800;margin-bottom:16px;}
.cta-banner p{font-size:18px;margin-bottom:24px;opacity:0.95;}
.cta-banner-btn{display:inline-block;padding:14px 36px;background:#ff6b35;color:#fff;border-radius:50px;font-size:16px;font-weight:700;box-shadow:0 6px 16px rgba(255,107,53,0.4);}
.cta-banner-btn:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(255,107,53,0.5);}
.footer{background:#212529;color:#fff;margin-top:60px;padding:48px 0 24px;}
.footer-inner{max-width:1200px;margin:0 auto;padding:0 20px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:32px;margin-bottom:32px;}
.footer-section h3{font-size:16px;font-weight:700;margin-bottom:16px;color:#fff;}
.footer-section p,.footer-section a{font-size:14px;color:#adb5bd;line-height:1.8;display:block;margin:4px 0;}
.footer-section a:hover{color:#0066ff;}
.theme-selector{margin-top:16px;padding:16px;background:rgba(255,255,255,0.05);border-radius:8px;}
.theme-selector label{display:block;font-size:12px;font-weight:600;color:#adb5bd;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;}
.theme-selector select{width:100%;padding:10px 14px;background:#343a40;border:1px solid #495057;border-radius:6px;font-size:14px;color:#fff;font-family:inherit;}
.theme-selector button{width:100%;margin-top:10px;padding:10px 20px;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer;}
.theme-selector button:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(255,107,53,0.4);}
.footer-bottom{text-align:center;color:#6c757d;font-size:14px;padding-top:24px;border-top:1px solid #343a40;}
.empty{text-align:center;padding:80px 20px;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e9ecef;}
.empty h2{font-size:32px;font-weight:800;color:#212529;margin-bottom:16px;}
.empty p{font-size:18px;color:#6c757d;margin-bottom:24px;}
.empty a{display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border-radius:50px;font-weight:700;font-size:16px;box-shadow:0 6px 16px rgba(255,107,53,0.3);}
.empty a:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(255,107,53,0.4);}
@media (max-width:768px){.hero h1{font-size:32px;}.hero p{font-size:16px;}.stats{grid-template-columns:1fr 1fr;gap:12px;}.posts-grid{grid-template-columns:1fr;}.nav{display:none;}.content{padding:24px;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
    <nav class="nav">
      <a href="/rss.xml">Resources</a>
      <a href="/sitemap.xml">Sitemap</a>
      <a href="/admin.php" class="cta-btn">Get Started</a>
    </nav>
  </div>
</header>

<main>
  <?php if($is_home): ?>
    <section class="hero">
      <h1><?=htmlspecialchars(SITE_NAME)?></h1>
      <p><?=htmlspecialchars(SITE_DESC)?></p>
      <a href="/admin.php" class="hero-cta">Start Your Journey Today</a>
    </section>

    <div class="container">
      <div class="stats">
        <div class="stat-card">
          <div class="stat-number"><?=count($allPosts)?></div>
          <div class="stat-label">Resources</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?=count($allTags)?></div>
          <div class="stat-label">Topics</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">24/7</div>
          <div class="stat-label">Access</div>
        </div>
      </div>
      
      <?php if(!empty($allTags)): ?>
      <div class="filters">
        <div class="filter-title">Browse by Topic</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Resources</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>"><?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>Ready to Get Started?</h2>
            <p>Create your first piece of content and start engaging with your audience today.</p>
            <a href="/admin.php">Create First Resource</a>
          </div>
        <?php else: ?>
          <h2 class="section-title">Featured Resources</h2>
          <div class="posts-grid">
            <?php foreach($posts as $post): ?>
              <article class="post-card">
                <div class="post-banner"><?=strtoupper(substr($post['title'], 0, 1))?></div>
                <div class="post-body">
                  <div class="post-meta">Published <?=htmlspecialchars($post['date'])?></div>
                  <h3 class="post-title"><a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a></h3>
                  <div class="excerpt"><?=htmlspecialchars($post['excerpt'])?></div>
                  <?php if(!empty($post['tags'])): ?>
                  <div class="tags">
                    <?php foreach($post['tags'] as $t): ?>
                      <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                  <a href="/?p=<?=rawurlencode($post['slug'])?>" class="read-more">Learn More →</a>
                </div>
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
      </section>

      <div class="cta-banner">
        <h2>Ready to Take Action?</h2>
        <p>Join thousands of satisfied customers who have transformed their business.</p>
        <a href="/admin.php" class="cta-banner-btn">Get Started Now</a>
      </div>
    </div>
  <?php else: ?>
    <div class="container">
      <section class="single">
        <a href="/" class="back">Back to Resources</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:16px;color:#6c757d;">Published <?=get_post_date($file)?></div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div style="margin-top:32px;">
            <strong style="color:#6c757d;font-size:14px;text-transform:uppercase;letter-spacing:0.5px;">Related Topics:</strong>
            <div class="tags" style="margin-top:12px;">
              <?php foreach($postMeta['tags'] as $t): ?>
                <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </article>

        <div class="cta-banner">
          <h2>Found This Helpful?</h2>
          <p>Explore more resources and take your business to the next level.</p>
          <a href="/" class="cta-banner-btn">View All Resources</a>
        </div>
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
          <label for="theme">Change Theme</label>
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
    <div class="footer-bottom">© <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?> • All Rights Reserved</div>
  </div>
</footer>
</body>
</html>