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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#f8f9fa;color:#2c3e50;font:400 16px/1.7 Inter,-apple-system,sans-serif;}
a{color:#0066cc;text-decoration:none;transition:color 0.2s;}
a:hover{color:#0052a3;}
.header{background:#fff;border-bottom:1px solid #e1e8ed;box-shadow:0 2px 8px rgba(0,0,0,0.05);position:sticky;top:0;z-index:100;}
.header-inner{max-width:1200px;margin:0 auto;padding:0 24px;height:80px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-size:28px;font-weight:700;color:#0066cc;display:flex;align-items:center;gap:12px;}
.brand::before{content:'✚';font-size:32px;color:#00a86b;}
.brand:hover{color:#0052a3;}
.nav{display:flex;gap:24px;align-items:center;}
.nav a{padding:10px 20px;font-size:15px;font-weight:500;color:#2c3e50;border-radius:6px;}
.nav a:hover{background:#f0f4f8;color:#0066cc;}
.nav .admin-btn{background:#0066cc;color:#fff;border:none;}
.nav .admin-btn:hover{background:#0052a3;}
.container{max-width:1000px;margin:0 auto;padding:32px 24px;}
.hero{background:linear-gradient(135deg,#0066cc 0%,#00a86b 100%);color:#fff;border-radius:12px;padding:48px 40px;margin-bottom:32px;box-shadow:0 4px 12px rgba(0,102,204,0.15);}
.hero h1{font-family:Lora,serif;font-size:42px;font-weight:600;margin-bottom:12px;line-height:1.2;}
.hero p{font-size:18px;opacity:0.95;line-height:1.6;}
.filters{background:#fff;border-radius:12px;padding:24px;margin-bottom:32px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border:1px solid #e1e8ed;}
.filter-title{font-size:14px;font-weight:600;color:#5a6c7d;margin-bottom:16px;text-transform:uppercase;letter-spacing:0.5px;}
.filter-chips{display:flex;gap:10px;flex-wrap:wrap;}
.chip{padding:8px 16px;background:#f0f4f8;border:1px solid #d1dce5;border-radius:20px;font-size:14px;font-weight:500;color:#2c3e50;}
.chip:hover{background:#e1e8ed;border-color:#b8c5d0;}
.chip.active{background:#0066cc;color:#fff;border-color:#0066cc;}
.posts{margin:32px 0;}
.post-card{background:#fff;border-radius:12px;padding:32px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-left:4px solid #00a86b;transition:all 0.3s;}
.post-card:hover{box-shadow:0 4px 16px rgba(0,0,0,0.08);transform:translateY(-2px);}
.post-header{margin-bottom:16px;}
.post-title{font-family:Lora,serif;font-size:26px;font-weight:600;color:#2c3e50;margin-bottom:8px;line-height:1.3;}
.post-title a{color:#2c3e50;}
.post-title a:hover{color:#0066cc;}
.post-meta{font-size:14px;color:#5a6c7d;display:flex;align-items:center;gap:16px;}
.post-meta::before{content:'📅';margin-right:4px;}
.excerpt{font-size:16px;color:#4a5568;line-height:1.7;margin-bottom:16px;}
.tags{display:flex;gap:8px;flex-wrap:wrap;}
.tag{padding:5px 12px;background:#e8f5f0;border:1px solid #b8ddc8;border-radius:16px;font-size:13px;color:#00a86b;font-weight:500;}
.tag:hover{background:#d4ebe0;border-color:#00a86b;}
.pagination{display:flex;gap:8px;justify-content:center;align-items:center;margin:40px 0;flex-wrap:wrap;}
.page-link{padding:10px 16px;background:#fff;border:1px solid #d1dce5;border-radius:8px;font-size:15px;font-weight:500;color:#2c3e50;transition:all 0.2s;}
.page-link:hover{background:#f0f4f8;border-color:#0066cc;color:#0066cc;}
.page-link.active{background:#0066cc;color:#fff;border-color:#0066cc;}
.page-link.disabled{opacity:0.4;pointer-events:none;}
.single{padding:32px 0;}
.back{display:inline-block;padding:10px 20px;background:#f0f4f8;border:1px solid #d1dce5;border-radius:8px;font-size:15px;font-weight:500;margin-bottom:24px;}
.back:hover{background:#e1e8ed;border-color:#b8c5d0;}
.back::before{content:'← ';}
.content{background:#fff;border-radius:12px;padding:48px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-top:4px solid #0066cc;}
.content h1{font-family:Lora,serif;font-size:38px;font-weight:600;margin:0 0 24px;color:#2c3e50;line-height:1.3;}
.content h2{font-family:Lora,serif;font-size:30px;font-weight:600;margin:40px 0 16px;color:#2c3e50;padding-bottom:8px;border-bottom:2px solid #e1e8ed;}
.content h3{font-size:24px;font-weight:600;margin:32px 0 12px;color:#2c3e50;}
.content p{margin:16px 0;line-height:1.8;color:#4a5568;}
.content ul,.content ol{margin:16px 0;padding-left:32px;line-height:1.8;color:#4a5568;}
.content li{margin:8px 0;}
.content blockquote{margin:24px 0;padding:20px 24px;border-left:4px solid #00a86b;background:#f8fdfb;border-radius:0 8px 8px 0;color:#2c3e50;}
.content pre{background:#2c3e50;color:#f8f9fa;border-radius:8px;padding:20px;overflow-x:auto;margin:20px 0;font-size:14px;}
.content code{background:#f0f4f8;padding:3px 8px;border-radius:4px;font-size:14px;color:#0066cc;}
.content pre code{background:none;padding:0;color:#f8f9fa;}
.content img{max-width:100%;height:auto;border-radius:12px;margin:24px 0;border:1px solid #e1e8ed;}
.content table{width:100%;margin:24px 0;border-collapse:collapse;}
.content table th{background:#f0f4f8;padding:12px;text-align:left;font-weight:600;border-bottom:2px solid #d1dce5;}
.content table td{padding:12px;border-bottom:1px solid #e1e8ed;}
.footer{background:#fff;border-top:1px solid #e1e8ed;margin-top:48px;padding:40px 0;}
.footer-inner{max-width:1000px;margin:0 auto;padding:0 24px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:32px;margin-bottom:32px;}
.footer-section h3{font-size:16px;font-weight:600;margin-bottom:16px;color:#2c3e50;}
.footer-section p,.footer-section a{font-size:14px;color:#5a6c7d;line-height:1.8;display:block;margin:4px 0;}
.footer-section a:hover{color:#0066cc;}
.theme-selector{margin-top:16px;padding:20px;background:#f8f9fa;border-radius:8px;border:1px solid #e1e8ed;}
.theme-selector label{display:block;font-size:13px;font-weight:600;color:#2c3e50;margin-bottom:8px;}
.theme-selector select{width:100%;padding:10px 14px;background:#fff;border:1px solid #d1dce5;border-radius:6px;font-size:14px;color:#2c3e50;font-family:inherit;}
.theme-selector button{width:100%;margin-top:12px;padding:10px 20px;background:#0066cc;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;}
.theme-selector button:hover{background:#0052a3;}
.footer-bottom{text-align:center;color:#5a6c7d;font-size:14px;padding-top:24px;border-top:1px solid #e1e8ed;}
.empty{text-align:center;padding:80px 24px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
.empty h2{font-size:28px;color:#2c3e50;margin-bottom:12px;font-weight:600;}
.empty p{font-size:16px;color:#5a6c7d;margin-bottom:24px;}
.empty a{display:inline-block;padding:12px 32px;background:#0066cc;color:#fff;border-radius:6px;font-weight:600;}
.empty a:hover{background:#0052a3;}
.info-badge{display:inline-block;padding:4px 10px;background:#e8f5f0;color:#00a86b;border-radius:12px;font-size:12px;font-weight:600;margin-left:8px;}
@media (max-width:768px){.container,.footer-inner{padding:0 16px;}.hero{padding:32px 24px;border-radius:0;margin-left:-16px;margin-right:-16px;}.hero h1{font-size:32px;}.post-card,.content{padding:24px;border-radius:8px;}.content h1{font-size:28px;}.nav{display:none;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
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
        <div class="filter-title">Filter by Topic</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Articles</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>"><?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No Articles Published</h2>
            <p>Start sharing your medical knowledge and insights with the world.</p>
            <a href="/admin.php">Create First Article</a>
          </div>
        <?php else: ?>
          <?php foreach($posts as $post): ?>
            <article class="post-card">
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
        <a href="/" class="back">Back to Articles</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:20px;"><?=get_post_date($file)?></div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e1e8ed;">
            <div style="font-size:14px;font-weight:600;color:#5a6c7d;margin-bottom:12px;">Related Topics:</div>
            <div class="tags">
              <?php foreach($postMeta['tags'] as $t): ?>
                <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
              <?php endforeach; ?>
            </div>
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
          <label for="theme">Theme Selection</label>
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
      <div class="footer-section">
        <h3>Disclaimer</h3>
        <p style="font-size:12px;line-height:1.6;">The information provided on this website is for educational purposes only and should not be considered medical advice. Always consult with a qualified healthcare professional.</p>
      </div>
    </div>
    <div class="footer-bottom">&copy; <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?> · Professional Medical Information Platform</div>
  </div>
</footer>
</body>
</html>