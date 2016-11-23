<?php
/**
* Template loader class
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Template_Loader {

	/**
	* Constructor
	*
	* @since   1.0.0
	* @version 3.1.6
	*/
	public function __construct() {

		// do template loading
		add_filter( 'template_include', array( $this, 'template_loader' ) );

		// restriction actions for each kind of restriction
		$reasons = apply_filters( 'llms_restriction_reasons', array(
			'course_time_period',
			'enrollment_lesson',
			'lesson_drip',
			'lesson_prerequisite',
			'membership',
			'sitewide_membership',
			'quiz',
		) );

		foreach ( $reasons as $reason ) {
			add_action( 'llms_content_restricted_by_' . $reason, array( $this, 'restricted_by_' . $reason ), 10, 1 );
		}

	}

	/**
	 * Add a notice and / or redirect during restriction actions
	 * @param    string    $msg       notice message to display
	 * @param    string    $redirect  optional url to redirect to after setting a notice
	 * @param    string    $msg_type  type of message to display [notice|success|error|debug]
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	private function handle_restriction( $msg = '', $redirect = '', $msg_type = 'notice' ) {

		if ( $msg ) {
			llms_add_notice( do_shortcode( $msg ), $msg_type );
		}

		if ( $redirect ) {
			wp_redirect( $redirect );
			exit;
		}

	}

	/**
	 * Handle redirects and messages when a course or associated quiz or lesson has time period
	 * date restrictions placed upon it
	 *
	 * Quizzes & Lessons redirect to the parent course
	 *
	 * Courses display a notice until the course opens and an error once the course closes
	 *
	 * @param    array     $info  array of restriction info from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_course_time_period( $info ) {

		$post_type = get_post_type( $info['content_id'] );

		// if this restriction occurs when attempting to view a lesson
		// redirect the user to the course, course restriction will handle display of the
		// message once we get there
		// this prevents duplicate messages from being displayed
		if ( 'lesson' === $post_type || 'llms_quiz' === $post_type ) {
			$msg = '';
			$redirect = get_permalink( $info['restriction_id'] );
		}

		if ( ! $msg && ! $redirect ) {
			return;
		}

		// handle the restriction action & allow developers to filter the results
		$this->handle_restriction(
			apply_filters( 'llms_restricted_by_course_time_period_message', $msg, $info ),
			apply_filters( 'llms_restricted_by_course_time_period_redirect', $redirect, $info ),
			'notice'
		);

	}

	/**
	 * Handle redirects and messages when a user attempts to access a lesson
	 * for a course they're not enrolled in
	 *
	 * redirect to parent course and display message
	 *
	 * @param    array     $info  array of restriction info from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_enrollment_lesson( $info ) {

		$course = new LLMS_Course( $info['restriction_id'] );

		$msg = $course->get( 'content_restricted_message' );
		$redirect = get_permalink( $course->get( 'id' ) );

		$this->handle_restriction(
			apply_filters( 'llms_restricted_by_enrollment_lesson_message', $msg, $info ),
			apply_filters( 'llms_restricted_by_enrollment_lesson_redirect', $redirect, $info ),
			'error'
		);

	}

	/**
	 * Handle redirects and messages when a user attempts to access a lesson
	 * for that is restricted by lesson drip settings
	 *
	 * redirect to parent course and display message
	 *
	 * @param    array     $info  array of restriction info from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_lesson_drip( $info ) {

		$lesson = new LLMS_Lesson( $info['restriction_id'] );

		$msg = sprintf( _x( 'The lesson "%s" will be available on %s', 'lesson restricted by drip settings message', 'lifterlms' ), $lesson->get( 'title' ), $lesson->get_available_date() );
		$redirect = get_permalink( $lesson->get_parent_course() );

		$this->handle_restriction(
			apply_filters( 'llms_restricted_by_lesson_drip_message', $msg, $info ),
			apply_filters( 'llms_restricted_by_lesson_drip_redirect', $redirect, $info ),
			'error'
		);

	}

	/**
	 * Handle redirects and messages when a user attempts to access a lesson
	 * for that is restricted by prerequisite lesson
	 *
	 * redirect to parent course and display message
	 *
	 * @param    array     $info  array of restriction info from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_lesson_prerequisite( $info ) {

		$lesson = new LLMS_Lesson( $info['content_id'] );
		$prereq_lesson = new LLMS_Lesson( $info['restriction_id'] );

		$prereq_link = '<a href="' . get_permalink( $prereq_lesson->get( 'id' ) ) . '">' . $prereq_lesson->get( 'title' ) . '</a>';

		$msg = sprintf( _x( 'The lesson "%s" cannot be accessed until the required prerequisite "%s" is completed.', 'lesson restricted by prerequisite message', 'lifterlms' ), $lesson->get( 'title' ), $prereq_link );
		$redirect = get_permalink( $lesson->get_parent_course() );

		$this->handle_restriction(
			apply_filters( 'llms_restricted_by_lesson_prerequisite_message', $msg, $info ),
			apply_filters( 'llms_restricted_by_lesson_prerequisite_redirect', $redirect, $info ),
			'error'
		);

	}

	/**
	 * Handle content restricted to a membership
	 *
	 * Parses and obeys Membership "Restriction Behavior" settings
	 *
	 * @param    array     $info  array of restriction results from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_membership( $info ) {

		$membership_id = $info['restriction_id'];

		// do nothing if we don't have a membership id
		if ( ! empty( $membership_id ) && is_numeric( $membership_id ) ) {

			// instatiate the membership
			$membership = new LLMS_Membership( $membership_id );

			$msg = '';
			$redirect = '';

			// get the redirect based on the redirect type (if set)
			switch ( $membership->get( 'restriction_redirect_type' ) ) {

				case 'custom':
					$redirect = $membership->get( 'redirect_custom_url' );
				break;

				case 'membership':
					$redirect = get_permalink( $membership->get( 'id' ) );
				break;

				case 'page':
					$redirect = get_permalink( $membership->get( 'redirect_page_id' ) );
				break;

			}

			if ( 'yes' === $membership->get( 'restriction_add_notice' ) ) {

				$msg = $membership->get( 'restriction_notice' );

			}

			// handle the restriction action & allow developers to filter the results
			$this->handle_restriction(
				apply_filters( 'llms_restricted_by_membership_message', $msg, $info ),
				apply_filters( 'llms_restricted_by_membership_redirect', $redirect, $info )
			);

		}

	}

	/**
	 * Handle attempts to access quizzes
	 * @param    array     $info  array of restriction results from llms_page_restricted()
	 * @return   void
	 * @since    3.1.6
	 * @version  3.1.6
	 */
	public function restricted_by_quiz( $info ) {

		$msg = '';
		$redirect = '';

		if ( get_current_user_id() ) {

			// if the user can edit the post, they're probably a creator giving it a test
			if ( current_user_can( 'edit_post', $info['restriction_id'] ) ) {

				$msg = sprintf( __( 'It looks like you\'re trying to test a quiz you just made. To test your quiz please read our documentation at %s', 'lifterlms' ), '<a href="https://lifterlms.com/docs/i-cant-take-the-quiz-i-just-created/" target="_blank">https://lifterlms.com/docs/i-cant-take-the-quiz-i-just-created/</a>' );

			} else {

				$msg = __( 'You cannot access quizzes directly. Please return to the associated lesson and start the quiz from there.', 'lifterlms' );

			}

		} else {

			$msg = __( 'You must be logged in to take quizzes.', 'lifterlms' );
			$redirect = llms_person_my_courses_url();

		}

		$this->handle_restriction(
			apply_filters( 'llms_restricted_by_membership_message', $msg, $info ),
			apply_filters( 'llms_restricted_by_membership_redirect', $redirect, $info ),
			'error'
		);

	}

	/**
	 * Handle content restricted to a membership
	 *
	 * Parses and obeys Membership "Restriction Behavior" settings
	 *
	 * @param    array     $info  array of restriction results from llms_page_restricted()
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function restricted_by_sitewide_membership( $info ) {
		$this->restricted_by_membership( $info );
	}

	/**
	 * Check if content should be restricted and include overrides where appropriate
	 * triggers various actions based on content restrictions
	 *
	 * @param  string  $template
	 * @return string html
	 */
	public function template_loader( $template ) {

		$page_restricted = llms_page_restricted( get_the_ID() );

		$post_type = get_post_type();

		if ( $page_restricted['is_restricted'] ) {

			// generic content restricted action
			do_action( 'lifterlms_content_restricted', $page_restricted );

			// specific content restriction action
			do_action( 'llms_content_restricted_by_' . $page_restricted['reason'], $page_restricted );

			// the actual content of membership and courses is handled via separate wysiwyg areas
			// so for these post types we'll return the regular template
			if ( 'course' === $post_type || 'llms_membership' === $post_type ) {
				return $template;
			} // otherwise return the no-access template in case no redirects are specified by the specific restriction action
			else {
				$template = 'single-no-access.php';
			}

			// } elseif ( is_single() && get_post_type() == 'llms_membership' ) {

			// 	return $template;

			// } elseif ( is_single() && get_post_type() == 'course' ) {

			// 	return $template;

			// } elseif ( is_single() && get_post_type() == 'lesson' ) {

			// 	return $template;

		} elseif ( is_post_type_archive( 'course' ) || is_page( llms_get_page_id( 'llms_shop' ) ) ) {

			$template = 'archive-course.php';

		} elseif ( is_tax( array( 'course_cat', 'course_tag', 'course_difficulty', 'course_track', 'membership_tag', 'membership_cat' ) ) ) {

			global $wp_query;
			$obj = $wp_query->get_queried_object();
			$template = 'taxonomy-' . $obj->taxonomy . '.php';

		} elseif ( is_post_type_archive( 'llms_membership' ) || is_page( llms_get_page_id( 'memberships' ) ) ) {

			$template = 'archive-llms_membership.php';

		} elseif ( is_single() && ( get_post_type() == 'llms_certificate' || get_post_type() == 'llms_my_certificate' ) ) {

			$template = 'single-certificate.php';

		} else {

			return $template;

		}

		// check for an override file
		$override = llms_get_template_override( $template );
		$template_path = $override ? $override : LLMS()->plugin_path() . '/templates/';
		return $template_path . $template;

	}

}

new LLMS_Template_Loader();
