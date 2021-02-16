$(document).ready(function(){
	$("body").append('<div class="alert-center"></div>');
	main_response = "";
	
	alert_count = 0;
	base_url = $("base").attr("data");
	alertcenter();
})
alertcenter = function(){	
	notify = function(data = "",type = "normal",options = {closer:true,duration:false}){
		var alert_box = $(".alert-center");
		if (type == "danger") {
			var alert_color = "#ff0000";
		}else{
			alert_color = "";
		}
		if (options.closer == false) {
			var alert_class = "closer-off";
		}else{
			var alert_class = "";
		}
		alert_count = alert_count + 1;
		alert_id = alert_count;
		var alert_data = "<div class='alert-item alert_"+alert_id+"' data-alert-id='"+alert_id+"'"+
				 			"data-alert-close-state='false'>"+
							"<div class='alert-text'><span style='color:"+alert_color+"'>"+
								data +
							"</span></div>"+
							"<div class='alert-close' data-close-id='"+alert_id+"'>"+
								"<button class='material-icons "+alert_class+" close-alert btn-none'>close</button>"+
							"</div>"+
						"</div>";
		alert_box.prepend(alert_data);
		alert_center();
		if (options.duration == true && options.closer == false) {
			setTimeout(function(){
				var alerter =  $(".alert_"+alert_id);
				alerter.attr("data-alert-close-state","true");
				setTimeout(function(){
					alerter.remove();
				},10)					
			},3000)
		}
	}
	function alert_center(){
		alert_close = $(".close-alert");
		alert_close.each(function(){
			$(this).click(function(){
				var alert_id    = $(this).parent().parent().attr("data-alert-id")
				var alert_item  = $(".alert_"+alert_id);
				var alert_state = alert_item.attr("data-alert-close-state");
				if (alert_state == "false") {
					alert_item.attr("data-alert-close-state","true");
					setTimeout(function(){
						alert_item.remove();
					},10)						
				} else{
					alert_item.attr("data-alert-close-state","false");						
				}
			})
		})
	}
	alert_center();
	alert = function(data){
		notify(data);
	}
}