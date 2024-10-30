
( function() {

    // Register plugin
    tinymce.create( 'tinymce.plugins.hub2wordCTA', {

        init: function( editor, url )  {

            // Add the Insert CTA button
            editor.addButton( 'hub2wordCTA', {
                // text: 'Form',
                image: url + '/cta.png',
                // icon: 'wp-menu-image dashicons-before dashicons-download',
                tooltip: 'Insert a HubSpot CTA',
                cmd: 'open_h2w_CTA_modal'
            });

            editor.addCommand( 'open_h2w_CTA_modal', function() {
                // Calls the pop-up modal
                editor.windowManager.open({
                    // Modal settings
                    title: 'Insert a HubSpot CTA',
                    // body: [{
                    //     type: 'container',
                    //     html: "Hello world!"
                    // }],
                    width: 500,
                    height: 400,
                    inline: 1,
                    id: 'hub2wordCTA',
                    buttons: [{
                        text: 'Insert',
                        id: 'hub2wordCTA-button-insert',
                        class: 'insert',
                        onclick: function( e ) {
                            var ctaId = jQuery( '#hubspotCTAId' ).val();
                            wp.media.editor.insert('[hubspot_CTA id="' + ctaId + '"]');
                            // Close window
                            editor.windowManager.close();
                        },
                    },
                    {
                        text: 'Cancel',
                        id: 'hub2wordCTA-button-cancel',
                        onclick: 'close'
                    }],
                });

                loadHubspotCTA();

            });

        }

    });

    tinymce.PluginManager.add( 'hub2wordCTA', tinymce.plugins.hub2wordCTA );

    function loadHubspotCTA () {
        var dialogBody = jQuery( '#hub2wordCTA-body' ).html( '<div class="hub2word_loading"><object data="/wp-content/plugins/hub2word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/hub2word/assets/H2W2-spinner.gif" /></object>' );

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'html',
            data: {action: 'hub2word_cta'},
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

