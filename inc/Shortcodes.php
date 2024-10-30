<?php

function Hub2Word_register_shortcodes() 
{
  add_shortcode('hubspot_CTA', 'Hub2Word_cta');
  add_shortcode('hubspot_form', 'Hub2Word_form');
}
add_action('init', 'Hub2Word_register_shortcodes');

//Add CTA Embed
function Hub2Word_cta($atts) 
{
  global $post;
  
  extract(shortcode_atts(array('id' => '',), $atts));
  $embeded_code = '
      <!--HubSpot Call-to-Action Code -->
      <span class="hs-cta-wrapper" id="hs-cta-wrapper-' . $id . '">
          <span class="hs-cta-node hs-cta-' . $id . '" id="'. $id . '">
              <!--[if lte IE 8]>
              <div id="hs-cta-ie-element"></div>
              <![endif]-->
              <a href="https://cta-redirect.hubspot.com/cta/redirect/' . get_option('hub_id') . '/'. $id . '" >
                  <img class="hs-cta-img" id="hs-cta-img-' . $id . '" style="border-width:0px;" src="https://no-cache.hubspot.com/cta/default/' . get_option('hub_id') . '/' . $id . '.png"  alt="New call-to-action"/>
              </a>
          </span>
          <script charset="utf-8" src="//js.hubspot.com/cta/current.js"></script>
          <script type="text/javascript">
              hbspt.cta.load(' . get_option('hub_id') . ', \''. $id . '\', {});
          </script>
      </span>
      <!-- end HubSpot Call-to-Action Code -->
  ';

  return $embeded_code;
}

//Add HubSpot form with embeded code
function Hub2Word_Form($atts) 
{
  extract(shortcode_atts(array('id' => '',), $atts));
  $embeded_code = '
      <!--[if lte IE 8]>
      <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
      <![endif]-->
      <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
      <script>
       hbspt.forms.create({
        portalId: "' . get_option('hub_id') . '",
        formId: "' . $id . '",
        css: ""
       });
      </script>
  ';
  return $embeded_code;
}

//Register newly added button for TinyMce
 
function Hub2Word_register_buttons($buttons) 
{
  array_push($buttons, " |", 'hub2wordForm', 'hub2wordCTA', 'hub2wordLink', 'hub2wordFile');
  return $buttons;
}
add_filter('mce_buttons', 'Hub2Word_register_buttons');

/**
 * Load hubspot cta dialog
 * @return HTML
 */
function Hub2Word_cta_content() 
{
  $response = true;
  if ($response) {
    $GLOBALS['response'] = $response;
  } else {
    $GLOBALS['response'] = FALSE;
  }
  include 'DialogCTA.php';
  die();
}
add_action('wp_ajax_hub2word_cta', 'Hub2Word_cta_content');

/**
 * Load hubspot forms for popup
 * @return HTML
 */
function Hub2Word_form_content() 
{
  $getToken = HubspotRequest::getToken();
  $response = HubspotRequest::getHubspotForms();
  if ($response) {
    $GLOBALS['response'] = $response;
  } else {
    $GLOBALS['response'] = FALSE;
  }
  include 'DialogForms.php';
  die();
}
add_action('wp_ajax_hub2word_form', 'Hub2Word_form_content');

/**
 * Add HUbspot links options
 * @return HTML
 */
function hub2word_link_content() 
{
  $portalId = get_option('hub_id');
  $offset = 0;
  $limit = 100;
  $results_count = 0;
  $results = array();

  //Run Initial Search
  $reqUrl = 'http://api.hubapi.com/contentsearch/v2/search?portalId=' . $portalId . '&term=a&type=BLOG_POST&type=LANDING_PAGE&type=SITE_PAGE&limit=' . $limit . '&offset=' . $offset . '&length=SHORT';
  $response = HubspotRequest::getHubspotData($reqUrl);
  $res = json_decode($response['response'], TRUE);
  $total = $res['total'];
  $results_count = count($res['results']);
  $results[] = $res['results'];
  $offset = $total - $results_count;

 //Continue to search if Total search count is greater than the API search limit
  while($results_count < $total){

      $reqUrl = 'http://api.hubapi.com/contentsearch/v2/search?portalId=' . $portalId . '&term=a&type=BLOG_POST&type=LANDING_PAGE&type=SITE_PAGE&limit=' . $limit . '&offset=' . $offset . '&length=SHORT';
      $response = HubspotRequest::getHubspotData($reqUrl);
      $res = json_decode($response['response'], TRUE);
      $results[] = $res['results'];
      $results_count += count($res['results']);
      $offset = $results_count;
  }

  //merge results arrays
  $GLOBALS['results'] =  call_user_func_array('array_merge', $results);

  die(include 'DialogLinks.php');
}

add_action('wp_ajax_hub2word_link', 'hub2word_link_content');

/**
 * Hubspot File managers
 * @return HTML
 */
function hub2word_file_folder()
{
  $reqUrl = "https://api.hubapi.com/filemanager/api/v2/folders";
  $response = HubspotRequest::getHubspotData($reqUrl);
  $GLOBALS['files'] = $response;
  include 'DialogFiles.php';
  die();
}

add_action('wp_ajax_hub2word_folder', 'hub2word_file_folder');

# get file from folder

function h2wp_folder_files() 
{
  $folder_id = $_POST['folder_id'];
  $folder_name = $_POST['folder_name'];
  $reqUrl = "http://api.hubapi.com/filemanager/api/v2/files";
  $output['status'] = "KO";
  $response = HubspotRequest::getHubspotFiles($reqUrl, $folder_id);
  if ($response) {
    $files = json_decode($response['response'], TRUE);
    $file_data = [];
    $i = 0;
    $output['f_name'] = $folder_name;
    if (count($files['objects']) > 0) {
      foreach ($files['objects'] as $key => $file) {
        $file_data[$i]['id'] = $file['id'];
        $file_data[$i]['name'] = $file['name'];
        $file_data[$i]['url'] = $file['url'];
        $file_data[$i]['alt_url'] = $file['alt_url'];
        $i++;
      }
      $output['status'] = "OK";
    }
    $output['result'] = $file_data;
  } else {
      $output['t_exp_message'] = "Token Missmatch!";
  }
  echo json_encode($output);
  die();
}

add_action('wp_ajax_h2w_files', 'h2wp_folder_files');