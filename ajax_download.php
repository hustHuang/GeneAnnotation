<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$filename = $_REQUEST["type"];
$file = "data/".$filename.".txt";
$name = $filename.".txt";
header('Content-Description: File Transfer');
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $name);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header("Content-Length: " . filesize($file));
readfile($file);

?>
