<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<title>
<?php if (is_home()) { echo bloginfo('name');
			} elseif (is_404()) {
			echo '404 Not Found';
			} elseif (is_category()) {
			echo 'Category:'; wp_title('');
			} elseif (is_search()) {
			echo 'Search Results';
			} elseif ( is_day() || is_month() || is_year() ) {
			echo 'Archives:'; wp_title('');
			} else {
			echo wp_title('');
			}
			?>
</title>
<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
<meta name="description" content="<?php bloginfo('description') ?>" />
<?php if(is_search()) { ?>
<meta name="robots" content="noindex, nofollow" />
<?php }?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,500,700,400italic|Kreon|Oswald' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.columnizer.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php bloginfo('template_url'); ?>/js/custom.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.cookie.js" charset="utf-8"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.hoverIntent.minified.js" charset="utf-8"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.dcjqaccordion.2.7.min.js" charset="utf-8"></script>
<script src="<?php bloginfo('template_url'); ?>/js/stickyMojo.js"></script>
<script type='text/javascript'>
	jQuery(document).ready(function() {
	jQuery("#menu-header-menu ul").css({display: "none"}); // Opera Fix
	jQuery("#menu-header-menu li").hover(function(){
			jQuery(this).find('ul:first').stop(true, true).slideDown(400);
			},function(){
			jQuery(this).find('ul:first').stop(true, true).delay(400).slideUp(400);
			});
	});   

$(document).ready(function($){
					$('#subNav').dcAccordion({
						eventType: 'click',
						autoClose: true,
						saveState: false,
						disableLink: false,
						speed: 'slow',
						showCount: false,
						autoExpand: true,
						cookie	: 'dcjq-accordion-1',
						classExpand	 : 'dcjq-current-parent'
					});
});
</script>

<?php wp_head(); ?>
</head>
<body>
<!-- header START -->

<div id="header-wrap">
	<div id="logo">
		<a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('template_url') ?>/images/BRA_Logo.png" title="Blue Ridge Adventures"/></a>
	</div>	
    <ul id="upperNav">
        <?php wp_nav_menu( array('menu' => 'Upper Header Navigation' )); ?>
    </ul>
    <div id="upperRightWrapper">
    	<?php include('socialInclude.php'); ?>
    	<?php echo do_shortcode('[google-translator]'); ?>
	</div>
    	<?php wp_nav_menu( array('menu' =>  'Header Menu') ); ?>
    <div id="mailchimp">
    	<?php echo do_shortcode('[mc4wp_form]'); ?>   
    </div>	
</div>
<!-- header END -->
<div style="clear: both"></div>
<div class="container_12">