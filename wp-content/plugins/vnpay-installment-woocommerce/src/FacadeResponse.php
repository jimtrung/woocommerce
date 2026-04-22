<?php

/**
 * 
 */

namespace vnpayInst\Facades;

interface FacadeResponse {

    public function getResponseDescription($responseCode);

    public function checkResponse($txnResponseCode);

    public function ipn_url_vnpay($txnResponfseCode);

    public function getOrder($orderId);
}
