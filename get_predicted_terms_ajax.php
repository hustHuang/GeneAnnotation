<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require './common.php';
require './class/Search.php';

$genes = $_POST["genes"];
$search = new Search();
$result = $search->get_predict_terms_by_gene_names($genes);
echo json_encode($result);

?>
