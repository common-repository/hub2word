
( function() {

    // Register plugin
    tinymce.create( 'tinymce.plugins.hub2wordForm', {

        init: function( editor, url )  {

            // Add the Insert HubSpot button
            editor.addButton( 'hub2wordForm', {
                // text: 'Form',
                image: url + '/form.png',
                // icon: 'wp-menu-image dashicons-before dashicons-list-view',
                tooltip: 'Insert HubSpot Forms',
                cmd: 'open_h2w_form_modal',
            });

            // Called when we click the Insert HubSpot button
            editor.addCommand( 'open_h2w_form_modal', function() {
                // Calls the pop-up modal
                editor.windowManager.open({
                    // Modal settings
                    title: 'Insert a HubSpot Form',
                    // width: jQuery( window ).width() * 0.7,
                    // height: (jQuery( window ).height() - 36 - 50) * 0.7,
                    width: 500,
                    height: 400,
                    inline: 1,
                    id: 'hub2wordForm',
                    buttons: [{
                        text: 'Insert',
                        id: 'hub2wordForm-button-insert',
                        class: 'insert',
                        onclick: function( e ) {
                            var formId = jQuery( '#hubspotFormId' ).val();
                            wp.media.editor.insert('[hubspot_form id="' + formId + '"]');
                            // Close window
                            editor.windowManager.close();
                        },
                    },
                    {
                        text: 'Cancel',
                        id: 'hub2wordForm-button-cancel',
                        onclick: 'close'
                    }],
                });

                loadHubspotForms();

            });

        }

    });

    tinymce.PluginManager.add( 'hub2wordForm', tinymce.plugins.hub2wordForm );

    function loadHubspotForms () {
        var dialogBody = jQuery( '#hub2wordForm-body' ).html( '<div class="hub2word_loading"><object data="/wp-content/plugins/hub2word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/hub2word/assets/H2W2-spinner.gif" /></object>' );

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'html',
            data: {action: 'hub2word_form'},
            success: function(response) {
                dialogBody.html(response);
            },
            error: function() {

            }
        })
        .fail(function() {
            console.log("error");
        });
    }
})();

