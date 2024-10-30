<?php
//HubSpot Authentication
function Hub2Word_api_authentication() 
{
  $api_key = isset($_POST['api_key']) ? $_POST['api_key'] : null;
  $url = 'https://api.hubapi.com/integrations/v1/me?hapikey=' . $api_key;

  //TODO - add to HubspotRequest/CURL
  $response = wp_remote_get( $url );

  $hub_info = json_decode($response['body']);

  if($hub_info->portalId){
    update_option( 'h2w_api', $api_key );
    update_option( 'hub_id', $hub_info->portalId );
  }

  die();
}

add_action('wp_ajax_hub2word_apiKey', 'Hub2Word_api_authentication');


function Hub2Word_authentication() 
{
  $redirect_uri = site_url("/wp-admin/admin.php?page=hub2word&hub2word_save_auth=1", 'https');
  $scope = array('contacts', 'forms', 'files', 'content');
  $response = HubspotRequest::getAuthUrl($redirect_uri, $scope);
  echo json_encode(array('auth_url' => $response, 'status' => 'Ok'));
  die();
}

add_action('wp_ajax_hub2word_auth', 'Hub2Word_authentication');

function Hub2Word_disconnect() 
{

  delete_option('access_token');
  delete_option('h2w_api');
  delete_option('hub_id');
  $GLOBALS['token_info'] = FALSE;

  die();
}

add_action('wp_ajax_hub2word_disconnect', 'Hub2Word_disconnect');

function Hub2Word_save_authentication() 
{
  if (isset($_GET['hub2word_save_auth']) && !empty($_GET['hub2word_save_auth'])) {
    $redirectUri = site_url("/wp-admin/admin.php?page=hub2word&hub2word_save_auth=1", 'https');
    if (isset($_GET['code']) && !empty($_GET['code'])) {
        $code = $_GET['code'];
      try {
        $grantType = 'authorization_code';
        $access = HubspotRequest::getAccessToken($grantType, $redirectUri, $code);

        if ($access['status']) {
          $response = json_decode($access['response']);

          if (!isset($response->status) && $response->status != 'BAD_AUTH_CODE') {

            $dateToRefresh = strtotime("+{$response->expires_in} seconds");

            $data = array (
                'access_token' => $response->access_token,
                'refresh_token' => $response->refresh_token,
                'expires_in' => $response->expires_in,
                'h2w_refresh' => ($dateToRefresh) - 1800, // will run a half hour earlier than required
            );

            delete_option('access_token');
            add_option('access_token', $data);

            $access_token_info = HubspotRequest::getAccessTokenInfo($response->access_token);
            $token_info = json_decode($access_token_info['response']);

            delete_option('hub_id');
            delete_option('hub_access_token_info');

            add_option('hub_id', $token_info->hub_id);
            add_option('hub_access_token_info', serialize($token_info));

            queue_flash_message('HubSpot Authentication Successful!', 'updated');
          } else {
            queue_flash_message("Error! {$response->message}", 'error');
          }
        }
        } catch (\Exception $e) {
          $error = json_decode(substr($e->getMessage(), strpos($e->getMessage(), '{')));
        }

    } else {
      queue_flash_message('Missing required parameters!', 'error');
    }
    wp_redirect(site_url("/wp-admin/admin.php?page=hub2word"));
    exit;
  }
}
add_action('admin_init', 'Hub2Word_save_authentication');

function Hub2Word_refresh_authentication($refreshToken) 
{
    try {
      $access = HubspotRequest::getRefreshTokenUrl($refreshToken);

      if ($access['response']) {
        $response = json_decode($access['response']);

        if (!isset($response->status) && $response->status != 'BAD_AUTH_CODE') {

          $file = 'token_refresh_log_' . date("mY") . '.txt'; 

          // $data = $response;
          // if ( is_array($data) || is_object($data) ) {
          //     $data = serialize($data);
          // }

          $data = 'Refreshed';

          // Write to a log file
          $output = '[' . date('Y-m-d H:i:s') . '] ' . $data . PHP_EOL;
          file_put_contents($file, $output, FILE_APPEND | LOCK_EX);

          $dateToRefresh = strtotime("+{$response->expires_in} seconds");

          $data = array (
              'access_token' => $response->access_token,
              'refresh_token' => $response->refresh_token,
              'expires_in' => $response->expires_in,
              'h2w_refresh' => ($dateToRefresh) - 1800, // will run a half hour earlier than required
          );

          delete_option('access_token');
          add_option('access_token', $data);

          $access_token_info = HubspotRequest::getAccessTokenInfo($response->access_token);
          $token_info = json_decode($access_token_info['response']);

          delete_option('hub_id');
          delete_option('hub_access_token_info');

          add_option('hub_id', $token_info->hub_id);
          add_option('hub_access_token_info', serialize($token_info));

        } else {
          queue_flash_message("Error! {$response->message}", 'error');
        }
      }
    } catch (\Exception $e) {
      $error = json_decode(substr($e->getMessage(), strpos($e->getMessage(), '{')));
    }
}