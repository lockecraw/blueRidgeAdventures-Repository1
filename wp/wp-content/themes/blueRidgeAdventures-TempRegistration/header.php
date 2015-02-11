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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.columnizer.js" type="text/javascript" charset="utf-8"></script>
<?php wp_head(); ?>
</head>
<body>
<!-- header START -->
<div class="container_12">
<div id="header-wrap">
	<div id="logo">
		<a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('template_url') ?>/images/BRA-logo-final.png" title="Blue Ridge Adventures"/></a>
	</div>	
    <ul id="nav">
        <?php wp_nav_menu( array( 'header-menu') ); ?>
    </ul>
</div>
<!-- header END -->
<div style="clear: both"></div>