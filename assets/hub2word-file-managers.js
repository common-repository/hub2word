(function() {
    // Register plugin
    tinymce.create('tinymce.plugins.hub2wordFile', {
        init: function(editor, url) {
            // Add the Insert H2W File button
            editor.addButton('hub2wordFile', {
                // text: 'Form',
                // icon: 'wp-menu-image dashicons-before dashicons dashicons-media-default',
                image: url + '/folders.png',
                tooltip: 'HubSpot File Manager',
                cmd: 'open_h2w_file_modal',
                // onclick: function() {
                //     tb_show('Insert Product Link', '/wp-content/plugins/hub2wordForm/hub2wordForm_link.php?&height=300&width=600&TB_iframe=true');
                // }
            });
            // Called when we click the Insert H2W File button
            editor.addCommand('open_h2w_file_modal', function() {
                // Calls the pop-up modal
                
                editor.windowManager.open({
                    // Modal settings
                    title: 'HubSpot File Manager',
                    width: jQuery(window).width() * 0.8,
                    height: (jQuery(window).height() - 36 - 50) * 0.7,
                    //width: 700,
                    // minus head and foot of dialog box
                    // height: (jQuery(window).height() - 36 - 50) * 0.7,
                    //height: 400,
                    inline: 1,
                    id: 'hub2wordFile',
                    buttons: [{
                        text: 'Insert into post',
                        id: 'hub2wordForm-button-insert',
                        class: 'insert',
                        onclick: function(e) {
                           
                            var return_text = "";
                            jQuery("li.hbs_img_item").each(function() { // loop through all li
                                if (jQuery(this).hasClass("selected")) { // check if li has active class
                                    var fileUrl = jQuery(this).data("fileurl");
                                    var fileName = jQuery(this).data("filename");

                                    if (isImageType(fileUrl)) {
                                        return_text += '<img class="alignnone size-full" src="' + fileUrl + '" alt="' + fileName + '">';
                                    } else {
                                        return_text += '<a href="' + fileUrl + '" target="_blank">' + fileName + '</a>';
                                    }
                                }
                            });
                            // var targetURL = jQuery("#hbs_url").val();
                            editor.execCommand('mceInsertContent', false, return_text);
                            tb_remove();
                            // Close window
                            editor.windowManager.close();
                        },
                    }, {
                        text: 'Cancel',
                        id: 'hub2wordForm-button-cancel',
                        onclick: 'close'
                    }],
                });
                loadHubspotFiles();
            });
        }
    });
    tinymce.PluginManager.add('hub2wordFile', tinymce.plugins.hub2wordFile);

    function loadHubspotFiles() {
        var dialogBody = jQuery('#hub2wordFile-body').html( '<div class="hub2word_loading"><object data="/wp-content/plugins/hub2word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/hub2word/assets/H2W2-spinner.gif" /></object>' );
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'html',
            data: {
                action: 'hub2word_folder'
            },
            success: function(response) {
                dialogBody.html(response);
                 var height = jQuery('#hub2wordFile-body').height() - 45;
                jQuery('.hbs_folder_container').height(height);
            },
            error: function() {
                // body
            }
        }).fail(function() {
            console.log("error");
        });

    }

    function isImageType(url) {
        return(url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }
})();