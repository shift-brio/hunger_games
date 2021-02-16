pos_login = function(){
	$(".pos-log-go").click(function(){
		var email = $(".pos-log-email").val();
		var pass = $(".pos-log-pass").val();

		if (email && pass) {
			pos_logger(email,pass);
		}
	})
	$(".pos-log-pass").on("keypress",function(e){
		if (e.which == 13) {
			var email = $(".pos-log-email").val();
			var pass = $(".pos-log-pass").val();

			if (email && pass) {
				pos_logger(email,pass);
			}
		}
	})
	pos_logger = function(email = false, pass = false){
		if (email && pass) {
			$(".pos-log-load").show();
			$.ajax({
				url:base_url+"login",
				type:"POST",
				data:{email:email,pass:pass},
				complete:function(){
					$(".pos-log-load").hide();
				},
				success:function(response){
					if (response.status) {
						location.reload();
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Internet error, check your connection and try again.");
				}
			})
		}
	}
}
$(document).ready(function(){
	pos_login();
})