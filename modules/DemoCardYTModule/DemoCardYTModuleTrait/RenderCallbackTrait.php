<?php
/**
 * DemoCardYTModule::render_callback()
 *
 * @package MEE\Modules\DemoCardYTModule
 * @since ??
 */

namespace MEE\Modules\DemoCardYTModule\DemoCardYTModuleTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block

use ET\Builder\Packages\Module\Module;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Options\Element\ElementComponents;
use MEE\Modules\DemoCardYTModule\DemoCardYTModule;

trait RenderCallbackTrait {

	/**
	 * DemoCardYT module render callback which outputs server side rendered HTML on the Front-End.
	 *
	 * @since ??
	 *
	 * @param array          $attrs    Block attributes that were saved by VB.
	 * @param string         $content  Block content.
	 * @param \WP_Block      $block    Parsed block object that is being rendered.
	 * @param ModuleElements $elements ModuleElements instance.
	 *
	 * @return string HTML rendered of DemoCardYT module.
	 */
	public static function render_callback( $attrs, $content, $block, $elements ) {
		// Debug éventuel :
		// error_log( '[DemoCardYTModule] render_callback called' );
		// error_log( '[DemoCardYTModule] attrs: ' . print_r( $attrs, true ) );

		// 1. RECUP DES DONNÉES DU MODULE
		$title       = $elements->render(
			[
				'attrName' => 'title',
			]
		);
		$description = $elements->render(
			[
				'attrName' => 'description',
			]
		);

		// Pour le texte brut :
		// $titleText       = $attrs['title']['innerContent']['desktop']['value'] ?? '';
		// $descriptionText = $attrs['description']['innerContent']['desktop']['value'] ?? '';

		$image_src = $attrs['image']['innerContent']['desktop']['value']['src'] ?? '';
		$image_alt = $attrs['image']['innerContent']['desktop']['value']['alt'] ?? '';

		// 2. TAG IMAGE
		$image = '';
		if ( ! empty( $image_src ) ) {
			$image = '<img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '">';
		}

		// Variables Globales
		$d5_global    = get_option( 'd5_global' );
		$api_key_demo = get_option( 'api_key_demo' );


		$video_id_text = $attrs['video_id']['innerContent']['desktop']['value'] ?? '';

		// 3. CONFIG YOUTUBE (dans render_callback comme demandé)
		// Tu peux utiliser une option dédiée si tu préfères, ex: get_option( 'yt_api_key' ).
		$api_key_yt = $api_key_demo; // ou une autre option/valeur.
		$video_id   = $video_id_text; //'WxkBjU40cDc'; // ou un attribut de module plus tard.
		

		// 4. RÉCUPÉRATION DES DONNÉES YOUTUBE
		$video_data = null;

		if ( ! empty( $api_key_yt ) && ! empty( $video_id ) ) {
			$video_data = self::d5_demo_get_youtube_video_data( $api_key_yt, $video_id );
		}

		// 5. VARIABLES POUR LA CARD (fallback statique si erreur API)
		if ( ! is_array( $video_data ) || is_wp_error( $video_data ) ) {
			// Fallback : données statiques (ton exemple initial).
			$video_id               = 'HW2p_MxvmQI';
			$video_url              = 'https://www.youtube.com/watch?v=HW2p_MxvmQI';
			$channel_url            = 'https://www.youtube.com/@benjamincode';
			$thumb_url              = 'https://i.ytimg.com/vi/HW2p_MxvmQI/maxresdefault.jpg';
			$thumb_alt              = 'Miniature de la vidéo';
			$video_duration         = '22:15';

			$description_title      = 'Description de la vidéo';
			$description_text       = "Une liste de 125 idées de projets pour monter en compétence en développement : front, back, mobile, outils perso… De quoi t’entraîner sans jamais être à court d’inspiration.\n\nTu peux retrouver la liste complète des idées sur la page dédiée : voir les projets proposés.\n\nN’hésite pas à piocher plusieurs idées, les combiner, et en faire des projets concrets à ajouter à ton portfolio. Plus tu pratiques, plus tu progresses.";

			$channel_title          = 'Nom de la Chaine';
			$channel_with_subs      = 'Nom de la Chaine (XXk abonnés)';
			$meta_line              = '3,3&nbsp;k&nbsp;vues • 1&nbsp;254&nbsp;likes • il y a 3&nbsp;heures';

			$avatar_initial         = 'B';
			$aria_label_channel     = 'Aller sur la chaîne YouTube de Benjamin Code';
			$video_title            = 'Titre de la vidéo';
		} else {
			// Données dynamiques depuis l’API YouTube.
			$video_id           = $video_data['video_id'];
			$video_url          = $video_data['video_url'];
			$channel_url        = $video_data['channel_url'];
			$thumb_url          = $video_data['thumb_url'];
			$thumb_alt          = $video_data['thumb_alt'];
			$video_duration     = $video_data['duration'];
			$description_title  = $video_data['title'];
			$description_text   = $video_data['description'];
			$channel_title      = $video_data['channel_title'];
			$channel_with_subs  = $video_data['channel_with_subs'];
			$meta_line          = $video_data['meta_line'];

			// Initial avatar = première lettre du nom de chaîne.
			$avatar_initial     = mb_substr( $channel_title, 0, 1 );
			$aria_label_channel = sprintf(
				__( 'Aller sur la chaîne YouTube de %s', 'd5-modules-demo' ),
				$channel_title
			);

			// Titre principal sous la vignette = titre de la vidéo.
			$video_title = $video_data['title'];
		}

		// 6. MARKUP HTML DU MODULE (CARD YT)
		$html  = '<div class="card-yt"';
		$html .=    ' data-video-id="' . esc_attr( $video_id ) . '"';
		$html .=    ' data-video-url="' . esc_url( $video_url ) . '"';
		$html .=    ' data-channel-url="' . esc_url( $channel_url ) . '"';
		$html .= '>';
		$html .=    '<!-- ZONE MÉDIA : flip + vidéo -->';
		$html .=    '<div class="card-yt-media">';
		$html .=        '<div class="card-yt-inner">';
		$html .=            '<!-- PILE : juste vignette, bouton et durée -->';
		$html .=            '<div class="card-yt-face card-yt-front">';
		$html .=                '<div class="card-yt-thumb">';
		$html .=                    '<img src="' . esc_url( $thumb_url ) . '" alt="' . esc_attr( $thumb_alt ) . '" />';
		$html .=                    '<div class="card-yt-playbtn"></div>';
		$html .=                    '<div class="card-yt-duration">' . esc_html( $video_duration ) . '</div>';
		$html .=                '</div>';
		$html .=            '</div>';
		$html .=            '<!-- FACE : description sur blur -->';
		$html .=            '<div class="card-yt-face card-yt-back">';
		$html .=                '<div class="card-yt-back-inner">';
		$html .=                    '<div class="card-yt-back-gradient"></div>';
		$html .=                    '<!-- Petit play en haut à droite -->';
		$html .=                    '<div class="card-yt-back-play"></div>';
		$html .=                    '<div class="card-yt-back-content">';
		$html .=                        '<h3>' . esc_html( $description_title ) . '</h3>';
		// On convertit les retours à la ligne de la description en <br>.
		$html .=                        '<p>' . nl2br( esc_html( $description_text ) ) . '</p>';
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
		$html .=            ' href="' . esc_url( $channel_url ) . '"';
		$html .=            ' target="_blank"';
		$html .=            ' rel="noopener noreferrer"';
		$html .=            ' aria-label="' . esc_attr( $aria_label_channel ) . '"';
		$html .=        '>';
		$html .=            '<div class="card-yt-avatar">' . esc_html( $avatar_initial ) . '</div>';
		$html .=        '</a>';
		$html .=        '<div class="card-yt-text">';
		$html .=            '<a';
		$html .=                ' class="card-yt-title-link"';
		$html .=                ' href="' . esc_url( $video_url ) . '"';
		$html .=                ' target="_blank"';
		$html .=                ' rel="noopener noreferrer"';
		$html .=            '>';
		$html .=                '<h3 class="card-yt-title-line">' . esc_html( $video_title ) . '</h3>';
		$html .=            '</a>';
		$html .=            '<div class="card-yt-channel-row">';
		$html .=                '<a';
		$html .=                    ' class="card-yt-channel-link"';
		$html .=                    ' href="' . esc_url( $channel_url ) . '"';
		$html .=                    ' target="_blank"';
		$html .=                    ' rel="noopener noreferrer"';
		$html .=                '>';
		$html .=                    esc_html( $channel_with_subs );
		$html .=                '</a>';
		$html .=            '</div>';
		// meta_line contient déjà les &nbsp; et le texte formaté.
		$html .=            '<div class="card-yt-meta-line">' . $meta_line . '</div>';
		$html .=        '</div>';
		$html .=    '</div>';
		$html .= '</div>';

		// 7. CONTEXTE PARENT
		$parent       = BlockParserStore::get_parent( $block->parsed_block['id'], $block->parsed_block['storeInstance'] );
		$parent_attrs = $parent->attrs ?? [];

		// 8. RENDU FINAL VIA Module::render()
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
				'classnamesFunction'  => [ DemoCardYTModule::class, 'module_classnames' ],
				'stylesComponent'     => [ DemoCardYTModule::class, 'module_styles' ],
				'scriptDataComponent' => [ DemoCardYTModule::class, 'module_script_data' ],
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

	// ---------------------------------------------------------------------
	// HELPERS API YOUTUBE (méthodes privées statiques pour rester modulaires)
	// ---------------------------------------------------------------------

	/**
	 * Appel générique à l’API YouTube Data v3.
	 *
	 * @param string $path   Endpoint (videos, channels, etc.).
	 * @param array  $params Paramètres de requête.
	 * @param string $api_key Clé d'API YouTube.
	 *
	 * @return array|\WP_Error
	 */
	private static function d5_demo_yt_api_request( $path, array $params, $api_key ) {
		$base_url       = 'https://www.googleapis.com/youtube/v3/' . ltrim( $path, '/' );
		$params['key']  = $api_key;
		$url            = add_query_arg( $params, $base_url );

		$response = wp_remote_get(
			$url,
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $code ) {
			return new \WP_Error(
				'd5_demo_yt_http_error',
				sprintf( 'Erreur HTTP YouTube : %d', $code )
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( null === $data || ! is_array( $data ) ) {
			return new \WP_Error(
				'd5_demo_yt_json_error',
				'Réponse JSON YouTube invalide.'
			);
		}

		return $data;
	}

	/**
	 * Récupère les infos d’une vidéo YouTube (snippet, contentDetails, statistics).
	 *
	 * @param string $api_key
	 * @param string $video_id
	 *
	 * @return array|\WP_Error
	 */
	private static function d5_demo_get_youtube_video_data( $api_key, $video_id ) {
		$data = self::d5_demo_yt_api_request(
			'videos',
			[
				'part' => 'snippet,contentDetails,statistics',
				'id'   => $video_id,
			],
			$api_key
		);

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data['items'] ) || ! isset( $data['items'][0] ) ) {
			return new \WP_Error(
				'd5_demo_yt_no_video',
				'Aucune vidéo trouvée pour cet ID.'
			);
		}

		$item       = $data['items'][0];
		$snippet    = isset( $item['snippet'] ) ? $item['snippet'] : [];
		$stats      = isset( $item['statistics'] ) ? $item['statistics'] : [];
		$content    = isset( $item['contentDetails'] ) ? $item['contentDetails'] : [];
		$channel_id = isset( $snippet['channelId'] ) ? $snippet['channelId'] : '';

		// Vignette : on tente maxres > standard > high > medium > default.
		$thumb_url = '';
		if ( ! empty( $snippet['thumbnails']['maxres']['url'] ) ) {
			$thumb_url = $snippet['thumbnails']['maxres']['url'];
		} elseif ( ! empty( $snippet['thumbnails']['standard']['url'] ) ) {
			$thumb_url = $snippet['thumbnails']['standard']['url'];
		} elseif ( ! empty( $snippet['thumbnails']['high']['url'] ) ) {
			$thumb_url = $snippet['thumbnails']['high']['url'];
		} elseif ( ! empty( $snippet['thumbnails']['medium']['url'] ) ) {
			$thumb_url = $snippet['thumbnails']['medium']['url'];
		} elseif ( ! empty( $snippet['thumbnails']['default']['url'] ) ) {
			$thumb_url = $snippet['thumbnails']['default']['url'];
		}

		// Durée au format 22:15.
		$duration_iso = isset( $content['duration'] ) ? $content['duration'] : '';
		$duration     = self::d5_demo_format_yt_duration( $duration_iso );

		$views = isset( $stats['viewCount'] ) ? (int) $stats['viewCount'] : 0;
		$likes = isset( $stats['likeCount'] ) ? (int) $stats['likeCount'] : 0;

		// Date de publication.
		$published_at = ! empty( $snippet['publishedAt'] ) ? strtotime( $snippet['publishedAt'] ) : 0;

		// Formatages.
		$views_short   = self::d5_demo_format_yt_count_short( $views );          // ex: "3,3 k"
		$likes_full    = self::d5_demo_format_yt_number_nbsp( $likes );          // ex: "1&nbsp;254"
		$time_ago      = self::d5_demo_format_yt_time_ago( $published_at );      // ex: "il y a 3 heures"
		$views_label   = $views_short . '&nbsp;vues';
		$likes_label   = $likes_full . '&nbsp;likes';
		$meta_line     = $views_label . ' • ' . $likes_label . ' • ' . $time_ago;

		$channel_title = isset( $snippet['channelTitle'] ) ? $snippet['channelTitle'] : '';
		$channel_url   = $channel_id ? 'https://www.youtube.com/channel/' . $channel_id : '';

		// Optionnel : récupérer les stats de la chaîne pour les abonnés.
		$channel_with_subs = $channel_title;
		if ( $channel_id ) {
			$channel_data = self::d5_demo_get_youtube_channel_data( $api_key, $channel_id );
			if ( ! is_wp_error( $channel_data ) && ! empty( $channel_data['subscriberCount'] ) ) {
				$subs_short        = self::d5_demo_format_yt_count_short( (int) $channel_data['subscriberCount'] );
				$channel_with_subs = sprintf( '%s (%s abonnés)', $channel_title, $subs_short );
			}
		}

		return [
			'video_id'              => $video_id,
			'video_url'             => 'https://www.youtube.com/watch?v=' . $video_id,
			'title'                 => isset( $snippet['title'] ) ? $snippet['title'] : '',
			'description'           => isset( $snippet['description'] ) ? $snippet['description'] : '',
			'thumb_url'             => $thumb_url,
			'thumb_alt'             => isset( $snippet['title'] ) ? $snippet['title'] : 'Miniature de la vidéo',
			'channel_id'            => $channel_id,
			'channel_title'         => $channel_title,
			'channel_url'           => $channel_url,
			'duration'              => $duration,
			'views'                 => $views,
			'likes'                 => $likes,
			'meta_line'             => $meta_line,
			'channel_with_subs'     => $channel_with_subs,
			'published_at'          => $published_at,
			'views_short'           => $views_short,
			'likes_formatted_nbsp'  => $likes_full,
			'time_ago'              => $time_ago,
		];
	}

	/**
	 * Récupère les infos principales d’une chaîne YouTube (pour les abonnés).
	 *
	 * @param string $api_key
	 * @param string $channel_id
	 *
	 * @return array|\WP_Error
	 */
	private static function d5_demo_get_youtube_channel_data( $api_key, $channel_id ) {
		$data = self::d5_demo_yt_api_request(
			'channels',
			[
				'part' => 'statistics,snippet',
				'id'   => $channel_id,
			],
			$api_key
		);

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data['items'] ) || ! isset( $data['items'][0] ) ) {
			return new \WP_Error(
				'd5_demo_yt_no_channel',
				'Aucune chaîne trouvée pour cet ID.'
			);
		}

		$item  = $data['items'][0];
		$stats = isset( $item['statistics'] ) ? $item['statistics'] : [];
		$subs  = isset( $stats['subscriberCount'] ) ? (int) $stats['subscriberCount'] : 0;

		return [
			'subscriberCount' => $subs,
		];
	}

	// ---------------------------------------------------------------------
	// HELPERS DE FORMATAGE
	// ---------------------------------------------------------------------

	/**
	 * Formatte la durée ISO8601 de YouTube (PT22M15S) en "22:15" ou "1:02:03".
	 *
	 * @param string $iso_duration
	 *
	 * @return string
	 */
	private static function d5_demo_format_yt_duration( $iso_duration ) {
		if ( empty( $iso_duration ) ) {
			return '';
		}

		try {
			$interval = new \DateInterval( $iso_duration );
		} catch ( \Exception $e ) {
			return '';
		}

		$hours   = (int) $interval->h + (int) $interval->d * 24;
		$minutes = (int) $interval->i;
		$seconds = (int) $interval->s;

		if ( $hours > 0 ) {
			return sprintf( '%d:%02d:%02d', $hours, $minutes, $seconds );
		}

		return sprintf( '%02d:%02d', $minutes, $seconds );
	}

	/**
	 * Formatte un compte (vues, abonnés, etc.) en style court FR : 3,3 k / 1,2 M / etc.
	 *
	 * @param int $number
	 *
	 * @return string
	 */
	private static function d5_demo_format_yt_count_short( $number ) {
		$number = (int) $number;

		if ( $number >= 1000000 ) {
			$value = $number / 1000000;
			$str   = number_format_i18n( $value, 1 ); // ex: 1,2
			$str   = str_replace( ' ', '&nbsp;', $str );
			return $str . '&nbsp;M';
		}

		if ( $number >= 1000 ) {
			$value = $number / 1000;
			$str   = number_format_i18n( $value, 1 ); // ex: 3,3
			$str   = str_replace( ' ', '&nbsp;', $str );
			return $str . '&nbsp;k';
		}

		$str = number_format_i18n( $number, 0 );
		return str_replace( ' ', '&nbsp;', $str );
	}

	/**
	 * Formatte un nombre avec séparateur de milliers et espaces insécables (&nbsp;).
	 *
	 * @param int $number
	 *
	 * @return string
	 */
	private static function d5_demo_format_yt_number_nbsp( $number ) {
		$str = number_format_i18n( (int) $number, 0 );
		return str_replace( ' ', '&nbsp;', $str );
	}

	/**
	 * Retourne une chaîne "il y a X" basée sur la date de publication.
	 *
	 * @param int $timestamp Publication Unix (UTC ou local converti).
	 *
	 * @return string
	 */
	private static function d5_demo_format_yt_time_ago( $timestamp ) {
		if ( ! $timestamp ) {
			return '';
		}

		$now  = current_time( 'timestamp' );
		$diff = human_time_diff( $timestamp, $now ); // Localisé par WordPress.

		// On préfixe par "il y a", la traduction de human_time_diff fera le reste.
		return sprintf( __( 'il y a %s', 'd5-modules-demo' ), $diff );
	}
}
