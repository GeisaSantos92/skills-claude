# SEO em WordPress

## Primeiro: descubra o que já existe
Antes de propor código, identifique plugin SEO (Yoast, Rank Math, SEOPress, AIOSEO), tema, page builder, plugin de cache/otimização e multilíngue, e o que cada um já gera. Veja `wp-content/plugins`, `functions.php` e o HTML final (view-source).
- **Yoast/Rank Math já gerenciam** (conforme configuração): title, meta description, robots, canonical, Open Graph/Twitter, sitemap XML, breadcrumbs, schema (grafo), redirects (premium/módulo), noindex de taxonomias/arquivos.
- Não duplique: um único emissor de canonical, OG e schema. Se o tema ou outro plugin também emite, desative em um deles. Para ajustar o do plugin, use os filtros dele (ex.: `wpseo_title`, `wpseo_metadesc`, `wpseo_canonical`, `wpseo_schema_graph_pieces`; Rank Math: `rank_math/frontend/title`, `rank_math/json_ld`). Confirme os nomes na documentação atual.
- Sem plugin SEO: title via `add_theme_support('title-tag')` (não fixar `<title>` no header); o restante conforme necessidade, preferindo um plugin estabelecido a reinventar sitemap/schema.

## Estrutura e templates
- Respeite a template hierarchy (`single-{cpt}.php`, `archive-{cpt}.php`, `taxonomy-{tax}.php`, `page-{slug}.php`, `404.php`, `search.php`). Ajuste o template existente; não crie paralelos.
- Custom post types/taxonomias: `public`, `has_archive`, rewrite slug estável; decida a indexação dos arquivos com critério; controle o sitemap pelo plugin SEO.
- Menus com `wp_nav_menu` (links reais); breadcrumbs de uma única fonte (plugin SEO ou tema), coerentes com BreadcrumbList.
- Conteúdo de campos ACF/CMB2 deve estar no HTML (não só via JS).
- Comentários, busca (`?s=`), arquivos de autor/data e anexos: avaliar `noindex`/redirect quando irrelevantes.
- Permalinks amigáveis (`/%postname%/`). Não trocar em site publicado sem 301.
- Redirects: plugin (Redirection, Yoast/Rank Math) ou servidor (`.htaccess`/nginx), sem regras duplicadas nos dois.
- Em produção, "Desencorajar mecanismos de busca" (Configurações → Leitura) deve estar **desligado**; confira robots e X-Robots-Tag.

## Enfileiramento e performance

```php
add_action('wp_enqueue_scripts', function () {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style('tema-main', $uri . '/assets/css/main.css', [], filemtime($dir . '/assets/css/main.css'));
    wp_enqueue_script('tema-main', $uri . '/assets/js/main.js', [], filemtime($dir . '/assets/js/main.js'), ['strategy' => 'defer', 'in_footer' => true]);
});
```

- Sempre `wp_enqueue_*` (nunca `<script>`/`<link>` fixos no header); versão via `filemtime()` para cache busting. `strategy => defer` exige WP 6.3+; alternativa: filtro `script_loader_tag`.
- Carregue scripts/estilos só nos templates que usam (`is_page()`, `is_singular()`); remova recursos do core (emojis, embeds, jQuery Migrate) só se comprovadamente não quebrar nada.
- Fontes locais, poucas variações. Imagens: tamanhos do WP (`add_image_size`, `srcset` via `wp_get_attachment_image()`); o WP aplica `lazy` por padrão e tenta poupar a primeira imagem, mas confirme o elemento LCP.
- Cache de página/objeto, compressão e CDN via hospedagem/plugin já adotado. Não empilhe plugins de otimização conflitantes (ex.: dois minificadores).
- Não instale plugin para problema pequeno; não crie plugin próprio sem necessidade.

## `wp_head` / `wp_footer`
Hooks corretos para scripts de terceiros/tracking; prefira gerenciar via GTM ou plugin existente. Ao inserir meta/JSON-LD manual, garanta que o plugin SEO não emita o mesmo (view-source antes e depois).

## Imagens e alt
O `alt` fica no metadado do anexo (`_wp_attachment_image_alt`). No tema, use `wp_get_attachment_image()` para herdar alt/srcset/dimensões; não fixe alt genérico repetido.

## Verificações rápidas
- view-source: um `<title>`, um canonical, um bloco de OG, robots correto, schema sem duplicatas.
- Sitemap (`/sitemap_index.xml` no Yoast e Rank Math) e `/robots.txt`.
- Tag/autor/busca/anexo com política deliberada de indexação.
- Após mudar permalinks/redirects: crawl de 404 e cadeias.
