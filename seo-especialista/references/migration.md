# Migração de sites (troca de tecnologia, domínio, tema, redesign)

Trate como projeto de risco: o objetivo é **não perder** o que já ranqueia.

## Antes (baseline)
1. Inventário de URLs: crawl do site atual + sitemap + Search Console (páginas com impressões/cliques) + analytics + backlinks (se disponível). Marque as de maior valor.
2. Registre por URL: status, title, description, headings, canonical, robots, schema, OG, links internos, imagens/alt, hreflang.
3. Capture desempenho base (CWV de campo se houver) e indexação atual.
4. Salve HTML/prints das páginas-chave para comparação.

## Mapa de redirects
- Planilha `URL antiga → URL nova`, uma linha por URL relevante.
- **Preserve URLs quando possível.** Mudou → 301 direto ao equivalente mais próximo, sem cadeias (A→C, não A→B→C), sem loops e sem mandar tudo para a home.
- Inclua variações: http/https, www, barra final, `index.*`, imagens/PDFs indexáveis, paginação, AMP se existir.
- Mudança de domínio: 301 página a página, Change of Address no Search Console, manter o domínio antigo com redirects por bastante tempo.

## Paridade de conteúdo e sinais (React → WordPress, etc.)
Compare antigo vs. novo e liste perdas: seções/textos, headings alterados, metadata, canonical, schema, OG, alt, links internos, sitemap, robots, redirects, conteúdo antes renderizado no servidor e agora só em JS (ou o contrário), comportamento de JS (menus, formulários, tracking), peso/velocidade. Se o novo layout muda conteúdo por motivo legítimo, registre a decisão.

## Pré-lançamento (staging)
- Staging com `noindex`/senha; **remover no go-live** (erro clássico: site novo no ar com noindex ou `Disallow: /`; em WordPress, "Desencorajar mecanismos de busca").
- Testar redirects em lote (`curl -sI` ou crawler), status 200 nas novas, canonicals autorreferentes, sitemap só com URLs finais, robots correto, schema válido, imagens carregando, formulários e tracking funcionando.

## Go-live e depois
- Enviar o sitemap novo; inspecionar URLs-chave no Search Console; acompanhar erros de rastreio, 404s e páginas indexadas nas primeiras semanas.
- Comparar cliques/impressões com o baseline; oscilação leve e temporária é comum; queda persistente em páginas-chave exige investigação por URL.
- Corrigir links internos para apontarem direto ao destino final.
- Monitorar 404 e ajustar o mapa.

## Relatório de migração
Entregue: o que foi preservado, o que mudou (e por quê), perdas detectadas, redirects criados, riscos abertos e o que o usuário ainda precisa verificar (Search Console, DNS etc.).
