<?php
/**
 * DemoEtudiantModule::custom_css().
 *
 * @package MEE\Modules\DemoEtudiantModule
 * @since ??
 */

namespace MEE\Modules\DemoEtudiantModule\DemoEtudiantModuleTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

trait CustomCssTrait {

	/**
	 * Custom CSS fields
	 *
	 * This function is equivalent of JS const cssFields located in
	 * src/components/demo-etudiant-module/custom-css.ts.
	 *
	 * A minor difference with the JS const cssFields, this function did not have `label` property on each array item.
	 *
	 * @since ??
	 */
	public static function custom_css() {
		return \WP_Block_Type_Registry::get_instance()->get_registered( 'example/demo-etudiant-module' )->customCssFields;
	}

}
