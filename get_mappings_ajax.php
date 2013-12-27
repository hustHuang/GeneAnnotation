<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require './common.php';
require './class/Search.php';

$gene = $_POST["gene"];
$search = new Search();
$mapings = $search->get_mappings_by_gene($gene);
echo json_encode($mapings);

?>
