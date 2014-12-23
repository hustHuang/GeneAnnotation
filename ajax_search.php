<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './common.php';
require_once './class/search.php';
$search_service = new Search_nw();
$keywords = $_REQUEST['terms'];
$chosen = $_REQUEST['chosen'];
$search_service->change_sql($chosen);
$search_result = $search_service->execut_search($keywords,$chosen);
$result['cw_node_data'] = $search_service->get_cw_node_data();
$result['cw_edge_data'] = $search_service->get_cw_edge_data();
echo json_encode($result);

//var_dump($child_node_ids);
//$term_ids='28682';
//$term_ids='1140093';
//$term_ids='2720507';
//$term_ids=array($term_ids);
//$child_node_ids=array();
//$search_result=$search_service->get_all_child_ids_sh($term_ids,$child_node_ids);
//echo count($search_result);
?>
