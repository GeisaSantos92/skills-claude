---
name: layout-briefing
description: Gera o layout (landing page, site institucional, página de serviço, portfólio) a partir de uma pasta de materiais do cliente. Varre a pasta (logos, favicon, imagens, textos/docx/pdf, paleta, fontes, referências), monta um briefing estruturado com o que existe e o que falta, define a direção de design e só então cria o layout em HTML/CSS/JS pronto para virar tema WordPress. Use quando o usuário pedir "montar o briefing", "levantar o que falta", "fazer o layout desse cliente a partir dessa pasta", "criar a página a partir dessa pasta" ou passar uma pasta bruta de materiais de marca e for preciso organizar tudo antes. Evita layout genérico de IA e nunca inventa dados do cliente. Se o foco é só o design (direção de arte, compor, melhorar o visual), use a skill layout-design.
---

# Layout a partir de briefing

Transforma uma pasta de materiais em (1) um briefing verificado e (2) um layout com identidade própria. Ordem obrigatória: **inventário → briefing → lacunas → direção de design → layout → pré-voo**. Não pule para o código.

## Regras gerais

- **Nunca invente** dados do cliente: nome, slogan, endereço, telefone, e-mail, CNPJ, depoimentos, números, clientes, prêmios, preços. Sem dado → placeholder marcado `[PREENCHER: ...]` no layout e item na lista de pendências.
- Use **os ativos do cliente** (logo, cores, fontes, fotos, textos) como material de partida. Só crie/gere ativos onde a pasta não tem.
- Textos da pasta são a fonte do copy. Pode reorganizar e enxugar; não altere fatos. Copy novo (títulos de seção, CTAs) deve ser plausível e simples, marcado como sugestão no briefing.
- Pergunte **uma vez**, agrupado, e só o que bloqueia. O que tiver default sensato, decida e registre no briefing como "suposição".
- Trabalhe em pasta de saída separada; **não modifique nem apague** os arquivos originais do cliente (copie os ativos para `assets/`).

## Passo 1 — Inventário da pasta

1. Se o usuário não informou a pasta, peça o caminho.
2. Rode `node ~/.claude/skills/layout-briefing/scripts/scan_folder.js "<pasta>"`. Ele lista arquivos por categoria, dimensões de imagens, cores hex de logos SVG e extrai texto de `.docx/.txt/.md` (só Node, sem dependências). Sem Node, use Glob/Read.
3. Leia o que o script não cobre: PDFs (Read com `pages`), imagens-chave (Read mostra a imagem: avalie qualidade, estilo, se tem fundo transparente, se é foto real ou arte), prints/referências de layout, arquivo de manual de marca.
4. Detalhes de classificação e extração em `references/asset-discovery.md`.

## Passo 2 — Briefing

Preencha `references/briefing-template.md` e salve como `briefing.md` na pasta de saída (padrão: `<pasta>/_layout/briefing.md`). Cada campo deve indicar a **origem** (arquivo da pasta, texto do usuário, ou "suposição"). Campos sem origem ficam como lacuna.

## Passo 3 — Lacunas

Mostre ao usuário, em uma única mensagem: resumo do briefing em 8–10 linhas + lista de lacunas por severidade (Bloqueia / Placeholder aceitável / Suposição minha). Pergunte apenas as que bloqueiam (ex.: nome/serviço principal sem nenhum texto, ausência total de logo e nome). Se nada bloqueia, siga em frente e liste as suposições no final.

## Passo 4 — Direção de design

Siga `references/design-rules.md`:
1. Declare em uma linha: **"Leitura: <tipo de página> para <público>, linguagem <vibe>, apoiado em <paleta/tipografia do cliente>."**
2. Defina os três dials (variação, movimento, densidade) a partir do tipo de negócio e do público.
3. Monte os **tokens**: paleta (derivada da marca), tipografia, raio, espaçamento, sombras. Se a marca não define paleta/fonte, proponha e registre como suposição, evitando os padrões clichê listados nas regras.
4. Esboce o **mapa de seções** (ordem, objetivo e família de layout de cada uma) antes de codar. Mostre-o em texto curto.

## Passo 5 — Layout

Siga `references/layout-output.md` (stack, estrutura de arquivos, tokens em CSS variables, responsivo, acessibilidade, SEO on-page básico, prontidão para WordPress). Default de stack: **HTML + CSS + JS puro, sem build**, para converter em tema WP; use React/Next/Tailwind só se o usuário pedir ou o projeto já usar.

## Passo 6 — Pré-voo e entrega

1. Execute o checklist de `references/layout-output.md` (§ Pré-voo). Corrija o que falhar antes de entregar.
2. Abra o resultado para checar visualmente quando houver ferramenta de navegador/screenshot; senão, declare que não foi verificado visualmente.
3. Entregue: caminho dos arquivos, resumo da direção (paleta, fontes, dials), **lista de pendências** (`[PREENCHER]`, imagens ausentes, fontes sem licença/arquivo), suposições feitas, e próximos passos (conversão para WordPress, revisão de SEO com a skill `seo-especialista`, checklist de go-live com `checklist-golive-wordpress`).
