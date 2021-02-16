/**/
$(document).ready(function(){
	//menu_();
	base_url = $("base").attr("data");
	$('[data-item="sign-out-admin"]').click(function(){
		location.href = base_url+"admin/logout";
	})
	part_tools();
	user_tools();
	texter();
	chart_range();	
})
part_tools = function(){	
	$(".item-tool").each(function(){
		$(this).click(function(){
			var partner = $(this).parent().parent().attr("data-partner");
			var action = $(this).attr("data-tool");
			if (action == "notification") {
				$(".utils").show();
				$(".util-btn").attr("data-user",partner);
				$(".notif-util").show();						
			}else if(action == 'info'){
				$(".utils").show();
				$(".info-util").show();

				var action = $(this).attr("data-tool");
				var user = $(this).parent().parent().attr("data-partner");
				loading("infin",true);

				email = $(".det-email").val("")
				phone = $(".det-phone").val("")
				pin   = $(".det-pin").val("");
				date  = $(".det-date").val("")
				more  = $(".referals");

				more.html("<div class='flow-text white-text'>No referals</div>");
				$.ajax({
					url:base_url+"get_partner",
					type:"POST",
					data:{user:user},
					complete:function(){
						loading("infin", false)
					},
					success:function(response){
						if (response.status) {
							email = $(".det-email").val(response.m.email)
							phone = $(".det-phone").val(response.m.phone)
							pin = $(".det-pin").val(response.m.pin);
							date = $(".det-date").val(response.m.joined)
                     $(".referals").html("");                     
							if (response.m.refs.length > 0) {
								for (var i = 0; i < response.m.refs.length; i++) {


									item = response.m.refs[i];
									var x = '<div class="p-item        		user-item" data-user="'+item.id+'">'+
											'<div class="item-text left">'+
											''+item.email+''+
											'</div>'+
											'<div class="item-tools right">'+
											'<button data-tool="user-more" data-tooltip="View data" class="material-icons tooltipped item-tool">keyboard_arrow_down</button>'+		
								'</div>'+
								'<div class="user_more" id="user_more_"'+item.id+'>';
								var next = '<div class="log-group">'+
											'<label  class="log-label">Date Joined: '+
												'<span class="descript">'+
													''+item.joined+''+
												'</span>'+
											'</label>'+
										'</div>';					
								var center = '<div class="log-group">'+
											'<label class="log-label">Subscriptions:'+
											'</label>'+
											'<ul class="desc-li">';
   								if (item.subs.length > 0) {
   									for (var v = 0; v < item.subs.length; v++) {
   										var sub =  item.subs[v];
   										z = '<li>'+
   												''+sub.name+' ~ '+sub.type+'  '+sub.date+
   											'</li>';
   											center += z;
   									}
   								}
   								center += "</ul>"+				
   										"</div>";		
                        var end = '</div></div>';
                        x  = x + next + center + end;
                        $(".referals").append(x);								
   							}                        
								user_tools();
							}else{
								more.html("<div class='flow-text white-text'>No referals</div>");
							}
						}else{
							alert(response.m)
						}
					},
					error:function(){
						alert("Network eror, check connection and try again.");
					}
				})
			}else if(action == 'block'){
				loading("infin",true);
				$.ajax({
					url:base_url+"block_p",
					type:"POST",
					data:{user:partner},
					complete:function(){
						loading("infin",false);
					},
					success:function(response){
						if (response.status) {
							location.reload();
						}else{
							alert(response.m)
						}
					},
					eror:function(){
					    alert("Network eror, check connection and try again.");
					}
				})
			}else if(action == 'approve'){
				loading("infin",true);
				$.ajax({
					url:base_url+"approve",
					type:"POST",
					data:{user:partner},
					complete:function(){
						loading("infin",false);
					},
					success:function(response){
						if(response.status){
							$(".m-active").click()
						}else{
							alert(response.m)
						}
					},
					error:function(){
						alert("Network eror, check connection and try again.");
					}
				})
			}
		})
	})

	$(".util-btn").each(function(){
		$(this).click(function(){
			if ($(this).hasClass("util-negative")) {
				$(this).parent().parent().hide('fast');
				setTimeout(function(){
					$(".utils").hide();
				},250)
			}else{
				var action = $(this).parent().parent().attr("data-tool");
				var user = $(this).attr("data-user");					
				if (action == 'info') {														
					if ($(".partner-data").attr("data-open") == "true") {
						$(".partner-data").attr("data-open",'false')
						$(".referals").attr("data-open",'true')
					}else{
						$(".partner-data").attr("data-open",'true')
						$(".referals").attr("data-open",'false')
					}
				}else if(action == 'block'){
					
				}else if(action == 'notification'){
					var title = $(".not-title").val();
					var body = $(".not-textarea").val();
					if (test_length(title) > 0) {
						if (test_length(body) > 0) {
							loading("infin",true);
							$.ajax({
								url:base_url+"send_notif",
								type:"POST",
								data:{user:user,title:title,message:body},
								complete:function(){
									loading("infin",false)
								},
								success:function(response){
									if (response.status) {
										$(".not-title").val("");
										$(".not-textarea").val("");
										alert("Notification sent.")
									}else{
										alert(response.m);
									}
								},
								error:function(){
									alert("Network eror, check connection and try again.");
								}
							})
						}else{
							alert("Enter a valid message.");
						}
					}else{
						alert("Enter a valid title.");
					}
				}
			}
		})
	})
   $(".add-part").click(function(){      
      loading("infin",true);
      $.ajax({
         url: `${base_url}add_part`,
         type: "POST",
         data: {email: $(".p_email").val()},
         complete: function(){
            loading("infin",false);
         },
         success: res => {
            if (res.status) {
               alert("Account added succesfully.");
               $(".m-active").click();
            }else{
               alert(res.m);
            }
         },
         error: () =>{
            _net_err();
         }
      })
   })
}
user_tools = function(){
	$("[data-tool='user-more']").each(function(){
		$(this).click(function(){
			$(".user-item").each(function(){
				$(this).css("height",'40px')
			});		
			if ($(this).parent().parent().css("height") != "40px") {
				$(this).parent().parent().css("height",'40px');
			}else{
				$(this).parent().parent().css("height",'190px');
			}
			
		})
	})
	$(".user-item").each(function(){
		$(this).click(function(){
			$(".user-item").each(function(){
				$(this).css("height",'40px')
			});		
			if ($(this).css("height") != "40px") {
				$(this).css("height",'40px');
			}else{
				$(this).css("height",'190px');
			}
			
		})
	})
}
home_charts = function(){
	loading('infin',true);
	$.ajax({
		url:base_url+"get_admin_chart",
		type:"POST",
		complete:function(){
			loading("infin",false);
		},
		success:function(response){
			if(response.status){
				var join = [];
				var subs = [];

  				for (var i = 0; i < response.m.join.length; i++) {
					v = [];
					v[0] = String(response.m.join[i].date);  							  						
	  				v[1] = Number(response.m.join[i].count);		  					  				
	  				join[i] = v;
	  			}		  			
	  			create_chart(join,"joining_line_chart",'line');
	  			//subscription data

	  			for (var i = 0; i < response.m.subs.length; i++) {
					v = [];
					v[0] = String(response.m.subs[i].date);  							  						
	  				v[1] = Number(response.m.subs[i].count);		  					  				
	  				subs[i] = v;
	  			}	  		
	  			create_chart(subs,"subs_line_chart",'line');
			}else{
				alert(response,m);
			}
		},
		error:function(){
			alert("Could not load chart data.")
		}
	})
}
texter = function(){
	$(".m-item").each(function(){		
		$(this).click(function(){
			item = $(this)	
			$(".m-item").removeClass("open")										
			if (item.hasClass("open")) {
				item.removeClass("open");					
			}else{											
				item.addClass("open");					
			}
			m = $(this).attr("data-item");
			if (item.hasClass("active")) {						
				$.ajax({
					url:base_url+'read_mesage',
					data:{m:m},
					type:"POST",
					success:function(response){
						if (response.status) {
							item.removeClass("active");
							$(".m-count").html(response.m);
						}
					}
				})
			}
		})
	})
}	
chart_range = function(){
	$(".get_chart_a").click(function(){
		var range = {
			'start_month':$(".month-start").val(),
			'start_year':$(".year-start").val(),
			'end_month':$(".month-end").val(),
			'end_year':$(".year-end").val(),
		}
		if ((range.start_month > range.end_month && range.start_year > range.end_year) || range.start_year > range.end_year || (range.start_month > range.end_month && range.start_year == range.end_year)) {
			alert("Invalid date range")
		}else{				
			get_range_data(range);	  				
			$("#ranger").modal("close");
		}
	})
}
get_range_data = function(range = false){
	if (range) {
		loading("infin",true);
		$.ajax({
			url:base_url+"range_data",
			type:"POST",
			data:{range:range},
			complete:function(){
				loading("infin",false)
			},
			success:function(response){
				if (response.status) {
					var join = [];
					var subs = [];

	  				for (var i = 0; i < response.m.join.length; i++) {
						v = [];
						v[0] = String(response.m.join[i].date);
		  				v[1] = Number(response.m.join[i].count);		  					  				
		  				join[i] = v;
		  			}		  			
		  			create_chart(join,"joining_line_chart",'line');
		  			//subscription data

		  			for (var i = 0; i < response.m.subs.length; i++) {
						v = [];
						v[0] = String(response.m.subs[i].date);  							  						
		  				v[1] = Number(response.m.subs[i].count);		  					  				
		  				subs[i] = v;
		  			}	  		
		  			create_chart(subs,"subs_line_chart",'line');
				}else{
					alert(response.m)
				}
			},
			error:function(){
				alert("Network error try again.")
			}
		})
	}
}
ac_defaults = function(){
	$(".user-search").on("keyup",function(){
		var key = $(this).val();
		if (key.length > 2) {
			$(".search-result").show();
			$(".res-body").html("");
			loading("infin",true);
			$.ajax({
				url:base_url+"search_user",
				type:"POST",
				data:{key:key},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						for (var i = 0; i < response.m.length; i++) {								
							var u = response.m[i];							
							var item = '<div class="p-item p-search" data-user="'+ u.id +'">'
								+'<div class="item-text search-item left">'
								   +u.name
								+'</div></div>';
							$(".res-body").append(item);
							ac_dynamic();
						}
					}
				},
				error:function(){
					alert("Network error, try again.");
				}
			})
		}
	})		
	$(".util-trigger").each(function(){
		$(this).click(function(){
			var util = $(this).attr("data-util");
			$(".ac-utils").show();
         if($(this).hasClass("sys-wide")){
            $(".mes-to").val("all");
         }
			$("."+util).show();
		})
	})
	$(".util-close").each(function(){
		$(this).click(function(){
			$(".ac-utils").hide();
			$(this).parent().parent().hide();
		})
	})
	$(".u-info").each(function(){
		$(this).click(function(){				
			var user = $(this).parent().parent().attr("data-user");
			get_user(user);
		})
	})
	$(".sm-user").each(function(){
		$(this).click(function(){
			$(".ac-utils").show();
			$(".message-util").show();
			var user = $(this).parent().parent().attr("data-user");
			$(".send-mess").attr("data-user",user)
			var name = $(this).parent().parent().children(".item-text").html();
			$(".mes-to").attr("value",name)
			$(".mes-to").attr("user",user);
		})
	})
	$(".bus-in").each(function(){
		$(this).click(function(){	
			var date   = $(".det-create")
			var owner  = $(".det-owner")
			var status = $(".det-status");

			date.val("");
			owner.val("");
			status.val("");

			var bus = $(this).parent().parent().attr("data-user");
			loading("infin",true);
			$.ajax({
				url:base_url+"get_bus",
				type:"POST",
				data:{bus:bus},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						$(".bus-info").show();
						date.val(response.m.date);
						owner.val(response.m.owner);
						status.val(response.m.sub);
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Network error, try again.");
				}
			})
		})
	})
}
ac_dynamic = function(){
	$(".close-pop").each(function(){
		$(this).click(function(){
			$(this).parent().parent().hide();
		})
	})
	$(".p-search").each(function(){
		$(this).click(function(){
			var user = $(this).attr("data-user");
			$(".search-result").hide();
			get_user(user);
		})
	})
	$(".send-mess").click(function(){
		 var type    = $(".mes-type");
		 var to      = $(".mes-to");
		 var title   = $(".mes-title"); 
		 var message = $(".mess-textarea");

		 var kind      = type.val();
		 var user      = to.attr("user");
		 var subject   = title.val(); 
		 var body      = message.val();

		 if (test_length(subject) > 0) {
		 	if (test_length(body) > 2) {
		 		loading("infin",true);
		 		$.ajax({
		 			url:base_url+"send_mess",
		 			type:"POST",
		 			data:{type:kind,to:user,title:subject,message:body},
		 			complete:function(){
		 				loading("infin",false);
		 			},
		 			success:function(response){
		 				if (response.status) {
		 					$(".message-util").hide();
		 					$(".ac-utils").hide();
		 					type.prop("selectedIndex",0);
							to.val("all");
							to.attr("user","all")
							title.val(""); 
							message.val("");
		 					alert("Message sent.");
		 				}else{
		 					alert(response.m);
		 				}
		 			},
		 			error:function(){
		 				alert("Network error, try again.");
		 			}
		 		})
		 	}else{
		 		alert("Enter valid message.");
		 	}
		 }else{
		 	alert("Enter valid title.");
		 }
	})
}
get_user = function(user = false){
	if (user) {
		$(".u-info-util").show();
		$(".ac-utils").show();

		var date  = $(".det-join");
		var email = $(".det-email");
		var name  = $(".u-name");
		var phone = $(".det-phone");			
		var accounts = $(".sub-item-inner");
		var subs  = $(".sub-inner");

		name.html("User details");
		date.val("");
		email.val("");
		phone.val("");			
		accounts.html("");
		subs.html("");

		loading("infin", true);
		$.ajax({
			url:base_url+"get_user",
			type:"POST",
			data:{user:user},
			complete:function(){
				loading("infin", false);
			},
			success:function(response){
				if (response.status) {
					name.html(response.m.name);
					date.val(response.m.date)
					email.val(response.m.email);
					phone.val(response.m.phone);				
					accounts.html(response.m.accounts);
					if (response.m.subs.length > 0) {
						for (var i = 0; i < response.m.subs.length; i++) {
							var sub = response.m.subs[i];
							var span = '<span class="sub-inner-item">'+
									sub.date + ' ' + sub.type + ' ' + sub.ac_type
							    +'</span><br>';
							subs.append(span);
						}
					}

				}else{
					$(".u-info-util").hide();
					$(".ac-utils").hide();
					alert(response.m);
				}
			},
			error:function(){
				$(".u-info-util").hide();
				$(".ac-utils").hide();
				alert("Network error, try again.")
			}
		})
	}
}
withdrawal = function(){
	$(".wit-notify").click(function(){
		$(".utils").show();
		$(".notif-util").show();
		var user = $(this).parent().parent().parent().attr("data-user");
		$(".notif-btn").attr("data-user",user);
	})
	$(".w-item").each(function(){
		$(this).click(function(){
			var item = $(this).attr("data-req");
			$(".withdraw-data").hide();
			loading("infin",true);
			var amnt = $(".with-amnt");
			var date = $(".with-date");
			var method = $(".with-method")
			var mess = $(".withdraw-item");
			var app = $(".app-wit");

			amnt.val("");
			date.val("");
			method.val("");
			mess.attr("data-user","");
			app.attr("data-req","");
			$.ajax({
				url:base_url+"get_with",
				type:"POST",
				data:{item:item},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){			
					if (response.status) {
						$(".withdraw-item").show();
						amnt.val(response.m.amount);
						date.val(response.m.date);
						method.val(response.m.method);
						mess.attr("data-user",response.m.partner);
						app.attr("data-req",item);
					}else{
						alert(response.m)
					}
				},
				error:function(){
					alert("Network error, try again");
				}
			})
		})
	})
	$(".app-wit").click(function(){
		var item = $(this).attr("data-req");
		if (item) {
			loading("infin",true);
			$.ajax({
				url:base_url+"approve_w",
				type:"POST",
				data:{item:item},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){
					if (response.status) {
						alert("success");
						$(".m-active").click();
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Network error, try again");
				}
			})
		}
	})
	$(".cl_ose").each(function(){
		$(this).click(function(){
			$(this).parent().parent().hide();
		})
	})
	$(".withdrawals").click(function(){
		$(".withdraw-data").toggle();
	})
}
let _net_err = () =>{
   alert("Network error, kindly check your connection and try again.");
}