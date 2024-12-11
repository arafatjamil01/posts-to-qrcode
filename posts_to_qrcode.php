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

//add_filter( 'pqrc_excluded_post_types', 'ptqc_exclude_post_types' );
function ptqc_exclude_post_types() {
	return array( 'page' );
}

//add_filter( 'pqrc_qr_code_dimension', 'ptqc_qr_code_dimension' );
function ptqc_qr_code_dimension() {
	return '300x300';
}

/**
 * Adding settings section for the plugin
 */
add_action( 'admin_init', 'ptqc_settings_init' );

function ptqc_settings_init() {
	add_settings_field( 'ptqc_height', __( 'QR Code Height', 'post-to-qrcde' ), 'ptqc_height_callback', 'general' );
	add_settings_field( 'ptqc_width', __( 'QR Code Width', 'post-to-qrcde' ), 'ptqc_width_callback', 'general' );

	register_setting( 'general', 'ptqc_height', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'ptqc_width', array( 'sanitize_callback' => 'esc_attr' ) );
}

function ptqc_height_callback() {
	$height = get_option( 'ptqc_height' );
	printf( '<input type="text" id="ptqc_height" name="ptqc_height" value="%s" />', $height );
}

function ptqc_width_callback() {
	$width = get_option( 'ptqc_width' );
	printf( '<input type="text" id="ptqc_width" name="ptqc_width" value="%s" />', $width );
}
