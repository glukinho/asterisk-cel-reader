<?php

function multiexplode ($delimiters,$string) {
   
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

class CEL_Event
{
	public function __construct($cel_event_array)
	{
		// print_r($cel_event_array);
		foreach ($cel_event_array as $key => $value) {
			$this->{$key} = $value;
			
			if ($key == 'extra') {
				$extra_obj = json_decode($value);
				if (json_last_error() == JSON_ERROR_NONE) {
					$this->{$key} = $extra_obj;
				} else {
					throw new CEL_Exception('CEL_Event extra json decode error');
				}
			}
			
			//// decode channame to object
			// if ($key == 'channame') {
				// $chan_arr = multiexplode ( [ '/', '-', ';' ], $value );
				// $chan_obj = new stdClass();
				// $chan_obj->full = $value;
				// $chan_obj->tech = $chan_arr[0];
				// $chan_obj->peer = $chan_arr[1];
				// $chan_obj->techpeer = $chan_obj->tech . '/' . $chan_obj->peer;
				// $chan_obj->id = $chan_arr[2];
				// $chan_obj->local_id = ( isset($chan_arr[3]) ? $chan_arr[3] : null );
				
				// $this->{$key} = $chan_obj;
			// }
		}
	}
	
	public function get($key)
	{
		return (isset($this->{$key}) ? $this->{$key} : null);
	}
}