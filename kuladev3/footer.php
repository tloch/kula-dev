<footer>
    <div class="container">
        <div class="row">
            <div class="footertop">
                <?php 
                            if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar('topfootersidebar') ) : ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="footerbottom">
                <?php 
                    if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar('bottomfootersidebar') ) : ?>
                <?php endif; ?>
            </div>
        </div>
    </div> 
    <div id="scrolltop">
        <a><i class="icon-arrow-1-up"></i><span><?php _e('top','vibe'); ?></span></a>
    </div>
</footer>
<div id="footerbottom">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h2 id="footerlogo"><a><img src="<?php $logo=vibe_get_option('logo'); echo (isset($logo)?$logo:VIBE_URL.'/images/logo.png'); ?>"></a></h2>
                <?php $copyright=vibe_get_option('copyright'); echo (isset($copyright)?$copyright:'&copy; 2013, All rights reserved.'); ?>
            </div>
            <div class="col-md-9">
                <div id="footermenu">
                    <?php
                            $args = array(
                                'theme_location'  => 'footer-menu',
                                'container'       => '',
                                'menu_class'      => 'footermenu',
                                'fallback_cb'     => 'vibe_set_menu',
                            );
                            wp_nav_menu( $args );
                    ?>
                </div>    
            </div>
        </div>
    </div>
</div>
</div><!-- END PUSHER -->
</div><!-- END MAIN -->
	<!-- SCRIPTS -->
<?php
wp_footer();
?>    
<?php
echo vibe_get_option('google_analytics');
?>
</body>
</html>