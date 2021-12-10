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