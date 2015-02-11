				
// Scroll to top
jQuery(document).ready(function()
{
	$("a[href='#top']").click(function() {
  	$("html, body").animate({ scrollTop: 0 }, "slow");
  	return false;
	});
	$('a[href*=#]').click(function(){
		$('html, body').animate({
    	scrollTop: $( $.attr(this, 'href') ).offset().top
		}, 500);
		return false;
});	
});