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
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:linear-gradient(135deg,#87ceeb 0%,#f0e68c 100%);color:#2c3e50;font:400 18px/1.6 Fredoka,Comic Sans MS,cursive;min-height:100vh;}
a{color:#ff6b35;text-decoration:none;transition:all 0.3s;}
a:hover{color:#ff8c42;transform:scale(1.05);}
.header{background:linear-gradient(90deg,#5eb3d6 0%,#4a9bc7 100%);border-bottom:4px solid #ff6b35;padding:20px 0;box-shadow:0 4px 12px rgba(0,0,0,0.15);}
.header-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;}
.brand{font-size:42px;font-weight:700;color:#fff;text-shadow:3px 3px 0 #ff6b35;position:relative;}
.brand::before{content:'🐾';margin-right:8px;}
.brand:hover{text-decoration:none;transform:rotate(-2deg);}
.nav{display:flex;gap:12px;align-items:center;flex-wrap:wrap;}
.nav a{padding:12px 20px;background:#ffd166;border-radius:25px;font-size:16px;font-weight:600;color:#2c3e50;border:3px solid #ff6b35;box-shadow:0 4px 0 #ff6b35;}
.nav a:hover{background:#ffda85;transform:translateY(-2px);box-shadow:0 6px 0 #ff6b35;}
.nav a:active{transform:translateY(0);box-shadow:0 2px 0 #ff6b35;}
.container{max-width:1000px;margin:0 auto;padding:20px;}
.hero{background:#fff;border-radius:30px;padding:40px;margin-bottom:20px;box-shadow:0 8px 20px rgba(0,0,0,0.1);border:4px solid #ff6b35;position:relative;}
.hero::before{content:'🦴';position:absolute;top:10px;right:20px;font-size:60px;opacity:0.2;transform:rotate(-15deg);}
.hero h1{font-size:48px;font-weight:700;color:#5eb3d6;margin-bottom:16px;text-shadow:2px 2px 0 #ffd166;}
.hero p{font-size:22px;color:#2c3e50;line-height:1.8;}
.filters{background:#fff;border-radius:25px;padding:20px;margin-bottom:20px;box-shadow:0 6px 15px rgba(0,0,0,0.1);border:3px solid #ffd166;}
.filter-title{font-size:20px;font-weight:700;color:#ff6b35;margin-bottom:12px;text-transform:uppercase;}
.filter-chips{display:flex;gap:10px;flex-wrap:wrap;}
.chip{padding:10px 20px;background:linear-gradient(135deg,#ffd166 0%,#ffda85 100%);border-radius:20px;font-size:16px;font-weight:600;color:#2c3e50;border:3px solid #ff6b35;box-shadow:0 4px 0 #ff6b35;}
.chip:hover{background:linear-gradient(135deg,#ffda85 0%,#ffe4a0 100%);transform:translateY(-2px);box-shadow:0 6px 0 #ff6b35;}
.chip.active{background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border-color:#d55728;}
.posts{margin:20px 0;}
.post-card{background:#fff;border-radius:25px;padding:30px;margin-bottom:20px;box-shadow:0 6px 15px rgba(0,0,0,0.1);border:4px solid #5eb3d6;position:relative;overflow:hidden;}
.post-card::before{content:'';position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:radial-gradient(circle,#ffd166 0%,transparent 70%);opacity:0.3;}
.post-card:hover{transform:translateY(-4px);box-shadow:0 12px 25px rgba(0,0,0,0.15);border-color:#ff6b35;}
.post-header{display:flex;gap:16px;margin-bottom:16px;align-items:center;}
.post-icon{width:60px;height:60px;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:28px;border:3px solid #fff;box-shadow:0 4px 8px rgba(0,0,0,0.2);}
.post-info{flex:1;}
.post-title{font-size:26px;font-weight:700;color:#5eb3d6;margin-bottom:4px;}
.post-title a{color:#5eb3d6;}
.post-title a:hover{color:#ff6b35;}
.post-meta{font-size:16px;color:#7f8c8d;}
.excerpt{font-size:18px;color:#2c3e50;line-height:1.7;margin-bottom:16px;}
.tags{display:flex;gap:8px;flex-wrap:wrap;}
.tag{padding:6px 14px;background:#e8f4f8;border-radius:15px;font-size:14px;color:#5eb3d6;font-weight:600;border:2px solid #5eb3d6;}
.tag:hover{background:#ffd166;color:#ff6b35;border-color:#ff6b35;}
.pagination{display:flex;gap:10px;justify-content:center;align-items:center;margin:30px 0;flex-wrap:wrap;}
.page-link{padding:12px 20px;background:#fff;border-radius:20px;font-size:18px;font-weight:700;color:#5eb3d6;box-shadow:0 4px 0 #5eb3d6;border:3px solid #5eb3d6;}
.page-link:hover{background:#ffd166;transform:translateY(-2px);box-shadow:0 6px 0 #ff6b35;border-color:#ff6b35;color:#ff6b35;}
.page-link.active{background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border-color:#d55728;box-shadow:0 4px 0 #d55728;}
.page-link.disabled{opacity:0.4;pointer-events:none;}
.single{padding:20px 0;}
.back{display:inline-block;padding:12px 24px;background:#ffd166;border-radius:25px;font-size:18px;font-weight:700;margin-bottom:20px;border:3px solid #ff6b35;box-shadow:0 4px 0 #ff6b35;}
.back:hover{background:#ffda85;transform:translateY(-2px);box-shadow:0 6px 0 #ff6b35;}
.back::before{content:'← ';}
.content{background:#fff;border-radius:25px;padding:40px;box-shadow:0 8px 20px rgba(0,0,0,0.1);border:4px solid #5eb3d6;}
.content h1{font-size:42px;font-weight:700;margin:0 0 20px;color:#5eb3d6;text-shadow:2px 2px 0 #ffd166;}
.content h2{font-size:34px;font-weight:700;margin:30px 0 16px;color:#ff6b35;}
.content h3{font-size:28px;font-weight:600;margin:24px 0 12px;color:#5eb3d6;}
.content p{margin:16px 0;line-height:1.8;color:#2c3e50;font-size:18px;}
.content ul,.content ol{margin:16px 0;padding-left:32px;line-height:1.8;}
.content li{margin:8px 0;}
.content blockquote{margin:20px 0;padding:20px;border-left:6px solid #ff6b35;background:#fef9e7;border-radius:15px;}
.content pre{background:#2c3e50;color:#fff;border-radius:15px;padding:20px;overflow-x:auto;margin:16px 0;}
.content code{background:#fef9e7;padding:4px 8px;border-radius:8px;font-size:16px;color:#ff6b35;}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:20px;margin:20px 0;border:4px solid #5eb3d6;box-shadow:0 6px 15px rgba(0,0,0,0.1);}
.footer{background:linear-gradient(90deg,#5eb3d6 0%,#4a9bc7 100%);border-top:4px solid #ff6b35;margin-top:40px;padding:30px 0;box-shadow:0 -4px 12px rgba(0,0,0,0.15);}
.footer-inner{max-width:1000px;margin:0 auto;padding:0 20px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;margin-bottom:20px;}
.footer-section{background:rgba(255,255,255,0.2);padding:20px;border-radius:20px;backdrop-filter:blur(10px);}
.footer-section h3{font-size:22px;font-weight:700;margin-bottom:12px;color:#fff;text-shadow:2px 2px 0 #ff6b35;}
.footer-section p,.footer-section a{font-size:16px;color:#fff;line-height:1.8;display:block;margin:4px 0;}
.footer-section a:hover{color:#ffd166;}
.theme-selector{margin-top:16px;padding:16px;background:rgba(255,255,255,0.3);border-radius:15px;}
.theme-selector label{display:block;font-size:14px;font-weight:700;color:#fff;margin-bottom:8px;text-shadow:1px 1px 0 #2c3e50;}
.theme-selector select{width:100%;padding:10px 14px;background:#fff;border:3px solid #ff6b35;border-radius:15px;font-size:16px;color:#2c3e50;font-family:inherit;font-weight:600;}
.theme-selector button{width:100%;margin-top:10px;padding:12px 20px;background:#ffd166;color:#2c3e50;border:3px solid #ff6b35;border-radius:20px;font-size:16px;font-weight:700;cursor:pointer;box-shadow:0 4px 0 #ff6b35;}
.theme-selector button:hover{background:#ffda85;transform:translateY(-2px);box-shadow:0 6px 0 #ff6b35;}
.footer-bottom{text-align:center;color:#fff;font-size:16px;padding-top:20px;border-top:2px solid rgba(255,255,255,0.3);font-weight:600;}
.empty{text-align:center;padding:80px 20px;background:#fff;border-radius:30px;box-shadow:0 8px 20px rgba(0,0,0,0.1);border:4px solid #ff6b35;}
.empty h2{font-size:36px;color:#5eb3d6;margin-bottom:16px;font-weight:700;}
.empty p{font-size:20px;color:#2c3e50;margin-bottom:24px;}
.empty a{display:inline-block;padding:16px 32px;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);color:#fff;border-radius:25px;font-weight:700;font-size:20px;border:3px solid #d55728;box-shadow:0 6px 0 #d55728;}
.empty a:hover{transform:translateY(-3px);box-shadow:0 9px 0 #d55728;}
@media (max-width:768px){.container,.footer-inner{padding:0 12px;}.hero,.post-card,.filters,.content{border-radius:15px;padding:20px;}.hero h1{font-size:32px;}.post-title{font-size:20px;}.brand{font-size:32px;}.nav{width:100%;justify-content:center;margin-top:12px;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">Sitemap</a>
      <a href="/admin.php">Admin</a>
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
        <div class="filter-title">Pick a Tag!</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Stories</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>">#<?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No Stories Yet!</h2>
            <p>Time to start an adventure and create your first story!</p>
            <a href="/admin.php">Create First Story</a>
          </div>
        <?php else: ?>
          <?php foreach($posts as $post): ?>
            <article class="post-card">
              <div class="post-header">
                <div class="post-icon"><?=strtoupper(substr($post['title'], 0, 1))?></div>
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
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page - 1])?>" class="page-link">← Back</a>
            <?php else: ?>
              <span class="page-link disabled">← Back</span>
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
        <a href="/" class="back">Back to Stories</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:16px;color:#7f8c8d;font-size:16px;"><?=get_post_date($file)?></div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:24px;">
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
          <label for="theme">Change Theme</label>
          <select name="theme" id="theme">
            <?php foreach($availableThemes as $themeSlug => $themeName): ?>
              <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>><?=htmlspecialchars($themeName)?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="change_theme">Change Theme!</button>
        </form>
      </div>
      <div class="footer-section">
        <h3>Quick Links</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">Made with fun! © <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?></div>
  </div>
</footer>
</body>
</html>