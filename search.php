<?php get_header(); ?>

			<?php if (have_posts()) : ?>
			
				<!-- Map on/off radio buttons -->
				<?php include (TEMPLATEPATH . '/inc/map_toggle.php' ); ?>
	
				<!-- Add Map Div -->
				<div id="mapWrap">
					<div class="searchMap" id="map"></div>
				</div>

				<div id="container" class="clearfix">
	
					<!-- Add Blog Posts -->
					<main id="content">

				<h2>Search Results</h2>

				<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

						<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

						<?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>

						<div class="entry clearfix">
							<?php the_excerpt(); ?>
						</div>

					</div>

				<?php endwhile; ?>

				<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>

			<?php else : ?>
			
				<div id="container" class="clearfix">
	
					<!-- Add Blog Posts -->
					<main id="content">

						<h2>No posts found.</h2>

			<?php endif; ?>
			
		</main>

		<?php get_sidebar(); ?>
		
	</div> <!-- container -->

<?php get_footer(); ?>