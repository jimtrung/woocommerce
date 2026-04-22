<?php

namespace vnpayInst\InstGateway;
class vnpayInstGateway extends \WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'vnpaytg';
        $this->icon = $this->get_option('logo');
        $this->has_fields = false;
        $this->method_title = __('vnpayInst', 'woocommerce');
        $this->supports           = array(
			'products',
			'refunds',
			'pre-orders',
		);
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->vnp_Url = $this->get_option('vnp_Url');
        $this->vnp_TmnCode = $this->get_option('vnp_TmnCode');
        $this->clientId = $this->get_option('clientId');
        $this->secretkey = $this->get_option('secretkey');
        
        $this->vnp_username = $this->get_option('vnp_username');
        $this->vnp_password = $this->get_option('vnp_password');
        $this->vnp_clientSecret = $this->get_option('vnp_clientSecret');
        $this->locale = $this->get_option('locale');

        if (!$this->isValidCurrency()) {
            $this->enabled = 'no';
        }

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
        add_action( 'woocommerce_checkout_process', 'redirect');

    }
    
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable vnpayInst Paygate', 'woocommerce'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => 'Tiêu đề thanh toán',
                'default' => 'Thanh toán trả góp qua VNPAY',
                'desc_tip' => true
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Mô tả phương thức thanh toán', 'woocommerce'),
                'default' => __('Thanh toán trả góp trực tuyến qua VNPAY', 'woocommerce'),
                'desc_tip' => true
            ),
            'vnp_Url' => array(
                'title' => __('VNPAY URL', 'woocommerce'),
                'type' => 'text',
                'description' => 'API URL',
                'default' => '',
                'desc_tip' => true
            ),
            'clientId' => array(
                'title' => __('client Id', 'woocommerce'),
                'type' => 'text',
                'description' => 'Mã merchant ID VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'vnp_TmnCode' => array(
                'title' => __('Terminal ID', 'woocommerce'),
                'type' => 'text',
                'description' => 'Mã terminal ID VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'secretkey' => array(
                'title' => __('Secret Key', 'woocommerce'),
                'type' => 'password',
                'description' => 'Mã bảo mật tạo/kiểm tra checksum VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'vnp_username' => array(
                'title' => __('User namme', 'woocommerce'),
                'type' => 'text',
                'description' => 'User VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'vnp_password' => array(
                'title' => __('Password', 'woocommerce'),
                'type' => 'password',
                'description' => 'Password VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'vnp_clientSecret' => array(
                'title' => __('Client Secret', 'woocommerce'),
                'type' => 'text',
                'description' => 'Client Secret VNPAY cung cấp',
                'default' => '',
                'desc_tip' => true
            ),
            'locale' => array(
                'title' => __('Locale', 'woocommerce'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('Choose your locale', 'woocommerce'),
                'desc_tip' => true,
                'default' => 'vn',
                'options' => array(
                    'vn' => 'vn',
                    'en' => 'en'
                )
            ),
        );
    }

    public function payment_scripts() {
        wp_enqueue_script( 'paymentscripts_js', plugins_url( 'paymentscripts.js', __FILE__ ) );
	wp_register_script( 'woocommerce_vnpay', plugins_url( 'paymentscripts.js', __FILE__ ), array( 'jquery', 'paymentscripts_js' ) );
	wp_enqueue_script( 'woocommerce_vnpay' );
    }
    
    public function process_payment($order_id) {
        $order = new \WC_Order($order_id);
        return array(
            'result' => 'success',
            'redirect' => $this->redirect($order_id)
        );
    }
    
    public function redirect($order_id) {
        $order = new \WC_Order($order_id);
        $order->update_status('on-hold');
        $order->add_order_note(__('Giao dịch chờ thanh toán hoặc chưa hoàn tất', 'woocommerce'));
        
            $amount = number_format($order->order_total, 2, '.', '') * 100;
            $apiUrl = $this->vnp_Url;
            $vnp_Returnurl = admin_url('admin-ajax.php') . '?action=payment_response_return&type=international';
            $vnp_TmnCode = $this->vnp_TmnCode;
            $SecretKey = $this->secretkey;

            $vnp_Locale = $this->locale;
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            $guid = date("YmdHis");

            //convert vi to en: Bill
            $forenamefw = $order->get_billing_first_name();
            $forename = $this->convert_vi_to_en($forenamefw);
            $surnamefw = $order->get_billing_last_name();
            $surname = $this->convert_vi_to_en($surnamefw);
            $mobile = $order->get_billing_phone();
            $emailfw = $order->get_billing_email();
            $email = $this->convert_vi_to_en($emailfw);
            $addressfw = $order->get_billing_address_1();
            $address = $this->convert_vi_to_en($addressfw);
            $cityfw = $order->get_billing_city();
            $city = $this->convert_vi_to_en($cityfw);
            $countryfw = $order->get_billing_country();
            $country = $this->convert_vi_to_en($countryfw);

            $authenRequest = array(
                'clientId' => $this->clientId,
                'username' => $this->vnp_username,
                'password' => $this->vnp_password,
                'clientSecret' => $this->vnp_clientSecret
            );
            $authen = $this->callAPI("POST", $apiUrl."/oauth/authen", json_encode($authenRequest));
            $data = json_decode($authen, true);
            if($data['rspCode']=="00"){
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ispTxnRequest = array(
                "reqId" => $guid,
                "tmnCode"=>$vnp_TmnCode,
                "order" => array
                (
                    "orderReference" => $order_id,
                    "orderInfo" => $order_id
                ),
                "transaction" => array
                (
                    "scheme" => "",
                    "recurringFrequency" => "monthly", //09
                    "recurringAmount" => 0,
                    "amount" => $amount, //07
                    "recurringNumberOfIsp" => 0,
                    "totalIspAmount" => $amount,
                    "currCode" => "VND", //08
                    "returnUrl" => $vnp_Returnurl,
                    "cancelUrl" => $vnp_Returnurl,
                    "mcDate" => date('YmdHis')
                ),
                "customerInfo" => array
                (
                    "forename" => $forename,
                    "surname" => $surname,
                    "mobile" => $mobile,
                    "email" => $email,
                    "address" => $address,
                    "city" => $city,
                    "country" => $country
                ),
                "version" => "2.1.0",
                "locale" => $vnp_Locale,
                "ipAddr"=> $vnp_IpAddr,
                "userAgent"=> $userAgent

            );
            $format = '%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s';
            $dataHash = sprintf($format, 
                    $ispTxnRequest['reqId'], //1
                    $ispTxnRequest['order']['orderReference'], //2
                    $ispTxnRequest['order']['orderInfo'], //3
                    $ispTxnRequest['tmnCode'], //4
                    '',				//5
                    $ispTxnRequest['transaction']['scheme'], //6
                    $ispTxnRequest['transaction']['recurringAmount'], //7
                    $ispTxnRequest['transaction']['recurringFrequency'], //8
                    $ispTxnRequest['transaction']['recurringNumberOfIsp'], //9
                    $ispTxnRequest['transaction']['amount'], //10
                    $ispTxnRequest['transaction']['totalIspAmount'],  //11
                    $ispTxnRequest['transaction']['currCode'],  //12
                    '', //13
                    '', //14
                    $ispTxnRequest['customerInfo']['forename'], //15
                    $ispTxnRequest['customerInfo']['surname'], //16
                    $ispTxnRequest['customerInfo']['mobile'], //17
                    $ispTxnRequest['customerInfo']['email'], //18
                    $ispTxnRequest['customerInfo']['address'], //19
                    $ispTxnRequest['customerInfo']['city'], //20
                    $ispTxnRequest['customerInfo']['country'], //21
                    $ispTxnRequest['ipAddr'], //22
                    $ispTxnRequest['userAgent'], //23
                    $ispTxnRequest['transaction']['returnUrl'], //24
                    $ispTxnRequest['transaction']['cancelUrl'], //25
                    $ispTxnRequest['version'], //26
                    $ispTxnRequest['locale'], //27
                    $ispTxnRequest['transaction']['mcDate']); //28
            $checksum = hash_hmac('SHA512', $dataHash, $SecretKey);
            
            $ispTxnRequest["secureHash"] = $checksum;
            $txnData = $this->callAPI_auth("POST", $apiUrl."/payment/init", json_encode($ispTxnRequest), $data["data"]["Bearer"]." ".$data["data"]["accessToken"]);
            $ispTxn = json_decode($txnData, true);

           if($ispTxn['rspCode'] == "00"){
               echo 
                    '
                    <form id="payment_form" action="'.$apiUrl.'/payment/pay" method="post">
                        <center><input class="button" id="submit_vnpayment_form" value="Thanh toán">
                        <input name="ispTxnId" value="'.$ispTxn['transaction']['id'].'" type="text"/>
                        <input name="dataKey" value="'.$ispTxn['dataKey'].'" type="text"/>
                        <input name="tmnCode" value="'.$vnp_TmnCode.'" type="text"/>
                        <script>
                        document.getElementById("payment_form").submit();
                        </script>
                    </form>
                    ';
                }
                    else if($ispTxn['rspCode'] == "07"){
                        wc_print_notice( 
                        sprintf( 'Số tiền thanh toán không hợp lệ, Số tiền thanh toán trả góp nhỏ hơn 3,000,000 VND' . ' -Mã lỗi:'.$ispTxn['rspCode'] . ' -Mô tả mã lỗi:'.$ispTxn['rspMsg'] .' -Mã giao dịch:' .$order_id
                        ), 'error' 
                        );
                        
                        echo "<div><a style=\"color: blue\" href=" . get_site_url() . ">Trở lại Website</a></div>";

                }
                    else{
                        wc_print_notice( 
                        sprintf( 'Có lỗi trong quá trình xử lý. Thanh toán thất bại. Vui lòng liên hệ quản trị website để được hỗ trợ hoặc vui lòng thử lại sau .' . ' -Mã lỗi:'.$ispTxn['rspCode'] .' -Mô tả mã lỗi:'.$ispTxn['rspMsg'].' -Mã giao dịch:' .$order_id
                        ), 'error' 
                        );
                        echo "<div><a style=\"color: Red\">Khách hàng lưu ý: Nhập đúng định dạng các thông tin Billing</a></div>";
                        echo "<div><a style=\"color: Red\">Ví dụ: 0123456789 .Số điện thoại nhập không áp dụng +(84) 1234 56789 hoặc +84123456789.</a></div>";
                        echo "<div><a style=\"color: blue\" href=" . get_site_url() . ">Trở lại Website</a></div>";
                    }
                }
                else{
                    wc_print_notice( 
                        sprintf( 'Có lỗi trong quá trình xử lý. Xác thực thất bại. Vui lòng liên hệ quản trị website để được hỗ trợ hoặc vui lòng thử lại sau' . ' -Mã lỗi:'.$ispTxn['rspCode'] .' -Mô tả mã lỗi:'.$ispTxn['rspMsg'].' -Mã giao dịch:' .$order_id
                        ), 'error' 
                        );
                        
                        echo "<div><a style=\"color: blue\" href=" . get_site_url() . ">Trở lại Website</a></div>";
                }
    }
    //convert vi to en
     function convert_vi_to_en($str) {
            $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
            $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
            $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
            $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
            $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
            $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
            $str = preg_replace("/(đ)/", 'd', $str);    
            $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
            $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
            $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
            $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
            $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
            $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
            $str = preg_replace("/(Đ)/", 'D', $str);
            return $str;
         }
         
	public function callAPI($method, $url, $data){
		   $curl = curl_init();
		   switch ($method){
			  case "POST":
				 curl_setopt($curl, CURLOPT_POST, 1);
				 if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				 break;
			  case "PUT":
				 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				 if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
				 break;
			  default:
				 if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		   }
		   // OPTIONS:
		   curl_setopt($curl, CURLOPT_URL, $url);
		   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			  'Content-Type: application/json',
		   ));
		   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		   // EXECUTE:
		   $result = curl_exec($curl);
		   if(!$result){die("Connection Failure");}
		   curl_close($curl);
		   return $result;
		}
		
    public function callAPI_auth($method, $url, $data, $accessToken){
	$curl = curl_init();
	switch ($method){
            case "POST":
		curl_setopt($curl, CURLOPT_POST, 1);
		if ($data)
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		break;
		case "PUT":
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		if ($data)
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
		break;
		default:
		if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
		   }
		   // OPTIONS:
		curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			  'Content-Type: application/json',
			   'Authorization: '.$accessToken
		   ));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		   // EXECUTE:
		$result = curl_exec($curl);
		if(!$result){die("Connection Failure");}
		curl_close($curl);
		return $result;
    }
    
    public function isValidCurrency() {
        return in_array(get_woocommerce_currency(), array('VND'));
    }

    public function admin_options() {
        if ($this->isValidCurrency()) {
            parent::admin_options();
        } else {
            ?>
            <div class="inline error">
                <p>
                    <strong>
            <?php _e('Gateway Disabled', 'woocommerce'); ?>
                    </strong> : 
            <?php
            _e('vnpay does not support your store currency. Currently, vnpay only supports VND currency.', 'woocommerce');
            ?>
                </p>
            </div>
                        <?php
                    }
                }

            }