<?php
/**
 * DemoCardModule::render_callback()
 *
 * @package MEE\Modules\DemoCardModule
 * @since ??
 */

namespace MEE\Modules\DemoCardModule\DemoCardModuleTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block

use ET\Builder\Packages\Module\Module;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Options\Element\ElementComponents;
use MEE\Modules\DemoCardModule\DemoCardModule;

trait RenderCallbackTrait {

	/**
	 * HelloWorld module render callback which outputs server side rendered HTML on the Front-End.
	 *
	 * @since ??
	 *
	 * @param array          $attrs    Block attributes that were saved by VB.
	 * @param string         $content  Block content.
	 * @param \WP_Block      $block    Parsed block object that is being rendered.
	 * @param ModuleElements $elements ModuleElements instance.
	 *
	 * @return string HTML rendered of HelloWorld module.
	 */
	public static function render_callback( $attrs, $content, $block, $elements ) {
		// Debug éventuel :
		// error_log( '[DemoCardModule] render_callback called' );
		// error_log( '[DemoCardModule] attrs: ' . print_r( $attrs, true ) );
		
		// 1. RECUP DES DONNÉES
		$title = $elements->render(['attrName' => 'title',]);
		$description = $elements->render(['attrName' => 'description',]);

		// Pour le text  brut :
		// $titleText       = $attrs['title']['innerContent']['desktop']['value'] ?? '';
		// $descriptionText = $attrs['description']['innerContent']['desktop']['value'] ?? '';
		
		$image_src = $attrs['image']['innerContent']['desktop']['value']['src'] ?? '';
		$image_alt = $attrs['image']['innerContent']['desktop']['value']['alt'] ?? '';

		// 2. TAG IMAGE		
		$image = '';
		if ( ! empty( $image_src ) ) {
			$image = '<img src="' . $image_src . '" alt="' . $image_alt . '">';
		}				

		// Variables Globales
		$d5_global    = get_option( 'd5_global' );
		$api_key_demo = get_option( 'api_key_demo' );


		// 3. MARKUP HTML DU MODULE
		// Variables de contenu
		$video_id               = 'HW2p_MxvmQI';
		$video_url              = 'https://www.youtube.com/watch?v=HW2p_MxvmQI';
		$channel_url            = 'https://www.youtube.com/@benjamincode';
		$thumb_url              = 'https://i.ytimg.com/vi/HW2p_MxvmQI/maxresdefault.jpg';
		$thumb_alt              = 'Miniature de la vidéo';
		$video_duration         = '22:15';

		$description_title      = 'Description de la vidéo';
		$description_p1         = 'Une liste de 125 idées de projets pour monter en compétence en développement&nbsp;: front, back, mobile, outils perso… De quoi t’entraîner sans jamais être à court d’inspiration.';
		$description_p2_prefix  = 'Tu peux retrouver la liste complète des idées sur la page dédiée :';
		$description_link_url   = 'https://benjamincode.com';
		$description_link_label = 'voir les projets proposés';
		$description_p3         = 'N’hésite pas à piocher plusieurs idées, les combiner, et en faire des projets concrets à ajouter à ton portfolio. Plus tu pratiques, plus tu progresses.';

		$avatar_initial         = 'B';
		$aria_label_channel     = 'Aller sur la chaîne YouTube de Benjamin Code';
		$video_title            = '125 projets de DEV pour progresser (et ne plus jamais manquer d\'idées)';
		$channel_with_subs      = 'Benjamin Code (150k abonnés)';
		$meta_line              = '3,3&nbsp;k&nbsp;vues • 1&nbsp;254&nbsp;likes • il y a 3&nbsp;heures';

		$html  = '<div class="card-yt"';
		$html .=    ' data-video-id="' . $video_id . '"';
		$html .=    ' data-video-url="' . $video_url . '"';
		$html .=    ' data-channel-url="' . $channel_url . '"';
		$html .= '>';
		$html .=    '<!-- ZONE MÉDIA : flip + vidéo -->';
		$html .=    '<div class="card-yt-media">';
		$html .=        '<div class="card-yt-inner">';
		$html .=            '<!-- PILE : juste vignette, bouton et durée -->';
		$html .=            '<div class="card-yt-face card-yt-front">';
		$html .=                '<div class="card-yt-thumb">';
		$html .=                    '<img src="' . $thumb_url . '" alt="' . $thumb_alt . '" />';
		$html .=                    '<div class="card-yt-playbtn"></div>';
		$html .=                    '<div class="card-yt-duration">' . $video_duration . '</div>';
		$html .=                '</div>';
		$html .=            '</div>';
		$html .=            '<!-- FACE : description sur blur -->';
		$html .=            '<div class="card-yt-face card-yt-back">';
		$html .=                '<div class="card-yt-back-inner">';
		$html .=                    '<div class="card-yt-back-gradient"></div>';
		$html .=                    '<!-- Petit play en haut à droite -->';
		$html .=                    '<div class="card-yt-back-play"></div>';
		$html .=                    '<div class="card-yt-back-content">';
		$html .=                        '<h3>' . $description_title . '</h3>';
		$html .=                        '<p>' . $description_p1 . '</p>';
		$html .=                        '<p>';
		$html .=                            $description_p2_prefix . ' ';
		$html .=                            '<a href="' . $description_link_url . '" target="_blank" rel="noopener noreferrer">';
		$html .=                                $description_link_label;
		$html .=                            '</a>.';
		$html .=                        '</p>';
		$html .=                        '<p>' . $description_p3 . '</p>';
		$html .=                    '</div>';
		$html .=                '</div>';
		$html .=            '</div>';
		$html .=        '</div>';
		$html .=        '<!-- Vidéo insérée au clic -->';
		$html .=        '<div class="card-yt-video-wrapper"></div>';
		$html .=    '</div>';
		$html .=    '<!-- INFOS SOUS LA VIGNETTE -->';
		$html .=    '<div class="card-yt-meta">';
		$html .=        '<a';
		$html .=            ' class="card-yt-avatar-link"';
		$html .=            ' href="' . $channel_url . '"';
		$html .=            ' target="_blank"';
		$html .=            ' rel="noopener noreferrer"';
		$html .=            ' aria-label="' . $aria_label_channel . '"';
		$html .=        '>';
		$html .=            '<div class="card-yt-avatar">' . $avatar_initial . '</div>';
		$html .=        '</a>';
		$html .=        '<div class="card-yt-text">';
		$html .=            '<a';
		$html .=                ' class="card-yt-title-link"';
		$html .=                ' href="' . $video_url . '"';
		$html .=                ' target="_blank"';
		$html .=                ' rel="noopener noreferrer"';
		$html .=            '>';
		$html .=                '<h3 class="card-yt-title-line">' . $video_title . '</h3>';
		$html .=            '</a>';
		$html .=            '<div class="card-yt-channel-row">';
		$html .=                '<a';
		$html .=                    ' class="card-yt-channel-link"';
		$html .=                    ' href="' . $channel_url . '"';
		$html .=                    ' target="_blank"';
		$html .=                    ' rel="noopener noreferrer"';
		$html .=                '>';
		$html .=                    $channel_with_subs;
		$html .=                '</a>';
		$html .=            '</div>';
		$html .=            '<div class="card-yt-meta-line">';
		$html .=                $meta_line;
		$html .=            '</div>';
		$html .=        '</div>';
		$html .=    '</div>';
		$html .= '</div>';


		// 4. CONTEXTE PARENT
		$parent       = BlockParserStore::get_parent( $block->parsed_block['id'], $block->parsed_block['storeInstance'] );
		$parent_attrs = $parent->attrs ?? [];

		// 5. RENDU FINAL VIA Module::render()
		return Module::render(
			[
				// FE only.
				'orderIndex'          => $block->parsed_block['orderIndex'],
				'storeInstance'       => $block->parsed_block['storeInstance'],

				// VB equivalent.
				'attrs'               => $attrs,
				'elements'            => $elements,
				'id'                  => $block->parsed_block['id'],
				'name'                => $block->block_type->name,
				'moduleCategory'      => $block->block_type->category,
				'classnamesFunction'  => [ DemoCardModule::class, 'module_classnames' ],
				'stylesComponent'     => [ DemoCardModule::class, 'module_styles' ],
				'scriptDataComponent' => [ DemoCardModule::class, 'module_script_data' ],
				'parentAttrs'         => $parent_attrs,
				'parentId'            => $parent->id ?? '',
				'parentName'          => $parent->blockName ?? '',
				'children'            => [
					ElementComponents::component(
						[
							'attrs'         => $attrs['module']['decoration'] ?? [],
							'id'            => $block->parsed_block['id'],

							// FE only.
							'orderIndex'    => $block->parsed_block['orderIndex'],
							'storeInstance' => $block->parsed_block['storeInstance'],
						]
					),
					$html,
				],
			]
		);
	}
}
