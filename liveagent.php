<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root. '/lphp/lmongodb/autoload.php';

class liveagent
{
	private $liveagent;

	public function __construct()
	{
		$client = new MongoDB\Client("mongodb://localhost:27017");
		$db = $client->wswrapper;
		$this->liveagent= $db->liveagent;
	}

	// get liveAgent routing
	public function status($msisdn)
	{
		$key['msisdn'] = $msisdn;
		$result = $this->liveagent->find($key);
		$total = count($result->toArray());

		if($total>0)
			return true;
		else
			return false;
	}

	// set routing to liveAgent
	public function insert($msisdn)
	{
		$key['msisdn'] = $msisdn;
		$result = $this->liveagent->insertOne($key);
	}

	// remove routing to liveAgent
	public function remove($msisdn)
	{
		$key['msisdn'] = $msisdn;
		$result = $this->liveagent->deleteMany($key);
	}
}

?>
