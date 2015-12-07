<!-- Template: raceInclude.php -->
<div id="content">
 	<?php 
 		$postID = get_post( $id );
 		$postType = get_post_type( $postID ); 
 	?>
 	<div id="content-wrapper" class="clearfix">
 	<div id="sidebar">
 	<?php
 		$params = array( 
    		'where'=>'race_post_type_name.meta_value LIKE "%' . $postType . '%"'
		); 
		$relatedPod = pods($raceType)->find( $params );  
		while ( $relatedPod->fetch() ) { 
    		$raceLogoLink = $relatedPod->display( 'race_thumbnail._src.medium' ); 
    		echo ('<img class="sidebarLogo" src="'.$raceLogoLink.'"/>');
    		$registrationLink = $relatedPod->display( 'registration_link' );
    		$postTitle = $relatedPod->display( 'name' );
		}
		$args = array(
		'post_type'    => $postType,
		'post_status'  => 'publish',
		'sort_column'  => 'menu_order',
		'sort_order' => 'ASC',
		'title_li'     => '',
		'parent' => 0,
		);
		
		$racePages = get_pages( $args );
		echo '<ul id="subNav" class="'.$raceType.'">';
		foreach ($racePages as $racePage):?>
			<?php $currentID = get_the_ID(); ?>
			<li><a class="<?php if ($currentID == $racePage->ID) {echo 'active';}?>" href="<?php echo get_permalink($racePage);?>"><?php echo $racePage->post_title;?></a>
			<?php 
			$childArgs = array(
				'post_type'    => $postType,
				'post_status'  => 'publish',
				'order' => 'ASC',
				'title_li'     => '',
				'post_parent' => $racePage->ID,
			);
			$pageChildren = get_children($childArgs);
			
			if( count( $pageChildren ) != 0 ) { 
				if ($currentID == $racePage->ID) {
					
						echo '<ul>';
							foreach ($pageChildren as $pageChild):?>
								<li><a href="#<?php echo $pageChild->post_name; ?>" class="scroll"><?php echo $pageChild->post_title; ?></a></li>
							<?php endforeach; echo '</ul>';
				}
				else{
						echo '<ul>';
							foreach ($pageChildren as $pageChild):?>
								<li><a href="<?php echo get_permalink($racePage);?>#<?php echo $pageChild->post_name; ?>" class="scroll"><?php echo $pageChild->post_title; ?></a></li>
							<?php endforeach; echo '</ul>';
			
					}
			}
		endforeach;
		echo '</li>';
		//echo '<li><a href="'.$registrationLink.'" target="_blank">Online Registration</a></li>';
		echo '</ul>';
	?>	
 	</div>	 
   <div id="subpageContent">
    <?php
		// The Query
		 if (have_posts()) : while (have_posts()) : the_post();
			echo the_content();
			echo edit_post_link(__('Edit This'));
			$thispage=$post->ID;
		endwhile;
		endif;

		wp_reset_postdata();	
				
		/* The 2nd Query (without global var) */
		$args2 = array(
		'post_type'    => $postType,
		'post_status'  => 'publish',
		'orderby'  => 'menu_order',
		'post_parent'     => $thispage,
		'order' => 'ASC'
		);
		
		$query2 = new WP_Query( $args2 );

		// The 2nd Loop
		while( $query2->have_posts() ) {
			$query2->the_post();
			echo '<a id="'.$post->post_name.'"class="anchorPoints"></a>';
			echo '<hr class="'.$raceType.'"/>';
			echo '<h2 class="anchorTitle">' . get_the_title( $query2->post->ID ) . '</h2>';
			echo the_content();
			echo edit_post_link(__('Edit This'));
		}

		// Restore original Post Data
		wp_reset_postdata();

?>		

    </div>
	</div> <!-- /#content-wrapper -->   
	<div class="raceSposors">
		<div class="sponsorsSubText"><?php echo date('Y').' '.$postTitle; ?> Sponsors</div>
		<?php echo build_lshowcase('rand', $postType,'new','normal','grid','true','0','','0'); ?>
	</div> 
</div>