<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Search
 *
 * @author Kegui Huang
 */

class Search {
    
     private $GET_GENE_ID_BY_NAME = 'SELECT g.GeneID FROM geneinfo g WHERE g.Symbol = ? OR g.Synonyms LIKE ? OR g.Synonyms LIKE ? OR g.Synonyms LIKE ?';
     private $GET_TERM_BY_ID = 'SELECT t.name FROM term_2013 t WHERE t.DOID = ?';
     private $GET_MINISET_MAPPING = 'SELECT m.DOID,m.score FROM miniset_mapping m where m.GeneID = ? ORDER BY m.score DESC' ;
     private $GET_DOLITE_MAPPING = 'SELECT m.DOID,m.score FROM dolist_mapping m where m.GeneID = ? ORDER BY m.score DESC' ;
     private $GET_TERM_BY_GENE_ID = 'SELECT t.name FROM geneinfo i, term_2013 t, generif g, term_mapping_2013 m WHERE m.generif_md5 = g.md5 AND t.DOID = m.term_id AND (m.status = 0 OR m.status = 1) AND i.GeneID = g.geneid AND g.geneid = ? ';
     private $GET_PREDICTED_TERMS_BY_GENE_ID = 'SELECT p.doid FROM predict_2013 p WHERE p.gene_id = ?';
     private $GET_ALL_GENES = 'SELECT DISTINCT GeneName FROM  ? ';
     private $GET_PREVIOUS_PREDICT_TERMS_BY_GENE = 'SELECT term ,PubmedID ,Generif FROM  ? WHERE GeneName = ? ';

     
     //REMOVE SAME NAMES
      function remove_same_name($name_array) {
        $result_array = array();
        foreach ($name_array as $name) {
            $name = trim($name);
            if ($name == '' || $name == ' ')
                continue;
            if (!in_array($name, $result_array)) {
                array_push($result_array, $name);
            } else {
                continue;
            }
        }
        return $result_array;
    }
     
     //通过GENE_ID获取GENE_NAME
     function get_gene_id_by_name($name) {
        global $global_do_conn;
        $left = $name . '|%';
        $middle = '%|' . $name . '|%';
        $right = '%|' . $name;
        return $global_do_conn->GetOne($this->GET_GENE_ID_BY_NAME, array($name, $left, $middle, $right));
    }
    
    
   //通过DOID获取term
    function get_term_by_term_id($term_id){
        global $global_do_conn;   
        return $global_do_conn->GetOne($this->GET_TERM_BY_ID,array($term_id));
    }
    
    
   //通过GENE_ID 根据GENERIF对应关系获取 TERM
   function get_term_name_by_gene_id($geneid) {
        global $global_do_conn;
        
        $result = get_array_from_resultset($global_do_conn->Execute($this->GET_TERM_BY_GENE_ID,array($geneid)));
        $term = array();
        foreach ($result as $item) {
            array_push($term, $item["name"]);
        }
        return $term;
    }
    
 
    //获取单个基因的映射
    function get_mappings_by_gene($name){     
        global $global_do_conn;
        $mappings = array();
        $miniset_array = array();
        $dolist_array = array();
        $geneid = $this->get_gene_id_by_name($name);
        $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_MAPPING,array($geneid)));
        $dolist_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING,array($geneid)));
        foreach ($miniset_mappings as $item) {
            foreach ($item as $key => $value) {
                if($key == "DOID"){
                    $term = $this->get_term_by_term_id($value);
                    $item["term"] = $term;
                } 
            }
            array_push($miniset_array, $item);
        }
        foreach ($dolist_mappings as $item) {
            foreach ($item as $key => $value) {
                if($key == "DOID"){
                    $term = $this->get_term_by_term_id($value);
                    $item["term"] = $term;
                } 
            }
            array_push($dolist_array, $item);
        }
        $mappings["dolist"] = $dolist_array;
        $mappings["miniset"] = $miniset_array;  
        return $mappings;      
    }
   //从MINISET中获取对应的网络图
    function get_miniset_cytoscapeweb($genes){
        
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $cw_node_data = '[';
        $cw_edge_data = '[';
        $nodes_array = array();
        foreach ($gene_names as $gene_name){
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_MAPPING,array($gene_id)));
            $count = 0 ;
            foreach ($miniset_mappings as $item) {
                $count++;
                $doid = $item["DOID"]; 
                $term = $this->get_term_by_term_id($doid);
                $score =$item["score"];
                if(in_array($term, $nodes_array)){
                     $cw_edge_data.=('{id:"'.$term.'_'.$gene_name .'",score:"'.$score.'",pvalue:"0",egc:"d2g",target:"'.$term .'",source:"'.$gene_name.'"},');
                }else{
                     array_push($nodes_array, $term);
                     $cw_node_data.=('{id:"'.$term .'",num:1,ngc:"r"},');
                     $cw_edge_data.=('{id:"'.$term.'_'.$gene_name .'",score:"'.$score.'",pvalue:"0",egc:"d2g",target:"'.$term .'",source:"'.$gene_name.'"},');
                }
            }
            $cw_node_data.= ('{id:"'.$gene_name .'",num:'.$count.',ngc:"q"},');
        }
       
        $cw_node_data =  substr($cw_node_data, 0,  strlen($cw_node_data)-1);
        $cw_edge_data =  substr($cw_edge_data, 0,  strlen($cw_edge_data)-1);
        $result["cw_node_data"] = $cw_node_data.']';
        $result["cw_edge_data"] = $cw_edge_data.']';
        return $result;
    }
    
    //从DOLIST中获取对应的网络图
    function get_dolist_cytoscapeweb($genes){
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $cw_node_data = '[';
        $cw_edge_data = '[';
        $nodes_array = array();
        foreach ($gene_names as $gene_name){
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING,array($gene_id)));
            $count = 0 ;
            foreach ($miniset_mappings as $item) {
                $count++;
                $doid = $item["DOID"]; 
                $term = $this->get_term_by_term_id($doid);
                $score =$item["score"];
                if(in_array($term, $nodes_array)){
                     $cw_edge_data.=('{id:"'.$term.'_'.$gene_name .'",score:"'.$score.'",pvalue:"0",egc:"d2g",target:"'.$term .'",source:"'.$gene_name.'"},');
                }else{
                    array_push($nodes_array, $term);
                    $cw_node_data.=('{id:"'.$term .'",num:1,ngc:"r"},');
                    $cw_edge_data.=('{id:"'.$term.'_'.$gene_name .'",score:"'.$score.'",pvalue:"0",egc:"d2g",target:"'.$term .'",source:"'.$gene_name.'"},');
                }  
            }
            $cw_node_data.= ('{id:"'.$gene_name .'",num:'.$count.',ngc:"q"},');
        }
       
        $cw_node_data =  substr($cw_node_data, 0,  strlen($cw_node_data)-1);
        $cw_edge_data =  substr($cw_edge_data, 0,  strlen($cw_edge_data)-1);
        $result["cw_node_data"] = $cw_node_data.']';
        $result["cw_edge_data"] = $cw_edge_data.']';
        return $result;
    }
    
    //从GENE_RIF中获取网络图
    function get_generif_cytoscapeweb($genes){
         global $global_do_conn;
         $gene_names = $this->remove_same_name(explode(" ", $genes));
         $cw_node_data = '[';
         $cw_edge_data = '[';  
         $nodes_array = array();
         foreach ($gene_names as $gene_name) {
             $gene_id = $this->get_gene_id_by_name($gene_name);
             if (is_null($gene_id) || $gene_id == '')
                continue;
             $term_array = $this->get_term_name_by_gene_id($gene_id);
             $result_term_nodes = array();
             foreach($term_array as $term){
                 if(key_exists($term, $result_term_nodes)){
                     $result_term_nodes[$term]++ ; 
                 }else{
                     $result_term_nodes[$term] = 1;
                 }    
             }
             arsort($result_term_nodes);
             foreach ($result_term_nodes as $key => $value) {
                 if(in_array($key, $nodes_array)){
                     $cw_edge_data .= ('{id:"' . $gene_name . '-' . $key . '", score: "0.5", pvalue:"0",egc: "g2d",target:"' . $gene_name . '",source:"' . $key . '"},');
                 } else{
                     array_push($nodes_array,$key);
                     $cw_node_data .= ('{id:"' . $key . '", num:' . $value . ',ngc:"r"},');
                     $cw_edge_data .= ('{id:"' . $gene_name.'-' .$key. '", score: "0.5", pvalue:"0",egc: "g2d",target:"' . $gene_name . '",source:"' . $key . '"},');
                 }
             }
             
             $cw_node_data .= ('{id:"' . $gene_name . '", num: 50 , ngc:"q"},');
         }
        $cw_node_data =  substr($cw_node_data, 0,  strlen($cw_node_data)-1);
        $cw_edge_data =  substr($cw_edge_data, 0,  strlen($cw_edge_data)-1);
        $result["cw_node_data"] = $cw_node_data.']';
        $result["cw_edge_data"] = $cw_edge_data.']';
        
        return $result;
    }
    
    //根据基因名预测DO TERM
    function  get_predict_terms_by_gene_names($genes){
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $result = array();
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $terms = array();
            $terms_array = get_array_from_resultset($global_do_conn->Execute($this->GET_PREDICTED_TERMS_BY_GENE_ID,array($gene_id)));
            foreach ($terms_array as $item) {
                $doid = $item['doid'];
                $term =  $this->get_term_by_term_id($doid);
                array_push($terms, $term);
            }
            $result[$gene_name] = $terms;
        }
        return $result;
    }
    
    //根据基因名获取之前预测到的DO TERM
    function get_previous_predict_results_by_gene($type,$gene){
        global $global_do_conn;
        $sql = 'SELECT p.term ,p.PubmedID ,p.Generif FROM '.$type.' p WHERE p.GeneName = "'.$gene.'"';
        $terms_array = get_array_from_resultset($global_do_conn->Execute($sql));
        $result[$gene] = $terms_array;
        return $result;
    }
    
    //得到每一种之前预测的全部结果
    function get_previous_predict_results($type){
        global $global_do_conn; 
        $sql = 'SELECT DISTINCT GeneName FROM '.$type;
        $result = array();
        $genes_array = get_array_from_resultset($global_do_conn->Execute($sql));
        foreach ($genes_array as $item) {
            $terms_array = $this->get_previous_predict_results_by_gene($type, $item["GeneName"]);
            array_push($result, $terms_array);
        }
        return $result;
    }
    
    
}

?>
