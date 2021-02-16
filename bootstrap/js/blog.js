(function($, window, undefined) {
    //is onprogress supported by browser?
    var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());

    //If not supported, do nothing
    if (!hasOnProgress) {
    	return;
    }
    
    //patch ajax settings to call a progress callback
    var oldXHR = $.ajaxSettings.xhr;
    $.ajaxSettings.xhr = function() {
    	var xhr = oldXHR();
    	if(xhr instanceof window.XMLHttpRequest) {
    		xhr.addEventListener('progress', this.progress, false);
    	}

    	if(xhr.upload) {
    		xhr.upload.addEventListener('progress', this.progress, false);
    	}

    	return xhr;
    };
})(jQuery, window);
$(document).ready(function(){
	blogger();
	imager(true);
	socials();
})
blogger = function(){
	base_url = $("base").attr("data")
	$(".save-blog").click(function(){
		var head = $(".blog-title");
		var body = CKEDITOR.instances['blog-content'];
		var image = $(".blog-image");

		title = head.val();
		content = body.getData();

		if (title.length > 2) {
			if (content.length > 10) {
				var data = new FormData();
				
				if (image.val()!='') {			    
					jQuery.each(jQuery('input.blog-image')[0].files, function(i, file) {
						data.append('file-'+i, file);
					});
				};
				data.append('title', title);
				data.append('content', content);
				edit = false;

				if(head.attr("data-edit") == "true") {
					edit = true;
					data.append("edit",true)
					data.append("post",$(".blog-info").attr("data-post"));
				}
				
				$.ajax({
					url:base_url+"blog/save",
					type:"POST",
					data:data,
					contentType: false,       
					cache: false,             
					processData:false,
					progress: function(e) {
						if(e.lengthComputable) {
							var pct = (e.loaded / e.total) * 100 + '%';
							loading("normal",true,pct)
						}
						else {
							loading("infin",true);
						}
					},
					complete:function(){
						loading("infin",false)
						loading("normal",false)
					},
					success:function(response){
						if (response.status) {
							if(edit){
								location.reload();
							}
							image.val("");
							head.val("");
							body.setData("");
							show_info("The article has been published",false);
						}else{
							show_info(response.m,true);
						}
					},
					error:function(){
						show_info("Internet error, try again",true);
					}
				})
			}else{
				show_info("Input valid post content",true);
			}
		}else{
			show_info("Input valid post title",true);
		}
	})
	show_info = function(text = false,error = false){
		info = $(".blog-info");
		$("*").animate({scrollTop:0}, 250);
		info.html("");
		info.removeClass("error");
		if (error) {
			info.addClass("error")
		}
		info.html(text)
		info.show()
		setTimeout(function(){
			info.slideUp("fast");
		},8000)
	}
	loading = function(type,state,value = false){
		loader = $(".prog-indic")
		if (type == "infin") {
			if (state == false) {
				loader.removeClass("prog-anim")
			}
			else{
				loader.addClass("prog-anim")			
			}
		}else{
			if (state == false) {
				loader.css("width","0%")
			}
			else{
				loader.css("width",value+"%")
			}
		}
	}
	$(".rm-image").click(function(){
		var post = $(".blog-info").attr("data-post");		
		con = confirm("Remove image?")
		if (con) {
			loading("infin",true);
			$.ajax({
				url:base_url+"blog/remove_image",
				type:"POST",
				data:{post:post},
				complete:function(){				
					loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						$(".edit-image").remove();
						show_info("Image removed",false);
					}else{
						show_info(response.m,true);
					}
				},
				error:function(){
					show_info("Internet error, try again",true);
				}
			})
		}
	})
	$(".rem-blog").click(function(){
		var post = $(".blog-info").attr("data-post");		
		con = confirm("Delete post?");
		if (con) {
			loading("infin",true);
			$.ajax({
				url:base_url+"blog/remove_post",
				type:"POST",
				data:{post:post},
				complete:function(){				
					loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						location.href = base_url+"blog";
					}else{
						show_info(response.m,true);
					}
				},
				error:function(){
					show_info("Internet error, try again",true);
				}
			})
		} 
	})
}
imager = function(init = false, div = "main-image > img"){
	if (init) {
		$("body").prepend(
			'<div class="image_viewer">'
				+'<div class="row">'
					+'<div class="col s12 m12 l12">'
						+'<button class="material-icons image_closer">arrow_back</button>'
						+'<div class="row">'
							+'<div class="m12 s12 l12">'
								+'<img class="viewer" src="<?php echo base_url("media/blog/default.png"); ?>" alt="">'
							+'</div>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>'
			);
	}
	$("." + div).each(function(){		
		$(this).click(function(){
			var src = $(this).attr("src");
			var image_viewer = $(".image_viewer");
			var viewer = $(".viewer")
			image_viewer.show();			
			$("html,body").css("overflow","hidden");
			viewer.attr("src",src);
		})
	})
	$(".image_closer").click(function(){			
			var image_viewer = $(".image_viewer");
			var viewer = $(".viewer")
			image_viewer.hide();						
			viewer.attr("src","");
			$("html,body").css("overflow","auto");
	})
}
socials = function(){
	;(function($){
  $.fn.customerPopup = function (e, intWidth, intHeight, blnResize) {
	    
	    // Prevent default anchor event
	    e.preventDefault();
	    
	    // Set values for window
	    intWidth = intWidth || '500';
	    intHeight = intHeight || '400';
	    strResize = (blnResize ? 'yes' : 'no');

	    // Set title and open popup with focus on it
	    var strTitle = ((typeof this.attr('title') !== 'undefined') ? this.attr('title') : 'Social Share'),
	        strParam = 'width=' + intWidth + ',height=' + intHeight + ',resizable=' + strResize,            
	        objWindow = window.open(this.attr('href'), strTitle, strParam).focus();
	  }
	  
	  /* ================================================== */
	  
	  $(document).on('mouseover',function () {
	    $('.customer.share').on("click", function(e) {
	      $(this).customerPopup(e);
	    });
	  });
	    
	}(jQuery));
	$('.fb-share').click(function(e) {
        e.preventDefault();       
        window.open($(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });
}