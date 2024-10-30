<?php
/*
Plugin Name: Hub2Word
Plugin URI: http://www.webbege.com/
Description: Hubspot 2 Wordpress Plugin
Version: 1.1.0
Author: Webbege, Inc
Author URI: http://www.webbege.com/
Text Domain: hub2word
*/

/**
 * Copyright (C) 2022 Webbege, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package     Hub2Word
 * @version     1.1.0
 * @since       1.0.0
 * @author      Webbege, Inc.
 * @copyright   Copyright (c) 2022 Webbege, Inc.
 * @link        https://www.webbege.com/
 * @license     http://www.gnu.org/licenses/gpl.html
 */

if(!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

// =============================================
// Define Global Constants
// =============================================
if ( !defined( 'H2W_NAME' ) ) {
    define( 'H2W_NAME', 'Hub2Word' );
}
//Set Current Plugin Version
if ( !defined( 'H2W_VERSION' ) ) {
    define( 'H2W_VERSION', '1.1.0' );
}
//Set Minimum WordPress version for compatibility
if( !defined( 'H2W_WP_MIN_VERSION' ) ){
    define( 'H2W_WP_MIN_VERSION', '5.0');
}

if ( !defined( 'H2W_TEXT' ) ) {
    define( 'H2W_TEXT', 'hub2word' );
}

if ( !defined( 'H2W_DIR' ) ) {
    define( 'H2W_DIR', dirname( plugin_basename( __FILE__ ) ) );
}

// Set constant URI to the plugin URL.
if ( !defined( 'H2W_URL' ) ) {
    define( 'H2W_URL', plugin_dir_url( __FILE__ ) );
}

// Set constant path to the plugin directory.
if ( !defined( 'H2W_PATH' ) ) {
    define( 'H2W_PATH', plugin_dir_path( __FILE__ ) );
}

// Set the constant path to the plugin's javascript directory.
if( !defined( 'H2W_JS' ) ) {
    define( 'H2W_JS', H2W_URL . trailingslashit( 'assets' ));
}

// Set the constant path to the plugin's CSS directory.
if( !defined( 'H2W_CSS' ) ) {
    define( 'H2W_CSS', H2W_URL . trailingslashit( 'assets' ));
}

// Set the constant path to the plugin's images directory.
if( !defined( 'H2W_IMG' ) ) {
    define( 'H2W_IMG', H2W_URL . trailingslashit( 'assets' ));
}

// Set the constant path to the plugin's includes directory.
if( !defined( 'H2W_INC' ) ) {
    define( 'H2W_INC', H2W_PATH . trailingslashit( 'inc' ));
}

// Set the constant path to the plugin's includes directory.
if( !defined( 'H2W_SUPPORT' ) ) {
    define( 'H2W_SUPPORT', 'https://www.webbege.com/hub2word/hub2word-feedback/');
}

/**
 *  Load H2W Settings Page
 */
function Hub2Word_settings() {
  $tokenResponse = HubspotRequest::getToken();
  if (isset($tokenResponse['token'])) {
    $tokenInfo = HubspotRequest::getAccessTokenInfo($tokenResponse['token']);
    if ($tokenInfo['status']) {
      $result = json_decode($tokenInfo['response']);
      $response = HubspotRequest::checkTokenValidity($result->expires_in);
      if ($response) {
        $GLOBALS['token_info'] = $result ;
      }
    }
  } else {
    $GLOBALS['token_info'] = FALSE;
  }

  //TODO - improve
  $GLOBALS['api_key'] = get_option('h2w_api');

  include H2W_INC . 'H2WoAuth.php';
}

// =============================================
// WP Admin Notices
// =============================================
function Hub2Word_PhpNotice(){
  echo '<div class="message error"><p>' . sprintf(__('%s <strong>Requirements failed.</strong> PHP version must <strong>at least %s</strong>. You are using version ' . PHP_VERSION, 'hub2word'), 'Hub2Word', '5.3.3') . '</p></div>';
}

function Hub2Word_TokenNotice(){
  if(!get_option('access_token') && !get_option('h2w_api')){
    $message = 'Please connect your <a href="' . admin_url( 'admin.php?page=hub2word' ) . '">Hub2Word plugin</a> to your HubSpot account.';
    queue_flash_message($message, 'error');
  }
}
add_action('admin_notices', 'Hub2Word_TokenNotice');

// =============================================
// Check for PHP Version and Include Needed Files
// =============================================
if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
    require_once H2W_INC .'Authentication.php';
    require_once H2W_INC .'curl/HubspotRequest.php';
    include H2W_INC .'WPFlashMessages.php';
} else {
    add_action('admin_notices', 'Hub2Word_PhpNotice');
}
// =============================================
// Check for HTTPS
// =============================================
// function Hub2Word_SSLNotice(){
//   echo '<div class="message error"><p>' . sprintf(__('%s <strong>Requirements failed.</strong> WP-Admin must be running on HTTPS ', 'hub2word'), 'Hub2Word') . '</p></div>';
// }
if (is_ssl()) {
//   add_action('admin_notices', 'Hub2Word_SSLNotice');
// } else {
  // =============================================
  // Token Refresh Checker
  // =============================================
  function Hub2Word_refresh_checker () {
      $h2w_token = get_option('access_token');
      if ($h2w_token) {
          if (time() >= $h2w_token['h2w_refresh']) {
              hub2word_refresh_authentication($h2w_token['refresh_token']);
          }
      }
  }
  add_action('init', 'Hub2Word_refresh_checker');
}


/**
* Check for Token - Include Files
*/

$h2w_token = get_option('access_token');
$api_key = get_option('h2w_api');
if($h2w_token || $api_key){
  require_once H2W_INC .'Widget.php';
  require_once H2W_INC .'ExtendSearch.php';
  require_once H2W_INC .'Shortcodes.php';

  add_filter('mce_external_plugins', 'Hub2Word_button_script');

}

register_activation_hook( __FILE__ , 'Hub2Word_Activation' );
register_deactivation_hook( __FILE__ , 'Hub2Word_Deactivation' );


// =============================================
// Load JS and CSS
// =============================================
/**
 * Enqueue front endscripts
 */
function Hub2Word_styles_method() {
  if(get_option('hbs_custom_css')){
    $custom_inline_style = get_option('hbs_custom_css');
    wp_register_style( 'h2wcustom-style', false );
    wp_enqueue_style( 'h2wcustom-style' );
    wp_add_inline_style( 'h2wcustom-style', $custom_inline_style );
  }
}
add_action('wp_enqueue_scripts', 'Hub2Word_styles_method');

/**
 * Add admin scripts
 */
function Hub2Word_LoadScript($hook){
  
  if ( 'toplevel_page_hub2word' != $hook ) {
        wp_enqueue_script('hub2word-admin', H2W_JS .'hub2word-admin.js',array('jquery'),'20180722',true);
        wp_enqueue_style('hub2word-style', H2W_CSS .'hub2word-style.css');
        return;
    } else {
        wp_enqueue_style('hub2word-style', H2W_CSS .'bootstrap.min.css');
        wp_enqueue_style('hub2word-admin-style', H2W_CSS .'hub2word-style.css');
        wp_enqueue_script('hub2word-custom', H2W_JS .'hub2word-custom.js',array('jquery'),'20180722',true); 
    }
}
add_action('admin_enqueue_scripts','Hub2Word_LoadScript');

/**
 * Load TinyMCE plugin scripts
 */
function Hub2Word_button_script($plugins) {
  if(get_option('include_forms')){
    $plugins['hub2wordForm'] = H2W_JS. 'hub2word-form.js';
  }
  if(get_option('include_cta')){
    $plugins['hub2wordCTA'] = H2W_JS. 'hub2word-cta.js';
  }
  if(get_option('include_link')){
  $plugins['hub2wordLink'] = H2W_JS. 'hub2word-link.js';
  }
  if(get_option('include_files')){
  $plugins['hub2wordFile'] = H2W_JS. 'hub2word-file-managers.js';
  }
  return $plugins;
}


// =============================================
// Setup Core H2W Admin Options and Settings
// =============================================

/**
 *  Admin dashboard menu bar
 */
add_action('admin_menu', 'Hub2Word_integration');

function Hub2Word_integration() {
  add_menu_page(
    'Hub2Word', 
    'Hub2Word', 
    'manage_options', 
    'hub2word', 
    'hub2word_settings', 
    'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyODUuMDggMjg1Ij48ZGVmcz48c3R5bGU+LmNscy0xLC5jbHMtMntmaWxsOiMwMDg3YmU7fS5jbHMtMiwuY2xzLTN7ZmlsbC1ydWxlOmV2ZW5vZGQ7fS5jbHMtM3tmaWxsOiNmNjA7fTwvc3R5bGU+PC9kZWZzPjx0aXRsZT5IdWIyV29yZF9idWc8L3RpdGxlPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTU1Ljg0LDU2LjE0QTEyMC43LDEyMC43LDAsMSwwLDk1LjIxLDI5Ljc5LDEyMS40NiwxMjEuNDYsMCwwLDAsNTUuODQsNTYuMTRaTTE5LjcsOTEuNDlhMTMyLjE0LDEzMi4xNCwwLDEsMS0xMCw1MC40MywxMzIuMTMsMTMyLjEzLDAsMCwxLDEwLTUwLjQzWiIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTc1LjQ4LDYwLjRsMzkuMDYsMjguNzJhNTguODksNTguODksMCwwLDEsMTcuNzktNS44MWwtLjE2LTQ2YTEwNC40OCwxMDQuNDgsMCwwLDAtNTYuNjksMjNaIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTUwLjY3LDM3LjMzbC4xNiw0NmE1OS4zMyw1OS4zMywwLDEsMS00NC4zNSwxMDYuNDJMNzMuOSwyMjIuMTNhMTA1LDEwNSwwLDEsMCw3Ni43OC0xODQuOFoiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik04Mi4yOSwxNDEuOTJBNTkuMTMsNTkuMTMsMCwwLDEsOTksMTAwLjY4TDYyLDczLjVBMTA1LDEwNSwwLDAsMCw2MC44NSwyMDlsMzIuNi0zMi40M2E1OS4wNiw1OS4wNiwwLDAsMS0xMS4xNi0zNC42M1oiLz48cGF0aCBjbGFzcz0iY2xzLTMiIGQ9Ik0xMTguNDQsMTE4Ljc0YTMyLjc5LDMyLjc5LDAsMSwwLDIzLjE4LTkuNiwzMi42OSwzMi42OSwwLDAsMC0yMy4xOCw5LjZaIi8+PC9zdmc+', 
    25
  );
}

/**
 * H2W Initial Activation
 */
function Hub2Word_activation() {
  global $wpdb;
  $options = get_option( 'h2w_options' );

  if ( ( $options['h2w_installed'] != 1 ) || ( ! is_array( $options ) ) ) {
    $opt = array(
      'h2w_installed'           => 1,
    );

    update_option( 'h2w_options', $opt );

    update_option('include_search', 1);
    update_option('search_blogs', 1);
    update_option('search_lpages', 1);
    update_option('search_pages', 1);
    update_option('include_forms', 1);
    update_option('include_cta', 1);
    update_option('include_link', 1);
    update_option('include_files', 1);

  }

}

/**
 * H2W Setting Saver
 */
function Hub2Word_save_settings() {

  $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : null;
  $opt_value = isset($_POST['opt_value']) ? sanitize_text_field($_POST['opt_value']) : null;
  $update = update_option($opt_name, $opt_value);
  
}
add_action('wp_ajax_Hub2Word_save_settings', 'Hub2Word_save_settings');

/**
 * H2W Deactivation
 */
function Hub2Word_deactivation() {
  
  //Nothing Yet

}

/**
 * Logs a debug statement to /wp-content/debug.log
 *
 * @param   string
 */
function Hub2W_log_debug( $message ) {
  if ( WP_DEBUG === true ) {
    if ( is_array( $message ) || is_object( $message ) ) {
      error_log( print_r( $message, true ) );
    } else {
      error_log( $message );
    }
  }
}
