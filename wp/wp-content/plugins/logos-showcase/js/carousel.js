//$ = jQuery.noConflict();

	jQuery(document).ready(function($){

	//$ = jQuery.noConflict();	

		for (var key in lssliderparam) {			

			 var auto = (lssliderparam[key]['auto'] === 'true');
			 var pause = parseInt(lssliderparam[key]['pause']);
			 var autohover = (lssliderparam[key]['autohover'] === 'true');
			 var ticker = (lssliderparam[key]['ticker'] === 'true');
			 var tickerhover = (lssliderparam[key]['tickerhover'] === 'true');
			 var usecss = (lssliderparam[key]['usecss'] === 'true');
			 var autocontrols = (lssliderparam[key]['autocontrols'] === 'true');
			 var speed = parseInt(lssliderparam[key]['speed']);
			 var slidemargin = parseInt(lssliderparam[key]['slidemargin']);
			 var infiniteloop = (lssliderparam[key]['infiniteloop'] === 'true');
			 var pager = (lssliderparam[key]['pager'] === 'true');
			 var controls = (lssliderparam[key]['controls'] === 'true');
			 var slidewidth = parseFloat(lssliderparam[key]['slidewidth'])+20;
			 var minslides = parseInt(lssliderparam[key]['minslides']);
			 var maxslides = parseInt(lssliderparam[key]['maxslides']);
			 var moveslides = parseInt(lssliderparam[key]['moveslides']);

			 var sliderDiv = $(lssliderparam[key]['divid']);

			sliderDiv.fadeIn('slow');

		    sliderDiv.bxSlider({				
		    auto: auto,		
			pause: pause,
			autoHover: autohover,
			ticker: ticker,
			tickerHover: tickerhover,
			useCSS: usecss,
			autoControls: autocontrols,
			mode: 'horizontal',
			speed: speed,
			slideMargin: slidemargin,
			infiniteLoop: infiniteloop,
		    pager: pager, 
			controls: controls,
		    slideWidth: slidewidth,
		    minSlides: minslides,
		    maxSlides: maxslides,
		    moveSlides: moveslides  		

		
			});

		}  

		
	});

