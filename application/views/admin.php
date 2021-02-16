<div class="app">
   <nav class="app-nav">
      <div class="nav-content">
        <div class="n-search">
            <div class="admin-text">
              Admin portal
            </div>
        </div>
         <div class="cart">            
            <img class="user" src="<?php echo base_url("media/system/profile.svg") ?>" alt="User">
            <?php $this->load->view("components/user_menu", ["admin" => true]); ?>
         </div>
      </div>
   </nav>
   <div class="admin-content">
      <div class="admin-loader">
         <img class="admin-load" src="<?php echo(base_url("media/system/rolling.svg")); ?>" alt="">
      </div>
      <div class="admin-nav">
         <a class="n-item click">
            Orders
         </a>
         <a class="n-item active click">
            Menu Items
         </a>
      </div>
      <div class="admin-tab">
         <div class="tab-top">
            <button class="add-menu click">
               Add new item 
               <i class="material-icons">add</i>
            </button>
         </div>
      </div>
   </div>
</div>
<div class="new-menu">
   <div class="new-m-content">
      <div class="new-m-title">
         <button class="cl-add-m material-icons click">
            close
         </button>
         <div>
            Add menu item
         </div>
      </div>
      <div class="new-m-body">
         
      </div>
   </div>
</div>