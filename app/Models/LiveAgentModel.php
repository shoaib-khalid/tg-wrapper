<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Client as MongoDB;

class LiveAgentModel extends Model
{
    use HasFactory;
	private $liveagent;

	public function __construct(){
		$dbHost=config('database.connections.mongodb.host');
		$dbPort=config('database.connections.mongodb.port');
		$dbUser=config('database.connections.mongodb.username');
		$dbPass=config('database.connections.mongodb.password');

		$client = new MongoDB("mongodb://$dbHost:$dbPort",["username" => $dbUser, "password" => $dbPass]);
		$db = $client->wswrapper;
		$this->liveagent = $db->liveagent;
	}
	
	// get liveAgent routing
	public function status($msisdn)
	{
		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Status msisdn[$msisdn]");
		
		$timeout = strtotime("-10 minutes");
		$validity = array('$gte' => $timeout);

		$key['msisdn'] = $msisdn;
		$key['timestamp'] = $validity;

		$result = $this->liveagent->find($key);
		$total = count($result->toArray());

		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Status result[" . json_encode($result) . "]");

		if($total>0)
			return true;
		else
			return false;
	}

	// set routing to liveAgent
	public function insert($msisdn)
	{
		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Insert msisdn[$msisdn]");

		$key['msisdn'] = $msisdn;
		$key['timestamp'] = strtotime("now");
		$result = $this->liveagent->insertOne($key);

		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Insert result[" . json_encode($result) . "]");
	}

	// remove routing to liveAgent
	public function remove($msisdn)
	{
		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Remove msisdn[$msisdn]");

		$key['msisdn'] = $msisdn;
		$result = $this->liveagent->deleteMany($key);

		\Log::channel('transaction')->debug("TG-Wrapper -- MongoDB Remove result[" . json_encode($result) . "]");

	}
}

?>