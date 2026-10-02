# Acabamento visual

Complementa `design-rules.md`. Aqui ficam as decisões de sistema e os detalhes que separam layout comum de layout bem feito.

## 1. Escala tipográfica
- Escolha uma proporção e derive todos os tamanhos: 1.2 (densa, confiança), 1.25 (padrão), 1.333 (editorial), 1.5+ (display dramático, premium/criativo).
- Tokens: `--step--1` a `--step-5` com `clamp()` fluido entre 375 e 1440 px. Corpo `--step-0` = 1rem–1.125rem.
- Títulos grandes: `letter-spacing` negativo (−0.01 a −0.03em) e `line-height` 1.0–1.15. Texto pequeno em caixa alta: +0.06 a +0.1em.
- `text-wrap: balance` em títulos, `text-wrap: pretty` em parágrafos. Sem viúva no título do hero.
- No máximo 2 famílias e 3 pesos visíveis por página. Contraste de peso (400 × 700/800) vale mais que contraste de tamanho.
- Números em dados: `font-variant-numeric: tabular-nums`.

## 2. Cor em proporção
- 60% superfície base, 30% superfícies secundárias/texto, 10% cor da marca/acento. O acento aparece em CTAs, links e na assinatura, não em decoração aleatória.
- Neutros tingidos com o matiz da marca (não `#000`/`#fff` puros, salvo direção que peça). Texto principal ≈ 90% de contraste, secundário ≈ 65–70% (sem cair abaixo de AA).
- Sombra tingida com o matiz do fundo, baixa opacidade, 2 camadas (próxima e difusa).
- Defina estados: `--color-accent-hover`, `--color-accent-active`, `--color-focus`.

## 3. Espaço e grade
- Escala 8 px: 4, 8, 12, 16, 24, 32, 48, 64, 96, 128. Espaço vertical de seção: 96–160 px no desktop, 64–96 px no mobile.
- Proximidade: o espaço entre título e seu texto é menor que o espaço entre blocos; entre blocos, menor que entre seções. Esse é o teste de agrupamento mais importante.
- Grade de 12 colunas, gutter 24–32 px, margem lateral ≥ 20 px no mobile. Alinhe bordas de texto e imagem às mesmas linhas da grade em seções diferentes.
- Assimetria intencional: 7/5, 8/4 ou deslocar o conteúdo uma coluna. Evite 6/6 em todas as seções.

## 4. Imagem
- Defina 2–3 proporções para a página (ex.: 4:5 retrato, 3:2 paisagem, 1:1) e use só elas.
- `object-fit: cover` com `object-position` escolhido por foto (rostos e assunto não cortados).
- Tratamento único: mesma temperatura/contraste; se as fotos do cliente forem heterogêneas, unifique com leve filtro ou duotone da marca na assinatura.
- Texto sobre foto: scrim em gradiente localizado (não véu escuro na foto inteira), contraste AA garantido.
- Sangria (foto encostando na borda da tela) é uma ferramenta forte: use 1–2 vezes.

## 5. Detalhes que contam
- Alinhamento ótico: ícones e títulos grandes às vezes precisam de 1–2 px de ajuste; aspas penduradas fora da margem em citações.
- Botões: altura 44–52 px, padding horizontal ≈ 2× vertical, raio coerente com a escala; ícone de seta só no CTA principal, se fizer.
- Links no texto: sublinhado com `text-underline-offset: .2em` e espessura fina; hover muda cor ou espessura.
- Fios (1 px, cor de borda suave) separam melhor que cartões em listas e tabelas.
- Ícones: mesmo tamanho ótico, mesmo traço, alinhados à linha de base do texto.
- Microinteração: hover em cartões com transição de 150–250 ms em `transform`; revelação por scroll sutil (8–16 px + opacidade), com escalonamento ≤ 80 ms.
- Header: transparente sobre hero escuro só se houver versão clara do logo; ao rolar, fundo sólido com fio inferior.
- Rodapé é parte do design: use a assinatura ou o wordmark em escala, não uma faixa cinza vazia.

## 6. Mobile não é desktop empilhado
- Reordene: no hero, título → subtexto → CTA → imagem (ou imagem curta acima, se ela é o produto).
- Reduza a densidade: grades 3×N viram 1 coluna ou carrossel com snap; bento vira pilha com a célula grande primeiro.
- Títulos de display reduzem a escala; a assinatura sobrevive em versão simplificada.
- Alvos de toque ≥ 44 px; CTA principal fixo no rodapé só em landing de conversão.
