<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');
echo "Currency: " . get_woocommerce_currency() . "\n";
?>
