$(document).ready(()=>{
$(".p-id").focus();		
$(".join-submit").click(function(){
	var id    = $(".p-id").val();
	var phone = $(".p-phone").val();
	var pin = $(".p-pin").val();
	var url = location.href;
	url = url.split("/");
	var email = url[url.length - 1];

	var terms = $(".terms_check").attr("value");	
	if (validateInput(phone,'phone') && test_length(id) == 8 && test_length(pin) > 2 && validateInput(email,"email")) {
		if (terms == true || terms == 'true') {

			$(".loader").show();
			$.ajax({
				url:base_url+"save_partner",
				type:"POST",
				data:{id:id,phone:phone,pin:pin,email:email},
				complete:function(){
					$(".loader").hide();
				},
				success:function(response){
					if (response.status) {
						//$(".phone-dialog").show();
						//$(".code").focus();
                  location.reload();
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Internet error, check connection and try agin");
				}
			})
		}else{
			alert("To continue, accept <a target='_blank' href='http://www.benfils.com'>Benfils</a> terms of service.")
		}
	}else{
		alert("Input valid values");				
	}
})
dialog = function(){
	$(".cancel-dialog").click(function(){
		$(".code").val();
		$(".phone-dialog").hide('fast')
	})
	$(".check-code").click(function(){
		var code = $(".code").val();
		if (test_length(code) == 5) {
			$(".loader").show();
			$.ajax({
				url:base_url+"check_code",
				type:"POST",
				data:{code:code},
				complete:function(){
					$(".loader").hide();
				},
				success:function(response){
					if (response.status) {
						location.reload();
					}else{
						alert(response.m)
					}
				},
				error:function(){
					alert("Internet error, check your connection and try again.")
				}
			})
		}else{
			alert("Enter a valid code.");
		}
	})
	$(".v-before").click(function(){
		$(".code").val("");
		$(".code").focus();
		$(".phone-dialog").show()
	})
	$(".refresh-code").click(function(){
		$(".loader").show();
		$.ajax({
			url:base_url+"refresh_code",
			type:"POST",
			complete:function(){
				$(".loader").hide();
			},
			success:function(response){
				if (response.status) {
					alert("A new code has been sent to your phone.")
				}else{
					alert(response.m);
				}
			},
			error:function(){
				alert("Internet error, check your connection and try again.")
			}
		})
	})
}
dialog();
})