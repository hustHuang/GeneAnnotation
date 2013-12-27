<?php
define('DB_DRIVER', 'mysql');
define('DB_NAME', 'gene_annotation');
define('DB_USER','root');
define('DB_PASSWORD', 'elwg324');
define('DB_HOST', '115.156.216.95');
define('DB_CHARSET', 'utf8');

//define('TIMEZONE', 'Asia/Chongqing');

define('STRING_SEPARATOR', ',');
if (defined('ICG_DEBUG'))
    define('DB_DEBUG', true);
else
    define('DB_DEBUG', false);
// End of script