<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GeneAnnotation</title>
        <link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
        <link rel="stylesheet" type="text/css" href="css/index.css" />
    </head>
    <body>
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="index.php">Gene Annotation Inference</a>
                <ul class="nav">
                    <li class=""><a href="index.php">Home</a></li>
                    <li class=""><a href="page_tutorial.php">Tutorial</a></li>
                    <li class="active"><a href="page_download.php">Download</a></li>
                    <li class=""><a href="page_about.php">About</a></li>
                </ul>
            </div>
        </div>

        <div class="container">
            <p> Downloads</p>
            <form name="df1" action="./ajax_download.php" method="POST">
                <p>previous proven predictions 2005 --> 2009</p>
                <input type="hidden" name="type" value="p0509" />
                <input class="btn btn-primary" type="submit" value="Download"  />
            </form>
            <form name="df2" action="./ajax_download.php" method="POST">
                <p>previous proven predictions 2009 --> 2013</p>
                <input type="hidden" name="type" value="p0913" />
                <input class="btn btn-primary" type="submit" value="Download"  />
            </form>
            <form name="df3" action="./ajax_download.php" method="POST">
                <p>previous proven predictions 2005 --> 2013</p>
                <input type="hidden" name="type" value="p0513" />
                <input class="btn btn-primary" type="submit" value="Download"  />
            </form>
        </div>
        <p class="text-center">Copyright &copy;2015 HUST</p>
        <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" >
            
        </script>
    </body>
</html>
