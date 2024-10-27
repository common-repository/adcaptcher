<?php
/*
Plugin Name: AdCaptcher
Plugin URI: http://www.adcaptcher.com
Description: AdCaptcher.com enables you to build custom captchas. You need an <a href="http://admin.adcaptcher.com" target="_blank">AdCaptcher account</a> to create profiles for your websites and to administrate your captchas.
Version: 1.2.5
Author: razvaniacob, razvantirboaca
Author URI: http://www.adcaptcher.com
*/

/*  Copyright 2008  Adcaptcher, http://www.adcaptcher.com
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

# ----------------------------------------------------------------
# AdCaptcher class
# ----------------------------------------------------------------
class adcaptcher {

	# ------
	# Version info
	# ------
	var $version = '1.2.5';
	
	# ----------------------------------------------------------------
	# Constructor
	# ----------------------------------------------------------------
	function adcaptcher() {
		add_action("admin_menu",  array("adcaptcher", "adc_submeniu"));
		$this->adc_init();
	}
	
	# ----------------------------------------------------------------
	# Alert admin if key is not set
	# ----------------------------------------------------------------
	function adc_warning() {
		if(get_admin_page_title() != 'AdCaptcher Config')
			echo "<div id='adcaptcher-warning' class='updated fade'><p>".sprintf(__('You must <a href="%1$s">enter your AdCaptcher key</a> for this website in order to work.'), "plugins.php?page=adcaptcher-key-config")."</p></div>";
	}
	
	# ----------------------------------------------------------------
	# Start adcaptcher script
	# ----------------------------------------------------------------
	function adc_init() {
		global $user_ID;
		
		if ( !$this->adc_get_key() && !isset($_POST['submit']) ) {
			add_action("admin_notices", array("adcaptcher", "adc_warning"));
			return;
		}
		
		if ($user_ID) {
			return;
		} else {
			if ( $this->adc_verify_key($this->adc_get_key()) ) {
				add_action("comment_form", array("adcaptcher", "adc_embeded"), 9999);
				add_action("preprocess_comment", array("adcaptcher", "adc_captcha_post"));
			}
		}
	}
	
	# ----------------------------------------------------------------
	# Add admin config link
	# ----------------------------------------------------------------
	function adc_submeniu() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('AdCaptcher Config'), __('AdCaptcher Config'), 'manage_options', 'adcaptcher-key-config', array('adcaptcher','adc_config_page'));
	}
	
	# ----------------------------------------------------------------
	# Admin config page
	# ----------------------------------------------------------------
	function adc_config_page() {
		global $adcaptcher;
		if ( isset($_POST['updatekey']) ) {
			if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die(__('Cheatin&#8217; uh?'));
	
			$key = $_POST['key'];
	
			if ( empty( $key ) ) {
				$ms = 'new_key_empty';
				delete_option('adc_key');
			} elseif ( !$adcaptcher->adc_verify_key( $key ) ) {
				$ms = 'new_key_invalid';
			} else {
				update_option('adc_key', $key);
				$ms = 'new_key_valid';
			}
		} else if ( isset($_POST['updateoptions']) ) {
			if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die(__('Cheatin&#8217; uh?'));
				
			$key = $adcaptcher->adc_get_key();
	
			if ( !isset($_POST['credits']) ) {
				add_option('adc_credits', 'false');
				$ms = 'not_show_copyr';
			} else {
				delete_option('adc_credits');
				$ms = 'show_copyr';
			}
		} else {
			$key = $adcaptcher->adc_get_key();
			if ( empty( $key ) ) {
				$ms = '';
			} elseif ( !$adcaptcher->adc_verify_key( $key ) ) {
				delete_option('adc_key');
				$ms = 'key_invalid';
			} else {
				$ms = 'key_valid';
			}
		}
		$messages = array(
			'new_key_empty' 	=> array('class' => 'updated', 'text' => __('Your key has been cleared.')),
			'new_key_valid' 	=> array('class' => 'updated', 'text' => __('Your key has been saved. Happy blogging!')),
			'new_key_invalid' 	=> array('class' => 'error', 'text' => __('The key you entered is invalid. Please check it in the Websites section of your AdCaptcher account.')),
			'key_valid' 		=> array('class' => 'updated', 'text' => __('This key is valid.')),
			'key_invalid' 		=> array('class' => 'error', 'text' => __('The key below was previously validated but a connection to adcaptcher.com can not be established at this time. Please check your server configuration.')),
			'not_show_copyr' 	=> array('class' => 'updated', 'text' => __('Your settings has been saved.')),
			'show_copyr' 		=> array('class' => 'updated', 'text' => __('Your settings has been saved.')),
		);
	?>
	<?php if ( $ms != '' ) { ?>
		<div class="<?php echo $messages[$ms]['class']; ?> fade"><p><strong><?php echo$messages[$ms]['text']; ?></strong></p></div>
	<?php } ?>
	
	<div class="wrap">
	  <h2><?php _e('Adcaptcher Configuration'); ?></h2>
	  <div class="narrow">
	    <form action="" method="post" id="adcaptcher-conf" style="margin: auto; width: 400px; ">
	      <p><?php printf(__('<a href="%1$s" target="_blank">AdCaptcher.com</a> enables you to build custom captchas. Please enter the activation key. You can find it in the <a href="%2$s" target="_blank">Websites section</a> of your AdCaptcher account.'), 'http://www.adcaptcher.com/', 'http://admin.adcaptcher.com/websites'); ?></p>
	      <h3><label for="key"><?php _e('Activation Key'); ?></label></h3>
	      <p>
	        <input id="key" name="key" type="text" size="32" maxlength="32" value="<?php echo $key; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />
	      </p>
	      <p class="submit"><input type="submit" name="updatekey" value="<?php _e('Update key &raquo;'); ?>" /></p>
	      <p><?php _e('<b>Pay attention:</b> AdCaptcher will not be shown to you when you are logged in the wp-admin. Log out if you want to see it!'); ?></p>
	    </form>
	  </div>
	  <div class="narrow">
	    <form action="" method="post" id="adcaptcher-conf" style="margin: auto; width: 400px; ">
		    <h3><?php _e('Show Credits:'); ?></h3>
		    <input id="credits" name="credits" type="checkbox" size="32" maxlength="32" value="1" <?php if(!get_option('adc_credits')) { ?>checked="checked"<?php } ?> /> <?php _e('Display the \'powered by\' link.'); ?>
			<p class="submit"><input type="submit" name="updateoptions" value="<?php _e('Save &raquo;'); ?>" /></p>
		</form>
	  </div>
	</div><?php
	}
	
	# ----------------------------------------------------------------
	# Validate public key
	# ----------------------------------------------------------------
	function adc_verify_key($key) {
		if(strlen($key) != 32 || strlen(preg_replace('/[a-z0-9]/', '', $key)) > 0)
			return false;
		else
			return true;
	}
	
	# ----------------------------------------------------------------
	# Get public key
	# ----------------------------------------------------------------
	function adc_get_key() {
		return get_option('adc_key');
	}
	
	# ----------------------------------------------------------------
	# Embeded script
	# ----------------------------------------------------------------
	function adc_embeded($id) {
		global $adcaptcher;
		global $user_ID;
		if( $user_ID ) {
			return $id;
		} else {
			echo '<script type="text/javascript" src="http://code.adcaptcher.com/'.$adcaptcher->adc_get_key().'"></script>
				  <script type="text/javascript">acImg();</script>';
			// on submit action	  
			echo '<script type="text/javascript">
				for( i = 0; i < document.forms.length; i++ ) {
					if( typeof(document.forms[i].kcodecaptcha) != \'undefined\' ) {
						commentForm = document.forms[i].comment.parentNode;
						break;
					}
				}
				var p  = document.createElement("p");
				var commentArea = commentForm.parentNode;
				var captchafrm = document.getElementById("captch-wrap");
				commentArea.insertBefore(p, commentForm);
				p.appendChild(captchafrm);
				document.getElementById("ac-in").tabIndex = document.getElementById("comment").tabIndex;
				function checkCode() {	return acCheck(document.getElementById("commentform")); }
				document.getElementById("commentform").onsubmit = checkCode;
			</script>';
				  
			if(get_option('adc_credits') == 'false') {
				echo '<style type="text/css">
						#captch-love { display: none !important;} 
					  </style>';
			}
		}
	}
	
	# ----------------------------------------------------------------
	# Validate user security code
	# ----------------------------------------------------------------
	function adc_captcha_post($id) {
		global $user_ID;
		
		if( $user_ID ) {
			return $id;
		} else {
			$response = file_get_contents("http://code.adcaptcher.com/check".$_POST["kcodekey"]."/".urlencode($_POST["kcodecaptcha"])); 
			if($response == "INVALID") {
			   // action when adcaptcher is INVALID
			   wp_die('You need to activate JavaScript in order to submit this form. Please activate it and <a href="'.$_SERVER['HTTP_REFERER'].'#respond">go back</a>, thank you!');
			  // echo ;
			  // exit();
			} else {
				// action when is INACTIVE or everithing OK
				return $id;
			}
		}
	}
}

$adcaptcher = new adcaptcher;

?>