# Direção de arte

## 1. Do material ao conceito
1. **Leitura**: "<tipo de página> para <público>, linguagem <vibe>, apoiado em <ativos da marca>."
2. **Três adjetivos** que a marca quer transmitir (ex.: preciso, caloroso, local). Tire do texto do cliente, do logo e das fotos, não do segmento genérico. Cada decisão visual deve servir a pelo menos um deles.
3. **Conceito**: uma frase que organiza a página. Bom conceito gera decisões concretas.
   - Fraco: "moderno e clean". Não decide nada.
   - Bom: "planta técnica" (escritório de engenharia): grade aparente, linhas finas, cotas e numeração, foto de obra recortada em retângulos exatos.
   - Bom: "mesa de padaria às 6h" (padaria artesanal): luz quente lateral, fotos grandes em close, tipografia robusta, fundo farinha com um acento crosta.
   - Bom: "prontuário claro" (clínica): muita respiração, informação em blocos rotulados, azul da marca só em ações, fotos humanas e reais.
4. **Elemento-assinatura**: um gesto visual que aparece no hero e retorna 2–3 vezes (não em toda seção). Exemplos: recorte de imagem com canto específico ou máscara do símbolo do logo; número/índice grande por seção; linha contínua que costura as seções; título em tamanho extremo cortado pela borda; faixa de cor inclinada; foto em duotone da cor da marca; moldura/grade aparente.
5. **Teste de troca**: se trocar o logo por outro do mesmo segmento e a página continuar servindo, a direção está genérica. Volte ao conceito.

## 2. Catálogo de direções
Ponto de partida, não fantasia. Ajuste à marca. Combine no máximo duas.

| Direção | Quando | Tipografia | Cor | Composição | Assinaturas típicas |
|---|---|---|---|---|---|
| **Editorial** | consultoria, arquitetura, marcas com história, portfólio | display de alto contraste ou grotesca grande + texto confortável | neutro dominante + 1 acento | colunas assimétricas, imagens grandes com legenda, muito branco | numeração de seção, fio fino, legenda pequena |
| **Suíço / grade** | engenharia, tecnologia B2B, indústria, escritórios técnicos | grotesca (Manrope, Geist, Satoshi), pesos contrastantes | monocromático + 1 cor sinal | grade de 12 colunas aparente, alinhamento rígido, blocos | grade/linhas visíveis, rótulos em monospace, cotas |
| **Calor local** | restaurante, padaria, serviço de bairro, pet, beleza | sans arredondada ou humanista com peso | cores quentes da marca, fundo levemente tingido | fotos dominantes, blocos generosos, cartões macios | foto recortada com raio grande, carimbo/selo, fundo tingido |
| **Confiança clara** | saúde, jurídico, contabilidade, financeiro, educação | sans neutra e legível, títulos sóbrios | azul/verde da marca em ações, neutros frios | simétrico com variação leve, informação em blocos rotulados | ícones consistentes, faixas de prova, ilustração mínima |
| **Premium minimal** | imóveis alto padrão, estética, moda, joalheria | display fina e espaçada ou serifa com motivo + sans | quase monocromática, contraste alto | pouco conteúdo por tela, imagem em sangria, alinhamentos raros | tipografia grande e espaçada, sangria total, transições lentas |
| **Vibrante / pop** | eventos, varejo jovem, apps de consumo, infantil | display gorda, títulos grandes | 2 cores saturadas da marca + neutro | blocos de cor cheia, sobreposição, rotação leve | blocos coloridos, adesivos, formas do logo como padrão |
| **Técnico escuro** | SaaS, dev tools, segurança, games | grotesca + mono para dados | fundo escuro da marca, 1 acento luminoso | painéis, dados reais, detalhes de interface | código/terminal real, linhas de grade, brilho contido |
| **Orgânico / natural** | agro, sustentabilidade, bem-estar, alimentos naturais | humanista ou serifa suave | verdes/terra da marca com 1 acento | formas e recortes suaves, texturas reais | máscaras orgânicas, textura fotográfica real |

## 3. Dials por direção
Use a tabela de `design-rules.md` §2 e ajuste: Editorial e Premium sobem variação; Confiança baixa variação e movimento; Vibrante sobe movimento; Técnico sobe densidade.

## 4. Quando apresentar opções
Apresente 2–3 direções se o usuário pedir, se a marca for nova/sem manual ou se o material permitir leituras muito diferentes. Cada opção: nome, conceito, 3 adjetivos, paleta (hex), par de fontes, assinatura, risco. As direções devem ser **realmente diferentes** (não três variações de cor). Gere `direcoes.html` com, para cada uma, a faixa de tokens e um hero de verdade com o conteúdo do cliente.

## 5. Sem ativos
- Sem logo: wordmark simples com a fonte de display (não um símbolo inventado) e pendência de logo.
- Sem cores: derive da direção e do segmento evitando clichês (`design-rules.md` §4); registre como proposta.
- Sem fotos: priorize composição tipográfica e cor; slots marcados com proporção e assunto; nunca texto + gradiente como único visual do hero; se houver ferramenta de geração de imagem, use para lacunas com estilo coerente entre as imagens.
