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
		$html  = '<div class="example_demo_card_module__inner">';
		$html .=    '<div class="example_demo_card_module__image">';
		$html .=        $image;
		$html .=    '</div>';
		$html .=    '<div class="example_demo_card_module__content-container">';
		$html .=        $title;
		$html .=        $description;
		$html .=    '</div>';
		$html .=    '<div>'; 
		$html .=    'D5 Global : ' . $d5_global; 
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
