# CLAUDE.md

Tema **clássico** do WordPress do site **Started Theme** (Agência R8).
Prioridades: **performance**, **segurança** e **baixa dependência** de plugins/build.

> O tema nasceu do starter em branco da R8; o processo de setup está em `SETUP.md`.
> Plugins previstos: **ACF Free** e, se o site for multi-idioma, **Polylang**.

## Regras que nunca mudam

- **Classic Theme, PHP hierárquico.** Nada de FSE/blocos estruturais. Exceção
  controlada: blocos de **conteúdo** para landing pages (opcional, `inc/blocks.php` —
  server-rendered, sem build; ver `docs/blocks.md`).
- **Sem build local.** Assets (`assets/css/`, `assets/js/`) são estáticos e entram via
  `wp_enqueue_*` — nunca `<link>`/`<script>` no HTML. CSS/JS por contexto
  (`front-page.css` só na home, `page-{slug}.css` só na página).
- **Escape sempre na saída:** `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- **Coalescência nula** em todo dado do ACF/Customizer: `get_field('x') ?? ''`.
- **Zero texto fixo** nos templates: conteúdo vem do ACF, dos CPTs ou do Customizer;
  strings de interface passam por gettext.
- `functions.php` **é só maestro:** apenas `starter_require()` dos arquivos de `inc/`.
- **ACF Free via código** (`acf_add_local_field_group`), nunca pelo painel. Repetições →
  CPT (o ACF Free não tem Repeater). Dados globais → **Customizer**, não ACF.
- **APIs do WordPress** no lugar de PHP cru (`wp_remote_get`, Transients, etc.).
- **Prefixo `starter_` / text-domain `started-theme`** em todo código novo.
- **Fontes auto-hospedadas** em `assets/fonts/` — nunca CDN do Google Fonts.
- **Menus com Polylang:** ao criar/atribuir menus por código, registre também em
  `polylang['nav_menus'][tema][location][idioma]`, senão o front renderiza vazio
  (o seed já faz isso em `sincronizar_polylang()`).

## Onde aprofundar (leia sob demanda)

- Setup inicial de um projeto novo (ficha + processo) → `SETUP.md`
- Decisões de arquitetura e o porquê → `docs/architecture.md`
- Como escrever código (PHP/HTML/CSS/segurança) → `docs/code-standards.md`
- Boas práticas de longevidade e plugins → `docs/best-practices.md`
- Estrutura de arquivos e como o tema cresce → `docs/project-structure.md`
- Landing pages com blocos (padrão sem build) → `docs/blocks.md`
- WP-CLI (`bin/wp`) e receitas → `docs/wp-cli.md`
- Ambiente LocalWP, debug, MailPit → `docs/local-env.md`

## Estado atual

**Starter recém-copiado.** Atualize esta seção conforme o projeto avança.

- **Home:** `front-page.php` orquestra os template-parts de `template-parts/home/`
  (por enquanto só `hero.php`, de exemplo). Campos no grupo ACF da página inicial
  (`inc/acf-fields-home.php`, uma aba por seção).
- **CPTs:** `inc/cpt-exemplo.php` + `inc/acf-fields-exemplo.php` são o molde —
  duplique por conteúdo real e apague o exemplo.
- **Chrome:** header com painéis de menu montados pela própria árvore do menu
  (`Starter_Walker_Nav_Menu`: filhos sem netos → cartões, filhos com netos →
  mega menu em colunas), seletor de idioma do Polylang e dois botões do
  Customizer; rodapé em colunas pelo menu `rodape`, links legais (`rodape_legal`),
  CTA, faixa da agência (`template-parts/rodape-agencia.php`), voltar ao topo,
  WhatsApp flutuante e banner de cookies.
- **Design system:** tokens em `assets/css/theme.css` (troque pelos do Figma do
  projeto); seções da home em `assets/css/front-page.css`.
- **Seed:** `./bin/wp starter seed [--reset]` (`inc/cli/seed.php`) importa os
  arquivos de `assets/seed/`, cria páginas, CPTs, menus e theme mods.
  É idempotente (meta `_starter_seed`).
- **SEO:** `inc/seo.php` gera meta/OG/JSON-LD só quando não há Rank Math ativo.
  CPT público ganha JSON-LD ao entrar em `starter_schema_tipos_cpt()`.
- **Traduções:** integração com o Polylang em `inc/polylang.php` (template da
  tradução, strings do Customizer, ACF na home traduzida). Strings de interface em
  `languages/{locale}.po|mo` — o nome é `{locale}.mo`, não `{dominio}-{locale}.mo`.

Ilustrações fechadas (com texto embutido na arte) são assets estáticos em
`assets/img/`, renderizados por `starter_imagem_tema()`. Logos do tema:
`assets/img/logo-header.svg` e `logo-footer.svg` (ver `starter_logos_tema()`);
sem eles, vale o logo do Customizer e, por último, o nome do site.

## Ambiente

LocalWP + `WP_DEBUG` ligado. Auditoria local: Query Monitor e Show Current Template.
WP-CLI via `bin/wp` (config por máquina em `bin/wp.config.sh`, não versionada).
