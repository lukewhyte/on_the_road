<?php get_header(); ?>

	<!-- Add Map Div -->
	<div id="mapWrap">
		<div class="single-post-map" id="map"></div>
	</div>

	<div id="container" class="clearfix">
	
		<!-- Add Blog Posts -->
		<main id="content">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			
					<h2><?php the_title(); ?></h2>
			
					<?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>
					
					<div class="navigation">
					  <div class="prev-posts"><?php previous_post('&laquo; &laquo; %', 'Previous Post', 'no'); ?></div>
					  <div class="nav-home-link"><a href="<?php echo home_url(); ?>">Home</a></div>
					  <div class="next-posts"><?php next_post('% &raquo; &raquo; ', 'Next Post', 'no'); ?></div>
					</div>

					<div class="entry clearfix">
				
						<?php the_content(); ?>

						<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
				
						<?php the_tags( 'Tags: ', ', ', ''); ?>

					</div>
			
					<?php edit_post_link('Edit this entry','','.'); ?>
			
				</div>

			<?php comments_template(); ?>

			<?php endwhile; endif; ?>
		
		</main>
	
		<?php get_sidebar(); ?>
		
	</div> <!-- container -->

<?php get_footer(); ?>