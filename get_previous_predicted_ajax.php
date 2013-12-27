<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require './common.php';
require './class/Search.php';

$type = $_POST["type"];

//$type = "p0509" ;
//$gene  = "AFP";
$search = new Search();
$result = $search->get_previous_predict_results($type);
//$result =$search->get_previous_predict_results_by_gene($type, $gene);
echo json_encode($result);

?>
