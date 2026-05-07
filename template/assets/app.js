$(document).ajaxStart(function() { Pace.restart(); });

$(document).ready(function() {

	window.setTimeout(function() {
		$(".alert-auto").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();
		});
	}, 3000);

	$(".select2").select2();

	$(".select2tag").select2({
		tags: true,
		maximumSelectionLength: 1
	});


	$(".colorpicker").colorpicker();

	$('.summernoteLarge').summernote({height: 400, maximumImageFileSize: 524288, dialogsInBody: true, disableDragAndDrop: true});
	$('.summernoteTicket').summernote({height: 300, maximumImageFileSize: 524288 });
	$('.summernote').summernote({height: 200, maximumImageFileSize: 524288, dialogsInBody: true, disableDragAndDrop: true});


	// append img-responsive to ticket replies
	$('.email-container').find('img').addClass('img-responsive');









	// ISSUES BOARD HANDLER
	$(function () {
		//"use strict";
		//jQuery UI sortable for the todo list
		$(".todo-list").sortable({
		  placeholder: "sort-highlight",
	      connectWith: ".todo-list",
		  handle: ".handle",
		  forcePlaceholderSize: true,
		  zIndex: 999999,
	      update: function (event, ui) {
	          var issueid = ui.item.context.id;
	          //var newstatus = ui.item.context.closest('.todo-list').id;
			  var newstatus = ui.item.context.parentElement.id;
	          var formData = {issueid:issueid,status:newstatus};
	          //alert(newstatus);
	          $.ajax({
	              data: formData,
	              type: 'POST',
	              url: 'index.php?qa=updateIssueStatus'
	          });

	      }
		});
	});


	// LOAD JQUERY KNOB
	$(function() {
		$(".knob").knob({
			draw : function () {
				// "tron" case
				if(this.$.data('skin') == 'tron') {

					var a = this.angle(this.cv)  // Angle
						, sa = this.startAngle          // Previous start angle
						, sat = this.startAngle         // Start angle
						, ea                            // Previous end angle
						, eat = sat + a                 // End angle
						, r = true;

					this.g.lineWidth = this.lineWidth;

					this.o.cursor
						&& (sat = eat - 0.3)
						&& (eat = eat + 0.3);

					if (this.o.displayPrevious) {
						ea = this.startAngle + this.angle(this.value);
						this.o.cursor
							&& (sa = ea - 0.3)
							&& (ea = ea + 0.3);
						this.g.beginPath();
						this.g.strokeStyle = this.previousColor;
						this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
						this.g.stroke();
					}

					this.g.beginPath();
					this.g.strokeStyle = r ? this.o.fgColor : this.fgColor ;
					this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
					this.g.stroke();

					this.g.lineWidth = 2;
					this.g.beginPath();
					this.g.strokeStyle = this.o.fgColor;
					this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
					this.g.stroke();
					return false;
				}
			}
		});
	});
});

var myRefreshTimeout;

function startAutorefresh(refreshPeriod) {
	myRefreshTimeout = setTimeout("window.location.reload();",refreshPeriod);
}

function stopAutorefresh() {
	clearTimeout(myRefreshTimeout);
	window.location.hash = 'stop'
}


function showM(url) {
	$('.modal-content').empty();

	$('.modal-content').load(url);
	$('#myModal').modal('show');
	stopAutorefresh();
}

function goBack() {
    window.history.back()
}

// Global Upload Progress Handler
$(document).on('submit', 'form', function(e) {
    if (e.isDefaultPrevented()) return; // Skip if other validation failed
    
    var $form = $(this);
    var files = $form.find('input[type="file"]').get();
    var hasFiles = false;
    
    for (var i = 0; i < files.length; i++) {
        if (files[i].files.length > 0) {
            hasFiles = true;
            break;
        }
    }

    if (hasFiles) {
        e.preventDefault();
        
        // Prepare FormData
        var formData = new FormData(this);
        
        // Show Swal Loading with Progress bar
        Swal.fire({
            title: 'Uploading...',
            html: '<div class="progress" style="height: 25px; border-radius: 12px; margin-top: 20px; background: #f1f5f9; overflow: hidden;">' +
                  '<div id="upload-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%; transition: width 0.3s ease; line-height: 25px; font-weight: bold; background-color: #3e81f1;">' +
                  '0%' +
                  '</div></div>' +
                  '<div id="upload-status" style="margin-top: 15px; font-size: 14px; color: #64748b; font-weight: 500;">Starting upload...</div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                // Swal.showLoading(); // We use our own progress bar
            }
        });

        // Use XHR for progress tracking
        var xhr = new XMLHttpRequest();
        xhr.open('POST', $form.attr('action') || window.location.href, true);
        
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                var percentComplete = Math.round((event.loaded / event.total) * 100);
                $('#upload-progress-bar').css('width', percentComplete + '%').text(percentComplete + '%').attr('aria-valuenow', percentComplete);
                
                var loadedMB = (event.loaded / 1024 / 1024).toFixed(1);
                var totalMB = (event.total / 1024 / 1024).toFixed(1);
                $('#upload-status').text('Uploaded ' + loadedMB + 'MB of ' + totalMB + 'MB');
                
                if (percentComplete === 100) {
                    $('#upload-status').text('Processing on server... Please wait.');
                }
            }
        };

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                Swal.fire({
                    icon: 'success',
                    title: 'Upload Complete!',
                    text: 'Process finished successfully.',
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect to the URL the server intended (XHR follows redirects, so responseURL is the final target)
                    window.location.href = xhr.responseURL || window.location.href;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'The server responded with an error (' + xhr.status + ').'
                });
            }
        };

        xhr.onerror = function() {
            Swal.fire('Error', 'A network error occurred during the upload.', 'error');
        };

        xhr.send(formData);
    }
});
