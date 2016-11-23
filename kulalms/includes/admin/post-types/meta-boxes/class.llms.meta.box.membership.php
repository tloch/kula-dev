<?php
/**
 * Membership Settings Metabox
 * @since   1.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Meta_Box_Membership extends LLMS_Admin_Metabox {

	/**
	 * This function allows extending classes to configure required class properties
	 * $this->id, $this->title, and $this->screens should be configured in this function
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public function configure() {

		$this->id = 'lifterlms-membership';
		$this->title = __( 'Membership Settings', 'lifterlms' );
		$this->screens = array(
			'llms_membership',
		);
		$this->priority = 'high';

	}

	/**
	 * Get array of data to pass to the auto enrollment courses table
	 * @param    obj     $membership  instance of LLMS_Membership for the current post
	 * @return   array
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	private function get_content_table( $membership ) {

		$data = array();

		foreach ( $membership->get_auto_enroll_courses() as $course_id ) {

			$course = new LLMS_Course( $course_id );

			$title = $course->get( 'title' );

			$data[ $title ] = array(

				'<a href="' . get_edit_post_link( $course->get( 'id' ) ) . '">' . $title . ' (ID#' . $course_id .')</a>',
				'<a class="llms-action-icon danger llms-remove-course" data-id="' . $course_id . '" href="#llms-course-remove"><span class="tooltip" title="' . __( 'Remove from Auto-enrollment', 'lifterlms' ) . '"><span class="dashicons dashicons-no"></span></span></a>',

			);

		}

		ksort( $data );

		return apply_filters( 'llms_membership_get_content_table_data', $data, $membership );

	}

	/**
	 * This function is where extending classes can configure all the fields within the metabox
	 * The function must return an array which can be consumed by the "output" function
	 *
	 * @return array
	 */
	public function get_fields() {

		$membership = new LLMS_Membership( $this->post );

		$redirect_options = array();
		$redirect_page_id = $membership->get( 'redirect_page_id' );
		if ( $redirect_page_id ) {
			$redirect_options[] = array(
				'key' => $redirect_page_id,
				'title' => get_the_title( $redirect_page_id ) . '(ID#' . $redirect_page_id . ')',
			);
		}

		return array(
			array(
				'title' 	=> __( 'Description', 'lifterlms' ),
				'fields' 	=> array(
					array(
						'type'		=> 'post-content',
						'label'		=> __( 'Members Description', 'lifterlms' ),
						'desc' 		=> __( 'If the Non-Members area below is left blank, this content will be displayed to all visitors, otherwise this content will only be displayed to active members.', 'lifterlms' ),
						'id' 		=> '',
						'class' 	=> '',
						'value' 	=> '',
						'desc_class' => '',
						'group' 	=> '',
					),
					array(
						'type'		=> 'post-excerpt',
						'label'		=> __( 'Non-Members Description', 'lifterlms' ),
						'desc' 		=> __( 'This content will only be shown to vistors who do not have access to this membership.', 'lifterlms' ),
						'id' 		=> '',
						'class' 	=> '',
						'value' 	=> '',
						'desc_class' => '',
						'group' 	=> '',
					),
				),
			),

			array(
				'title' 	=> __( 'Restrictions', 'lifterlms' ),
				'fields' 	=> array(
					array(
						'allow_null' => false,
						'class' 	=> '',
						'desc' 		=> __( 'When a non-member attempts to access content restricted to this membership', 'lifterlms' ),
						'id' 		=> $this->prefix . 'restriction_redirect_type',
						'is_controller' => true,
						'type'		=> 'select',
						'label'		=> __( 'Restricted Access Redirect', 'lifterlms' ),
						'value'   => array(
							array(
								'key' => 'none',
								'title' => __( 'Stay on page', 'lifterlms' ),
							),
							array(
								'key' => 'membership',
								'title' => __( 'Redirect to this membership page', 'lifterlms' ),
							),
							array(
								'key' => 'page',
								'title' => __( 'Redirect to a WordPress page', 'lifterlms' ),
							),
							array(
								'key' => 'custom',
								'title' => __( 'Redirect to a Custom URL', 'lifterlms' ),
							),
						),
					),
					array(
						'class' 	=> '',
						'controller' => '#' . $this->prefix . 'restriction_redirect_type',
						'controller_value' => 'page',
						'data_attributes' => array(
							'post-type' => 'page',
						),
						'id' 		=> $this->prefix . 'redirect_page_id',
						'label'		=> __( 'Select a WordPress Page', 'lifterlms' ),
						'type'		=> 'select',
						'class'     => 'llms-select2-post',
						'value'   => $redirect_options,
					),
					array(
						'class' 	=> '',
						'controller' => '#' . $this->prefix . 'restriction_redirect_type',
						'controller_value' => 'custom',
						'id' 		=> $this->prefix . 'redirect_custom_url',
						'label'		=> __( 'Enter a Custom URL', 'lifterlms' ),
						'type'		=> 'text',
						'value'   => 'test',
					),
					array(
						'class' 	=> '',
						'controls' => '#' . $this->prefix . 'restriction_notice',
						'default'   => 'yes',
						'desc' 		=> __( 'Check this box to output a message after redirecting. If no redirect is selected this message will replace the normal content that would be displayed.', 'lifterlms' ),
						'desc_class' => 'd-3of4 t-3of4 m-1of2',
						'id' 		=> $this->prefix . 'restriction_add_notice',
						'label'		=> __( 'Display a Message', 'lifterlms' ),
						'type'		=> 'checkbox',
						'value'   => 'yes',
					),
					array(
						'class' 	=> 'full-width',
						'desc' 		=> sprintf( __( 'Shortcodes like %s can be used in this message', 'lifterlms' ), '[lifterlms_membership_link id="' . $this->post->ID . '"]' ),
						'default'   => sprintf( __( 'You must belong to the %s membership to access this content.', 'lifterlms' ), '[lifterlms_membership_link id="' . $this->post->ID . '"]' ),
						'id' 		=> $this->prefix . 'restriction_notice',
						'label'		=> __( 'Restricted Content Notice', 'lifterlms' ),
						'type'		=> 'text',
					),
				),
			),

			array(
				'title' 	=> __( 'Auto Enrollment', 'lifterlms' ),
				'fields' 	=> array(
					array(
						'label' 	=> __( 'Auto Enrollment', 'lifterlms' ),
						'desc' 		=> __( 'When a student joins this membership they will be automatically enrolled in these courses', 'lifterlms' ),
						'id' 		=> $this->prefix . 'content_table',
						'titles'	=> array( __( 'Course Name', 'lifterlms' ), '' ),
						'empty_message' => __( 'No auto-enrollment couses found.', 'lifterlms' ),
						'type'  	=> 'table',
						'table_data' => $this->get_content_table( $membership ),
						'group' 	=> '',
						'class' 	=> '',
					),
					array(
						'class'     => 'llms-select2-post',
						'data_attributes' => array(
							'placeholder' => __( 'Select course(s)', 'lifterlms' ),
							'post-type' => 'course',
						),
						'desc' 		=> __( 'When a member is enrolled in this membership they will be automatically enrolled into any courses in the auto-enrollment list', 'lifterlms' ),
						'id' 		=> $this->prefix . 'auto_enroll',
						'label'		=> __( 'Add Auto-enrollment Course(s)', 'lifterlms' ),
						'multi'  => true,
						'type'		=> 'select',
						'value'     => array(),
					),
				),
			),
		);
	}

	/**
	 * Save field data
	 * Called by $this->save_actions()
	 * @param  int   $post_id   WP Post ID of the post being saved
	 * @return void
	 * @since  3.0.0
	 */
	public function save( $post_id ) {

		$membership = new LLMS_Membership( $post_id );

		if ( ! isset( $_POST[ $this->prefix . 'restriction_add_notice' ] ) ) {
			$_POST[ $this->prefix . 'restriction_add_notice' ] = 'no';
		}

		// save all the fields
		$fields = array(
			'restriction_redirect_type',
			'redirect_page_id',
			'redirect_custom_url',
			'restriction_add_notice',
			'restriction_notice',
		);
		foreach ( $fields as $field ) {

			if ( isset( $_POST[ $this->prefix . $field ] ) ) {

				$membership->set( $field, $_POST[ $this->prefix . $field ] );

			}

		}

		// add new autoenroll courses
		if ( isset( $_POST[ $this->prefix . 'auto_enroll' ] ) && is_array( $_POST[ $this->prefix . 'auto_enroll' ] ) ) {

			$membership->add_auto_enroll_courses( $_POST[ $this->prefix . 'auto_enroll' ] );

		}

	}

}
