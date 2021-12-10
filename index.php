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
// $latest_cpt = get_posts("category=11&numberposts=1");
// echo $latest_cpt[0]->ID
?>
	<style>
		h1, h2, h3, h4, h5, h6, p, span {
			font-family: 'Times New Roman', serif;
			color: #777 !important;
		}
		.site-title {
			margin-top: 1em;
			font-size: 1em;
		}
		.carousel-item {    
			background-color: #ddd;
    		padding: 10px;
			position: relative;
			height: 100%;
		}
		.carousel-item img {    
			position: relative;
			top: 50%;
			transform: translateY(-50%);
			max-width: 80%;
			max-height: 100%;
			margin: auto;
		}
		.homepage-thumbnail:before {
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
		.category-title:hover {
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		#carousel-container {
			position: relative;
		}
		.carousel-inner {
			background-color: #ddd;
			height: 500px;
		}
		#work-description-wrapper {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			overflow-y: scroll;
			width: 100%;
			height: 0;
			background-color: rgba(255,255,255,0.8);
			height: 0;
			width: 100%;
			transition: .5s;
		}
		.show-slide-up {
			height: 100% !important;
		}
		#work-description {
			width: 80%;
			margin: auto;
			font-size: 1.3em;
			margin-top: 4em;
		}
		#work-details {
			text-align: center;
		}
		#previous-slide-control, #next-slide-control {
			cursor: pointer;
		}
		#next-slide-control {
			float: right;
		}
		#work-more-control {
			text-decoration: underline;
			cursor: pointer;
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
							<div id="work-description-wrapper">
								<p id="work-description"></p>
							</div>
						</div>
					</div>
				</div>
				<div class="row justify-content-between">
					<div class="col-md-2 col-6">
						<div id="previous-slide-control">
							<
							<!-- <span>Previous</span> -->
						</div>
					</div>
					<div class="col-md-8 order-md-2 order-3" id="work-details">
						<span id="work-info"></span>
						<span id="work-more"></span>
					</div>
					<div class="col-md-2 col-6 order-md-3 order-2">
						<div id="next-slide-control">
							<!-- <span>Next</span> -->
							>
						</div>
					</div>
				</div>
				<div class='row'>
					<?php

					function get_posts_years_array() {
						global $wpdb;
						$result = array();
						$post_type = 'post';
						$years = $wpdb->get_results( // https://wordpress.stackexchange.com/questions/145148/get-list-of-years-when-posts-have-been-published/273627
							$wpdb->prepare(
								"SELECT YEAR(post_date) FROM {$wpdb->posts} WHERE post_status = 'publish' and post_type='%s' GROUP BY YEAR(post_date) DESC",
								$post_type
							),
							ARRAY_N
						);
						if ( is_array( $years ) && count( $years ) > 0 ) {
							foreach ( $years as $year ) {
								$result[] = $year[0];
							}
						}
						return $result;
					}
					$years = array_reverse(get_posts_years_array());
					foreach($years as $year) {
						$args = array(
							'post_type' => 'post',
							'post_status' => 'publish',
							'date_query' => array(
								array(
									'year'  => $year
								),
							),
							'category_name' => 'portfolio',
							'posts_per_page' => -1
						);
						$year_query = new WP_Query( $args );
						if ( $year_query->have_posts() ) { ?>
							<div class="col-md-2 col-xs-4 homepage-thumbnail-wrapper category-title">
								<span><?php echo $year; ?></span>
							</div><?php
							// Load posts loop.
							while ( $year_query->have_posts() ) {
								$year_query->the_post();
								?>
									<div class="col-md-2 col-xs-4 homepage-thumbnail-wrapper">
										<div class="homepage-thumbnail" data-post-id="<?php echo get_the_ID() ?>" style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>"></div>
									</div>
								<?php 
								
							}

						} else {
							// If no content, include the "No posts found" template.
							get_template_part( 'template-parts/content/content', 'none' );
						}
					}



					$categories = get_categories(array('child_of' => 11)); // TODO: ID will be different when transferring to new site
					foreach($categories as $category) {
						$args = array(
							'post_type' => 'post',
							'post_status' => 'publish',
							'category_name' => $category->name,
							'posts_per_page' => -1
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
									<div class="col-md-2 homepage-thumbnail-wrapper">
										<div class="homepage-thumbnail" data-post-id="<?php echo get_the_ID() ?>" style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>"></div>
									</div>
								<?php 
								
							}

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
	let nextPostWrapper = null;
	let previousPostWrapper = null;
	let currentPostWrapper = null;
	const carousel = $('#top-carousel');
	const nextSlideControl = $('#next-slide-control');
	const previousSlideControl = $('#previous-slide-control');
	const nextItemControl = $('#next-item-control');
	const previousItemControl = $('#previous-item-control');
	let numSlides = 0;

	carousel.carousel({
        pause: true,
        interval: false
    });

	function setCarousel(postID) {
		nextSlideControl.text('>');
		previousSlideControl.text('|<');
		$('#work-description-wrapper').removeClass('show-slide-up')
		const imageLink = `http://localhost:8888/robeyclark_com/wp-json/wp/v2/media?parent=${postID}`
		fetch(imageLink)
			.then(response => response.json())
			.then(json => {
				const imageContainer = $('#top-carousel .carousel-inner');
				imageContainer.empty();
				numSlides = 0;
				json.forEach((image, idx) => {
					let divWrapper = $('<div></div>')
										.addClass(() => idx === 0 ? 'carousel-item active' : 'carousel-item')
					let imgElement = $('<img>')
										.attr('src', image.source_url)
										.attr('alt', image.alt_text)
										.addClass('d-block')
					divWrapper.append(imgElement)
					imageContainer.append(divWrapper)
					numSlides++;
				})
				handleSlideNavigationIcons('reset');
			})
			.catch(err => console.error(err))
		
		const postMetaLink = `http://localhost:8888/robeyclark_com/wp-json/wp/v2/posts/${postID}`
		fetch(postMetaLink)
			.then(response => response.json())
			.then(json => {
				let workDetails = json.work_details
				let infoStringArray = []
				if(json.title.rendered) infoStringArray.push(json.title.rendered)
				Object.entries(workDetails).forEach((entry) => {
					if(entry[1] && entry[0] !== 'caption')
						infoStringArray.push(entry[1][0])
				})
				let infoString = '';
				infoStringArray.forEach((info, idx) => {
					if(idx > 0)
						infoString += ', ';
					infoString +=  info;
				})
				$('#work-info').text(infoString);
				const detailsWrapper = $('#work-more')
				detailsWrapper.text('');
				if(workDetails.caption) {
					detailsWrapper.prepend(', ')
					const control = $('<span id="work-more-control">more</span>');
					control.on('click',toggleCaption)
					detailsWrapper.append(control);
					$('#work-description').text(workDetails.caption ? workDetails.caption[0] : '');
				}
			})
	}
	
	const toggleCaption = () => {
		$('#work-description-wrapper').toggleClass('show-slide-up');
		$('#work-more-control')
			.text(
				$('#work-description-wrapper').hasClass('show-slide-up') 
				? 'less'
				: 'more'
			);
	};

	function setSurroundingPosts() {
		nextPostWrapper = currentPostWrapper.next();
		if(nextPostWrapper.hasClass('category-title')) {
			nextPostWrapper = nextPostWrapper.next();
		} else if(nextPostWrapper.length === 0) {
			nextPostWrapper = $('.homepage-thumbnail').first().parent();
		}

		previousPostWrapper = currentPostWrapper.prev();
		if(previousPostWrapper.hasClass('category-title')){
			previousPostWrapper = previousPostWrapper.prev();
		}
		if(previousPostWrapper.length === 0) {
			previousPostWrapper = $('.homepage-thumbnail').last().parent();
		}
	}

    $('.homepage-thumbnail')
		.on('click', function(event) {
			currentPostWrapper = $(this).parent();
			setSurroundingPosts();
			setCarousel($(this).data('post-id'))
		});

	function handleSlideNavigationIcons(movement) {
		const currentSlide = carousel.find('.active').index();
		if(movement === 'reset') {
			if(currentSlide + 1 >= numSlides) {
				nextSlideControl.text('>|');
			} else {
				nextSlideControl.text('>');
			}
			previousSlideControl.text('|<');
		} else if(movement === 'forward') {
			if(currentSlide + 2 >= numSlides) {
				nextSlideControl.text('>|');
			} else {
				nextSlideControl.text('>');
			}
			previousSlideControl.text('<');
		} else if(movement === 'back') {
			nextSlideControl.text('>');
			if(currentSlide - 1 === 0) {
				previousSlideControl.text('|<');
			} else {
				previousSlideControl.text('<');
			}
		}
		
	}

	previousSlideControl.on('click', function(event) {
		handleSlideNavigationIcons('back');
		if(carousel.find('.active').index() - 1 < 0) {
			setCarousel(previousPostWrapper.find('.homepage-thumbnail').data('post-id'));
			currentPostWrapper = previousPostWrapper;
			setSurroundingPosts();
		} else {
			carousel.carousel('prev');
		}
	});
	nextSlideControl.on('click', function(event) {
		handleSlideNavigationIcons('forward');
		if(carousel.find('.active').index() + 1 >= numSlides) {
			setCarousel(nextPostWrapper.find('.homepage-thumbnail').data('post-id'));
			currentPostWrapper = nextPostWrapper;
			setSurroundingPosts();
		} else {
			carousel.carousel('next');
		}
	});

	currentPostWrapper = $('.homepage-thumbnail').first().parent();
	setCarousel(currentPostWrapper.find('.homepage-thumbnail').data('post-id'));
	setSurroundingPosts();

});
</script>
<?php
get_footer();
