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
		* {
			font-family: 'Times New Roman', serif;
		}
		.site-title a {
			text-decoration: none;
		}
		.carousel-item {
			height: 500px;
		}
		.thumbnail-grid-container {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
		}
		.homepage-thumbnail-wrapper {
			width: 180px;
			height: 180px;
			background-position: 50% 50%;
			background-repeat: no-repeat;
			background-size: cover;
			margin: 10px;
		}
		.homepage-thumbnail-wrapper:hover, .selected-thumbnail {
			-webkit-box-shadow: 0px 0px 0px 2px lightgrey; 
			box-shadow: 0px 0px 0px 2px lightgrey;
		}
		.category-title {
			position: relative;
		}
		.category-title span{
			position: absolute;
			bottom: 0;
			right: 0;
			text-align: right;
			width: 100%;
		}
		.category-title:hover{
			-webkit-box-shadow: none;
			box-shadow: none;
		}
	</style>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="container-lg">
				<div class='row justify-content-center'>
					<div class='col-md-10'>
						<div id="carousel-container">
							<div id="top-carousel" class="carousel slide" data-bs-ride="carousel">
								<div class="carousel-inner"></div>
							</div>
						</div>
					</div>
				</div>
				<div class='row justify-content-center'>
					<div class='col-md-10'>
						<div class="thumbnail-grid-container">
							<?php
							$categories = get_categories();
							foreach($categories as $category) {
								$args = array(
									'post_type' => 'post',
									'post_status' => 'publish',
									'category_name' => $category->name,
									'posts_per_page' => 3
								);
								$category_query = new WP_Query( $args );
								if ( $category_query->have_posts() ) { ?>
									<div class="homepage-thumbnail-wrapper category-title">
										<span><?php echo $category->name; ?></span>
									</div><?php
									// Load posts loop.
									while ( $category_query->have_posts() ) {
										$category_query->the_post();
										?>
											<div class="homepage-thumbnail-wrapper" 
												style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>"
												data-post-id="<?php echo get_the_ID() ?>" >
											</div>
										<?php 
										
									}
		
									// // Previous/next page navigation.
									// twentynineteen_the_posts_navigation();
		
								} else {
		
									// If no content, include the "No posts found" template.
									get_template_part( 'template-parts/content/content', 'none' );
		
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
			
		</main><!-- .site-main -->
	</div><!-- .content-area -->
<script>
$( document ).ready(function() {
	function setCarousel(postID) {
		const link = `http://localhost:8888/robeyclark_com/wp-json/wp/v2/media?parent=${postID}`
		fetch(link)
			.then(response => response.json())
			.then(json => {
				console.log(json)
				const imageContainer = $('#top-carousel .carousel-inner');
				imageContainer.empty();
				json.forEach((image, idx) => {
					let divWrapper = $('<div></div>')
										.addClass(() => idx === 0 ? 'carousel-item active' : 'carousel-item')
					let imgElement = $('<img>')
										.attr('src', image.source_url)
										.attr('alt', image.alt_text)
										.addClass('d-block w-100')
					divWrapper.append(imgElement)
					imageContainer.append(divWrapper)
				})
			})
			.catch(err => console.error(err))
	}

    $('.homepage-thumbnail-wrapper')
		.on('click', function(event) {
			setCarousel($(this).data('post-id'))
		})
});
</script>
<?php
get_footer();
