order_manager = function(){
	var unpaid_select = $(".pay-sel-clickable");
	var unpaid_delete = $(".unpaid-del");
	order_loading = function(state = true){
		if (state) {
			$(".ord-loader-in").show();
		}else{
			$(".ord-loader-in").hide();
		}
	}
	internet_error = function(){
		notify("Network error, check your connection and try again.")
	}
	unpaid_select.each(function(){
		$(this).click(function(){
			var item = $(this).parent().parent().attr("data-item");
			var changed = $(this);
			order_loading();
			if (item) {
				if (confirm("Mark item as paid?")) {
					$.ajax({
						url:base_url+"pay_order",
						type:"POST",
						data:{order:item},
						complete:function(){
							order_loading(false);
						},
						success:function(response){
							if (response.status) {
								alert("Done!","normal",{closer:false,duration:false});
								changed.addClass("selected");
								changed.removeClass("pay-sel-clickable");
								changed.parent().parent().removeAttr("data-item");
								changed.parent().parent().children(":last-child").children("button").removeClass("unpaid-del");
								changed.parent().parent().children(":last-child").children("button").attr("disabled",true);
							}else{
								alert(response.m);
							}
						},
						error:function(){
							internet_error();
						}
					})
				}
			}
		})
	})
	unpaid_delete.each(function(){
		$(this).click(function(){
			var item = $(this).parent().parent().attr("data-item");
			var changed = $(this);
			order_loading();
			if (item) {
				if (confirm("Delete order?")) {
					$.ajax({
						url:base_url+"del_order",
						type:"POST",
						data:{order:item},
						complete:function(){
							order_loading(false);
						},
						success:function(response){
							if (response.status) {
								alert("Deleted!","normal",{closer:false,duration:false});
								changed.parent().parent().slideUp("fast");										
								setTimeout(function(){
									changed.parent().parent().remove("data-item");
								},1000)
							}else{
								alert(response.m);
							}
						},
						error:function(){
							internet_error();
						}
					})
				}
			}
		})
	})
	order_filters = function(){
		$(".filter-item").each(function(){
			$(this).on("change",function(){
				filter_orders();
			})
		})
		filter_orders =  function(){
			var filter_fields = {
				category: $(".filter-item:nth-child(1)").val(),
				month: $(".filter-item:nth-child(2)").val(),
				year: $(".filter-item:nth-child(3)").val(),
				staff: $(".filter-item:nth-child(4)").val(),
				order_by: $(".filter-item:nth-child(5)").val(),
				criteria: $(".filter-item:nth-child(6)").val()
			};
			if (filter_fields) {
				order_loading();
				$.ajax({
					url:base_url+"filter_orders",
					type:"POST",
					data:{filter_fields:filter_fields},
					complete:function(){
						order_loading(false);
					},
					success:function(response){						
						if (response.status) {
							$(".order-items-body").html(response.m);
							if (response.total) {
								$("div.order-total").html('<div class="order-total-curr">&gt; Ksh. </div>'+response.total);
							}else{
								$("div.order-total").html('<div class="order-total-curr">&gt; Ksh. </div>0');
							}
							order_manager();
						}else{
							$(".order-total").html('<div class="order-total-curr">&gt; Ksh. </div>0');
							alert(response.m,"error",{closer:false,duration:false});
						}
					},
					error:function(){
						internet_error();
					}
				})
			}
		}
	}
	$(".save-staff").click(function(){
		var staff = $(this).parent().parent().attr("data-item");
		var role = $(".staff-pos").val();
		if (staff) {
			$.ajax({
				url:base_url+"update_staff",
				type:"POST",
				data:{staff:staff,pos:role},
				success:function(response){
					if (response.status) {
						location.reload();
					}else{
						alert(response.m,"error");
					}
				},
				error:function(){
					alert("Internet error, check connection and try again","error");
				}			
			})
		}
	})
	$(".dash-more").click(function(){
		$(".dash-config").toggle();
	})
	$(document).click(function(e){
		if ($(e.target).is(".staff-row") || $(e.target).is(".staff-body")) {
			
		}
	})
	$(".config-save").click(function(){
		var sel = $(".config-account").val();
		if (sel) {
			$.ajax({
				url:base_url+"pos_accounts",
				type:"POST",
				data:{account:sel},
				success:function(response){
					if (response.status) {
						location.reload();
					}else{
						alert(response.m);
					}
				},
				error:function(){
					alert("An error occured, please check your connection and try again.")
				}
			})
		}
	})
}
order_manager();
order_filters();