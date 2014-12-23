<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="./js/jquery-1.7.2.min.js" ></script>
        <script type="text/javascript" src="./js/json2.min.js"></script>
        <script type="text/javascript" src="./js/AC_OETags.min.js"></script>
        <script type="text/javascript" src="./js/cytoscapeweb.min.js"></script>
        <style>
            #cytoscapeWeb{
                width: 800px;
                height: 600px;
                border: 1px solid #CCC;
            }
        </style>
    </head>
    <body>
        <?php
        // put your code here
        ?>

        <div id="cytoscapeWeb"></div>
        <script type="text/javascript" src="./js/createCytoscapeWeb.js"></script>
        <script type="text/javascript">
            $(function(){
                $.ajax({
                    type:'POST',
                    url:'./js/cytoscape.json',
                    dataType: 'JSON',
                    async: false,
                    data: {},
                    async: false,
                    success: function(data){
                       makeCytoscapeWebView('cytoscapeWeb', data.cw_node_data, data.cw_edge_data);  
                    },
                    error:function(a,b,c){
                        alert(a);
                        alert(b);
                        alert(c);
                    }
                });
            });
        </script>
    </body>
</html>
