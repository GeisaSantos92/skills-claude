# Saída do layout

## Stack padrão
HTML + CSS + JS puro, sem build, para facilitar a conversão em tema WordPress. Use React/Next/Tailwind apenas se o usuário pedir ou o projeto já usar (verifique `package.json`; instale só o necessário e informe o comando antes).

## Estrutura de arquivos (pasta de saída, padrão `<pasta>/_layout/`)
```
_layout/
  briefing.md
  index.html
  css/
    tokens.css      # variáveis: cores, fontes, raio, espaçamento, sombras
    base.css        # reset, tipografia, utilitários
    components.css  # botões, cartões, formulários, nav
    sections.css    # estilos por seção
  js/main.js        # menu mobile, revelação por scroll, etc.
  assets/
    img/  fonts/  logo/  favicon/   # cópias otimizadas; originais intocados
  PENDENCIAS.md     # placeholders, imagens ausentes, licenças de fonte, suposições
```
Páginas adicionais (sobre, serviços, contato) seguem o mesmo padrão e compartilham os CSS.

## Tokens
Todo valor visual vem de variável em `tokens.css`: `--color-*` (primária, destaque, superfícies, texto, bordas), `--font-display`, `--font-body`, `--radius-*` (uma escala), `--space-*`, `--shadow-*` (sombra tingida com o matiz do fundo), `--z-*`. Nenhum hex ou tamanho mágico solto nas seções. Isso permite ao usuário retocar a marca em um lugar e facilita o `theme.json`/CSS do tema WordPress.

## HTML
- Semântico: `header` (logo + nav), `main` com `section`s, `footer`. Um H1. IDs/classes descritivos e consistentes (BEM simples ou prefixo por seção) para virarem template parts depois.
- `<html lang="pt-BR">`, viewport, `<title>` e meta description reais (do briefing), favicon (`svg`/`ico`/`apple-touch-icon`), Open Graph básico (`og:title`, `og:description`, `og:image` se houver imagem social).
- Conteúdo dinâmico do futuro WP marcado com comentário (`<!-- WP: loop de serviços -->`).
- Placeholder de dado ausente: texto `[PREENCHER: telefone]` visível e registrado em `PENDENCIAS.md`. Nunca dado inventado.
- Imagens: `width`/`height`, `alt` útil, hero com `fetchpriority="high"` e sem lazy; demais `loading="lazy"`. Fontes via `@font-face` local com `font-display: swap`.

## CSS
- Mobile-first; breakpoints 640/768/1024/1280. Contêiner `max-width` 1200–1400 px. Grid para colunas.
- `min-height: 100dvh` em hero, nunca `100vh`.
- Animações só `transform`/`opacity`, sob `@media (prefers-reduced-motion: no-preference)`.
- Focus visível em todos os interativos.

## JS
Mínimo necessário: menu mobile acessível (botão com `aria-expanded`), revelação por scroll via IntersectionObserver, rolagem suave se útil. Sem bibliotecas para efeitos pequenos.

## SEO base embutido
Title e description únicos, um H1 claro com a proposta da página, hierarquia de headings, `alt` útil, links descritivos, canonical apontando para a URL final quando conhecida. Schema (Organization/LocalBusiness) somente com dados reais do briefing. Para revisão aprofundada, acione a skill `seo-especialista`.

## Pré-voo (verifique cada item; corrija antes de entregar)
**Briefing e conteúdo**
- [ ] Nenhum dado inventado; todo placeholder está em `PENDENCIAS.md`.
- [ ] Textos vêm da pasta/usuário; copy novo marcado como sugestão; releitura de todo o texto visível feita.
- [ ] Um rótulo por intenção de CTA; nenhum botão quebra em 2 linhas no desktop.

**Marca**
- [ ] Logo, favicon, cores e fontes do cliente usados como definidos; logo não foi redesenhado.
- [ ] Uma cor de destaque, um tema, uma escala de raio; sem clichês (gradiente roxo genérico, palette bege+latão por reflexo).

**Layout**
- [ ] Hero cabe na primeira dobra, ≤ 4 elementos de texto, com visual real.
- [ ] Contagem de eyebrows ≤ ceil(seções/3); sem zigue-zague > 2 seguidas; ≥ 4 famílias de layout com 8 seções; máx. 1 marquee.
- [ ] Nav em uma linha (64–80 px); comportamento mobile definido em cada seção multi-coluna (testar 375 px).
- [ ] Sem célula vazia em grades; sem listas longas com linha em cada item.

**Acessibilidade e desempenho**
- [ ] Contraste AA em textos, botões, formulários, placeholders e foco (verifique os pares de cor do `tokens.css`).
- [ ] Um H1, headings em ordem, `alt` adequado, navegação por teclado ok.
- [ ] Movimento respeita `prefers-reduced-motion`; sem listener de `scroll` cru; só `transform`/`opacity`.
- [ ] Imagens otimizadas com dimensões; hero sem lazy; fontes self-hosted com `font-display: swap`.

**Verificação**
- [ ] Layout aberto e conferido em desktop e ~375 px (screenshot/navegador) — ou declarado como "não verificado visualmente".

## Mensagem de entrega
1. Caminho dos arquivos e como abrir (`index.html`).
2. Direção adotada em 3–4 linhas (leitura, paleta, fontes, dials).
3. **Pendências** e **suposições** (copie de `PENDENCIAS.md`).
4. Próximos passos: aprovação do layout → conversão para tema WordPress → SEO (`seo-especialista`) → go-live (`checklist-golive-wordpress`).
