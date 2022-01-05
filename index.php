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

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="container-lg">
				<div class='row justify-content-center d-none' id="carousel-row">
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
				<div class="row justify-content-between d-none" id="slide-control-row">
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
					$numberColumns = 9;
					$count = 0;
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
							<div class="col-md col-3 homepage-thumbnail-wrapper category-title">
								<span><?php echo $category->name; ?></span>
							</div><?php
							$count += 1;
							// Load posts loop.
							while ( $category_query->have_posts() ) {
								$category_query->the_post();
								?>
									<div class="col-md col-3 homepage-thumbnail-wrapper">
										<div class="homepage-thumbnail" data-post-id="<?php echo get_the_ID() ?>" style="<?php echo "background-image: url(" . get_the_post_thumbnail_url() . ");"; ?>"></div>
									</div>
								<?php 
								
								$count += 1;
								if($count % $numberColumns == 0) { ?>
									<div class="col-md-12 h-0 d-none d-md-block"></div>
								<?php }
							}

						}
					}

					while($count % $numberColumns > 0){ ?>
						<div class="col-md col-0 h-0 d-none d-md-block"></div> <?php 
						$count += 1;
					} ?>
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
		const imageLink = `wp-json/wp/v2/media?parent=${postID}&per_page=100`
		fetch(imageLink)
			.then(response => response.json())
			.then(json => {
				const imageContainer = $('#top-carousel .carousel-inner');
				imageContainer.empty();
				numSlides = 0;
				json.reverse().forEach((image, idx) => {
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
		
		const postMetaLink = `wp-json/wp/v2/posts/${postID}`
		fetch(postMetaLink)
			.then(response => response.json())
			.then(json => {
				let workDetails = json.work_details
				let infoStringArray = []
				// if(json.title.rendered) infoStringArray.push(json.title.rendered.replace('&#038;', '&'))
				Object.entries(workDetails).forEach((entry) => {
					if(entry[1] && entry[0] !== 'caption')
						infoStringArray.push(entry[1][0].replace('&#038;', '&'))
				})
				let infoString = '';
				infoStringArray.forEach((info, idx) => {
					if(idx > 0)
						infoString += ', ';
					infoString +=  info;
				})
				if(workDetails.caption)
					infoString += ', ';
				$('#work-info').text(infoString);
				const detailsWrapper = $('#work-more')
				detailsWrapper.text('');
				if(workDetails.caption) {
					const control = $('<span id="work-more-control">more</span>');
					control.on('click',toggleCaption)
					detailsWrapper.append(control);
					$('#work-description').text(workDetails.caption ? workDetails.caption[0] : '');
				}
				$("html, body").animate({ scrollTop: 0 }, 500);
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
		$('.homepage-thumbnail').removeClass('selected-thumbnail')
		currentPostWrapper.find('.homepage-thumbnail').addClass('selected-thumbnail')
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
			$('#slide-control-row').removeClass('d-none')
			$('#carousel-row').removeClass('d-none')
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

	// currentPostWrapper = $('.homepage-thumbnail').first().parent();
	// setCarousel(currentPostWrapper.find('.homepage-thumbnail').data('post-id'));
	// setSurroundingPosts();

});
</script>
<?php
get_footer();
