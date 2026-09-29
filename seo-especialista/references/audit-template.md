# Modelo de auditoria

Use apenas as seções aplicáveis; marque N/A com motivo. Cada achado segue o formato do SKILL.md (Problema, Prioridade, Tipo, Por que importa, Evidência, Correção, Implementação). Sem nota/score.

Cabeçalho do relatório: escopo · o que foi verificado e como · **o que não foi verificado** (ex.: Search Console, dados de campo, JS renderizado).

| # | Seção | O que checar | Referência |
|---|---|---|---|
| 1 | Indexação | noindex/X-Robots-Tag, soft 404, duplicatas, variações http/https/www/barra, arquivos/taxonomias | technical-seo |
| 2 | Crawling | robots.txt, recursos bloqueados, parâmetros, paginação, facetas, URLs infinitas, órfãs | technical-seo |
| 3 | Arquitetura | hierarquia, profundidade, breadcrumbs, navegação, páginas de serviço/local | technical-seo |
| 4 | URLs | legibilidade, consistência, duplicidade, redirects, risco de mudança | technical-seo |
| 5 | Titles | relevância, intenção, duplicados/vazios, diferenciação | on-page-content |
| 6 | Meta descriptions | utilidade, duplicadas, naturalidade | on-page-content |
| 7 | Headings | hierarquia, clareza, vazios/duplicados, uso só estético | on-page-content |
| 8 | Conteúdo | intenção, completude, originalidade, lacunas, canibalização | on-page-content |
| 9 | Internal linking | contexto, anchors, órfãs, páginas-chave subalimentadas | technical-seo |
| 10 | Imagens | alt, formato, dimensões, lazy/LCP, CLS | performance-images |
| 11 | Structured data | validade, duplicidade, correspondência com o visível | structured-data |
| 12 | Canonical | autorreferência, alvos indexáveis, conflitos com sitemap | technical-seo |
| 13 | Sitemap | só URLs canônicas/200/indexáveis, lastmod, index | technical-seo |
| 14 | Robots | bloqueios acidentais, Sitemap, confusão com noindex | technical-seo |
| 15 | Performance | TTFB, render-blocking, JS/CSS, fontes, terceiros, cache | performance-images |
| 16 | Core Web Vitals | LCP/INP/CLS de campo e causa | performance-images |
| 17 | Mobile | viewport, paridade de conteúdo, usabilidade | technical-seo |
| 18 | JavaScript | conteúdo/links/meta no HTML inicial, status em SPA | technical-seo |
| 19 | SEO local | NAP, GBP, LocalBusiness, páginas locais reais | local-seo |
| 20 | Open Graph | tags, imagem, URL, prévia | on-page-content |
| 21 | Acessibilidade ligada a SEO | alt, headings, links, labels, semântica | on-page-content |

Fechamento:
1. **Resumo executivo**: os 3–5 itens que mais importam.
2. **Plano por prioridade**: Crítico → Alto → Médio → Baixo → Oportunidades.
3. **O que está correto e deve ser mantido.**
4. **Não verificado / depende do usuário.**
