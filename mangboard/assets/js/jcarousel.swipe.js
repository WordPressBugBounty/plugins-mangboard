(function($) {
    $(function() {
		var jcarousel = $('.mb-board .jcarousel');
        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();

                carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');
            })
			.swipe({
				swipeLeft: function(event, direction, distance, duration, fingerCount) {   
					$(this).jcarousel('scroll', '+=1');
				},
				swipeRight: function(event, direction, distance, duration, fingerCount) {
					$(this).jcarousel('scroll', '-=1');
				}
			})
            .jcarousel({
                wrap: 'circular'
            });

        $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

		$('.jcarousel-control-1')
			.on('click', function(e) {
				$('.jcarousel-pagination a').eq(0).trigger('click');
            })
		$('.jcarousel-control-2')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(1).trigger('click');
            })
		$('.jcarousel-control-3')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(2).trigger('click');
            })
		$('.jcarousel-control-4')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(3).trigger('click');
            })
		$('.jcarousel-control-5')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(4).trigger('click');
            })
		$('.jcarousel-control-6')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(5).trigger('click');
            })
		$('.jcarousel-control-7')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(6).trigger('click');
            })
		$('.jcarousel-control-8')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(7).trigger('click');
            })
		$('.jcarousel-control-9')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(8).trigger('click');
            })
		$('.jcarousel-control-10')
            .on('click', function(e) {
				$('.jcarousel-pagination a').eq(9).trigger('click');
            })

        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });
})(jQuery);