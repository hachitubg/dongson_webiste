(function ($) {
	
	"use strict";

	// Page loading animation
	$(window).on('load', function() {

        $('#js-preloader').addClass('loaded');

    });

	// Language Switcher
	const langConfig = {
		vi: { flag: 'images/vietnam.png', text: 'VN', name: 'Tiếng Việt' },
		en: { flag: 'images/united-states.png', text: 'EN', name: 'English' },
		jp: { flag: 'images/japan.png', text: 'JP', name: '日本語' },
		kr: { flag: 'images/south-korea.png', text: 'KR', name: '한국어' },
		th: { flag: 'images/thailand.png', text: 'TH', name: 'ไทย' }
	};

	// Load saved language or default to Vietnamese
	let currentLang = localStorage.getItem('selectedLanguage') || 'vi';

	// Initialize language on page load
	function initLanguage() {
		updateCurrentLangButton(currentLang);
		setActiveOption(currentLang);
	}

	// Update current language button
	function updateCurrentLangButton(lang) {
		const config = langConfig[lang];
		$('#currentLang .flag-icon').attr('src', config.flag).attr('alt', config.name);
		$('#currentLang .lang-text').text(config.text);
	}

	// Set active option in dropdown
	function setActiveOption(lang) {
		$('.lang-option').removeClass('active');
		$(`.lang-option[data-lang="${lang}"]`).addClass('active');
	}

	// Toggle dropdown
	$('#currentLang').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		const dropdown = $('#langDropdown');
		const btn = $(this);
		
		dropdown.toggleClass('show');
		btn.toggleClass('active');
	});

	// Language option click
	$('.lang-option').on('click', function(e) {
		e.preventDefault();
		const selectedLang = $(this).data('lang');
		
		// Update language
		currentLang = selectedLang;
		localStorage.setItem('selectedLanguage', selectedLang);
		
		// Update UI
		updateCurrentLangButton(selectedLang);
		setActiveOption(selectedLang);
		
		// Close dropdown with animation
		$('#langDropdown').removeClass('show');
		$('#currentLang').removeClass('active');
		
		// You can add translation logic here
		// translatePage(selectedLang);
	});

	// Close dropdown when clicking outside
	$(document).on('click', function(e) {
		if (!$(e.target).closest('.language-switcher').length) {
			$('#langDropdown').removeClass('show');
			$('#currentLang').removeClass('active');
		}
	});

	// Initialize language on page load
	initLanguage();

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

		$("#preloader").animate({
			'opacity': '0'
		}, 600, function(){
			setTimeout(function(){
				$("#preloader").css("visibility", "hidden").fadeOut();
			}, 300);
		});

		// AOS (Animate On Scroll) initialization and dynamic attributes
		try {
			// add data-aos attributes to major sections for animated reveal
			$('.featured, .video, .fun-facts, .best-deal, .properties, .contact, .contact-content, .single-property').attr('data-aos', 'fade-up');
			// add animation to each product item with staggered delay
			$('.properties .item').each(function(i){
				$(this).attr('data-aos', 'fade-up');
				$(this).attr('data-aos-delay', (i % 6) * 80); // stagger delays
			});

			// initialize AOS if available
			if (typeof AOS !== 'undefined') {
				AOS.init({
					once: true,
					duration: 800,
					easing: 'ease-in-out'
				});
			}
		} catch (e) {
			// fail silently if AOS not loaded
			console.warn('AOS init skipped:', e);
		}
	});
    


})(window.jQuery);