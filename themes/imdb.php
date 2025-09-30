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
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
:root{--imdb-yellow:#f5c518;--imdb-yellow-hover:#d4a90e;--imdb-dark:#121212;--imdb-darker:#000000;--imdb-gray:#2c2c2c;--imdb-light-gray:#757575;--imdb-white:#ffffff;--imdb-border:#404040;--imdb-card-bg:#1a1a1a;--imdb-text:#ffffff;--imdb-text-secondary:#cccccc;--radius:4px;--shadow:0 4px 12px rgba(0,0,0,0.5);}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--imdb-dark);color:var(--imdb-text);font-family:'Roboto',-apple-system,BlinkMacSystemFont,sans-serif;font-size:14px;line-height:1.4;}
a{color:var(--imdb-yellow);text-decoration:none;}
a:hover{text-decoration:underline;}
.header{background:var(--imdb-darker);border-bottom:2px solid var(--imdb-yellow);padding:0;position:sticky;top:0;z-index:100;}
.header-inner{max-width:1400px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;height:60px;}
.brand{display:flex;align-items:center;gap:10px;font-size:24px;font-weight:700;color:var(--imdb-yellow);text-transform:uppercase;letter-spacing:1px;}
.brand:hover{text-decoration:none;opacity:0.9;}
.nav{display:flex;gap:20px;align-items:center;}
.nav a{font-size:14px;font-weight:500;color:var(--imdb-text);padding:8px 12px;border-radius:var(--radius);transition:all 0.2s;}
.nav a:hover{background:var(--imdb-gray);text-decoration:none;color:var(--imdb-yellow);}
.admin-link{background:var(--imdb-yellow);color:var(--imdb-darker)!important;padding:8px 16px!important;border-radius:var(--radius);font-weight:600!important;}
.admin-link:hover{background:var(--imdb-yellow-hover)!important;text-decoration:none!important;}
.container{max-width:1400px;margin:0 auto;padding:0 20px;}
.hero{background:linear-gradient(135deg,var(--imdb-darker),var(--imdb-gray));border-radius:var(--radius);padding:40px;margin:30px 0;text-align:center;border:1px solid var(--imdb-border);position:relative;overflow:hidden;}
.hero:before{content:"";position:absolute;top:0;left:0;right:0;bottom:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><defs><pattern id="stars" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white"/><circle cx="50" cy="50" r="0.5" fill="white"/><circle cx="80" cy="30" r="0.7" fill="white"/><circle cx="30" cy="80" r="0.8" fill="white"/><circle cx="70" cy="70" r="0.6" fill="white"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');}
.hero h1{font-size:36px;font-weight:700;margin-bottom:10px;color:var(--imdb-yellow);position:relative;}
.hero p{font-size:16px;color:var(--imdb-text-secondary);max-width:600px;margin:0 auto;position:relative;}
.filters{background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);padding:20px;margin:25px 0;}
.filter-title{font-size:18px;font-weight:600;margin-bottom:15px;color:var(--imdb-yellow);border-bottom:1px solid var(--imdb-border);padding-bottom:8px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{padding:8px 16px;background:var(--imdb-gray);border:1px solid var(--imdb-border);border-radius:var(--radius);font-size:13px;color:var(--imdb-text);transition:all 0.2s;font-weight:500;}
.chip:hover{background:var(--imdb-yellow);border-color:var(--imdb-yellow);color:var(--imdb-darker);text-decoration:none;}
.chip.active{background:var(--imdb-yellow);color:var(--imdb-darker);border-color:var(--imdb-yellow);font-weight:600;}
.posts{padding:25px 0;}
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;}
.post-card{background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);padding:20px;transition:all 0.3s;position:relative;}
.post-card:hover{border-color:var(--imdb-yellow);box-shadow:var(--shadow);transform:translateY(-2px);}
.post-card:before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:var(--imdb-yellow);border-radius:var(--radius) var(--radius) 0 0;}
.post-card h2{font-size:16px;font-weight:600;margin-bottom:10px;color:var(--imdb-text);line-height:1.3;}
.post-card h2 a{color:var(--imdb-text);}
.post-card h2 a:hover{color:var(--imdb-yellow);text-decoration:none;}
.post-meta{display:flex;gap:12px;align-items:center;margin-bottom:12px;color:var(--imdb-light-gray);font-size:12px;font-weight:500;}
.post-meta span{display:flex;align-items:center;gap:4px;}
.excerpt{color:var(--imdb-text-secondary);line-height:1.5;margin-bottom:15px;font-size:13px;}
.tags{display:flex;gap:6px;flex-wrap:wrap;}
.tag{padding:3px 10px;background:var(--imdb-gray);border:1px solid var(--imdb-border);border-radius:12px;font-size:11px;color:var(--imdb-text-secondary);font-weight:500;text-transform:uppercase;letter-spacing:0.5px;}
.tag:hover{background:var(--imdb-yellow);border-color:var(--imdb-yellow);color:var(--imdb-darker);text-decoration:none;}
.pagination{display:flex;gap:8px;align-items:center;justify-content:center;margin:40px 0;flex-wrap:wrap;}
.page-link{padding:10px 16px;background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);font-size:14px;color:var(--imdb-text);font-weight:500;min-width:44px;text-align:center;transition:all 0.2s;}
.page-link:hover{background:var(--imdb-yellow);border-color:var(--imdb-yellow);color:var(--imdb-darker);text-decoration:none;}
.page-link.active{background:var(--imdb-yellow);color:var(--imdb-darker);border-color:var(--imdb-yellow);font-weight:600;}
.page-link.disabled{opacity:0.4;cursor:not-allowed;pointer-events:none;background:var(--imdb-gray);}
.single{padding:30px 0;}
.back{display:inline-flex;align-items:center;gap:8px;margin-bottom:25px;font-weight:500;padding:10px 18px;background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);color:var(--imdb-text);transition:all 0.2s;}
.back:hover{background:var(--imdb-yellow);border-color:var(--imdb-yellow);color:var(--imdb-darker);text-decoration:none;}
.back:before{content:"←";}
.content{background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);padding:30px;max-width:900px;margin:0 auto;position:relative;}
.content:before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:var(--imdb-yellow);border-radius:var(--radius) var(--radius) 0 0;}
.content h1{font-size:28px;font-weight:700;margin:0 0 20px;color:var(--imdb-yellow);padding-bottom:10px;border-bottom:1px solid var(--imdb-border);}
.content h2{font-size:22px;font-weight:600;margin:25px 0 15px;color:var(--imdb-text);padding-bottom:8px;border-bottom:1px solid var(--imdb-border);}
.content h3{font-size:18px;font-weight:600;margin:20px 0 12px;color:var(--imdb-text);}
.content p{margin:15px 0;line-height:1.6;color:var(--imdb-text-secondary);}
.content ul,.content ol{margin:15px 0;padding-left:25px;line-height:1.6;}
.content li{margin:8px 0;color:var(--imdb-text-secondary);}
.content blockquote{margin:15px 0;padding:15px 20px;border-left:4px solid var(--imdb-yellow);background:var(--imdb-gray);color:var(--imdb-text-secondary);border-radius:0 var(--radius) var(--radius) 0;}
.content pre{background:var(--imdb-gray);border:1px solid var(--imdb-border);border-radius:var(--radius);padding:20px;overflow-x:auto;margin:15px 0;position:relative;}
.content code{background:var(--imdb-gray);padding:3px 6px;border-radius:3px;font-size:85%;font-family:'Courier New',monospace;color:var(--imdb-yellow);}
.content pre code{background:none;padding:0;}
.content img{max-width:100%;height:auto;border-radius:var(--radius);border:1px solid var(--imdb-border);margin:15px 0;}
.copy-btn{position:absolute;top:10px;right:10px;padding:6px 14px;background:var(--imdb-darker);border:1px solid var(--imdb-yellow);color:var(--imdb-yellow);font-size:12px;font-weight:600;cursor:pointer;border-radius:var(--radius);opacity:0;transition:all 0.3s;text-transform:uppercase;letter-spacing:0.5px;font-family:'Roboto',sans-serif;}
.copy-btn:hover{background:var(--imdb-yellow);color:var(--imdb-darker);box-shadow:0 0 10px rgba(245,197,24,0.4);}
.content pre:hover .copy-btn{opacity:1;}
.copy-btn.copied{background:var(--imdb-yellow);color:var(--imdb-darker);border-color:var(--imdb-yellow);opacity:1;}
.footer{background:var(--imdb-darker);border-top:2px solid var(--imdb-yellow);padding:40px 0;margin-top:60px;}
.footer-inner{max-width:1400px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;gap:40px;flex-wrap:wrap;}
.footer-col h3{font-size:16px;font-weight:700;margin-bottom:15px;color:var(--imdb-yellow);text-transform:uppercase;letter-spacing:1px;}
.footer-col p,.footer-col a{color:var(--imdb-text-secondary);font-size:13px;line-height:1.8;}
.footer-col a{display:block;}
.footer-col a:hover{color:var(--imdb-yellow);text-decoration:none;}
.theme-selector{margin-top:20px;}
.theme-selector label{display:block;font-size:13px;font-weight:600;margin-bottom:8px;color:var(--imdb-text-secondary);}
.theme-selector select{padding:8px 35px 8px 12px;background:var(--imdb-card-bg);border:1px solid var(--imdb-border);border-radius:var(--radius);font-size:13px;color:var(--imdb-text);cursor:pointer;width:100%;}
.theme-selector select:hover{border-color:var(--imdb-yellow);}
.theme-selector button{margin-top:10px;padding:8px 16px;background:var(--imdb-yellow);color:var(--imdb-darker);border:none;border-radius:var(--radius);font-size:13px;font-weight:600;cursor:pointer;width:100%;}
.theme-selector button:hover{background:var(--imdb-yellow-hover);}
.footer-bottom{text-align:center;color:var(--imdb-light-gray);font-size:12px;margin-top:30px;padding-top:25px;border-top:1px solid var(--imdb-border);width:100%;}
.empty{text-align:center;padding:60px 20px;color:var(--imdb-text-secondary);background:var(--imdb-card-bg);border-radius:var(--radius);border:1px solid var(--imdb-border);}
.empty h2{font-size:22px;margin-bottom:12px;color:var(--imdb-text);}
.empty p{font-size:14px;margin-bottom:20px;}
.empty a{display:inline-block;padding:12px 24px;background:var(--imdb-yellow);color:var(--imdb-darker);border-radius:var(--radius);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
.empty a:hover{background:var(--imdb-yellow-hover);text-decoration:none;}
@media (max-width:768px){.header-inner,.container,.footer-inner{padding:0 15px;}.nav{display:none;}.hero{padding:25px;}.hero h1{font-size:28px;}.posts-grid{grid-template-columns:1fr;}.content{padding:20px;}.footer-inner{flex-direction:column;gap:25px;}.copy-btn{opacity:1;}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <a href="/" class="brand">
      <span style="font-size:16px;color:var(--imdb-light-gray);margin-left:5px;"><?=htmlspecialchars(SITE_NAME)?></span>
    </a>
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
        <div class="filter-title">Browse by Tags</div>
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
            <h2>No Content Available</h2>
            <p>Be the first to create amazing content</p>
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
          <div class="post-meta" style="margin-bottom:20px;">
            <span>📅 <?=get_post_date($file)?></span>
          </div>
          <?=$content?>
          <?php if(!empty($postMeta['tags'])): ?>
          <div class="tags" style="margin-top:25px;">
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
        <h3><?=htmlspecialchars(SITE_NAME)?></h3>
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
  
  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCopyButtons);
  } else {
    initCopyButtons();
  }
  
  function initCopyButtons() {
    // Find all code blocks within .content
    var codeBlocks = document.querySelectorAll('.content pre');
    
    codeBlocks.forEach(function(pre) {
      // Skip if button already exists
      if (pre.querySelector('.copy-btn')) return;
      
      // Create copy button
      var button = document.createElement('button');
      button.className = 'copy-btn';
      button.textContent = 'Copy';
      button.setAttribute('type', 'button');
      button.setAttribute('aria-label', 'Copy code to clipboard');
      
      // Add click event
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get code text (excluding the button itself)
        var codeElement = pre.querySelector('code');
        var codeText = '';
        
        if (codeElement) {
          codeText = codeElement.textContent || codeElement.innerText;
        } else {
          // Clone the pre element and remove the button to get clean text
          var preClone = pre.cloneNode(true);
          var btnClone = preClone.querySelector('.copy-btn');
          if (btnClone) {
            btnClone.remove();
          }
          codeText = preClone.textContent || preClone.innerText;
        }
        
        // Copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(codeText).then(function() {
            // Success feedback
            button.textContent = 'Copied!';
            button.classList.add('copied');
            
            setTimeout(function() {
              button.textContent = 'Copy';
              button.classList.remove('copied');
            }, 2000);
          }).catch(function(err) {
            // Fallback for errors
            fallbackCopy(codeText, button);
          });
        } else {
          // Fallback for older browsers
          fallbackCopy(codeText, button);
        }
      });
      
      // Append button to pre element
      pre.appendChild(button);
    });
  }
  
  // Fallback copy method for older browsers
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
        button.textContent = 'Copied!';
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
