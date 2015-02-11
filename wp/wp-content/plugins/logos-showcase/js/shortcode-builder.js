function lshowcaseshortcodegenerate() {
	
	var order = document.getElementById('orderby').value;
	var category = document.getElementById('category').value;
	var url = document.getElementById('activeurl').value;
	var style = document.getElementById('style').value;
	var layout = document.getElementById('interface').value;
	var tooltip = document.getElementById('tooltip').value;
	var limit = document.getElementById('limit').value;

	var img_code = "";
	var img_php = 0;
	var carousel = "";
	var php_carousel = "";

	if(document.getElementsByName('use_defaults')[1].checked) { 

		var autoscroll = document.getElementsByName('lshowcase_carousel_autoscroll')[0].value;
		var pausetime = document.getElementsByName('lshowcase_carousel_pause')[0].value;
		var pausehover = document.getElementsByName('lshowcase_carousel_autohover')[0].value;
		var autocontrols = document.getElementsByName('lshowcase_carousel_autocontrols')[0].value;
		var speed = document.getElementsByName('lshowcase_carousel_speed')[0].value;
		var margin = document.getElementsByName('lshowcase_carousel_slideMargin')[0].value;
		var loop = document.getElementsByName('lshowcase_carousel_infiniteLoop')[0].value;
		var pager = document.getElementsByName('lshowcase_carousel_pager')[0].value;
		var controls = document.getElementsByName('lshowcase_carousel_controls')[0].value;
		var minslides = document.getElementsByName('lshowcase_carousel_minSlides')[0].value;
		var maxslides = document.getElementsByName('lshowcase_carousel_maxSlides')[0].value;
		var slidesmove = document.getElementsByName('lshowcase_carousel_moveSlides')[0].value;

		var img = document.getElementsByName('lshowcase_image_size_overide')[0].value;
		if (img!="") { 
			img_code = " img='"+img+"'"; 
			img_php = img;
			} 

		carousel = "carousel='"+autoscroll+","+pausetime+","+pausehover+","+autocontrols+","+speed+","+margin+","+loop+","+pager+","+controls+","+minslides+","+maxslides+","+slidesmove+"'";
		php_carousel = autoscroll+","+pausetime+","+pausehover+","+autocontrols+","+speed+","+margin+","+loop+","+pager+","+controls+","+minslides+","+maxslides+","+slidesmove;
		
		}
	
	var shortcode = document.getElementById('shortcode');
	var php = document.getElementById('phpcode');
	
	shortcode.innerHTML = "[show-logos orderby='"+order+"' category='"+category+"' activeurl='"+url+"' style='"+style+"' interface='"+layout+"' tooltip='"+tooltip+"' limit='"+limit+"' "+carousel + img_code + "]";
	php.innerHTML = "&lt;?php echo build_lshowcase('"+order+"','"+category+"','"+url+"','"+style+"','"+layout+"','"+tooltip+"','"+limit+"','"+php_carousel+"','"+img_php+"'); ?&gt; ";
	
		
	var preview = document.getElementById('preview');
	
	
	
var data = {
		action: 'lshowcase',
		porder: order,
		pcategory:category,
		purl:url,
		pstyle:style,
		pinterface:layout,
		ptooltip:tooltip,
		plimit: limit,
		pimg: img_php
	};


	
jQuery.post(ajax_object.ajax_url, data, function(response) {
		preview.innerHTML=response;
		checkslider();
		checktooltip();
		
		
	});
	
	if(layout=="hcarousel") {
		var e = document.getElementById('ls_carousel_settings_option');
        e.style.display = 'block';
		}
	else {
		var e = document.getElementById('ls_carousel_settings_option');
        e.style.display = 'none';
        var e = document.getElementById('ls_carousel_settings');
        e.style.display = 'none';
	}


	hidecustomcarouselsettings();
	
}

 function hidecustomsettings() {
		var e = document.getElementById('ls_carousel_settings');
        e.style.display = 'none';

        var f = document.getElementById('ls_carousel_settings_option');
        f.style.display = 'none';

        lshowcaseshortcodegenerate();
    }

function showcustomsettings() {
		
	if(document.getElementsByName('use_defaults')[1].checked) { 		
		var e = document.getElementById('ls_carousel_settings');
		e.style.display = 'block';
		
	}
	lshowcaseshortcodegenerate();

}


function hidecustomcarouselsettings() {

	var autoscroll = document.getElementsByName('lshowcase_carousel_autoscroll')[0].value;

	var div_pause_time = document.getElementById('lst_pause_time');
	var div_pause_hover = document.getElementById('lst_pause_hover');
	var div_auto_controls = document.getElementById('lst_auto_controls');
	var div_carousel_settings = document.getElementById('lst_carousel_common_settings');

	div_pause_time.style.display = 'none';
	div_pause_hover.style.display = 'none';
	div_auto_controls.style.display = 'none';
	div_carousel_settings.style.display = 'none';

	if(autoscroll=="true") {
							div_pause_time.style.display = 'block'; 
							div_pause_hover.style.display = 'block'; 
							div_auto_controls.style.display = 'block'; 
							div_carousel_settings.style.display = 'block'; 
							}

	if(autoscroll=="false") {
							div_carousel_settings.style.display = 'block';  
							}

	if(autoscroll=="ticker") {
							div_pause_hover.style.display = 'block'; 
							div_pause_time.style.display = 'none';
							div_auto_controls.style.display = 'none';
							div_carousel_settings.style.display = 'none';
							}

}

showcustomsettings();