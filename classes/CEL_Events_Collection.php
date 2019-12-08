<?php

class CEL_Events_Collection
{
	protected $events = array();
	
	public function add(CEL_Event $event)
	{
		$this->events[] = $event;
	}
	
	public function all()
	{
		return $this->events;
	}
	
	public function first()
	{
		return $this->events[0];
	}
	
	public function second()
	{
		return ( isset($this->events[1]) ? $this->events[1] : null );
	}
	
	public function last()
	{
		return end($this->events);
	}
	
	public function count()
	{
		return count($this->events);
	}
	
	public function filter($key, $value, $operation = 'equal')
	{
		$result = new CEL_Events_Collection();
		
		// $key = str_replace('.', '->', $key);
		
		foreach ($this->events as $e) {
			if (call_user_func($operation, $e->{$key}, $value)) $result->add($e);
		}
		
		return $result;
	}
	
	public function has($value, $key = 'eventtype')
	{
		foreach ($this->events as $e) {
			if ($e->{$key} == $value) return true;
		}
		return false;
	}
}









function equal($a, $b) {
	foreach ( (array) $b as $element) {
		if ($a == $element) return true;
	}
	
	return false;
}

function begins($fulltext, $parts) {
	foreach ( (array) $parts as $part ) {
		if (substr($fulltext, 0, strlen($part)) == $part) return true;
	}
	return false;
}


