---
name: criar-tema-wordpress
description: Cria um tema clássico de WordPress novo a partir do tema-base da Agência R8 (extraído do projeto CLI Connect, sem nada da marca). Copia `tema-base/` para `wp-content/themes/{slug}`, renomeia prefixo/text-domain com `bin/rename.sh`, aplica identidade (cores, fontes, logos), configura WP-CLI, ativa e roda o seed. O tema já vem com header com mega menu, rodapé em colunas, Customizer, SEO/JSON-LD, Polylang, blog, cookies, WhatsApp, login white-label, SMTP→MailPit, ACF via código e seed idempotente. Use quando pedirem "criar um tema novo", "começar um site novo", "novo projeto WordPress", "tema do zero", "usar o tema base/starter da R8" ou "scaffold de tema".
---

# Criar tema WordPress a partir do tema-base R8

O tema-base fica em `~/.claude/skills/criar-tema-wordpress/tema-base/`. Ele usa
tokens neutros que o `bin/rename.sh` troca pelos do projeto:

| Token | Vira | Onde |
| --- | --- | --- |
| `started-theme` | `{slug}` | text-domain, pasta |
| `Started_Theme` / `Started Theme` | `{Prefixo}` / `{Nome}` | `@package`, Theme Name |
| `STARTER_` / `Starter_` | `{PREFIXO}_` / `{Prefixo}_` | constantes / classes |
| `starter_` / `starter-` / `starter/` | `{prefixo}_` / `-` / `/` | funções, handles, blocos |
| `starter seed` | `{prefixo} seed` | comando WP-CLI |

**Nunca edite o `tema-base/` para um projeto específico** — ele é o molde. Mudança
que vale para todos os projetos futuros (bug no walker, melhoria no seed) vai no
`tema-base/` e é avisada ao usuário.

## Fluxo

### 1. Ficha mínima

Pegue do pedido (pergunte só o que faltar e não tiver padrão razoável):

- **Nome** do site/tema, **slug** (minúsculas-hífens) e **prefixo** PHP (minúsculas_underscores, curto — ex.: `acme`).
- **Pasta de destino**: `.../wp-content/themes/` do site LocalWP (descubra pelo diretório atual ou pergunte).
- Cores primárias, fontes, logos — se houver Figma/briefing, extraia de lá.
- Multi-idioma? Blog? CPTs previstos?

O restante da ficha (redes, GA, SMTP…) está em `tema-base/SETUP.md` — use-a como checklist, sem interrogar o usuário item a item.

### 2. Copiar e renomear

```bash
cp -R ~/.claude/skills/criar-tema-wordpress/tema-base "{themes}/{slug}"
cd "{themes}/{slug}"
bash bin/rename.sh --slug={slug} --prefix={prefixo} --name="{Nome}"
grep -rn "starter_\|starter-\|STARTER_\|Starter_\|Started\|started-theme" . | grep -v bin/rename.sh
```

O grep final tem que voltar vazio (exceto se o slug contiver "starter").
Depois apague `bin/rename.sh` se o usuário quiser.

### 3. Identidade

- `style.css`: Description, Author, Theme URI.
- Tokens no topo de `assets/css/theme.css` (`--cor-*`, `--fonte-*`, `--tam-*`) e
  `--login-*` em `inc/login.php`.
- Fontes: baixe os `.woff2` para `assets/fonts/`, declare em `assets/css/fonts.css`,
  ajuste o preload em `inc/enqueue.php` (`{prefixo}_preload_fonts`). Nunca Google Fonts CDN.
- Logos: `assets/img/logo-header.svg` e `logo-footer.svg` (dimensões em
  `{prefixo}_logos_tema()` em `inc/template-tags.php`).
- Redes sociais: deixe em `{prefixo}_social_networks()` (`inc/customizer.php`) só as do cliente.
- `screenshot.png` 1200×900 se houver arte.

### 4. Ambiente

```bash
cp bin/wp.config.example.sh bin/wp.config.sh   # ajuste PHP_BIN, DB_SOCKET, WP_PATH (no Windows, copie de outro projeto R8)
./bin/wp theme activate {slug}
./bin/wp plugin install advanced-custom-fields --activate
./bin/wp plugin install polylang --activate      # só se multi-idioma
git init && git add -A && git commit -m "chore: scaffold do tema a partir do starter R8"
```

Ativar tema e instalar plugins altera o site: confirme com o usuário antes se o site
já tiver outro tema em uso.

### 5. Conteúdo e seed

- Edite `inc/cli/seed.php`: páginas em `criar_paginas()`, campos da home em
  `preencher_home()`, menus em `criar_menus()`, theme mods em `configurar_customizer()`.
- Imagens do seed em `assets/seed/` (nome base = chave do `img()`).
- `./bin/wp {prefixo} seed` — idempotente; `--reset` apaga o que o seed criou.

### 6. Verificação

- Abra home, uma página, o blog, um post, a busca e um 404 — sem erro e sem warning
  no `wp-content/debug.log`.
- Menu mobile abre/fecha (Esc fecha), dropdowns por teclado.
- Logos no header, footer e login.
- Lint: `"$PHP_BIN" -l` em todos os `.php`.

### 7. Entrega ao usuário

Diga a pasta criada, o prefixo/text-domain, o que foi ativado/instalado, o que ficou
pendente da ficha e o próximo passo natural (montar as seções da home a partir do
Figma). Atualize a seção "Estado atual" do `CLAUDE.md` do tema novo.

## Como o tema cresce (lembrete para as próximas tarefas)

- **Seção da home:** `template-parts/home/{secao}.php` + aba em `inc/acf-fields-home.php`
  + bloco em `assets/css/front-page.css` + entrada em `$…_secoes` no `front-page.php`.
- **Página com layout próprio:** `page-{slug}.php` + `inc/acf-fields-{slug}.php` +
  `assets/css/page-{slug}.css` enfileirado com `{prefixo}_e_pagina( '{slug}' )`.
- **Lista que cresce:** CPT (o ACF Free não tem Repeater) — duplique
  `inc/cpt-exemplo.php` + `inc/acf-fields-exemplo.php`, registre em `functions.php`,
  `./bin/wp rewrite flush`, apague o exemplo.
- **CPT público com SEO:** adicione em `{prefixo}_schema_tipos_cpt()` (`inc/seo.php`)
  e, se multi-idioma, o título do archive em `inc/polylang.php`.
- Regras de código: `CLAUDE.md` e `docs/` do próprio tema.
