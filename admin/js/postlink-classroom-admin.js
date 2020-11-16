(function( $ ) {
	'use strict';

	$(document).ready(function () {
		// Show the first tab and hide the rest
		$("#tabs-nav li:first-child").addClass("active");
		$(".tab-content").hide();
		$(".tab-content:first").show();

		// Click function
		$("#tabs-nav li").click(function () {
		$("#tabs-nav li").removeClass("active");
		$(this).addClass("active");
		$(".tab-content").hide();

		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
		});
	});
	
	function formdatasave() {
		$(document).on('click', '.save-btn', function () {
			let perpage_post = document.getElementById('posts-display').value;
			let users = document.getElementById('select-user').value;
			let _slug = document.getElementById('posts_slug').value;

			let posts_slug = "";
			if (!_slug) {
				posts_slug = "my-posts";
			} else {
				posts_slug = _slug;
			}
			$.ajax({
				type: "POST",
				url: admin_ajax_url.ajax_url,
				data: {
					action: "configured_students_page",
					post_shows: perpage_post,
					users: users,
					posts_slug: posts_slug
				},
				beforeSend: function () {
					$('.warning').text('Submitting...');
					$('.warning').show();
				},
				success: function (response) {
					$('#posts_slug').attr("placeholder", posts_slug).val("");
					$('.warning').show();
					$('.warning').css('color', 'green');
					$('.warning').text('Success');
					$('.warning').fadeOut(2000);
				}
			});
		});
	}

	// Add category
	$('.add-btn').on('click', function () {
		let category = $('.category').val();
		$.ajax({
			type: "POST",
			url: admin_ajax_url.ajax_url,
			data: {
				action: "add_category",
				category: category
			},
			beforeSend: function () {
				$('.warning2').css('color', '#444').text('Submitting...').fadeIn();
			},
			dataType: "json",
			success: function (response) {
				if (response.exist) {
					$('.warning2').css('color', 'red').text(response.exist).fadeIn();
				}
				if (response.success) {
					$('.warning2').css('color', '#444').text(response.success).fadeIn();
					$('.category').val("");
					get_categories();
					setTimeout(function () {
						$('.warning2').fadeOut();
					}, 1000);
				}
			}
		});
	});
	get_categories();
	function get_categories() {
		$.ajax({
			type: "POST",
			url: admin_ajax_url.ajax_url,
			data: {
				action: "get_categories"
			},
			success: function (response) {
				$('.category_table tbody').html("");
				$('.category_table tbody').append(response);

				$('.cat_del').on('click', function () {
					let cat_val = $(this).attr("data-value");
					swal({
						title: "Are you sure?",
						text: "Once deleted, you will not be able to recover this Category!",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
						.then((willDelete) => {
							if (willDelete) {
								$.ajax({
									type: "POST",
									url: admin_ajax_url.ajax_url,
									data: {
										action: "delete_my_category",
										cat_id: cat_val
									},
									dataType: "JSON",
									success: function (response) {
										if (response.success) {
											swal(response.success, {
												icon: "success",
											});
											get_categories();
										} else {
											swal({
												title: "OOPS!",
												text: "Something problems, please try again!",
												icon: "error"
											})
										}
									}
								});
							} else {
								swal("This project is safe!");
							}
						});//end popup

					
				});
			}
		});
	}

	//edit downloads count ajax submit
	$('.edit_inp').on('keypress', function (e) {
		if (e.keyCode === 13) {
			let student_id = $(this).attr('data-sid');
			let project_id = $(this).attr('data-value');
			let values = $(this).val();
			let mythis = $(this);
			
			if (values) {
				$.ajax({
					type: "POST",
					url: admin_ajax_url.ajax_url,
					data: {
						action: "edit_requests",
						project_id: project_id,
						student_id: student_id,
						values: values
					},
					success: function (response) {
						let data = '';
						if (response == "") {
							data = '0';
						} else {
							data = response;
						}
						mythis.attr('placeholder', data + ' Times');
						mythis.val("");
						swal({
							title: "GOOD JOB",
							text: "Count is updated!",
							icon: "success"
						})
						return false;
					}
				});
			}
			
		}
	});
	//download
	$('.download_btn').on('click', function (e) {//load more button
		let project_id = $(this).attr('data-value');
		let mythis = $(this);
		$.ajax({
			type: "POST",
			url: admin_ajax_url.ajax_url,
			data: {
				action: "download_action",
				project_id: project_id
			},
			dataType: "json",
			success: function (response) {
				window.location.href = response.project;
				mythis.parent().prev().children('.dcounts').html(' ' + response.count + ' ');
			}
		});
	});

	$('.del_btn').on('click', function (e) {
		let mythis = $(this);
		let student_id = $(this).attr('data-sid');
		let project_id = $(this).attr('data-value');
		swal({
			title: "Are you sure?",
			text: "Once deleted, you will not be able to recover this project!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
		})
			.then((willDelete) => {
				if (willDelete) {
					$.ajax({
						type: "post",
						url: admin_ajax_url.ajax_url,
						data: {
							action: "delete_project_from_admin",
							student_id: student_id,
							project_id: project_id
						},
						success: function (response) {
							mythis.parent().parent().remove();
							swal("oof! "+response+"!", {
								icon: "success",
							});
							return false;
						}
					});
				} else {
					swal("This project is safe!");
				}
			});//end popup
	});//end delete

	// suppliers list table searchbox
	$(".search_project").on("keyup", function () {
		var value = $(this).val().toLowerCase();
		$(".project_table tbody tr").filter(function () {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
	formdatasave();
})( jQuery );
