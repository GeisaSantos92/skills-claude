# R8 Treinamentos

Plugin WordPress da Agência R8 que adiciona uma **Central de Treinamentos** ao painel:
vídeos tutoriais para o cliente aprender a gerenciar o próprio site.

Sem build, sem dependências: PHP + CSS/JS estáticos, dados em duas options.

## O que ele faz

- **Menu "Treinamentos"** no painel (abaixo do Painel), visível para quem pode editar
  posts (`edit_posts`): player de vídeo + lista dos módulos.
- **Treinamentos → Gerenciar** (só a função **Agência R8**): cadastra, edita, reordena
  (arrastar ou setas) e remove treinamentos. Cada um tem título, descrição e vídeo.
- **Vídeo**: arquivo da Biblioteca de Mídia, URL direta (.mp4/.webm) ou link do
  YouTube/Vimeo (oEmbed, com cache).
- **Widget no Dashboard** com os primeiros treinamentos e o botão "Acessar Treinamentos".
- **Aparência e textos** na mesma tela: cor de destaque, título, subtítulo, texto do
  widget e um botão de atalho opcional (ex.: "Gerenciar imóveis" → `edit.php?post_type=imovel`).

## Função "Agência R8" (`agenciar8`)

O plugin cria a função **Agência R8**: tem tudo que o administrador tem (inclusive
capabilities que outros plugins derem ao administrador depois) e é a única com
`r8t_manage_trainings`. Todos os outros, **inclusive administradores**, só assistem:

- não veem o submenu Gerenciar nem nenhum botão de cadastro;
- não veem a opção "Agência R8" ao criar/editar usuários e não conseguem se promover;
- não veem, editam, excluem nem trocam a senha de usuários Agência R8 (eles somem da
  tela Usuários e da contagem);
- não veem o plugin em Plugins, não conseguem desativá-lo nem abri-lo no editor de plugins;
- não veem nem apagam, na Mídia, os vídeos usados nos treinamentos.

A função guarda uma cópia das capabilities do administrador, então quem é Agência R8
continua com acesso total mesmo com o plugin desativado.

Limite: quem tem FTP/banco de dados ou instala um editor de funções (ex.: User Role
Editor) consegue contornar. A proteção vale para o uso normal do painel.

## Instalação

1. Copie a pasta `r8-treinamentos/` para `wp-content/plugins/` (ou envie um `.zip`
   dela em Plugins → Adicionar novo → Enviar plugin).
2. Ative o plugin. Na ativação (ou na atualização a partir da 1.0), se ainda não
   existir nenhum Agência R8, o administrador com login **agenciar8** e todo
   administrador com e-mail **@agenciar8.com.br** viram
   Agência R8. Se não houver nenhum, vira o primeiro administrador que abrir o painel,
   então nesse caso ative logado com o usuário da agência.
3. Vá em **Treinamentos → Gerenciar** e cadastre os vídeos.
4. Para tornar outro usuário Agência R8: Usuários → editar → Função "Agência R8"
   (só um Agência R8 vê essa opção). Pelo WP-CLI: `wp user set-role <usuario> agenciar8`.

Ao excluir o plugin pelo painel, as options do plugin são apagadas e quem era
Agência R8 volta a ser administrador.

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
| `r8t_manage_capability` | `r8t_manage_trainings` | Quem cadastra treinamentos (só Agência R8). |

## Estrutura

```
r8-treinamentos/
├── r8-treinamentos.php        # cabeçalho + requires
├── includes/
│   ├── data.php               # options, sanitização, permissões, URLs
│   ├── access.php             # função agenciar8 e restrições de acesso
│   ├── video.php              # player (arquivo de vídeo ou oEmbed)
│   ├── admin-pages.php        # telas Treinamentos e Gerenciar + salvamento
│   └── dashboard-widget.php   # widget do Dashboard
├── assets/css/admin.css
├── assets/js/manage.js
└── uninstall.php
```
