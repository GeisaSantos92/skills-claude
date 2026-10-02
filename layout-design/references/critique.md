# Autocrítica com screenshot

Leia cada fatia de `_shots/` com Read. Anote problemas como `[alta|média|baixa] seção: problema → correção`. Corrija alta e média antes de entregar; baixa entra na entrega como sugestão.

## Primeira impressão (hero, fatia 01)
- Em 3 segundos dá para dizer o que é, para quem é e o que fazer? (alta)
- O conceito e a assinatura aparecem no hero? Passa no teste de troca do logo? (alta)
- O hero cabe na dobra (1440×900 e 375×812) com CTA visível? (alta)
- Há um ponto de entrada visual claro, ou título, imagem e botão disputam atenção? (média)

## Teste de apertar os olhos
Imagine a fatia desfocada:
- Existe hierarquia de 3 níveis (dominante, secundário, detalhe) por seção? (alta)
- Os CTAs são as manchas de acento mais fortes da página? (média)
- As massas estão equilibradas, ou há um lado pesado e um buraco sem intenção? (média)

## Ritmo da página inteira
- As seções variam de família, densidade e fundo? Algum trecho parece "mais do mesmo"? (média)
- Espaços entre seções consistentes; nenhum vão gigante nem seção espremida. (média)
- Alinhamentos verticais se repetem entre seções (mesmas linhas de grade)? (baixa)

## Acabamento
- Títulos sem viúvas, quebras feias ou corte de descendentes; botões em uma linha. (média)
- Imagens sem distorção, rostos não cortados, proporções do sistema. (alta se distorcida)
- Ícones do mesmo estilo e tamanho; fios e raios consistentes. (baixa)
- Texto sobre foto legível. Contraste aparente baixo em algum lugar? (alta)
- Placeholders `[PREENCHER]` visíveis mas sem quebrar o layout. (baixa)
- Elementos invisíveis no screenshot (reveal sem fallback para movimento reduzido) = bug. (alta)

## Mobile (375)
- Aviso de rolagem horizontal do script = bug. (alta)
- Nada encostado na borda sem intenção; margens ≥ 20 px. (média)
- Ordem de leitura faz sentido; grades não viraram colunas espremidas. (alta)
- Menu acessível e header sem ocupar mais de ~72 px. (média)

## Genérico de IA (tudo média ou alta)
Gradiente roxo/azul, hero centralizado escuro com brilho, 3 cartões idênticos com ícone em círculo repetidos várias vezes, eyebrow em toda seção, números redondos sem fonte, "soluções inovadoras", fundo com blobs desfocados, ilustração SVG improvisada. Se encontrar, volte ao conceito e substitua pelo elemento-assinatura ou por conteúdo real.

## Critério de parada
Pare quando uma rodada inteira não encontrar problema alto ou médio, com no mínimo 2 rodadas. Na entrega, cite o que mudou entre as rodadas em 2–4 linhas.
