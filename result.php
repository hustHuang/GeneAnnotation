<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<?php
   //include './common.php';
   //include './class/Search.php';
   //$search = new Search();
   $type = $_REQUEST['type'];
   $genes = trim($_REQUEST['genes']);
   $genes = str_replace(","," ", $genes);
   $genenames = explode(" ", $genes);
   $count = count($genenames);
   /*
   foreach ($genenames as $value) {
       $id = $search->get_gene_id_by_name($value);
       $mapings = $search->get_mappings_by_gene_id($id);
       echo json_encode($mapings)."<br/>";
   }*/

   //$mapings = $search->get_miniset_cytoscapeweb($genes);
   //echo json_encode($mapings);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GeneAnnotation</title>
         <link rel="stylesheet" type="text/css" href="./css/layout-default-latest.css" />
        <link rel="stylesheet" type="text/css" href="./css/result.css" />
        <script type="text/javascript" >
           var TYPE = "<?php echo $type ?>";
           var GENES = "<?php echo $genes ?>";
           var COUNT = "<?php echo $count ?>";
        </script>
    </head>
    <body>
       <div id="main_container"></div>
       <div id="cytoscapeweb_container">
           <div id="tabs-north" class="ui-layout-north no-padding no-scrollbar"></div>
           <div id="tabs-west" class="ui-layout-west no-padding no-scrollbar"></div>
           <div id="tabs-center" class="ui-layout-center no-padding no-scrollbar"></div>
           <div id="tabs-east" class="ui-layout-east no-padding no-scrollbar"></div>
       </div>
      <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
      <script type="text/javascript" src="./js/jquery-ui-1.8.17.custom.min.js"></script> 
      <script type="text/javascript" src="./js/jquery.layout-latest.js"></script>
      <script type="text/javascript" src="./js/json2.min.js"></script>
      <script type="text/javascript" src="./js/AC_OETags.min.js"></script>
      <script type="text/javascript" src="./js/cytoscapeweb.min.js"></script>
      <script type="text/javascript" src="./js/createCytoscapeWeb.js"></script>
      <script type="text/javascript" src="./js/main.js"></script>
    </body>
</html>
