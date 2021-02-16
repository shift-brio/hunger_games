p_log = function(){
	var base_url = $("base").attr("data");
	$(".p-login").click(function(){
		var email = $(".p-email").val();
		var password = $(".p-pass").val();
		$(".loader").show();
		$.ajax({
			url:base_url+"partner_login",
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
	p_log();
})