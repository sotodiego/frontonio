jQuery(document).ready( function($) {
  jQuery("select.select2").select2({
    placeholder: "Seleccione un elemento",
    allowClear: true
  });
  jQuery('.select2-search__field').addClass("browser-default");
  jQuery('#icon_apple').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar Icon Apple',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#icon_apple_v').val(attachment.attributes['url']);
        jQuery('#icon_apple_vn').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#icon_apple_vn').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery('#logo').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar logo',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#logo_v').val(attachment.attributes['url']);
        jQuery('#logo_vn').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#logo_vn').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery('#logo_blanco').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar logo para menú',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#logo_blanco_v').val(attachment.attributes['url']);
        jQuery('#logo_blanco_vn').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#logo_blanco_vn').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery('#favicon').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar Favicon',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#favicon_v').val(attachment.attributes['url']);
        jQuery('#favicon_vn').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#favicon_vn').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery('#bglogin').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar Imagen de fondo para Login',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#bglogin_vn').val(attachment.attributes['url']);
        jQuery('#bglogin_v').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#bglogin_v').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery('#lglogin').click(function(e) {
    e.preventDefault();
    var image_frame;
    if(image_frame){
      image_frame.open();
    }else{
      image_frame = wp.media({
        title: 'Seleccionar Imagen de fondo para Login',
        multiple : false,
        library : {
          type : 'image',
        }
      });

      image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        var attachment = selection.models[0];
        jQuery('#lglogin_vn').val(attachment.attributes['url']);
        jQuery('#lglogin_v').val(attachment['id']);
      });

      image_frame.on('open',function() {
        var selection =  image_frame.state().get('selection');
        var id = jQuery('#lglogin_v').val();
        if(id){
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add( attachment ? [ attachment ] : [] );
        }
      });
      image_frame.open();
    }
  });
  jQuery("#botton_fondo, #botton_tono, #botton_color, #botton_color_t").on('select2:select select2:open', function(e){
    var f = $("#botton_fondo").val();
    var t = $("#botton_tono").val();
    var c = $("#botton_color").val();
    var ct = $("#botton_color_t").val();
    $("#test_button").removeClass().addClass("btn").addClass(f).addClass(t).addClass(ct).addClass(c);
  })
  jQuery("#test_button").click(function(e){
    e.preventDefault();
  })

});
