<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require './common.php';
require './class/Search.php';

set_time_limit(0);
$data = $_POST["data"];
$type = $_POST["type"];
$search = new Search();

$cytoscapeweb_data = $search->get_related_gene_cytoscapeweb($data ,$type);
echo json_encode($cytoscapeweb_data);

?>
