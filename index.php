<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GeneAnnotation</title>
        <link rel="stylesheet" type="text/css" href="css/index.css" />
    </head>
    <body>
        <?php
        // put your code here
        ?>
        
        
        <div id="main">
            <form id="form1" action ="result.php" method ="post">
                <p>Search for related terms:</p>
                <input id = "s_type" type="hidden"  name = "type" value="s"></input>
                <input id = "s_genes" type="input"  name = "genes" value=""></input>
                <input id="s_Sbt" type="submit" value="Search"></input>
            </form>
            
            <form id="form2" action ="result.php" method ="post">
                <p>Predict indirectly related terms:</p>
                <input id = "p_type" type="hidden"  name = "type" value="p"></input>
                <input type="input" id = "p_genes"  name = "genes" value=""></input>
                <input id="p_Sbt" type="submit" value="Predict"></input>
            </form>
        </div>
        
     <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
     
    </body>
</html>
