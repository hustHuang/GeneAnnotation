<?php


require './common.php';
require './class/Search.php';
$type = $_POST["type"];
$gene = $_POST["gene"];
$search = new Search();
/*if($type == "dl"){
 	//$mapings = $search->get_mappings_by_gene($gene);
    $mappings = $search->get_dolist_mappings_by_gene_name($gene);
}else if($type == "ms"){
    $mappings = $search->get_mappings_by_gene($gene);
}*/

$mappings = $search->get_mappings($gene, $type);

echo json_encode($mappings);

?>
