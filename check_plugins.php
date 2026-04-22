<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');
$active_plugins = get_option('active_plugins');
print_r($active_plugins);
?>
