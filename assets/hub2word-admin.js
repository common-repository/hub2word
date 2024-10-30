var hub2wordApp = {
  openFiles: function(){
    if (this.window === undefined) {
      this.window = wp.media({
        title: 'Insert a media',
        library: {type: 'image'},
        multiple: false,
        button: {text: 'Insert'}
      });

      var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var first = self.window.state().get('selection').first().toJSON();
        wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
      });
    }

    this.window.open();
    return false;
  }
};
jQuery(function($) {
  $(document).ready(function(){
    $('#insertHub2WordFiles').click(hub2wordApp.openFiles); 
  });

});

jQuery(function ($) {

    jQuery('#hub2wordForm-button-insert').addClass('avoid-clicks');

    jQuery(document).on('click', '.hbs-folder', function () {
        var folder_id = $(this).data('id');
        var folder_name = $(this).data('name');
        var dialogBody = jQuery('#hub2wordFile-body').html('<div class="hub2word_loading"><object data="/wp-content/plugins/Hub2Word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/Hub2Word/assets/H2W2-spinner.gif" /></object>');
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {action: 'h2w_files', folder_id: folder_id, folder_name: folder_name},
            success: function (response) {
                if (response.status === 'OK') {
                    var data = response.result;
                    var defaultImg = '/wp-content/plugins/Hub2Word/assets/file.png';
                    var html_view = '<div class="h2w_file"><div class="hbs_breadcrumb">\
                        <ul>\
                            <li class="hbs_back_btn"><a href="javascript:void(0)">Main Folder</a></li>\
                            <li class="active">' + response.f_name + '</li>\
                        </ul>\
                    </div>\
                    <div class="file_manager_modal">\
                        <div class="hbs_folder_container" style="overflow-y: scroll;">\
                           <ul class="hbs_folder_list">';

                        jQuery.each(data, function (index, item) {
                            var fileName = item.url;
                            var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1); 

                            html_view += '<li class="hbs_img_item" data-fileurl = "' + item.url + '" data-filename = "' + item.name + '">\
                                    <a href="javascript:void(0)" alt="' + item.name + '">';
                            if (item.url.match(/\.(jpeg|jpg|gif|png)$/) != null) {
                                html_view += '<span class="h2w-file-type">' + fileExtension.toUpperCase() +'</span><img class="h2w-file-img" src="' + item.url + '" alt="' + item.name.split('_') + '"><span class="h2w-file-name">' + item.name +'</span>';
                            } else {
                                html_view += '<span class="h2w-file-type">' + fileExtension.toUpperCase() +'</span><img class="h2w-file-img" src="' + defaultImg + '" alt="' + item.name.split('_') + '"><span class="h2w-file-name">' + item.name +'</span>';
                            }
                            html_view += '</a>\
                                <button type="button" class="button-link check" tabindex="0">\
                                    <span class="media-modal-icon"></span><span class="screen-reader-text"></span>\
                                </button>\
                                </li>';
                        });

                        html_view += "</ul>\
                            </div>\
                        </div>\
                    </div>";

                    dialogBody.show().html(html_view);
                    var height = $('#hub2wordFile-body').height() - 57;
                    $('.hbs_folder_container').height(height);
                }
            },
            error: function () {

            }
        });
    });

    jQuery(document).on('click', '.hbs_img_item', function () {
        if (jQuery(this).hasClass('selected')) {
            jQuery(this).removeClass('selected');
        } else {
            jQuery(this).addClass('selected');
        }

        if (jQuery('.hbs_folder_list li.selected').length === 0) {
            jQuery('#hub2wordForm-button-insert').addClass('avoid-clicks');
        } else {
            jQuery('#hub2wordForm-button-insert').removeClass('avoid-clicks');
        }
    });

    jQuery(document).on('click', '.hbs_back_btn', function () {
        var dialogBody = jQuery('#hub2wordFile-body').html('<div class="hub2word_loading"><object data="/wp-content/plugins/Hub2Word/assets/H2W-spinner.svg" type="image/svg+xml"><img src="/wp-content/plugins/Hub2Word/assets/H2W2-spinner.gif" /></object>');
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'html',
            data: {
                action: 'hub2word_folder'
            },
            success: function (response) {
                dialogBody.html(response);
            },
            error: function () {}
        }).fail(function () {
            console.log("error");
        });
    });
});
