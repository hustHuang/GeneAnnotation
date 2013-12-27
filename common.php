<?php

/**
 * @author Lin Zhifeng
 * 2013-4-18 11:41:35
 */

/* Define absolute root of the project. */
define('ABSPATH', dirname(__FILE__) . '/');

//$context_path = explode('/', $_SERVER['REQUEST_URI']);
//define('SITEURI', 'HTTP://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $context_path[1]);
define('SITEURI', 'http://' . $_SERVER['HTTP_HOST'].'/GeneAnnotation');

/* Load configuration of the project. */
if (file_exists(ABSPATH . 'config/cfg.php')){
	require_once( ABSPATH . 'config/cfg.php' );
} else {
    die("Cannot load configuration file './config/cfg.php'");
}

require_once( ABSPATH . 'util/functions.php' );

/**  */
set_timezone();

/**  */
require_icg_conn();
//end of script