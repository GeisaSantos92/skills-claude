# Dados estruturados (Schema.org / JSON-LD)

## Regra fundamental
Só marque o que está **visível e verificável na página**. Nunca invente preço, avaliação, review, horário, endereço, telefone, disponibilidade, autor, data ou dados da empresa. Faltou dado → omita a propriedade (ou pergunte). Schema não substitui conteúdo.

## Boas práticas
- Formato preferido: JSON-LD em `<script type="application/ld+json">`.
- Use `@id` estáveis e relacione entidades (Organization ↔ WebSite ↔ WebPage ↔ BreadcrumbList) em vez de blocos soltos duplicados.
- Prefira o tipo mais específico que corresponda à realidade (ex.: `Dentist`, `LegalService`) em vez de `LocalBusiness` genérico.
- Consistência com a página: NAP, nome, logo e URLs iguais ao conteúdo visível e ao Google Business Profile.
- Valide: Rich Results Test (elegibilidade) e validator.schema.org (sintaxe/vocabulário). Válido ≠ elegível a rich result ≠ melhora de ranking.

## Tipos comuns e quando usar
- **Organization / LocalBusiness**: home ou institucional/contato (sem repetir com dados divergentes em várias páginas). Campos: `name`, `url`, `logo`, `sameAs` (perfis reais), `address`, `telephone`, `openingHoursSpecification`, `geo`, `areaServed`, `priceRange` (só se real).
- **WebSite**: home. `SearchAction` só se a busca do site existe e funciona.
- **WebPage / AboutPage / ContactPage**: opcional; útil para ligar entidades.
- **BreadcrumbList**: com breadcrumb visível e coerente com a hierarquia.
- **Article / BlogPosting**: posts com `headline`, `datePublished`, `dateModified` reais, `author` real, `image`.
- **Product / Offer**: só com produto vendável na página; preço, moeda e disponibilidade reais e atualizados.
- **Service**: página de serviço com `provider` e `areaServed` reais. Serve à clareza semântica, sem rich result específico.
- **Event, JobPosting, VideoObject, ImageObject, Person**: apenas com conteúdo correspondente e requisitos do Google atendidos.
- **Review / AggregateRating**: somente avaliações reais, visíveis e sobre o item. Nunca marcar auto-avaliações do próprio negócio para ganhar estrelas; confira a política atual do Google.
- **FAQPage**: só se as perguntas e respostas estão visíveis. O Google restringiu os rich results de FAQ e descontinuou HowTo; confirme na documentação atual antes de prometer resultado.

## Detectar problemas
- Schema duplicado (tema + plugin SEO + plugin específico gerando Organization/Breadcrumb duas vezes).
- Dados conflitantes (telefone diferente do visível, preço divergente, datas incoerentes).
- Marcação de conteúdo ausente na página, reviews sem origem, `AggregateRating` sem avaliações reais.
- JSON inválido, `@type` inexistente, URLs relativas onde se espera absoluta, imagens inacessíveis.
- Em WordPress: veja o que Yoast/Rank Math já emitem (grafo próprio) e estenda pelos filtros do plugin em vez de injetar bloco paralelo (ver `wordpress.md`).

## Modelo mínimo (LocalBusiness — preencher só com dados reais)

```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://exemplo.com.br/#organization",
  "name": "<nome real>",
  "url": "https://exemplo.com.br/",
  "telephone": "<telefone visível na página>",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "<logradouro, número>",
    "addressLocality": "<cidade>",
    "addressRegion": "<UF>",
    "postalCode": "<CEP>",
    "addressCountry": "BR"
  }
}
```
