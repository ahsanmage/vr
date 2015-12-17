/*
 * Galathemes
 *
 * @license commercial software
 * @copyright (c) 2014 Codespot Software JSC - Galathemes.com. (http://www.galathemes.com)
 */
(function($) {
    if (typeof EM == 'undefined') EM = {};
    if (typeof EM.SETTING == 'undefined') EM.SETTING = {};
    // retia
    function emRetina(){        
        if (window.devicePixelRatio > 1 ||
	       (window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches)) {
			  var images = $('img.retina-img');
              var len = images.length;
              if(len){
                    // loop through the images and make them hi-res
    			  for(var i = 0; i < len; i++) {    
    				// create new image name
    				var imageType = images[i].src.substr(-4);
    				var imageName = images[i].src.substr(0, images[i].src.length - 4);
    				imageName += "@2x" + imageType;
    
    				//rename image
    				images[i].src = imageName;
    			  }
              }
		 }
    };
    
    /* hiep fix right to left */
    function fixOwlSliderRtl(){
        if(EM.SETTING.RIGHT_TO_LEFT==1){
            var $body = $('body');
            $body.addClass('em-rtl');
        }
    };
    
    /* fluid bootstrap */
    function fluidBootstrap() {
        var sContainer = $('div.container');
        var len = sContainer.length;
        if(len){sContainer.removeClass('container').addClass('container-fluid');}        
    };
    
    function emCollapsible(){
        var wi = $(window).width();
        //alert(wi);
        if(isPhone || wi<768){            
            var sCollap = $("[data-collapse-target]");            
            var len = sCollap.length;
            if(len){
                sCollap.removeClass('em-collapsed non-collapsed').addClass('non-collapsed');
                for(var i=0;i<len;i++){
                    var id = sCollap.eq(i).data('collapse-target');
                    var $id=$(id);
                    if($id.length){
                        if($id.is(':visible')){
                            $id.hide(200,function(){
                                $(this).css('overflow','inherit');
                            });
                        }
                    }                    
                }
                sCollap.unbind('click');
                sCollap.click(function(){
                    var $this = $(this);
                    $this.toggleClass('non-collapsed');
                    $this.toggleClass('em-collapsed');
                    var id = $this.data('collapse-target');
                    var $id = $(id);
                    $id.slideToggle(200,function(){
                        $(this).css('overflow','inherit');
                    });
                });
            }   
        }else{
            var sCollap = $("[data-collapse-target]");
            var len = sCollap.length;
            if(len){
                sCollap.removeClass('em-collapsed non-collapsed');
                sCollap.unbind('click');
                for(var i=0;i<len;i++){
                    var id = sCollap.eq(i).data('collapse-target');
                    var $id=$(id);
                    if($id.length){
                        if(!$id.is(':visible')){
                            $id.show(200,function(){
                                $(this).css('overflow','inherit');
                            });
                        }
                    }                    
                }
            } 
        }
    };

    //Fix iPhone/iPod auto zoom-in when text fields, select boxes are focus
    function fixIPhoneAutoZoomWhenFocus() {
        var viewport = $('head meta[name=viewport]');
        if (viewport.length == 0) {
            $('head').append("<meta name='viewport' content='width=device-width, initial-scale=1.0'/>");
            viewport = $('head meta[name=viewport]');
        }
        var old_content = viewport.attr('content');

        function zoomDisable() {
            viewport.attr('content', old_content + ', user-scalable=0');
        }

        function zoomEnable() {
            viewport.attr('content', old_content);
        }
        $('input[type=text], textarea, select').mouseover(zoomDisable).mousedown(zoomEnable);
    };
    
    function stickyElement(e,position,condition,classfixed){
        if(!isPhone){
            var sE = $(e);
            var posE = $(position);
            var len = sE.length;
            if(len){
                if (condition != 0) {
                    var stickyE = function() {
                        var scroll_top = $(window).scrollTop();
                        var pos = posE.offset().top;
                        if (scroll_top > pos) {
                            sE.addClass(classfixed);
                        } else {
                            sE.removeClass(classfixed);
                        }
                    };
                    $(window).scroll(function() {
                        stickyE();
                    });
                }
            }
        }        
    };
	
    /* Menu Left */

	function doMenuLeft() {
		var wi = $(window).width();
		if (isHomePage == 1 || pos_menuleft == true) {
			$('.all_categories').attr('id', 'menuleftTextHomepage');
			$('#menu-default').css('display', 'block');
		} else {
			$('.all_categories').attr('id', 'menuleftText');
			$('#menu-default').css('display', 'none');
		}
		var container = $("#menu-default");
		if (EM.SETTING.DISABLE_RESPONSIVE != 1) {			
			if ((!isPhone) && (wi > 767)) {
				$("#menuleftText,#menuleftTextHomepage").unbind("click");
				$(".menu-wrapper").unbind('hover');
				container.show();
				if (!($("body").hasClass("cms-index-index"))) {
					container.hide();
					$(".menu-wrapper").hover(

					function() {
						container.fadeIn(500);
						$("#menuleftText").toggleClass('hidden-arrow');
						$("#menuleftText").attr('title', 'hide-option');
					}, function() {
						container.fadeOut(100);
						$("#menuleftText").removeClass('hidden-arrow');
						$("#menuleftText").attr('title', 'show-option');
					});
				}
			} else {
				$("#menu-default").css("display", "none");
				$(".menu-wrapper").unbind('hover');
				$("#menuleftText,#menuleftTextHomepage").unbind("click");
				$("#menuleftText,#menuleftTextHomepage").removeClass('hidden-arrow');
				$("#menuleftText,#menuleftTextHomepage").attr('title', 'show-option');
				$("#menuleftText,#menuleftTextHomepage").click(

				function(event) {
					event.preventDefault();
					if (container.is(":visible")) {
						container.hide();
						$("#menuleftText,#menuleftTextHomepage").removeClass('hidden-arrow');
						$("#menuleftText,#menuleftTextHomepage").attr('title', 'show-option');
					} else {
						container.show();
						$("#menuleftText,#menuleftTextHomepage").toggleClass('hidden-arrow');
						$("#menuleftText,#menuleftTextHomepage").attr('title', 'hide-option');
					}
				});
			}
		} else {
            if(!isMobile){
    			$("#menuleftText,#menuleftTextHomepage").unbind("click");
    			$(".menu-wrapper").unbind('hover');
    			container.show();
    			if (!($("body").hasClass("cms-index-index"))) {
    				container.hide();
    				$(".menu-wrapper").hover(
    
    				function() {
    					container.fadeIn(500);
    					$("#menuleftText").toggleClass('hidden-arrow');
    					$("#menuleftText").attr('title', 'hide-option');
    				}, function() {
    					container.fadeOut(100);
    					$("#menuleftText").removeClass('hidden-arrow');
    					$("#menuleftText").attr('title', 'show-option');
    				});
    			}
            }else{
    			container.show();
    			if (!($("body").hasClass("cms-index-index"))) {
    				container.hide();
    				$("#menuleftText,#menuleftTextHomepage").click(

    				function(event) {
    					event.preventDefault();
    					if (container.is(":visible")) {
    						container.hide();
    						$("#menuleftText,#menuleftTextHomepage").removeClass('hidden-arrow');
    						$("#menuleftText,#menuleftTextHomepage").attr('title', 'show-option');
    					} else {
    						container.show();
    						$("#menuleftText,#menuleftTextHomepage").toggleClass('hidden-arrow');
    						$("#menuleftText,#menuleftTextHomepage").attr('title', 'hide-option');
    					}
    				});
    			}
            }
		}
	};
	
    // sticky menu
    function persistentMenu() {
        var sMenu = $('.em-top-menu');
		var $_menu = $('.em-menu');
		var $_Logo = $('.em-logo');
		var $_Logo_Sticky = $('.em-logo-sticky');
        if (sMenu.length) {
            var wWindow = $(window).width();
            if (EM.SETTING.STICKY_MENU != 0 && !isPhone) {
                var sticky_navigation = function() {
                    var scroll_top = $(window).scrollTop();
                    var navpos = $('#navpos').offset().top;

                    if (wWindow > 767) {
                        if (scroll_top > navpos) {
							$_Logo.hide();
							$_Logo_Sticky.show();
                            sMenu.addClass('navbar-fixed-top animated fadeInDown');
                        } else {
                            sMenu.removeClass('navbar-fixed-top animated fadeInDown');
							$_Logo.show();
							$_Logo_Sticky.hide();
                        }
                    } else {
                        sMenu.removeClass('navbar-fixed-top');
						$_Logo.show();
						$_Logo_Sticky.hide();
                    }
                };
				sticky_navigation();
                $(window).scroll(function() {
                    sticky_navigation();
                });
            }
            if (wWindow <= 767) {
                sMenu.removeClass('navbar-fixed-top');
				$_Logo.show();
				$_Logo_Sticky.hide();
            }
        }
    };

    // sticky menu no responsive
    function freezedMenuNoResponsive() {
        var sMenu = $('.em-top-menu');
		var $_Logo = $('.em-logo');
		var $_Logo_Sticky = $('.em-logo-sticky');
        if (sMenu.length) {
            if (EM.SETTING.STICKY_MENU != 0 && !isPhone) {
                var sticky_navigation = function() {
                    var scroll_top = $(window).scrollTop();
                    var navpos = $('#navpos').offset().top;
                    if (scroll_top > navpos) {
                        $_Logo.hide();
						$_Logo_Sticky.show();
                        sMenu.addClass('navbar-fixed-top animated fadeInDown');
                    } else {
                        sMenu.removeClass('navbar-fixed-top animated fadeInDown');
						$_Logo.show();
						$_Logo_Sticky.hide();
                    }
                };
				sticky_navigation();
                $(window).scroll(function() {
                    sticky_navigation();
                });
            }
            if (isPhone) {
                sMenu.removeClass('navbar-fixed-top');
				$_Logo.show();
				$_Logo_Sticky.hide();
            }
        }
    };
    // random string
    function randomString(len, charSet) {
        charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var randomString = '';
        for (var i = 0; i < len; i++) {
            var randomPoz = Math.floor(Math.random() * charSet.length);
            randomString += charSet.substring(randomPoz, randomPoz + 1);
        }
        return randomString;
    };

    // do slider
    function doSliderOwl() {
        function getOption(e, optionType, optionValue) {
            //number of item class
            if ($(e).data(optionType)) {
                optionValue = $(e).data(optionType);
            }
            return optionValue;
        };
        var $selector = $('.em-slider');
        var $len = $selector.length;
        if ($len) {
            for (var i = 0; i < $len; i++) {
                var stringRandom = randomString(100);
                $selector.eq(i).attr('id', 'owl_slider_' + stringRandom);
                var owl = $('#owl_slider_' + stringRandom);
                if(EM.SETTING.DISABLE_RESPONSIVE!=0){
                    var items = getOption(owl, 'emslider-items', 4);
                    var itemsDesktop = getOption(owl, 'emslider-desktop', items);
                    var itemsDesktopSmall = getOption(owl, 'emslider-desktop-small', items);
                    var itemsTablet = getOption(owl, 'emslider-tablet', items);
                    var itemsMobile = getOption(owl, 'emslider-mobile', items);
                    var responsive =  true;
                }else{
                    var items = getOption(owl, 'emslider-items', 4);
                    var itemsDesktop = items;
                    var itemsDesktopSmall = items;
                    var itemsTablet = items;
                    var itemsMobile = items;
                    var responsive =  false;
                }
                var navigation = getOption(owl, 'emslider-navigation', false);
                var pagination = getOption(owl, 'emslider-pagination', false);
                var paginationNumbers = getOption(owl, 'emslider-pagination-numbers', false);
                if (paginationNumbers == true) {
                    pagination = true;
                }
                // do owl carousel
                owl.owlCarousel({
                    //Basic Speeds
                    slideSpeed: 800,
                    rewindSpeed: 800,
                    //Autoplay
                    lazyLoad: true,
                    stopOnHover: true,
                    // Navigation
                    navigation: navigation,
                    pagination: pagination,
                    paginationNumbers: paginationNumbers,
                    // Responsive 
                    responsive: responsive,
                    items: items,
                    /*items above 1200px browser width*/
                    itemsDesktop: [1200, itemsDesktop],
                    /*//items between 1199px and 981px*/
                    itemsDesktopSmall: [992, itemsDesktopSmall],
                    itemsTablet: [768, itemsTablet],
                    itemsMobile: [480, itemsMobile],
                    // CSS Styles
                    baseClass: 'owl-carousel',
                    theme: 'owl-theme',
                    addClassActive: true
                });
            }
        }
    };

    // build bootstrap js
    function buildBootrapJs() {
        var sToolTip = $("[data-toggle='tooltip']");
        var len = sToolTip.length;
        if (len) {
            for (var i = 0; i < len; i++) {
                sToolTip.eq(i).tooltip();
            }
        }
    };
    
    function ieVersion() {
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) rv = parseFloat(RegExp.$1);
		}
		return rv;
	};
    
    function emAnimation() {
        if (!isMobile && ((ieVersion() == -1 || ieVersion() > 9)) ) {
            function addAnimationEffects(e,effects){
                $this = $(e).children('div').not('.clearfix');
                if($this.length){
                    $this.attr('data-animate', effects);
                }
            };
            if(EM.SETTING.STICKY_CART || EM.SETTING.STICKY_SEARCH || EM.SETTING.STICKY_MENU){
                addAnimationEffects('.em-header-top',EM.SETTING.ANIMATE_HEADER);
				addAnimationEffects('.em-header-bottom',EM.SETTING.ANIMATE_HEADER);
            }else{
                addAnimationEffects('.em-header-container',EM.SETTING.ANIMATE_HEADER);
            }            
            addAnimationEffects('.em-col-left',EM.SETTING.ANIMATE_LEFT);
            addAnimationEffects('.em-col-right',EM.SETTING.ANIMATE_RIGHT);
            addAnimationEffects('.em-wrapper-main,.inner-main,.inner-main-top,.inner-main-ads,.inner-main-blog,.inner-main-features,.inner-main-ads02,.inner-main-bottom',EM.SETTING.ANIMATE_MAIN);
            addAnimationEffects('.inner-footer,.inner-footer-top',EM.SETTING.ANIMATE_FOOTER);
            
            var animate = $('[data-animate]');
            animate.addClass('em-animate-delay');
            var len = animate.length;
            if(len){            
                animate.bind('inview', function (event, visible) {
                    var $this = $(this);
                    if (visible && !$this.hasClass('em-animated-end')) {
                        var elementAnimation = $this.data('animate');
                        var timeAnimation = $this.data('animate-delay') ? $this.data('animate-delay') : 100;
                        setTimeout(function(){
                            $this.addClass(elementAnimation + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){                                
                                $this.removeClass(elementAnimation + ' animated em-animate-delay').addClass('em-animated-end');
                            });
                        },timeAnimation);
                    }
                });
            }        
        }
    };

    // safari hack: remove bold in h5, .h5
    function fixFontBoldSafari() {
        if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
            $('h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6').css('font-weight', 'normal');
        }
    };

    // add quantity to favico
   /* function updateFavico() {
        var itemsCount = parseInt($('.em-topcart-qty').eq(0).text());
        var favicon = new Favico({
            animation: 'slide',
        });
        favicon.badge(itemsCount);
    };*/

    function emResetFullSlideshowOnDisableVariation() {
        var sSlideshow = $('.em-full-slideshow');
        var sDivSlideshow = $('.em-area-01');
        if (EM.SETTING.FULL_SLIDESHOW != 0) {
            sSlideshow.addClass('selected');
            sDivSlideshow.parents('.container-slideshow').removeClass('container').addClass('container-fluid');
        } else {
            sSlideshow.removeClass('selected');
            sDivSlideshow.parents('.container-slideshow').removeClass('container-fluid').addClass('container');
        }
    };
	function DecorateVideo(){
		if (isMobile)	
			$(".container-main-video .upb_video-wrapper .upb_video-bg em").toggleClass("fa-pause").toggleClass("fa-play");
		$(".container-main-video .upb_video-wrapper .upb_video-bg em").click(function () {
			if($(this).hasClass("fa-play")){
				$(".container-main-video .upb_video-wrapper .upb_video-bg video").get(0).play();
			}
			else
				$(".container-main-video .upb_video-wrapper .upb_video-bg video").get(0).pause();
			$(this).toggleClass("fa-pause").toggleClass("fa-play");
			return false;
		});
	};
	
	
	
	function fixHeaderTop(){
		var screenHeight=$(window).height();
		$(".cms-index-index.layout-fullscreen-slideshow .wrapper-header").css("margin-top",screenHeight+"px");
	}
	
    //Ready Function
    $(document).ready(function() {        
        // all page
        fixOwlSliderRtl();
        fixFontBoldSafari();
        if (EM.SETTING.FLUID_LAYOUT != 0) {
            fluidBootstrap();
        }
        buildBootrapJs();
        if (EM.SETTING.DISABLE_VARIATION != 1) {
            emResetFullSlideshowOnDisableVariation();
        }
        // init ajax wishlist for wishlist link which has css class "link-wislist"
        if (EM.SETTING.DISABLE_AJAX_ADDTO != 0){
            var sWishlist = $('.link-wishlist');
            sWishlist.emAjaxWishList();

            var sCompare = $('.link-compare');
            sCompare.emAjaxCompare({
                sidebarSelector : ".block-compare"
            });
        }        
        //show popup video link
        if (EM.SETTING.POPUP_VIDEO == 1) {
            var sVideo = $('a[data-link]');                  
            if(sVideo.length && sVideo.data("link")=='video'){                
                sVideo.fancybox({
                    openEffect: 'elastic',
                    closeEffect: 'elastic',
                    helpers: {
                        media: {}
                    }
                });
            }
        }
       /* if(ieVersion()==-1)
            updateFavico();*/

        
		doMenuLeft() ;
        // responsive
        if (EM.SETTING.DISABLE_RESPONSIVE != 0) {
            isMobile && fixIPhoneAutoZoomWhenFocus();
            persistentMenu();
        } else {
            freezedMenuNoResponsive();
        }
        
        if( EM.SETTING.ANIMATION_LOADING !=0){
            emAnimation();
        }
        emRetina();
        stickyElement('.em-top-cart','#cartpos',EM.SETTING.STICKY_CART,'cart-fixed-right');
        stickyElement('.em-top-search','#searchpos',EM.SETTING.STICKY_SEARCH,'search-fixed-right');      
		DecorateVideo();
		fixHeaderTop();
    });

    //Load Function
    $(window).bind('load', function() {
        if (EM.SETTING.DISABLE_RESPONSIVE != 0) {        
            if(EM.SETTING.DISABLE_COLLAPSE!=0){
                emCollapsible();   
            }
        }
        setTimeout(function() {
            doSliderOwl();
        }, 300);        
    });
    var tmresize;
    $(window).resize(function() {
        if (EM.SETTING.DISABLE_RESPONSIVE != 0) {
            clearTimeout(tmresize);
            tmresize = setTimeout(function() {
                persistentMenu();
            }, 300);
            if(EM.SETTING.DISABLE_COLLAPSE!=0){
                emCollapsible();   
            }
			fixHeaderTop();
        }        
    });

    $(window).bind('orientationchange', function() {
        //emCollapsible();
    });
})(jQuery);