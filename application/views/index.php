<div class="index">
   <div class="app-content">      
      <div class="nav"> 
         <div class="logo-cont">
            <img src="<?php echo base_url("media/system/logo.png"); ?>" alt="logo" class="logo_index">
         </div>       
         <div class="n-left">
            <div class="socials">
               <img src="<?php echo base_url("media/system/twitter.png") ?>" alt="" class="social click">
               <img src="<?php echo base_url("media/system/facebook.svg") ?>" alt="" class="social click">
               <img src="<?php echo base_url("media/system/ig.png") ?>" alt="" class="social click">
            </div>
         </div>
         <div class="n-right">
            <div class="logs">
               <button class="logger sign click">
                  Sign up  
                  <i class="material-icons">person_add</i>     
               </button>
               <button class="logger log click">
                  Log In   
                  <i class="material-icons">lock_open</i>         
               </button>
            </div>
         </div>
      </div>
      <div class="index-content">
         <div class="desc">
            A new way to experience food.
         </div>
         <div>
            <button class="join click">
               SHOP NOW
               <i class="material-icons">shopping_cart</i>
            </button>
         </div>
      </div>      
   </div>   
</div>
<div class="app-modal" id="logger">
   <div class="modal-content">
      <div class="modal-body">
         <div class="log-split">
            <button class="split-item click">
               Sign up
            </button>            
            <button class="split-item click active">
               Log in
            </button>
         </div>
         <div class="sign-up">
            <div class="in-group">
               <label class="log-label"> Full name</label>
               <input type="text" id="name" placeholder="Name" class="log-in browser-default">
            </div>
            <div class="in-group">
               <label class="log-label"> Email Address</label>
               <input type="text" id="email_sign" placeholder="Email address" class="log-in browser-default">
            </div>
            <div class="in-group">
               <label class="log-label">Phone Number</label>
               <input type="text" id="phone" placeholder="Phone Number" class="log-in browser-default">
            </div>
            <div class="in-group">
               <label class="log-label">Password</label>
               <input type="password" id="pass_sign" placeholder="Password" class="log-in browser-default">
            </div>
            <div class="in-group bttn">
               <button class="log-go sign-go click">
                  Create account
                  <i class="material-icons">arrow_forward</i>
               </button>   
            </div>
         </div>
         <div class="logg-in">
            <div class="in-group">
               <label class="log-label"> Email Address</label>
               <input type="text" id="email_log" placeholder="Email address" class="log-in browser-default">
            </div>            
            <div class="in-group">
               <label class="log-label">Password</label>
               <input type="password" id="pass_log" placeholder="Password" class="log-in browser-default">
            </div>
            <div class="in-group bttn">
               <button class="log-go l-go click">
                  Log in
                  <i class="material-icons">arrow_forward</i>
               </button>   
            </div>
         </div>
         <div class="modal-tools">
            <button class="material-icons cls-log click">
               close
            </button>  
            <img src="<?php echo base_url("media/system/rolling.svg"); ?>" alt="loader" class="log-loader">      
         </div>
      </div>
   </div>
</div>
