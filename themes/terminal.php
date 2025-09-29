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
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@keyframes blink{0%,49%{opacity:1}50%,100%{opacity:0}}
@keyframes scan{0%{transform:translateY(-100%)}100%{transform:translateY(100%)}}
:root{--bg:#0a0e14;--fg:#00ff41;--fgDim:#00cc33;--fgMuted:#008822;--accent:#00ffff;--prompt:#ffff00;--error:#ff0055;--border:#00ff41;--shadow:0 0 10px rgba(0,255,65,0.3);}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--fg);font:400 14px/1.6 'JetBrains Mono','Courier New',monospace;position:relative;overflow-x:hidden;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:repeating-linear-gradient(0deg,rgba(0,255,65,0.03) 0px,transparent 1px,transparent 2px,rgba(0,255,65,0.03) 3px);pointer-events:none;z-index:9999;}
body::after{content:'';position:fixed;top:0;left:0;width:100%;height:2px;background:rgba(0,255,65,0.1);animation:scan 8s linear infinite;pointer-events:none;z-index:9998;}
a{color:var(--accent);text-decoration:none;border-bottom:1px dotted var(--accent);}
a:hover{color:var(--prompt);border-bottom-color:var(--prompt);background:rgba(255,255,0,0.1);}
.terminal-window{max-width:1200px;margin:20px auto;background:rgba(10,14,20,0.95);border:2px solid var(--border);box-shadow:var(--shadow);border-radius:8px;}
.terminal-header{background:rgba(0,255,65,0.1);border-bottom:2px solid var(--border);padding:8px 12px;display:flex;align-items:center;gap:8px;}
.terminal-btn{width:12px;height:12px;border-radius:50%;border:1px solid var(--border);}
.terminal-btn.close{background:var(--error);}
.terminal-btn.minimize{background:var(--prompt);}
.terminal-btn.maximize{background:var(--fg);}
.terminal-title{flex:1;text-align:center;font-size:12px;color:var(--fgMuted);text-transform:uppercase;}
.terminal-body{padding:20px;}
.prompt{color:var(--prompt);font-weight:600;}
.prompt::before{content:'$ ';}
.cursor{display:inline-block;width:8px;height:14px;background:var(--fg);margin-left:2px;animation:blink 1s step-end infinite;}
.header{padding:20px 0;border-bottom:2px solid var(--border);margin-bottom:20px;}
.brand{font-size:20px;font-weight:700;color:var(--fg);text-shadow:0 0 10px var(--fg);border:none;}
.brand::before{content:'> ';color:var(--prompt);}
.nav{display:flex;gap:16px;margin-top:12px;flex-wrap:wrap;}
.nav a{font-size:13px;padding:4px 12px;background:rgba(0,255,65,0.1);border:1px solid var(--border);border-radius:4px;}
.nav a:hover{background:rgba(0,255,65,0.2);border-bottom:1px solid var(--border);}
.hero{padding:20px 0;margin-bottom:20px;}
.hero h1{font-size:28px;color:var(--fg);text-shadow:0 0 10px var(--fg);margin-bottom:12px;}
.hero h1::before{content:'# ';}
.hero p{color:var(--fgDim);font-size:14px;line-height:1.8;}
.hero p::before{content:'// ';}
.section-title{font-size:16px;color:var(--prompt);margin:24px 0 12px;font-weight:600;text-transform:uppercase;}
.section-title::before{content:'[';color:var(--fgMuted);}
.section-title::after{content:']';color:var(--fgMuted);}
.filters{background:rgba(0,255,65,0.05);border:1px solid var(--border);padding:16px;margin:20px 0;border-radius:4px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
.chip{padding:4px 12px;background:rgba(0,0,0,0.5);border:1px solid var(--fgMuted);color:var(--fgDim);font-size:12px;border-radius:3px;transition:all 0.2s;}
.chip:hover{background:rgba(0,255,65,0.2);border-color:var(--fg);color:var(--fg);border-bottom:1px solid var(--fg);}
.chip.active{background:rgba(0,255,65,0.3);border-color:var(--fg);color:var(--fg);box-shadow:0 0 10px rgba(0,255,65,0.3);}
.chip::before{content:'[';}
.chip::after{content:']';}
.posts{margin:20px 0;}
.post-list{list-style:none;}
.post-item{background:rgba(0,0,0,0.3);border:1px solid var(--fgMuted);padding:16px;margin-bottom:12px;border-radius:4px;transition:all 0.2s;}
.post-item:hover{border-color:var(--fg);background:rgba(0,255,65,0.05);box-shadow:0 0 15px rgba(0,255,65,0.2);}
.post-item::before{content:'>';color:var(--prompt);font-weight:700;margin-right:8px;}
.post-title{font-size:16px;font-weight:600;margin-bottom:8px;}
.post-title a{color:var(--fg);text-decoration:none;border:none;}
.post-title a:hover{color:var(--accent);text-shadow:0 0 5px var(--accent);}
.post-meta{color:var(--fgMuted);font-size:12px;margin-bottom:8px;}
.post-meta::before{content:'[INFO] ';}
.excerpt{color:var(--fgDim);font-size:13px;line-height:1.6;margin-bottom:8px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:2px 8px;background:rgba(0,0,0,0.5);border:1px solid var(--fgMuted);color:var(--fgMuted);font-size:11px;border-radius:3px;}
.tag:hover{background:rgba(0,255,255,0.2);border-color:var(--accent);color:var(--accent);border-bottom:1px solid var(--accent);}
.tag::before{content:'#';}
.pagination{display:flex;gap:8px;align-items:center;justify-content:center;margin:32px 0;flex-wrap:wrap;}
.page-link{padding:6px 12px;background:rgba(0,0,0,0.5);border:1px solid var(--fgMuted);color:var(--fgDim);font-size:13px;border-radius:3px;}
.page-link:hover{background:rgba(0,255,65,0.2);border-color:var(--fg);color:var(--fg);border-bottom:1px solid var(--fg);}
.page-link.active{background:rgba(0,255,65,0.3);border-color:var(--fg);color:var(--fg);box-shadow:0 0 10px rgba(0,255,65,0.3);}
.page-link.disabled{opacity:0.3;cursor:not-allowed;pointer-events:none;}
.single{padding:20px 0;}
.back{display:inline-block;padding:6px 12px;background:rgba(0,255,65,0.1);border:1px solid var(--border);margin-bottom:20px;border-radius:4px;font-size:13px;}
.back::before{content:'<- ';}
.back:hover{background:rgba(0,255,65,0.2);border-bottom:1px solid var(--border);}
.content{background:rgba(0,0,0,0.3);border:1px solid var(--border);padding:24px;border-radius:4px;}
.content h1{font-size:24px;color:var(--fg);text-shadow:0 0 10px var(--fg);margin:0 0 20px;padding-bottom:12px;border-bottom:1px solid var(--border);}
.content h1::before{content:'## ';}
.content h2{font-size:20px;color:var(--fgDim);margin:24px 0 12px;}
.content h2::before{content:'### ';}
.content h3{font-size:16px;color:var(--fgDim);margin:20px 0 10px;}
.content h3::before{content:'#### ';}
.content p{margin:12px 0;line-height:1.8;color:var(--fgDim);}
.content ul,.content ol{margin:12px 0;padding-left:32px;line-height:1.8;color:var(--fgDim);}
.content li{margin:6px 0;}
.content li::marker{color:var(--fg);}
.content blockquote{margin:16px 0;padding:12px 16px;border-left:3px solid var(--border);background:rgba(0,255,65,0.05);color:var(--fgDim);}
.content blockquote::before{content:'> ';color:var(--fg);font-weight:700;}
.content pre{background:rgba(0,0,0,0.5);border:1px solid var(--border);padding:16px;overflow-x:auto;margin:16px 0;border-radius:4px;}
.content code{background:rgba(0,0,0,0.5);padding:2px 6px;border:1px solid var(--fgMuted);border-radius:3px;font-size:13px;color:var(--accent);}
.content pre code{background:none;padding:0;border:none;color:var(--fg);}
.content img{max-width:100%;height:auto;border:2px solid var(--border);margin:16px 0;border-radius:4px;filter:contrast(1.1) brightness(0.9);}
.content a{color:var(--accent);}
.footer{border-top:2px solid var(--border);padding:20px 0;margin-top:40px;}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;margin-bottom:20px;}
.footer-section h3{font-size:14px;color:var(--prompt);margin-bottom:12px;text-transform:uppercase;}
.footer-section h3::before{content:'// ';}
.footer-section p,.footer-section a{color:var(--fgDim);font-size:12px;line-height:1.8;display:block;margin:4px 0;}
.footer-section a{border:none;}
.footer-section a::before{content:'> ';}
.theme-selector{margin-top:12px;padding:12px;background:rgba(0,0,0,0.3);border:1px solid var(--fgMuted);border-radius:4px;}
.theme-selector label{display:block;font-size:11px;color:var(--fgMuted);margin-bottom:6px;text-transform:uppercase;}
.theme-selector label::before{content:'[THEME] ';}
.theme-selector select{width:100%;padding:6px 10px;background:rgba(0,0,0,0.5);border:1px solid var(--border);color:var(--fg);font-family:inherit;font-size:12px;border-radius:3px;cursor:pointer;}
.theme-selector select:focus{outline:none;border-color:var(--fg);box-shadow:0 0 5px rgba(0,255,65,0.5);}
.theme-selector button{width:100%;margin-top:8px;padding:6px 12px;background:rgba(0,255,65,0.2);border:1px solid var(--border);color:var(--fg);font-family:inherit;font-size:12px;font-weight:600;cursor:pointer;border-radius:3px;text-transform:uppercase;}
.theme-selector button:hover{background:rgba(0,255,65,0.3);box-shadow:0 0 10px rgba(0,255,65,0.3);}
.footer-bottom{text-align:center;color:var(--fgMuted);font-size:11px;padding-top:16px;border-top:1px solid var(--fgMuted);}
.footer-bottom::before{content:'[SYS] ';}
.empty{text-align:center;padding:60px 20px;color:var(--fgDim);}
.empty h2{font-size:20px;color:var(--fg);margin-bottom:12px;}
.empty h2::before{content:'[ERROR] ';}
.empty p{font-size:14px;margin-bottom:16px;}
.empty a{display:inline-block;padding:8px 16px;background:rgba(0,255,65,0.2);border:1px solid var(--border);color:var(--fg);font-weight:600;border-radius:4px;}
.empty a:hover{background:rgba(0,255,65,0.3);box-shadow:0 0 10px rgba(0,255,65,0.3);border-bottom:1px solid var(--border);}
.status-bar{position:fixed;bottom:0;left:0;right:0;background:rgba(0,255,65,0.1);border-top:1px solid var(--border);padding:4px 20px;font-size:11px;color:var(--fgMuted);z-index:1000;}
.status-bar::before{content:'[ONLINE] ';color:var(--fg);}
@media (max-width:768px){.terminal-window{margin:10px;}.terminal-body{padding:12px;}.hero h1{font-size:20px;}.post-item{padding:12px;}.content{padding:16px;}.footer-grid{grid-template-columns:1fr;}.status-bar{font-size:10px;padding:4px 10px;}}
</style>
</head>
<body>
<div class="terminal-window">
  <div class="terminal-header">
    <div class="terminal-btn close"></div>
    <div class="terminal-btn minimize"></div>
    <div class="terminal-btn maximize"></div>
    <div class="terminal-title">xsukax@cms: ~<?=$is_home ? '' : '/post/' . htmlspecialchars($slug)?></div>
  </div>
  
  <div class="terminal-body">
    <header class="header">
      <a href="/" class="brand"><?=htmlspecialchars(SITE_NAME)?><span class="cursor"></span></a>
      <nav class="nav">
        <a href="/rss.xml">rss.xml</a>
        <a href="/sitemap.xml">sitemap.xml</a>
        <a href="/admin.php">sudo admin</a>
      </nav>
    </header>

    <main>
      <?php if($is_home): ?>
        <section class="hero">
          <h1><?=htmlspecialchars(SITE_NAME)?></h1>
          <p><?=htmlspecialchars(SITE_DESC)?></p>
        </section>
        
        <?php if(!empty($allTags)): ?>
        <div class="filters">
          <div class="section-title">Filter Tags</div>
          <div class="filter-chips">
            <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">all</a>
            <?php foreach(array_slice($allTags,0,15) as $t): ?>
              <a href="/?tag=<?=urlencode($t)?>" class="chip <?=$tag===$t?'active':''?>"><?=htmlspecialchars($t)?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <section class="posts">
          <?php if(empty($posts)): ?>
            <div class="empty">
              <h2>NO DATA FOUND</h2>
              <p>Initialize database with first entry</p>
              <a href="/admin.php">CREATE ENTRY</a>
            </div>
          <?php else: ?>
            <div class="section-title">Posts Directory</div>
            <ul class="post-list">
              <?php foreach($posts as $post): ?>
                <li class="post-item">
                  <div class="post-title">
                    <a href="/?p=<?=rawurlencode($post['slug'])?>"><?=htmlspecialchars($post['title'])?></a>
                  </div>
                  <div class="post-meta">
                    <?=htmlspecialchars($post['date'])?> | <?=number_format(strlen($post['content']))?> bytes
                  </div>
                  <div class="excerpt"><?=htmlspecialchars($post['excerpt'])?></div>
                  <?php if(!empty($post['tags'])): ?>
                  <div class="tags">
                    <?php foreach($post['tags'] as $t): ?>
                      <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
            
            <?php if($totalPages > 1): ?>
            <div class="pagination">
              <span class="prompt">PAGES</span>
              <?php if($page > 1): ?>
                <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $page - 1])?>" class="page-link">PREV</a>
              <?php else: ?>
                <span class="page-link disabled">PREV</span>
              <?php endif; ?>
              
              <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <?php if($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                  <a href="<?=build_url(['tag' => $tag ?: null, 'page' => $i])?>" class="page-link <?=$i === $page ? 'active' : ''?>"><?=$i?></a>
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
      <?php else: ?>
        <section class="single">
          <a href="/" class="back">RETURN</a>
          <article class="content">
            <div class="post-meta">
              [DATE] <?=get_post_date($file)?> | [SIZE] <?=number_format(strlen($content))?> bytes
            </div>
            <?=$content?>
            <?php if(!empty($postMeta['tags'])): ?>
            <div style="margin-top:24px;">
              <div class="section-title">Tags</div>
              <div class="tags">
                <?php foreach($postMeta['tags'] as $t): ?>
                  <a href="/?tag=<?=urlencode($t)?>" class="tag"><?=htmlspecialchars($t)?></a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
          </article>
        </section>
      <?php endif; ?>
    </main>

    <footer class="footer">
      <div class="footer-grid">
        <div class="footer-section">
          <h3>System Info</h3>
          <p><?=htmlspecialchars(SITE_NAME)?></p>
          <p><?=htmlspecialchars(SITE_DESC)?></p>
          
          <form method="post" class="theme-selector">
            <label for="theme">Theme Config</label>
            <select name="theme" id="theme">
              <?php foreach($availableThemes as $themeSlug => $themeName): ?>
                <option value="<?=htmlspecialchars($themeSlug)?>" <?=$themeSlug === $currentTheme ? 'selected' : ''?>>
                  <?=htmlspecialchars($themeName)?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" name="change_theme">Apply Config</button>
          </form>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <a href="/rss.xml">RSS Feed</a>
          <a href="/sitemap.xml">XML Sitemap</a>
          <a href="/admin.php">Admin Panel</a>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; <?=date('Y')?> <?=htmlspecialchars(SITE_NAME)?> | Powered by xsukax CMS v1.0
      </div>
    </footer>
  </div>
</div>

<div class="status-bar">
  Session: <?=session_id() ?: 'GUEST'?> | Time: <?=date('H:i:s')?> | Theme: <?=strtoupper($currentTheme)?> | Uptime: <?=number_format(time())?> sec
</div>

</body>
</html>