checkers = function(){
	$("[type=checkbox]").click(function(){
		val = $(this).attr("value");
		if (val == 'true') {
			$(this).attr("value",false);
		}else{
			$(this).attr("value",true);
		}
	})
}
base_url = "";
$(document).ready(function(){
	base_url = $(".url-holder").val()
	$(".save-sign").click(function(){
		var first_name =   {elem:$(".first_name"),v:$(".first_name").val()};
		var last_name  =   {elem:$(".last_name"),v:$(".last_name").val()};
		var u_mail     =   {elem:$(".sign_email"),v:$(".sign_email").val()};
		var phone_number = {elem:$(".phone_number"),v:$(".phone_number").val()};
		var u_country  =   {elem:$(".u_country"),v:$(".u_country").val()};
		var sign_pass  =   {elem:$(".sign_pass"),v:$(".sign_pass").val()};
		var sign_pass_confirm = {elem:$(".sign_pass_confirm"),v:$(".sign_pass_confirm").val()}; 
		var terms = $(".terms_check").attr("value");
		tested = [{text:"First Name",name:"first_name",item:first_name},{text:"Last Name",name:"last_name",item:last_name},{text:"Email",name:"u_mail",item:u_mail},{text:"Phone number",name:"phone_number",item:phone_number},{text:"Country",name:"u_country",item:u_country}];

		for (var i = 0; i < tested.length; i++) {
		 	name  = tested[i].name;
		 	item  = tested[i].item;
		 	text  = tested[i].text;
		 	if (name != "phone_number" && name != "u_mail") {
		 		if (test_length(item.v) < 2 ) {
		 			item.elem.addClass("emt");
		 			item.valid = false;			 			
		 		}else{
		 			item.elem.removeClass("emt");
		 			item.valid = true;
		 		}
		 	}
		 	else if(name == "u_mail"){
		 		if (!validateEmail(item.v)) {
		 			item.elem.addClass("emt");
		 			item.valid = false;			 			
		 		}else{
		 			item.elem.removeClass("emt");
		 			item.valid = true;
		 		}
		 	}
		 	else if(name == "phone_number"){
		 		if (!validateInput(item.v,'phone')) {
		 			item.elem.addClass("emt");
		 			item.valid = false;			 			
		 		}else{
		 			item.elem.removeClass("emt");		 			
		 			item.valid = true;		 			
		 		}
		 	}
		 } 
		 if (first_name.valid && last_name.valid && u_mail.valid && phone_number.valid && u_country.valid ) 
		 {
		 	 if (test_length(sign_pass.v) < 5) {
			 		sign_pass.elem.addClass("emt");	
			 		notify("Invalid password","normal",{closer:false,duration:true});			 		
			 }else{
			 	sign_pass.elem.removeClass("emt");
			 	if (sign_pass.v == sign_pass_confirm.v) {
			 		sign_pass_confirm.elem.removeClass("emt");
			 		if (terms == true || terms == "true") {
			 			$(".sign-loader").show()
			 			$.ajax({
			 				url:base_url+"sign",
			 				type:"POST",
			 				data:{first_name:first_name.v,last_name:last_name.v,email:u_mail.v,phone:phone_number.v,country:u_country.v,pass:sign_pass.v, referral: $(".sign_ref").val()},
			 				complete:function(){
			 					$(".sign-loader").hide();
			 				},
			 				success:function(response){
			 					if (response.status == true) {
			 						location.reload();
			 					}else{
			 						notify(response.m,"danger");
			 					}
			 				},
			 				error:function(){
			 					notify("An error occurred, try again","danger");
			 				}
			 			})
			 		}
			 		else{
			 			notify("Kindly agree to terms of service to continue","normal",{closer:false,duration:true});
			 			$(".check_p").css("animation-name","slide_up");
			 			$(".check_p").css("animation-duration",".3s");
			 		}
			 	}else{
			 		sign_pass_confirm.elem.addClass("emt");
			 		notify("Your passwords do not match","normal",{closer:false,duration:true});
			 	}
			 }
		 }else{
		 	notify("Kindly check your details and try again","normal",{closer:false,duration:true});
		 }			 	
	})
	$(".sign_pass_confirm").on("keyup",function(){
		if ($(this).val() == $(".sign_pass").val()) {
			$(this).addClass("okay")				
		}else{
			$(this).removeClass("okay")
		}
	})
	function validateEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }
    function test_length(val){
    	return val.replace(/\s/g, '').length;
    }
    $(".log-in-btn").click(function(){
    	var email = $(".log-mail").val();
    	var pass = $(".log-pass").val();
    	if (email == "") {
    		notify("Enter your email")
    	}else{
    		if (pass == "") {
    			notify("Enter your password")
    		}else{
    			$(".log-test").show();
    			$.ajax({
    				url:base_url+"login",
    				type:"POST",
    				data:{email:email,pass:pass},
    				complete:function(){
    					$(".log-test").hide();
    				},
    				success:function(response){
    					if (response.status == true) {
    						location.reload();
    					}else{
    						notify(response.m,"danger");
    					}
    				},
    				error:function(a){
    					notify("An error occurred, try again");
    				}
    			})
    		}
    	}
    })
    $(".log-mail,.log-pass").on("keypress",function(e){
    	if (e.which == 13) {
	      $(".log-in-btn").click();
	    }
    })
})
$(document).ready(function(){
	alert_count = 0;
	checkers();
	alertcenter();
	$("body").append('<div class="alert-center"></div>');
	c = $(".sign-up-cont")	
	$(".sign-up-btn").on("click",function(){			
		//c.addClass("s-exp");
		//$("html").css("overflow","hidden");
      location.href = `${ base_url }register`;
	})
	$(".close-sign").click(()=>{		
		$("html").css("overflow","auto");
		c.removeClass("s-exp")
	})
	$('.modal').modal(
		{
	      dismissible: true,
	      opacity: .5,
	      inDuration: 300,
	      outDuration: 200,
	      startingTop: '4%',
	      endingTop: '10%',	      
	      ready: function(modal, trigger) { 		        
	      },
	      complete: function() {}
	    }
	);	
})	
alertcenter = function(){	
	notify = function(data = "",type = "normal",options = {closer:true,duration:false}){
		var alert_box = $(".alert-center");
		if (type == "danger") {
			var alert_color = "#ea2c2c";
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
$(document).ready(function(){
	$(".sub-v").click(function(){
		var v_mail = $(".v-mail").val();
		var v_phone = $(".v-phone").val();
		var message = $(".v-message").val();
		var v_name = $(".v-name").val();		
		if (test_length(v_name) > 0 && test_length(message) > 0 && validateInput(v_phone,'phone') && validateInput(v_mail,'email')) {
			//loading("infin",true);
			$.ajax({				
				type:"POST",
				url:base_url+"contact",
				data:{name:v_name,email:v_mail,phone:v_phone,message:message},
				complete:function(){
					//loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						$(".v-mail").val("");
						$(".v-phone").val("");
						$(".v-message").val("");
						$(".v-name").val("")
						alert("Thank you for contacting us, we will get back to you in due course.")
					}else{
						alert(response.m)
					}
				},
				error:function(){
					alert("There is a problem with your internet connection.")
				}
			})
		}else{
			alert("Enter valid values");
		}
	})
	$(".scroll-btn").click(function(){      
		$("html").animate({scrollTop:$("header").height()}, 450);
	})
})
function validateInput(input,type = 'email'){
	if (type === 'phone') {
		var exp = /[+]+[0-9]+[0-9]+[0-9]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;
		var exp1 = /[0]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;

		if (exp.test(input) || exp1.test(input)) {
			return true;
		}else{
			return false;
		}
	} else if(type === 'email'){
		var exp = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	}else{
		return false;
	}

	return exp.test(input);
}
function getPhoneType(phone){
	var exp = /[+]+[0-9]+[0-9]+[0-9]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;
    var exp1 = /[0]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;
	if (exp.test(phone)) {
		return "type_1";
	}else if(exp1.test(phone)){
		return "type_2";
	}else{
		return false;
	}

}
function test_length(val){
	return val.replace(/\s/g, '').length;
}	

$(document).ready(function(){
	function Utils() {

	}
	Utils.prototype = {
	    constructor: Utils,
	    isElementInView: function (element, fullyInView) {
	        var pageTop = $(window).scrollTop();
	        var pageBottom = pageTop + $(window).height();
	        var elementTop = $(element).offset().top;
	        var elementBottom = elementTop + $(element).height();

	        if (fullyInView === true) {
	            return ((pageTop < elementTop) && (pageBottom > elementBottom));
	        } else {
	            return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
	        }
	    }
	};
	var Utils = new Utils();
	$("body").on("scroll",function(){
		if (Utils.isElementInView(".about-cont")) {
			//$(".about-cont").css("animation","slide_up .5s ease");			
		}else{
			//$(".about-cont").css("animation","none");
			//$(".about-cont").next().next().css("animation","none");
		}
		if (Utils.isElementInView(".pricing-cont")) {
			//$(".pricing-cont > *").css("animation","zoomer .5s ease-out");		
		}else{
			//$(".pricing-cont > *").css("animation","none");		
		}
		if (Utils.isElementInView(".footer")) {
			//$(".footer > *").css("animation","zoomer .5s ease-out");		
		}else{
			//$(".footer > *").css("animation","none");		
		}
	})
})
$(document).ready(() => {
	$(".rec-go").click(function (){
		var mail = $(".rec-in").val();
		if (validateInput(mail, "email")) {
         $(".rec-load").show();
			$.ajax({
            type: "POST",
            url: `${base_url}recover_email`,
            data: {email: mail},
            complete: ()=>{
               $(".rec-load").hide();
            },
            success: res => {
               if (res.status) {
                  $(".ch_email").show();
                  setTimeout(()=>{
                     $(".ch_email").hide();

                  }, 10000);
               }else{
                  notify(res.m);
               }
            },
            error: () => {
               notify("An error occurred, kindly check your connection and try again.");
            }
         })
		}else{
			notify("Kindly input a valid email address.")
		}
	})
   $(".p-toggle").click(function(){
      el = $(".log-pass, .sign_pass, .sign_pass_confirm");
      if(el.attr("type") == "password"){
         el.attr("type", "text");
         $(this).html("visibility_off");
      }else{
         el.attr("type", "password");
         $(this).html("visibility")
      }
   })
})