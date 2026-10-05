# Estrutura do projeto

## Estrutura do starter

```text
started-theme/
├── style.css                 # Metadados obrigatórios (Theme Name, Text Domain, ...)
├── functions.php             # Maestro: setup + require dos inc/
├── index.php                 # Fallback obrigatório do loop
├── header.php                # <head> + wp_head() + skip-link + wp_nav_menu()
├── footer.php                # CTA + rodapé (menus, redes) + faixa da agência + wp_footer()
├── front-page.php            # home: só orquestra template-parts/home/*
├── home.php                  # listagem do blog (destaque + cards)
├── page.php  single.php  archive.php  search.php  404.php
├── template-parts/          # content*, pagination, cookie-banner, home/, blog/, single/
├── screenshot.png            # 1200×900 (4:3) — adicionar por projeto
├── page-templates/
│   └── landing.php           # canvas p/ landing pages com blocos (opcional)
├── blocks/                   # blocos de seção (opcional — ver docs/blocks.md)
│   └── <nome>/               # block.json + render.php + editor.js (sem build)
├── assets/
│   ├── css/theme.css         # base neutra + componentes (custom properties no topo)
│   ├── css/blocks.css        # estilos dos blocos (front + editor)
│   ├── css/blocks-editor.css # ajustes só do canvas do editor
│   ├── img/                  # ilustrações e logos do tema (starter_imagem_tema())
│   └── js/theme.js           # menu mobile + comportamentos globais
├── inc/
│   ├── enqueue.php           # wp_enqueue_* + filemtime()
│   ├── clean-head.php        # limpeza do wp_head (emoji, generator, feeds)
│   ├── customizer.php        # opções globais (logos, redes, contatos, GA)
│   ├── analytics.php         # GA/GTM condicional (produção + não logado)
│   ├── template-tags.php     # starter_logo(), starter_social_icon()
│   ├── pagination.php        # paginação numerada (classes .pagination*)
│   ├── login.php             # white-label da tela de login
│   ├── smtp.php              # MailPit em dev (inerte sem WP_DEBUG)
│   ├── cpt-exemplo.php       # modelo de CPT (duplique por conteúdo)
│   ├── acf-fields-exemplo.php# modelo de grupo ACF local
│   ├── blocks.php            # registro dos blocos de seção (require comentado)
│   └── cli/                  # bootstrap + seed (só no contexto WP-CLI)
├── template-parts/
│   ├── content.php           # card genérico de listagem (lê o loop)
│   ├── content-search.php    # card de resultado de busca (badge de tipo)
│   ├── content-none.php      # "nada encontrado"
│   └── pagination.php        # wrapper de starter_pagination_render()
├── bin/
│   ├── rename.sh             # renomeia os tokens do starter (ver SETUP.md)
│   ├── wp                    # wrapper WP-CLI p/ LocalWP (ver docs/wp-cli.md)
│   └── wp.config.example.sh  # template da config local (não versionada)
├── languages/                # .po/.mo do tema
├── docs/                     # esta documentação
├── SETUP.md                  # ficha do projeto + processo de setup inicial
├── CLAUDE.md  AGENTS.md  README.md  .gitignore
```

## Convenções de nomes

- **Text-domain:** `started-theme` · **Prefixo:** `starter_` (funções/hooks) e
  `starter-` (handles). Renomeie ao iniciar um projeto (`bin/rename.sh` — ver SETUP.md).
- **Templates de CPT:** `single-{cpt}.php`, `archive-{cpt}.php`.
- **Módulos:** um arquivo por responsabilidade em `inc/` — `cpt-{nome}.php`,
  `acf-fields-{contexto}.php`, `{recurso}-filters.php`.
- **Componentes:** `template-parts/{tipo}-{variante}.php` (ex.: `card-projeto.php`).
- **CSS por contexto:** nome espelha o template (ex.: `front-page.css`).

## Como o tema cresce

1. **Nova página mapeada (ACF):** `page-{slug}.php` + `inc/acf-fields-{slug}.php` +
   seções em `template-parts/{slug}-*.php`.
2. **Nova listagem/repetição:** `inc/cpt-{nome}.php` (copie de `cpt-exemplo.php`) +
   `archive-{nome}.php`/`single-{nome}.php` + card em `template-parts/card-{nome}.php`.
3. **Novo dado global:** setting + control em `inc/customizer.php`.
4. **Filtros de archive:** módulo próprio `inc/{nome}-filters.php` com `pre_get_posts`.
5. **Landing page editável pelo cliente:** ative `inc/blocks.php` e crie seções em
   `blocks/` ([blocks.md](blocks.md)).

> Regras e "porquês" das escolhas: ver [`architecture.md`](architecture.md).
