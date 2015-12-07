<div style="clear: both"></div>
</div>
<!-- start footer -->
<a href="#top" class="footerTopArrow" title="to the top!"></a>
<div id="footer">
	<div id="footerWrapper">
		<div id="footerListWrapper">
	  		<div class="footerList">
	  			<div class="footerRoadRace">
	  				<h3>Road Bike Races</h3>
					<hr/>
					<ul>
						<?php echo do_shortcode('[pods name="road_races" orderby="race_date" template="Race Footer"]'); ?>
					</ul>
	  			</div>
			</div>
			<div class="footerList">
	  			<div class="footerMountainRace">
	  				<h3>Mountain Bike Races</h3>
					<hr/>
					<ul>
						<?php echo do_shortcode('[pods name="mountain_race" orderby="race_date" template="Race Footer"]'); ?>
					</ul>
	  			</div>
			</div>
			<div class="footerList">
	  			<div class="footerKidRace">
	  				<h3>Kid Bike Races</h3>
					<hr/>
					<ul>
						<?php echo do_shortcode('[pods name="kid_races" orderby="race_date" template="Race Footer"]'); ?>
					</ul>
	  			</div>
			</div>
			<div class="footerList">
	  			<div class="footerBRA">
	  				<h3>Blue Ridge Adventures</h3>
					<hr/>
						<?php wp_nav_menu( array('menu' => 'Footer Navigation' )); ?>
	  			</div>
			</div>
		</div>
		<div id="footerSocialWrapper">
			<div id="footerSocialIcons">
				<?php include('socialInclude.php'); ?>
			</div>
		</div> 
		<div id="subFooter">
			<div id="subFooterText">
				Blue Ridge Adventures Races are located in the Grandfather and Pisgah Ranger District of Pisgah National Forest and licensed under Special Use Permits from the US Forest Service. </br>
				&copy; <?php echo date('Y'); ?> <a href="<?php echo get_settings('home'); ?>">BlueRidgeAdventures.net</a>, all rights reserved, website developed by <a href="mailto:locke@lokkdesign.com">Lokk</a>.
			</div>
			<div id="subFooterLogo">
				<a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('template_url') ?>/images/BRA_footerLogo.png" title="Blue Ridge Adventures"/></a>
			</div>
		</div>
	</div>  
</div>
<!-- end footer -->
<?php wp_footer(); ?>
<script>
	function isiPhone(){
		return (
			(navigator.platform.indexOf("iPhone") != -1) ||
			(navigator.platform.indexOf("iPod") != -1)
		);
	}

	$(document).ready(function(){
		//$('#sidebar').stickyMojo({footerID: '#footer', contentID: '#subpageContent'});


		if($('.cart .event-addon').length){
			$('.cart').columnize({ columns: 2 });
		}

		/*
		if($('#content-wrapper').length){
			var content_position = $('#content-wrapper').position();
			var content_top = content_position.top;
			var content_height = $('#content-wrapper').outerHeight();
			var sidebar_height = $('#sidebar').outerHeight();
			var max_top = content_height - sidebar_height - 50;

			var animation_duration = 10;


			$( window ).scroll(function() {
				var body_scrolltop = $('body').scrollTop();
				var html_scrolltop = $('html').scrollTop();
				
				var scrolltop = 0;
				//It seems firefox uses html and other browsers use body for scrollTop?
				//So use whichever one is bigger?
				if(html_scrolltop > body_scrolltop){
					scrolltop = html_scrolltop;
				}
				else{
					scrolltop = body_scrolltop;
				}

				if(scrolltop > content_top){
					
					if(scrolltop - content_top > max_top){
						$('#sidebar').stop().animate({'top': max_top+'px'}, animation_duration);
					}
					else{
						$('#sidebar').stop().animate({'top': (scrolltop-content_top)+"px"},animation_duration);
					}
					
				}
				else{
					$('#sidebar').stop().animate({'top':'0px'},animation_duration);
				}

			});
		}
		*/

		if($('#content-wrapper').length){
			function relative_sticky(id, topSpacing){

				if(!topSpacing){ 
					var topSpacing = 0; 
				}

				var content_height = $('#content-wrapper').outerHeight();
				var sidebar_height = $('#sidebar').outerHeight();
				var max_top = content_height - sidebar_height - 50;

				var el_top = parseFloat(document.getElementById(id).getBoundingClientRect().top);
				el_top = el_top - parseFloat(document.getElementById(id).style.top);
				el_top = el_top * (-1);
				el_top = el_top + topSpacing;

				if(el_top > 0){
					if(el_top > max_top){
						document.getElementById(id).style.top = max_top + "px";
					}
					else{
						document.getElementById(id).style.top = el_top + "px";
					}
				} else{
					document.getElementById(id).style.top = "0px";
				}
			}

			var delay = (function(){
				var timer = 0;
				return function(callback, ms){
					clearTimeout (timer);
					timer = setTimeout(callback, ms);
				};
			})();

			window.onscroll = function(){
				if(isiPhone()){
					delay(function(){
						relative_sticky("sidebar", 30);
					}, 100);
				}
				else{
					relative_sticky("sidebar", 30);
				}
			}
			relative_sticky("sidebar", 30);
		}
	});
</script>
</body>
</html>