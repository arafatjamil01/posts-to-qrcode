<?php
/**
 * Plugin Name:       Posts to QR Code plugin
 * Plugin URI:        https://github.com/arafatjamil01/posts-to-qrcode
 * Description:       Add a QR Code at the end of the post.
 * Version:           1.0
 * Author:            Arafat Jamil
 * Author URI:        https://github.com/arafatjamil01
 * License:           GPL v2 or later
 * Text Domain:       post-to-qrcode
 * Domain Path:       /languages/
 */

// register_activation_hook( __FILE__, 'word_count_activate' );
// function word_count_activate() {}

// register_deactivation_hook( __FILE__, 'word_count_deactivate' );
// function word_count_deactivate() {}


$pqrc_countries = array(
	__( 'Afghanistan', 'post-to-qrcode' ),
	__( 'Bangladesh', 'post-to-qrcode' ),
	__( 'Bhutan', 'post-to-qrcode' ),
	__( 'India', 'post-to-qrcode' ),
	__( 'Iran', 'post-to-qrcode' ),
	__( 'Maldives', 'post-to-qrcode' ),
	__( 'Nepal', 'post-to-qrcode' ),
	__( 'Pakistan', 'post-to-qrcode' ),
	__( 'Sri Lanka', 'post-to-qrcode' ),

);

add_action( 'init', 'pqrc_init' );

function pqrc_init() {
	global $pqrc_countries;
	$pqrc_countries = apply_filters( 'pqrc_countries', $pqrc_countries );
}

add_filter( 'the_content', 'pqrc_add_qr_code', 11 );

function pqrc_add_qr_code( $contnet ) {
	if ( is_single() ) {
		$current_url       = urlencode( get_permalink() );
		$current_post_type = get_post_type();

		$excluded_post_types = apply_filters( 'pqrc_excluded_post_types', array() );
		if ( in_array( $current_post_type, $excluded_post_types ) ) {
			return $contnet;
		}

		$height    = get_option( 'pqrc_height' );
		$width     = get_option( 'pqrc_width' );
		$dimension = apply_filters( 'pqrc_qr_code_dimension', $height . 'x' . $width );

		$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=$dimension&data=$current_url";
		$qr_code     = "<p><img src='$qr_code_url' alt='QR Code' /></p>";
		$contnet    .= $qr_code;
	}

	return $contnet;
}

// add_filter( 'pqrc_excluded_post_types', 'pqrc_exclude_post_types' );.
function pqrc_exclude_post_types() {
	return array( 'page' );
}

// add_filter( 'pqrc_qr_code_dimension', 'pqrc_qr_code_dimension' );
function pqrc_qr_code_dimension() {
	return '300x300';
}

/**
 * Adding settings section for the plugin
 */
add_action( 'admin_init', 'pqrc_settings_init' );

/**
 * Function to initialize the settings with admin_init hook.
 *
 * @return void
 */
function pqrc_settings_init() {
	// Add settings sections.
	add_settings_section( 'pqrc_section', __( 'QR Code Settings', 'post-to-qrcode' ), 'pqrc_section_callback', 'general' );

	// Add settings field.
	add_settings_field( 'pqrc_height', __( 'QR Code Height', 'post-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_height' ) );
	add_settings_field( 'pqrc_width', __( 'QR Code Width', 'post-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_width' ) );
	add_settings_field( 'pqrc_extra', __( 'Extra', 'post-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_extra' ) );
	add_settings_field( 'pqrc_select', __( 'Dropdown', 'post-to-qrcode' ), 'pqrc_display_select_field', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_checkbox', __( 'Select Countries', 'post-to-qrcode' ), 'pqrc_display_checkboxgroup_field', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_radio', __( 'Do you opt in?', 'post-to-qrcode' ), 'pqrc_display_radio_field', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_toggle', __( 'Toggle Field', 'post-to-qrcode' ), 'pqrc_display_toggle_field', 'general', 'pqrc_section' );

	// register the settings to get the value from the options table.
	register_setting( 'general', 'pqrc_height', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_width', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_extra', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_select', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_checkbox' );
	register_setting( 'general', 'pqrc_radio', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_toggle', array( 'sanitize_callback' => 'esc_attr' ) );
}

/**
 * Function to show a section on a callback.
 */
function pqrc_section_callback() {
	echo '<p>' . __( 'Custom settings for QR Code', 'post-to-qrcode' ) . '</p>';
}

/**
 * Single callback for all the settings one. This function will be called automatically
 * and the data will render naturally every time this data is called.
 */
function pqrc_display_field( $args ) {
	$option = get_option( $args[0] ); // $args[0] is for the first argument, since the function add_settings_field passed only one argument, the first one will appear here.
	printf( '<input type="text" id="%s" name="%s" value="%s" />', $args[0], $args[0], $option );
}

/**
 * Function to display the height field form.
 *
 * @return void
 */
function pqrc_height_callback() {
	$height = get_option( 'pqrc_height' );
	printf( '<input type="text" id="pqrc_height" name="pqrc_height" value="%s" />', $height );
}

/**
 * Function to display the width field form.
 *
 * @return void
 */
function pqrc_width_callback() {
	$width = get_option( 'pqrc_width' );
	printf( '<input type="text" id="pqrc_width" name="pqrc_width" value="%s" />', $width );
}

/**
 * Show the dropdown to choose from the SAARC countries.
 *
 * @return void
 */
function pqrc_display_select_field() {
	$option = get_option( 'pqrc_select' );
	global $pqrc_countries;

	printf( '<select id="%s" name="%s">', 'pqrc_select', 'pqrc_select' );

	foreach ( $pqrc_countries as $country ) {
		$selected = ( $option == $country ) ? 'selected' : '';
		printf( '<option value="%s" %s>%s</option>', $country, $selected, $country );
	}

	echo '</select>';
}


/**
 * Show the dropdown to choose from the SAARC countries.
 *
 * @return void
 */
function pqrc_display_checkboxgroup_field() {
	$option = get_option( 'pqrc_checkbox' );

	global $pqrc_countries;

	foreach ( $pqrc_countries as $country ) {
		$checked = '';
		if ( is_array( $option ) && in_array( $country, $option ) ) {
			$checked = 'checked';
		}

		$selected = ( $option == $country ) ? 'selected' : '';
		printf( '<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s>%s</input><br/>', $country, $checked, $country );
	}

	echo '</select>';
}

/**
 * Show the radio button to choose from the options.
 */
function pqrc_display_radio_field() {
	$option = get_option( 'pqrc_radio' );

	$radio_options = array(
		'Yes',
		'No',
		'Maybe',
	);

	foreach ( $radio_options as $radio_option ) {
		$checked = '';
		if ( $option == $radio_option ) {
			$checked = 'checked';
		}

		printf( '<input type="radio" name="pqrc_radio" value="%s" %s>%s</input><br/>', $radio_option, $checked, $radio_option );
	}
}

// Testing the filter 'pqrc_countries'
add_filter(
	'pqrc_countries',
	function ( $countries ) {
		return array_diff( $countries, array( 'Maldives', 'India' ) );
	}
);

/**
 * Show toggle field.
 *
 * @return void
 */
function pqrc_display_toggle_field() {
	$option = get_option( 'pqrc_toggle' );
	?>
	<div class="toggle">
	<div class="minitoggle">
		<div class="toggle-handle"></div>
	</div>
	</div>
	<?php
	echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='" . $option . "'/>";
}


/**
 * Enqueue scripts in admin, with proper screen.
 */
add_action( 'admin_enqueue_scripts', 'pqrc_enqueue_scripts' );

function pqrc_enqueue_scripts( $hook ) {
	if ( 'options-general.php' == $hook ) {
		wp_enqueue_script( 'mini-toggle', plugin_dir_url( __FILE__ ) . 'assets/js/minitoggle.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'pqrc-admin', plugin_dir_url( __FILE__ ) . 'assets/js/pqrc-main.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'mini-toggle', plugin_dir_url( __FILE__ ) . 'assets/css/minitoggle.css', array(), '1.0', 'all' );
	}
}
