<?php
/*
Plugin Name: TMA Signature
Plugin URI: https://thorstenmarx.com/wordpress/plugins/tma-signature
Description: Adds a signature under every post
Version: 1.0.0
Author: Thorsten Marx
Author URI: https://thorstenmarx.com
Text Domain: tma-signature
*/

require_once "settings.php";
require_once "user.settings.php";

function tma_signature_add_post_content($content) {
    if(!is_feed() && is_singular('post')) { //  && !is_home()
        if( get_user_option('tma_signature') &&  get_user_option('tma_signature') !== "" ) {
            $content .= get_user_option('tma_signature');
        } else {
            $options = get_option( 'tma_signature_options' );
            if ($options !== FALSE && isset($options['signature'])) {
                $content .= $options['signature'];
            }
        }
	}
	return $content;
}

$priority = 10;
$options = get_option( 'tma_signature_options' );
if ($options !== FALSE && isset($options['priority'])) {
    $priority = $options['priority'];
}
add_filter('the_content', 'tma_signature_add_post_content', $priority);


