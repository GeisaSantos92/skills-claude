# SEO técnico: rastreio, indexação, arquitetura, URLs, JS

## Como diagnosticar
- Headers e status: `curl -sIL <url>` (siga a cadeia). Veja também `X-Robots-Tag`.
- HTML inicial: `curl -s <url>` e compare com o DOM renderizado (DevTools). Conteúdo/links/meta só no renderizado = risco (ver JS).
- `robots.txt`, sitemaps, `<head>` (robots, canonical, hreflang), Search Console (Inspeção de URL, Páginas, Sitemaps) quando o usuário fornecer.

## Rastreio (crawling)
- `robots.txt`: `User-agent`, `Allow`, `Disallow`, `Sitemap`. Procure bloqueios acidentais (`Disallow: /` herdado de staging, bloqueio de CSS/JS/imagens necessários à renderização).
- Disallow impede rastreio, **não** remove do índice (a URL pode ser indexada sem conteúdo). Para desindexar: `noindex` (página rastreável), 404/410 ou proteção por senha.
- URLs infinitas, filtros/facetas, parâmetros de sessão/ordenação, calendários, busca interna: decidir entre não linkar, `noindex`, canonical ou Disallow conforme o caso; não aplicar tudo por reflexo.
- Paginação: cada página com URL própria, linkada por `<a href>`; canonical autorreferente (não apontar todas para a página 1). `rel=prev/next` não é usado pelo Google.
- Páginas órfãs: comparar lista do sitemap/CMS com URLs alcançáveis por links internos.

## Indexação
- `noindex`/`nofollow` (meta e X-Robots-Tag) em páginas que deveriam ranquear = Crítico.
- Soft 404: página "não encontrada"/vazia retornando 200 → retornar 404/410.
- Baixa qualidade/vazias/duplicadas: consolidar, melhorar ou `noindex`, com critério (não em massa sem análise).
- Variações de URL: HTTP/HTTPS, www/non-www, trailing slash, maiúsculas, `index.php`/`index.html`, parâmetros. Escolher uma versão canônica e redirecionar 301 as demais.
- Busca interna, arquivos por data/autor, tags finas, anexos de mídia (WP): avaliar `noindex` quando não têm valor próprio.

## Canonical
- Padrão: autorreferente, URL absoluta, mesma versão (protocolo/host/barra) do sitemap e dos links internos.
- Erros: apontar para URL 404/redirecionada/noindex, canonical para página de conteúdo diferente, múltiplos canonicals conflitantes, canonical da home para tudo, conflito canonical × sitemap × redirect.
- Canonical é sugestão, não diretiva. Duplicação por parâmetros/variantes → canonical; URL antiga substituída → 301; conteúdo que não deve existir → noindex/410.

## Status HTTP e redirects
- 200 ok · 301/308 permanente · 302/307 temporário (não usar em migração definitiva) · 404 inexistente · 410 removido de vez · 500 erro · 503 indisponível temporário (com `Retry-After` em manutenção).
- Corrija cadeias (A→B→C vira A→C) e loops. Links internos devem apontar direto ao destino final.
- Redirect para página irrelevante (tudo para a home) tende a ser tratado como soft 404: redirecione para o equivalente mais próximo.

## Sitemap XML
- Deve listar só URLs canônicas, indexáveis, status 200. Sem noindex, 404, redirect, parâmetros duplicados ou URLs bloqueadas.
- `lastmod` só se refletir mudança real de conteúdo. `priority`/`changefreq` são ignorados pelo Google.
- Limites: 50.000 URLs / 50 MB por arquivo; usar sitemap index. Informar no `robots.txt` e no Search Console.
- Sitemap listando URL que é noindex ou canonicalizada para outra = sinal conflitante (achado).

## Arquitetura e links internos
- Hierarquia lógica; páginas importantes a poucos cliques da home; navegação e breadcrumbs em HTML com `<a href>` reais.
- Toda página nova sugerida precisa de justificativa: intenção de busca distinta ou função útil. Se duas páginas atendem à mesma intenção, consolidar.
- Links internos: contextuais, anchor descritivo e natural, entre páginas relacionadas por tema/serviço. Não recomendar links aleatórios nem anchors exatos repetidos de forma forçada. Verifique páginas importantes com poucos links recebidos.

## URLs
- Legíveis, minúsculas, hífens, curtas, consistentes, sem parâmetros desnecessários.
- **Não alterar URLs já indexadas sem ganho claro.** Se alterar: 301 mapeado, atualizar links internos, canonical e sitemap.

## JavaScript SEO
- Verifique se title, meta, canonical, headings, texto principal e links existem no HTML inicial ou só após JS.
- CSR puro depende de renderização (mais lento e frágil; alguns crawlers, inclusive de prévia social, não renderizam). SSR/SSG/ISR entregam conteúdo no HTML. A hydration não deve alterar canonical/robots/conteúdo indexável.
- Links devem ser `<a href>`; `onclick`/`router.push` sem href não são rastreáveis.
- Erros 404 em SPAs devem devolver status 404 real (ou noindex), não 200 com tela de erro.
- Scroll infinito/"ver mais" precisa de URLs paginadas alcançáveis.
- Next/Nuxt etc.: confira como a metadata (`generateMetadata`/`head`) é gerada por rota.

## Mobile
Viewport configurada, conteúdo equivalente ao desktop (indexação mobile-first), nada cobrindo o conteúdo, alvos de toque adequados.

## HTML semântico
`header/nav/main/article/section/aside/footer/address/figure/figcaption`, listas reais, `button` para ações e `a` para navegação. Semântica ≠ estilo: não usar heading só para tamanho de fonte.
