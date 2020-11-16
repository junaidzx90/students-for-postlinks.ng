(function ($) {
	'use strict';
	$(document).ready(function () {
		// Upload page media uploader=============================
		var limit = 5;//This variable for show_projects function
		var start = 0;//This variable for show_projects function
		var action = 'inactive';//This variable for show_projects function
		
		project_uploads();
		// START project_uploads
		function project_uploads() {
			$('#fileUpInp').change(function () {
				$('.file_path').text("");
				let filename = $('#fileUpInp').val().replace(/.*(\/|\\)/, '');
				//replace directory from file name
				$.ajax({ ///checking file ext
					type: "POST",
					url: _ajax_url.ajax_url,
					data: {
						action: "checking_file",
						filename: filename
					},
					dataType: "JSON",
					success: function (response) {
						if (response.inavalid) {
							$('.up_warning').show().html(response.inavalid);
							return false;
						} else {
							if ($('#fileUpInp').val()) {
								$('.file_catch').slideDown();
								$('.file_path').append(filename); //Show filename
								$('.up_warning').hide();
								$('.image_upload_btn').show();
							}
							$('.upbtn').hide();
							$('#btnSubmit').show();
							$('.form_area').slideDown();
						}
					}
				});
			});

			$('.project_name').keydown(function (e) {
				$('.project_name').val($('.project_name').val());
				if ($('.project_name').val().length > 30) {
					e.preventDefault();
					swal({
						title: "OOPS!",
						text: "You cannot write more than 30 characters",
						icon: "error"
					});
					setTimeout(function () {
						$('.project_name').attr("maxlength", 30);
						$('.project_name').val("");
					},1000)
				}
				$('.project_name').css('box-shadow', 'none');
			});

			$('.project_name').blur(function () {
				let name = $(this).val();
				$.ajax({ ///checking file ext
					type: "POST",
					url: _ajax_url.ajax_url,
					data: {
						action: "checking_file",
						existName: name
					},
					dataType: "JSON",
					success: function (response) {
						if (response.exist) {
							$('.existName').html(response.exist);
							$('.project_name').css('box-shadow', '0px 0px 4px 0px red');
							return false;
						}
						if (response.success) {
							$('.existName').html("");
							$('.project_name').css('box-shadow', 'none');
						}
					}
				});
			})

			$('#image_inp').change(function () {
				let imgName = $('#image_inp').val().replace(/.*(\/|\\)/, ''); //replace directory from file name
				let exten = imgName.substring(imgName.lastIndexOf('.') + 1);
				let expects = ["jpg", "jpeg", "svg", "gif", "png", "PNG"];
				
				if (expects.indexOf(exten) == -1) {
					$('.up_warning').html("Invalid image!").show();
					$('.upbtn').attr("disabled", true);
					return false;
				}
				if ($('#image_inp')[0].files[0].size > 10485760) {
					$('.up_warning').show().html("We are sorry try to upload maximum 2MB");
					return false;
				}

				if ($('.image_inp').val()) {
					let imgname = $('.image_inp').val().replace(/.*(\/|\\)/, ''); //replace directory from file name
					$('.upbtn').attr("disabled", false);
					$('.image_upload_btn').hide();
					$('.up_warning').hide();
					$('.icon_mark').show().append(imgname); //Show filename
				}
			});

			$('.file_desc').keydown(function (e) {
				$('.file_desc').val($('.file_desc').val());
				if ($('.file_desc').val().length > 70) {
					e.preventDefault();
					swal({
						title: "OOPS!",
						text: "You cannot write more than 70 characters",
						icon: "error"
					});
					setTimeout(function () {
						$('.file_desc').attr("maxlength", 70);
						$('.file_desc').val("");
					},1000)
				}
				$('.file_desc').css('box-shadow', 'none');
			});

			$('#uploadForm').submit(function (e) {
				if ($('#fileUpInp').val()) {
					if ($('.project_name').val() == "") {
						$('.project_name').css('box-shadow', '0px 0px 4px 0px red');
						return false;
					}else{
						$('.project_name').css('box-shadow', 'none');
					}
					if ($('#categories').val() == "") {
						$('#categories').css('box-shadow', '0px 0px 4px 0px red');
						return false;
					}else{
						$('.project_name').css('box-shadow', 'none');
					}
					if ($('.file_desc').val() == "") {
						$('.file_desc').css('box-shadow', '0px 0px 4px 0px red');
						return false;
					}else{
						$('.project_name').css('box-shadow', 'none');
					}
					if (!$('#image_inp').val()) {
						$('.up_warning').html("");
						$('.up_warning').html("Select your project image").show();
						return false;
					} else {
						$('.up_warning').html("").hide();
					}

					let imgName = $('#image_inp').val().replace(/.*(\/|\\)/, ''); //replace directory from file name
					let exten = imgName.substring(imgName.lastIndexOf('.') + 1);
					let expects = ["jpg", "jpeg", "svg", "gif", "png", "PNG"];
					
					if (expects.indexOf(exten) == -1) {
						$('.up_warning').html("Invalid image!").show();
						$('.upbtn').attr("disabled", true);
						return false;
					} else {
						e.preventDefault();
						$('#loader-icon').show();
						$('.progress').show();

						$(this).ajaxSubmit({
							type: "POST",
							url: _ajax_url.ajax_url,
							data: {
								action: "upload_media"
							},
							dataType: "json",
							beforeSend: function () {
								$(".progressbar").val('0');
							},
							uploadProgress: function (event, position, total, percentComplete) {
								$(".progressbar").animate({
									width: percentComplete+'%'
								}, {
									duration: 1000
								});
							},
							success: function (response) {
								$('#loader-icon').fadeOut();
								$('.up_warning').html("").hide();
								$(".progressbar").animate({
									width: '0%'
								});
								$('.progress').hide();
								$('.file_catch').hide();
								$('.form_area').slideUp(1000);
								$('.upbtn').show();
								$('#btnSubmit').hide();
								$('#fileUpInp').show();
								$('.icon_mark').hide();
								$('.image_upload_btn').hide();

								if (response.error) {
									$('.up_warning').html(response.error).fadeIn();
									$('.up_warning').fadeOut(2000);
								} else {
									$('#targetLayer').html("");
									action == "inactive";
									show_projects(limit, start);
									swal({
										title: "GOOD JOB!",
										text: "Your project is published",
										icon: "success"
									});
								}
							},
							resetForm: true
						});
					}
				}
				return false;
			});

			$(document).on('click', function (e) {//delete option toggle
				if ($(e.target).hasClass('project_menu') || $(e.target).hasClass('uploaded_dropdown') || $(e.target).hasClass('uploaded_item')) {
					$(e.target).parent().next('.uploaded_dropdown').show();
				} else {
					$('.uploaded_dropdown').hide();
				}

				$('.delete-project').on('click', function (e) {
					let project_id = $(this).attr('data-id');
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
									url: _ajax_url.ajax_url,
									data: {
										action: "delete_project",
										project_id: project_id
									},
									success: function (response) {
										$('.uploaded[data-id="' + response + '"]').slideUp();
										swal("oof! This project has been deleted!", {
											icon: "success",
										});
										return false;
									}
								});
							} else {
								swal("This project is safe!");
							}
						});//end popup
				});//end response
			});//end function for delete

		}
		// ENDS project_uploads
		//Show project data inside upload page
		function show_projects(limit, start) {
			$.ajax({
				method: "POST",
				url: _ajax_url.ajax_url,
				data: {
					action: "show_self_projects_data",
					limit: limit,
					start: start
				},
				beforeSend: function () {
					$('#loader-icon').show();
				},
				cache: false,
				success: function (response) {
					$('.up_warning').hide();
					$('.show-loadmore').remove();
					$('#targetLayer').append(response);
					if (response == "") {
						$('#loader-icon').fadeOut();
						$('.up_warning').html("No project found!").fadeIn();
						action = 'active';
					} else {
						$('#loader-icon').fadeOut();
						action = 'inactive';
					}
				}
			});
		} //end func
		if (action == "inactive") {
			action = "active";
			show_projects(limit, start);
		}
		//END =============Show project data inside upload page
		$(document).on('click',function (e) {
			if ($(e.target).hasClass('show-loadmore') && action == 'inactive') {
				action = "active";
				start = start + limit;
				show_projects(limit, start);
			}
		});
	});
})(jQuery);