<?php


$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'vnpayInst\\Traits\\' => array($baseDir . '/src/traits'),
    'vnpayInst\\Shortcodes\\' => array($baseDir . '/src/shortcodes'),
    'vnpayInst\\InstGateway\\' => array($baseDir . '/src/InstGateway'),
    'vnpayInst\\' => array($baseDir . '/src'),
);
