<?php
/**
* Register WordPress AJAX methods for Analytics Widgets
*
* @since  3.0.0
* @version 3.0.0
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Analytics_Widget_Ajax {

	public function __construct() {

		// only proceed if we're doing ajax
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			return;

		}

		$methods = array(

			// sales
			'coupons',
			'discounts',
			'refunded',
			'refunds',
			'revenue',
			'sales',
			'sold',

			// enrollments
			'enrollments',

		);

		// include the abstract
		include LLMS_PLUGIN_DIR . 'includes/abstracts/abstract.llms.analytics.widget.php';

		/**
		 * @todo  is there a way to only register the function being called during this request?
		 */
		foreach ( $methods as $method ) {

			$file = LLMS_PLUGIN_DIR . 'includes/admin/analytics/widgets/class.llms.analytics.widget.' . $method . '.php';

			if ( file_exists( $file ) ) {

				include $file;
				$class = 'LLMS_Analytics_' . ucwords( $method ) . '_Widget';
				add_action( 'wp_ajax_llms_widget_' . $method, array( new $class, 'output' ) );

			}

		}

	}

}

return new LLMS_Analytics_Widget_Ajax();
