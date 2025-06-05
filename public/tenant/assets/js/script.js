// Related Product Slider
$('.relatedpro_slider').slick({
    dots: true,
    arrows: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 200,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 1025,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
            }
        },
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 601,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});
// Product Categories Toggle
$(document).ready(function () {
    $('.toggle_arrow').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $parent = $(this).closest('.have_sub_cat');

        // Close others
        $('.have_sub_cat').not($parent).removeClass('open').find('.sub_category_list').slideUp();

        // Toggle this one
        $parent.toggleClass('open');
        $parent.find('.sub_category_list').stop(true, true).slideToggle();
    });
    $('.sub_toggle_arrow').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $parent = $(this).closest('.have_subsub_cat');

        // Close others
        $('.have_subsub_cat').not($parent).removeClass('open').find('.subsub_category_list').slideUp();

        // Toggle this one
        $parent.toggleClass('open');
        $parent.find('.subsub_category_list').stop(true, true).slideToggle();
    });
});
// Search Modal
$(document).ready(function () {
    $('.search').on('click', function () {
        $('.search_modal').addClass('open');
        $('body').addClass('search_modal_open');
    });

    $('.close_modal').on('click', function () {
        $('.search_modal').removeClass('open');
        $('body').removeClass('search_modal_open');
    });

    // Optional: Close when clicking outside modal content
    $(document).on('click', function (e) {
        if (
            $('.search_modal').hasClass('open') &&
            !$(e.target).closest('.search_modal, .search').length
        ) {
            $('.search_modal').removeClass('open');
            $('body').removeClass('search_modal_open');
        }
    });
});
// Mobile Menu
$('.mobile_menu').on('click', function(e) {
    e.stopPropagation();
    $('.main_menu').toggleClass('active');
});
$(document).on('click', function(e) {
    if ($('.main_menu').hasClass('active') &&
        !$(e.target).closest('.main_menu, .mobile_menu').length) {
        $('.main_menu').removeClass('active');
    }
});
