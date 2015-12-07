<!-- Template: single.php -->
<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="genericSubpageContent">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="posts-wrap grid_8">
  <div class="navigation" id="nav-single">

   
  </div>
  <div class="post" id="post-single">
    <h1 class="newstitle" id="entry-title-single">
      <?php the_title(); ?>
    </h1>
    <div class="entry-content" id="entry-content-single">
      <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
    </div>
    <!-- end entry-content -->
    <div class="meta"> Posted:
      <?php the_date(); ?>
      <?php edit_post_link(__('Edit This')); ?>
      <br />
      <?php _e("Filed under:"); ?>
      <?php the_category(',') ?>
      <?php wp_link_pages(); ?>
    </div>
    <!-- end entry-meta -->
  </div>
  <!-- end post -->
  <?php endwhile; else: ?>
  <?php _e('Sorry, no posts matched your criteria', 'blank'); ?>
  .
  <?php endif; ?>
</div>
</div>
<!-- end posts-wrap -->
<?php get_footer(); ?>