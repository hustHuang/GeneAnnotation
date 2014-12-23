<?php

require './common.php';
require './class/Search.php';

set_time_limit(0);
$genes = $_POST["genes"];
$search = new Search();

//$cytoscapeweb_data = $search->get_cytoscapeweb($genes);
$cytoscapeweb_data = $search->get_all_related_gene_cytoscapeweb($genes);
echo json_encode($cytoscapeweb_data);

?>
