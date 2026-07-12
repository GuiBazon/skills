<?php
/**
 * Módulo C – Lyon Heritage Sites
 * WorldSkills Web Technologies
 *
 * Roteador principal e lógica de back-end.
 * Responsabilidades:
 *   - Roteamento de URLs amigáveis (/heritages/..., /tags/...)
 *   - Leitura recursiva de content-pages/ e extração de front-matter
 *   - Renderização de listagens (C1), artigos (C3) e filtro por tag (C2)
 *   - Geração de meta tags sociais (C4)
 */

// ─── Configurações ────────────────────────────────────────────────────────────

define('CONTENT_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'content-pages');
define('IMAGES_DIR', 'content-pages/images');
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
define('SITE_URL', rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']),
'/'));
define('TODAY', date('Y-m-d'));

// ─── Front Matter ─────────────────────────────────────────────────────────────

function parseFrontMatter(string &$content): array
{
    $frontMatter = [];
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $content, $matches)) {
        $yaml = $matches[1];
        $content = substr($content, strlen($matches[0]));
        foreach (explode("\n", $yaml) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (preg_match('/^(\w+)\s*:\s*(.+)$/', $line, $m)) {
                $key = strtolower(trim($m[1]));
                $value = trim($m[2]);
                if (preg_match('/^\[(.*)\]$/', $value, $listMatch)) {
                    $frontMatter[$key] = array_map('trim', explode(',', $listMatch[1]));
                } else {
                    $frontMatter[$key] = $value;
                }
            }
        }
    }
    return $frontMatter;
}

// ─── Extração de título ──────────────────────────────────────────────────────

function extractTitle(string $filename, array $frontMatter, string $htmlContent): string
{
    // 1. Campo title do front-matter
    if (!empty($frontMatter['title'])) {
        return $frontMatter['title'];
    }
    // 2. Conteúdo do primeiro <h1>
    if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $htmlContent, $m)) {
        return strip_tags($m[1]);
    }
    // 3. Nome do arquivo: remove a data, troca hífens por espaços, capitaliza
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $name);
    return ucwords(str_replace('-', ' ', $name));
}

// ─── Extração de resumo ──────────────────────────────────────────────────────

function extractSummary(string $content, array $frontMatter): string
{
    if (!empty($frontMatter['summary'])) {
        return $frontMatter['summary'];
    }
    $text = preg_replace('/\s+/', ' ', strip_tags($content));
    return mb_substr(trim($text), 0, 150);
}

// ─── Extração de data do nome do arquivo ────────────────────────────────────

function extractDate(string $filename): string
{
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', pathinfo($filename, PATHINFO_FILENAME), $m)) {
        return $m[1];
    }
    return '';
}

// ─── Verificações de metadados ───────────────────────────────────────────────

function isDraft(array $frontMatter): bool
{
    return isset($frontMatter['draft'])
        && (strtolower((string)$frontMatter['draft']) === 'true');
}

function isFutureDate(string $filename): bool
{
    $date = extractDate($filename);
    return $date !== '' && $date > TODAY;
}

function isValidArticleFile(string $filename): bool
{
    return (bool)preg_match('/^\d{4}-\d{2}-\d{2}-.+\.(html|txt)$/i', $filename);
}

// ─── Leitura recursiva dos artigos ───────────────────────────────────────────

function scanArticles(string $dir, bool $recursive = true): array
{
    $result = ['files' => [], 'subdirs' => []];
    if (!is_dir($dir)) return $result;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === 'images') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            if ($recursive) {
                $sub = scanArticles($path, true);
                $result['files'] = array_merge($result['files'], $sub['files']);
            }
            $result['subdirs'][] = $item;
        } elseif (isValidArticleFile($item)) {
            $rawContent = file_get_contents($path);
            $frontMatter = parseFrontMatter($rawContent);
            if (isDraft($frontMatter)) continue;
            if (isFutureDate($item)) continue;

            $result['files'][] = [
                'path'        => $path,
                'filename'    => $item,
                'title'       => extractTitle($item, $frontMatter, $rawContent),
                'summary'     => extractSummary($rawContent, $frontMatter),
                'date'        => extractDate($item),
                'frontMatter' => $frontMatter,
            ];
        }
    }

    sort($result['subdirs']);
    usort($result['files'], fn($a, $b) => strcmp($b['filename'], $a['filename']));

    return $result;
}

function scanAllArticles(): array
{
    return scanArticles(CONTENT_DIR, true);
}

// ─── Busca por tag ────────────────────────────────────────────────────────────

function getArticlesByTag(string $tag): array
{
    $tag = strtolower(trim($tag));
    $articles = [];
    /** @var SplFileInfo $file */
    foreach (
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(CONTENT_DIR, RecursiveDirectoryIterator::SKIP_DOTS)
        ) as $file
    ) {
        if (!$file->isFile() || !isValidArticleFile($file->getFilename())) continue;

        $rawContent = file_get_contents($file->getPathname());
        $frontMatter = parseFrontMatter($rawContent);
        if (isDraft($frontMatter) || isFutureDate($file->getFilename())) continue;

        $tags = (array)($frontMatter['tags'] ?? []);
        $tags = array_map('strtolower', $tags);

        if (in_array($tag, $tags, true)) {
            $articles[] = [
                'path'        => $file->getPathname(),
                'filename'    => $file->getFilename(),
                'title'       => extractTitle($file->getFilename(), $frontMatter, $rawContent),
                'summary'     => extractSummary($rawContent, $frontMatter),
                'date'        => extractDate($file->getFilename()),
                'frontMatter' => $frontMatter,
            ];
        }
    }
    usort($articles, fn($a, $b) => strcmp($b['filename'], $a['filename']));
    return $articles;
}

// ─── Processamento de conteúdo ────────────────────────────────────────────────

function processTxtContent(string $content): string
{
    $html = '';
    foreach (explode("\n", trim($content)) as $line) {
        $line = trim($line);
        if ($line === '') {
            $html .= "<p>&nbsp;</p>\n";
        } elseif (preg_match('/^[^\s]+\.(jpe?g|png|gif)$/i', $line)) {
            $html .= '<img src="' . SITE_URL . '/' . IMAGES_DIR . '/'
                  . htmlspecialchars($line, ENT_QUOTES, 'UTF-8')
                  . '" class="content-img" alt="Imagem do artigo">' . "\n";
        } else {
            $html .= '<p>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "</p>\n";
        }
    }
    return $html;
}

function fixImagePaths(string $html): string
{
    return preg_replace(
        '/src\s*=\s*"([^"]+\.(jpe?g|png|gif))"/i',
        'src="' . SITE_URL . '/' . IMAGES_DIR . '/$1"',
        $html
    );
}

function getCoverUrl(array $frontMatter, string $filename): string
{
    if (!empty($frontMatter['cover'])) {
        return SITE_URL . '/' . IMAGES_DIR . '/' . ltrim($frontMatter['cover'], '/');
    }
    $baseName = pathinfo($filename, PATHINFO_FILENAME);
    foreach (['.jpeg', '.jpg', '.png', '.gif'] as $ext) {
        if (file_exists(CONTENT_DIR . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $baseName . $ext)) {
            return SITE_URL . '/' . IMAGES_DIR . '/' . $baseName . $ext;
        }
    }
    return '';
}

function getTags(array $frontMatter): array
{
    $tags = $frontMatter['tags'] ?? [];
    return is_array($tags) ? $tags : [$tags];
}

// ─── Renderização ─────────────────────────────────────────────────────────────

function renderHeader(string $title, array $meta = []): void
{
    $ogTitle       = htmlspecialchars($meta['og:title'] ?? $title, ENT_QUOTES, 'UTF-8');
    $ogType        = htmlspecialchars($meta['og:type'] ?? 'website', ENT_QUOTES, 'UTF-8');
    $ogImage       = htmlspecialchars($meta['og:image'] ?? '', ENT_QUOTES, 'UTF-8');
    $ogUrl         = htmlspecialchars($meta['og:url'] ?? '', ENT_QUOTES, 'UTF-8');
    $ogDescription = htmlspecialchars($meta['og:description'] ?? '', ENT_QUOTES, 'UTF-8');
    $twitterCard   = htmlspecialchars($meta['twitter:card'] ?? 'summary', ENT_QUOTES, 'UTF-8');
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> – Lyon Heritage Sites</title>
<?php if ($ogTitle): ?>
<meta property="og:title" content="<?= $ogTitle ?>">
<?php endif; ?>
<?php if ($ogType): ?>
<meta property="og:type" content="<?= $ogType ?>">
<?php endif; ?>
<?php if ($ogImage): ?>
<meta property="og:image" content="<?= $ogImage ?>">
<?php endif; ?>
<?php if ($ogUrl): ?>
<meta property="og:url" content="<?= $ogUrl ?>">
<?php endif; ?>
<?php if ($ogDescription): ?>
<meta property="og:description" content="<?= $ogDescription ?>">
<?php endif; ?>
<?php if ($ogImage): ?>
<meta name="twitter:card" content="<?= $twitterCard ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/css/style.css">
</head>
<body>

<div id="modal-overlay" class="modal-overlay" style="display:none" onclick="closeModal()">
    <img id="modal-image" src="" alt="Imagem ampliada">
</div>

<div class="container">
<?php
}

function renderFooter(): void
{
    ?>
</div><!-- /.container -->
<script src="<?= BASE_PATH ?>/js/script.js"></script>
</body>
</html>
<?php
}

function renderListing(array $articlesData, string $pageTitle, bool $showSearch = true): void
{
    renderHeader($pageTitle);
    ?>
    <header class="page-header">
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($showSearch): ?>
        <div class="search-bar">
            <input type="text" id="search-input"
                   placeholder="Buscar artigos (use / para separar termos)..."
                   aria-label="Buscar artigos">
        </div>
        <?php endif; ?>
    </header>

    <div class="articles-list" id="articles-list">
        <?php foreach ($articlesData['subdirs'] as $subdir): ?>
        <article class="article-item folder-item">
            <h2>
                <a href="<?= BASE_PATH ?>/heritages/<?= rawurlencode($subdir) ?>">
                    <?= htmlspecialchars(ucwords(str_replace('-', ' ', $subdir)), ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>
            <p><a href="<?= BASE_PATH ?>/heritages/<?= rawurlencode($subdir) ?>">Pasta de conteúdo</a></p>
        </article>
        <?php endforeach; ?>

        <?php foreach ($articlesData['files'] as $article):
            $relPath = str_replace(CONTENT_DIR . DIRECTORY_SEPARATOR, '', $article['path']);
            $relPath = str_replace(DIRECTORY_SEPARATOR, '/', $relPath);
            $relPath = preg_replace('/\.(html|txt)$/i', '', $relPath);
            $url = BASE_PATH . '/heritages/' . $relPath;
        ?>
        <article class="article-item"
                 data-title="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
                 data-summary="<?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?>">
            <h2><a href="<?= $url ?>"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
            <p><a href="<?= $url ?>"><?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?></a></p>
        </article>
        <?php endforeach; ?>

        <?php if (empty($articlesData['files']) && empty($articlesData['subdirs'])): ?>
        <p class="no-results">Nenhum conteúdo encontrado.</p>
        <?php endif; ?>
    </div>

    <p class="no-results" id="no-search-results" style="display:none">Nenhum resultado encontrado para a busca.</p>
    <?php
    renderFooter();
}

function renderArticlePage(string $filePath): void
{
    $rawContent  = file_get_contents($filePath);
    $frontMatter = parseFrontMatter($rawContent);
    $filename    = basename($filePath);
    $title       = extractTitle($filename, $frontMatter, $rawContent);
    $summary     = extractSummary($rawContent, $frontMatter);
    $date        = extractDate($filename);
    $tags        = getTags($frontMatter);
    $isDraftFlag = isDraft($frontMatter);
    $ext         = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $coverUrl    = getCoverUrl($frontMatter, $filename);

    // Processa o conteúdo conforme o tipo
    if ($ext === 'html') {
        $htmlContent = fixImagePaths($rawContent);
    } else {
        $htmlContent = processTxtContent($rawContent);
    }

    // URL canônica do artigo
    $relPath   = str_replace(CONTENT_DIR . DIRECTORY_SEPARATOR, '', $filePath);
    $relPath   = str_replace(DIRECTORY_SEPARATOR, '/', $relPath);
    $relPath   = preg_replace('/\.(html|txt)$/i', '', $relPath);
    $currentUrl = SITE_URL . '/heritages/' . $relPath;

    $meta = [
        'og:title'       => $title,
        'og:type'        => 'article',
        'og:image'       => $coverUrl,
        'og:url'         => $currentUrl,
        'og:description' => $summary,
        'twitter:card'   => $coverUrl ? 'summary_large_image' : 'summary',
    ];

    renderHeader($title, $meta);
    ?>
    <article class="article-full">
        <div class="article-main">
            <?php if ($coverUrl): ?>
            <div class="article-cover" id="article-cover">
                <img src="<?= htmlspecialchars($coverUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt="Capa do artigo: <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                     class="cover-image" id="cover-image">
            </div>
            <?php endif; ?>

            <h1 class="article-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

            <div class="article-content" id="article-content">
                <?= $htmlContent ?>
            </div>
        </div>

        <aside class="article-meta" id="article-meta">
            <?php if ($date): ?>
            <p class="meta-date"><strong>Data:</strong> <?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($tags): ?>
            <div class="meta-tags">
                <strong>Tags:</strong>
                <?php foreach ($tags as $tag): ?>
                <a href="<?= BASE_PATH ?>/tags/<?= rawurlencode(trim($tag)) ?>"
                   class="tag-link"><?= htmlspecialchars(trim($tag), ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($isDraftFlag): ?>
            <span class="draft-badge">RASCUNHO</span>
            <?php endif; ?>
        </aside>
    </article>
    <?php
    renderFooter();
}

function renderTagListing(string $tag): void
{
    $articles  = getArticlesByTag($tag);
    $pageTitle = 'Tag: ' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');

    renderHeader($pageTitle);
    ?>
    <header class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="<?= BASE_PATH ?>/">&larr; Voltar para o início</a></p>
    </header>

    <div class="articles-list">
        <?php foreach ($articles as $article):
            $relPath = str_replace(CONTENT_DIR . DIRECTORY_SEPARATOR, '', $article['path']);
            $relPath = str_replace(DIRECTORY_SEPARATOR, '/', $relPath);
            $relPath = preg_replace('/\.(html|txt)$/i', '', $relPath);
            $url = BASE_PATH . '/heritages/' . $relPath;
        ?>
        <article class="article-item"
                 data-title="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
                 data-summary="<?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?>">
            <h2><a href="<?= $url ?>"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
            <p><a href="<?= $url ?>"><?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?></a></p>
        </article>
        <?php endforeach; ?>

        <?php if (empty($articles)): ?>
        <p class="no-results">Nenhum artigo encontrado com a tag "<?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>".</p>
        <?php endif; ?>
    </div>
    <?php
    renderFooter();
}

function render404(): void
{
    http_response_code(404);
    renderHeader('Página não encontrada');
    ?>
    <h1>404 – Página não encontrada</h1>
    <p><a href="<?= BASE_PATH ?>/">Voltar para o início</a></p>
    <?php
    renderFooter();
}

// ─── Roteador ─────────────────────────────────────────────────────────────────

function router(): void
{
    $path = substr($_SERVER['REQUEST_URI'], strlen(BASE_PATH));
    $path = strtok($path, '?');
    $path = rtrim($path, '/');
    $path = ltrim($path, '/');
    $segments = $path !== '' ? explode('/', $path) : [];

    // Rota raiz: /XX_module_c/
    if (empty($path)) {
        renderListing(scanAllArticles(), 'Lyon Heritage Sites');
        return;
    }

    // Sanitiza segmentos: rejeita path traversal (..)
    $segments = array_values(array_filter($segments, function ($s) {
        return $s !== '..' && $s !== '.' && $s !== '';
    }));

    // Rota: /tags/:tag
    if (!empty($segments) && $segments[0] === 'tags') {
        $tag = $segments[1] ?? '';
        if ($tag !== '') {
            renderTagListing(urldecode($tag));
            return;
        }
        header('Location: ' . BASE_PATH . '/');
        exit;
    }

    // Rota: /heritages/...
    if (!empty($segments) && $segments[0] === 'heritages') {
        $heritageSegments = array_slice($segments, 1);
        if (empty($heritageSegments)) {
            header('Location: ' . BASE_PATH . '/');
            exit;
        }

        $relPath  = implode(DIRECTORY_SEPARATOR, $heritageSegments);
        $fullPath = CONTENT_DIR . DIRECTORY_SEPARATOR . $relPath;

        // Se for um diretório, lista o conteúdo
        if (is_dir($fullPath)) {
            $folderName = ucwords(str_replace('-', ' ', end($heritageSegments)));
            renderListing(scanArticles($fullPath, true), $folderName);
            return;
        }

        // Tenta encontrar o arquivo .html ou .txt
        foreach (['.html', '.txt'] as $ext) {
            $testPath = $fullPath . $ext;
            if (file_exists($testPath)) {
                renderArticlePage($testPath);
                return;
            }
        }

        // Se o último segmento parece um artigo, procura recursivamente
        $lastSegment = end($heritageSegments);
        if (preg_match('/^\d{4}-\d{2}-\d{2}-/', $lastSegment)) {
            $parentDir = CONTENT_DIR . DIRECTORY_SEPARATOR
                       . implode(DIRECTORY_SEPARATOR, array_slice($heritageSegments, 0, -1));
            if (is_dir($parentDir)) {
                /** @var SplFileInfo $file */
                foreach (
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($parentDir, RecursiveDirectoryIterator::SKIP_DOTS)
                    ) as $file
                ) {
                    if (!$file->isFile()) continue;
                    $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    if ($name === $lastSegment && in_array(strtolower($file->getExtension()), ['html', 'txt'], true)) {
                        renderArticlePage($file->getPathname());
                        return;
                    }
                }
            }
        }

        render404();
        return;
    }

    // Qualquer outra rota: 404
    render404();
}

// Executa o roteador apenas quando acessado como script principal (não via require)
if (isset($_SERVER['REQUEST_URI'])) {
    router();
}
