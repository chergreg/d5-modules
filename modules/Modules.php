<?php
/**
 * Register all modules with dependency tree.
 *
 * @package MEE\Modules
 * @since ??
 */

namespace MEE\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use MEE\Modules\StaticModule\StaticModule;
use MEE\Modules\HelloWorldModule\HelloWorldModule;
use MEE\Modules\DemoEtudiantModule\DemoEtudiantModule;


add_action(
	'divi_module_library_modules_dependency_tree',
	function ( $dependency_tree ) {
		$dependency_tree->add_dependency( new StaticModule() );
		$dependency_tree->add_dependency( new HelloWorldModule() );
		$dependency_tree->add_dependency( new DemoEtudiantModule() );
	}
);
