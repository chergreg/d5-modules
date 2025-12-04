<?php
/**
 * Module: Demo Card Y T Module class.
 *
 * @package MEE\Modules\DemoCardYTModule
 * @since ??
 */

namespace MEE\Modules\DemoCardYTModule;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;


/**
 * `DemoCardYTModule` is consisted of functions used for Demo Card Y T Module such as Front-End rendering, REST API Endpoints etc.
 *
 * This is a dependency class and can be used as a dependency for `DependencyTree`.
 *
 * @since ??
 */
class DemoCardYTModule implements DependencyInterface {
	use DemoCardYTModuleTrait\RenderCallbackTrait;
	use DemoCardYTModuleTrait\ModuleClassnamesTrait;
	use DemoCardYTModuleTrait\ModuleStylesTrait;
	use DemoCardYTModuleTrait\ModuleScriptDataTrait;

	/**
	 * Loads `DemoCardYTModule` and registers Front-End render callback and REST API Endpoints.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public function load() {
		$plugin_root = dirname( dirname( __DIR__ ) );
		$module_json_folder_path = trailingslashit( $plugin_root ) . 'modules-json/demo-card-y-t-module/';

		// DEBUG : log au chargement
		error_log('[DemoCardYTModule] load() called');
		error_log('[DemoCardYTModule] JSON path: ' . $module_json_folder_path);
		error_log(
			'[DemoCardYTModule] module.json exists: ' .
			( file_exists( $module_json_folder_path . 'module.json' ) ? 'YES' : 'NO' )
		);
		
		add_action(
			'init',
			function() use ( $module_json_folder_path ) {
				ModuleRegistration::register_module(
					$module_json_folder_path,
					[
						'render_callback' => [ DemoCardYTModule::class, 'render_callback' ],
					]
				);
			}
		);
	}


}
