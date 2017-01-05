<?php
/**
 * Plugin Name:     Mail Test
 * Plugin URI:      https://github.com/trepmal/mail-test
 * Description:     Test wp_mail from Tools
 * Author:          Kailey Lampert
 * Author URI:      trepmal.com
 * Text Domain:     mail-test
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Mail_Test
 */


namespace trepmal\Mail_Test;

add_action( 'admin_menu', __NAMESPACE__ . '\menu' );
add_action( 'wp_ajax_' . esc_js( __NAMESPACE__ . '_send' ), __NAMESPACE__ . '\send_callback' );

/**
 * Setup admin menu
 */
function menu() {
	add_management_page( __( 'Test wp_mail', 'some-plugin' ), __( 'Test wp_mail', 'some-plugin' ), 'manage_options', __CLASS__, __NAMESPACE__ . '\page' );
}

/**
 * Setup admin page
 */
function page() {
	wp_enqueue_script( 'mail-test', plugins_url( 'mail-test.js', __FILE__ ), array( 'jquery', 'wp-util' ), '0.1.0' );
	wp_localize_script( 'mail-test', 'mailTest', array(
		'action'     => esc_js( __NAMESPACE__ . '_send' ),
		'nonce'      => wp_create_nonce( 'mail-test' ),
		'loadingGif' => admin_url( 'images/wpspin_light.gif' ),
	) );
	?><div class="wrap">
	<h2><?php esc_html_e( 'Test wp_mail()', 'some-plugin' ); ?></h2>
	<p>
		<?php $email = wp_get_current_user()->user_email; ?>
		<input type="email" id="send-test-email" value="<?php echo esc_attr( $email ); ?>" />
	</p>
	<?php submit_button( __( 'Send', 'some-plugin' ), 'primary', 'send-test' ); ?>
	<p id="send-test-result"></p>
	</div><?php
}

/**
 * Ajax callback.
 *
 */
function send_callback() {

	check_ajax_referer( 'mail-test', 'nonce', false );

	$to = $_POST['email'];

	if ( ! is_email( $to ) ) {
		wp_send_json_error( array(
			'message' => __( 'Invalid email address', 'some-plugin' )
		) );
	}

	$subject = apply_filters( 'mail_test_subject', __( 'WordPress Mail Test', 'some-plugin' ) );
	$body    = apply_filters( 'mail_test_body', __( 'Lorem ipsum', 'some-plugin' ) );

	$return  = wp_mail( $to, $subject, $body );

	if ( $return ) {
		wp_send_json_success( array(
			'message' => __( 'WordPress believes this email was sent successfully.', 'some-plugin' ),
			'data'    => $return,
		) );
	} else {
		wp_send_json_error( array(
			'message' => __( 'Failed to send.', 'some-plugin' ),
			'data'    => $return
		) );
	}

}