a_log = function(){
	var base_url = $("base").attr("data");
	$(".a-login").click(function(){
		var email = $(".a-email").val();
		var password = $(".a-pass").val();
		$(".loader").show();
		$.ajax({
			url:base_url+"admin_login",
			type:"POST",
			data:{email:email, pass:password},
			complete:function(){
				$(".loader").hide();
			},
			success:function(response){
				if (response.status) {
					location.reload();
				}else{
					alert(response.m);
				}
			},
			error:function(){
				alert("Internet error, try again.");
			}
		})
	})
}
$(document).ready(function(){
	a_log();
})