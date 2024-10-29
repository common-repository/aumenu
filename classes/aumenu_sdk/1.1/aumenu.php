<?php
/**
* AuMenu SDK
* https://www.aumenu.info
* Description: Display your taps and beers from AuMenu on your website!
* Version: 1.0
* Author: Hyper
* Author URI: http://www.atelierhyper.com
* License: AuMenu 2016
*/

	
/************************************************************************/
//
//	require_once('aumenu_sdk/1.0/aumenu.php');
//	
//	$aumenu = new AuMenuSDK();
//	$aumenu->setLanguage('fr'); // default value is english ('en')
//	$aumenu->getToken('rVYClAmb6vMr8lQWyJhdQ1WgyhSfGP', 's7KrJ626i8B4AJpqFYaXb1Y8hxq9M6YfdJGXQI38REcsoBT4A32M41UlykukqtzEdgB3ZFcRkojJ0cNA');
//	print_r($aumenu->getUser());
//  print_r($aumenu->getEstablishment('my-establishment-or-brewery-name'));
//  print_r($aumenu->getBeer('my-beer-name', 'my-brewery-name'));
//  print_r($aumenu->getBeers('my-brewery-name'));
//  print_r($aumenu->getTaps('my-establishment-name'));
//  print_r($aumenu->getFoods('my-establishment-name'));
/************************************************************************/


class AuMenuSDK {
	public $url = 'https://www.aumenu.info/webservice.php?v=1.1';
	private $public_key = '';
	private $secret_key = '';
	private $grant_type = 'client_credentials';
	
	private $access_token = '';
	
	private $language = 'en';
	
	public function __construct($url=null) {
		if ($url) {
			$this->url = $url;
		}
	}
	
	public function getToken($public_key, $secret_key, $grant_type = 'client_credentials') {
		$return = null;
		
		$this->public_key = $public_key;
		$this->secret_key = $secret_key;
		$this->grant_type = $grant_type;
		
		$data = array(
		    'grant_type' => $this->grant_type,
		    'client_id' => $this->public_key,
		    'client_secret' => $this->secret_key
		);
		
		$response = json_decode($this->callPost($data));
		if (isset($response->error)) {
			$return['status'] = false;
			$return['code'] = $response->error;
			$return['message'] = $response->error_description;
		} else if (isset($response->access_token)) {
			$this->access_token = $response->access_token;
			$return['status'] = true;
		}
		
		return $return;
	}
	
	public function setLanguage($lang) {
		$this->language = $lang;
	}
	
	public function getUser() {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'user',
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getEstablishment($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'establishment',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getBeer($id, $establishment) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'beer',
		    'id' => $id,
		    'establishment' => $establishment,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getBeers($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'beers',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	public function getBeersSections($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'beers_sections',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getTaps($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'taps',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	public function getTapsSections($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'taps_sections',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getBottles($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'bottles',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	public function getBottlesSections($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'bottles_sections',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getFoods($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'foods',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	public function getFoodsSections($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'foods_sections',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getGlassware($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'glassware',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getBarrel($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'barrel',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	public function getType($id) {
		$return = null;
		
		$data = array(
		    'access_token' => $this->access_token,
		    'a' => 'type',
		    'id' => $id,
		    'lang' => $this->language
		);
		
		return $this->callPost($data);
	}
	
	
	
	private function callPost($data) {
		$ch = curl_init($this->url);
		$jsonDataEncoded = http_build_query($data);
		
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($jsonDataEncoded)));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec($ch);
		
		return $result;
	}
}
?>