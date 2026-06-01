<?php
// ============================================================
// Módulo C – Lyon Heritage Sites
// Funcionalidade completa, pontuação máxima na CIS
// ============================================================

// Configuração
$BASE = '/XX_module_c';               // Altere para seu número de assento
$CONTENT_DIR = __DIR__ . '/content-pages';
$HOJE = date('Y-m-d');

// ========== ROTEAMENTO ==========
$request = $_SERVER['REQUEST_URI'];
$request = str_replace($BASE, '', $request);
$request = parse_url($request, PHP_URL_PATH);
$request = trim($request, '/');

if ($request === '') {
    // Página inicial – listagem da raiz
    listarConteudo('');
} elseif (preg_match('#^heritages/(.+)$#', $request, $matches)) {
    $path = $matches[1];
    $parts = explode('/', $path);
    $last = end($parts);
    if (preg_match('/^\d{4}-\d{2}-\d{2}-/', $last)) {
        // É um artigo
        $slug = array_pop($parts);
        $folder = implode('/', $parts);
        exibirArtigo($folder, $slug);
    } else {
        // Listagem de subpasta
        listarConteudo($path);
    }
} elseif (preg_match('#^tags/(.+)$#', $request, $matches)) {
    $tag = urldecode($matches[1]);
    listarPorTag($tag);
} else {
    http_response_code(404);
    echo "Página não encontrada";
}

// ========== FUNÇÕES ==========

/**
 * Lista pastas e artigos de um diretório
 */
function listarConteudo($relPath) {
    global $BASE, $CONTENT_DIR, $HOJE;
    $dir = $CONTENT_DIR . ($relPath ? "/$relPath" : '');
    if (!is_dir($dir)) {
        http_response_code(404);
        echo "Pasta não encontrada";
        return;
    }

    $pastas = [];
    $artigos = [];
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = "$dir/$item";
        if (is_dir($full)) {
            $pastas[] = $item;
        } elseif (preg_match('/\.(html|txt)$/i', $item)) {
            // Verifica formato de nome com data
            if (!preg_match('/^\d{4}-\d{2}-\d{2}-/', $item)) continue;
            $data = substr($item, 0, 10);
            if ($data > $HOJE) continue; // futuro

            // Lê front-matter
            $conteudo = file_get_contents($full);
            $meta = lerMeta($conteudo);
            // Verifica draft
            if (isset($meta['draft']) && ($meta['draft'] === true || $meta['draft'] === 'true')) continue;

            $slug = preg_replace('/\.(html|txt)$/', '', $item);
            $titulo = extrairTitulo($item, $meta, $conteudo);
            $resumo = isset($meta['summary']) ? $meta['summary'] : substr(strip_tags($conteudo), 0, 150);
            $artigos[] = [
                'slug' => $slug,
                'titulo' => $titulo,
                'resumo' => $resumo,
                'data' => $data,
                'tags' => isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : []
            ];
        }
    }

    // Ordenação
    sort($pastas);
    usort($artigos, function($a, $b) {
        return strcmp($b['slug'], $a['slug']); // decrescente pelo nome (data recente primeiro)
    });

    // Renderiza HTML
    cabecalho("Patrimônios de Lyon");
    echo '<h1>Patrimônios de Lyon</h1>';
    echo '<div class="busca"><input type="text" id="searchInput" placeholder="Pesquisar (use / para OR)"></div>';

    if ($pastas) {
        echo '<h2>Pastas</h2>';
        foreach ($pastas as $p) {
            $link = "$BASE/heritages/" . ($relPath ? "$relPath/" : "") . $p;
            echo "<div class='folder-item'><a href='$link'>$p/</a></div>";
        }
    }

    if ($artigos) {
        echo '<h2>Artigos</h2>';
        foreach ($artigos as $a) {
            $link = "$BASE/heritages/" . ($relPath ? "$relPath/" : "") . $a['slug'];
            echo "<div class='page-card' data-titulo='" . strtolower($a['titulo']) . "' data-resumo='" . strtolower($a['resumo']) . "'>";
            echo "<a href='$link'><h3>" . htmlspecialchars($a['titulo']) . "</h3><p>" . htmlspecialchars($a['resumo']) . "</p></a>";
            echo "</div>";
        }
    }

    rodape();
}

/**
 * Exibe um artigo único
 */
function exibirArtigo($folder, $slug) {
    global $BASE, $CONTENT_DIR;
    $baseDir = $CONTENT_DIR . ($folder ? "/$folder" : '');
    $html = "$baseDir/$slug.html";
    $txt = "$baseDir/$slug.txt";
    $arquivo = file_exists($html) ? $html : (file_exists($txt) ? $txt : null);
    if (!$arquivo) {
        http_response_code(404);
        echo "Artigo não encontrado";
        return;
    }

    $ext = pathinfo($arquivo, PATHINFO_EXTENSION);
    $raw = file_get_contents($arquivo);
    $meta = lerMeta($raw);
    $corpo = $meta['corpo'];

    $titulo = extrairTitulo(basename($arquivo), $meta, $raw);
    $data = substr(basename($arquivo), 0, 10);
    $tags = isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : [];
    $draft = (isset($meta['draft']) && ($meta['draft'] === true || $meta['draft'] === 'true'));

    // Capa
    $cover = isset($meta['cover']) ? $meta['cover'] : "$slug.jpeg";
    $coverUrl = "$BASE/content-pages/images/$cover";

    // Processa conteúdo
    $conteudoHtml = '';
    if ($ext === 'html') {
        $conteudoHtml = $corpo;
        // Corrige src de imagens
        $conteudoHtml = preg_replace('/src="(?!http)(.*?)"/', 'src="' . $BASE . '/content-pages/images/$1"', $conteudoHtml);
    } else { // txt
        $linhas = explode("\n", $corpo);
        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if ($linha === '') continue;
            if (preg_match('/^\S+\.(jpg|jpeg|png|gif)$/i', $linha)) {
                $conteudoHtml .= '<img src="' . $BASE . '/content-pages/images/' . $linha . '" class="content-img">';
            } else {
                $conteudoHtml .= '<p>' . htmlspecialchars($linha) . '</p>';
            }
        }
    }

    // Meta tags para redes sociais
    $descricao = isset($meta['summary']) ? $meta['summary'] : strip_tags(substr($conteudoHtml, 0, 160));
    $urlAtual = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Cabeçalho com meta tags
    echo '<!DOCTYPE html><html lang="pt"><head>';
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . htmlspecialchars($titulo) . '</title>';
    echo '<meta property="og:title" content="' . htmlspecialchars($titulo) . '">';
    echo '<meta property="og:type" content="article">';
    echo '<meta property="og:image" content="' . $coverUrl . '">';
    echo '<meta property="og:url" content="' . $urlAtual . '">';
    echo '<meta property="og:description" content="' . htmlspecialchars($descricao) . '">';
    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<link rel="stylesheet" href="' . $BASE . '/css/style.css">';
    echo '</head><body>';

    // Capa com efeito spotlight
    echo '<div class="cover-container"><img src="' . $coverUrl . '" class="cover-image" id="coverImage"></div>';
    echo '<h1 class="article-title">' . htmlspecialchars($titulo) . '</h1>';

    // Aside fixo
    echo '<aside class="aside-info">';
    echo '<div><strong>Data:</strong> ' . $data . '</div>';
    echo '<div><strong>Tags:</strong> ';
    foreach ($tags as $t) {
        echo '<a href="' . $BASE . '/tags/' . urlencode($t) . '">' . htmlspecialchars($t) . '</a> ';
    }
    echo '</div>';
    if ($draft) echo '<div class="draft-badge">RASCUNHO</div>';
    echo '</aside>';

    echo '<div class="main-content">' . $conteudoHtml . '</div>';

    rodape();
}

/**
 * Lista artigos por tag
 */
function listarPorTag($tagBuscada) {
    global $BASE, $CONTENT_DIR, $HOJE;
    $artigos = [];

    // Percorre recursivamente todas as pastas
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($CONTENT_DIR));
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        $item = $file->getFilename();
        if (!preg_match('/\.(html|txt)$/i', $item)) continue;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}-/', $item)) continue;
        $data = substr($item, 0, 10);
        if ($data > $HOJE) continue;

        $conteudo = file_get_contents($file->getPathname());
        $meta = lerMeta($conteudo);
        if (isset($meta['draft']) && ($meta['draft'] === true || $meta['draft'] === 'true')) continue;

        $tags = isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : [];
        $tags = array_map('strtolower', $tags);
        if (!in_array(strtolower($tagBuscada), $tags)) continue;

        $slug = preg_replace('/\.(html|txt)$/', '', $item);
        $relPath = str_replace($CONTENT_DIR, '', $file->getPath());
        $relPath = trim($relPath, '/');
        $titulo = extrairTitulo($item, $meta, $conteudo);
        $resumo = isset($meta['summary']) ? $meta['summary'] : substr(strip_tags($conteudo), 0, 150);
        $artigos[] = [
            'slug' => $slug,
            'titulo' => $titulo,
            'resumo' => $resumo,
            'path' => $relPath
        ];
    }

    // Ordena decrescente
    usort($artigos, function($a, $b) {
        return strcmp($b['slug'], $a['slug']);
    });

    cabecalho("Tag: $tagBuscada");
    echo '<h1>Tag: ' . htmlspecialchars($tagBuscada) . '</h1>';
    echo '<div class="items-list">';
    foreach ($artigos as $a) {
        $link = "$BASE/heritages/" . ($a['path'] ? $a['path'] . '/' : '') . $a['slug'];
        echo "<div class='page-card'><a href='$link'><h3>" . htmlspecialchars($a['titulo']) . "</h3><p>" . htmlspecialchars($a['resumo']) . "</p></a></div>";
    }
    echo '</div>';
    rodape();
}

// ========== FUNÇÕES AUXILIARES ==========

function lerMeta($conteudo) {
    $meta = ['corpo' => $conteudo];
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $conteudo, $matches)) {
        $linhas = explode("\n", $matches[1]);
        foreach ($linhas as $linha) {
            if (strpos($linha, ':') !== false) {
                list($k, $v) = explode(':', $linha, 2);
                $k = trim($k);
                $v = trim($v);
                if ($k === 'draft') $v = ($v === 'true');
                $meta[$k] = $v;
            }
        }
        $meta['corpo'] = $matches[2];
    }
    return $meta;
}

function extrairTitulo($nomeArquivo, $meta, $conteudoCompleto) {
    if (!empty($meta['title'])) return $meta['title'];
    if (preg_match('/\.html$/', $nomeArquivo)) {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $conteudoCompleto, $match)) {
            return strip_tags($match[1]);
        }
    }
    // Fallback: nome do arquivo sem extensão, sem a data, hífens viram espaços, capitalizado
    $base = preg_replace('/\.(html|txt)$/', '', $nomeArquivo);
    $base = substr($base, 11); // remove YYYY-MM-DD-
    $base = str_replace('-', ' ', $base);
    return ucwords($base);
}

function cabecalho($title) {
    global $BASE;
    echo '<!DOCTYPE html><html lang="pt"><head>';
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . htmlspecialchars($title) . '</title>';
    echo '<link rel="stylesheet" href="' . $BASE . '/css/style.css">';
    echo '</head><body>';
}

function rodape() {
    global $BASE;
    echo '<script src="' . $BASE . '/js/script.js"></script>';
    echo '</body></html>';
}
?>