<?php
/**
 * Plugin Name:       Posts to QR Code plugin
 * Plugin URI:        https://github.com/arafatjamil01/posts-to-qrcode
 * Description:       Add a QR Code at the end of the post.
 * Version:           1.0
 * Author:            Arafat Jamil
 * Author URI:        https://github.com/arafatjamil01
 * License:           GPL v2 or later
 * Text Domain:       post-to-qrcde
 * Domain Path:       /languages/
 */

// register_activation_hook( __FILE__, 'word_count_activate' );
// function word_count_activate() {}

// register_deactivation_hook( __FILE__, 'word_count_deactivate' );
// function word_count_deactivate() {}

add_filter( 'the_content', 'ptqc_add_qr_code', 11 );

function ptqc_add_qr_code( $contnet ) {
	if ( is_single() ) {
		$current_url       = urlencode( get_permalink() );
		$current_post_type = get_post_type();

		$excluded_post_types = apply_filters( 'pqrc_excluded_post_types', array() );
		if ( in_array( $current_post_type, $excluded_post_types ) ) {
			return $contnet;
		}

		$height    = get_option( 'ptqc_height' );
		$width     = get_option( 'ptqc_width' );
		$dimension = apply_filters( 'pqrc_qr_code_dimension', $height . 'x' . $width );

		$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=$dimension&data=$current_url";
		$qr_code     = "<p><img src='$qr_code_url' alt='QR Code' /></p>";
		$contnet    .= $qr_code;
	}

	return $contnet;
}

// add_filter( 'pqrc_excluded_post_types', 'ptqc_exclude_post_types' );.
function ptqc_exclude_post_types() {
	return array( 'page' );
}

// add_filter( 'pqrc_qr_code_dimension', 'ptqc_qr_code_dimension' );
function ptqc_qr_code_dimension() {
	return '300x300';
}

/**
 * Adding settings section for the plugin
 */
add_action( 'admin_init', 'ptqc_settings_init' );

/**
 * Function to initialize the settings with admin_init hook.
 *
 * @return void
 */
function ptqc_settings_init() {
	// Add settings sections.
	add_settings_section( 'ptqc_section', __( 'QR Code Settings', 'post-to-qrcde' ), 'ptqc_section_callback', 'general' );

	// Add settings field.
	add_settings_field( 'ptqc_height', __( 'QR Code Height', 'post-to-qrcde' ), 'ptqc_display_field', 'general', 'ptqc_section', array( 'ptqc_height' ) );
	add_settings_field( 'ptqc_width', __( 'QR Code Width', 'post-to-qrcde' ), 'ptqc_display_field', 'general', 'ptqc_section', array( 'ptqc_width' ) );
	add_settings_field( 'ptqc_extra', __( 'Extra', 'post-to-qrcde' ), 'ptqc_display_field', 'general', 'ptqc_section', array( 'ptqc_extra' ) );

	// register the settings to get the value from the options table.
	register_setting( 'general', 'ptqc_height', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'ptqc_width', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'ptqc_extra', array( 'sanitize_callback' => 'esc_attr' ) );
}

/**
 * Function to show a section on a callback.
 */
function ptqc_section_callback() {
	echo '<p>' . __( 'Custom settings for QR Code', 'post-to-qrcde' ) . '</p>';
}

/**
 * Single callback for all the settings one. This function will be called automatically
 * and the data will render naturally every time this data is called.
 */
function ptqc_display_field( $args ) {
	$option = get_option( $args[0] ); // $args[0] is for the first argument, since the function add_settings_field passed only one argument, the first one will appear here.
	printf( '<input type="text" id="%s" name="%s" value="%s" />', $args[0], $args[0], $option );
}

/**
 * Function to display the height field form.
 *
 * @return void
 */
function ptqc_height_callback() {
	$height = get_option( 'ptqc_height' );
	printf( '<input type="text" id="ptqc_height" name="ptqc_height" value="%s" />', $height );
}

/**
 * Function to display the width field form.
 *
 * @return void
 */
function ptqc_width_callback() {
	$width = get_option( 'ptqc_width' );
	printf( '<input type="text" id="ptqc_width" name="ptqc_width" value="%s" />', $width );
}
