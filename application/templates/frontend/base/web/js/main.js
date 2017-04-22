
if ($.browser.mobile) $('body').addClass('mobile');
if ($.browser.safari) $('body').addClass('safari');
if ($.browser.mozilla) $('body').addClass('mozilla');
if ($.browser.iphone || $.browser.ipad || $.browser.ipod ) $('body').addClass('ios');


jQuery(document).ready( function () {
	//$("form").validate();

    $(".fb_form").on('beforeSubmit', function () {
        var form = $(this),
            data = new FormData(this);

        $.ajax({
            type: 'POST',
            url: form.attr('action') + '?format=json',
            dataType: 'json',
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function (data) {
                form.find('button[type="submit"]').attr('disabled', 'disabled');
            },
            success: function (data) {

                if (data['result'] == 'error') {
                    sweetAlert("Oops...", data['error'], "error");
                } else {
                    form.trigger('reset');
                    form.closest('.modal_div').animate({opacity: 0}, 100,
                        function(){
                            $(this).css('display', 'none');
                            overlay.fadeOut(100);
                            $(".message_modal").removeClass("show");
                            $('body').removeClass('no-scroll');
                        }
                    );

                    sweetAlert({
                        title: '',
                        text: data['success'],
                        type: "success",
                        html: true
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            },
            complete: function (data) {
                form.find('button[type="submit"]').prop('disabled', false);
            }
        });
        return false;
    });


	//slider video
    /*
	var $sync1 = $(".list_video_slider"),
		$sync2 = $(".list_thumbs_video"),
		flag = false,
		duration = 300;

	$sync1
		.owlCarousel({
			items: 1

		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;
				$sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});

	$sync2
		.owlCarousel({
			margin: 20,
			items: 6,
			nav: true,
			navText : ["",""],
			responsive:{
				0:{
					items:3
				},
				600:{
					items:3
				},
				1000:{
					items:6
				}
			}


		})
		.on('click', '.owl-item', function () {
			$sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);

		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;
				$sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});
*/

	// slider feedback
	$('.list_feedback .owl-carousel').owlCarousel({
		loop:true,
		//autoplay:true,
		nav:true,
		navText: "",
		responsive:{
			0:{
				items:1
			},
			600:{
				items:1
			},
			1000:{
				items:1
			}
		}
	});


	//modal form
	var overlay = $('#overlay');
	var open_modal = $('.open_modal');
	var close = $('.modal_close, #overlay');
	var modal = $('.modal_div');

	open_modal.on('click', function(event) {
		event.preventDefault();
		var div = $(this).attr('href');
		overlay.fadeIn(100,
			function(){
				$(div)
					.css('display', 'block')
					.animate({opacity: 1}, 100);
				$('body').addClass('no-scroll');
			});
	});

	close.click( function(){
		modal
			.animate({opacity: 0}, 100,
				function(){
					$(this).css('display', 'none');
					overlay.fadeOut(100);
					$(".message_modal").removeClass("show");
					$('body').removeClass('no-scroll');
				}
			);
	});


	$(this).keydown(function(eventObject){
		if (eventObject.which == 27)
			modal.animate({opacity: 0}, 200,
				function(){
					$(this).css('display', 'none');
					overlay.fadeOut(400);
					$(".message_modal").removeClass('show');
					$('body').removeClass('no-scroll');

				}
			);
	});


	//$(window).on('load', function() {

		$("header .menu-icon-open").on('click', function () {
			$("header .menu_mobile, header").addClass('open');
			$('body').addClass('no-scroll');


		});
		$("header .menu-icon-close").on('click', function () {
			$("header .menu_mobile, header").removeClass('open');
			$('body').removeClass('no-scroll');

		});
	//});


	$(".block_about .show_more").on('click', function () {
		$(".block_about .block_text").addClass('open');
		$(this).addClass('hide');
		$(".block_about .hide_more").removeClass('hide');


	});

	$(".block_about .hide_more").on('click', function () {
		$(".block_about .block_text").removeClass('open');
		$(this).addClass('hide');
		$(".block_about .show_more").removeClass('hide');


	});


	$(function() {

        $('a[href^="#"]:not(".open_modal")').on('click', function (e) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: $($(this).attr('href')).offset().top
            }, 1000);
        });

        if (window.location.hash) {
            $('html, body').stop().animate({
                scrollTop: $(window.location.hash).offset().top
            }, 1000);
        }

		var clickFunction = function(hash, e) {
			var hrefVal, target;
			if (typeof hash === 'string') {
				hrefVal = hash;
			} else {
				hrefVal = $(this).attr('href');
			}
			target = $(hrefVal);
			if (target.length) {
				$('html, body').animate({
					scrollTop: target.offset().top
				}, 1000);
				return false;
			}
		};

		$('.button_down').click(clickFunction);
	});

	//jQuery(window).load(function() {
	if (jQuery(window).width() > 959) {

		new WOW().init();
		$(".fancy_title").textillate({in: {effect: 'fadeInLeftBig'}});
		$(".fancy_title2").textillate({in: {effect: 'fadeInLeft'}});
	}
	//});


});
