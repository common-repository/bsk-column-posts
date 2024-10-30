<?php

/*
Plugin Name: BSK Column Posts
Description: A plugin that show posts in column, featured image suported. Responsive supported.
Plugin URL: http://www.bannersky.com/bsk-column-posts/
Version: 2.0
Author: BannerSky
Author URI: http://www.bannersky.com/


------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, 
or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKColumnPosts{
	
    private static $instance;
    
	private static $_bsk_colposts_plugin_version = '2.0';
	public static $_bsk_colposts_settings_option_name = '_bsk_colposts_settings_';
	public static $_bsk_colposts_post_meta_option_name = '_bsk_colposts_meta_';
	
	public $_bsk_colposts_front_OBJ = NULL;
	public $_bsk_colposts_dashboard_OBJ = NULL;
	
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BSKColumnPosts ) ) {
			self::$instance = new BSKColumnPosts;
            
            // Plugin Folder Path.
            if ( ! defined( 'BSK_COLUMN_PLUGIN_DIR' ) ) {
                define( 'BSK_COLUMN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( ! defined( 'BSK_COLUMN_PLUGIN_URL' ) ) {
                define( 'BSK_COLUMN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
		
            add_action( 'wp_enqueue_scripts', array(self::$instance, 'bsk_colposts_enqueue_scripts_and_styles') );
            add_action( 'admin_enqueue_scripts',  array(self::$instance, 'bsk_colposts_enqueue_scripts_and_styles') );
            add_action( 'init', array(self::$instance, 'bsk_colposts_post_action') );

            //hooks
            register_activation_hook( __FILE__, array(self::$instance, 'bsk_colposts_activate') );
            register_deactivation_hook(  __FILE__, array(self::$instance, 'bsk_colposts_deactivate') );
            register_uninstall_hook( __FILE__, 'BSKColumnPosts::bsk_colposts_uninstall' );
            
            require_once( BSK_COLUMN_PLUGIN_DIR.'inc/bsk-colposts-common.php' );
            require_once( BSK_COLUMN_PLUGIN_DIR.'inc/bsk-colposts-front.php' );
            require_once( BSK_COLUMN_PLUGIN_DIR.'inc/bsk-colposts-dashboard.php' );
            
            self::$instance->_bsk_colposts_front_OBJ = new BSKColumnPostsFront();
            self::$instance->_bsk_colposts_dashboard_OBJ = new BSKColumnPostsDashboard();
        }
        
        return self::$instance;
	}
	
    public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
    public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
	function bsk_colposts_activate(){
		// Clear the permalinks
		flush_rewrite_rules();
	}
	
	function bsk_colposts_deactivate(){
		// Clear the permalinks
		flush_rewrite_rules();
	}
	
	function bsk_colposts_enqueue_scripts_and_styles(){
		if ( is_admin() ) {
			wp_enqueue_style( 'bsk-colposts-style', BSK_COLUMN_PLUGIN_URL . 'css/bsk-colposts-admin.css', array(), self::$_bsk_colposts_plugin_version );
			wp_enqueue_script( 'bsk-colposts-admin', BSK_COLUMN_PLUGIN_URL . 'js/bsk-colposts-admin.js', array( 'jquery' ), self::$_bsk_colposts_plugin_version );
		}else{
			wp_enqueue_style( 'bsk-colposts-style', BSK_COLUMN_PLUGIN_URL . 'css/bsk-colposts.css', array(), self::$_bsk_colposts_plugin_version );
		}
	}
	
	function bsk_colposts_post_action(){
		if( isset( $_POST['bsk_colposts_action'] ) && strlen($_POST['bsk_colposts_action']) > 0 ) {
			do_action( 'bsk_colposts_' . $_POST['bsk_colposts_action'], $_POST );
		}
	}
	
	function bsk_colposts_uninstall(){
		delete_option( '_bsk_colposts_settings_' );
		
		global $wpdb;
		
		$sql = 'DELETE FROM `'.$wpdb->postmeta.'` WHERE `meta_key` LIKE "_bsk_colposts_meta_"';
		
		$wpdb->query( $sql );
	}
	
}

BSKColumnPosts::instance();
