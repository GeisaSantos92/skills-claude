# Descoberta de ativos numa pasta

## Classificação (heurística; confirme olhando o arquivo)
| Categoria | Pistas |
|---|---|
| Logo | nome com `logo`, `marca`, `brand`, `logotipo`; SVG/PNG com fundo transparente; pasta `logo/` |
| Ícone/Favicon | `favicon`, `icon`, `ico`, `.ico`, PNG quadrado ≤ 512 px |
| Foto | JPG/WEBP grandes (≥ 1000 px), pastas `fotos`, `imagens`, `banner` |
| Ilustração/arte | PNG/SVG sem aspecto fotográfico, mockups, padrões |
| Fonte | `.ttf .otf .woff .woff2`; pasta `fonts/fontes` |
| Texto/copy | `.docx .txt .md .pdf` com nomes como `textos`, `copy`, `conteudo`, `briefing` |
| Manual de marca | PDF com `manual`, `brandbook`, `identidade` |
| Referência de layout | prints, `.fig`, links do Figma, PDFs de proposta, `ref`/`referencia` |
| Vídeo | `.mp4 .mov .webm` |
| Outros | planilhas com dados (`.xlsx/.csv`), zips (avise, não extraia sem necessidade) |

## Logos
- Prefira SVG. Sem SVG, PNG com transparência e largura ≥ 600 px.
- Procure variações: colorida, branca (para fundo escuro), só ícone, horizontal/vertical. Se só existe uma versão, avalie o contraste sobre os fundos usados e registre a limitação; **não redesenhe o logo**.
- Logo em JPG com fundo branco: só use sobre fundo claro; sinalize pedido de versão transparente.

## Favicon
- Se existir: usar. Se não: derivar do ícone/inicial do logo (quadrado, legível em 16–32 px) e listar como pendência. Entregar `favicon.ico`/`favicon.svg` + `apple-touch-icon` 180 px quando possível.

## Paleta
1. Manual de marca ou textos com hex/RGB/CMYK → fonte de verdade (converter CMYK só se necessário e avisar).
2. Sem manual: extrair as cores do logo (o script lista os hex de SVGs; para PNG/JPG, olhe a imagem com Read e estime) e escolher primária/secundária/destaque; confirme olhando o logo. Cores extraídas de foto não são paleta de marca.
3. Registre para cada cor: hex, papel, e contraste de texto (WCAG AA: 4,5:1 texto normal, 3:1 texto grande/UI).
4. Complete com neutros derivados do matiz da marca. Uma cor de destaque só.

## Fontes
- Arquivos de fonte na pasta → use via `@font-face` (woff2 preferível; converter/avisar se só houver ttf/otf).
- Só o nome da fonte, sem arquivo → verifique se existe no Google Fonts/Fontshare e se a licença permite web; senão proponha substituta próxima e registre.
- Sem fonte definida → proponha par (títulos + texto) coerente com a leitura de design. Registre como suposição.

## Textos
- `.docx`: o script extrai o texto (via `tar`); `.pdf`: use Read com `pages`; imagens de texto: leia visualmente e transcreva com cuidado (avise a origem).
- Mapeie cada trecho para uma seção. Marque conteúdo repetido, contraditório ou datado.
- Extraia fatos (nome, serviços, diferenciais, contatos) para o briefing; separe do que é opinião/adjetivo.
- Erros de português: corrija ortografia óbvia e sinalize alterações relevantes de sentido.

## Imagens
- Avalie: resolução, proporção, nitidez, se é foto real do negócio (preferível) ou banco genérico, direitos de uso (marca d'água = não usar), consistência de estilo entre elas.
- Defina o uso de cada uma (hero, seção, galeria, fundo) e recortes/proporções. Liste as imagens que faltam com proporção e assunto.
- Otimize ao copiar para `assets/`: redimensione para o maior tamanho exibido e converta para WebP/AVIF quando possível, mantendo o original intacto.

## Referências de layout
Se houver prints/PDF/Figma: extraia estrutura (seções, hierarquia, tom) e o que o cliente gostou. Inspiração ≠ cópia; nunca reproduzir layout de terceiros.

## Relatório do inventário
Ao final, gere a tabela: arquivo → categoria → uso previsto → observação (qualidade, problema). E a lista do que **não foi encontrado** (logo, favicon, paleta, fonte, textos, fotos).
