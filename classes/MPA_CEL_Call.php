<?php

class MPA_CEL_Call extends CEL_Call
{
	protected $external_peers = [ 'SIP/mastertel', 'SIP/beeline', 'SIP/telphin' ];
	protected $workdays = '12345'; // mon-fri
	
	protected $schedule = [
		'Mon' => [
			'from' => '09:30', 'to' => '17:59'
		],
		'Tue' => [
			'from' => '09:30', 'to' => '17:59'
		],
		'Wed' => [
			'from' => '09:30', 'to' => '17:59'
		],
		'Thu' => [
			'from' => '09:30', 'to' => '17:59'
		],
		'Fri' => [
			'from' => '09:30', 'to' => '17:59'
		],
	];
	
	
	public function getDirection()
	{
		$first_channel 	= $this->getEvents()->filter('eventtype', 'CHAN_START')->first()->get('channame');
		if (begins($first_channel, $this->external_peers)) return 'INCOMING';
		
		$second_channel = $this->getEvents()->filter('eventtype', 'CHAN_START')->second();
		if (!is_null($second_channel)) {
			$second_channel = $second_channel->get('channame');
		} else {
			return 'UNKNOWN';
		}
		
		if (begins($second_channel, $this->external_peers)) return 'OUTGOING';
		
		return 'UNKNOWN';		
	}
	
	public function isAnswered()
	{
		return ($this->getEvents()->has('BRIDGE_ENTER'));
	}
	
	public function isWorkTime()
	{
		$start = $this->getStartTimestamp();
		
		$start_weekday = $start->format('D');
		// echo $start_weekday . PHP_EOL;
		
		$start_time = $start->format('H:i');
		// echo $start_time . PHP_EOL;
		
		$result = false;
		
		if (!array_key_exists($start_weekday, $this->schedule)) return $result;
		
		if ( $start_time >= $this->schedule[$start_weekday]['from'] && $start_time <= $this->schedule[$start_weekday]['to'] ) {
			$result = true;		
		}
		
		return $result;
	}
	
	public function getTimeBeforeHumanAnswer()
	{
		$start = $this->getStartTimestamp();
		
		if ($this->isAnswered()) {
			$answer = new DateTime($this->getEvents()->filter('eventtype', 'BRIDGE_ENTER')->first()->get('eventtime'));
			$result = $answer->getTimestamp() - $start->getTimestamp();
		} else {
			$hangup = new DateTime($this->getEvents()->last()->get('eventtime'));
			$result = $hangup->getTimestamp() - $start->getTimestamp();
		}
	
		return $result;
	}
	
	public function getSourcePeer()
	{
		$channame = $this->getEvents()->filter('eventtype', 'CHAN_START')->first()->get('channame');
		$channame_arr = multiexplode([ '/', '-', ';' ], $channame);
		return ($channame_arr[0] . '/' . $channame_arr[1]);
	}
	
	public function getBridgedPeer()
	{
		$result = '';
		
		$bridges = $this->getEvents()->filter('eventtype', 'BRIDGE_ENTER')->filter('channame', 'SIP/', 'begins')->all();
		
		// print_r($bridges);
		
		foreach ($bridges as $b) {			
			$bridged_channame = $b->get('channame');
			
			$bridged_channame_arr = multiexplode([ '/', '-', ';' ], $bridged_channame);
			
			$bridged_peer = $bridged_channame_arr[0] . '/' . $bridged_channame_arr[1];
			
			if ($bridged_peer == $this->getSourcePeer()) {
				continue;
			} else {
				$result = $bridged_peer;
			}
		}
		
		return $result;
	}
	
	public function getCID()
	{
		return $this->getEvents()->filter('eventtype', 'CHAN_START')->first()->get('cid_num');
	}
	
	public function getDID()
	{
		return $this->getEvents()->filter('eventtype', 'CHAN_START')->first()->get('exten');
	}
}