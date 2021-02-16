<div class="app">
   <nav class="app-nav">
      <div class="nav-content">
        <div class="n-search">
            <input placeholder="Find dishes" type="text" class=" browser-default search-bar">
        </div>
         <div class="cart">
            <span class="badge">
               <?php echo isset($_SESSION["cart"]) && is_array($_SESSION["cart"]) ? sizeof($_SESSION["cart"]): 0; ?>
            </span>
            <button data-position="left" data-tooltip="View your shopping cart" class="material-icons click v-cart tooltipped">shopping_cart</button>
            <img data-position="left" data-tooltip="Account options" class=" tooltipped user" src="<?php echo base_url("media/system/profile.svg") ?>" alt="User">
            <?php $this->load->view("components/user_menu"); ?>
         </div>
      </div>
   </nav>
   <div class="items row">
      <div class="col s12 m6 l3 menu-item">
         <div class="item-main">
             <div class="menu-img">
               <img src="<?php echo base_url("media/system/food1.jpg"); ?>" alt="">
            </div>
            <div class="item-details">
               <div class="item-name">
                  <div class="name-text">This food item has a name</div>                 
               </div>
               <div class="item-desc">
                  Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
               </div>
               <div class="item-name">                  
                  <div class="name-price">&nbsp;3,000.00</div>
               </div>
            </div>
            <div class="item-tools">
               <button item="1" class="cart-add click">
                  Add to cart
                  <i class="material-icons">add_shopping_cart</i>
               </button>
            </div>
         </div>
      </div>     
      <div class="col s12 m6 l3 menu-item">
         <div class="item-main">
             <div class="menu-img">
               <img src="<?php echo base_url("media/system/food2.jpeg"); ?>" alt="">
            </div>
            <div class="item-details">
               <div class="item-name">
                  <div class="name-text">This food item</div>     
               </div>
               <div class="item-desc">
                  Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
               </div>
               <div class="item-name">                  
                  <div class="name-price">&nbsp;1,500.00</div>
               </div>
            </div>
            <div class="item-tools">
               <button class="cart-add click">
                  Add to cart
                  <i class="material-icons">add_shopping_cart</i>
               </button>
            </div>
         </div>
      </div>     
       <div class="col s12 m6 l3 menu-item">
         <div class="item-main">
             <div class="menu-img">
               <img src="<?php echo base_url("media/system/food3.jpg"); ?>" alt="">
            </div>
            <div class="item-details">
               <div class="item-name">
                  <div class="name-text">This food item</div>    
               </div>
               <div class="item-desc">
                  Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
               </div>
               <div class="item-name">                  
                  <div class="name-price">&nbsp;500.00</div>
               </div>
            </div>
            <div class="item-tools">
               <button class="cart-add click">
                  Add to cart
                  <i class="material-icons">add_shopping_cart</i>
               </button>
            </div>
         </div>
      </div>     
       <div class="col s12 m6 l3 menu-item">
         <div class="item-main">
             <div class="menu-img">
               <img src="<?php echo base_url("media/system/back.jpg"); ?>" alt="">
            </div>
            <div class="item-details">
               <div class="item-name">
                  <div class="name-text">This food item</div>    
               </div>
               <div class="item-desc">
                  Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
               </div>
               <div class="item-name">                  
                  <div class="name-price">&nbsp;2,500.00</div>
               </div>
            </div>
            <div class="item-tools">
               <button class="cart-add click">
                  Add to cart
                  <i class="material-icons">add_shopping_cart</i>
               </button>
            </div>
         </div>
      </div>      
       <div class="col s12 m6 l3 menu-item">
         <div class="item-main">
             <div class="menu-img">
               <img src="<?php echo base_url("media/system/back.jpg"); ?>" alt="">
            </div>
            <div class="item-details">
               <div class="item-name">
                  <div class="name-text">This food item</div>
                  <div class="name-price">&nbsp;200</div>
               </div>
               <div class="item-desc">
                  Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
               </div>
            </div>
            <div class="item-tools">
               <button class="cart-add click">
                  Add to cart
                  <i class="material-icons">add_shopping_cart</i>
               </button>
            </div>
         </div>
      </div>              
   </div>
   <div class="bottom">      
      <img class="loader" src="<?php echo base_url("media/system/rolling.svg"); ?>" alt="">
      <button disabled="true" class="checkout-btn">
         Checkout now
         <i class="material-icons">shopping_cart</i>
      </button>
   </div>
</div>
<div class="app-modal">
   <div class="modal-content">
      <div class="modal-body">
         <div class="modal-title">
            This is a title
         </div>
      </div>
      <div class="modal-tools">
         <button class="material-icons click">
            close
         </button>
         <button class="modal-right click">
            Add to cart
         </button>
      </div>
   </div>
</div>
<div class="shopping-cart">
   <div class="cart-content">
      <div class="cr-title">
         <div class="title-text">
            Your shopping cart
         </div>
      </div>
      <div class="cr-body">
         <?php $this->load->view("components/empty_cart") ?>
      </div>
      <div class="cr-tools">
        <div class="cr-totals">
           <div class="right">
              <div class="text">
                 Items
              </div>
              <div class="count">
                 0
              </div>
           </div>
           <div class="left">
              <div class="text">
                 Cost
              </div>
              <div class="count">
                 &nbsp;00.00
              </div>
           </div>
        </div>
        <div class="cr-btns">
            <button class="cr-tool cr-close click">
               <i class="material-icons">close</i>
            </button>
            <img class="cr-loader" src="<?php echo base_url("media/system/rolling.svg"); ?>" alt="">
            <button disabled class="cr-tool cr-go click">
               Complete order
               <i class="material-icons">arrow_forward</i>
            </button>
        </div>
      </div>
   </div>
</div>
<div class="add-cart">
   <div class="add-content">
      <div class="add-top">
         <div class="ad-close">
            <button class="add-close click material-icons">
               close
            </button>
         </div>
         <img src="<?php echo base_url("media/system/food1.jpg"); ?>" alt="">
      </div>
      <div class="add-desc">
         <div class="item-name">
            <div class="name-text">This food item</div>    
         </div>
         <div class="item-desc">
            Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Repellendus beatae fugit consequuntur quam amet
         </div>
         <div class="in-group it-qt">
             <label class="log-label">Quantity</label>
             <div class="qty-dv">
                <button class="material-icons click">remove</button>
                <input type="number" value="1" id="item-qty" placeholder="Quantity" class="log-in browser-default">
                <button class="material-icons click">add</button>
             </div>
         </div>
         <div class="item-name">                  
            <div class="name-price main">&nbsp;500.00</div>
         </div>
         <div class="add-go-dv">
            <img class="add-loader" src="<?php echo base_url("media/system/rolling.svg"); ?>" alt="">
            <button class="add-go click">
               Add item to cart
               <i class="material-icons">done</i>
            </button>
         </div>         
      </div>
   </div>
</div>
<div class="complete">
   <div class="c-content">
      <div class="c-title">
         Complete order
      </div>      
      <div class="c-body">
         <div class="c-text">
            Total order amount
         </div>
         <div class="c-tot">
            0.00
         </div>
      </div>
      <div class="c-bottom">
         <button class="c-close material-icons click">close</button>        
         <button class="c-order click">
            COMPLETE ORDER
            <i class="material-icons">done</i>
         </button>
         <img class="or-loader" src="<?php echo base_url("media/system/rolling.svg"); ?>" alt="">
      </div>
   </div>
</div>
<div class="orders">
   <div class="ord-content">
      <div class="ord-title">
         <span>
            Your orders
         </span>
         <button class="material-icons cl-ord click">
            close
         </button>         
      </div>
      <div class="ord-body">
         <?php 
            $orders = $this->common_database->get_data("orders", false, false);
            if ($orders) {
               foreach ($orders as $order) {
                  common::render_order($order);
               }
            }else{
               echo '<div class="flow-text center"> You have not made any orders yet</div>';
            }
          ?>       
      </div>
   </div>
</div>
<script>   

   function putImage(){
     var canvas1 = document.getElementsByTagName("canvas")[0];        
     if (canvas1.getContext) {
        var ctx = canvas1.getContext("2d");                
        var image = canvas1.toDataURL("image/png").replace("image/png", "image/octet-stream");      
     }
      window.location.href=image;                           

   }
</script>