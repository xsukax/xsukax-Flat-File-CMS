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
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;900&display=swap" rel="stylesheet">
<style>
@keyframes glow{0%,100%{box-shadow:0 0 5px #00d9ff,0 0 10px #00d9ff,0 0 15px #00d9ff}50%{box-shadow:0 0 10px #00d9ff,0 0 20px #00d9ff,0 0 30px #00d9ff}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.5}}
@keyframes slide{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#0a0e27;color:#e0e6ed;font:400 15px/1.6 Orbitron,monospace;position:relative;overflow-x:hidden;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 50%,rgba(0,217,255,0.1) 0%,transparent 50%),radial-gradient(circle at 80% 50%,rgba(138,43,226,0.1) 0%,transparent 50%);pointer-events:none;z-index:0;}
a{color:#00d9ff;text-decoration:none;transition:all 0.3s;}
a:hover{color:#8a2be2;text-shadow:0 0 10px #8a2be2;}
.header{background:rgba(10,14,39,0.95);border-bottom:2px solid #00d9ff;backdrop-filter:blur(10px);position:sticky;top:0;z-index:100;box-shadow:0 4px 20px rgba(0,217,255,0.3);}
.header-inner{max-width:1200px;margin:0 auto;padding:0 20px;height:70px;display:flex;align-items:center;justify-content:space-between;}
.brand{font-size:28px;font-weight:900;background:linear-gradient(90deg,#00d9ff 0%,#8a2be2 50%,#00d9ff 100%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:slide 3s linear infinite;letter-spacing:2px;}
.brand:hover{text-shadow:none;}
.nav{display:flex;gap:12px;align-items:center;}
.nav a{padding:10px 20px;background:linear-gradient(135deg,rgba(0,217,255,0.1) 0%,rgba(138,43,226,0.1) 100%);border:1px solid #00d9ff;border-radius:4px;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px;}
.nav a:hover{background:linear-gradient(135deg,rgba(0,217,255,0.3) 0%,rgba(138,43,226,0.3) 100%);border-color:#8a2be2;animation:glow 2s infinite;}
.container{max-width:1000px;margin:0 auto;padding:20px;position:relative;z-index:1;}
.hero{background:linear-gradient(135deg,rgba(0,217,255,0.05) 0%,rgba(138,43,226,0.05) 100%);border:2px solid #00d9ff;border-radius:8px;padding:40px;margin-bottom:20px;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:conic-gradient(from 0deg,transparent,rgba(0,217,255,0.1),transparent 30%);animation:slide 4s linear infinite;pointer-events:none;}
.hero h1{font-size:36px;font-weight:900;background:linear-gradient(90deg,#00d9ff 0%,#8a2be2 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:12px;letter-spacing:1px;position:relative;z-index:1;}
.hero p{font-size:16px;color:#9ca9b3;position:relative;z-index:1;line-height:1.8;}
.filters{background:rgba(10,14,39,0.6);border:1px solid rgba(0,217,255,0.3);border-radius:8px;padding:20px;margin-bottom:20px;backdrop-filter:blur(10px);}
.filter-title{font-size:14px;font-weight:700;color:#00d9ff;margin-bottom:12px;text-transform:uppercase;letter-spacing:2px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{padding:8px 16px;background:rgba(0,217,255,0.1);border:1px solid rgba(0,217,255,0.3);border-radius:20px;font-size:12px;font-weight:600;color:#00d9ff;text-transform:uppercase;letter-spacing:1px;}
.chip:hover{background:rgba(0,217,255,0.2);border-color:#00d9ff;box-shadow:0 0 15px rgba(0,217,255,0.5);}
.chip.active{background:linear-gradient(135deg,#00d9ff 0%,#8a2be2 100%);color:#fff;border-color:transparent;box-shadow:0 0 20px rgba(0,217,255,0.8);}
.posts{margin:20px 0;}
.post-card{background:rgba(10,14,39,0.6);border:1px solid rgba(0,217,255,0.3);border-radius:8px;padding:24px;margin-bottom:16px;backdrop-filter:blur(10px);position:relative;transition:all 0.3s;}
.post-card::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:linear-gradient(180deg,#00d9ff 0%,#8a2be2 100%);opacity:0;transition:opacity 0.3s;}
.post-card:hover{border-color:#00d9ff;box-shadow:0 4px 20px rgba(0,217,255,0.3);transform:translateX(4px);}
.post-card:hover::before{opacity:1;}
.post-header{display:flex;gap:16px;margin-bottom:16px;align-items:center;}
.post-icon{width:48px;height:48px;background:linear-gradient(135deg,#00d9ff 0%,#8a2be2 100%);border-radius:4px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:20px;box-shadow:0 4px 12px rgba(0,217,255,0.4);animation:pulse 3s infinite;}
.post-info{flex:1;}
.post-title{font-size:20px;font-weight:700;color:#e0e6ed;margin-bottom:4px;letter-spacing:0.5px;}
.post-title a{color:#e0e6ed;}
.post-title a:hover{color:#00d9ff;}
.post-meta{font-size:12px;color:#6c7a89;text-transform:uppercase;letter-spacing:1px;}
.excerpt{font-size:14px;color:#9ca9b3;line-height:1.7;margin-bottom:12px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:4px 10px;background:rgba(138,43,226,0.2);border:1px solid rgba(138,43,226,0.4);border-radius:4px;font-size:11px;color:#8a2be2;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
.tag:hover{background:rgba(138,43,226,0.3);border-color:#8a2be2;color:#00d9ff;}
.pagination{display:flex;gap:8px;justify-content:center;align-items:center;margin:30px 0;flex-wrap:wrap;}
.page-link{padding:10px 16px;background:rgba(0,217,255,0.1);border:1px solid rgba(0,217,255,0.3);border-radius:4px;font-size:13px;font-weight:700;color:#00d9ff;text-transform:uppercase;letter-spacing:1px;}
.page-link:hover{background:rgba(0,217,255,0.2);border-color:#00d9ff;box-shadow:0 0 15px rgba(0,217,255,0.5);}
.page-link.active{background:linear-gradient(135deg,#00d9ff 0%,#8a2be2 100%);color:#fff;border-color:transparent;box-shadow:0 0 20px rgba(0,217,255,0.8);}
.page-link.disabled{opacity:0.3;pointer-events:none;}
.single{padding:20px 0;}
.back{display:inline-block;padding:10px 20px;background:rgba(0,217,255,0.1);border:1px solid #00d9ff;border-radius:4px;font-size:13px;font-weight:700;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.back:hover{background:rgba(0,217,255,0.2);box-shadow:0 0 15px rgba(0,217,255,0.5);}
.back::before{content:'← ';}
.content{background:rgba(10,14,39,0.6);border:2px solid rgba(0,217,255,0.3);border-radius:8px;padding:32px;backdrop-filter:blur(10px);}
.content h1{font-size:32px;font-weight:900;margin:0 0 20px;background:linear-gradient(90deg,#00d9ff 0%,#8a2be2 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;letter-spacing:1px;}
.content h2{font-size:26px;font-weight:700;margin:28px 0 16px;color:#00d9ff;letter-spacing:0.5px;}
.content h3{font-size:20px;font-weight:700;margin:24px 0 12px;color:#8a2be2;letter-spacing:0.5px;}
.content p{margin:16px 0;line-height:1.8;color:#9ca9b3;}
.content ul,.content ol{margin:16px 0;padding-left:28px;line-height:1.8;color:#9ca9b3;}
.content li{margin:8px 0;}
.content blockquote{margin:20px 0;padding:16px 20px;border-left:4px solid #00d9ff;background:rgba(0,217,255,0.05);color:#9ca9b3;}
.content pre{background:rgba(0,0,0,0.5);border:1px solid rgba(0,217,255,0.3);border-radius:6px;padding:16px;overflow-x:auto;margin:16px 0;}
.content code{background:rgba(0,217,255,0.1);padding:2px 6px;border-radius:4px;font-size:13px;color:#00d9ff;font-family:monospace;}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:6px;margin:16px 0;border:2px solid rgba(0,217,255,0.3);}
.footer{background:rgba(10,14,39,0.95);border-top:2px solid #00d9ff;margin-top:40px;padding:30px 0;backdrop-filter:blur(10px);}
.footer-inner{max-width:1000px;margin:0 auto;padding:0 20px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;margin-bottom:20px;}
.footer-section{background:rgba(0,217,255,0.05);padding:20px;border-radius:8px;border:1px solid rgba(0,217,255,0.2);}
.footer-section h3{font-size:16px;font-weight:700;margin-bottom:12px;color:#00d9ff;text-transform:uppercase;letter-spacing:1px;}
.footer-section p,.footer-section a{font-size:13px;color:#9ca9b3;line-height:1.8;display:block;margin:4px 0;}
.footer-section a:hover{color:#00d9ff;}
.theme-selector{margin-top:16px;padding:16px;background:rgba(0,0,0,0.3);border-radius:6px;border:1px solid rgba(0,217,255,0.2);}
.theme-selector label{display:block;font-size:12px;font-weight:700;color:#00d9ff;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px;}
.theme-selector select{width:100%;padding:10px 12px;background:rgba(0,0,0,0.5);border:1px solid rgba(0,217,255,0.3);border-radius:4px;font-size:13px;color:#00d9ff;font-family:inherit;}
.theme-selector select:focus{outline:none;border-color:#00d9ff;box-shadow:0 0 10px rgba(0,217,255,0.5);}
.theme-selector button{width:100%;margin-top:10px;padding:10px 16px;background:linear-gradient(135deg,#00d9ff 0%,#8a2be2 100%);color:#fff;border:none;border-radius:4px;font-size:13px;font-weight:700;cursor:pointer;text-transform:uppercase;letter-spacing:1px;}
.theme-selector button:hover{box-shadow:0 0 20px rgba(0,217,255,0.8);}
.footer-bottom{text-align:center;color:#6c7a89;font-size:12px;padding-top:20px;border-top:1px solid rgba(0,217,255,0.2);text-transform:uppercase;letter-spacing:1px;}
.empty{text-align:center;padding:60px 20px;background:rgba(0,217,255,0.05);border-radius:8px;border:1px solid rgba(0,217,255,0.3);}
.empty h2{font-size:24px;color:#00d9ff;margin-bottom:12px;font-weight:700;letter-spacing:1px;}
.empty p{font-size:14px;color:#9ca9b3;margin-bottom:20px;}
.empty a{display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#00d9ff 0%,#8a2be2 100%);color:#fff;border-radius:4px;font-weight:700;font-size:14px;text-transform:uppercase;letter-spacing:1px;}
.empty a:hover{box-shadow:0 0 20px rgba(0,217,255,0.8);text-shadow:none;}
@media (max-width:768px){.container,.footer-inner{padding:0 12px;}.hero,.post-card,.filters,.content{border-radius:6px;padding:20px;}.hero h1{font-size:28px;}.brand{font-size:22px;}.nav{display:none;}.footer-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?></a>
    <nav class="nav">
      <a href="/rss.xml">RSS</a>
      <a href="/sitemap.xml">Map</a>
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
        <div class="filter-title">Filter Neural Network</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Data</a>
          <?php foreach(array_slice($allTags,0,15) as $t): ?>
            <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>"><?=htmlspecialchars($t)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      
      <section class="posts">
        <?php if(empty($posts)): ?>
          <div class="empty">
            <h2>No Data Found</h2>
            <p>Initialize neural network with first data node</p>
            <a href="/admin.php">Create Node</a>
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
                  <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
          
          <?php if($totalPages > 1): ?>
          <div class="pagination">
            <?php if($page > 1): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page - 1])?>" class="page-link">Prev</a>
            <?php else: ?>
              <span class="page-link disabled">Prev</span>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
              <?php if($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $i])?>" class="page-link <?=$i === $page ? 'active' : ''?>"><?=$i?></a>
              <?php elseif(abs($i - $page) == 3): ?>
                <span class="page-link disabled">...</span>
              <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page + 1])?>" class="page-link">Next</a>
            <?php else: ?>
              <span class="page-link disabled">Next</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    </div>
  <?php else: ?>
    <div class="container">
      <section class="single">
        <a href="/" class="back">Return</a>
        <article class="content">
          <div class="post-meta" style="margin-bottom:16px;color:#6c7a89;font-size:12px;"><?=get_post_date($file)?></div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:24px;">
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
        <h3><?=htmlspecialchars(SITE_NAME)?></h3>
        <p><?=htmlspecialchars(SITE_DESC)?></p>
        <form method="post" class="theme-selector">
          <label for="theme">Theme Protocol</label>
          <select name="theme" id="theme">
            <?php foreach($availableThemes as $themeSlug => $themeName): ?>
              <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>><?=htmlspecialchars($themeName)?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="change_theme">Execute</button>
        </form>
      </div>
      <div class="footer-section">
        <h3>System Links</h3>
        <a href="/rss.xml">RSS Feed</a>
        <a href="/sitemap.xml">Sitemap</a>
        <a href="/admin.php">Admin Panel</a>
      </div>
    </div>
    <div class="footer-bottom">Neural Network © <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?></div>
  </div>
</footer>
</body>
</html>