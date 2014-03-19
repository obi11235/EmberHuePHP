<?php
/**
 * Description of Hue_Base
 *
 * @author paul
 */
class Hue_Base
{
	private $ip;
	private $name;
	private $key;
	private $id;
	
	private $lights = array();
	
	function __construct($ip, $key)
	{
		$this->ip = $ip;
		$this->key = $key;
		
		$response = $this->_getData();
		Debug::print_r($response);
		
		$this->name = $response->config->name;
		
		$this->_updateLights($response);
	}
	
	protected function _getData($command = '')
	{
		$url = 'http://'.$this->ip.'/api/'.$this->key.'/'.$command;
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		if(!$response)
			throw new Exception('No Response from Hue');

		$response = json_decode($response);
		
		//TODO: Parse Errors
		
		return $response;
	}
	
	protected function _sendData($command, $payload)
	{
		$url = 'http://'.$this->ip.'/api/'.$this->key.'/'.$command;
		$payload = json_encode($payload);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PUT, true);

		curl_setopt($ch, CURLOPT_INFILE, fopen('data://text/plain,' . urlencode($payload), 'rb'));
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($payload));   

		$response = curl_exec($ch);
		if(!$response)
			throw new Exception('No Response from Hue');

		$response = json_decode($response);
		
		//TODO: Parse Errors
		
		return $response;
	}
	
	public function save()
	{
		//TODO: Save to database
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getIp()
	{
		return $this->ip;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function updateLights()
	{
		$this->_updateLights($this->_getData());
	}
	
	protected function _updateLights($data)
	{
		foreach($data->lights as $id=>$l)
			$this->lights[$id] = $l->name;
	}

	public function getLights()
	{
		return $this->lights;
	}
	
	public function getLightObj($light_id)
	{
		if(!isset($this->lights[$light_id]))
			throw new Exception('Invalid Light ID');
		
		return new Hue_Bulb($this, $light_id);
	}
	
	public function getLightName($light_id)
	{
		if(!isset($this->lights[$light_id]))
			throw new Exception('Invalid Light ID');
		
		return $this->lights[$light_id];
	}
	
	public function getLightState($light_id)
	{
		if(!isset($this->lights[$light_id]))
			throw new Exception('Invalid Light ID');
		
		$raw_light = $this->_getData('lights/'.$light_id);
		
		return $raw_light;
	}
	
	/**
	 *
	 * @param Hue_Light $light 
	 */
	public function sendLight($light)
	{
		$cmd = 'lights/'.$light->getId().'/state';

		$result = $this->_sendData($cmd, $light->getStatePayload());
		Debug::print_r($result);
	}

	public static function link($ip, $name)
	{
		$url = 'http://'.$ip.'/api';
		
		$hash = md5(rand(0,1000), $name);
		
		$data = array(
			'username' => $hash,
			'devicetype' => $name);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$response = curl_exec($ch);
		
		if(!$response)
			throw new Exception('No Response from Hue Basestation');
	
		$response = json_decode($response);
		
		//TODO: Parse Error
		
		$base = new Hue_Base($ip, $hash);
		$base->save();
		
		return $base;
		

	}
}