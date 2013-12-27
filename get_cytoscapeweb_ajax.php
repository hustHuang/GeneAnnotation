<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require './common.php';
require './class/Search.php';

$genes = $_POST["genes"];
$search = new Search();
$cytoscapeweb_data["miniset_data"] = $search->get_miniset_cytoscapeweb($genes);
$cytoscapeweb_data["dolist_data"] = $search->get_dolist_cytoscapeweb($genes);
$cytoscapeweb_data["generif_data"] = $search->get_generif_cytoscapeweb($genes);
echo json_encode($cytoscapeweb_data);

//$gene_array = explode(" ", $genes);
//$gene_id =$search->get_gene_id_by_name($gene_array[0]);
//$test = $search->get_term_name_by_gene_id($gene_id);
//echo json_encode($test);
?>
