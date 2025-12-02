<?php
/*
Plugin Name: D5 Demo Modules: Modules
Plugin URI:
Description: Example modules.
Version:     0.1.0
Author:      Elegant Themes
Author URI:  https://elegantthemes.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: d5-modules-demo
Domain Path: /languages
*/

/* 
Utilisation des valeurs globales
$d5_global    = get_option( 'd5_global' );
$api_key_demo = get_option( 'api_key_demo' );
*/


if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

define( 'D5_DEMO_MODULES_PATH', plugin_dir_path( __FILE__ ) );
define( 'D5_DEMO_MODULES_JSON_PATH', D5_DEMO_MODULES_PATH . 'modules-json/' );

/**
 * Requires Autoloader.
 */
require D5_DEMO_MODULES_PATH . 'vendor/autoload.php';
require D5_DEMO_MODULES_PATH . 'modules/Modules.php';

/**
 * Enqueue style and scripts of Module Extension Example for Visual Builder.
 *
 * @since ??
 */
function d5_demo_modules_enqueue_vb_scripts() {
	if ( et_builder_d5_enabled() && et_core_is_fb_enabled() ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );

		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			[
				'name'   => 'd5-demo-modules-builder-bundle-script',
				'version' => '1.0.0',
				'script' => [
					'src' => "{$plugin_dir_url}scripts/bundle.js",
					'deps'               => [
						'divi-module-library',
						'divi-vendor-wp-hooks',
					],
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
				],
			]
		);

		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			[
				'name'   => 'd5-demo-modules-builder-vb-bundle-style',
				'version' => '1.0.0',
				'style' => [
					'src' => "{$plugin_dir_url}styles/vb-bundle.css",
					'deps'               => [],
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
				],
			]
		);
	}
}
add_action( 'divi_visual_builder_assets_before_enqueue_scripts', 'd5_demo_modules_enqueue_vb_scripts' );

/**
 * Enqueue style and scripts of Module Extension Example
 *
 * @since ??
 */
function d5_demo_modules_enqueue_frontend_scripts() {
	$plugin_dir_url = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'd5-demo-modules-builder-bundle-style', "{$plugin_dir_url}styles/bundle.css", array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'd5_demo_modules_enqueue_frontend_scripts' );



/**
 * ===============================
 *  Page de configuration / options
 * ===============================
 */

/**
 * Enregistre les options du plugin.
 */
function d5_demo_modules_register_settings() {
	// VarGlobale "d5_global"
	register_setting(
		'd5_demo_modules_options_group',
		'd5_global',
		[
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		]
	);

	// Clé API "api_key_demo"
	register_setting(
		'd5_demo_modules_options_group',
		'api_key_demo',
		[
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		]
	);
}
add_action( 'admin_init', 'd5_demo_modules_register_settings' );

/**
 * Ajoute l’entrée de menu "D5 Demo Modules" dans l’admin.
 */
function d5_demo_modules_add_admin_menu() {
	add_menu_page(
		__( 'D5 Demo Modules', 'd5-modules-demo' ), // Titre de la page
		__( 'D5 Demo Modules', 'd5-modules-demo' ), // Libellé du menu
		'manage_options',                           // Capacité requise
		'd5-demo-modules-settings',                 // Slug
		'd5_demo_modules_render_settings_page',     // Callback d’affichage
		'dashicons-admin-generic',                  // Icône
		59                                          // Position dans le menu
	);
}
add_action( 'admin_menu', 'd5_demo_modules_add_admin_menu' );

/**
 * Affiche la page de configuration.
 */
function d5_demo_modules_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$d5_global    = get_option( 'd5_global', '' );
	$api_key_demo = get_option( 'api_key_demo', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'D5 Demo Modules', 'd5-modules-demo' ); ?></h1>

		<form method="post" action="options.php">
			<?php
				// Ajoute les champs de sécurité pour le groupe d’options.
				settings_fields( 'd5_demo_modules_options_group' );
			?>

			<table class="form-table" role="presentation">
				<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="d5_global">
							<?php esc_html_e( 'VarGlobale "d5_global"', 'd5-modules-demo' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							id="d5_global"
							name="d5_global"
							value="<?php echo esc_attr( $d5_global ); ?>"
							class="regular-text"
						/>
						<p class="description">
							<?php esc_html_e( 'Valeur globale utilisée par le plugin.', 'd5-modules-demo' ); ?>
						</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="api_key_demo">
							<?php esc_html_e( 'Clé API "api_key_demo"', 'd5-modules-demo' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							id="api_key_demo"
							name="api_key_demo"
							value="<?php echo esc_attr( $api_key_demo ); ?>"
							class="regular-text"
							autocomplete="off"
						/>
						<p class="description">
							<?php esc_html_e( 'Clé API pour les appels externes (exemple).', 'd5-modules-demo' ); ?>
						</p>
					</td>
				</tr>
				</tbody>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
