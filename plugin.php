<?php
/**
 * Plugin Name: Minimal Shortcode UI
 * Description: The minimum code required that allows the user configure and add an shortcode to the page contents using a popup form that is accessible through the TinyMCE content editor. Suitable for developers that wish to provide their users with an easy way of adding your custom shortcodes. The plugin provides an abstract shortcode class that can be extended and registered into the shortcode factory. Which will result in the shortcodes showing up in the dropdown menu.
 * Version: 1.0.0
 * Requires at least: 4.2
 * Author: Dutchwise
 * Author URI: http://www.dutchwise.nl/
 * Text Domain: sui
 * Domain Path: /locale/
 * Network: true
 * License: MIT license (http://www.opensource.org/licenses/mit-license.php)
 */

// expose the shortcode API to other plugins/themes
require_once 'lib/shortcodes.php';

/**
 * Is provided an array of MCE plugins that may be
 * modified to add more plugins and their respective files.
 *
 * @param array $plugins
 * @filter mce_external_plugins
 * @return array
 */
function sui_register_mce_plugin(array $plugin) {
    $plugin['sui'] = plugin_dir_url(__FILE__) . 'js/lib/sui-tinymce-plugin.js';
    return $plugin;
}

/**
 * Is provided an array of MCE buttons that may be
 * modified to add more buttons.
 * 
 * @param array $buttons
 * @filter mce_buttons
 * @return array
 */
function sui_register_mce_buttons(array $buttons) {
    array_push($buttons, 'shortcode-ui');
    return $buttons;
}

/**
 * Is run on admin initialization. Used to add relevant
 * filters used during initialization.
 *
 * @action admin_init
 * @return void
 */
function sui_wp_admin_init() {		
    add_filter('mce_external_plugins', 'sui_register_mce_plugin');
    add_filter('mce_buttons', 'sui_register_mce_buttons');
}

add_action('admin_init', 'sui_wp_admin_init');

/**
 * Loads all .jst files in the provided directory
 * into an array.
 *
 * @param string $dir
 * @return array
 */
function sui_load_underscore_templates($dir) {
	$dir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
	$extension = '.jst';
	$query = $dir . '*' . $extension;
	$files = glob($query);
	$templates = array();
	
	foreach($files as $file) {
		ob_start();
		include $file;
		$templates[basename($file, $extension)] = ob_get_clean();
	}
	
	return $templates;
}

/**
 * Returns all internationalised strings that the
 * client side code needs to display to the
 * admin user.
 *
 * @return array
 */
function sui_get_script_i18n_content() {
	return array(
		'lib/sui-modal-dialog' => array(
			'dialog-title' => __('Insert a shortcode', 'sui'),
			'dialog-cancel' => __('Cancel', 'sui'),
			'dialog-insert' => __('Insert', 'sui'),
			'media-dialog-title' => __('Select or Upload Media', 'sui'),
			'media-dialog-button' => __('Use this media', 'sui'),
			'field-image-alt' => __('Select Image', 'sui')
		),
		'lib/sui-tinymce-plugin' => array(
			'button-label' => __('Insert Shortcode', 'sui'),
			'button-title' => __('Shortcode UI', 'sui'),
			'button-tooltip' => __('Insert a shortcode', 'sui')
		),
		'templates/sui-form' => array(
			'button-image' => __('Select image', 'sui'),
			'label-content' => __('Content', 'sui')
		),
		'templates/sui-select' => array(
			'label-shortcode' => __('Select shortcode', 'sui')
		)
	);
}

/**
 * Is run when admin scripts need to be added.
 *
 * @action admin_enqueue_scripts
 * @return void
 */
function sui_wp_admin_enqueue_scripts() {
	$sui_dialog_js = plugins_url('/js/lib/sui-modal-dialog.js', __FILE__);
	$sui_templates_dir = __DIR__ . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'templates';
	
	// load required scripts
	wp_enqueue_script('sui-modal-dialog', $sui_dialog_js, array(
		'underscore', 'jquery', 'jquery-ui-core',
		'jquery-ui-dialog', 'media-upload'
	));
	
	// inserts the shortcode register, dialog templates and i18n strings
	wp_localize_script('sui-modal-dialog', 'sui', array(
		'shortcodes' => Sui\Shortcodes::registration(),
		'templates' => sui_load_underscore_templates($sui_templates_dir),
		'i18n' => sui_get_script_i18n_content()
	));
}

add_action('admin_enqueue_scripts', 'sui_wp_admin_enqueue_scripts', 10, 1);