<?php
/**
 * Module: Demo Etudiant Module class.
 *
 * @package MEE\Modules\DemoEtudiantModule
 * @since ??
 */

namespace MEE\Modules\DemoEtudiantModule;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;


/**
 * `DemoEtudiantModule` is consisted of functions used for Demo Etudiant Module such as Front-End rendering, REST API Endpoints etc.
 *
 * This is a dependency class and can be used as a dependency for `DependencyTree`.
 *
 * @since ??
 */
class DemoEtudiantModule implements DependencyInterface {
	use DemoEtudiantModuleTrait\RenderCallbackTrait;
	use DemoEtudiantModuleTrait\ModuleClassnamesTrait;
	use DemoEtudiantModuleTrait\ModuleStylesTrait;
	use DemoEtudiantModuleTrait\ModuleScriptDataTrait;

	/**
	 * Loads `DemoEtudiantModule` and registers Front-End render callback and REST API Endpoints.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public function load() {
		$plugin_root = dirname( dirname( __DIR__ ) );
		$module_json_folder_path = trailingslashit( $plugin_root ) . 'modules-json/demo-etudiant-module/';

		// DEBUG : log au chargement
		error_log('[DemoEtudiantModule] load() called');
		error_log('[DemoEtudiantModule] JSON path: ' . $module_json_folder_path);
		error_log(
			'[DemoEtudiantModule] module.json exists: ' .
			( file_exists( $module_json_folder_path . 'module.json' ) ? 'YES' : 'NO' )
		);
		
		add_action(
			'init',
			function() use ( $module_json_folder_path ) {
				ModuleRegistration::register_module(
					$module_json_folder_path,
					[
						'render_callback' => [ DemoEtudiantModule::class, 'render_callback' ],
					]
				);
			}
		);
	}


}
