# Regras de direção de design (anti layout genérico)

Vale para landing pages, sites institucionais/de serviço e portfólios. Cada regra é contextual: primeiro leia o briefing, depois escolha. A marca do cliente sempre vence as preferências abaixo.

## 1. Leitura antes de desenhar
Declare em uma linha: **"Leitura: <tipo de página> para <público>, linguagem <vibe>, apoiado em <paleta/tipografia do cliente>."** O público define a estética, não o gosto do modelo. Restrições silenciosas (saúde, jurídico, setor público, público idoso, B2B sério) pesam mais que estilo.

## 2. Dials
Escolha valores (1–10) e siga-os em todas as decisões.
- **Variação** (1 simétrico → 10 assimétrico/experimental)
- **Movimento** (1 estático → 10 cinematográfico)
- **Densidade** (1 galeria arejada → 10 painel denso)

| Contexto | Variação | Movimento | Densidade |
|---|---|---|---|
| Institucional/serviço local (default) | 5–6 | 3–4 | 3–4 |
| Saúde, jurídico, financeiro, confiança | 3–4 | 2–3 | 4–5 |
| SaaS/tecnologia | 6–7 | 4–6 | 3–4 |
| Marca premium/lifestyle | 7–8 | 5–6 | 2–3 |
| Agência/criativo/portfólio de design | 8–9 | 6–8 | 3 |
| Blog/editorial | 5–6 | 3–4 | 3 |

Se o movimento passa de 4, a página precisa realmente mover (entrada do hero, revelação por scroll, hover em CTAs). Se não vai entregar movimento funcional, baixe para 3.

## 3. Tipografia
- Use as fontes da marca. Sem definição: par sans coerente com a leitura (ex.: Geist, Outfit, Cabinet Grotesk, Satoshi, Manrope, DM Sans, Plus Jakarta Sans), evitando Inter como padrão automático.
- Serifa só se a marca pedir ou o segmento for realmente editorial/luxo/tradição, e com justificativa. Não use Fraunces nem Instrument Serif por reflexo.
- Ênfase dentro de título: itálico/negrito da **mesma** família, nunca misturar uma palavra em outra família.
- Escala de título sensata: `clamp()` (ex.: `clamp(2.25rem, 5vw, 4rem)`), corpo 16–18 px, linha de leitura ≤ 65 caracteres, `line-height` 1.5–1.7 no corpo.
- Itálico grande com descendentes (y g j p q): `line-height` mínimo 1.1 e folga inferior para não cortar.

## 4. Cor
- Uma cor de destaque, travada na página inteira; saturação moderada; neutros derivados do matiz da marca.
- Evite os clichês de IA: gradiente roxo/azul neon, brilho em botões, malha escura genérica. Se a marca é roxa, execute com intenção e paleta consistente.
- Marca premium/artesanal: evite reflexo bege/creme + latão/argila + marrom-café. Só use se a marca definir. Alternativas: preto + tan, verde-floresta + osso, cobalto + neutro único, terracota + ardósia, monocromático + um acento.
- Um tema por página (claro ou escuro). Não alternar entre seções, salvo um único momento deliberado.
- Contraste WCAG AA em todos os textos, botões, placeholders, campos e foco.

## 5. Layout
- Evite hero centralizado com fundo escuro genérico quando variação > 4: prefira divisão 50/50, texto à esquerda + imagem à direita, assimetria com espaço em branco. Centralizado é ok para manifesto/anúncio.
- **Hero cabe na primeira dobra**: título até 2 linhas, subtexto até 20 palavras, CTA visível sem rolar. Padding-top do hero ≤ 6rem. Use `min-height: 100dvh`, não `100vh`.
- **Hero com no máximo 4 elementos de texto**: eyebrow (opcional), título, subtexto, CTAs (1 primário + 1 secundário). Selos de confiança, logos de clientes, preço e listas ficam numa seção logo abaixo.
- Navegação em uma linha no desktop, altura 64–80 px.
- Não repita a mesma família de layout em seções diferentes; com 8 seções, use pelo menos 4 famílias. No máximo 2 seções seguidas com o padrão imagem+texto alternado (zigue-zague).
- **Eyebrow com moderação**: no máximo 1 rótulo pequeno em caixa-alta por 3 seções. Muitas vezes o título sozinho basta.
- Cabeçalho de seção com uma mensagem só: título em cima, texto embaixo (≤ 65ch). Evite o padrão "título grande à esquerda + parágrafo pequeno flutuando à direita" por padrão.
- Grades tipo bento com exatamente o número de células do conteúdo (sem célula vazia) e variação visual real entre as células.
- Use CSS Grid para colunas (não cálculos com flex e porcentagem). Declare o comportamento mobile (< 768 px) de cada seção multi-coluna.
- Cartões só quando a elevação comunica hierarquia; senão separe com linha ou espaço. Uma única escala de raio de borda para a página.
- Listas longas (> 5 itens): use grade de cartões, abas, acordeão ou carrossel em vez de lista com linha em cada item. Tabelas longas de especificação: agrupe em 2–3 blocos.

## 6. Conteúdo e copy
- Por seção: título curto (≤ 8 palavras), subtexto curto (≤ 25 palavras) e um visual ou um CTA. Corte o que não trabalha.
- Um único rótulo por intenção de CTA em toda a página (não misture "Fale conosco", "Entre em contato" e "Vamos conversar"). Rótulo de botão em uma linha, 1–3 palavras.
- Depoimentos: até 3 linhas, com nome + cargo/empresa, aspas tipográficas. Só reais e fornecidos pelo cliente.
- Números só se vierem do cliente ou de fonte verificável. Nada de "98% de satisfação" inventado.
- Sem travessão (—) como recurso de estilo no copy. Sem frases de efeito vazias ("soluções inovadoras", "excelência em tudo"). Releia todo o texto visível antes de entregar: corrija erros, referentes soltos e frases que soam artificiais.
- Um registro de linguagem por página (formal, próximo, técnico), coerente com o público.

## 7. Imagens e ativos
- Prioridade: (1) fotos e logos do cliente, (2) imagem gerada por ferramenta disponível para lacunas, (3) banco de imagens de licença livre com direito de uso claro, (4) slots claramente marcados `<!-- TODO: foto X, proporção Y -->` e listados nas pendências.
- Nada de "screenshots falsos" feitos com divs, nem ilustrações SVG desenhadas à mão como muleta. Hero precisa de visual real; texto + gradiente não é hero.
- Ícones de uma única biblioteca (Phosphor, Tabler, Radix Icons, Hugeicons) com espessura de traço padronizada. Não desenhe ícones à mão. Sem emoji, salvo tom brincalhão pedido.
- Logos de clientes ("quem confia") só reais e autorizados; a faixa de logos mostra logos e nada mais (sem rótulo de categoria embaixo).
- Prefira SVG para logos e ícones; imagens em WebP/AVIF com `width`/`height`, `srcset` quando fizer sentido.

## 8. Estados e formulários
- Botões: estados hover, focus visível, active (leve translação/escala) e disabled. Contraste do texto do botão ≥ 4,5:1; botão fantasma sobre foto precisa de scrim.
- Formulários: label acima do campo, texto de ajuda e erro sob o campo, nunca placeholder como label. Contraste AA em campos, placeholders, foco e erros. Campos de telefone com máscara e validação.

## 9. Movimento
- Só anima `transform` e `opacity`. Todo movimento tem motivo (hierarquia, narrativa, feedback, transição de estado); sem motivo, não faça.
- No máximo 1 marquee por página. Sem `window.addEventListener('scroll')`; use IntersectionObserver, `animation-timeline` em CSS ou biblioteca de scroll.
- Respeite `prefers-reduced-motion` (obrigatório se movimento > 3): loops, parallax e efeitos magnéticos viram estáticos.

## 10. Desempenho e acessibilidade
- Meta: LCP < 2,5 s, INP < 200 ms, CLS < 0,1. Imagem do hero sem `loading="lazy"`, com `fetchpriority="high"`; demais imagens com lazy. Reserve espaço para imagens e fontes (`font-display: swap`, fontes self-hosted).
- HTML semântico (`header nav main section footer`), um H1 claro, hierarquia de headings lógica, links com texto descritivo, `alt` útil (decorativa = `alt=""`), foco visível, navegação por teclado.
- Suporte a modo escuro só se a marca/briefing pedir; senão trave um tema. Se houver os dois, defina tokens por CSS variables e respeite `prefers-color-scheme`.
- Z-index com escala documentada, sem `z-index: 9999` aleatório.
