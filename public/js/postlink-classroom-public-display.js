(function ($) {
	'use strict';
	$(document).ready(function () {
		// START students posts
		let page = 2;
		function loaddata(pages) {
			$.ajax({
				type: "POST",
				url: _ajax_url.ajax_url,
				data: {
					action: "postlink_students_posts",
					paged: pages
				},
				beforeSend: function () {
					$('.pst-loadmore').text('Loading...');
				},
				success: function (response) {
					if (response) {
						$('.warn2').remove();
						$('.pst-loadmore').remove();
						$('.published_post').html("MY PUBLISHED POSTS");
						$('.loaddata').append(response);
						$('.pst-loadmore').text('See More');
					} else {
						$('.pst-loadmore').text('No more data');
						$('.pst-loadmore').css('cursor', 'not-allowed');
						$('.pst-loadmore').attr('disabled', true);
					}
				}
			});
			// If posts is not create
			if ($('.loaddata').children('.publish-content').length == 0 && $('.loaddata').children('.warn2').length == 0) {
				$('.loaddata').append('<span class="warn2">No post found.</span>')
			}
		}
		loaddata();

		$(document).on('click', '.pst-loadmore', function () {
			let more = page++;
			loaddata(more);
		});
		// ENDS students posts
	});
})(jQuery);