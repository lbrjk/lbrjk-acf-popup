<?php

/**
Plugin Name: LBRJK ACF Popup
Description: A lightweight popup solution using Advanced Custom Fields
Version: 1.0.0
Author: Lumberjack
Author URI: https://lbrjk.com
Text Domain: lbrjk-acf-popup
 */

// Prevent direct file access
defined('ABSPATH') or exit;

define('LACFPOP_VERSION' ,'1.0.0');
define('LACFPOP_PLUGIN_DIR', dirname(__FILE__) . '/');
define('LACFPOP_PLUGIN_URL', plugins_url('/', __FILE__));
define('LACFPOP_PLUGIN_FILE', __FILE__);

/**
 * Set directory for ACF to load fields from
 * @param  string $paths Set new paths
 * @return string        New paths
 */
add_filter( 'acf/settings/load_json', function( $paths ) {
	unset( $paths[0] );

    $paths[] = plugin_dir_path( __FILE__ ) . 'fields';

    return $paths;
} );

/**
 * Simple function to return status of the popup
 * based on field value
 *
 * @return bool is the popup enabled
 */
function lbjrk_popup_enabled() {
	return get_field( 'popup_enabled', 'option' );
}

/**
 * Add the ACF options page
 *
 */
if( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( array(
		'page_title' => 'Pop Up',
		'menu_title' => 'Pop Up',
		'menu_slug'  => 'lj-popup',
		'icon_url'   => 'dashicons-format-image',
		'position'   => '80'
	) );
}

/**
 * Include the necessary plugin styles and scripts
 *
 */
add_action( 'wp_enqueue_scripts', function() {
	if( lbjrk_popup_enabled() ) {
		wp_register_script( 'lbrjk-acf-popup-js', LACFPOP_PLUGIN_URL . 'assets/js/lbrjk-acf-popup.js', array('jquery'), LACFPOP_VERSION, true );
		wp_register_style( 'lbrjk-acf-popup-css', LACFPOP_PLUGIN_URL . 'assets/css/lbrjk-acf-popup.css', array(), LACFPOP_VERSION);
		wp_enqueue_script( 'lbrjk-acf-popup-js' );
		wp_enqueue_style( 'lbrjk-acf-popup-css' );
	}
}, 20, 1 );

/**
 * Insert popup markup to page
 *
 */
add_action( 'wp_footer', function() {
	if( lbjrk_popup_enabled() ) {
		$image = get_field( 'popup_image', 'option' );
		$content = get_field( 'popup_content', 'option' );
		$popup_id = md5( $content['popup_text'] );
		$delay = get_field( 'popup_options', 'option' )['popup_delay'];
		$dismiss = get_field( 'popup_options', 'option' )['popup_dismiss'];
		$button_class = get_field( 'popup_content', 'option' )['popup_button_class'] ? get_field( 'popup_content', 'option' )['popup_button_class'] : 'button';

		echo '<div class="popup" tabindex="-1" role="dialog" aria-labelledby="popup-' . $popup_id . '-title" data-popup-id="' .  $popup_id . '" data-delay="' . $delay . '" data-dismiss="' . $dismiss . '">';
		echo '<div class="popup-dialog" role="document">';
		echo '<div class="popup-content">';
		if( $image['image'] ) {
			echo '<div class="popup-image">';
			echo '<img src="' . $image['image']['sizes']['medium'] . '" alt="' . $image['image']['alt'] . '" class="cover" />';
			echo '</div>';
		}
		echo '<div class="popup-body">';
		if( $content['popup_title'] ) echo '<h3 id="popup-' . $popup_id . '-title" class="popup-title">' . $content['popup_title'] . '</h3>';
		if( $content['popup_text'] ) echo $content['popup_text'];
		if( $content['popup_button'] ) echo '<footer class="popup-actions"><button onclick="window.location.href = \' ' . $content['popup_button']['url'] . ' \'" class="' . $button_class . '">' . $content['popup_button']['title'] . '</button></footer>';
		echo '</div>';
		echo '<button class="popup-dismiss"><svg aria-hidden="true" tabindex="0" focusable="false" role="img" viewBox="0 0 320 512"><path fill="currentColor" d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg><span class="sr-only">Close</span></button>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
} );