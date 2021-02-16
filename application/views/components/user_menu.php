<div class="user-content">
   <?php 
      if (isset($admin)) {
         echo '
            <button class="m-item click user-ac">
               Home
               <i class="material-icons">launch</i>
            </button>
         ';
      }else{
         echo '
         <button class="m-item click admin-ac">
            Admin account
            <i class="material-icons">launch</i>
         </button>
         ';
      }
    ?>
   <?php 
      if (!isset($admin)) {
         echo '
            <button class="m-item v-ord click">
               My orders
               <i class="material-icons">list</i>
            </button>
         ';
      }
    ?>
   <button class="m-item click logout">
      Log out
      <i class="material-icons">lock</i>
   </button>
</div>