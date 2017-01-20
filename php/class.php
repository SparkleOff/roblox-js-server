<?php

class RobloxJS
{
	protected   $domain         =   'http://rbx-js.herokuapp.com';   	//The heroku domain that was hopefully created already
   	protected   $key            =   '';        	 			//Your private key that was in the settings.json
    	
    	protected function post($url, $data)
    	{	
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch,CURLOPT_URL,$this->domain.$url);
		curl_setopt($ch,CURLOPT_POST,count($data));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($ch);
		return $result;
	}
	
	protected function http ($path,$data=array())
	{
		$data['key'] = $this->key;
  		return $this->post($path, json_encode($data));
	}
	
	public function promote ($group, $userId)
	{
  		return $this->http('/promote/'.$group.'/'.$userId);
	}
	
	public function demote ($group, $userId)
	{
  		return $this->http('/demote/'.$group.'/'.$userId);
	}
	
	public function setRank ($group, $userId, $rank)
	{
  		return $this->http('/setRank/'.$group.'/'.$userId.'/'.$rank);
	}
	
	public function shout ($group, $message)
	{
  		return $this->http('/shout/'.$group,array('message' => $message));
	}
	
	public function postgroup ($group, $message)
 	{
 		 return $this->http('/post/'.$group, array('message' => $message));
	}
	
	public function handleJoinRequest ($group, $username, $accept)
  	{
 		return $this->http('/handleJoinRequest/'.$group.'/'.$username.'/'.$accept);
	}
	
	public function getPlayers ($group, $rank='', $limit='-2', $online='false')
  	{
  		$complete = 0;

  		$job = json_decode($this->http('/getPlayers/make/'.$group.'/'.$rank.'?limit='.$limit.'&online='.$online),true);
		foreach($job as $lock=>$pick) {if ($lock == "data") { $jobid = $pick["uid"]; }}
	
		while ($complete == 0) {
			$retrievepeople = file_get_contents($this->domain.'/getPlayers/retrieve/'.$jobid);
			$finalresult = json_decode($retrievepeople,true);
			foreach($finalresult as $name=>$data) {
				if ($name == "data") {
					foreach($data as $jdatan=>$jdatad) {
						if ($jdatan == "progress") { $complete = $jdatad; }
					}
				}
			}
			if ($complete > 0) { return $retrievepeople;}
		}
  	}
  	
  	public function sendmessage ($userId, $subject, $message)
  	{
  		return $this->http('/message/'.$userId, array('subject' => $subject,'body' => $message));
	}
	
	public function forumPostNew ($forumId, $subject, $body, $locked = 'false')
	{
		
  		return $this->http('/forumPost/new/'.$forumId.'?locked='.$locked, array('subject' => $subject,'body' => $body));
	}

	public function forumPostReply ($postId, $body, $locked = 'false')
	{
		return $this->http('/forumPost/reply/'.$postId.'?locked='.$locked, array('body' => $body));
	}
}
?>
