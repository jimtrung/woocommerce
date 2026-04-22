<?php
/**
 * 
 */

namespace vnpayInst\Traits;

trait Pages
{
	protected $pages = array(
		'thank-you' => array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => 'Payment Success',
			'post_content' => ''
		)
	);
}