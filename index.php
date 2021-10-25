<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>
	<style>
		.thumbnail-grid-container {
			margin: 0 10%;
			display: flex;
			align-items: center;
		}

		.homepage-thumbnail-wrapper {
			width: 180px;
			height: 180px;
			background-position: 50% 50%;
			background-repeat: no-repeat;
			background-size: cover;
			margin: 10px;
		}
	</style>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<div class="thumbnail-grid-container">
			<?php
			if ( have_posts() ) {

				// Load posts loop.
				while ( have_posts() ) {
					the_post();
					?>
						<div class="homepage-thumbnail-wrapper" 
							style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>" >
						</div>
					<?php 
					
				}

				// // Previous/next page navigation.
				// twentynineteen_the_posts_navigation();

			} else {

				// If no content, include the "No posts found" template.
				get_template_part( 'template-parts/content/content', 'none' );

			}
			?>
		</div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->
<script></script>
<?php
get_footer();
