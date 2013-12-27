<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataFetcher
 *
 * @author Administrator
 */
class DataFetcher {
    //put your code here
    function __construct() {
       
    }
    
    function  loadMinisetMapping($FILE){
         if(!file_exists($FILE)){
            echo 'file does not exist';
            exit(0);
         }
         global $global_do_conn;
         $count = 0 ;
         $file = fopen($FILE, "r");
         while (!feof($file)){
             $line = fgets($file);
             $contents = explode(" ", $line);
             $gene_id = $contents[0];
             $doid = explode(":", $contents[1])[1];
             $score = $contents[2];
             $count++;
             $sql = "INSERT INTO miniset_mapping SET GeneID=".$gene_id.",DOID = '".$doid."',score = ".$score;
             $global_do_conn->Execute($sql);
         }
         echo $count;
    }
    
    function loadDolistMapping($FILE){
        if(!file_exists($FILE)){
            echo 'file does not exist';
            exit(0);
         }
         global $global_do_conn;
         $count = 0 ;
         $file = fopen($FILE, "r");
         while (!feof($file)){
             $line = fgets($file);
             $contents = explode(" ", $line);
             $gene_id = $contents[0];
             $doid = explode(":", $contents[1])[1];
             $pvalue = $contents[2]; 
             $score = $contents[3];
             $count++;
             $sql = "INSERT INTO dolist_mapping SET GeneID =".$gene_id.",DOID = '".$doid."',pvalue = '".$pvalue."',score = ".$score;
             $global_do_conn->Execute($sql);
         }
         echo $count;
    }
    
    function loadPreditData($FILE_NAME){
        $FILE = "./data/".$FILE_NAME.".txt";
        if(!file_exists($FILE)){
            echo 'file does not exist';
            exit(0);
         }
         global $global_do_conn;
         $count = 0 ;
         $file = fopen($FILE, "r");
         while (!feof($file)){
            $count++;
            $line = fgets($file);
            $contents = explode("\t", $line); 
            $gene_id = $contents[0];
            $gene_name = $contents[1];
            $doid = $contents[2];
            $term = $contents[3];
            $PubmedID = $contents[4];
            $geneRIF = $contents[5];
            $geneRIF = explode("\r\n", $geneRIF)[0];
            $sql = "INSERT INTO ".$FILE_NAME." SET GeneID = ".$gene_id.",GeneName = '".$gene_name."',DOID = '".$doid."' ,term = '".$term."' , PubmedID =".$PubmedID.",Generif = '".$geneRIF."'";
            $global_do_conn->Execute($sql);
         }
         echo $count;
    }
    
    function loadPredict($FILE){
        if(!file_exists($FILE)){
            echo 'file does not exist';
            exit(0);
         }
         global $global_do_conn;
         $count = 0 ;
         $file = fopen($FILE, "r");
         while (!feof($file)){
             $count++;
             $line = fgets($file);
             $contents = explode(" ", $line);
             $gene_id =$contents[0];
             $DOID = explode(":", $contents[1])[1];
             $DOID = explode("\r\n", $DOID)[0];
             $sql = "INSERT INTO predict_2013 SET gene_id = ".$gene_id." ,doid = '".$DOID."'" ;
             $global_do_conn->Execute($sql);
         }
        echo $count;
    }
    
}

?>
