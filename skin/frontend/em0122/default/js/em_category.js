/*
 * Galathemes
 *
 * @license commercial software
 * @copyright (c) 2014 Codespot Software JSC - Galathemes.com. (http://www.galathemes.com)
 */
 (function($) {
    if (typeof EM == 'undefined') EM = {};
    if (typeof EM.SETTING == 'undefined') EM.SETTING = {};
    var domLoaded = false;
    var windowLoaded = false;
    
    function ieVersion() {
        var rv = -1; // Return value assumes failure.
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re = new RegExp('MSIE ([0-9]{1,}[\.0-9]{0,})');
            if (re.exec(ua) != null) rv = parseFloat(RegExp.$1);
        }
        return rv;
    };
    
    //Ready Function
    $(document).ready(function() {
        domLoaded = true;
        // category
        toolbarCategory();
    });

    //Load Function
    $(window).bind('load', function() {
        // all page
        windowLoaded = true;
        // category page       
        if (ieVersion() != -1 || ieVersion() < 9) {
            setTimeout(function() {
                if (typeof em_slider !== 'undefined') {
                    em_slider.reinit();
                }
            }, 8000);
        }
        if (!isPhone) {
            setTimeout(function() {
                setEqualHeightItemsCategory();
            }, 2000);
        }
    });
    var tmresize;
    $(window).resize($.throttle(300,function(){
        if (EM.SETTING.DISABLE_RESPONSIVE != 0) {
            clearTimeout(tmresize);
            tmresize = setTimeout(function() {
                if (typeof em_slider !== 'undefined') {
                    em_slider.reinit();
                }
            }, 300);
        }
        if (!isPhone) {
            setTimeout(function() {
                setEqualHeightItemsCategory();
            }, 2000);
        }   
    }));
})(jQuery);

function toolbarCategory() {
    var $ = jQuery;
	var sToolbar = $('.toolbar-top .toolbar-show');
	var sSortby = $('.toolbar-top .sortby');
    if (!(isMobile)) {
        if(sToolbar.length){
            sToolbar.insertUl();
            sToolbar.selectUl();
        }
        if(sSortby.length){
            sSortby.insertUl();
            sSortby.selectUl();
        }
	}else {
		if(sToolbar.length){
			sToolbar.selectpicker();    
        }
        if(sSortby.length){
            sSortby.selectpicker(); 
        }
	}
};

//After Layer Update
window.afterLayerUpdate = function() {
    var $ = jQuery;
    toolbarCategory();
    if ((typeof EM_QUICKSHOP_DISABLED == 'undefined' || !EM_QUICKSHOP_DISABLED) && !isMobile) {
        qs({
            itemClass: EM.QuickShop.QS_ITEM_CLASS,
            aClass: EM.QuickShop.QS_A_CLASS,
            imgClass: EM.QuickShop.QS_IMG_CLASS
        });
    }    
    setTimeout(function(){setEqualHeightItemsCategory();},500);
    if (EM.SETTING.DISABLE_AJAX_ADDTO != 0) {
        var sCompare = $('.link-compare');
        sCompare.emAjaxCompare({
            sidebarSelector: ".block-compare",
        });
        var sWishlist = $('.link-wishlist');
        sWishlist.emAjaxWishList();
    }
   
    if(EM.SETTING.COLOR_SWATCHES){ConfigurableSwatchesList.init(); }       
};

function setEqualHeightItemsCategory() {
    var $ = jQuery;
    var $list = $('#em-grid-mode');
    var len = $list.length;
    if(len){
        var gridItemMaxHeight = 0;
        var $listItems = $list.children().find('li.item');
        var lenLi = $listItems.length;
        if(lenLi){
            for(var j=0;j<lenLi;j++){
                $listItems.eq(j).css('height', '');
                gridItemMaxHeight = Math.max(gridItemMaxHeight, $listItems.eq(j).height());
            }
        }
        $listItems.css('height', gridItemMaxHeight + 'px');
    }
};