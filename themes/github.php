<!DOCTYPE html>
<html lang="en" data-color-mode="auto" data-light-theme="light" data-dark-theme="dark">
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
:root{--bgPrimary:#ffffff;--bgSecondary:#f6f8fa;--fgPrimary:#24292f;--fgSecondary:#57606a;--accent:#0969da;--border:#d0d7de;--borderHover:#d8dee4;--success:#1a7f37;--danger:#cf222e;--radius:6px;--shadow:0 1px 3px rgba(0,0,0,0.12);}
@media (prefers-color-scheme:dark){:root{--bgPrimary:#0d1117;--bgSecondary:#161b22;--fgPrimary:#e6edf3;--fgSecondary:#7d8590;--accent:#2f81f7;--border:#30363d;--borderHover:#484f58;--success:#3fb950;--danger:#f85149;--shadow:0 0 transparent;}}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bgPrimary);color:var(--fgPrimary);font:400 14px/1.5 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
a{color:var(--accent);text-decoration:none;}
a:hover{text-decoration:underline;}
.header{background:var(--bgPrimary);border-bottom:1px solid var(--border);padding:16px 0;}
.header-inner{max-width:1280px;margin:0 auto;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.brand{font-size:20px;font-weight:600;color:var(--fgPrimary);}
.brand:hover{text-decoration:none;}
.nav{display:flex;gap:16px;align-items:center;flex-wrap:wrap;}
.nav a{font-size:14px;font-weight:500;color:var(--fgPrimary);}
.admin-link{background:var(--bgSecondary);padding:5px 16px;border-radius:var(--radius);border:1px solid var(--border);}
.admin-link:hover{background:var(--bgSecondary);border-color:var(--borderHover);text-decoration:none;}
.search-form{display:flex;gap:8px;}
.search-input{padding:5px 12px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);font-size:14px;color:var(--fgPrimary);min-width:200px;}
.search-input:focus{outline:none;border-color:var(--accent);}
.search-btn{padding:5px 16px;background:var(--accent);color:#fff;border:none;border-radius:var(--radius);font-size:14px;font-weight:500;cursor:pointer;}
.search-btn:hover{opacity:0.9;}
.container{max-width:1280px;margin:0 auto;padding:0 32px;}
.hero{padding:48px 0 32px;}
.hero h1{font-size:32px;font-weight:600;margin-bottom:8px;}
.hero p{font-size:16px;color:var(--fgSecondary);}
.search-info{background:var(--bgSecondary);border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;margin:24px 0;display:flex;justify-content:space-between;align-items:center;}
.search-info-text{font-size:14px;color:var(--fgSecondary);}
.clear-search{padding:4px 12px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);font-size:12px;font-weight:500;color:var(--fgPrimary);text-decoration:none;}
.clear-search:hover{background:var(--bgSecondary);border-color:var(--borderHover);text-decoration:none;}
.filters{background:var(--bgSecondary);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin:24px 0;}
.filter-title{font-size:12px;font-weight:600;text-transform:uppercase;color:var(--fgSecondary);margin-bottom:12px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{padding:5px 12px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:99px;font-size:12px;font-weight:500;color:var(--fgPrimary);}
.chip:hover{background:var(--bgSecondary);border-color:var(--borderHover);text-decoration:none;}
.chip.active{background:var(--accent);color:#fff;border-color:var(--accent);}
.posts{padding:24px 0;}
.posts-grid{display:grid;gap:16px;}
.post-card{background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);padding:16px;transition:all 0.2s;}
.post-card:hover{border-color:var(--borderHover);box-shadow:var(--shadow);}
.post-card h2{font-size:20px;font-weight:600;margin-bottom:8px;}
.post-card h2 a{color:var(--accent);}
.post-meta{display:flex;gap:12px;align-items:center;margin-bottom:8px;color:var(--fgSecondary);font-size:12px;}
.excerpt{color:var(--fgSecondary);line-height:1.5;margin-bottom:8px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:2px 7px;background:var(--bgSecondary);border:1px solid var(--border);border-radius:99px;font-size:11px;color:var(--fgSecondary);font-weight:500;}
.tag:hover{background:var(--bgPrimary);border-color:var(--borderHover);text-decoration:none;}
.pagination{display:flex;gap:8px;align-items:center;justify-content:center;margin:32px 0;flex-wrap:wrap;}
.page-link{padding:5px 12px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);font-size:14px;color:var(--fgPrimary);font-weight:500;}
.page-link:hover{background:var(--bgSecondary);border-color:var(--borderHover);text-decoration:none;}
.page-link.active{background:var(--accent);color:#fff;border-color:var(--accent);}
.page-link.disabled{opacity:0.5;cursor:not-allowed;pointer-events:none;}
.single{padding:32px 0;}
.back{display:inline-flex;align-items:center;gap:4px;margin-bottom:24px;font-weight:500;padding:5px 12px;background:var(--bgSecondary);border:1px solid var(--border);border-radius:var(--radius);}
.back:hover{background:var(--bgPrimary);border-color:var(--borderHover);text-decoration:none;}
.back:before{content:'←';}
.content{max-width:900px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);padding:32px;}
.content h1{font-size:32px;font-weight:600;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.content h2{font-size:24px;font-weight:600;margin:32px 0 16px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.content h3{font-size:20px;font-weight:600;margin:24px 0 12px;}
.content p{margin:16px 0;line-height:1.6;}
.content ul,.content ol{margin:16px 0;padding-left:32px;line-height:1.6;}
.content li{margin:8px 0;}
.content blockquote{margin:16px 0;padding:0 16px;border-left:4px solid var(--border);color:var(--fgSecondary);}
.content pre{background:var(--bgSecondary);border:1px solid var(--border);border-radius:var(--radius);padding:16px;overflow-x:auto;margin:16px 0;position:relative;}
.content code{background:var(--bgSecondary);padding:2px 6px;border-radius:3px;font-size:85%;font-family:ui-monospace,monospace;}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:var(--radius);border:1px solid var(--border);margin:16px 0;}
.copy-btn{position:absolute;top:8px;right:8px;padding:4px 12px;background:var(--bgSecondary);border:1px solid var(--border);border-radius:var(--radius);font-size:12px;font-weight:500;color:var(--fgPrimary);cursor:pointer;opacity:0;transition:opacity 0.2s,background 0.2s;}
.copy-btn:hover{background:var(--bgPrimary);border-color:var(--borderHover);}
.content pre:hover .copy-btn{opacity:1;}
.copy-btn.copied{background:var(--success);color:#fff;border-color:var(--success);}
.footer{background:var(--bgSecondary);border-top:1px solid var(--border);padding:40px 0;margin-top:64px;}
.footer-inner{max-width:1280px;margin:0 auto;padding:0 32px;display:flex;justify-content:space-between;gap:32px;flex-wrap:wrap;}
.footer-col h3{font-size:14px;font-weight:600;margin-bottom:12px;}
.footer-col p,.footer-col a{color:var(--fgSecondary);font-size:12px;line-height:1.8;}
.footer-col a{display:block;}
.footer-col a:hover{color:var(--accent);}
.theme-selector{margin-top:16px;}
.theme-selector label{display:block;font-size:12px;font-weight:600;margin-bottom:8px;color:var(--fgSecondary);}
.theme-selector select{padding:5px 32px 5px 12px;background:var(--bgPrimary);border:1px solid var(--border);border-radius:var(--radius);font-size:12px;color:var(--fgPrimary);cursor:pointer;}
.theme-selector select:hover{border-color:var(--borderHover);}
.theme-selector button{margin-top:8px;padding:5px 16px;background:var(--accent);color:#fff;border:none;border-radius:var(--radius);font-size:12px;font-weight:500;cursor:pointer;}
.theme-selector button:hover{opacity:0.9;}
.footer-bottom{text-align:center;color:var(--fgSecondary);font-size:12px;margin-top:32px;padding-top:24px;border-top:1px solid var(--border);}
.empty{text-align:center;padding:64px 20px;color:var(--fgSecondary);}
.empty h2{font-size:24px;margin-bottom:12px;color:var(--fgPrimary);}
.empty p{font-size:14px;margin-bottom:16px;}
.empty a{display:inline-block;padding:8px 16px;background:var(--accent);color:#fff;border-radius:var(--radius);font-weight:500;}
.empty a:hover{opacity:0.9;text-decoration:none;}
@media (max-width:768px){.header-inner,.container,.footer-inner{padding:0 16px;}.nav{display:none;}.hero h1{font-size:24px;}.posts-grid{grid-template-columns:1fr;}.content{padding:20px;}.footer-inner{flex-direction:column;}.copy-btn{opacity:1;}.search-input{min-width:150px;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand">📦 <?=htmlspecialchars(SITE_NAME)?></a>
    <form method="get" class="search-form">
      <input type="search" name="s" class="search-input" placeholder="Search posts..." value="<?=htmlspecialchars($search)?>">
      <button type="submit" class="search-btn">Search</button>
    </form>
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
    
    <?php if($search): ?>
    <div class="container">
      <div class="search-info">
        <span class="search-info-text">
          Found <?=$totalPosts?> result<?=$totalPosts !== 1 ? 's' : ''?> for "<?=htmlspecialchars($search)?>"
        </span>
        <a href="/" class="clear-search">Clear search</a>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if(!empty($allTags) && !$search): ?>
    <div class="container">
      <div class="filters">
        <div class="filter-title">Filter by Tags</div>
        <div class="filter-chips">
          <a href="/" class="chip <?=!isset($_GET['tag'])?'active':''?>">All Posts</a>
          <?php foreach(array_slice($allTags,0,20) as $t): ?>
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
            <h2><?=$search ? 'No results found' : 'No posts yet'?></h2>
            <p><?=$search ? 'Try a different search term' : 'Start creating amazing content'?></p>
            <?php if(!$search): ?>
            <a href="/admin.php">Create First Post</a>
            <?php endif; ?>
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
              <a href="<?=build_url(['tag' => $tag ?: null, 's' => $search ?: null, 'page' => $page - 1])?>" class="page-link">← Previous</a>
            <?php else: ?>
              <span class="page-link disabled">← Previous</span>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
              <?php if($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                <a href="<?=build_url(['tag' => $tag ?: null, 's' => $search ?: null, 'page' => $i])?>" class="page-link <?=$i === $page ? 'active' : ''?>"><?=$i?></a>
              <?php elseif(abs($i - $page) == 3): ?>
                <span class="page-link disabled">...</span>
              <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
              <a href="<?=build_url(['tag' => $tag ?: null, 's' => $search ?: null, 'page' => $page + 1])?>" class="page-link">Next →</a>
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
            <span>📅 <?=get_post_date($postMeta['created'] ?: @filemtime($file))?></span>
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
        <h3>📦 <?=htmlspecialchars(SITE_NAME)?></h3>
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

<script>
(function() {
  'use strict';
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCopyButtons);
  } else {
    initCopyButtons();
  }
  
  function initCopyButtons() {
    var codeBlocks = document.querySelectorAll('.content pre');
    
    codeBlocks.forEach(function(pre) {
      if (pre.querySelector('.copy-btn')) return;
      
      var button = document.createElement('button');
      button.className = 'copy-btn';
      button.textContent = 'Copy';
      button.setAttribute('type', 'button');
      button.setAttribute('aria-label', 'Copy code to clipboard');
      
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        var codeElement = pre.querySelector('code');
        var codeText = '';
        
        if (codeElement) {
          codeText = codeElement.textContent || codeElement.innerText;
        } else {
          var preClone = pre.cloneNode(true);
          var btnClone = preClone.querySelector('.copy-btn');
          if (btnClone) {
            btnClone.remove();
          }
          codeText = preClone.textContent || preClone.innerText;
        }
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(codeText).then(function() {
            button.textContent = '✓ Copied!';
            button.classList.add('copied');
            
            setTimeout(function() {
              button.textContent = 'Copy';
              button.classList.remove('copied');
            }, 2000);
          }).catch(function(err) {
            fallbackCopy(codeText, button);
          });
        } else {
          fallbackCopy(codeText, button);
        }
      });
      
      pre.appendChild(button);
    });
  }
  
  function fallbackCopy(text, button) {
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
      var successful = document.execCommand('copy');
      if (successful) {
        button.textContent = '✓ Copied!';
        button.classList.add('copied');
        setTimeout(function() {
          button.textContent = 'Copy';
          button.classList.remove('copied');
        }, 2000);
      } else {
        button.textContent = 'Failed';
        setTimeout(function() {
          button.textContent = 'Copy';
        }, 2000);
      }
    } catch (err) {
      button.textContent = 'Failed';
      setTimeout(function() {
        button.textContent = 'Copy';
      }, 2000);
    }
    
    document.body.removeChild(textArea);
  }
})();
</script>

</body>
</html>
