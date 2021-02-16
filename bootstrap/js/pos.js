pos = function(){
	/*lookup*/
	$(".pos-hinge").click(function(){
		$(".pos-menu-items").show();
	})
	var search_in = $(".pos-search");
	var search_go = $(".pos-search-go");
	var search_cont = $(".pos-searcher");
	var search_body = $(".pos-searched");
	var type_search = '<tr class="pos-searched">'+
	'<td colspan="8" class="pos-data pos-none">Type in "search" to add another item.</td>'+
	'</tr>';

	var type_search_err = '<tr class="pos-searched">'+
	'<td colspan="8" class="pos-data pos-none">No items found</td>'+
	'</tr>';
	var cart_main = $(".pos-selected");
	var cart_count = $(".pos-total-count");
	var cart_total_amount  = $(".pos-total-amount");

	var shopping_cart = [];
	search_in.on("click",function(){
		search_cont.show();			
	})
	$(".search-close").click(function(){
		search_cont.hide();
		search_in.val("")
		search_body.html(type_search);
	})
	pos_loading = function(state = false,loaded = ".pos-load"){
		if (state) {
			$(loaded).show();
		}else{
			$(loaded).hide();
		}
	}
	search_in.on("keyup",function(){
		var key = $(this).val();
		if (key.length > 0) {
			search_pos(key);
		}else{
			search_body.html(type_search);
		}
	})
	search_go.on("click",function(){
		var key = search_in.val();
		search_cont.show();
		if (key.length > 0) {				
			search_pos(key);
		}else{
			search_in.click();
			search_in.select();
		}
	})
	search_pos = function(key = false){
		if (key.length > 0) {
			pos_loading(true);
			$.ajax({
				url:base_url+"search_pos",
				type:"POST",
				data:{key:key},
				complete:function(){
					pos_loading(false);
				},
				success:function(response){
					if (response.status) {
						search_body.html(response.m);
						/*trigger searched fns*/
					}else{
						search_body.html(type_search_err)
					}
					search_funcs();
				},
				error:function(){
					search_body.html(type_search_err);
					search_funcs();
				}
			})				
		}
	}
	search_funcs = function(){			
		$(".pos-insert").each(function(){
			$(this).click(function(){
				var i_p = $(this).parent().parent();
				var item = {
					serial: i_p.children(".pos-serial").html(),
					item: i_p.attr("data-item"),
					desc: i_p.children(".pos-name").html(),
					cost: Number(i_p.children(".pos-cost").html()),
					qty: Number(i_p.children(".pos-quantity").html()),
					disc: Number(i_p.children(".pos-discount").html()),						
				};			
				cart_add(item);
				refresh_cart(shopping_cart);
				search_in.click();
				search_in.select();
			})
		})
		$(".pos-quantity.searched-in").each(function(){
			$(this).on("keyup",function(){
				var value = Number($(this).html());
				if (value <= 0) {
					value = 1;
				}
				if (value < 1) {
					value = 1;
				}					
				var cost = Number($(this).parent().children(".pos-cost").html());
				var discount = Number($(this).parent().children(".pos-discount").html());

				if (cost < 1) {
					cost = 1;						
				}
				if (discount < 0) {
					discount = 0;
				}					
				var tot = (cost * value);
				tot = tot - discount;
				$(this).parent().children(".pos-item-tot").html(tot);								
			})
		})
		$(".pos-discount.searched-in").each(function(){
			$(this).on("keyup",function(){
				var discount = Number($(this).html());
				
				if (discount < 0) {						
					discount = 0;
				}					
				var cost = Number($(this).parent().children(".pos-cost").html());
				var qty = Number($(this).parent().children(".pos-quantity").html());

				if (cost < 1) {
					cost = 1;						
				}
				if (qty < 0) {
					qty = 0;
				}					
				var tot = (cost * qty);
				tot = tot - discount;
				$(this).parent().children(".pos-item-tot").html(tot);					
			})
		})
	}
	cart_func = function(){
		$(".pos-rem").each(function(){
			$(this).click(function(){
				var item = $(this).parent().parent().attr("data-item");
				if (item) {
					rem_cart(item);
				}
			})
		})
		$(".pos-quantity:not(.searched-in)").each(function(){
			$(this).on("keyup",function(){
				var value = Number($(this).html());
				if (value <= 0) {
					value = 1;
				}
				if (value < 1) {
					value = 1;
				}					
				var cost = Number($(this).parent().children(".pos-cost").html());
				var discount = Number($(this).parent().children(".pos-discount").html());

				item = $(this).parent().attr("data-item");
				for (var i = 0; i < shopping_cart.length; i++) {
					if (shopping_cart[i].item = item) {
						shopping_cart[i].qty = value;
					}
				}
				if (cost < 1) {
					cost = 1;						
				}
				if (discount < 0) {
					discount = 0;
				}					
				var tot = (cost * value);
				tot = tot - discount;
				$(this).parent().children(".pos-item-tot").html(tot);						
				update_item(item);			
				refresh_cart([],true);							
			})
		})
		$(".pos-discount:not(.searched-in)").each(function(){
			$(this).on("keyup",function(){
				var discount = Number($(this).html());
				
				if (discount < 0) {						
					discount = 0;
				}					
				var cost = Number($(this).parent().children(".pos-cost").html());
				var qty = Number($(this).parent().children(".pos-quantity").html());

				if (cost < 1) {
					cost = 1;						
				}
				if (qty < 0) {
					qty = 0;
				}					
				var tot = (cost * qty);
				tot = tot - discount;
				$(this).parent().children(".pos-item-tot").html(tot);

				item = $(this).parent().attr("data-item");
				for (var i = 0; i < shopping_cart.length; i++) {
					if (shopping_cart[i].item = item) {
						shopping_cart[i].disc = discount;
					}
				}	
				update_item(item);
				refresh_cart([],true);		
			})
		})
	}
	$(".confirm-back").click(function(e){
		if ($(e.target).is(".confirm-back")) {
			$(".confirm-back").hide();
		}
	})
	update_item = function(item = false){
		var parent = $(".pos-selected-item[data-item='"+item+"']");
		var qty = Number(parent.children(".pos-quantity").html());
		var disc = Number(parent.children(".pos-discount").html());
		var cost = Number(parent.children(".pos-cost").html());

		if (qty < 1) {
			qty = 1;
		}
		var tot = ((qty * cost) - disc);

		parent.children(".pos-item-tot").html(tot);
	}
	rem_cart = function(item = false){
		if (item && confirm("Remove item for cart?")) {
			var updating_list = [];
			for (var i = 0; i < shopping_cart.length; i++) {
				if (shopping_cart[i].item != item) {
					updating_list.push(shopping_cart[i]);
				}
			}
			shopping_cart = updating_list;
			refresh_cart(shopping_cart);
		}
	}
	cart_add = function(item = ""){
		if (item && item.length != "") {			
			if (item.qty > 0) {
				if (item.disc < 0) {
					item.disc = 0;
				}
				
				if (shopping_cart.length > 0) {
					ch_cart = false;
					for (var i = 0; i < shopping_cart.length; i++) {
						if (shopping_cart[i].item == item.item) {
							ch_cart = true;
							adding = Number(shopping_cart[i].qty) + Number(item.qty);
							shopping_cart[i].qty = adding;

							adding = Number(shopping_cart[i].disc) + Number(item.disc);
							shopping_cart[i].disc = adding;
							shown = 	shopping_cart[i].qty;																						
						}													
					}
					if (!ch_cart) {
						shown = item.qty;								
						shopping_cart.push(item);
					}
				}else{						
					shopping_cart.push(item);
					shown = item.qty;
				}	
				$(".added-count").html(shown);						
				$(".added-count").show();
				setTimeout(function(){
					$(".added-count").fadeOut("fast");
				},1000);								
				refresh_cart(shopping_cart);
				$(".pos-searched").html('<tr class="pos-searched">'+
					'<td colspan="8" style="color:#79D20C;" class="pos-data pos-none">Item added.</td>'+
					'</tr>');
				setTimeout(function(){
					$(".pos-searched").html('<tr class="pos-searched">'+
						'<td colspan="8" class="pos-data pos-none">Type in "search" to add another item.</td>'+
						'</tr>');
				},2000)
				search_in.click();
				search_in.select();				
			}else{
				alert("Enter quantity.");
			}
		}else{
			console.log("No items");
		}
	}
	refresh_cart = function(items = false,totals = false){			
		if (items == false) {
			items = shopping_cart;
		}
		cart_total = 0;
		cart_discs = 0;
		cart_html = "";
		con = false;				
		for (var i = 0; i < items.length; i++) {
			if (items[i].qty <= 0) {
				items[i].qty = 1;
			}
			if (items[i].disc < 0) {
				items[i].qty = 0;
			}
			cart_discs += items[i].disc;
			item_total = ((Number(items[i].qty) * Number(items[i].cost)) - Number(items[i].disc));
			cart_total += item_total;
			if (!totals) {
				post = i +1;
				cart_html += '<tr class="pos-selected-item pos-added" data-item="'+items[i].item+'">'+
				'<td class="pos-data pos-count">'+post+'</td>'+
				'<td class="pos-data pos-serial">'+items[i].serial+'</td>'+
				'<td class="pos-data pos-name">'+items[i].desc+'</td>'+
				'<td class="pos-data pos-cost inv-cash">'+items[i].cost+'</td>'+
				'<td class="pos-data pos-quantity pos-in" '+'contenteditable="true">'+items[i].qty+'</td>'+
				'<td class="pos-data pos-discount pos-in" '+'contenteditable="true">'+items[i].disc+'</td>'+
				'<td class="pos-data pos-item-tot inv-cash">'+item_total+'</td>'+
				'<td class="pos-data pos-m">'+
				'<button data-position="left" data-tooltip="Remove item from cart" class=" tooltipped material-icons tooltipped pos-rem click-btn">close</button>'+
				'</td>'+
				'</tr>';
			}

		}
		if (!totals) {
			if (cart_html != "") {
				cart_main.html(cart_html);
				$(".pos-action").show();									
			}else{
				cart_main.html('<tr class="pos-selected-item pos-added">'+
					'<td colspan="8" class="pos-data pos-none">No items in cart.</td>	'+				
					'</tr>');
			}
		}
		
		if (shopping_cart.length == 1) {
			cart_count.html(shopping_cart.length+" item");
		}else{
			cart_count.html(shopping_cart.length+" items");
		}
		$(".checkout-tot-head").html(cart_total.toFixed(2));
		cart_total_amount.html(cart_total.toFixed(2));
		if (!totals) {
			cart_func();
		}
		$('.tooltipped').tooltip({delay: 50,html:true});
	}
	$(".pos-cancel").click(function(){
		if (shopping_cart.length > 0) {
			if (shopping_cart.length > 0) {
				$(".confirm-back").show();
			}else{
				shopping_cart = [];
				refresh_cart(shopping_cart);
				$(".confirm-back").hide();
				del_pending();
				$(".pos-search-go").click();
				shopping_cart = [];
				refresh_cart(shopping_cart);
				del_pending();
				con = false;
				if ($(".pos").attr("data-editing") == true || $(".pos").attr("data-editing") == "true") {
					con = confirm("Delete pending order?");
					if (con) {
						del_pending();
					}
				}
			}					
		}
	})
	$(".pos-new").click(function(){
		if (shopping_cart.length > 0) {
			$(".confirm-back").show();
		}else{
			$(".pos-search-go").click();
		}
	})
	$(".cart-add").click(function(){
		$(".pos-searcher").show();
		search_in.click();
		search_in.select();		
	})
	$(".confirm-delete").click(function(){
		shopping_cart = [];
		refresh_cart(shopping_cart);
		$(".confirm-back").hide();
		del_pending();
	})
	del_pending = function(){
		if ($(".pos").attr("data-editing") == true || $(".pos").attr("data-editing") == "true") {
			var removed = $(".pos").attr("data-edited");
			$.ajax({
				url:base_url+"rem_pending",
				type:"POST",
				data:{edited:removed},
				success:function(response){
					$(".pending-body").html("");
					$p_items = '';
					if (response.pending && response.pending.length > 0) {							
						for (var i = 0; i < response.pending.length; i++) {
							response.pending[i]
							$p_items += '<div  class="pending-item" data-item="'+response.pending[i].id+'">'+
							'<div class="pending-details">'+response.pending[i].desc+'</div>'+
							'<div class="right pending-time">'+
							response.pending[i].date+
							'</div>'+
							'</div>';
						}
					}else{
						$p_items += '<div  class="pending-item">'+
						'<div class="pending-details">No pending items</div>'+
						'</div>';
					}
					$(".pending-body").html($p_items);						
					pending_func();
				}
			})
		}
	}
	$(".pos-complete").click(function(){
		if (shopping_cart.length > 0) {
			$(".checkout-row").show();
		}
	})
	$(".checkout-row").click(function(e){
		if ($(e.target).is(".checkout-row")) {
			$(".checkout-row").hide();
		}
	})

	$(".checkout-pay").on("change",function(){
		var it = $(this).val();
		if (it == "paid") {
			$(".pay-details").removeAttr("disabled");
			$(".pay-details > div > select").removeAttr("disabled");
		}else{
			$(".pay-details").attr("disabled","disabled");
			$(".pay-details > div > select").attr("disabled","disabled");
		}
	})
	$(".check-out-disc").on("keyup",function(){
		var disc = Number($(this).val());
		if (disc < 0) {
			disc = 0;
		}
		var s_tot = 0;
		for (var i = 0; i < shopping_cart.length; i++) {
			s_tot += ((shopping_cart[i].qty * shopping_cart[i].cost) - shopping_cart[i].disc);
		}
		$(".checkout-tot-head").html((s_tot - disc).toFixed(2));
	})
	$(".checkout-go").click(function(){
		var cart = shopping_cart;
		if (cart.length > 0) {
			var items = [];
			for (var i = 0; i < cart.length; i++) {
				item = cart[i];
				var v = {
					item: item.item,
					qty: item.qty,
					disc: item.disc,
					serial: item.serial
				};
				items.push(v);
			}
			var gen = {
				pay_method: $(".checkout-method").val(),
				pay_status: $(".checkout-pay").val(),
				disc: $(".check-out-disc").val()
			};
			var data  = {
				gen: gen,
				cart:items,
			}
			complete_order(data);
		}else{
			//alert("No items in shopping cart.")
		}
	})
	clear_cart = function(){
		shopping_cart = [];
		refresh_cart(shopping_cart);
	}
	$(".confirm-save").click(function(){
		var cart = shopping_cart;
		if (cart.length > 0) {
			var items = [];
			for (var i = 0; i < cart.length; i++) {
				item = cart[i];
				var v = {
					item: item.item,
					qty: item.qty,
					disc: item.disc,
					serial: item.serial
				};
				items.push(v);
			}
			var gen = {
				pay_method: $(".checkout-method").val(),
				pay_status: $(".checkout-pay").val(),
				disc: $(".check-out-disc").val()
			};
			var data  = {
				gen: gen,
				cart:items,
			}
			complete_order(data,true);
		}else{
			alert("No items in shopping cart.")
		}
	})
	complete_order = function(data = false,save = false){
		var editing = false;
		if ($(".pos").attr("data-editing") == "true" || $(".pos").attr("data-editing") == true) {
			editing = true;
			var edited = $(".pos").attr("data-edited");
		}else{			
			var edited = false;
		}
		if (data) {
			if (save) {
				data = {
					order: data,
					editing: editing,
					edited:edited,
					save:true				
				};
			}else{
				data = {
					order: data,
					editing: editing,
					edited:edited,				
				};
			}
			loading(true,".checkout-load-in");
			$(".checkout-go").attr("disabled","true");
			$.ajax({
				url:base_url+"new_order",
				type:"POST",
				data:data,
				complete:function(){
					loading(false,".checkout-load-in");
				},
				success:function(response){
					if (response.status) {
						clear_cart();
						p_items = "";
						$(".checkout-row").hide();
						$(".check-out-disc").val(0);
						$(".confirm-back").hide();
						if (save) {
							notify("Order saved");								
						}
						$(".checkout-go").removeAttr("disabled");
						if (response.pending && response.pending.length > 0) {
							p_items = '';
							for (var i = 0; i < response.pending.length; i++) {
								response.pending[i]
								p_items += '<div  class="pending-item" data-item="'+response.pending[i].id+'">'+
								'<div class="pending-details">'+response.pending[i].desc+'</div>'+
								'<div class="right pending-time">'+
								response.pending[i].date+
								'</div>'+
								'</div>';
							}
						}else{
							p_items += '<div  class="pending-item">'+
							'<div class="pending-details">No pending items</div>'+
							'</div>';
						}
						$(".pending-body").html(p_items);
						$(".pos-pending").css("animation","zoomer .15s ease-out");
						pending_func();
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
	pending_func = function(){
		$(".pending-item").each(function(){
			$(this).click(function(){
				var p = $(this).attr("data-item");
				if (p) {
					pos_loading(true);
					$.ajax({
						url:base_url+"get_pending",
						type:"POST",
						data:{item:p},
						complete:function(){
							pos_loading(false);
						},
						success:function(response){
							if (response.status) {									
								if (response.m.length > 0) {
									var ord_id = p
									shopping_cart = [];
									refresh_cart(shopping_cart);
									for (var i = 0; i < response.m.length; i++) {	
										var added =  response.m[i];									
										var item = {
											serial: added.serial,
											item: added.item,
											desc: added.desc,
											cost: added.cost,
											qty: added.qty,
											disc: added.disc,						
										};
										shopping_cart.push(item);																						
									}		
									refresh_cart(shopping_cart);
									$(".pos-pending-items").slideUp("fast");
									$(".pos").attr("data-editing",true);
									$(".pos").attr("data-edited",ord_id);								
								}									
							}else{
								alert(response.m);
							}
						},
						error:function(){
							alert("Internet error, check your connection and try again.");
						}
					})
				}
			})
		})
	}		
	pending_func();
	$(document).click(function(e){
		var isPending = $(e.target).is(".pos-shop");
		if (isPending) {
			$(".pos-pending-items").slideUp("fast");
			$(".pos-menu-items").hide();
		}
	})
	$(".pos-pending").click(function(){
		$(".pos-pending-items").toggle();
	})
	$(".confirm-action").click(function(){
		$(".pos-search-go").click();
	})
	window.onbeforeunload = function(e){
	  if (shopping_cart.length > 0) {
	  	return 'Cancel current order?';
	  }
	};
	ctrl_pressed = false;
	$(".pos-more").click(function(){
		$(".search-close").click();
	})	
}
$(document).on("keydown",function(e){	
	if (e.which == 27) {
		$(".search-close").click();
	}	 
  if (e.which == 17 || e.which == 91) {
  	ctr_pressed = true;
  }     
})
pos();