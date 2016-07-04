jQuery(document).ready(function(){  

    // https://developers.google.com/maps/documentation/geocoding/
    // requires wp_enqueue_script("googlemaps_js", "https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"); for geolocation

    console.log("Document ready");

/*
    jQuery('#wpfancyboxopener').fancybox({ 
	autoResize:true,
	autoCenter:true 
    }); 
*/
    jQuery('.custom_media_upload').on('click', open_media_dialog());

    var get_loc_fn = function(){
	return function(latitude, longitude){
	    console.log('Latitude:'+latitude);
	    console.log('Longitude:'+longitude);
	}
    }
//    getLatitudeAndLongitude(get_loc_fn())(null);

	// .upload_logo is a link or button
	jQuery( '.wpstocks_upload_logo' ).click( function( e ) {
		e.preventDefault();
		// hat tip: http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
		var logo_uploader = wp.media( {
			title: 'Select a logo image',
			button: {
				text: 'Upload'
			},
			multiple: false
		} )
			.on( 'select', function() {
				var attachment = logo_uploader.state().get( 'selection' ).first().toJSON();
				jQuery( '.wpstocks_logomedia_image' ).attr( 'src', attachment.url );
				jQuery( '.wpstocks_logo_media_url' ).val( attachment.url );
				jQuery( '.wpstocks_logo_media_id' ).val( attachment.id );
			} )
			.open();
	} );

});


var getLatitudeAndLongitude = function(callback_fn){
    return function(e){
	if(navigator.geolocation) {
	    console.log('Navigator:');
	    console.log(navigator);
	    navigator.geolocation.getCurrentPosition(
		getPositionCallback(),
		handleNoGeolocation(callback_fn),{ enableHighAccuracy: true});
	}
	else{
	    console.log('No navigator found');
	}
    }
}

var getPositionCallback = function(){
    return function(position){
	//    navigator.geolocation.watchPosition(function(position) 
	var latitude = position.coords.latitude; 
	var longitude = position.coords.longitude;
	var geocode_fn = get_geocode_callback();
	get_geocode(latitude+","+longitude, geocode_fn(latitude+","+longitude));
    }
}

var get_geocode = function( address, callback){
    var geocoder = new google.maps.Geocoder();
    var cb = callback(geocoder);
    geocoder.geocode( { 'address': address}, cb );
}

var get_geocode_callback = function(){
    return function(latlng){
	return function(geocoder){
	    return function(results, status){
		if (status == google.maps.GeocoderStatus.OK) {
		    // 5 is country, 6 is post code
//		    var loc = results[0].address_components[1].short_name+', '+results[0].address_components[2].short_name+', '+results[0].address_components[3].short_name;
		    console.log("Your location (approx): ")
		    console.log(results[0].address_components);
		    console.log(results);
		}
		else{
		    console.log('Geocode was not successful for the following reason: ' + status);
		}
	    }
	}
    }
}    

var handleNoGeolocation = function() {
    return function(errorFlag){
	var content = '';
	if (errorFlag) {
	    console.log('Error: The Geolocation service failed.');
	} else {
	    console.log( 'Error: Your browser doesn\'t support geolocation.');
	}
    }
}

var open_media_dialog = function(){
    return function(e){
	/*
	  See plugins/ytp/ for usage
	  Requires:
	  if(function_exists( 'wp_enqueue_media' )){
	  wp_enqueue_media();
	  }else{
	  wp_enqueue_style('thickbox');
	  wp_enqueue_script('media-upload');
	  wp_enqueue_script('thickbox');
	  }
Example:
               <div class="col-sm-7">
<?php
if(function_exists( 'wp_enqueue_media' )){
    wp_enqueue_media();
}else{
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
}
?>
    <!-- http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options -->
                    <button class="btn btn-primary btn-large custom_media_upload" type="button">Select image</button>
                    <img class="custom_media_image" src="" style="width:100%;" />
                    <input class="custom_media_image" value="" type="hidden" name="ytp_banner_imgsrc" />
               </div>
         </div>

	*/
	if(e){
	    console.log('Preventing default action');   
	    e.preventDefault(); //STOP default action
	}

	console.log('uploading image');
	console.log(wp);

	var send_attachment_bkp = wp.media.editor.send.attachment;

	wp.media.editor.send.attachment = function(props, attachment) {

            jQuery('.custom_media_image').attr('src', attachment.url);
            jQuery('.custom_media_url').val(attachment.url);
            jQuery('.custom_media_id').val(attachment.id);

            wp.media.editor.send.attachment = send_attachment_bkp;
	}

	wp.media.editor.open();

	return false;       

    }
}

var valid_url = function(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  if(!pattern.test(str)) {
    return false;
  } else {
    return true;
  }
}
