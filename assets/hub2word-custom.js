jQuery(document).ready(function ($) {
    /* authentication event */
  $('.hbs_settings_api').on('click', '#h2w_apiconnect', function (event) {
    console.log('hello');
    var api_key = $('#h2w_api').val();
    console.log(api_key);
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'hub2word_apiKey',
        api_key: api_key
      },
      beforeSend: function (xhr) {
        $('#h2w_apiconnect').css('pointer-event', 'none');
      },
      success: function (data, textStatus, jqXHR) {
        if (data.status === 'Ok') {
            console.log(data);
            location.reload();
        }
      }
    });
  });

	/* authentication event */
  $('.hbs_settings_auth').on('click', '#hub2word_authentication', function (event) {
  	console.log('hello');
    var client_id = $('#client-id').val(),
      client_secret = $('#client-secret').val();
    if (client_id === '' || client_secret === '') {
      $('.error').show().html('Before Insert/Update authentication not available.');
      return false;
    }
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'hub2word_auth'
      },
      beforeSend: function (xhr) {
        $('#hub2word_authentication').css('pointer-event', 'none');
      },
      success: function (data, textStatus, jqXHR) {
        if (data.status === 'Ok') {
            window.location.href = data.auth_url;
        }
      }
    });
	});

  $('.hbs_settings_dis').on('click', '#hub2word_disconnect', function (event) {
    var client_id = $('#client-id').val(),
      client_secret = $('#client-secret').val();
    if (client_id === '' || client_secret === '') {
      $('.error').show().html('Before Insert/Update authentication not available.');
      return false;
    }
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'hub2word_disconnect'
      },
      beforeSend: function (xhr) {
        $('#hub2word_disconnect').css('pointer-event', 'none');
      },
      success: function (textStatus, jqXHR) {
            location.reload();
      }
    });
  });

  /* show/hide child search settings */
  checkbox = $('#h2w_search'),
  chShipBlock = $('#hs_search_items');

  if($(checkbox).is(':checked')){
    chShipBlock.show();
  }

  checkbox.on('click', function() {
     if($(this).is(':checked')) {
        chShipBlock.show();
     } else {
        chShipBlock.hide();
     }
  });

  /* update optoin meta */
  $('.h2w-opt-btn').on('click', function() {
     if($(this).is(':checked')) {

        var labelId = $(this).attr("id");

        $.ajax({
          url      : ajaxurl,
          type     : 'POST',
          dataType : 'json',
          data : {
            action:'Hub2Word_save_settings',
            opt_name: $(this).attr("name"),
            opt_value: 1
          },
          beforeSend: function (xhr) {
            $('.h2wsuccess').hide();
            $('label[for="' + labelId + '"]').after('<span class="h2wloader"></span>');
          },
          success: function (data, textStatus, jqXHR) {
              $('.h2wloader').hide();

              $('label[for="' + labelId + '"]').after('<span class="h2wsuccess"> Saved<div alt="f147" class="dashicons dashicons-yes"></div></a>');
              $('.h2wsuccess').delay(3000).fadeOut(300);

            if (data.status === 'OK') {
              $('.error').html(data.message);

            } else if (data.status === 'KO') {
              $('.error').html(data.v_error);
            } 
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log('Something went wrong!');
          }
        });

     } else {

        var labelId = $(this).attr("id");

        $.ajax({
          url      : ajaxurl,
          type     : 'POST',
          dataType : 'json',
          data : {
            action:'Hub2Word_save_settings',
            opt_name: $(this).attr("name"),
            opt_value: 0
          },
          beforeSend: function (xhr) {
            $('.h2wsuccess').hide();
            $('label[for="' + labelId + '"]').after('<span class="h2wloader"></span>');
          },
          success: function (data, textStatus, jqXHR) {

              $('.h2wloader').hide();

              $('label[for="' + labelId + '"]').after('<span class="h2wsuccess"> Saved<div alt="f147" class="dashicons dashicons-yes"></div></a>');
              $('.h2wsuccess').delay(3000).fadeOut(300);

            if (data.status === 'OK') {
              $('.error').html(data.message);

            } else if (data.status === 'KO') {
              $('.error').html(data.v_error);
            } 
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log('Something went wrong!');
          }
        });

     }
  });

  /* update custom styles */
    $('#hub2word_csssave').click(function() {
    
    var css = $('#h2w_css').val();

    console.log(css);
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'Hub2Word_save_settings',
        opt_name: 'hbs_custom_css',
        opt_value: String(css)
      },
      beforeSend: function (xhr) {
        $('.h2wsuccess').hide();
        $('#hub2word_csssave').after('<span class="h2wloader"></span>');
      },
      success: function (data, textStatus, jqXHR) {

            console.log(data);

          $('.h2wloader').hide();

          $('#hub2word_csssave').after('<span class="h2wsuccess"> Saved<div alt="f147" class="dashicons dashicons-yes"></div></a>');
          $('.h2wsuccess').delay(3000).fadeOut(300);

        if (data.status === 'OK') {
          $('.error').html(data.message);

        } else if (data.status === 'KO') {
          $('.error').html(data.v_error);
        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log('Something went wrong!');
      }
    });
  });


	/* end */

});