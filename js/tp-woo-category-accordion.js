jQuery( document ).ready( function( $  ) {
	"use strict";

    $('.tp-woo-category-accordion > li').each(function() {
        if ($(this).find('.sub-categories').length) {
            $(this).addClass('has-subcategories').find('.sub-categories').hide();
        }
    });
    $('.tp-woo-category-accordion > li.has-subcategories > a').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent();
        $('.tp-woo-category-accordion > li.has-subcategories').not($parent).removeClass('active').find('.sub-categories').slideUp();
        $parent.toggleClass('active').find('.sub-categories').slideToggle();
    });

});