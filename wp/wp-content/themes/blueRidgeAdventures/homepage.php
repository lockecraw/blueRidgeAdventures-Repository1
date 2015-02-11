<?php
/**
 * Template Name: Home Page */
 get_header(); ?>
<?php nivo_slider( "home-page" ); ?>
<div id="homepageSponsors">
	<div class="sponsorsText">Sponsors</div>
	<?php echo build_lshowcase('none','home-page','new','normal','hcarousel','false','0','true,4000,true,false,500,10,true,false,true,1,8,1','0'); ?>	
</div>
<div id="homeContentWrapper">
	<div id="homeContent">
 	 <?php if (have_posts()) : while (have_posts()) : the_post();?>
  		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1>Blue Ridge Adventures</h1>
			<p><?php echo get_excerpt(215); ?></p>
			<div class="moreContentInfo">
				<a title="About Blue Ridge Adventures" href="<?php echo get_option('home');?>/about/">More Info</a>
			</div>
 	   		<div class="meta"> 
      			<?php edit_post_link(__('Edit This')); ?>
    		</div>
  		</div>
  	<?php endwhile; else: ?>
  	<p>
		<?php _e('Sorry, no posts matched your criteria.'); ?>
	</p>
  	<?php endif; ?>
	</div>
	<div id="latestNews">
		<?php
			$args = array( 'posts_per_page' => 1 );
			$lastposts = get_posts( $args );
			foreach ( $lastposts as $post ) :
 				 setup_postdata( $post ); ?>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<p><?php echo get_excerpt(215); ?></p>
				<div class="moreContentInfo">
					<a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">More Info</a>
				</div>
 	   			<div class="meta"> 
      				<?php edit_post_link(__('Edit This')); ?>
    			</div>
				<?php endforeach; 
				wp_reset_postdata(); 
		?>
	</div>
	<div style="clear: both"></div>
	<div id="homeRaces">
		<div class="homeRaceTab">
			<div class="raceBlueTab">
				Road Bike Races
			</div>
			<?php echo do_shortcode('[pods name="road_races" orderby="race_date" template="Road Races Home"]'); ?>
		</div>
		<div style="clear: both"></div>
		<div class="homeRaceTab">
			<div class="raceGreenTab">
				Mountain Bike Races
			</div>
			<?php echo do_shortcode('[pods name="mountain_race" orderby="race_date" template="Mountain Races Home"]'); ?>
		</div>
		<div style="clear: both"></div>
		<div class="homeRaceTab">
			<div class="racePurpleTab">
				Kid Bike Races
			</div>
			<?php echo do_shortcode('[pods name="kid_races" orderby="race_date" template="Kids Races Home"]'); ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>