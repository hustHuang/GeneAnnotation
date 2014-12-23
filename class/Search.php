<?php
/**
 * Description of Search
 *
 * @author Kegui Huang
 */
class Search {

    private $GET_GENE_ID_BY_NAME = 'SELECT g.GeneID FROM geneinfo g WHERE g.Symbol = ? OR g.Synonyms LIKE ? OR g.Synonyms LIKE ? OR g.Synonyms LIKE ?';
    private $GET_GENE_NAME_BY_ID = 'SELECT g.Symbol,g.Synonyms FROM geneinfo g WHERE g.GeneID = ?';
    private $GET_TERM_BY_ID = 'SELECT t.name FROM term_2013 t WHERE t.DOID = ?';
    private $GET_MINISET_MAPPING = 'SELECT m.DOID,m.score FROM miniset_mapping m where m.GeneID = ? ORDER BY m.score DESC';
    private $GET_DOLITE_MAPPING = 'SELECT m.DOID,m.score FROM dolist_mapping m where m.GeneID = ? ORDER BY m.score DESC';
    private $GET_TERM_BY_GENE_ID = 'SELECT t.name FROM geneinfo i, term_2013 t, generif g, term_mapping_2013 m WHERE m.generif_md5 = g.md5 AND t.DOID = m.term_id AND (m.status = 0 OR m.status = 1) AND i.GeneID = g.geneid AND g.geneid = ? ';
    private $GET_PREDICTED_TERMS_BY_GENE_ID = 'SELECT p.doid FROM predict_2013 p WHERE p.gene_id = ?';
    private $GET_DOLITE_GENE_ID = 'SELECT DISTINCT GeneID FROM dolist_mapping';
    private $GET_GENE_ID = 'SELECT DISTINCT GeneID FROM geneinfo';
    private $GET_MINISET_GENE_ID = 'SELECT DISTINCT GeneID FROM miniset_mapping';
    private $GET_PREDICT_GENE_ID = 'SELECT DISTINCT gene_id FROM predict_2013';

    private $CONFIRM_PREDICT_SQL = 'SELECT * FROM predict_2013 where gene_id = ? and doid = ?';
    //private $GET_GENERIF_INFO_SQL = 'select t.generif_id, g.text from term_mapping_2013 t inner join generif g on t.generif_md5 = g.md5 where g.geneid = ? and t.term_id = ?';
    private $GET_GENERIF_INFO_SQL = 'select DISTINCT t.generif_id, g.text from term_mapping_2013 t inner join generif g on t.text = g.text where g.geneid = ? and t.term_id = ?';
    private $GET_GENE_DO_MAPPING = 'SELECT m.score FROM dolist_mapping m WHERE m.GeneID = ? AND m.DOID = ?';
    
    private $cw_node_data;
    private $cw_edge_data;
    private $cw_edge_type = array();
    private $related_gene_array = array();

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

    //通过GENE_NAME获取GENE_ID
    function get_gene_id_by_name($name) {
        global $global_do_conn;
        $left = $name . '|%';
        $middle = '%|' . $name . '|%';
        $right = '%|' . $name;
        return $global_do_conn->GetOne($this->GET_GENE_ID_BY_NAME, array($name, $left, $middle, $right));
    }

    //通过GENE_ID获取GENE_NAME
    function get_gene_name_by_id($id) {
        global $global_do_conn;
        $result_array = array();
        $result = get_array_from_resultset($global_do_conn->Execute($this->GET_GENE_NAME_BY_ID, array($id)));
        foreach ($result as $item) {
            $symbol = $item["Symbol"];
            array_push($result_array, $symbol);
            /*
            $synonyms = $item["Synonyms"];
            $synonyms_array = explode("|", $synonyms);
            foreach ($synonyms_array as $value) {
                array_push($result_array, $value);
            }
           */
        }
        return $result_array[0];
    }

    //通过DOID获取term
    function get_term_by_term_id($term_id) {
        global $global_do_conn;
        return $global_do_conn->GetOne($this->GET_TERM_BY_ID, array($term_id));
    }

    //通过GENE_ID 根据GENERIF对应关系获取 TERM
    function get_term_name_by_gene_id($geneid) {
        global $global_do_conn;
        $result = get_array_from_resultset($global_do_conn->Execute($this->GET_TERM_BY_GENE_ID, array($geneid)));
        $term = array();
        foreach ($result as $item) {
            array_push($term, $item["name"]);
        }
        return $term;
    }

    //获取单个基因的映射
    function get_mappings_by_gene($name) {
        global $global_do_conn;
        $mappings = array();
        $miniset_array = array();
        $dolist_array = array();
        $geneid = $this->get_gene_id_by_name($name);
        $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_MAPPING, array($geneid)));
        $dolist_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING, array($geneid)));
        foreach ($miniset_mappings as $item) {
            foreach ($item as $key => $value) {
                if ($key == "DOID") {
                    $term = $this->get_term_by_term_id($value);
                    $item["term"] = $term;
                }
            }
            array_push($miniset_array, $item);
        }
        $miniset[$name] = $miniset_array;
        foreach ($dolist_mappings as $item) {
            foreach ($item as $key => $value) {
                if ($key == "DOID") {
                    $term = $this->get_term_by_term_id($value);
                    $item["term"] = $term;
                }
            }
            array_push($dolist_array, $item);
        }
        $dolist[$name] = $dolist_array;
        $mappings["dolist"] = $dolist;
        $mappings["miniset"] = $miniset;
        return $dolist;
    }

    //do by zxy
    function get_mappings($gene_name, $type){
        $dolist = array();
        $name_list = explode(" ", $gene_name);
        foreach ($name_list as $key => $name) {
            if (trim($name) != "" && !is_null($name)) {
                $list = $this->get_mappings_by_gene_name($name, $type);
                $dolist[$name] = $list;
            }
        }
        return $dolist;
    }

    //获取两个TABLE的数据
    function get_mappings_by_gene_name($gene_name, $type){
        global $global_do_conn;
        $mappings_array = array();
        $geneid = $this->get_gene_id_by_name($gene_name);
        if($type == 'dl'){
            $mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING, array($geneid)));
        }else{
            $mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_MAPPING, array($geneid)));
        }
        if(0 != count($mappings)){
            foreach ($mappings as $item) {
                $term = $this->get_term_by_term_id($item["DOID"]);
                $item["term"] = $term;
                $predict_info = get_array_from_resultset($global_do_conn->Execute($this->CONFIRM_PREDICT_SQL , array($geneid, $item['DOID'])));
                if (count($predict_info) != 0) {
                    $item["direct"] = 'predict';
                } else {
                    $item["direct"] = 'direct';
                }
                $generif_info = get_array_from_resultset($global_do_conn->Execute($this->GET_GENERIF_INFO_SQL, array($geneid, $item['DOID'])));
                if (0 != count($generif_info)) {
                   $item["GeneRIF"] = $generif_info;
                   array_push($mappings_array, $item); 
                } else {
                    $item["GeneRIF"] = array();
                    array_push($mappings_array, $item);
                }
            }
        }
        return $mappings_array;
    }
    
    //获取五种反应的关系，v1
    function get_gene_relation_by_gene_id($gene_id) {
        global $global_do_conn;
        $nodes = array();
        $gene_name = $this->get_gene_name_by_id($gene_id);
        
        $relation = array('genetic_interactions', 'co_expression', 'co_localization', 'physical_interactions', 'shared_protein_domains');

        foreach ($relation as $value) {
            $sql = 'SELECT r.Gene_A_Id,r.Gene_B_Id,r.Weight FROM ' . $value . ' r WHERE (r.Gene_A_Id = ? OR r.Gene_B_Id = ?) AND r.Weight > 0.2';
            $result = get_array_from_resultset($global_do_conn->Execute($sql, array($gene_id, $gene_id)));
            if (count($result) == 0) {
                continue;
            }
            array_push($this->cw_edge_type, $value);
            $relation_nodes = array();
            foreach ($result as $item) {
                if($item["Gene_A_Id"] == $item["Gene_B_Id"]){
                    continue;
                }
                if ($item["Gene_A_Id"] == $gene_id) { 
                    if (!in_array($item["Gene_B_Id"], $nodes)) {
                        array_push($nodes, $item["Gene_B_Id"]);
                        $gene = $this->get_gene_name_by_id($item["Gene_B_Id"]);
                        $this->cw_node_data .= ('{id:"' . $gene . '",num:1,ngc:"g"},');
                    }
                    if (!in_array($item["Gene_B_Id"], $relation_nodes)) {
                        array_push($relation_nodes, $item["Gene_B_Id"]);
                        $gene = $this->get_gene_name_by_id($item["Gene_B_Id"]);
                        $this->cw_edge_data.=('{id:"' . $gene . '_' . $gene_name . '_' . $value . '",score:"' . $item["Weight"] . '",pvalue:"2",egc:"' . $value . '",target:"' . $gene . '",source:"' . $gene_name . '"},');
                    }
                } else {          
                    if (!in_array($item["Gene_A_Id"], $nodes)) {
                        array_push($nodes, $item["Gene_A_Id"]);
                        $gene = $this->get_gene_name_by_id($item["Gene_A_Id"]);
                        $this->cw_node_data .= ('{id:"' . $gene . '",num:1,ngc:"g"},');
                    }
                    if (!in_array($item["Gene_A_Id"], $relation_nodes)) {
                        array_push($relation_nodes, $item["Gene_A_Id"]);
                        $gene = $this->get_gene_name_by_id($item["Gene_A_Id"]);
                        $this->cw_edge_data.=('{id:"' . $gene . '_' . $gene_name . '_' . $value . '",score:"' . $item["Weight"] . '",pvalue:"2",egc:"' . $value . '",target:"' . $gene . '",source:"' . $gene_name . '"},');
                    }
                }
            }
        }
        return $nodes;
    }

    // 获取GENE对应的DO及五种反应的网络图,Weight给定阈值 0.2，v1
    function get_cytoscapeweb($genes) {
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $this->cw_node_data = '[';
        $this->cw_edge_data = '[';
        $nodes_array = array();
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $gene_relations = $this->get_gene_relation_by_gene_id($gene_id);
            $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING, array($gene_id)));
            $count = 0;
            foreach ($miniset_mappings as $item) {
                $count++;
                $doid = $item["DOID"];
                $term = $this->get_term_by_term_id($doid);
                $score = $item["score"];
                if (in_array($term, $nodes_array)) {
                    $this->cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"0",pvalue:"' . $score . '",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                } else {
                    array_push($nodes_array, $term);
                    $this->cw_node_data.=('{id:"' . $term . '",num:1,ngc:"r"},');
                    $this->cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"0",pvalue:"' . $score . '",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                }
                
                foreach ($gene_relations as $related_gene_id) {
                    $related_mapping = get_array_from_resultset($global_do_conn->Execute($this->GET_GENE_DO_MAPPING, array($related_gene_id, $doid)));
                    if (count($related_mapping) > 0) {
                        $related_gene_name = $this->get_gene_name_by_id($related_gene_id);
                        $score = $related_mapping[0]["score"];
                        $this->cw_edge_data.=('{id:"' . $term . '_' . $related_gene_name . '",score:"0",pvalue:"' . $score . '",egc:"d2g",target:"' . $term . '",source:"' . $related_gene_name . '"},');
                    }
                }
            }
            $this->cw_node_data.= ('{id:"' . $gene_name . '",num:' . $count . ',ngc:"q"},');
        }

        $this->cw_node_data = substr($this->cw_node_data, 0, strlen($this->cw_node_data) - 1);
        $this->cw_edge_data = substr($this->cw_edge_data, 0, strlen($this->cw_edge_data) - 1);
        $result["cw_node_data"] = $this->cw_node_data . ']';
        $result["cw_edge_data"] = $this->cw_edge_data . ']';
        $result["cw_edge_type"] = $this->cw_edge_type;
        return $result;
    }

    //获取基因的DO与五种反应关系的联接，v2
    function get_relation_nodes_by_gene_id($gene_id, $doid_array) {

        global $global_do_conn;
        $gene_related_nodes = array(); //与这个基因相关的所有独特基因 
        $gene_name = $this->get_gene_name_by_id($gene_id);
        $relation = array('genetic_interactions', 'co_expression', 'co_localization', 'physical_interactions', 'shared_protein_domains');
        //$relation = array('genetic_interactions', 'co_expression', 'co_localization');

        foreach ($relation as $value) {
            $related_nodes = array(); //与这个基因有有这种关系的所有独特基因
            $related_edge_nodes = array(); //与这个基因有这种关系的已联接的基因
            $sql = 'SELECT r.Gene_A_Id,r.Gene_B_Id FROM ' . $value . ' r WHERE (r.Gene_A_Id = ? OR r.Gene_B_Id = ?) AND r.Weight > 0.01';
            $result = get_array_from_resultset($global_do_conn->Execute($sql, array($gene_id, $gene_id)));
            if (count($result) == 0 || is_null($result)) {
                continue;
            }
            foreach ($result as $item) {
                if ($item["Gene_A_Id"] == $item["Gene_B_Id"]) {
                    continue;
                }
                if ($item["Gene_A_Id"] == $gene_id) {
                    if (!in_array($item["Gene_B_Id"], $related_nodes)) {
                        array_push($related_nodes, $item["Gene_B_Id"]);
                    }
                } else {
                    if (!in_array($item["Gene_A_Id"], $related_nodes)) {
                        array_push($related_nodes, $item["Gene_A_Id"]);
                    }
                }
            }

            $related_count = 0;
            foreach ($related_nodes as $related_node) {
                foreach ($doid_array as $doid) {
                    $related_mapping = get_array_from_resultset($global_do_conn->Execute($this->GET_GENE_DO_MAPPING, array($related_node, $doid)));
                    if (count($related_mapping) > 0) {
                        $term = $this->get_term_by_term_id($doid);
                        $related_gene_name = $this->get_gene_name_by_id($related_node);
                        $score = $related_mapping[0]["score"];
                        if (!in_array($related_gene_name, $this->related_gene_array)) {
                            array_push($this->related_gene_array, $related_gene_name);
                            $this->cw_node_data .= ('{id:"' . $related_gene_name . '",num:1, ngc:"g"},');
                        }
                        $this->cw_edge_data .= ('{id:"' . $term . '_' . $related_gene_name . '",score:"10",pvalue:' . $score . ',egc:"d2g",target:"' . $term . '",source:"' . $related_gene_name . '"},');
                        if (!in_array($related_gene_name, $gene_related_nodes)) {
                            array_push($gene_related_nodes, $related_gene_name);        //该基因对应的基因不存在时，才加入一条边
                            array_push($related_edge_nodes, $related_gene_name);
                            $this->cw_edge_data .= ('{id:"' . $gene_name . '_' . $related_gene_name . '_' . $value . '",score:"5",pvalue:2,egc:"' . $value . '",target:"' . $gene_name . '",source:"' . $related_gene_name . '"},');
                            $related_count ++;
                        }else{
                            if(!in_array($related_gene_name, $related_edge_nodes)){
                                array_push($related_edge_nodes, $related_gene_name);    //该种类型的反应不存在时，才加入连接
                                $this->cw_edge_data .= ('{id:"' . $gene_name . '_' . $related_gene_name . '_' . $value . '",score:"5",pvalue:2,egc:"' . $value . '",target:"' . $gene_name . '",source:"' . $related_gene_name . '"},');
                                $related_count ++;
                            }
                        }
                    }
                }
            }
            //有这种反应，则加入这种类型的标识
            if ($related_count > 0) {
                if (!in_array($value, $this->cw_edge_type)) {
                   array_push($this->cw_edge_type, $value);
                }
            }
        }
    }

    // 获取GENE对应的DO及五种反应的网络图 ，v2
    function get_all_related_gene_cytoscapeweb($genes) {
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $this->cw_node_data = '[';
        $this->cw_edge_data = '[';
        $nodes_array = array();
        $doid_array = array();

        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $dolist_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING, array($gene_id)));
            $count = 0;
            foreach ($dolist_mappings as $item) {
                $count++;
                $doid = $item["DOID"];
                $term = $this->get_term_by_term_id($doid);
                $score = $item["score"];
                if (in_array($term, $nodes_array)) {
                    $this->cw_edge_data .= ('{id:"' . $term . '_' . $gene_name . '",score:"25",pvalue:' . $score . ',egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                } else {
                    array_push($doid_array, $doid);
                    array_push($nodes_array, $term);
                    $this->cw_node_data .= ('{id:"' . $term . '",num:2,ngc:"r"},');
                    $this->cw_edge_data .= ('{id:"' . $term . '_' . $gene_name . '",score:"25",pvalue:' . $score . ',egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                }
            }
            //$this->cw_node_data.= ('{id:"' . $gene_name . '",num:' . $count . ',ngc:"q"},');
             $this->cw_node_data.= ('{id:"' . $gene_name . '",num:12,ngc:"q"},');
        }
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $this->get_relation_nodes_by_gene_id($gene_id, $doid_array);
        }

        $this->cw_node_data = substr($this->cw_node_data, 0, strlen($this->cw_node_data) - 1);
        $this->cw_edge_data = substr($this->cw_edge_data, 0, strlen($this->cw_edge_data) - 1);
        $result["cw_node_data"] = $this->cw_node_data . ']';
        $result["cw_edge_data"] = $this->cw_edge_data . ']';
        $result["cw_edge_type"] = $this->cw_edge_type;
        return $result;
    }

    function get_related_gene_cytoscapeweb($data, $type) {
        $this->cw_edge_data = "[";
        $this->cw_node_data = "[";
        global $global_do_conn;
        $gene_related_nodes = array(); //与这个基因相关的所有独特基因
        foreach ($data as $gene => $do_items) {
            $gene_id = $this->get_gene_id_by_name($gene);
            $related_nodes = array(); //与这个基因有有这种关系的所有独特基因
            $related_edge_nodes = array(); //与这个基因有这种关系的已联接的基因
            $sql = 'SELECT r.Gene_A_Id,r.Gene_B_Id FROM ' . $type . ' r WHERE (r.Gene_A_Id = ? OR r.Gene_B_Id = ?) AND r.Weight > 0.01';
            $result = get_array_from_resultset($global_do_conn->Execute($sql, array($gene_id, $gene_id)));
            if (count($result) == 0 || is_null($result)) {
                continue;
            }
            foreach ($result as $item) {
                if ($item["Gene_A_Id"] == $item["Gene_B_Id"]) {
                    continue;
                }
                if ($item["Gene_A_Id"] == $gene_id) {
                    if (!in_array($item["Gene_B_Id"], $related_nodes)) {
                        array_push($related_nodes, $item["Gene_B_Id"]);
                    }
                } else {
                    if (!in_array($item["Gene_A_Id"], $related_nodes)) {
                        array_push($related_nodes, $item["Gene_A_Id"]);
                    }
                }
            }

            $related_count = 0;
            foreach ($related_nodes as $related_node) {
                foreach ($do_items as $do) {
                    $doid = $do["DOID"];
                    $related_mapping = get_array_from_resultset($global_do_conn->Execute($this->GET_GENE_DO_MAPPING, array($related_node, $doid)));
                    if (count($related_mapping) > 0) {
                        $term =  $do["term"];
                        $related_gene_name = $this->get_gene_name_by_id($related_node);
                        $score = $related_mapping[0]["score"];
                        if (!in_array($related_gene_name, $this->related_gene_array)) {
                            array_push($this->related_gene_array, $related_gene_name);
                            $this->cw_node_data .= ('{id:"' . $related_gene_name . '",num:1, ngc:"g"},');
                        }
                        $this->cw_edge_data .= ('{id:"' . $term . '_' . $related_gene_name . '",score:"10",pvalue:' . $score . ',egc:"d2g",target:"' . $term . '",source:"' . $related_gene_name . '"},');
                        if (!in_array($related_gene_name, $gene_related_nodes)) {
                            array_push($gene_related_nodes, $related_gene_name);        //该基因对应的基因不存在时，才加入一条边
                            array_push($related_edge_nodes, $related_gene_name);
                            $this->cw_edge_data .= ('{id:"' . $gene . '_' . $related_gene_name . '_' . $type . '",score:"5",pvalue:2,egc:"' . $type . '",target:"' . $gene . '",source:"' . $related_gene_name . '"},');
                            $related_count++;
                        } else {
                            if (!in_array($related_gene_name, $related_edge_nodes)) {
                                array_push($related_edge_nodes, $related_gene_name);    //该种类型的反应不存在时，才加入连接
                                $this->cw_edge_data .= ('{id:"' . $gene . '_' . $related_gene_name . '_' . $type . '",score:"5",pvalue:2,egc:"' . $type . '",target:"' . $gene . '",source:"' . $related_gene_name . '"},');
                                $related_count++;
                            }
                        }
                    }
                }
            }
        }
        if(strlen($this->cw_node_data) > 2){
            $this->cw_node_data = substr($this->cw_node_data, 0, strlen($this->cw_node_data) - 1);
        }
        if(strlen($this->cw_edge_data) > 2){
            $this->cw_edge_data = substr($this->cw_edge_data, 0, strlen($this->cw_edge_data) - 1);
        }
        $result_data["cw_node_data"] = $this->cw_node_data . ']';
        $result_data["cw_edge_data"] = $this->cw_edge_data . ']';
        return $result_data;
    }

    //根据基因名预测DO TERM
    function get_predict_terms_by_gene_names($genes) {
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $result = array();
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $terms = array();
            $terms_array = get_array_from_resultset($global_do_conn->Execute($this->GET_PREDICTED_TERMS_BY_GENE_ID, array($gene_id)));
            foreach ($terms_array as $item) {
                $doid = $item['doid'];
                $term = $this->get_term_by_term_id($doid);
                array_push($terms, $term);
            }
            $result[$gene_name] = $terms;
        }
        return $result;
    }

    //根据基因名获取之前预测到的DO TERM
    function get_previous_predict_results_by_gene($type, $gene) {
        global $global_do_conn;
        $sql = 'SELECT p.term ,p.PubmedID ,p.Generif FROM ' . $type . ' p WHERE p.GeneName = "' . $gene . '"';
        $terms_array = get_array_from_resultset($global_do_conn->Execute($sql));
        $result[$gene] = $terms_array;
        return $result;
    }

    //得到每一种之前预测的全部结果
    function get_previous_predict_results($type) {
        global $global_do_conn;
        $sql = 'SELECT DISTINCT GeneName FROM ' . $type;
        $result = array();
        $genes_array = get_array_from_resultset($global_do_conn->Execute($sql));
        foreach ($genes_array as $item) {
            $terms_array = $this->get_previous_predict_results_by_gene($type, $item["GeneName"]);
            array_push($result, $terms_array);
        }
        return $result;
    }

    //获取输入的提示基因
    function getAutocompeleteData() {
        global $global_do_conn;
        $gene_id_array = array();
        $gene_names_array = array();
        $result_data = 'var names = [';

        $id1_array = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_GENE_ID));
        $id2_array = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_GENE_ID));
        $id3_array = get_array_from_resultset($global_do_conn->Execute($this->GET_PREDICT_GENE_ID));
        foreach ($id1_array as $item) {
            $gene_id = $item["GeneID"];
            array_push($gene_id_array, $gene_id);
            $gene_name_array = $this->get_gene_name_by_id($gene_id);
            $gene_names_array = array_merge($gene_names_array, $gene_name_array);
        }
        foreach ($id2_array as $item) {
            $gene_id = $item["GeneID"];
            
            if (!in_array($gene_id, $gene_id_array)) {
                echo $gene_id."<br/>";
                array_push($gene_id_array, $gene_id);
                $gene_name_array = $this->get_gene_name_by_id($gene_id);
                $gene_names_array = array_merge($gene_names_array, $gene_name_array);
            }
        }
        foreach ($id3_array as $item) {
            $gene_id = $item["gene_id"];
            
            if (!in_array($gene_id, $gene_id_array)) {
                echo $gene_id."<br/>";
                array_push($gene_id_array, $gene_id);
                $gene_name_array = $this->get_gene_name_by_id($gene_id);
                $gene_names_array = array_merge($gene_names_array, $gene_name_array);
            }
        }
        
        foreach ($gene_names_array as $gene_name) {
            $result_data .= '{k:"' . $gene_name . '"},';
        }
        $result_data = rtrim($result_data, ",");
        $result_data .= '];';
        $file = fopen("./data/gene_names.js", "wb");
        fwrite($file, $result_data);
        fclose($file);
    }
    
function getAutocompeleteData_() {
        global $global_do_conn;
        $gene_id_array = array();
        $gene_names_array = array();
        $result_data = 'var names = [';

        $id1_array = get_array_from_resultset($global_do_conn->Execute($this->GET_GENE_ID));
      
        //echo "OK";
        foreach ($id1_array as $item) {
            $gene_id = $item["GeneID"];
            //echo $gene_id."<br/>";
            array_push($gene_id_array, $gene_id);
            $gene_name_array = $this->get_gene_name_by_id($gene_id);
            $gene_names_array = array_merge($gene_names_array, $gene_name_array);
        }  
        
        foreach ($gene_names_array as $gene_name) {
            $result_data .= '{k:"' . $gene_name . '"},';
            echo "K <br/>";
        }
        $result_data = rtrim($result_data, ",");
        $result_data .= '];';
        $file = fopen("./data/gene_names.js", "wb");
        fwrite($file, $result_data);
        fclose($file);
    }
    
    
    //获取可以预测到的基因
    function getPredictAutocompeleteData() {
        global $global_do_conn;
        $gene_id_array = array();
        $gene_names_array = array();
        $result_data = 'var predict_names = [';
        $id_array = get_array_from_resultset($global_do_conn->Execute($this->GET_PREDICT_GENE_ID));
      
        foreach ($id_array as $item) {
            $gene_id = $item["gene_id"];
            if (!in_array($gene_id, $gene_id_array)) {
                array_push($gene_id_array, $gene_id);
                $gene_name_array = $this->get_gene_name_by_id($gene_id);
                $gene_names_array = array_merge($gene_names_array, $gene_name_array);
            }
        }
        foreach ($gene_names_array as $gene_name) {
            $result_data .= '{k:"' . $gene_name . '"},';
        }
        $result_data = rtrim($result_data, ",");
        $result_data .= '];';
        $file = fopen("./data/predict_gene_names.js", "wb");
        fwrite($file, $result_data);
        fclose($file);
    }

    //从MINISET中获取对应的网络图
    function get_miniset_cytoscapeweb($genes) {
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $cw_node_data = '[';
        $cw_edge_data = '[';
        $nodes_array = array();
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_MINISET_MAPPING, array($gene_id)));
            if (is_null($miniset_mappings)) {
                continue;
            }
            $count = 0;
            foreach ($miniset_mappings as $item) {
                $count++;
                $doid = $item["DOID"];
                $term = $this->get_term_by_term_id($doid);
                $score = $item["score"];
                if (in_array($term, $nodes_array)) {
                    $cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"' . $score . '",pvalue:"0",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                } else {
                    array_push($nodes_array, $term);
                    $cw_node_data.=('{id:"' . $term . '",num:1,ngc:"r"},');
                    $cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"' . $score . '",pvalue:"0",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                }
            }
            $cw_node_data.= ('{id:"' . $gene_name . '",num:' . $count . ',ngc:"q"},');
        }

        $cw_node_data = substr($cw_node_data, 0, strlen($cw_node_data) - 1);
        $cw_edge_data = substr($cw_edge_data, 0, strlen($cw_edge_data) - 1);
        $result["cw_node_data"] = $cw_node_data . ']';
        $result["cw_edge_data"] = $cw_edge_data . ']';
        return $result;
    }

    //从DOLIST中获取对应的网络图
    function get_dolist_cytoscapeweb($genes) {
        global $global_do_conn;
        $gene_names = $this->remove_same_name(explode(" ", $genes));
        $cw_node_data = '[';
        $cw_edge_data = '[';
        $nodes_array = array();
        foreach ($gene_names as $gene_name) {
            $gene_id = $this->get_gene_id_by_name($gene_name);
            $miniset_mappings = get_array_from_resultset($global_do_conn->Execute($this->GET_DOLITE_MAPPING, array($gene_id)));
            $count = 0;
            foreach ($miniset_mappings as $item) {
                $count++;
                $doid = $item["DOID"];
                $term = $this->get_term_by_term_id($doid);
                $score = $item["score"];
                if (in_array($term, $nodes_array)) {
                    $cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"' . $score . '",pvalue:"0",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                } else {
                    array_push($nodes_array, $term);
                    $cw_node_data.=('{id:"' . $term . '",num:1,ngc:"r"},');
                    $cw_edge_data.=('{id:"' . $term . '_' . $gene_name . '",score:"' . $score . '",pvalue:"0",egc:"d2g",target:"' . $term . '",source:"' . $gene_name . '"},');
                }
            }
            $cw_node_data.= ('{id:"' . $gene_name . '",num:' . $count . ',ngc:"q"},');
        }

        $cw_node_data = substr($cw_node_data, 0, strlen($cw_node_data) - 1);
        $cw_edge_data = substr($cw_edge_data, 0, strlen($cw_edge_data) - 1);
        $result["cw_node_data"] = $cw_node_data . ']';
        $result["cw_edge_data"] = $cw_edge_data . ']';
        return $result;
    }

    //从GENE_RIF中获取网络图
    function get_generif_cytoscapeweb($genes) {
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
            foreach ($term_array as $term) {
                if (key_exists($term, $result_term_nodes)) {
                    $result_term_nodes[$term]++;
                } else {
                    $result_term_nodes[$term] = 1;
                }
            }
            arsort($result_term_nodes);
            foreach ($result_term_nodes as $key => $value) {
                if (in_array($key, $nodes_array)) {
                    $cw_edge_data .= ('{id:"' . $gene_name . '-' . $key . '", score: "0.5", pvalue:"0",egc: "g2d",target:"' . $gene_name . '",source:"' . $key . '"},');
                } else {
                    array_push($nodes_array, $key);
                    $cw_node_data .= ('{id:"' . $key . '", num:' . $value . ',ngc:"r"},');
                    $cw_edge_data .= ('{id:"' . $gene_name . '-' . $key . '", score: "0.5", pvalue:"0",egc: "g2d",target:"' . $gene_name . '",source:"' . $key . '"},');
                }
            }

            $cw_node_data .= ('{id:"' . $gene_name . '", num: 50 , ngc:"q"},');
        }
        $cw_node_data = substr($cw_node_data, 0, strlen($cw_node_data) - 1);
        $cw_edge_data = substr($cw_edge_data, 0, strlen($cw_edge_data) - 1);
        $result["cw_node_data"] = $cw_node_data . ']';
        $result["cw_edge_data"] = $cw_edge_data . ']';

        return $result;
    }

}

?>
