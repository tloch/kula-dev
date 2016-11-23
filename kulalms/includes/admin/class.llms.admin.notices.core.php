<?php
/**
 * Manage core admin notices
 *
 * @since 3.0.0
 * @version  3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Admin_Notices_Core {

	/**
	 * Costructor
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public static function init() {

		add_action( 'current_screen', array( __CLASS__, 'add_init_actions' ) );

	}

	/**
	 * Add actions on different hooks depending on the current screen
	 * Adds later for LLMS Settings screens to accommodate for settings that are updated later in the load cycle
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public static function add_init_actions() {

		$screen = get_current_screen();
		if ( ! empty( $screen->base ) && 'lifterlms_page_llms-settings' === $screen->base ) {
			$action = 'lifterlms_settings_notices';
			$priority = 5;
		} else {
			$action = 'admin_init';
			$priority = 10;
		}

		add_action( $action, array( __CLASS__, 'sidebar_support' ), $priority );
		add_action( $action, array( __CLASS__, 'gateways' ), $priority );
		add_action( $action, array( __CLASS__, 'check_staging' ), $priority );

	}

	/**
	 * Outputs a notice that allows users to enable or disable automated recurring payments
	 * appears when we identify that the url has changed or when an admin resets the settings
	 * from the button on the general settings tab
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public static function check_staging() {

		$id = 'maybe-staging';

		if ( isset( $_GET['llms-staging-status'] ) && isset( $_GET['_llms_staging_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_llms_staging_nonce'], 'llms_staging_status' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'lifterlms' ) );
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'lifterlms' ) );
			}

			if ( 'enable' === $_GET['llms-staging-status'] ) {
				LLMS_Site::set_lock_url();
				LLMS_Site::update_feature( 'recurring_payments', true );
			} elseif ( 'disable' === $_GET['llms-staging-status'] ) {
				LLMS_Site::clear_lock_url();
				LLMS_Site::update_feature( 'recurring_payments', false );
				update_option( 'llms_site_url_ignore', 'yes' );
			}

			LLMS_Admin_Notices::delete_notice( $id );

		}

		if ( ! LLMS_Site::is_clone_ignored() && ! LLMS_Admin_Notices::has_notice( $id ) && ( LLMS_Site::is_clone() ) ) {

			LLMS_Admin_Notices::add_notice( $id, array(
				'type' => 'info',
				'dismissible' => false,
				'remindable' => false,
				'template' => 'admin/notices/staging.php',
			) );

		}

	}

	/**
	 * Check for gateways and output gateway notice
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public static function gateways() {
		$id = 'no-gateways';
		if ( ! LLMS()->payment_gateways()->has_gateways( true ) ) {
			$html = __( 'No LifterLMS Payment Gateways are currently enabled. Students will only be able to enroll in courses or memberships with free access plans.', 'lifterlms' ) . '<br><br>';
			$html .= sprintf( __( 'For starters you can configure manual payments on the %sCheckout Settings tab%s. Be sure to check out all the available %sLifterLMS Payment Gateways%s and install one later so that you can start selling your courses and memberships.', 'lifterlms' ), '<a href="' . add_query_arg( array( 'page' => 'llms-settings', 'tab' => 'checkout' ), admin_url( 'admin.php' ) ) . '">', '</a>', '<a href="https://lifterlms.com/product-category/plugins/payment-gateways/" target="_blank">', '</a>' );
			LLMS_Admin_Notices::add_notice( $id, $html, array(
				'type' => 'warning',
				'dismiss_for_days' => 7,
				'remindable' => true,
			) );
		} elseif ( LLMS_Admin_Notices::has_notice( $id ) ) {
			LLMS_Admin_Notices::delete_notice( $id );
		}
	}


	/**
	 * Check theme support for LifterLMS Sidebars
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.2
	 */
	public static function sidebar_support() {

		$template = get_option( 'template' );
		$id = 'sidebars-' . $template;

		if ( ! current_theme_supports( 'lifterlms-sidebars' ) && ! in_array( $template, llms_get_core_supported_themes() ) ) {

			$msg = sprintf(
				__( '<strong>Your theme does not declare support for LifterLMS Sidebars.</strong> Please see our %sintegration guide%s or check out our %sLaunchPad%s theme which is designed specifically for use with LifterLMS', 'lifterlms' ),
				'<a href="https://lifterlms.com/docs/lifterlms-sidebar-support/?utm_source=notice&utm_medium=product&utm_content=sidebarsupport&utm_campaign=lifterlmsplugin" target="_blank">', '</a>',
				'<a href="https://lifterlms.com/product/launchpad/?utm_source=notice&utm_medium=product&utm_content=launchpad&utm_campaign=lifterlmsplugin" target="_blank">', '</a>'
			);

			LLMS_Admin_Notices::add_notice( $id, $msg, array(
				'dismissible' => true,
				'dismiss_for_days' => 730, // @todo there should be a "forever" setting
				'remindable' => false,
				'type' => 'warning',
			) );

		} elseif ( LLMS_Admin_Notices::has_notice( $id ) ) {

			LLMS_Admin_Notices::delete_notice( $id );

		}

	}

}

LLMS_Admin_Notices_Core::init();
