<?php
/**
 * Comando WP-CLI: popula o site com o conteúdo inicial aprovado no layout.
 *
 * Uso:
 *   ./bin/wp starter seed          # cria o que faltar (idempotente)
 *   ./bin/wp starter seed --reset  # apaga o conteúdo do seed e recria
 *
 * Idempotência: cada objeto criado guarda a meta `_starter_seed` com um slug
 * estável. Rodar de novo atualiza em vez de duplicar.
 *
 * Como crescer: um método `criar_*()` por CPT e um `preencher_*()` por página
 * com ACF, todos chamados em __invoke() na ordem de dependência (mídia →
 * páginas → CPTs → campos → menus → Customizer).
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Seeder do conteúdo inicial.
 */
class Starter_Seed {

	/**
	 * Chave de meta que marca objetos criados pelo seed.
	 */
	const META = '_starter_seed';

	/**
	 * Cache de anexos importados (nome base do arquivo => attachment ID).
	 *
	 * @var array<string,int>
	 */
	protected $midia = array();

	/**
	 * Popula o site com o conteúdo inicial.
	 *
	 * ## OPTIONS
	 *
	 * [--reset]
	 * : Apaga tudo que o seed criou antes de recriar.
	 *
	 * @param array $args       Argumentos posicionais.
	 * @param array $assoc_args Flags.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['reset'] ) ) {
			$this->reset();
		}

		WP_CLI::log( '— Importando mídia…' );
		$this->importar_midia();

		WP_CLI::log( '— Criando páginas…' );
		$paginas = $this->criar_paginas();

		WP_CLI::log( '— Criando CPTs…' );
		$this->criar_exemplos();

		WP_CLI::log( '— Preenchendo a home…' );
		$this->preencher_home( $paginas['home'] );

		WP_CLI::log( '— Montando menus…' );
		$this->criar_menus();

		WP_CLI::log( '— Ajustando o Customizer…' );
		$this->configurar_customizer();

		flush_rewrite_rules();

		WP_CLI::success( 'Conteúdo inicial populado.' );
	}

	/* =====================================================================
	   INFRAESTRUTURA
	   ===================================================================== */

	/**
	 * Apaga todo o conteúdo criado por execuções anteriores.
	 *
	 * @return void
	 */
	protected function reset() {
		$ids = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);

		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}

		foreach ( array( 'principal', 'rodape', 'rodape_legal' ) as $location ) {
			$menu = wp_get_nav_menu_object( $this->slug_menu( $location ) );

			if ( $menu ) {
				wp_delete_nav_menu( $menu->term_id );
			}
		}

		WP_CLI::log( sprintf( '  reset: %d objetos removidos.', count( $ids ) ) );
	}

	/**
	 * Cria (ou atualiza) um post identificado pelo slug do seed.
	 *
	 * @param string $slug  Identificador estável do seed (ex.: 'pagina:contato').
	 * @param array  $dados Argumentos de wp_insert_post.
	 * @return int ID do post.
	 */
	protected function upsert( $slug, $dados ) {
		$existente = $this->buscar( $slug, $dados['post_type'] ?? 'post' );
		$dados     = wp_parse_args( $dados, array( 'post_status' => 'publish' ) );

		if ( $existente ) {
			$dados['ID'] = $existente;
			wp_update_post( $dados );

			return $existente;
		}

		$id = wp_insert_post( $dados, true );

		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar "%s": %s', $slug, $id->get_error_message() ) );

			return 0;
		}

		update_post_meta( $id, self::META, $slug );

		return $id;
	}

	/**
	 * Busca um post já criado pelo seed a partir do identificador estável.
	 *
	 * @param string $slug      Identificador do seed.
	 * @param string $post_type Tipo do post.
	 * @return int ID do post, ou 0 se não existir.
	 */
	protected function buscar( $slug, $post_type ) {
		$ids = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $slug,        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Importa os arquivos de assets/seed/ para a biblioteca de mídia.
	 *
	 * Nomeie os arquivos pelo papel deles (ex.: hero-home.webp,
	 * cliente-acme.svg): o nome base vira a chave usada em img().
	 *
	 * @return void
	 */
	protected function importar_midia() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$dir = get_theme_file_path( '/assets/seed' );

		if ( ! is_dir( $dir ) ) {
			WP_CLI::log( '  mídia: pasta assets/seed não encontrada — seguindo sem imagens.' );

			return;
		}

		// Permite SVG no contexto WP-CLI (seed apenas).
		add_filter(
			'upload_mimes',
			static function ( $mimes ) {
				$mimes['svg'] = 'image/svg+xml';
				return $mimes;
			}
		);

		$arquivos = glob( $dir . '/*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE );
		$novos    = 0;

		foreach ( $arquivos as $arquivo ) {
			$nome = basename( $arquivo );
			$base = pathinfo( $nome, PATHINFO_FILENAME );
			$slug = 'midia:' . $base;

			$existente = $this->buscar( $slug, 'attachment' );

			if ( $existente ) {
				$this->midia[ $base ] = $existente;

				continue;
			}

			$tmp = wp_tempnam( $nome );
			copy( $arquivo, $tmp );

			$id = media_handle_sideload(
				array(
					'name'     => $nome,
					'tmp_name' => $tmp,
				),
				0,
				$this->titulo_da_midia( $nome )
			);

			if ( is_wp_error( $id ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				WP_CLI::warning( sprintf( 'Falha ao importar %s: %s', $nome, $id->get_error_message() ) );

				continue;
			}

			update_post_meta( $id, self::META, $slug );
			update_post_meta( $id, '_wp_attachment_image_alt', $this->titulo_da_midia( $nome ) );

			$this->midia[ $base ] = (int) $id;
			++$novos;
		}

		WP_CLI::log( sprintf( '  mídia: %d arquivos (%d novos).', count( $this->midia ), $novos ) );
	}

	/**
	 * Título legível a partir do nome do arquivo de mídia.
	 *
	 * @param string $nome Nome do arquivo.
	 * @return string
	 */
	protected function titulo_da_midia( $nome ) {
		$base = pathinfo( $nome, PATHINFO_FILENAME );
		$base = preg_replace( '/^(cliente|logo|hero|blog|midia)-/', '', $base );

		return ucwords( str_replace( '-', ' ', $base ) );
	}

	/**
	 * ID do anexo importado a partir do nome base do arquivo.
	 *
	 * @param string $nome Nome sem extensão (ex.: 'hero-home').
	 * @return int
	 */
	protected function img( $nome ) {
		return $this->midia[ $nome ] ?? 0;
	}

	/**
	 * Monta o array de um campo ACF do tipo Link.
	 *
	 * @param string $titulo Texto do botão.
	 * @param string $url    URL de destino (relativa ou absoluta).
	 * @return array
	 */
	protected function link( $titulo, $url ) {
		return array(
			'title'  => $titulo,
			'url'    => $this->url_absoluta( $url ),
			'target' => '',
		);
	}

	/**
	 * Converte URL relativa ('/contato/') em absoluta; absolutas passam direto.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	protected function url_absoluta( $url ) {
		$url = (string) $url;

		return ( '/' === substr( $url, 0, 1 ) ) ? home_url( $url ) : $url;
	}

	/**
	 * Grava uma lista de campos ACF num post (nome do campo => valor).
	 *
	 * @param int   $post_id ID do post.
	 * @param array $campos  Campos.
	 * @return void
	 */
	protected function gravar_campos( $post_id, $campos ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::warning( 'ACF inativo — campos não gravados.' );

			return;
		}

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/* =====================================================================
	   PÁGINAS
	   ===================================================================== */

	/**
	 * Cria as páginas do site e define home/blog.
	 *
	 * @return array<string,int> slug => ID.
	 */
	protected function criar_paginas() {
		$definicoes = array(
			'home'        => 'Home',
			'blog'        => 'Blog',
			'contato'     => 'Contato',
			'privacidade' => 'Política de Privacidade',
		);

		$ids = array();

		foreach ( $definicoes as $slug => $titulo ) {
			$ids[ $slug ] = $this->upsert(
				'pagina:' . $slug,
				array(
					'post_type'    => 'page',
					'post_title'   => $titulo,
					'post_name'    => $slug,
					'post_content' => '',
				)
			);
		}

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['blog'] );

		WP_CLI::log( sprintf( '  páginas: %d (home #%d, blog #%d).', count( $ids ), $ids['home'], $ids['blog'] ) );

		return $ids;
	}

	/**
	 * Campos ACF da página inicial (grupo de inc/acf-fields-home.php).
	 *
	 * @param int $home_id ID da página inicial.
	 * @return void
	 */
	protected function preencher_home( $home_id ) {
		$campos = array(
			'hero_eyebrow'   => 'Bem-vindo',
			'hero_titulo'    => 'Título principal com *destaque*',
			'hero_subtitulo' => 'Subtítulo de apoio da home — troque pelo texto aprovado no layout.',
			'hero_botao'     => $this->link( 'Fale conosco', '/contato/' ),
			'hero_imagem'    => $this->img( 'hero-home' ),
		);

		$this->gravar_campos( $home_id, $campos );

		WP_CLI::log( sprintf( '  home: %d campos preenchidos.', count( $campos ) ) );
	}

	/* =====================================================================
	   CPTs
	   ===================================================================== */

	/**
	 * Itens do CPT de exemplo (inc/cpt-exemplo.php).
	 *
	 * @return void
	 */
	protected function criar_exemplos() {
		if ( ! post_type_exists( 'starter_exemplo' ) ) {
			return;
		}

		$itens = array(
			'primeiro' => array( 'Primeiro exemplo', 'Resumo do primeiro item.' ),
			'segundo'  => array( 'Segundo exemplo', 'Resumo do segundo item.' ),
		);

		$ordem = 0;

		foreach ( $itens as $slug => list( $titulo, $resumo ) ) {
			$id = $this->upsert(
				'exemplo:' . $slug,
				array(
					'post_type'  => 'starter_exemplo',
					'post_title' => $titulo,
					'menu_order' => $ordem++,
				)
			);

			$this->gravar_campos( $id, array( 'exemplo_resumo' => $resumo ) );
		}

		WP_CLI::log( sprintf( '  exemplos: %d.', count( $itens ) ) );
	}

	/* =====================================================================
	   MENUS
	   ===================================================================== */

	/**
	 * Monta os menus do tema.
	 *
	 * Itens aceitam a forma curta ('Título' => '/url/') e a completa (array com
	 * titulo, url, descricao, classes e filhos). No menu principal, filhos sem
	 * netos viram painel de cartões e filhos com netos viram mega menu.
	 *
	 * @return void
	 */
	protected function criar_menus() {
		$this->montar_menu(
			'principal',
			'Principal',
			array(
				'Início'  => '/',
				'Blog'    => '/blog/',
				'Contato' => '/contato/',
			)
		);

		$this->montar_menu(
			'rodape',
			'Rodapé',
			array(
				array(
					'titulo' => 'Institucional',
					'url'    => '#',
					'filhos' => array(
						'Blog'    => '/blog/',
						'Contato' => '/contato/',
					),
				),
			)
		);

		$this->montar_menu(
			'rodape_legal',
			'Rodapé — legal',
			array(
				'Política de Privacidade' => '/privacidade/',
			)
		);
	}

	/**
	 * Slug estável do menu de uma location.
	 *
	 * @param string $location Location registrada em functions.php.
	 * @return string
	 */
	protected function slug_menu( $location ) {
		return 'starter-' . $location;
	}

	/**
	 * Cria/recria um menu e atribui à location.
	 *
	 * @param string $location Slug da location registrada.
	 * @param string $nome     Nome legível do menu.
	 * @param array  $itens    Itens (titulo, url, filhos).
	 * @return void
	 */
	protected function montar_menu( $location, $nome, $itens ) {
		$slug = $this->slug_menu( $location );
		$menu = wp_get_nav_menu_object( $slug );

		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}

		$menu_id = wp_create_nav_menu( $slug );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar o menu %s.', $location ) );

			return;
		}

		wp_update_term( $menu_id, 'nav_menu', array( 'name' => $nome ) );

		$this->inserir_itens( $menu_id, $itens, 0 );

		$locations              = get_theme_mod( 'nav_menu_locations', array() );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		$this->sincronizar_polylang( $location, $menu_id );

		WP_CLI::log( sprintf( '  menu "%s" → location %s.', $nome, $location ) );
	}

	/**
	 * Insere recursivamente uma lista de itens sob um item pai.
	 *
	 * @param int   $menu_id ID do menu.
	 * @param array $itens   Itens no formato aceito por normalizar_itens().
	 * @param int   $pai     ID do item pai (0 no 1º nível).
	 * @return void
	 */
	protected function inserir_itens( $menu_id, $itens, $pai ) {
		foreach ( $this->normalizar_itens( $itens ) as $item ) {
			$id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'       => $item['titulo'],
					'menu-item-url'         => $this->url_absoluta( $item['url'] ),
					'menu-item-description' => $item['descricao'],
					'menu-item-classes'     => $item['classes'],
					'menu-item-status'      => 'publish',
					'menu-item-type'        => 'custom',
					'menu-item-parent-id'   => $pai,
				)
			);

			if ( is_wp_error( $id ) || ! $item['filhos'] ) {
				continue;
			}

			$this->inserir_itens( $menu_id, $item['filhos'], (int) $id );
		}
	}

	/**
	 * Normaliza itens de menu (forma curta ou completa).
	 *
	 * @param array $itens Itens em qualquer uma das duas formas.
	 * @return array<int,array>
	 */
	protected function normalizar_itens( $itens ) {
		$saida = array();

		foreach ( $itens as $chave => $item ) {
			if ( ! is_array( $item ) ) {
				$item = array(
					'titulo' => $chave,
					'url'    => $item,
				);
			}

			$saida[] = wp_parse_args(
				$item,
				array(
					'titulo'    => '',
					'url'       => '',
					'descricao' => '',
					'classes'   => '',
					'filhos'    => array(),
				)
			);
		}

		return $saida;
	}

	/**
	 * Registra o menu nas locations do Polylang (que são por idioma).
	 *
	 * Sem isso o Polylang sobrescreve `theme_mod_nav_menu_locations` no front e
	 * o wp_nav_menu sai vazio, mesmo com a location atribuída no tema.
	 *
	 * @param string $location Slug da location.
	 * @param int    $menu_id  ID do menu.
	 * @return void
	 */
	protected function sincronizar_polylang( $location, $menu_id ) {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return;
		}

		$idiomas = pll_languages_list();
		$opcoes  = get_option( 'polylang' );

		if ( ! $idiomas || ! is_array( $opcoes ) ) {
			return;
		}

		$tema = get_stylesheet();

		if ( ! isset( $opcoes['nav_menus'] ) || ! is_array( $opcoes['nav_menus'] ) ) {
			$opcoes['nav_menus'] = array();
		}

		foreach ( $idiomas as $lang ) {
			$opcoes['nav_menus'][ $tema ][ $location ][ $lang ] = $menu_id;
		}

		update_option( 'polylang', $opcoes );
	}

	/* =====================================================================
	   CUSTOMIZER
	   ===================================================================== */

	/**
	 * Define os theme mods usados por header, rodapé e cookies.
	 *
	 * @return void
	 */
	protected function configurar_customizer() {
		$mods = array(
			'starter_header_cta_texto'   => 'Fale conosco',
			'starter_header_cta_url'     => home_url( '/contato/' ),
			'starter_cta_titulo'         => "Vamos conversar\nsobre o seu projeto?",
			'starter_cta_botao_texto'    => 'Entre em contato',
			'starter_cta_botao_url'      => home_url( '/contato/' ),

			// Banner de cookies: texto vazio esconde o banner (ver cookie-banner.php).
			'starter_cookies_texto'      => 'Usamos cookies para melhorar sua experiência. Ao continuar navegando, você concorda com a nossa',
			'starter_cookies_link_texto' => 'política de privacidade',
			'starter_cookies_botao'      => 'Concordar',
		);

		foreach ( $mods as $chave => $valor ) {
			set_theme_mod( $chave, $valor );
		}

		WP_CLI::log( sprintf( '  customizer: %d opções.', count( $mods ) ) );
	}
}

WP_CLI::add_command( 'starter seed', 'Starter_Seed' );
