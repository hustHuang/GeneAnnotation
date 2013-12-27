<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
set_time_limit(0);
include  './common.php';
require  './class/DataFetcher.php';
$FILE1 = "./data/miniset.txt" ;
$FILE2 = "./data/pval_paper.txt" ;
$FILE3 ="./data/predict_2013.txt";
$data = new DataFetcher();
/*
$data->loadMinisetMapping($FILE1);
$data->loadDolistMapping($FILE2);
*/
$data->loadPreditData("p0509");
$data->loadPreditData("p0513");
$data->loadPreditData("p0913");

 /*
$data->loadPredict($FILE3);
*/
 ?>
  
