<?php declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');

const POSTS_DIR = __DIR__ . '/Posts';
const THEMES_DIR = __DIR__ . '/themes';
const CONFIG_FILE = __DIR__ . '/../config.php';

if (!is_dir(POSTS_DIR)) { @mkdir(POSTS_DIR, 0775, true); }

function get_config(): array {
  $defaults = [
    'SITE_URL' => 'https://yourdomain.com',
    'SITE_NAME' => 'xsukax Flat-File CMS',
    'SITE_DESC' => 'A modern, elegant flat-file CMS for professional blogs',
    'POSTS_PER_PAGE' => 12
  ];
  if (file_exists(CONFIG_FILE)) {
    $config = @include CONFIG_FILE;
    return is_array($config) ? array_merge($defaults, $config) : $defaults;
  }
  return $defaults;
}

$config = get_config();
define('SITE_URL', $config['SITE_URL']);
define('SITE_NAME', $config['SITE_NAME']);
define('SITE_DESC', $config['SITE_DESC']);
define('POSTS_PER_PAGE', (int)$config['POSTS_PER_PAGE']);

if (isset($_POST['change_theme'])) {
  $theme = sanitize_slug($_POST['theme'] ?? 'github');
  setcookie('theme', $theme, time() + (365 * 24 * 60 * 60), '/');
  header('Location: ' . ($_SERVER['REQUEST_URI'] ?? '/'));
  exit;
}

$currentTheme = $_COOKIE['theme'] ?? 'github';

function sanitize_slug(string $s): string {
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9\-_]/', '-', $s);
  $s = preg_replace('/-+/', '-', $s);
  $s = trim($s, '-');
  return $s ?: 'home';
}

function safe_post_path(string $slug): ?string {
  $slug = sanitize_slug($slug);
  $path = POSTS_DIR . '/' . $slug . '.xfc';
  $base = realpath(POSTS_DIR);
  if (!$base) return null;
  $dir = realpath(dirname($path)) ?: dirname($path);
  if (strpos($dir . DIRECTORY_SEPARATOR, $base . DIRECTORY_SEPARATOR) !== 0) return null;
  return $path;
}

function parse_post_meta(string $content): array {
  $meta = ['tags' => [], 'created' => null];
  if (preg_match('/<!--META\s*(.*?)\s*META-->/s', $content, $matches)) {
    $metaLines = explode("\n", trim($matches[1]));
    foreach ($metaLines as $line) {
      if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === 'tags' && $value) {
          $meta['tags'] = array_filter(array_map('trim', explode(',', $value)));
        } elseif ($key === 'created' && $value) {
          $meta['created'] = (int)$value;
        }
      }
    }
  }
  return $meta;
}

function get_post_content(string $content): string {
  return preg_replace('/<!--META\s*.*?\s*META-->/s', '', $content);
}

function get_post_date(int $timestamp): string {
  return date('F j, Y', $timestamp);
}

function extract_title(string $content): string {
  if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
    return strip_tags($matches[1]);
  }
  return 'Untitled Post';
}

function extract_excerpt(string $content, int $length = 160): string {
  $content = strip_tags($content);
  $content = preg_replace('/\s+/', ' ', trim($content));
  return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
}

function get_all_posts(?string $tagFilter = null, ?string $searchQuery = null): array {
  $posts = [];
  $files = @glob(POSTS_DIR . '/*.xfc');
  if (!$files) return [];
  
  foreach ($files as $f) {
    if (!is_file($f)) continue;
    $name = basename($f, '.xfc');
    $postContent = @file_get_contents($f);
    if ($postContent === false) continue;
    
    $meta = parse_post_meta($postContent);
    $cleanContent = get_post_content($postContent);
    $title = extract_title($cleanContent);
    
    if ($tagFilter && !in_array($tagFilter, $meta['tags'])) continue;
    
    if ($searchQuery) {
      $searchLower = strtolower($searchQuery);
      $titleLower = strtolower($title);
      $contentLower = strtolower(strip_tags($cleanContent));
      if (strpos($titleLower, $searchLower) === false && strpos($contentLower, $searchLower) === false) continue;
    }
    
    $created = $meta['created'] ?: @filemtime($f);
    
    $posts[] = [
      'slug' => $name,
      'title' => $title,
      'content' => $cleanContent,
      'tags' => $meta['tags'],
      'date' => get_post_date($created),
      'excerpt' => extract_excerpt($cleanContent),
      'timestamp' => $created,
      'url' => SITE_URL . '/?p=' . urlencode($name)
    ];
  }
  usort($posts, fn($a, $b) => $b['timestamp'] - $a['timestamp']);
  return $posts;
}

function get_available_themes(): array {
  $themes = [];
  $files = @glob(THEMES_DIR . '/*.php');
  if ($files) {
    foreach ($files as $file) {
      $name = basename($file, '.php');
      $themes[$name] = ucfirst($name);
    }
  }
  return $themes;
}

if (isset($_SERVER['REQUEST_URI']) && preg_match('/sitemap\.xml$/i', $_SERVER['REQUEST_URI'])) {
  header('Content-Type: application/xml; charset=UTF-8');
  $posts = get_all_posts();
  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
  echo '  <url><loc>' . htmlspecialchars(SITE_URL . '/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>' . "\n";
  foreach ($posts as $post) {
    echo '  <url><loc>' . htmlspecialchars($post['url']) . '</loc><lastmod>' . date('Y-m-d', $post['timestamp']) . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
  }
  echo '</urlset>';
  exit;
}

if (isset($_SERVER['REQUEST_URI']) && preg_match('/(rss|feed)\.xml$/i', $_SERVER['REQUEST_URI'])) {
  header('Content-Type: application/rss+xml; charset=UTF-8');
  $posts = array_slice(get_all_posts(), 0, 20);
  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
  echo '<channel>' . "\n";
  echo '<title>' . htmlspecialchars(SITE_NAME) . '</title>' . "\n";
  echo '<link>' . htmlspecialchars(SITE_URL) . '</link>' . "\n";
  echo '<description>' . htmlspecialchars(SITE_DESC) . '</description>' . "\n";
  echo '<language>en-us</language>' . "\n";
  foreach ($posts as $post) {
    echo '<item><title>' . htmlspecialchars($post['title']) . '</title><link>' . htmlspecialchars($post['url']) . '</link><guid>' . htmlspecialchars($post['url']) . '</guid><description>' . htmlspecialchars($post['excerpt']) . '</description><pubDate>' . date('r', $post['timestamp']) . '</pubDate></item>' . "\n";
  }
  echo '</channel>' . "\n";
  echo '</rss>';
  exit;
}

header('Content-Type: text/html; charset=UTF-8');

$slug = sanitize_slug($_GET['p'] ?? '');
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$search = isset($_GET['s']) ? trim($_GET['s']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$is_home = empty($slug) || $slug === 'home';
$file = null;
$content = '';
$postMeta = [];
$status = 200;

if (!$is_home) {
  $file = safe_post_path($slug);
  if (!$file || !is_file($file)) {
    $status = 404;
    $content = "<h1>Post Not Found</h1><p>The requested post could not be found.</p>";
  } else {
    $rawContent = @file_get_contents($file);
    $postMeta = parse_post_meta($rawContent);
    $content = get_post_content($rawContent);
  }
  http_response_code($status);
}

$allPosts = get_all_posts($tag ?: null, $search ?: null);
$totalPosts = count($allPosts);
$totalPages = max(1, (int)ceil($totalPosts / POSTS_PER_PAGE));
$page = min($page, $totalPages);
$offset = ($page - 1) * POSTS_PER_PAGE;
$posts = array_slice($allPosts, $offset, POSTS_PER_PAGE);

// Build page title based on context
if (!$is_home) {
  // Single post page
  $pageTitle = extract_title($content) . ' - ' . SITE_NAME;
} elseif ($tag) {
  // Tag filter page
  $pageTitle = $tag . ' - ' . SITE_NAME;
} elseif ($search) {
  // Search results page
  $pageTitle = 'Search: ' . $search . ' - ' . SITE_NAME;
} else {
  // Home page
  $pageTitle = SITE_NAME;
}

$allTags = [];
foreach (get_all_posts() as $p) {
  $allTags = array_merge($allTags, $p['tags']);
}
$allTags = array_values(array_unique($allTags));
shuffle($allTags);

$availableThemes = get_available_themes();

function build_url(array $params): string {
  $base = strtok($_SERVER['REQUEST_URI'], '?');
  $filtered = array_filter($params, fn($v) => $v !== null && $v !== '');
  $query = http_build_query($filtered);
  return $base . ($query ? '?' . $query : '');
}

$themeFile = THEMES_DIR . '/' . sanitize_slug($currentTheme) . '.php';
if (!file_exists($themeFile)) {
  $themeFile = THEMES_DIR . '/github.php';
  $currentTheme = 'github';
}

if (file_exists($themeFile)) {
  include $themeFile;
} else {
  die('Theme file not found. Please ensure themes/github.php exists.');
}
