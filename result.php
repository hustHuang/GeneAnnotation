
<?php

// GET THE PARAMETERS FROM INDEX
$type = $_REQUEST['type'];
$genes = trim($_REQUEST['genes']);
$genes = str_replace(",", " ", $genes);
$genenames = explode(" ", $genes);
$count = count($genenames);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GeneAnnotation</title>
        <link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="./css/index.css" />
        <link rel="stylesheet" type="text/css" href="./css/result.css" />
        <script type="text/javascript" >
            var TYPE = "<?php echo $type ?>";
            var GENES = "<?php echo $genes ?>";
            var COUNT = "<?php echo $count ?>";
        </script>
    </head>
    <body>
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="index.php">Gene Annotation Inference</a>
                <ul class="nav">
                    <li class=""><a href="index.php">Home</a></li>
                    <li class=""><a href="page_tutorial.php">Tutorial</a></li>
                    <li class=""><a href="page_download.php">Download</a></li>
                    <li class=""><a href="page_about.php">About</a></li>
                </ul>
            </div>
        </div>
        <div id="main_container">
            <ul class="nav nav-tabs" id="pageTab">
                <li><a href="#dl" data-toggle="tab">GDOList</a></li>
                <li><a href="#ms" data-toggle="tab">MiniGDOList</a></li>
                <li><a href="#nw" data-toggle="tab">Network</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="dl"><div class="loader"></div></div>
                <div class="tab-pane" id="ms"><div class="loader"></div></div>
                <div class="tab-pane" id="nw"><div class="loader"></div></div>
            </div>
        </div>

        <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="./js/underscore-min.js"></script>
        <script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="./js/json2.min.js"></script>
        <script type="text/javascript" src="./js/AC_OETags.min.js"></script>
        <script type="text/javascript" src="./js/cytoscapeweb.min.js"></script>
        
        <script type="text/javascript" src="./js/createCytoscapeWeb.js"></script>
        <script type="text/javascript" src="./js/main.js"></script>
    </body>
</html>
