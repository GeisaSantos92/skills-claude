# R8 Treinamentos

Plugin WordPress da Agência R8 que adiciona uma **Central de Treinamentos** ao painel:
vídeos tutoriais para o cliente aprender a gerenciar o próprio site.

Sem build, sem dependências: PHP + CSS/JS estáticos, dados em duas options.

## O que ele faz

- **Menu "Treinamentos"** no painel (abaixo do Painel), visível para quem pode editar
  posts (`edit_posts`): player de vídeo + lista dos módulos.
- **Treinamentos → Gerenciar** (só administradores): cadastra, edita, reordena
  (arrastar ou setas) e remove treinamentos. Cada um tem título, descrição e vídeo.
- **Vídeo**: arquivo da Biblioteca de Mídia, URL direta (.mp4/.webm) ou link do
  YouTube/Vimeo (oEmbed, com cache).
- **Widget no Dashboard** com os primeiros treinamentos e o botão "Acessar Treinamentos".
- **Aparência e textos** na mesma tela: cor de destaque, título, subtítulo, texto do
  widget e um botão de atalho opcional (ex.: "Gerenciar imóveis" → `edit.php?post_type=imovel`).

## Instalação

1. Copie a pasta `r8-treinamentos/` para `wp-content/plugins/` (ou envie um `.zip`
   dela em Plugins → Adicionar novo → Enviar plugin).
2. Ative o plugin.
3. Vá em **Treinamentos → Gerenciar** e cadastre os vídeos.

Ao excluir o plugin pelo painel, as options `r8t_trainings` e `r8t_settings` são apagadas.

## Integração com o tema (opcional)

Tudo é editável pelo painel, mas o tema pode definir os valores iniciais:

```php
// Treinamentos gravados na primeira vez que o plugin roda no site
// (útil para migrar vídeos antigos ou já entregar a lista pronta).
add_filter( 'r8t_seed_trainings', function () {
	return array(
		array(
			'id'          => 'cadastro-imoveis',
			'title'       => 'Cadastro de Imóveis',
			'description' => 'Passo a passo para cadastrar um imóvel.',
			'video'       => get_option( 'meu_video_antigo', '' ),
		),
	);
} );

// Padrões de aparência (valem enquanto o admin não salvar outros).
add_filter( 'r8t_default_settings', function ( $defaults ) {
	return array(
		'accent'       => '#C79A4A',
		'button_label' => 'Gerenciar imóveis',
		'button_url'   => 'edit.php?post_type=imovel',
	);
} );
```

| Filtro | Padrão | Para quê |
| --- | --- | --- |
| `r8t_seed_trainings` | `array()` | Lista inicial de treinamentos (só na primeira execução). |
| `r8t_default_settings` | — | Padrões de cor, textos e botão de atalho. |
| `r8t_view_capability` | `edit_posts` | Quem assiste aos treinamentos. |
| `r8t_manage_capability` | `manage_options` | Quem cadastra treinamentos. |

## Estrutura

```
r8-treinamentos/
├── r8-treinamentos.php        # cabeçalho + requires
├── includes/
│   ├── data.php               # options, sanitização, permissões, URLs
│   ├── video.php              # player (arquivo de vídeo ou oEmbed)
│   ├── admin-pages.php        # telas Treinamentos e Gerenciar + salvamento
│   └── dashboard-widget.php   # widget do Dashboard
├── assets/css/admin.css
├── assets/js/manage.js
└── uninstall.php
```
