<!-- Template: woocommerce.php -->
<?php get_header(); ?>

<div id="storeContent">
 	<?php woocommerce_content(); ?>
 	<div id="backToStore"><a class="backToStore" href="<?php echo get_settings('home'); ?>/shop/"><< Back to Store</a></div>
</div>
<?php get_footer(); ?>