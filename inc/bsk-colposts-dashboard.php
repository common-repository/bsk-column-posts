<?php

class BSKColumnPostsDashboard{

	
	public function __construct() {

		add_action( 'admin_menu', array($this, 'bsk_colposts_settings_menu') );
		add_action( 'bsk_colposts_save_settings', array($this, 'bsk_colposts_save_settings_fun') );
	}
	
	function bsk_colposts_settings_menu() {
		
		add_options_page(   'BSK Column Posts', 
                                      'BSK Column Posts', 
                                      'manage_options', 
                                      'bsk-column-posts', 
                                      array($this, 'bsk_colposts_settings')
                                    );
	}
	
	function bsk_colposts_settings() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		$plugin_settings = get_option( BSKColumnPosts::$_bsk_colposts_settings_option_name, '' );
		$plugin_truncate_content_eanble = false;
		$plugin_truncate_limited_length = 50;
        $pluing_default_featured_image_size = 'thumbnail';
		if( $plugin_settings && is_array($plugin_settings) ){
			if( isset($plugin_settings['truncate_content_eanble']) && $plugin_settings['truncate_content_eanble'] == 'YES' ){
				$plugin_truncate_content_eanble = true;
			}
			if( isset($plugin_settings['truncate_limited_length']) ){
				$plugin_truncate_limited_length = $plugin_settings['truncate_limited_length'];
			}
            if( isset($plugin_settings['default_featured_image_size']) ){
				$pluing_default_featured_image_size = $plugin_settings['default_featured_image_size'];
			}
            
		}
		?>
		<div class="wrap" id="bsk_colpostsoptions_form_ID">
            <h2>BSK Column Posts</h2>
            <div id="bsk_colposts_settings">
            <form method="POST" id="bsk_colposts_settings_form_id">
                <input type="hidden" name="page" value="bsk-column-posts" />
                <h3 style="margin-top: 40px;">Content</h3>
                <p>
                    <?php
                    $checked_str = '';
                    $limited_length_display = 'none';
                    if( $plugin_truncate_content_eanble ){
                        $checked_str = ' checked="checked"';
                        $limited_length_display = 'block';
                    }
                    ?>
                    <label><input type="checkbox" name="bsk_colposts_truncate_content_eanble" id="bsk_colposts_truncate_content_eanble_chk_ID" value="YES" <?php echo $checked_str; ?>/> Enable truncate posts content</label>
                    <br /><br />
                    <span style="font-style:italic;">If no &lt;!--more--&gt; in the post all content will be shown. Enable this to truncate content to limited length.</span>
                </p>
                <p id="bsk_colposts_truncate_limited_length_container_ID" style="display:<?php echo $limited_length_display; ?>;">
                    <label>Limited length: </label>
                    <input type="number" name="bsk_colposts_truncate_content_limited_length" id="bsk_colposts_truncate_content_limited_length_ID" max="3000" min="50" value="<?php echo $plugin_truncate_limited_length; ?>" /> words
                </p>
                <h3>Featured Images</h3>
                <p>
                    <select name="bsk_colposts_default_featured_image_size">
                        <option value="">Select default image size</option>
                        <?php
                        $sizes = BSKColumnPostsCommon::get_image_sizes();
                        $size_name_array = array_keys( $sizes );
                        foreach( $sizes as $size_name => $size_data ){
                            $selected_str = $pluing_default_featured_image_size == $size_name ? ' selected="selected"' : '';
                            $str = '<option value="'.$size_name.'"'.$selected_str.'>'.$size_name.' ('.$size_data['width'].' x '.$size_data['height'].')</option>';
                            
                            echo $str;
                        }
                        ?>
                    </select>
                </p>
                <p>Or, you may use image size name to shortcode parameter: <span style="font-size: 1.1em;">[bsk-colposts include="1,2" columns="2" show-featured-image="YES" featured-image-size="<span style="font-weight: bold;">medium_large</span>"]</span><br />
                     The image size name can be used in your WordPress is: <span style="font-size: 1.1em;"><?php echo '<span style="font-weight: bold; ">'.implode( '</span>, <span style="font-weight: bold;">' , $size_name_array ); ?></span></span>
                </p>
                <p>
                    <?php
                    $nonce = wp_create_nonce( '-bsk-column-posts-settings-nonce' );
                    ?>
                    <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
                    <input type="hidden" name="bsk_colposts_action" value="save_settings" />
                    <input type="button" name="bsk_colposts_save_settings" id="bsk_colposts_save_settings_ID" value="Save" class="button-primary" />
                </p>
            </form>
		</div>
        <div class="bsk-prdoucts">
        	<div class="bsk-prdoucts-single bsk-prdoucts-single-first">
            	<h3>BSK PDF Manager</h3>
                <p>The plugin support you manage your PDF files in WordPress. It’s convenient to make use of. You just need replica the shortcodes into the page/post the place where you wish to have PDF files to exhibit.</p>
                <ol>
                	<li>Upload PDF files via categories</li>
                    <li>Display PDF order by title, date, filename or custom order. Support most top X files shown.</li>
                    <li>Bulk Upload PDF via FTP</li>
                    <li>Show PDF in widget area</li>
                    <li>Featured image supported</li>
               	</ol>
                <p><span style="color:#F33;">Extra 10% off if you have any valid license of our product</span></p>
                <p class="bsk-prdoucts-single-center">
                	<a class="button button-primary bsk-prdoucts-single-link-button" href="http://www.bannersky.com/bsk-pdf-manager/" target="_blank">More Info</a>
                </p>
            </div>
            
            <div class="bsk-prdoucts-single">
            	<h3>BSK GravityForms Blacklist</h3>
                <p>The plugin was built to help block submissions from users using spam data or competitors info to create new entry to your site. This plugin allows you to validate a field's value against the keywords and email addresses that you added in admin area.</p>
                <ol>
                    <li>Blacklist use to block form submitting if field value match any of items( keywords ).</li>
                    <li>White list use to allow form submitting only the field value match any of items( keywords ).</li>
                    <li>Email list use to allow form submitting only the field value match any of items( email address ).</li>
                    <li>Support add items( keywords ) by import CSV file, also can export items ( keywords ) to CSV file.</li>
               	</ol>
                <p>
                	<span style="color:#F33;">Extra 10% off if you have any valid license of our product</span>
                </p>
                <p class="bsk-prdoucts-single-center">
                	<a class="button button-primary bsk-prdoucts-single-link-button" href="http://www.bannersky.com/bsk-gravityforms-blacklist/" target="_blank">More Info</a>
                </p>
            </div>
        </div>
		<?php 
	}
	
	function bsk_colposts_save_settings_fun( $data ){
		if( !wp_verify_nonce( $data['nonce'], '-bsk-column-posts-settings-nonce' ) ) {
			wp_die( 'Invoice nonce' );
		}
		if (!current_user_can('manage_options'))  {
			return;
		}
		$plugin_settings = get_option( BSKColumnPosts::$_bsk_colposts_settings_option_name, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['truncate_content_eanble'] = isset($data['bsk_colposts_truncate_content_eanble']) ? $data['bsk_colposts_truncate_content_eanble'] : 'NO';
		$plugin_settings['truncate_limited_length'] = isset($data['bsk_colposts_truncate_content_limited_length']) ? $data['bsk_colposts_truncate_content_limited_length'] : '';
        $plugin_settings['default_featured_image_size'] = isset($data['bsk_colposts_default_featured_image_size']) ? $data['bsk_colposts_default_featured_image_size'] : '';
        
		update_option( BSKColumnPosts::$_bsk_colposts_settings_option_name, $plugin_settings );
	}
}
