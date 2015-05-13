<?php
/*
Plugin Name: WP Plugin Jenkins Job
Plugin URI: 
Description: 
Version: 0.1
Author: salimkapadia
Author URI: 
License: GPLv2
*/

	define('WP_PLUGIN_JENKINS_JOB','wordpress_plugin_jenkins_job'); // This is used for namespacing of possible language files (i18n)

	define('SITEPUSH_LABEL','Publish to live'); // The label to display

	define('ELEMENT_NAME','wordpress_plugin_jenkins_job_site_push'); // The name of the HTML element

	/*
		Do any installation steps here.
	 */	
	function wordpress_plugin_jenkins_job_install(){

	}
	/*
		Do any removal steps here.
	 */
	function wordpress_plugin_jenkins_job_remove(){

	}
	/*
		Do any initialization here.
	 */
	function wordpress_plugin_jenkins_job_init(){

	}

	/*
		Adds a box to the main column on the Post and Page edit screens.
		http://codex.wordpress.org/Function_Reference/add_meta_box
	*/
	function wordpress_plugin_jenkins_job_add_meta_box(){
		$id = 'wordpress_plugin_jenkins_job_id'; // HTML 'id' attribute of the edit screen section. Default: None

		$title = 'Sitepush'; // Title of the edit screen section, visible to user. Default: None

		$callback = 'wordpress_plugin_jenkins_job_meta_box_callback'; // Function that prints out the HTML for the edit screen section. Default: None

		$screen = NULL; // The type of writing screen on which to show the edit screen section ('post','page','dashboard','link','attachment','custom_post_type'). Default: null
		$screens = array( 'post', 'page' ); //show the option on these pages

		$context = 'side'; // The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). Default: 'advanced'

		$priority = 'high'; // The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: 'default'

		$callback_args = NULL; // Arguments to pass into your callback function. The callback will receive the $post object and whatever parameters are passed through this variable.

		foreach ( $screens as $screen ) {

			add_meta_box($id,
				__( $title, WP_PLUGIN_JENKINS_JOB),
				$callback,
				$screen,
				$context,
				$priority,
				$callback_args
			);
		}
	}

	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	function wordpress_plugin_jenkins_job_meta_box_callback( $post ){
		echo '<label for="wordpress_plugin_jenkins_job_site_push">';
			_e( SITEPUSH_LABEL, WP_PLUGIN_JENKINS_JOB );
		echo '</label> ';
		echo '<input type="checkbox" id="' . ELEMENT_NAME . '" name="' . ELEMENT_NAME . '" checked />';
	}

	function post_saved( $post_id ) {
		$post = get_post($post_id); 

		if( $post == NULL){
			return;
		}

		$post_status = $post->post_status;

		/* check if the custom field is submitted (checkboxes that aren't marked, aren't submitted) */
		$is_custom_action_requested = ($_POST[ELEMENT_NAME]) ? true : false;

		//https://codex.wordpress.org/Post_Status
		switch ( $post_status ) {
			case 'draft': //https://codex.wordpress.org/Post_Status#Draft

			case 'auto-draft': //https://codex.wordpress.org/Post_Status#Auto-Draft

			case 'future': //https://codex.wordpress.org/Post_Status#Future

			case 'pending': //https://codex.wordpress.org/Post_Status#Pending

			case 'private': //https://codex.wordpress.org/Post_Status#Private

			case 'trash': //https://codex.wordpress.org/Post_Status#Trash

			case 'inherit': //https://codex.wordpress.org/Post_Status#Inherit
				break;

			case 'publish': //https://codex.wordpress.org/Post_Status#Published
				if ($is_custom_action_requested){
					run_jenkins_job();
				}
				break;
			break;
		}
	}

	/*
		This function will call out to jenkins to perform the site push.
	 */
	function run_jenkins_job(){

	}

	/* Runs when plugin is activated */
	register_activation_hook(__FILE__,'wordpress_plugin_jenkins_job_install');

	/* Runs when plugin is deactivated */
	register_deactivation_hook(__FILE__,'wordpress_plugin_jenkins_job_remove');

	/* Runs plugin initiaiton code */
	add_action( 'init', 'wordpress_plugin_jenkins_job_init');
	
	/* https://codex.wordpress.org/Plugin_API/Action_Reference/save_post */
	/* Runs whenever a post or page is created or updated, which could be from an import, post/page edit form, xmlrpc, or post by email. */
	add_action( 'save_post', 'post_saved' );

	/* This adds a box in various WP admin screens	*/
	/* http://codex.wordpress.org/Function_Reference/add_meta_box */
	add_action( 'add_meta_boxes', 'wordpress_plugin_jenkins_job_add_meta_box' );