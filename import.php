<?php

/**
 * @description : Save the client side data 
 *
 * @author Kegui Huang
 */ 
   
   //GET TYPE
   $type = $_POST['type'];

   //Get the raw POST data:  
   $data = $_POST["data"];

   //ONLY png AND pdf are base64 encoded
   if($type == "png" || $type == "pdf"){
      $data = base64_decode($data);
   }
   
   //set the temp file name
   $filename = "./tmp/network.".$type;
   
   //write the file
   file_put_contents($filename, $data);
?>
