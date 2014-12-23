<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GeneAnnotation</title>
        <!--[if IE]>
            <script type="text/javascript">window.location.href="browser.php";</script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
        <link rel="stylesheet" type="text/css" href="css/index.css" />
    </head>
    <body>
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="index.php">Gene Annotation Inference</a>
                <ul class="nav">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li class=""><a href="page_tutorial.php">Tutorial</a></li>
                    <li class=""><a href="page_download.php">Download</a></li>
                    <li class=""><a href="page_about.php">About</a></li>
                </ul>
            </div>
        </div>

        <div class="container">
            <h2 class="text-center">Gene Annotation Inference</h2>
            <div id="box">
                <form id="form1" class="form-search text-center" action ="result.php" method ="post">
                    <input id = "s_type"  type="hidden"  name = "type" value="s"/>
                    <i class="icon-search"></i> 
                    <input id = "s_genes" type="text" class="input-medium span4"  name = "genes"  placeholder="input the genes,use blank as separator" />
                    <input id="s_Sbt" class="btn btn-primary"  type="submit"  value="Search" />
                </form>
            </div>

        </div>
        <p class="text-center" style="font-family:arial;">Copyright &copy;2015 HUST</p>
        <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="./js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="./js/gene_names.js"></script>
        <script type="text/javascript" src="./js/predict_gene_names.js"></script>
        <script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" >
            $('#s_genes').autocomplete(names, {
                matchContains: true,
                multiple: true,
                multipleSeparator: " ",
                max: 100,
                formatItem: function(row, i, max) {
                    return row.k;
                },
                formatMatch: function(row, i, max) {
                    return row.k;
                },
                formatResult: function(row) {
                    return row.k ;
                }
            });
            $('#p_genes').autocomplete(predict_names, {
                matchContains: true,
                multiple: true,
                multipleSeparator: " ",
                max: 100,
                formatItem: function(row, i, max) {
                    return row.k;
                },
                formatMatch: function(row, i, max) {
                    return row.k;
                },
                formatResult: function(row) {
                    return row.k ;
                }
            });
        </script>
    </body>
</html>
