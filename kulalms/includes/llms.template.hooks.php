<?php
/**
* Template Add Actions
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }


/***********************************************************************
 *
 * Main Content Wrappers
 *
 ***********************************************************************/
add_action( 'lifterlms_before_main_content', 'lifterlms_output_content_wrapper', 10 );
add_action( 'lifterlms_after_main_content', 'lifterlms_output_content_wrapper_end', 10 );


/***********************************************************************
 *
 * Single Course
 *
 ***********************************************************************/
add_action( 'lifterlms_single_course_before_summary', 'lifterlms_template_single_featured_image', 10 );
add_action( 'lifterlms_single_course_before_summary', 'lifterlms_template_single_video',          20 );
add_action( 'lifterlms_single_course_before_summary', 'lifterlms_template_single_audio',          30 );

add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_meta_wrapper_start', 5 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_length',             10 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_difficulty',         20 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_course_tracks',      25 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_course_categories',  30 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_course_tags',        35 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_course_author',             40 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_meta_wrapper_end',   50 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_prerequisites',      55 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_pricing_table',             60 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_course_progress',    60 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_syllabus',           90 );
add_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_single_reviews',            100 );





/***********************************************************************
 *
 * Single Lesson
 *
 ***********************************************************************/
add_action( 'lifterlms_single_lesson_before_summary', 'lifterlms_template_single_parent_course', 10 );
add_action( 'lifterlms_single_lesson_before_summary', 'lifterlms_template_single_lesson_video',  20 );
add_action( 'lifterlms_single_lesson_before_summary', 'lifterlms_template_single_lesson_audio',  20 );

add_action( 'lifterlms_single_lesson_after_summary', 'lifterlms_template_complete_lesson_link',  10 );
add_action( 'lifterlms_single_lesson_after_summary', 'lifterlms_template_lesson_navigation',     20 );






/***********************************************************************
 *
 * Course & Membership Loops
 *
 ***********************************************************************/
add_action( 'lifterlms_before_loop', 'lifterlms_loop_start', 10 );
add_action( 'lifterlms_after_loop', 'lifterlms_loop_end', 10 );


/***********************************************************************
 *
 * Course & Membership Loop Items
 *
 ***********************************************************************/
add_action( 'lifterlms_before_loop_item', 'lifterlms_loop_link_start', 10 );

add_action( 'lifterlms_before_loop_item_title', 'lifterlms_template_loop_thumbnail', 10 );
add_action( 'lifterlms_before_loop_item_title', 'lifterlms_template_loop_progress', 15 );

add_action( 'lifterlms_after_loop_item_title', 'lifterlms_template_loop_author', 10 );
add_action( 'lifterlms_after_loop_item_title', 'lifterlms_template_loop_length', 15 );
add_action( 'lifterlms_after_loop_item_title', 'lifterlms_template_loop_difficulty', 20 );


add_action( 'lifterlms_after_loop_item', 'lifterlms_loop_link_end', 5 );














//After Course Archive Loop Item Title
add_action( 'lifterlms_after_shop_loop_item_title', 'lifterlms_template_loop_price', 10 );

add_action( 'lifterlms_after_shop_loop_item_title', 'lifterlms_template_loop_view_link', 10 );







/**
 * MEMBERSHIP
 */
//Before Membership Archive Loop Item Title
add_action( 'lifterlms_before_memberships_loop_item_title', 'lifterlms_template_loop_course_thumbnail', 10 );
add_action( 'lifterlms_after_memberships_loop_item_title', 'lifterlms_template_loop_price', 10 );
add_action( 'lifterlms_after_memberships_loop_item_title', 'lifterlms_template_loop_view_link', 10 );

//Before Membership Summary
add_action( 'lifterlms_single_membership_before_summary', 'lifterlms_template_single_featured_image', 10 );
add_action( 'lifterlms_single_membership_after_summary', 'lifterlms_template_pricing_table', 10 );

//After Membership Summary

/**
 * QUIZ
 */
//before Quiz Summary
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_timer', 5 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_wrapper_start', 5 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_return_link', 10 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_results', 15 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_summary', 20 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_passing_percent', 25 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_attempts', 30 );
add_action( 'lifterlms_single_quiz_before_summary', 'lifterlms_template_quiz_time_limit', 35 );

//After Quiz Summary
add_action( 'lifterlms_single_quiz_after_summary', 'lifterlms_template_quiz_wrapper_end', 5 );
add_action( 'lifterlms_single_quiz_after_summary', 'lifterlms_template_start_button', 10 );
add_action( 'lifterlms_single_quiz_after_summary', 'lifterlms_template_quiz_question', 15 );



//Before Question Summary
add_action( 'lifterlms_single_question_before_summary', 'lifterlms_template_question_wrapper_start', 10 );
add_action( 'lifterlms_single_question_before_summary', 'lifterlms_template_single_question_count', 10 );

//After Question Summary
add_action( 'lifterlms_single_question_after_summary', 'lifterlms_template_single_single_choice_ajax', 10 );
add_action( 'lifterlms_single_question_after_summary', 'lifterlms_template_single_prev_question', 10 );
add_action( 'lifterlms_single_question_after_summary', 'lifterlms_template_single_next_question', 10 );
add_action( 'lifterlms_single_question_after_summary', 'lifterlms_template_question_wrapper_end', 10 );


/**
 * Student Dashboard
 */
add_action( 'lifterlms_before_student_dashboard', 'lifterlms_template_student_dashboard_wrapper_open', 10 );

add_action( 'lifterlms_before_student_dashboard_content', 'lifterlms_template_student_dashboard_navigation', 10 );
add_action( 'lifterlms_before_student_dashboard_content', 'lifterlms_template_student_dashboard_title', 20 );

add_action( 'lifterlms_after_student_dashboard', 'lifterlms_template_student_dashboard_wrapper_close', 10 );


add_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar' );






if ( ! is_admin() ) {
	add_filter( 'post_class', 'llms_post_classes', 20, 3 );
}
