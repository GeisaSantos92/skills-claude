# Performance, Core Web Vitals e imagens

## Métricas (confirme os limiares na doc atual, web.dev/vitals)
- **LCP** (maior elemento visível): bom ≤ 2,5 s. **INP** (responsividade; substituiu o FID em 2024): bom ≤ 200 ms. **CLS** (estabilidade visual): bom ≤ 0,1. Avaliados no percentil 75 de dados de campo (CrUX); dado de laboratório (Lighthouse) é diagnóstico, não veredito.
- Outros: TTFB, FCP, TBT (proxy de laboratório para INP).
- Prefira dado de campo (Search Console CWV, CrUX, "dados reais" do PSI). Sem medição, diga "não medido".

## Classifique a causa antes de corrigir
SEO · performance · implementação (tema/código) · terceiro (GTM, chat, pixels, embeds) · infraestrutura (hospedagem, cache, TTFB). Nem todo item do Lighthouse é SEO. Avalie custo × benefício.

## LCP
- Identifique o elemento LCP (DevTools/PSI). Se for imagem: sem `loading="lazy"` nela; `fetchpriority="high"`; dimensões corretas; formato moderno; `preload` apenas se o elemento não é descoberto cedo no HTML (ex.: background CSS ou injetado por JS).
- Reduza TTFB (cache de página, hospedagem, plugins pesados), CSS/JS que bloqueiam renderização e fontes lentas.

## INP
Quebrar tarefas longas de JS, remover/adiar scripts de terceiros, evitar handlers pesados, reduzir hidratação e DOM excessivo.

## CLS
`width`/`height` (ou `aspect-ratio`) em imagens/iframes/vídeos, reservar espaço para banners/anúncios/embeds, `font-display` com fallback de métricas próximas, não injetar conteúdo acima do existente.

## Recursos
- CSS crítico inline só quando o ganho justificar a complexidade; senão minifique e evite CSS não usado.
- JS: `defer` para scripts que não precisam bloquear; `async` para independentes; não adicionar biblioteca para pequenas funcionalidades.
- Fontes: locais/WOFF2, poucas famílias e pesos; `preload` só das usadas acima da dobra. **Sem preload/preconnect/dns-prefetch indiscriminado**: cada hint compete por banda; `preconnect` só para origens críticas realmente usadas.
- Terceiros: carregar sob demanda/interação e auditar necessidade.
- Cache e compressão: cache longo para estáticos versionados, Brotli/gzip, HTTP/2+, CDN se aplicável.

## Imagens
- Formato: AVIF/WebP com fallback quando necessário; SVG para vetores; comprimir; dimensões próximas do tamanho exibido; `srcset`/`sizes` no responsivo.
- `width`/`height` sempre. `loading="lazy"` abaixo da dobra; **nunca** na imagem LCP/hero.
- **alt**: descreve função/conteúdo para quem não vê a imagem, em linguagem natural. Sem keyword stuffing. Decorativa → `alt=""`. Imagem que é link/botão → alt descreve destino/ação. Não repetir a legenda literalmente.
- Filename descritivo e limpo (bônus pequeno). Imagens em CSS `background` não têm alt: se carregam conteúdo, use `<img>`.
- Sitemap de imagens só se as imagens são relevantes para descoberta e não são achadas de outra forma.

## Interpretar PageSpeed/Lighthouse
Separe as quatro categorias; priorize o que move LCP/INP/CLS ou a experiência real; ignore "oportunidades" de ganho irrisório; mobile é throttled, desktop não; varie e repita a medição (há oscilação entre execuções).
