<?php


$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'vnpayInst\\Facades\\FacadeResponse' => $baseDir . '/src/FacadeResponse.php',
    'vnpayInst\\InstGateway\\vnpayInstGateway' => $baseDir . '/src/InstGateway/international/vnpayInstGateway.php',
    'vnpayInst\\InstGateway\\vnpayInternationalResponse' => $baseDir . '/src/InstGateway/international/vnpayInternationalResponse.php',
    'vnpayInst\\Page' => $baseDir . '/src/Page.php',
    'vnpayInst\\Responses\\vnpayResponse' => $baseDir . '/src/vnpayResponse.php',
    'vnpayInst\\Shortcodes\\Thankyou' => $baseDir . '/src/shortcodes/Thankyou.php',
    'vnpayInst\\Traits\\Pages' => $baseDir . '/src/traits/Pages.php',
);

