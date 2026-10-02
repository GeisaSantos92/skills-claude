---
name: layout-design
description: Cria o layout do site (landing page, institucional, página de serviço, portfólio) a partir do briefing enviado pelo usuário. Lê o briefing (arquivo .md/.docx/.pdf, Google Docs, texto colado ou briefing.md da skill layout-briefing) e extrai o que define o site, como identidade do cliente, paleta, fontes, logo, assunto, público, objetivo/CTA, seções e conteúdo. A partir disso define a direção de arte, monta o sistema visual com a paleta do cliente, compõe as seções e faz rodadas de autocrítica com screenshot. Foco em design, não em fazer perguntas. Saída em HTML/CSS/JS pronta para virar tema WordPress. Use quando o usuário pedir "cria o layout a partir desse briefing", "faz o site com base no briefing", "desenha a página desse cliente", "faz o design dessa landing", "melhora o visual" ou enviar um briefing para virar layout. Se ainda não há briefing e só existe uma pasta bruta de materiais, use a skill layout-briefing.
---

# Layout com foco em design

Objetivo: transformar o briefing do cliente num layout com personalidade, fiel à identidade da marca e bem acabado. O briefing diz **o que** o site é; esta skill decide **como** ele fica. O trabalho é de diretor de arte: decidir, compor, revisar. **Não faça entrevista.** Ordem: **briefing → ficha de design → conceito → sistema visual → composição → código → revisão com screenshot → entrega**.

## Regras gerais

- **O briefing é a fonte de verdade.** Paleta, fontes, logo, tom, público, objetivo, seções e textos definidos no briefing são seguidos à risca. Sua criatividade entra no que o briefing não define: composição, hierarquia, ritmo, tratamento de imagem, detalhes.
- **Decida, não pergunte.** O que o briefing não cobre, decida pelo contexto dele e registre como "decisão de design". Só pergunte se faltar o essencial (não dá para saber do que é o site), em uma única pergunta.
- **Nunca invente dados do cliente** (contatos, números, depoimentos, clientes, prêmios, preços). Use `[PREENCHER: ...]` e liste nas pendências. Copy de apoio (títulos de seção, CTAs) pode ser escrito, simples e plausível.
- **A marca vence o gosto.** Logo, cores e fontes do cliente são o ponto de partida; não redesenhe o logo.
- Não altere os arquivos originais do cliente. Saída padrão: `<pasta>/_layout/` (ou a pasta indicada).
- Regras de design obrigatórias: `~/.claude/skills/layout-briefing/references/design-rules.md`. Regras de código e pré-voo técnico: `~/.claude/skills/layout-briefing/references/layout-output.md`. Leia as duas antes de codar.

## Passo 1 — Ler o briefing e montar a ficha de design

1. **Localize o briefing.** Se o usuário não enviou nem indicou, peça o briefing (só isso). Formatos: `.md/.txt` (Read), `.docx` (use o extrator do `scan_folder.js` da layout-briefing ou a skill `docx`), `.pdf` (Read com `pages`), Google Docs/Drive (conectores, se disponíveis), texto colado na conversa, `briefing.md` gerado pela `layout-briefing`.
2. **Abra os ativos que o briefing cita** (logo, manual de marca, fotos, referências). Se estiverem numa pasta, rode `node ~/.claude/skills/layout-briefing/scripts/scan_folder.js "<pasta>"`. **Olhe** com Read o logo, as referências e as fotos principais: a cor real do logo e o estilo das fotos pesam tanto quanto o texto.
3. **Monte a ficha de design** (`ficha-design.md` na pasta de saída), uma linha por item, indicando a origem (`briefing`, `ativo: arquivo` ou `decisão de design`):
   - **Cliente e assunto**: nome, o que faz, segmento, cidade/área.
   - **Público e objetivo**: quem visita, ação principal (CTA) e secundária.
   - **Tipo de site**: landing, institucional, serviço, portfólio; páginas pedidas.
   - **Identidade**: logo (versões, fundo), paleta com hex e papel de cada cor, fontes, tom de voz, palavras de estilo do cliente, do's e don'ts.
   - **Conteúdo**: seções pedidas e textos disponíveis para cada uma, provas reais, contatos.
   - **Referências**: sites/imagens que o cliente gostou e **o que** neles (estrutura, clima, cor). Inspiração, nunca cópia.
   - **Restrições**: o que o cliente não quer, requisitos técnicos, prazo.
4. Paleta só com nomes ("azul e laranja") ou sem hex: tire o hex do logo/manual. Sem nenhuma cor definida: proponha a partir do segmento e das palavras de estilo e marque como decisão de design.
5. Conflitos (ex.: briefing pede "sóbrio", referência é colorida e lúdica): siga o briefing escrito e registre o conflito na entrega.
6. Layout existente para melhorar: além do briefing, tire screenshot primeiro (Passo 6) e diagnostique com `references/critique.md` antes de mexer.

Mostre ao usuário a ficha resumida em 5–8 linhas ("Entendi assim: ...") e siga sem esperar, a menos que algo essencial esteja ambíguo.

## Passo 2 — Conceito e direção de arte

Siga `references/art-direction.md`, sempre derivando da ficha de design:
1. **Leitura** em uma linha (tipo de página, público, vibe, ativos da marca).
2. **Três adjetivos** da marca (use as palavras de estilo do briefing quando existirem) e o **conceito** (uma ideia visual que organiza a página, nascida do assunto do cliente).
3. **Direção** escolhida do catálogo (ou mistura de duas), com os dials de variação/movimento/densidade.
4. **Elemento-assinatura**: o único gesto memorável da página (ex.: recorte de foto, tipografia gigante, grade aparente, faixa diagonal). Um só, repetido com disciplina.

Se o usuário pedir opções ou o material for muito aberto, proponha **2–3 direções** curtas (conceito + paleta + fontes + assinatura) e um `direcoes.html` com moodboard de tokens e um hero de cada; siga com a escolhida. Caso contrário, escolha uma e siga.

## Passo 3 — Sistema visual

Monte `css/tokens.css` antes de qualquer seção (ver `references/visual-craft.md`), com as **cores e fontes do briefing** nos papéis certos (primária, destaque, superfícies, texto). Se uma cor da marca não passar em contraste como texto, use-a em superfície/detalhe e derive um tom acessível, sem trocar a cor da marca. Defina escala tipográfica com proporção definida, paleta com papéis e proporção 60/30/10, escala de espaçamento em 8 px, raio, sombra, grade (colunas, margens, contêiner) e tratamento de imagem. Confira contraste AA de cada par texto/fundo.

## Passo 4 — Composição

Monte o mapa de seções com `references/section-patterns.md`. As seções e a ordem vêm do briefing quando ele define; se não define, use a sequência sugerida para o tipo de site e o objetivo do briefing. Para cada seção: objetivo, conteúdo (de qual parte do briefing), padrão escolhido (código do catálogo) e onde entra a assinatura. Verifique variedade (≥ 4 famílias em 8 seções, ritmo claro/denso/arejado) e mostre o mapa ao usuário em texto curto antes de codar. Não espere aprovação, a menos que ele tenha pedido.

## Passo 5 — Código

HTML + CSS + JS puro, sem build (salvo pedido ou projeto existente), conforme `layout-output.md`. Construa de cima para baixo, seção completa por vez, com todos os valores vindos dos tokens. Hero primeiro e com mais cuidado: é ele que vende a direção.

## Passo 6 — Revisão com screenshot (obrigatória)

1. `node ~/.claude/skills/layout-design/scripts/screenshot.js "<_layout>/index.html"` gera fatias em `_shots/` (1440 e 375 px, com movimento reduzido emulado). Precisa de Node 22+ e Chrome ou Edge.
2. Leia **todas** as fatias com Read e avalie com `references/critique.md` (composição, hierarquia, ritmo, acabamento, mobile, aviso de rolagem horizontal).
3. Corrija e repita. Mínimo **2 rodadas**; pare quando a crítica não encontrar problema de severidade alta ou média. Se o script falhar, diga isso e entregue como "não verificado visualmente".
4. Rode o pré-voo de `layout-output.md`.

## Passo 7 — Entrega

Em poucas linhas: caminho dos arquivos; como o briefing virou design (o que foi seguido à risca e o que foi decisão de design); conflitos encontrados no briefing; conceito, direção e assinatura; paleta e fontes; o que mudou nas rodadas de revisão; pendências (`[PREENCHER]`, imagens a obter, licenças de fonte) em `PENDENCIAS.md`; próximos passos (ajustes do cliente → tema WordPress → `seo-especialista` → `checklist-golive-wordpress`).
