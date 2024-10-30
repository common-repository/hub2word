<div class="container">
  <div class="row">
  <?php 
  if (!empty($message)) : ?>
      <div class="message notice <?php echo esc_attr($class); ?>">
          <p><?php echo esc_attr($message); ?></p>
      </div>
  <?php endif; ?>
  <div class="hbs_settings" style="width:100%;">     
      <div class="page-header">
        <h1><?php echo H2W_NAME; ?> <small>Plugin Configuration</small></h1>
        <p class="lead">Close the gap between HubSpot and Wordpress</p>
      </div>

        <div class="col-sm-6">
            <?php if (is_ssl()) { ?>
              <div class="hbs_settings_desc">
                  <?php
                  $response = $GLOBALS['token_info'];
                  if ($response):
                      ?>
                      <h4>You are currently connected with HubSpot</h4>
                      <p><div alt="f147" class="dashicons dashicons-yes" style="color:green"></div> Connected to: <strong><?php echo $response->hub_id; ?></strong> - <i><?php echo $response->hub_domain; ?></i></p>
                      <div class="hbs_settings_dis">
                          <button id="hub2word_disconnect" class="btn btn-danger">Disconnect Account</button>
                      </div>
                  <?php else: ?>
                      <h4>You are not connected with HubSpot</h4>
                      <span>Click the button below to connect your HubSpot account.</span>
                      <div class="hbs_settings_auth">
                          <button id="hub2word_authentication" class="btn btn-success">Click Here to Connect</button>
                      </div>
                  <?php endif; ?>
              </div>
            <?php } else { 
              if(!get_option('hub_id')){ ?>
                <div class="hbs_settings_api">
                  <label>Enter your HubSpot API Key</label>
                  <input type="text" id="h2w_api" value="<?php echo $GLOBALS['api_key']; ?>">
                  <button id="h2w_apiconnect" class="btn btn-success">Save API Key</button>
                </div>
              <?php } else { ?> 
                <div class="hbs_settings_dis">
                  <h4>You are currently connected with HubSpot</h4>
                  <p><div alt="f147" class="dashicons dashicons-yes" style="color:green"></div> Connected to: <strong><?php echo get_option('hub_id'); ?></strong></p>
                  <button id="hub2word_disconnect" class="btn btn-danger">Disconnect API Key</button>
                </div>
              <?php } 
              }?>
            <br />
            <br />


            <?php if($response || $GLOBALS['api_key']): ?>

              <div class="panel panel-default">
                <div class="panel-heading"><h4><?php echo H2W_NAME; ?> Settings</h4></div>
<!--                 <div class="panel-body">
                </div> -->
                <!-- List group -->
                <ul class="list-group">
                  <li class="list-group-item"><p>
                      <input id="h2w_search" name="include_search" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('include_search') ); ?> value="1">
                      <label for="h2w_search" id="include_search">Include HubSpot Content in WordPress Search Results</label>
                      <p>Hub2Word extends your standard WordPress search into HubSpot content. All HubSpot content related to your search is included in the WordPress search results.</p>                      
                  </li>
                  <li class="list-group-item" id="hs_search_items" style="display: none;">
                    <h5>Select the HubSpot items you would like to include in your WordPress search.</h5>
                    <p><input id="h2w_blogs" name="search_blogs" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('search_blogs') ); ?> value="1">
                      <label for="h2w_blogs" id="search_blogs">HubSpot Blog Posts</label>
                      <p>Check this box to include HubSpot Blogs in your WordPress search results</p>  
                    </p>
                    <p><input id="h2w_lpages" name="search_lpages" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('search_lpages') ); ?> value="1">
                      <label for="h2w_lpages" id="search_lpages">HubSpot Landing Pages</label>
                      <p>Check this box to include HubSpot Landing Pages in your WordPress search results</p>
                    </p>
                    <p><input id="h2w_pages" name="search_pages" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('search_pages') ); ?> value="1">
                      <label for="h2w_pages" id="search_pages">HubSpot Website Pages</label>
                      <p>Check this box to include HubSpot Website Pages in your WordPress search results</p>
                    </p>
                  <li class="list-group-item">
                    <h5>Shortcode Settings</h5>
                    <p><input id="h2w_sc_forms" name="include_forms" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('include_forms') ); ?> value="1">
                      <label for="h2w_sc_forms" id="include_forms">Include Forms shortcode</label>
                      <p>Check this box to add the HubSpot Forms shortcode generator to the WordPress content editor.</p>
                    </p>
                    <p><input id="h2w_sc_cta" name="include_cta" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('include_cta') ); ?> value="1">
                      <label for="h2w_sc_cta" id="include_cta">Include CTA shortcode</label>
                      <p>Check this box to add the HubSpot CTA shortcode generator to the WordPress content editor.</p>
                    </p>
                    <p><input id="h2w_sc_link" name="include_link" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('include_link') ); ?> value="1">
                      <label for="h2w_sc_link" id="include_link">Include Links shortcode</label>
                      <p>Check this box to add the HubSpot link generator to the WordPress content editor.</p>
                    </p>
                    <p><input id="h2w_sc_files" name="include_files" class="h2w-opt-btn" type="checkbox" <?php checked( get_option('include_files') ); ?> value="1">
                      <label for="h2w_sc_files" id="include_files">Include Files shortcode</label>
                      <p>Check this box to add the HubSpot File Manager to the WordPress content editor.</p>
                    </p>
                  </li>
                  <li class="list-group-item" id="settings_css">
                    <h5>Custom CSS</h5>
                    <p>Enter custom CSS below to apply your own styles to any of the Hub2Word elements.</p>
                    <p>
                      <div class="form-group">
                        <label for="exampleFormControlTextarea1">Enter CSS</label>
                        <textarea class="form-control" id="h2w_css" rows="3"><?php echo get_option('hbs_custom_css'); ?></textarea>
                      </div>
                      <button id="hub2word_csssave" class="btn btn-info">Save Styles</button>
                    </p>
                  </li>
                </ul>

              </div>

            <?php endif; ?>

            <p style="padding-top:40px;"><strong><?php echo H2W_NAME; ?></strong> <i>Version <?php echo H2W_VERSION; ?></i><br />
            Need Help? <a href="<?php echo H2W_SUPPORT; ?>" target="_blank">Contact Us</a></p>


      </div>
      <div class="col-sm-6">
        <div class="hbs_update_settings">
              <object style="max-width:250px;height:auto;margin:0 auto;" data="<?php echo H2W_IMG;?>logo.svg" type="image/svg+xml">
                <img style="max-width:250px;height:auto;;margin:0 auto;" src="<?php echo H2W_IMG;?>logo.png" />
              </object>
              <!--[if lte IE 8]>
              <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
              <![endif]-->
              <div style="max-width:250px;padding-top:40px;">
                <h4>Subscribe for updates!</h4>
                <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
                <script>
                  hbspt.forms.create({
                  portalId: "1887204",
                  formId: "3823d6ce-f5bf-4551-9fc7-f6b3742a6c10"
                });
                </script>
              </div>
        </div>
      </div>
    </div>
  </div>
</div>