<?php
/**
 * Plugin Name: Nybnewsletter
 * Plugin URI: http://nybblemouse.com
 * Description: Nybnewsletter implements a simple database to record contacts subscribed via the signup form
 * Version: 1.0.0
 * Author: Christopher McKirgan
 * Author URI: http://mckirgan.com
 * License: GPLv2 or later
 */

/**
 * Adds a view to the post being viewed
 *
 * Finds the current views of a post and adds one to it by updating
 * the postmeta. The meta key used is "awepop_views".
 *
 * @global object $post The post object
 * @return integer $new_views The number of views the post has
 *
 */


//add_action("wp_head", "nybnewsletter_writetoscreen");
add_action( 'admin_menu', 'nybnewsletter_menu' );


// get template and set it to shortcode for template output
add_shortcode( 'nybnewsletter', 'nybNewsletter_get_template_html' );

// Setting panel style sheets
//wp_register_style('nybnewsletterSettingsStylesheet', plugins_url() . '/nybnewsletter/nybnewsletter-settings.css');
//wp_enqueue_style( 'nybnewsletterSettingsStylesheet');
register_activation_hook( __FILE__, 'nybnewsletter_activate' );
register_deactivation_hook( __FILE__, 'nybnewsletter_deactivate' );

function create_nybnewsletter(){

}

function nybnewsletter_menu() {
   add_options_page( 'Nybnewsletter Options', 'Nybnewsletter', 'manage_options', 'nybnewsletter-options', 'nybnewsletter_options' );
}

function nybnewsletter_options() {
   if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
   }
}

function nybnewsletter_activate(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} else {   
		$nybnews = nybnewsletter_create_nybnewsletter();
		$nybnews->activate;
   }
}

function nybnewsletter_deactivate() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} else {   
		$nybnews = nybnewsletter_create_nybnewsletter();
		$nybnews->disactivate;

	}
}

function nybNewsletter_get_template_html() {
	$nybnews = nybnewsletter_create_nybnewsletter();
	return $nybnews->doShortcode();
}

function nybnewsletter_create_nybnewsletter() {
	require_once 'nybnewsletter.class.php';
	$nybnews = new nybnewsletter;

	return $nybnews;
}

