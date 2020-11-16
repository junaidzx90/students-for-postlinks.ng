(function ($) {
	'use strict';
	$(document).ready(function () {
		// Download page =============================
		function cards_actions() {
			//menu btn
			$(document).on('click', function (e) {//delete option toggle
				if ($(e.target).hasClass('project_menus') || $(e.target).hasClass('uploaded_dropdowns') || $(e.target).hasClass('edit_count')) {
					$(e.target).next('.uploaded_dropdowns').show();
				} else {
					$('.uploaded_dropdowns').hide();
				}
			});

			//download
			$('.download_btn').on('click', function (e) {//load more button
				let project_id = $(this).attr('data-value');
				let mythis = $(this);
				$.ajax({
					type: "POST",
					url: _ajax_url.ajax_url,
					data: {
						action: "download_action",
						project_id: project_id
					},
					dataType: "json",
					success: function (response) {
						window.location.href = response.project;
						mythis.parent('.project').children('.down_count').children('.dcounts').html(' ' + response.count + ' ');
					}
				});
			});
			//edit downloads count ajax submit
			$('.edit_count').on('keypress', function (e) {
				if (e.keyCode === 13) {
					let student_id = $(this).attr('data-id');
					let project_id = $(this).attr('data-value');
					let values = $(this).val();
					let mythis = $(this);
								
					if (values) {
						$.ajax({
							type: "POST",
							url: _ajax_url.ajax_url,
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
								mythis.parent().parent().parent().children('.dcounts').html(' ' + data + ' ');
								mythis.attr('placeholder', data);
								return false;
							}
						});
					}
				}
			});
		}//Project card actions

		function loads(plimit) {//This function make loadmore facilities in frontend, acceptable only for filtering
			$('.project').hide();//Hide all loaded projects
			if ($('.project_item').children('.loadmore_project').length == false) {
				$('.project_item').append('<div class="loadmore_project"><button class="sload_btn">Load more</button></div>');//Add button if ajax daynamic btn not exist
			}
			$('.sload_btn').click(function () {//Make click event on loadmore button
				$('#loader-icon').show();
				setTimeout(function () {
					plimit = plimit + plimit;
					loads(plimit);
					$('#loader-icon').hide();
				}, 500);
			});
			var lenth = $('.project_item').children('.project').length;//Length for getting limits
			var listElements = $('.project_item').children('.project');
			for (let i = 0; i < plimit; i++) {
				$(listElements[i]).show();//Show project depending limits
				lenth--;//make length minus as show per click
			}
			if (lenth < 0 ) {
				$('.sload_btn').remove();//Hide loadmore button if length minus value
			}	
		}
		download_page();
		function download_page() {
			// load all projects
			let plimit = 12;
			let pstart = 0;
			all_projects(plimit, pstart);
			function all_projects(plimit, pstart) {
				$.ajax({
					type: "POST",
					url: _ajax_url.ajax_url,
					data: {
						action: "show_all_project_data",
						limit: plimit,
						start: pstart
					},
					cache: false,
					beforeSend: function () {
						$('#loader-icon').show();
					},
					success: function (response) {
						if (!response) {
							$('#loader-icon').fadeOut();
							$('.load_btn').hide();
							$('.up_warning').html("No project found!").show();
						} else {
							$('#loader-icon').fadeOut();
							$('.loadmore_project').remove();
							$('.project_item').append(response);
							cards_actions();
						}
					}
				});
			}
			$(document).on('click', function (e) {//load more button
				if ($(e.target).hasClass('load_btn')) {
					pstart = pstart + plimit;
					all_projects(plimit, pstart);
				}
			});
			// ==================SEARCHES=======================

			searching();
			function searching() {//Live searching
				// ==================LIVE SEARCH=======================
				$('.search').keyup(function (e) {
					$('.category').val("");
					$('.button-group-pills').hide();
					$('.recent_project').prop('checked', false);
					$('.popular_project').prop('checked', false);
					pstart = 0;
					let svalue = $(this).val();
					let noncess = $('input[name="livesearch_nonce"]').val();
					$.ajax({
						type: "POST",
						url: _ajax_url.ajax_url,
						data: {
							action: "live_searching_students_name",
							liveSearch: "liveSearch",
							svalue: svalue,
							start: pstart,
							noncess: noncess,
							limit: plimit
						},
						cache: false,
						beforeSend: function () {
							$('#loader-icon').show();
						},
						success: function (response) {
							if (response) {
								$('.up_warning').hide();
								$('#loader-icon').hide();
								$('.project_item').html(response);
								cards_actions();
								loads(plimit);
							} else {
								$('.project_item').html(response);
								$('#loader-icon').hide();
								$('.up_warning').show().html("No project found!");
							}
						}
					});
					
				});//end live search
				// ==================FILTER CATEGORY=======================
				$('.category').change(function () {
					$('.livesearch').val('');
					$('.button-group-pills').show();
					$('.recent_project').prop('checked', false);
					$('.popular_project').prop('checked', false);
					pstart = 0;
					let svalue = $(this).val();
					$.ajax({
						type: "POST",
						url: _ajax_url.ajax_url,
						data: {
							action: "live_searching_students_name",
							category_search: "category_search",
							svalue: svalue,
							start: pstart,
							limit: plimit
						},
						cache: false,
						beforeSend: function () {
							$('#loader-icon').show();
						},
						success: function (response) {
							if (response) {
								$('.up_warning').hide();
								$('#loader-icon').hide();
								$('.project_item').html(response);
								cards_actions();
								loads(plimit);
							} else {
								$('.project_item').html(response);
								$('#loader-icon').hide();
								$('.up_warning').show().html("No project found!");
							}
						}
					});
				});
				// ==================FILTERS=======================
				$('.recent_project').change(function () {
					// Recent post request to server, it's show last 1 months results
					if ($(this).prop('checked') == true && $('.popular_project').prop('checked') == false) {
						let category = $('.category').val();
						$.ajax({
							type: "POST",
							url: _ajax_url.ajax_url,
							data: {
								action: "live_searching_students_name",
								category: category,
								recentvalue: 'recentvalue',
								start: pstart,
								limit: plimit
							},
							cache: false,
							beforeSend: function () {
								$('#loader-icon').show();
							},
							success: function (response) {
								if (response) {
									$('.up_warning').hide();
									$('#loader-icon').hide();
									$('.project_item').html(response);
									cards_actions();
									loads(plimit);
								} else {
									$('.project_item').html(response);
									$('#loader-icon').hide();
									$('.up_warning').show().html("No project found!");
								}
							}
						});
					} else {
						// If recent filter is off then checking popular filter active or not, if popular it will call popular filters to show popular projects
						if ($('.popular_project').prop('checked') == true && $('.recent_project').prop('checked') == false) {
							let category = $('.category').val();
							$.ajax({
								type: "POST",
								url: _ajax_url.ajax_url,
								data: {
									action: "live_searching_students_name",
									category: category,
									popularvalue: 'popularvalue',
									start: pstart,
									limit: plimit
								},
								cache: false,
								beforeSend: function () {
									$('#loader-icon').show();
								},
								success: function (response) {
									if (response) {
										$('.up_warning').hide();
										$('#loader-icon').hide();
										$('.project_item').html(response);
										cards_actions();
										loads(plimit);
									} else {
										$('.project_item').html(response);
										$('#loader-icon').hide();
										$('.up_warning').show().html("No project found!");
									}
								}
							});
						} else {
							// If Recent and popuar filter is off then request categories to server
							if ($('.recent_project').prop('checked') == false && $('.popular_project').prop('checked') == false) {
								let svalue = $('.category').val();
								$.ajax({
									type: "POST",
									url: _ajax_url.ajax_url,
									data: {
										action: "live_searching_students_name",
										category_search: "category_search",
										svalue: svalue,
										start: pstart,
										limit: plimit
									},
									cache: false,
									beforeSend: function () {
										$('#loader-icon').show();
									},
									success: function (response) {
										if (response) {
											$('.up_warning').hide();
											$('#loader-icon').hide();
											$('.project_item').html(response);
											cards_actions();
											loads(plimit);
										} else {
											$('.project_item').html(response);
											$('#loader-icon').hide();
											$('.up_warning').show().html("No project found!");
										}
									}
								});
							}
						}
					}
					// If popular and recent both filter is active then call to server for showing boths results
					if ($(this).prop('checked') == true && $('.popular_project').prop('checked') == true) {
						let category = $('.category').val();
						$.ajax({
							type: "POST",
							url: _ajax_url.ajax_url,
							data: {
								action: "live_searching_students_name",
								category: category,
								twoselected: 'twoselected',
								start: pstart,
								limit: plimit
							},
							cache: false,
							beforeSend: function () {
								$('#loader-icon').show();
							},
							success: function (response) {
								if (response) {
									$('.up_warning').hide();
									$('#loader-icon').hide();
									$('.project_item').html(response);
									cards_actions();
									loads(plimit);
								} else {
									$('.project_item').html(response);
									$('#loader-icon').hide();
									$('.up_warning').show().html("No project found!");
								}
							}
						});
					}
				});

				$('.popular_project').change(function () {
					// Popular post request to server, it's show last 1 months results
					if ($(this).prop('checked') == true && $('.recent_project').prop('checked') == false) {
						let category = $('.category').val();
						$.ajax({
							type: "POST",
							url: _ajax_url.ajax_url,
							data: {
								action: "live_searching_students_name",
								category: category,
								popularvalue: 'popularvalue',
								start: pstart,
								limit: plimit
							},
							cache: false,
							beforeSend: function () {
								$('#loader-icon').show();
							},
							success: function (response) {
								if (response) {
									$('.up_warning').hide();
									$('#loader-icon').hide();
									$('.project_item').html(response);
									cards_actions();
									loads(plimit);
								} else {
									$('.project_item').html(response);
									$('#loader-icon').hide();
									$('.up_warning').show().html("No project found!");
								}
							}
						});
					} else {
						// If popular filter is off then checking recent filter active or not, if recent it will call recent filters to show popular projects
						if ($('.recent_project').prop('checked') == true && $('.popular_project').prop('checked') == false) {
							let category = $('.category').val();
							$.ajax({
								type: "POST",
								url: _ajax_url.ajax_url,
								data: {
									action: "live_searching_students_name",
									recentvalue: "recentvalue",
									category: category,
									start: pstart,
									limit: plimit
								},
								cache: false,
								beforeSend: function () {
									$('#loader-icon').show();
								},
								success: function (response) {
									if (response) {
										$('.up_warning').hide();
										$('#loader-icon').hide();
										$('.project_item').html(response);
										cards_actions();
										loads(plimit);
									} else {
										$('.project_item').html(response);
										$('#loader-icon').hide();
										$('.up_warning').show().html("No project found!");
									}
								}
							});
						} else {
							// If Recent and popuar filter is off then request categories to server
							if ($('.recent_project').prop('checked') == false && $('.popular_project').prop('checked') == false) {
								let svalue = $('.category').val();
								$.ajax({
									type: "POST",
									url: _ajax_url.ajax_url,
									data: {
										action: "live_searching_students_name",
										category_search: "category_search",
										svalue: svalue,
										start: pstart,
										limit: plimit
									},
									cache: false,
									beforeSend: function () {
										$('#loader-icon').show();
									},
									success: function (response) {
										if (response) {
											$('.up_warning').hide();
											$('#loader-icon').hide();
											$('.project_item').html(response);
											cards_actions();
											loads(plimit);
										} else {
											$('.project_item').html(response);
											$('#loader-icon').hide();
											$('.up_warning').show().html("No project found!");
										}
									}
								});
							}
						}
					}
					// If popular and recent both filter is active then call to server for showing boths results
					if ($(this).prop('checked') == true && $('.recent_project').prop('checked') == true) {
						let category = $('.category').val();
						$.ajax({
							type: "POST",
							url: _ajax_url.ajax_url,
							data: {
								action: "live_searching_students_name",
								category: category,
								twoselected: 'twoselected',
								start: pstart,
								limit: plimit
							},
							cache: false,
							beforeSend: function () {
								$('#loader-icon').show();
							},
							success: function (response) {
								if (response) {
									$('.up_warning').hide();
									$('#loader-icon').hide();
									$('.project_item').html(response);
									cards_actions();
									loads(plimit);
								} else {
									$('.project_item').html(response);
									$('#loader-icon').hide();
									$('.up_warning').show().html("No project found!");
								}
							}
						});
					}
				});
			}
		}//END Download page
	});
})(jQuery);