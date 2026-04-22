<?php // 
/**
 * 
 * 
 */

namespace vnpayInst\InstGateway;

use vnpayInst\Responses\vnpayResponse;
use vnpayInst\InstGateway\vnpayInstGateway;

class vnpayInternationalResponse extends vnpayResponse {

    public function __construct() {
        parent::__construct();
    }

    public function getResponseDescription($responseCode) {
        if ($_GET['vnp_ResponseCode'] == '00') {
            $result= "Giao dịch thanh toán thành công qua VNPAY";
        } else {
            $result= "Giao dịch không thành công";
        }
        
        return $result;
    }

  
    public function thankyou() {
        $gateway = new vnpayInstGateway;
        return $gateway->get_option('receipt_return_url');
    }

   

}
