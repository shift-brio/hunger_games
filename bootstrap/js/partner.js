menu_ = function(){
	menu = $(".sid-nav");
	trigger = $(".hinge")
	cont = $(".main-cont");
	trigger.click(function(){
		state = menu.attr("data-state");
		if (state == "folded") {
			menu.attr("data-state","expanded")
			trigger.html("close")
			cont.attr("data-margin","true")
		}else{
			menu.attr("data-state","folded")
			cont.attr("data-margin","false")
			trigger.html("menu")				
		}
	})
}
$(document).ready(function(){
	menu_();	
	base_url = $("base").attr("data");
	$('[data-item="sign-out-partner"]').click(function(){
		location.href = base_url+"partner/logout";		
	})	
})
part_ner = function(){
	$(".p-info").each(function(){
		$(this).click(function(){
			 var user = $(this).parent().parent().attr("data-user");			 
			 loading("infin",true);
			 var email = $(".p-mail")
			 var date  = $(".p-date")
			 var sub   = $(".p-subs")

			 email.val("");
			 date.val("");
			 sub.val("");
			 $.ajax({
			 	url:base_url+"get_refered",
			 	type:"POST",
			 	data:{ref:user},
			 	complete:function(){
			 		loading("infin",false);
			 	},
			 	success:function(response){
			 		if (response.status) {
			 			 $(".ref-details").show();
			 			 email.val(response.m.email);
						 date.val(response.m.date);
						 sub.val(response.m.sub);
			 		}else{
			 			alert(response.m);
			 		}
			 	},
			 	error:function(){
			 		alert("Internet error, try again.")
			 	}
			 })
		})		
	})
	$(".close-pop").each(function(){
		$(this).click(function(){
			$(this).parent().parent().hide();
		})
	})
	$(".p-wid").click(function(){
		$(".with-draw").show();
	})
	$(".p-ref").click(function(){
		$(".add-refer").show();
	})
	$(".s-ref").click(function(){
		$(".add-sales").show();
	})
   $(".ref-copy").click(function(){      
      copy($(".ref-link"),"Referral link copied to clipboard");
   })
	$(".add-ref").click(function(){
		var id = $(".det-ac").val();
		if (test_length(id) >= 4) {
			loading("infin",true);
			$.ajax({
				url:base_url+"add_ref",
				type:"POST",
				data:{id:id},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){
					alert(response.m);
					if (response.status) {
						location.reload();
					}					
				},
				error:function(){
					alert("Internet error, try again.");
				}
			})
		}else{
			alert("Enter a valid account number.")
		}
	})
	$(".add-agent").click(function(){
		var email = $(".agent-email").val();
		var commission = $(".agent-comm").val();		
		if (email && commission >= 0 && commission <= 100) {
			loading("infin",true);
			$.ajax({
				url:base_url+"add_rep",
				type:"POST",
				data:{email:email, commission:commission},
				complete:function(){
					loading("infin",false);
				},
				success:function(response){
					$(".agent-email").val("");
					alert(response.m);						
				},
				error:function(){
					alert("Internet error, try again.");
				}
			})
		}else{
			alert("Enter a valid account number.")
		}
	})
	$(".with-btn").click(function(){
		var method = $(".with-method").val();
		var amount = $(".with-amnt").val();
		if (method == 'mpesa') {
			if (amount >= 100) {
					loading("infin", true);
					$.ajax({
						url:base_url+"withdraw",
						type:"POST",
						data:{method:method,amount:amount},
						complete:function(){
							loading("infin", false);
						},
						success:function(response){
							if (response.status) {
								alert(response.m);
								setTimeout(function(){
									$(".m-active").click();
								},2000)
							}else{
								alert(response.m);
							}
						},
						error:function(){
							alert("Internet error, try again.")
						}
					})
			}else{
				alert("You cannot withdraw less than 100.")
			}
		}else{
			alert("Select a valid method.")
		}
	})
	get_earns = function(){
		loading("infin",true);
		div = "earns_line_chart";		
		$.ajax({
			url:base_url+"get_earnings",
			type:"POST",
			data:{div:div},
			complete:function(){
				loading("infin",false);							
			},
			success:function(response){				
				if (response.status) {
					  var earns = [];

	  				for (var i = 0; i < response.m.length; i++) {
							v = [];
							v[0] = String(response.m[i].date);  							  						
		  				v[1] = Number(response.m[i].amount);		  					  				
		  				earns[i] = v;
		  			}		  			
					create_chart(earns,div,'line');
				}
			},			
			error:function(r){				
				console.log("Could not get chart data")
			}
		})
	}
	get_earns();
	$(".cl_ose").each(function(){
		$(this).click(function(){
			$(this).parent().parent().hide();
		})
	})
	$(".wit-req").click(function(){
		$(".withdraw-data").toggle();
	})
	$(".partner_reps").click(function(){
		$(".my-reps").slideDown("fast");
	})	
	$(".reps-close").click(function(){
		$(".my-reps").slideUp("fast");
	})
	$(".reps-date,.sel-cancel").click(function(){
		$(".date-sel").toggle();
	})
	rep_funcs = function(){
		$(".save-comm").each(function(){
			$(this).click(function(){
				var comm = $(this).parent().children(".sales-rep-comm").html();
				var rep = $(this).parent().parent().attr("data-item");
				if (comm != "" && rep && confirm("Change comissing?")) {
					$.ajax({
						url:base_url+"update_comm",
						type:"POST",
						data:{comm:comm,rep:rep},
						success:function(response){
							if (response.status) {
								alert("Commission saved");
							}else{
								alert(response.m);
							}
						},
						error:function(){
							alert("Internet error, check your connection and try again.")
						}
					})
				}
			})
		})
		$(".block-rep").each(function(){
			$(this).click(function(){				
				var rep = $(this).parent().parent().attr("data-item");
				btn = $(this);
				if (btn.html() == "undo") {
					txt = "Block";					
				}else{					
					txt = "Unblock";
				}							
				if (rep && confirm(txt+" Sales rep?")) {
					$.ajax({
						url:base_url+"block_rep",
						type:"POST",
						data:{rep:rep},
						success:function(response){
							alert(response.m);
							if (response.status) {
								if (btn.html() == "undo") {
									btn.html("block");
									btn.arrt("data-tooltip","Block rep");
								}else{
									btn.html("undo");
									btn.arrt("data-tooltip","Unblock rep");
								}
								$(".tooltipped").tooltip();
							}
						},
						error:function(){
							alert("Internet error, check your connection and try again.")
						}
					})
				}
			})
		})
	}
	rep_funcs();
	partner_func();
	$(".sel-go").click(function(){
		var start_date = $(".sel-start");
		var end_date = $(".sel-end");
		if (!start_date) {
			start_date = false;
		}
		if (!end_date) {
			end = false;
		}
		$(".sel-go").click(function(){
			var start_date = $(".sel-start").val();
			var end_date = $(".sel-end").val();
			if (!start_date) {
				start_date = false;
			}
			if (!end_date) {
				end = false;
			}
			var d = {start_date:start_date,end_date:end_date};
			$.ajax({
				url:base_url+"get_reps",
				type:"POST",
				data:d,
				success:function(response){
					if (response.status) {
						$(".date-sel").hide();
						$(".rep-list").html(response.m);
						$(".reps-title").html(response.text);
						rep_funcs();
						partner_func();
						$(".tooltipped").tooltip();
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Internet error, check your connection and try again.")
				}
			})
		})		
	})
	$(".earn-date").click(function(){
		$(".in-date").toggle();
	})
	$(document).click(function(e){
		var is_sel = $(e.target).is(".in-date-btn") || $(e.target).is(".in-date-in") || $(e.target).is(".earn-date");
		if (!is_sel) {
			$(".in-date").slideUp("fast");
		}
	})
	$(".in-date-btn").click(function(){
		var month = $(".in-date-month").val();
		var year = $(".in-date-year").val();
		if (month && year) {
			$.ajax({
				url:base_url+"get_earned",
				type:"POST",
				data:{month:month,year:year},
				success:function(response){
					if (response.status) {
						$(".earn-amnt").html(response.m);
						$(".in-date").slideUp("fast");
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("Internet error, check your connection and try again.")
				}
			})
		}
	})
	$(".close-agent").click(function(){
		$(".rep-chart").slideUp("fast");
	})	
}
partner_func = function(){
	$(".view-rep").each(function(){
		$(this).click(function(){
			var partner = $(this).parent().parent().attr("data-item");
			if (partner) {
				$.ajax({
					url:base_url+"get_clients",
					type:"POST",
					data:{partner:partner},
					success:function(response){
						if (response.status) {								
							$(".rep-chart-body").html(response.m);
							$(".agent-name").html(response.text);	
							$(".rep-chart").slideDown("fast");							
						}else{
							alert(response.m);
						}
					},
					error:function(){
						alert("Internet error, check your connection and try again.")
					}
				})
			}
		})
	})
}
ref_pt = function(){
	$(".ref-p").click(function(){
		var email = $(".p-email").val();
		var id = $(".p-id").val();
		var phone = $(".p-phone").val();
		var kra  = $(".p-pin").val();

		if (validateInput(email,"email")) {
			if (id.length > 2) {
				if (validateInput(phone,"phone")) {
					if (kra != "") {
						loading(true);
						$.ajax({
							url:base_url+"refer_partner",
							type:"POST",
							data:{email:email,id:id,phone:phone,kra:kra},
							complete:function(){
								loading(false);
							},
							success:function(response = {status:false}){
								if (response.status) {
									notify("Referal succesull");
									$(".p-email").val("");
									$(".p-id").val("");
									$(".p-phone").val("");
									$(".p-pin").val("");
								}else{
									notify(response.m);
								}
							},
							error:function(){
								notify("An unknown error occurred, try again.")
							}
						})
					}else{
						notify("Enter a valid phone number.");
					}
				}else{
					notify("Enter a valid phone number.");
				}
			}else{
				notify("Enter a valid id Number.");
			}
		}else{
			notify("Enter a valid email.");
		}
	})
}
function copy(el = $("body"), m = "Text copied to clipboard") {  
  el.select();
  //el.setSelectionRange(0, 99999); 
  
  document.execCommand("copy");
  
  alert(m);
}