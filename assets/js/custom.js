(function ($) {
	
	"use strict";

	// Page loading animation
	$(window).on('load', function() {

        $('#js-preloader').addClass('loaded');

    });

	// Language Switcher - Integrated with Google Translate
	const langConfig = {
		vi: { flag: 'images/vietnam.png', text: 'VN', name: 'Tiếng Việt' },
		en: { flag: 'images/united-states.png', text: 'EN', name: 'English' },
		ja: { flag: 'images/japan.png', text: 'JP', name: '日本語' },
		ko: { flag: 'images/south-korea.png', text: 'KR', name: '한국어' },
		th: { flag: 'images/thailand.png', text: 'TH', name: 'ไทย' }
	};

	// Toggle dropdown
	$('#currentLang').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		const dropdown = $('#langDropdown');
		const btn = $(this);
		
		dropdown.toggleClass('show');
		btn.toggleClass('active');
	});

	// Close dropdown when clicking outside
	$(document).on('click', function(e) {
		if (!$(e.target).closest('.language-switcher').length) {
			$('#langDropdown').removeClass('show');
			$('#currentLang').removeClass('active');
		}
	});

	// Language option click - Close dropdown
	$('.lang-option').on('click', function(e) {
		// Close dropdown with animation
		setTimeout(function() {
			$('#langDropdown').removeClass('show');
			$('#currentLang').removeClass('active');
		}, 100);
	});

	$(window).scroll(function() {
	  var scroll = $(window).scrollTop();
	  var box = $('.header-text').height();
	  var header = $('header').height();

	  if (scroll >= box - header) {
	    $("header").addClass("background-header");
	  } else {
	    $("header").removeClass("background-header");
	  }
	})

	$('.owl-banner').owlCarousel({
	  center: true,
      items:1,
      loop:true,
      nav: true,
	  dots:true,
	  navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
      margin:30,
      responsive:{
        992:{
            items:1
        },
		1200:{
			items:1
		}
      }
	});

	var width = $(window).width();
		$(window).resize(function() {
		if (width > 767 && $(window).width() < 767) {
			location.reload();
		}
		else if (width < 767 && $(window).width() > 767) {
			location.reload();
		}
	})

	const elem = document.querySelector('.properties-box');
	const filtersElem = document.querySelector('.properties-filter');
	if (elem) {
		const rdn_events_list = new Isotope(elem, {
			itemSelector: '.properties-items',
			layoutMode: 'masonry'
		});
		if (filtersElem) {
			filtersElem.addEventListener('click', function(event) {
				if (!matchesSelector(event.target, 'a')) {
					return;
				}
				const filterValue = event.target.getAttribute('data-filter');
				rdn_events_list.arrange({
					filter: filterValue
				});
				filtersElem.querySelector('.is_active').classList.remove('is_active');
				event.target.classList.add('is_active');
				event.preventDefault();
			});
		}
	}


	// Menu Dropdown Toggle
	if($('.menu-trigger').length){
		$(".menu-trigger").on('click', function() {	
			$(this).toggleClass('active');
			$('.header-area .nav').slideToggle(200);
		});
	}


	// Menu elevator animation
	$('.scroll-to-section a[href*=\\#]:not([href=\\#])').on('click', function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				var width = $(window).width();
				if(width < 991) {
					$('.menu-trigger').removeClass('active');
					$('.header-area .nav').slideUp(200);	
				}				
				$('html,body').animate({
					scrollTop: (target.offset().top) - 80
				}, 700);
				return false;
			}
		}
	});


	// Page loading animation
	$(window).on('load', function() {
		if($('.cover').length){
			$('.cover').parallax({
				imageSrc: $('.cover').data('image'),
				zIndex: '1'
			});
		}
	});

	// Advanced Scroll Animations for Homepage
	function initScrollAnimations() {
		// Add scroll animation classes to sections
		$('.featured').addClass('scroll-animate');
		$('.video').addClass('scroll-animate');
		$('.fun-facts').addClass('scroll-animate');
		$('.best-deal').addClass('scroll-animate');
		$('.properties').addClass('scroll-animate');
		$('.contact').addClass('scroll-animate');

		// Stagger animation for product items
		$('.properties .item').each(function(index) {
			$(this).css('animation-delay', (index * 0.1) + 's');
		});

		// Scroll trigger function
		function checkScroll() {
			var scrollTop = $(window).scrollTop();
			var windowHeight = $(window).height();

			$('.scroll-animate, .scroll-animate-left, .scroll-animate-right').each(function() {
				var elementTop = $(this).offset().top;
				
				if (scrollTop + windowHeight > elementTop + 100) {
					$(this).addClass('active');
				}
			});
		}

		// Run on scroll
		$(window).on('scroll', checkScroll);
		
		// Run on load
		checkScroll();
	}

	// Parallax effect for banner and video sections
	function initParallaxEffect() {
		$(window).on('scroll', function() {
			var scrolled = $(window).scrollTop();
			
			// Banner parallax
			$('.main-banner .item').css('transform', 'translateY(' + (scrolled * 0.5) + 'px)');
			
			// Video section parallax
			$('.video').css('background-position', 'center ' + (scrolled * 0.3) + 'px');
		});
	}

	// Counter animation for fun facts
	function initCounterAnimation() {
		var counted = false;
		
		$(window).on('scroll', function() {
			var scrollTop = $(window).scrollTop();
			var funFactsTop = $('.fun-facts').length ? $('.fun-facts').offset().top : 0;
			var windowHeight = $(window).height();
			
			if (!counted && scrollTop + windowHeight > funFactsTop + 200) {
				counted = true;
				
				$('.count-number').each(function() {
					var $this = $(this);
					var countTo = $this.data('to');
					
					$({ countNum: 0 }).animate({
						countNum: countTo
					}, {
						duration: 2000,
						easing: 'swing',
						step: function() {
							$this.text(Math.floor(this.countNum));
						},
						complete: function() {
							$this.text(this.countNum);
						}
					});
				});
			}
		});
	}

	// Image lazy loading effect
	function initImageEffects() {
		$('.properties .item img, .best-deal img').on('load', function() {
			$(this).addClass('loaded');
		});

		// Add hover effect to all images
		$('img').hover(
			function() {
				$(this).css('transition', 'all 0.3s ease');
			},
			function() {
				$(this).css('transition', 'all 0.3s ease');
			}
		);
	}

	// Smooth reveal for accordion
	$('.accordion-button').on('click', function() {
		$(this).closest('.accordion-item').addClass('active-accordion');
	});

	// Initialize all homepage animations
	$(document).ready(function() {
		initScrollAnimations();
		initParallaxEffect();
		initCounterAnimation();
		initImageEffects();

		// Add stagger animation to info table items
		$('.info-table ul li').each(function(index) {
			$(this).css({
				'animation': 'fadeInUp 0.6s ease-out ' + (index * 0.15) + 's both'
			});
		});

		// Add hover effects to tabs
		$('.nav-link').hover(
			function() {
				$(this).css('transform', 'translateY(-3px)');
			},
			function() {
				if (!$(this).hasClass('active')) {
					$(this).css('transform', 'translateY(0)');
				}
			}
		);

		// Form input animations
		$('#contact-form input, #contact-form textarea').on('focus', function() {
			$(this).parent().addClass('focused');
		}).on('blur', function() {
			if ($(this).val() === '') {
				$(this).parent().removeClass('focused');
			}
		});

		// Add ripple effect to buttons
		$('.main-button a, .icon-button a, button').on('click', function(e) {
			var $button = $(this);
			var $ripple = $('<span class="ripple"></span>');
			
			$button.append($ripple);
			
			var x = e.pageX - $button.offset().left;
			var y = e.pageY - $button.offset().top;
			
			$ripple.css({
				left: x,
				top: y
			});
			
			setTimeout(function() {
				$ripple.remove();
			}, 600);
		});
	});

	// Add CSS for ripple effect
	$('<style>')
		.prop('type', 'text/css')
		.html(`
			.ripple {
				position: absolute;
				border-radius: 50%;
				background: rgba(255, 255, 255, 0.6);
				width: 20px;
				height: 20px;
				animation: ripple-animation 0.6s ease-out;
				pointer-events: none;
			}
			@keyframes ripple-animation {
				to {
					width: 300px;
					height: 300px;
					opacity: 0;
					margin-left: -150px;
					margin-top: -150px;
				}
			}
			.focused {
				transform: scale(1.02);
			}
			@keyframes fadeInUp {
				from {
					opacity: 0;
					transform: translateY(20px);
				}
				to {
					opacity: 1;
					transform: translateY(0);
				}
			}
			.loaded {
				animation: imageLoad 0.5s ease-out;
			}
			@keyframes imageLoad {
				from {
					opacity: 0;
					transform: scale(0.95);
				}
				to {
					opacity: 1;
					transform: scale(1);
				}
			}
		`)
		.appendTo('head');
    


})(window.jQuery);