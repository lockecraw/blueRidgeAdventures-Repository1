<?php
/**
 * Template Name: Race Registration */
 get_header(); ?>
<div id="content" class="grid_8">
  <?php if (have_posts()) : while (have_posts()) : the_post();?>
  <div id="post-entries">
  <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
    <h1 class="storytitle"><?php the_title(); ?></h1>
<?php the_content() ?>
    <div class="clear"></div>
  </div>
  </div>
  <?php endwhile; else: ?>
  <p>
    <?php _e('Sorry, no posts matched your criteria.'); ?>
  </p>
  <?php endif; ?>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>