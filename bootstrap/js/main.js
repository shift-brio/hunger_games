(function($, window, undefined) {
    //is onprogress supported by browser?
    var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());

    //If not supported, do nothing
    if (!hasOnProgress) {
    	return;
    }
    
    //patch ajax settings to call a progress callback
    var oldXHR = $.ajaxSettings.xhr;
    $.ajaxSettings.xhr = function() {
    	var xhr = oldXHR();
    	if(xhr instanceof window.XMLHttpRequest) {
    		xhr.addEventListener('progress', this.progress, false);
    	}

    	if(xhr.upload) {
    		xhr.upload.addEventListener('progress', this.progress, false);
    	}

    	return xhr;
    };
  })(jQuery, window);
/*
progress: function(e) {
    if(e.lengthComputable) {
        var pct = (e.loaded / e.total) * 100 + '%';
        $("#innerprogress").attr('style','width:'+pct+';')
    }
    else {
        $("#mainprogress").addClass('hidden');
    }
},
*/
menu_ = function(){
	menu = $(".sid-nav");
	trigger = $(".hinge");
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
checkers = function(){
	$("[type=checkbox]").each(function(){
		$(this).click(function(){
			val = $(this).attr("value");
			if (val == 'true') {
				$(this).attr("value",false);
				$(this).removeAttr("checked");
			}else{
				$(this).attr("value",true);
				$(this).attr("checked",'');
			}
		})
	})
}
$(document).ready(function(){
	$("body").append('<div class="alert-center"></div>');
	main_response = "";
	
	alert_count = 0;
	base_url = $("base").attr("data")
	/*if (base_url != window.location.href) {
		base_url = window.location.href;
	}*/
	setTimeout(function(){
		$(".m-active").click();
	},1);	
	triggers();
   index();	
   app();		
	cat_type = "";
	$(".filter-list").each(function(){
		$(this).click(function(){
         let month = $(".fl-month").val();
         let year = $(".fl-year").val();
			if ($(".m-active").attr("data-item") == "categories") {
            var curr_item  = $("[data-expand-state=true]").parent().attr("data-item-id");
            get_list_data(curr_item, month, year);
         }else{
            load_item_data($(".m-active").attr("data-item"),month,year);
         }
		})
	})	
	if (isMobile()) {
		// $(".hinge").click();
	}
})
isMobile = function(){
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
		|| /1if207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) 
	{
		return true;
	}
	else{
		return false;
	}	
}
network_error = (t = "") => {
   alert(t == "" ? "Nework error, kindly check your connection and try again": t);
}
triggers = function(){
	alertcenter();		
   drop_down();	
	$('.tooltipped').tooltip({delay: 50,html:true});
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
   
	$(".collapsible").collapsible();
	$('select').material_select();
	$('.datepicker').pickadate({
	    selectMonths: true, // Creates a dropdown to control month
	    selectYears: 15, // Creates a dropdown of 15 years to control year,
	    today: 'Today',
	    clear: 'Clear',
	    close: 'Ok',
	    closeOnSelect: false // Close upon selecting a date,
	  });		
}
drop_down = () => {
      $('.dropdown-button').dropdown({
      inDuration: 350,
      outDuration: 225,
      constrainWidth: false, 
      hover: false, 
      gutter: 0,
      belowOrigin: false,
      alignment: 'left', 
      stopPropagation: false
   })
}
loading = function(loader = ".loader", state = false){
	loader = $(loader)
   if (state == false) {
      loader.hide();
   }
   else{
      loader.show();
   }
}
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
function validateEmail(email) {
   var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   return re.test(email);
}
function validateInput(input,type = 'email'){
   if (type === 'phone') {
      var exp = /[+]+[0-9]+[0-9]+[0-9]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;
      var exp1 = /[0]+[7,2]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;

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
   var exp1 = /[0]+[7,2]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/;
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
index = () =>{
   $(".join.click, .logger").click(function(){
      $("#logger").attr("style","display:flex");
      if($(this).hasClass("log")){         
         s_tab("log")
      }else if($(this).hasClass("sign")){
         s_tab("sign");        
      }       
   })
   $(".cls-log").click(function(){
      $("#logger").hide();
   })
   s_tab = tab =>{
      if (tab == "sign") {              
         $(".split-item:first-child").addClass("active");
         $(".split-item:last-child").removeClass("active");
         $(".logg-in").hide();
         $(".sign-up").show();         
      }else if(tab == "log"){
         $(".split-item:last-child").addClass("active");
         $(".split-item:first-child").removeClass("active");
         $(".logg-in").show();
         $(".sign-up").hide();    
      }
   }
   $(".split-item").each(function(){
      $(this).click(function(){
         if($(this).is(":last-child")){         
            s_tab("log")
         }else if($(this).is(":first-child")){
            s_tab("sign");        
         }
      })
   })
   $(".add-cart").click(function(e){
      if ($(e.target).is(".add-cart")) {
         $(this).hide();
         curr_item = {};
      }
   })
   $(".log-go.sign-go").click(function(){     
      var email = $("#email_sign").val();
      var phone = $("#phone").val();
      var pass = $("#pass_sign").val();
      var name = $("#name").val();

      if (test_length(name) >= 4) {
         if (validateEmail(email)) {
            if (validateInput(phone,"phone")) {
               data = {
                  email: email,
                  phone: phone,
                  pass: pass,
                  name: name
               }
               loading(".log-loader", true);
               $.ajax({
                  url: base_url+"register",
                  type: "POST",
                  data: data,
                  complete: function(){
                     loading(".log-loader", false);
                  },
                  success:function(res){
                     if (res.status) {
                        location.reload();
                     }else{
                        notify(res.m);
                     }
                  },
                  error: () =>{
                    network_error();
                  }
               })
            }else{
               notify("Kendly enter a valid phone number")
            }
         }else{
            notify("Kendly enter a valid email address")
         }
      }else{
         notify("Kendly enter you name")
      }
   })
   $(".log-go.l-go").click(function(){     
      var email = $("#email_log").val();      
      var pass = $("#pass_log").val();

      if (validateEmail(email)) {
         data = {
            email: email,               
            pass: pass
         }
         loading(".log-loader", true);
         $.ajax({
            url: base_url+"login",
            type: "POST",
            data: data,
            complete: function(){
               loading(".log-loader", false);
            },
            success:function(res){
               if (res.status) {
                  location.reload();
               }else{
                  notify(res.m);
               }
            },
            error: () =>{
              network_error();
            }
         })
      }else{
         notify("Kendly enter a valid email address")
      }
   })
}
app = () =>{
   let search_i = `${base_url}media/system/rolling.svg`;
   active_item = {};   
   cart_handler = (callback) =>{
      return {
         items : [],
         is_editing: false,
         listener : callback,
         set item(val){
            var is_set = false;
            if (this.items.length > 0) {
               for (var i = 0; i < this.items.length; i++) {
                  if (this.items[i].id = val.id) {
                     is_set = true;
                  }
               }
            }
            if (is_set) {
               this.update(val.id, val);
            }else{
               this.items[this.items.length] = val;
            }
            this.listener(val);
            this.save();
         },
         save(){            
            cl = [];
            for (var i = 0; i < this.items.length; i++) {
               cl.push({id: this.items[i].id, qty: this.items[i].qty});
            }           
            $.ajax({
               url:`${base_url}update_cart`,
               type:"POST",
               data: {items: cl.length > 0 ? cl : 0, mode: "update"},
               success:() =>{
                  /*something should be here in the future*/
               }, 
               error: () =>{
                  setTimeout(() =>{ this.save()}, 10000);
               }
            })
         },         
         get item(){
            return this.items;
         },
         get length(){
            return this.items.length
         },
         remove_all(){
            this.items = [];
         },
         remove(val, c = ()=>{}){   
            var items = this.items;
            this.remove_all();

            for (var i = 0; i < items.length; i++) {               
               if (!items[i].id == val) {                  
                  this.item = items[i];
               }
            }  
            this.listener();                     
            c();   
            this.save()      
            return true;            
         },
         update(it, v, c = () =>{}){            
            for (var i = 0; i < this.items.length; i++) {
               if (this.items[i].id == it) {                  
                  this.items[i] = v;                                 
               }
            }            
            c();                        
            return true            
         },
         render(it, c = () =>{}){
            return `
               <div class="cart-item">
                  <div class="c-it-name">
                     ${it.name}
                  </div>
                  <div class="c-it-data">
                     <div class="c-it-d">
                        <div class="c-it-dt">
                           Quantity
                        </div>
                        <div class="c-it-dv">
                           ${it.qty}
                        </div>
                     </div>
                     <div class="c-it-d">
                        <div class="c-it-dt">
                           Total cost
                        </div>
                        <div class="c-it-dv amnt">
                            ${new Intl.NumberFormat().format(it.cost * it.qty)}
                        </div>
                     </div>
                  </div>
                  <div class="c-it-tools">
                     <button item="${it.id}" class="c-it-rem click">
                        <i class="material-icons">remove_shopping_cart</i>
                        Remove
                     </button>
                     <button item="${it.id}" class="c-it-edit click">
                        <i class="material-icons">edit</i>
                        Edit quantity
                     </button>
                  </div>
               </div>
            `;
         },
         get empty(){
            return `<div class="empty">
                        <img src="${base_url}media/system/emt.svg" alt="">
                        <div>Items in shopping your cart will be listed here</div>
                     </div>`;
         },
         single(id){
            for (var i = 0; i < this.items.length; i++) {
               if (this.items[i].id == id) {
                  return this.items[i];
               }
            }
         },
         get total(){
            var t = 0;
            if (this.length > 0) {
               this.items.forEach(i => t += Number((i.cost * i.qty)));               
            }
            return number_format(t);
         },
         get render_all(){
            items = this.items;
            var totals = 0;
            var item_count = 0;
            if (items.length > 0) {
               $(".cr-body").html("");         
               for (var i = 0; i < items.length; i++) {            
                  $(".cr-body").append(this.render(items[i]));
                  totals += items[i].cost * items[i].qty;
                  item_count += 1;
               }

            }else{
               $(".cr-body").html(this.empty);
            }
            $(".cr-totals > .right > .count").html(Number(item_count));
            $(".cr-totals > .left > .count").html(`&nbsp;${number_format(totals)}`);
            $(".c-it-rem").each(function(){
               $(this).click(function(){
                  var it = $(this).attr("item");
                  if (it) {
                     if (confirm("Are you sure you want to remove this item from your shopping cart?")) {
                        cart.remove(it);
                        cart.render_all
                     }
                  }
               })
            })
             $(".c-it-edit").each(function(){
               $(this).click(function(){
                  var it = $(this).attr("item");
                  if (it) {
                     $(".add-go").html(`
                           Update quantity
                           <i class="material-icons">done</i>
                        `);
                     cart.is_editing = true;
                     add_cart(it, cart.single(it).qty);
                  }
               })
            })
         }

      }
   }   

   /* initialize cart here */
   cart = cart_handler(val => { 
      items = cart.items.length;      
      $(".cart > .badge").html(items);
      items > 0 ? $(".checkout-btn, .cr-go").removeAttr("disabled"): $(".checkout-btn, .cr-go").attr("disabled","disabled");
      $(".c-tot").html(` ${cart.total}`);
   });

   /* get cart data here */
   (function() {
       $.ajax({
         url: `${base_url}get_cart`,
         type: "POST",
         success: res =>{
            if (res.status) {
               if (res.m.length > 0) {
                  res.m.forEach(item => {                     
                     cart.item = item
                  });
               }
            }
         }
       })
   })();

   $(".user").click(function(){
      if (!$(".user-content").is(":visible")) {
         $(".user-content").attr("style","display:flex");
      }else{
         $(".user-content").hide();
      }
   })
   $(".logout").click(function(){
      location.href = `${base_url}logout`;
   })
   $(".admin-ac").click(function(){
      location.href = `${base_url}admin`;
   })
   $(".user-ac").click(function(){
      location.href = `${base_url}`;
   })
   $(".add-close").click(function(){
      $(".add-cart").hide();
      active_item = {};
   })
   $(".qty-dv > :first-child").click(function(){
      if (Number($("#item-qty").val()) >= 2) {
         $("#item-qty").val(Number($("#item-qty").val()) - 1);
         $(".name-price.main").html(" "+ " " + new Intl.NumberFormat().format(Number($("#item-qty").val()) * active_item.cost));
      }
   })
   $(".qty-dv > :last-child").click(function(){
      $("#item-qty").val(Number($("#item-qty").val()) + 1);
      $(".name-price.main").html(" "+ " " + new Intl.NumberFormat().format(Number($("#item-qty").val()) * active_item.cost));
   })
   $(".v-cart").click(function(){
      $(".shopping-cart").attr("style"," display:flex");
      cart.render_all
   })      
   $(".cr-close").click(function(){
      $(".shopping-cart").hide();
   })
   $(".checkout-btn, .cr-go").click(function(){
      $(".complete").css("display","flex");
   })
   $(".c-close").click(() =>{
      $(".complete").hide();
   })
   $(".add-go").click(function(){
      if (active_item != {}) {
         var item = {
            name: active_item.name,
            cost: active_item.cost,
            qty: $("#item-qty").val() >= 1 ? $("#item-qty").val(): 1,
            id: active_item.id
         };
         cart.item = item;
         if (cart.is_editing) {
            cart.render_all
         }
         cart.is_editing = false;
         $(".add-cart").hide();
         $(this).html(`
            Add item to cart
            <i class="material-icons">done</i>
         `);         
         active_item = {};
      }
   })
   menu = function(){
      $(".cart-add").each(function(){         
         $(this).click(function(){            
            var item = $(this).attr("item");
            add_cart(item, cart.single(item) ?  cart.single(item).qty : 1 );
         })
      })      
   }
   add_cart = (item, qty = 1) => {
      if (item) {
         loading(".loader", true);
         active_item = {};
         $.ajax({
            url: `${base_url}menu_item`,
            type: "POST",
            data: {item: item},
            complete: function(){
               loading(".loader", false);                     
            },
            success: function(res){
               if (res.status) {
                  ({name, desc, image, cost} = res.m);
                  active_item = res.m;
                  $(".add-cart").attr("style","display:flex !important");
                  $(".add-top > img").attr("src",`${base_url}media/menu/${image}`);
                  $(".add-desc > .item-name > .name-text").html(name);
                  $(".add-desc > .item-desc").html(desc);
                  $(".name-price.main").html(" " + new Intl.NumberFormat().format(cost * qty));
                  $("#item-qty").val(qty);
               }else{
                  notify(res.m);
               }
            },
            error:function(){
               network_error();
            }
         })
      }
   }   
   menu();
   number_format = val => new Intl.NumberFormat().format(val);
   render_menu = ({id = null,name = "", desc = "", cost = 0, image = ""}) =>{
      return `
         <div class="col s12 m6 l3 menu-item">
            <div class="item-main">
                <div class="menu-img">
                  <img src="${image}" alt="">
               </div>
               <div class="item-details">
                  <div class="item-name">
                     <div class="name-text">${name}</div>     
                  </div>
                  <div class="item-desc">
                     ${desc}
                  </div>
                  <div class="item-name">                  
                     <div class="name-price">&nbsp;${number_format(cost)}</div>
                  </div>
               </div>
               <div class="item-tools">
                  <button item="${id}" class="cart-add click">
                     Add to cart
                     <i class="material-icons">add_shopping_cart</i>
                  </button>
               </div>
            </div>
         </div>     
      `;
   }
   $(".search-bar").on("keyup", function(){
      var val = $(this).val();
      if (val.length >= 3) {
         $(this).attr("style",`background-image: url(${search_i}) !important;`);

         $.ajax({
            url: `${base_url}search`,
            type:"POST",
            data: {key: val},
            complete: function(){
               $(".search-bar").removeAttr("style");
            },
            success:function(res){
               if (res.status) {
                  var dv = $(".items");
                  dv.html("");

                  if (typeof res.m == 'object') {
                     for (var i = 0; i < res.m.length; i++) {
                        dv.append(render_menu(res.m[i]));
                     }
                     menu();
                  }else{
                     dv.html(res.m);
                  }
               }else{
                  notify(res.m);
               }
            },
            error:function(){
               network_error();
            }
         })
      }else{
         $(this).removeAttr("style");
      }
   })
   $(".search-bar").on("focusout", function(){
      $(this).removeAttr("style");
   })
   $(".c-order").click(function(){
      if (cart.items.length > 0) {
         $(".or-loader").show();
         $.ajax({
            url: `${base_url}place_order`,
            type:"POST",
            data:{cart: cart.item},
            complete: function(){
               $(".or-loader").hide();
            },
            success:function(res){
               if (res.status) {
                  confirm("Your order has been placed successfully");
                  location.reload();
               }else{
                  notify(res.m);
               }
            },
            error:() =>{
               network_error();
            }
         })
      }else{
         notify("You do not have any items in your shopping cart.");
      }
   })
   $(".v-ord").click(function(){
      $(".orders").attr("style","display:flex;");
      $(".user-content").hide();
   })
   $(".ord-cancel").each(function(){
      $(this).click(function(){
         var item = $(this).attr("item");
         if (item != "") {
            if (confirm("Are you sure you want to cancel this order?")) {
               $.ajax({
                  url: `${base_url}cancel_order`,
                  type:"POST",
                  data:{item: item},
                  complete: function(){

                  },
                  success:function(res){
                     if (res.status) {
                        location.reload();
                     }else{
                        notify(res.m);
                     }
                  },
                  error: function(){
                     network_error();
                  }
               })
            }
         }
      })
   })
   $(".cl-ord").click(function(){
      $(".orders").hide();
   })
}