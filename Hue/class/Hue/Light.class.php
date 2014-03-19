<?php
/**
 * Description of Hue_Light
 *
 * @author paul
 */
class Hue_Light
{
	
	const STATE_ON = TRUE;
	const STATE_OFF = FALSE;
	
	const MODE_HUE = 'hue';
	const MODE_XY = 'xy';
	const MODE_CT = 'ct';
	
	protected $mode;
	
	protected $on;
	protected $brightness;
	protected $hue;
	protected $sat;
	protected $x;
	protected $y;
	protected $temp;
	
	// Only set it to change it
	// Can not be queried
	protected $transitionTime = FALSE;
	
	
	
	/**
	 *
	 * @param Hue_Base $base
	 * @param uint $light_id 
	 */
	function __construct()
	{
		
	}
	
	public function getStatePayload()
	{
		
		$data = array(
			'on' => $this->on,
			);
		
		if($this->transitionTime !== FALSE)
			$data['transitiontime'] = $this->transitionTime;
		
		if($this->on)
		{
			$data['bri'] = $this->brightness;
			switch($this->mode)
			{
				case self::MODE_CT:
					$data['ct'] = $this->temp;
					break;
				case self::MODE_HUE:
					$data['hue'] = $this->hue;
					$data['sat'] = $this->sat;
					break;
				case self::MODE_XY:
					$data['xy'] = array($this->x, $this->y);
					break;
			}
		}
		
		return $data;
	}
	
	public function setON($value)
	{
		if(!is_bool($value))
			throw new Exception('Invalid State');
		$this->on = $value;
	}
	
	public function setBrightness($value)
	{
		if($value < 0 || $value > 254)
			throw new Exception('Invalid brighness, must be between 0 and 254');
		$this->brightness = $value;
	}
	
	public function setTemp($value)
	{
		if($value < 154 || $value > 500)
			throw new Exception('Invalid temp, must be between 154 and 500');
		$this->temp = $value;
		$this->setColorMode(self::MODE_CT);
	}
	
	public function setHue($value)
	{
		if($value < 0 || $value > 65535)
			throw new Exception('Invalid hue, must be between 0 and 65535');
		$this->hue = $value;
		$this->setColorMode(self::MODE_HUE);
	}
	
	public function setSat($value)
	{
		if($value < 0 || $value > 254)
			throw new Exception('Invalid saturation, must be between 0 and 254');
		$this->sat = $value;
		$this->setColorMode(self::MODE_HUE);
	}
	
	public function setTransitionTime($value)
	{
		if(!is_int($value) || $value < 0)
			throw new Exception('Invalid transition time, must be positive integer');
		$this->transitionTime = $value;
	}
	
	public function clearTransitionTime()
	{
		$this->transitionTime = false;
	}
	
	public function setXY($x, $y)
	{
		if($x < 0 || $x > 1 || $y < 0 || $y > 1)
			throw new Exception('Invalid xy, must be between 0 and 1');
		$this->x = $x;
		$this->y = $y;
		$this->setColorMode(self::MODE_XY);
	}
	
	public function colorMode($mode)
	{
		if(!in_array($mode, array(self::MODE_CT, self::MODE_HUE, self::MODE_XY)))
			throw new Exception('Invalid Color Mode');
		
		$this->mode = $mode;
	}

	public function getMode()
	{
		return $this->mode;
	}
	
	public function getX()
	{
		if(!$this->mode == self::MODE_XY)
			throw Exception('Not Active Mode');
		
		return $this->x;
	}
	
	public function getY()
	{
		if(!$this->mode == self::MODE_XY)
			throw Exception('Not Active Mode');
		
		return $this->y;
	}
	
	public function getHue()
	{
		if(!$this->mode == self::MODE_HUE)
			throw Exception('Not Active Mode');
		
		return $this->hue;
	}
	
	public function getSat()
	{
		if(!$this->mode == self::MODE_HUE)
			throw Exception('Not Active Mode');
		
		return $this->sat;
	}
	
	public function getBrightness()
	{
		return $this->brightness;
	}
	
	public function getTemp()
	{
		if(!$this->mode == self::MODE_CT)
			throw Exception('Not Active Mode');
		
		return $this->temp;
	}
	
	public function getTransitionTime()
	{
		return $this->transitionTime;
	}
	
	public function isOn()
	{
		return $this->on;
	}
}