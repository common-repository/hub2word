(function() {
    // Register plugin
    tinymce.create('tinymce.plugins.hub2wordLink', {
        init: function(editor, url) {
            editor.addButton('hub2wordLink', {
                // text: 'Link',
                //icon: 'wp-menu-image dashicons-before dashicons-megaphone',
                tooltip: 'Insert a HubSpot Link',
                image: url + '/network.png',
                cmd: 'open_h2w_link_modal',
                // onclick: function() {
                //     tb_show('Insert Product Link', '/wp-content/plugins/hub2word/hub2wordForm_link.php?&height=300&width=600&TB_iframe=true');
                // }
            });
            editor.addCommand('open_h2w_link_modal', function() {
                // Calls the pop-up modal
                editor.windowManager.open({
                    // Modal settings
                    title: 'Insert a HubSpot Link',
                    // width: jQuery(window).width() * 0.7,
                    width: 700,
                    // minus head and foot of dialog box
                    // height: (jQuery(window).height() - 36 - 50) * 0.7,
                    height: 400,
                    inline: 1,
                    id: 'hub2wordLink',
                    buttons: [{
                        text: 'Add Link',
                        id: 'hub2wordLink-button-insert',
                        class: 'insert',
                        onclick: function(e) {
                            e.preventDefault();
                            // var selected_text = editor.selection.getContent();
                            var selected_text = jQuery("#hbs_link_text").val();
                            var target_prop  = "";
                            if(jQuery("#hbs_link_check").is(':checked')){
                                var target_prop = 'target="_blank"';
                            }
                            var return_text = "";
                            var targetURL = jQuery("#hbs_url").val();
                            return_text = '<a href="' + targetURL + '" '+ target_prop +' class="hubspot-link">' + selected_text + '</span>';
                            editor.execCommand('mceInsertContent', false, return_text);
                            tb_remove();
                            // Close window
                            editor.windowManager.close();
                        },
                    }, {
                        text: 'Cancel',
                        id: 'hub2wordLink-button-cancel',
                        onclick: 'close'
                    }],
                });
                loadHubspotLinks();
            });
        }
    });
    tinymce.PluginManager.add('hub2wordLink', tinymce.plugins.hub2wordLink);

    function loadHubspotLinks() {
        var dialogBody = jQuery('#hub2wordLink-body').html( '<div class="hub2word_loading"><object data="/wp-content/plugins/hub2word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/hub2word/assets/H2W2-spinner.gif" /></object>' );
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'html',
            data: {
                action: 'hub2word_link'
            },
            success: function(response) {
                dialogBody.html(response);
                // var selected_text = editor.selection.getContent();
                // jQuery("#hbs_link_text").val(selected_text);
            },
            error: function() {}
        }).fail(function() {
            console.log("error");
        });
    }
})();
