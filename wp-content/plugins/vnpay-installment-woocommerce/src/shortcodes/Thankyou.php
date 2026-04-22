<?php
/**
 * 
 * 

 */

namespace vnpayInst\Shortcodes;

class Thankyou
{
	public function __construct()
	{
		add_shortcode('vnpay_thankyou', array($this, 'callback'));
	}


	public function callback($atts)
	{
        echo $content;
	}

}