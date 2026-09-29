---
name: checklist-golive-wordpress
description: Executa o checklist padrão da agência para finalizar e publicar um site WordPress (acesso do cliente, segurança, plugins, tema, revisão, pré-go live, SEO, testes, documentação de entrega). Use quando o usuário pedir para "finalizar o site", "rodar o checklist de go live", "checklist WordPress", "preparar o site para entrega" ou algo equivalente. Adapta o checklist ao site (ignora blog, banner, horário etc. se não existirem) e pede ao usuário o que faltar.
---

# Checklist de finalização e Go Live — WordPress

Objetivo: executar (ou verificar) cada item do checklist da agência, adaptando ao que o site realmente tem, e entregar um relatório final claro.

## Regras gerais

- **Itens condicionais**: se o site não tem o recurso (blog, banner, horário de funcionamento, newsletter, integrações, formulário etc.), marque o item como ⏭️ N/A e não gaste tempo com ele. Detecte isso inspecionando o site; se estiver em dúvida, pergunte.
- **Nunca invente dados.** Se faltar informação (URL, credenciais, link do Figma, IDs de integração, número de WhatsApp, e-mail do rodapé), **peça ao usuário** antes de seguir. Agrupe as perguntas em uma única mensagem.
- **Confirmar antes de agir em produção**: criar usuário admin, instalar/ativar plugins, alterar URL de login e enviar formulários de teste modificam o site. Mostre o plano e peça confirmação uma vez antes de executar em bloco.
- **Testes em guia anônima** (sem extensões nem cache do navegador). Lembre o usuário disso quando algo depender dele.
- O item de **URL de login** muda o acesso ao painel: avise o usuário antes e informe o novo endereço depois.
- Tudo o que não puder ser feito por você (Safari, dispositivos reais, gravar Loom, Discord/WhatsApp para testar preview) vai para a lista "Precisa de você" no final.

## Passo 0 — Levantamento (sempre primeiro)

Pergunte/descubra, de uma vez só:

1. Nome do cliente e URL do site (ambiente `/teste` e/ou produção) e como acessar (wp-admin, WP-CLI, pasta local do tema em `wp-content/themes/`).
2. Link do Figma (para comparar layout) e conteúdos enviados pelo cliente.
3. O site tem: blog? banner? horário de funcionamento? formulários? newsletter? integrações (RD Station, GA, GTM, Pixel)? WhatsApp flutuante (qual número)?
4. E-mail de contato do rodapé (será usado no acesso do cliente).
5. IDs/links das integrações a instalar.

Depois monte a lista de itens aplicáveis e mostre ao usuário em resumo antes de começar.

## Fase 1 — Acesso do cliente

- Criar usuário com papel **Administrador**:
  - Usuário: `@nomedocliente`
  - Senha: gerar aleatória forte (mínimo 20 caracteres). Mostre uma única vez no relatório final; não grave em arquivo nem em memória.
  - E-mail: o e-mail de contato do rodapé do site.

## Fase 2 — Padrões de segurança

- Captcha no campo de login (Advanced Google reCAPTCHA; peça as chaves site/secret se não houver).
- URL de login alterada para `/wp-painel` (padrão igual para todos os clientes) com o plugin **WPS Hide Login**.
- Plugin **All In One WP Security & Firewall** instalado e configurado.
- Prefixo de tabelas `r8wp_`: só é definido na instalação do WordPress. Verifique em `wp-config.php` (`$table_prefix`). Se estiver diferente, **avise o usuário** (não altere em site já populado sem autorização).

## Fase 3 — Plugins

Instalar e ativar (os que já estiverem instalados, apenas conferir):

Autoptimize · Performance Lab (otimiza imagens) · Wordfence Security · Simple History · Login Lockdown · Inactive Logout · WPS Hide Login · Disable WP REST API · Advanced Google reCAPTCHA · Yoast SEO & Yoast Duplicate Post · ACF **ou** CMB2 (usar o que o tema já usa) · Contact Form 7 & WP Mail SMTP · Admin Menu Editor · Loco Translate.

Cuidados (avise o usuário, não decida sozinho):
- **Disable WP REST API** pode quebrar o Contact Form 7 e integrações: teste o formulário depois e libere as rotas necessárias se falhar.
- Wordfence, All In One Security e Login Lockdown têm funções sobrepostas: siga a lista, mas confira conflitos (bloqueio indevido de login, avisos).
- Configurar o WP Mail SMTP (peça credenciais SMTP se necessário).

## Fase 4 — Tema

- Atualizar informações no `style.css` (nome do tema, autor, URI, descrição, versão, text domain).
- Adicionar a capa do tema (`screenshot.png`, 1200x900).

## Fase 5 — Revisão interna

Comparar o site com o Figma e conferir funcionalidades padrão do WordPress:
- Revisão de layout (fidelidade ao Figma, desktop e mobile).
- Páginas de **categoria**, **resultado de busca** e **404** (categoria só se houver blog/categorias).
- Capa do tema e favicon.
- Rodapé padrão da agência.

Se não tiver acesso ao Figma, peça o link ou imagens.

## Fase 6 — Pré-Go Live (fazer em `/teste` antes de liberar)

### Geral
- Favicon.
- Endereço no padrão: `Logradouro, número - Bairro, Cidade - Estado, CEP`.
- Tradução do tema (Loco Translate; sem strings em inglês soltas).
- Horário de funcionamento correto (se houver).
- Banner funcionando (se houver).
- Link da agência no rodapé abre em **outra aba** (`target="_blank"` com `rel="noopener"`).
- Menu do painel WP organizado, com conteúdos fáceis de achar e editar (Admin Menu Editor).
- Todos os links de redes sociais funcionando.
- Todos os conteúdos enviados pelo cliente cadastrados (peça a lista/arquivos se não tiver).
- Testar todos os links e botões; sem links quebrados.
- WhatsApp flutuante adicionado (peça o número e a mensagem padrão).

### Integrações (se houver)
- Adicionar e testar RD Station, Google Analytics, GTM, Pixel etc.
- Integração de newsletter adicionada e testada.

### Formulário (se houver)
- Máscara para telefone e CPF.
- Campos de telefone, CPF, data etc. não aceitam letras e validam número de dígitos.
- Campos padrão: nome, e-mail, telefone (obrigatório) e mensagem.
- Captcha no formulário.
- Testar o disparo e o recebimento do e-mail (confirme com o usuário qual caixa conferir).

### Blog (**somente se o site tiver blog**, senão N/A)
- Campo de comentários.
- Botões de compartilhamento: Facebook, WhatsApp, LinkedIn, Twitter e e-mail.
- Página de busca por postagens e categorias.
- Página de categorias.
- Campo de busca: testar vazio, com palavra existente no site e com caracteres especiais (ex.: `239jewjdksd8@@0)%%%$)`).

### SEO
- Yoast configurado; testar preview compartilhando o link no Discord ou WhatsApp (pedir ao usuário).
- Description.
- Autor do site.
- Title padrão dinâmico.
- Slugs limpos.
- Imagem de compartilhamento (capa social) do site.

### Testes de usabilidade
- Navegadores: Chrome, Firefox, Edge, Opera e Safari (se possível).
- Dispositivos reais iPhone e Android (se possível).
- O que não puder ser testado por você vai para "Precisa de você".

### Testes de desempenho
- Simular navegação lenta pelo DevTools.
- Medir performance em web.dev/measure, PageSpeed Insights e GTmetrix. Registre as notas e os principais problemas.
- Responsividade: Google Mobile-Friendly Test e Responsinator.

### Código
- Remover hardcodes (`website.local`, URLs locais, imagens fixas no código): busque no tema com grep.
- Comprimir imagens que não são gerenciáveis pelo WP ou que são usadas via CSS.

## Fase 7 — Entrega e Go Live

Criar a documentação de gerenciamento do site para o cliente (linguagem simples, com passo a passo), contendo:
- Como fazer login no site (URL `/wp-painel`).
- Como alterar os textos do site.
- Como criar um novo acesso de usuário.
- Como alterar o e-mail de recebimento de formulário (omitir se não houver formulário).
- Como otimizar imagens antes de subir no site.

Depois, avise o usuário que falta **gravar o Loom de treinamento** baseado na documentação (você pode gerar um roteiro).

## Relatório final

Entregue ao usuário, no final:

1. **Resumo por fase** com status por item: ✅ feito e verificado · ⚠️ feito com ressalva · ❌ falhou · ⏭️ N/A (com motivo) · 🙋 precisa do usuário.
2. **Precisa de você**: lista objetiva do que depende do usuário (testes em Safari/dispositivos, preview em Discord/WhatsApp, Loom, chaves e IDs pendentes).
3. **Credenciais do cliente** (usuário, senha e URL de login), mostradas uma única vez.
4. **Riscos e observações** (conflitos de plugin, notas baixas de performance, prefixo de tabelas etc.).

Nunca marque ✅ um item que você não verificou de fato.
