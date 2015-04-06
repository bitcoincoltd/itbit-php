<?php
###############################################
# 	itbit-php Class 1.0
# 	Author: David Barnes
# 	Contact: david.barnes (at) bitcoin.co.th
# 	Copyright (c) 2015 Bitcoin Co. Ltd.
# 	License: MIT
###############################################

class itbit{
	var $api_url = 'https://api.itbit.com/v1/';
	var $secret, $client, $user_id;
	function __construct($secret, $client, $user_id){
		$this->secret = $secret;
		$this->client = $client;
		$this->user_id = $user_id;
	}
	
	private function curl($url, $body = '', $type=''){
		$url = $this->api_url.$url;
		
		// Generate a nonce
		$mt = explode(' ', microtime());
		$nonce = $mt[1].substr($mt[0], 2, 6);
		$nonce = 1;
		
		// Use current timestamp
		$timestamp = time() * 1000;
		if($body != ''){
			$body = json_encode($body);
		}
		
		$signature = $this->sign_message(($type != '' ? $type : ($body == '' ? 'GET' : 'POST')),$url, $body, $nonce, $timestamp);
		
		$headers = array('Authorization: '.$this->client.':'.$signature,
						 'X-Auth-Timestamp: '.$timestamp,
						 'X-Auth-Nonce: '. $this->nformat($nonce),
						 'User-Agent: php-requester', 
						 'Connection: keep-alive',
						 'Accept-Encoding: gzip, deflate',
						 'Content-Type: application/json');
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, ($type != '' ? $type : ($body == '' ? 'GET' : 'POST')));
		if($body != ''){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		}
		
		$rawData = curl_exec($curl);
		$info = curl_getinfo ($curl);
		curl_close($curl);
		
		if($json = json_decode(trim($rawData))){
			return $json;
		}
		return trim($rawData);
	}
	
	private function sign_message($verb, $url, $body, $nonce, $timestamp){
		//Generate the message
		$message = stripslashes(json_encode(array($verb, $url, ($body == '' ? '' : addslashes($body)), (string)$nonce, (string)$timestamp)));
		// Hash the message plus nonce
		$nonced_message = $this->nformat($nonce) . $message;
		$hash_digest = hash('sha256',$nonced_message, true);
		$hmac_digest = hash_hmac('sha512', utf8_encode($url) . $hash_digest, utf8_encode($this->secret),true);
		$sig = base64_encode($hmac_digest);
		return $sig;
	}
	
	// Make sure the nonce doesn't get put into notation
	private function nformat($nonce){
		return number_format($nonce,0,'','');
	}
	
	// Below are the public methods that should be used to interact with the API
	
	public function wallet($wallet_id='', $currency = ''){
		return $this->curl('wallets'.($wallet_id != '' ? '/'.$wallet_id . ($currency != '' ? '/balances/'.$currency : '') : '?userId='.$this->user_id));
	}
	
	public function balance($wallet_id, $currency){
		return $this->wallet($wallet_id, $currency);
	}
	
	public function orders($wallet_id, $order_id=''){
		return $this->curl('wallets/'.$wallet_id.'/orders'.($order_id != '' ? '/'.$order_id : ''));
	}
	
	public function trades($wallet_id){
		return $this->curl('wallets/'.$wallet_id.'/trades');
	}
	
	public function cancel($wallet_id, $order_id){
		return $this->curl('wallets/'.$wallet_id.'/orders/'.$order_id,'','DELETE');
	}
	
	public function create_order($wallet_id, $order_type, $amount, $price){
		$order_data = array('side' => ($order_type == 'sell' ? 'sell' : 'buy'),
							'type' => 'limit',
							'currency' => 'XBT',
							'amount' => (string)number_format($amount,4,'.',''),
							'price' => (string)$price,
							'instrument' => 'XBTUSD');
							
		return $this->curl('wallets/'.$wallet_id.'/orders',$order_data,'POST');
	}
	
	public function withdraw($wallet_id, $amount, $address){
		$withdraw_data = array('currency' => 'XBT',
							   'amount' => (string)$amount,
							   'address' => $address);
							   
		return $this->curl('wallets/'.$wallet_id.'/cryptocurrency_withdrawals',$withdraw_data,'POST');
	}
	
	public function deposit($wallet_id){
		return $this->curl('wallets/'.$wallet_id.'/cryptocurrency_deposits',array('currency' => 'XBT'),'POST');
	}
}
