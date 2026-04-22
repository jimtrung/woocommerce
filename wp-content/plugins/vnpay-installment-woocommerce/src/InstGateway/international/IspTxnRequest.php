<?php

namespace vnpayInst\InstGateway;

public class IspTxnRequest
{
  public $reqId;
  public Order $order;
  public Txn $txn;
  public CustomerInfo $customerInfo;
  public $version;
  public $secureHash;
  public $secureType;
  public $locale;
  public $addData;
  public $ipAddr;
  public $userAgent;

  public function getReqId()
  {
    return $this->reqId;
  }
}

public class Order
{
    public $orderReference;
    public $orderInfo;
}

public class Txn
{
    public $tmnCode;
    public $issuerCode;
    public $scheme;
    public $amount;
    public $totalIspAmount;
    public $recurringAmount;
    public $recurringFrequency;
    public $recurringNumberOfIsp;
    public $currCode;
    public $returnUrl;
    public $cancelUrl;
    public $mcDate;
}

public class CustomerInfo
{
    public $identityCode;
    public $forename;
    public $surname;
    public $mobile;
    public $email;
    public $address;
    public $city;
    public $country;
}

?>