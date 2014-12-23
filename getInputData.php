<?php

set_time_limit(0);
include  './common.php';
require  './class/Search.php';

$search = new Search();
$search->getAutocompeleteData_();
//$search->getPredictAutocompeleteData();
 ?>
  
