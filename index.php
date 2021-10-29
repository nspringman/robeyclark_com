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
		h1, h2, h3, h4, h5, h6, p, span {
			font-family: 'Times New Roman', serif;
		}
		.site-title {
			font-family: 'Times New Roman', serif;
			text-decoration: none;

		}
		.carousel-item {
			height: 500px;
		}
		.homepage-thumbnail:before{
			content: "";
			display: block;
			padding-top: 100%;  /* initial ratio of 1:1*/
		}
		.homepage-thumbnail {
			background-position: 50% 50%;
			background-repeat: no-repeat;
			background-size: cover;
			margin: 7px 0px;
		}
		.homepage-thumbnail:hover, .selected-thumbnail {
			-webkit-box-shadow: 0px 0px 0px 2px lightgrey; 
			box-shadow: 0px 0px 0px 2px lightgrey;
		}
		.category-title {
			position: relative;
		}
		.category-title span{
			position: absolute;
			bottom: 0;
			right: 14px;
			text-align: right;
			width: 100%;
		}
		.category-title:hover{
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		#next-slide-control {
			float: right;
		}
	</style>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="container-lg">
				<div class='row justify-content-center'>
					<div class='col-md-12'>
						<div id="carousel-container">
							<div id="top-carousel" class="carousel slide" data-bs-ride="carousel" data-interval="false">
								<div class="carousel-inner"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row justify-content-between">
					<div class="col-md-2 col-6">
						<div id="previous-slide-control">
							<span><</span>
							<span >Previous</span>
						</div>
					</div>
					<div class="col-md-8" id="work-details">
						<span id="work-title"></span>
						<span id="work-client"></span>
						<span id="work-year"></span>
						<span id="work-medium"></span>
						<span id="work-dimensions"></span>
						<span id="work-price"></span>
					</div>
					<div class="col-md-2 col-6">
						<div id="next-slide-control">
							<span>Next</span>
							<span>></span>
						</div>
					</div>
				</div>
				<div class='row'>
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
							<div class="col-md-2 homepage-thumbnail-wrapper category-title">
								<span><?php echo $category->name; ?></span>
							</div><?php
							// Load posts loop.
							while ( $category_query->have_posts() ) {
								$category_query->the_post();
								?>
									<div class="col-md-2
									homepage-thumbnail-wrapper" 
										data-post-id="<?php echo get_the_ID() ?>" >
										<div class="homepage-thumbnail" style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>"></div>
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
			
		</main><!-- .site-main -->
	</div><!-- .content-area -->
<script>
$(document).ready(function() {
	function setCarousel(postID) {
		const imageLink = `http://localhost:8888/robeyclark_com/wp-json/wp/v2/media?parent=${postID}`
		fetch(imageLink)
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
		
		const postMetaLink = `http://localhost:8888/robeyclark_com/wp-json/wp/v2/posts/${postID}`
		fetch(postMetaLink)
			.then(response => response.json())
			.then(json => {
				console.log(json)
				let workDetails = json.work_details
				$('#work-title').text(json.title.rendered ? json.title.rendered + ',' : '');
				$('#work-client').text(workDetails.client ? workDetails.client[0] + ',' : '');
				$('#work-year').text(workDetails.year ? workDetails.year[0] + ',' : '');
				$('#work-medium').text(workDetails.medium ? workDetails.medium[0] + ',' : '');
				$('#work-dimensions').text(workDetails.dimensions ? workDetails.dimensions[0] + ',' : '');
				$('#work-price').text(workDetails.price ? workDetails.price[0] : '');
			})
	}

    $('.homepage-thumbnail-wrapper')
		.on('click', function(event) {
			setCarousel($(this).data('post-id'))
		});
	const carousel = $('#top-carousel');
	carousel.carousel('pause');
	$('#previous-slide-control')
		.on('click', function(event) {
			carousel.carousel('prev');
		});
	$('#next-slide-control')
		.on('click', function(event) {
			carousel.carousel('next');
		});
});
</script>
<?php
get_footer();
