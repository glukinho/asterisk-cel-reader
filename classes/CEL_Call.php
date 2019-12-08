<?php

class CEL_Call
{
	protected $linkedid;
	protected $events;
	protected $exists = null;
	protected $start;
	
	protected $db;
	protected $table_name;

	
	public function __construct($linkedid, $db_options, $table_name = 'cel')
	{
		$this->linkedid = $linkedid;
		$this->db = new SafeMySql($db_options);
		$this->table_name = $table_name;
		
		$this->events = new CEL_Events_Collection();
	}
	
	public function getEvents()
	{
		return $this->events;
	}
	

	public function load()
	{
		$sql = "SELECT * FROM `{$this->table_name}` WHERE `linkedid`='{$this->linkedid}'";
		$result = $this->db->getAll($sql);

		$this->exists = (count($result) > 0);
		
		foreach ($result as $r) {
			$this->addEvent($r);
		}
	}
	
	protected function addEvent($event_arr)
	{
		$event = new CEL_Event($event_arr);
		$this->events->add($event);
		// $this->events[] = $event;
	}
	

	public function exists()
	{
		return $this->exists;
	}
	
	public function getStartTimestamp()
	{
		return new DateTime($this->getEvents()->first()->get('eventtime'));
	}

}


class CEL_Exception extends Exception { }
