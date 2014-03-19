<?php
/**
 * Description of Hue_Light
 *
 * @author paul
 */
class Hue_Bulb extends Hue_Light
{
	
	const ALERT_FLASH_ONCE = 'select';
	const ALERT_FLASH = 'lselect';
	
	private $base;
	
	private $id;
	private $name;
	
	
	/**
	 *
	 * @param Hue_Base $base
	 * @param uint $light_id 
	 */
	function __construct($base, $light_id)
	{
		$this->name = $base->getLightName($light_id);
		
		$this->id = $light_id;
		$this->base = $base;
		$this->update();	
	}
	
	public function update()
	{
		$raw_data = $this->base->getLightState($this->id);
		
		$this->name = $raw_data->name;
		
		$this->brightness = $raw_data->state->bri;
		$this->hue = $raw_data->state->hue;
		$this->sat = $raw_data->state->sat;
		$this->on = ($raw_data->state->on?true:false);
		$this->mode = $raw_data->state->colormode;
		$this->temp = $raw_data->state->ct;
		$this->x = $raw_data->state->xy[0];
		$this->y = $raw_data->state->xy[1];
		
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getBase()
	{
		return $this->base;
	}
	
	/**
	 *
	 * @param Hue_Light $light 
	 */
	public function setLight($light)
	{
		$this->setBrightness($light->getBrightness());
		$this->setON($light->isOn());
		if($light->getTransitionTime() !== FALSE)
			$this->setTransitionTime($light->getTransitionTime());
		
		switch($this->mode)
		{
			case self::MODE_CT:
				$this->setTemp($light->getTemp());
				break;
			case self::MODE_HUE:
				$this->setHue($light->getHue());
				$this->setSat($light->getSat());
				break;
			case self::MODE_XY:
				$this->setXY($light->getX(), $light->getY());
				break;
			default:
				throw new Exception('Internal Error');
				break;
		}
	}
	
	/**
	 *
	 * @return \Hue_Light
	 * @throws Exception 
	 */
	public function getLightCopy()
	{
		$l = new Hue_Light();
		
		$l->setBrightness($this->brightness);
		$l->setON($this->on);
		if($this->transitionTime !== FALSE)
			$l->setTransitionTime($this->transitionTime);
		
		switch($this->mode)
		{
			case self::MODE_CT:
				$l->setTemp($this->temp);
				break;
			case self::MODE_HUE:
				$l->setHue($this->hue);
				$l->setSat($this->sat);
				break;
			case self::MODE_XY:
				$l->setXY($this->x, $this->y);
				break;
			default:
				throw new Exception('Internal Error');
				break;
		}
		
		return $l;
	}
}