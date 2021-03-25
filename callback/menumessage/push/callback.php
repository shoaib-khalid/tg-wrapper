<?php

namespace kbotcore;

class callback
{
	public $packet;
	public $msisdn;
	public $message;

	public function __construct()
	{
		// receive packet from core
		$packet = trim(file_get_contents("php://input"));
		$this->packet= trim(preg_replace('/\s\s+/', '', $packet));
		$object = json_decode($this->packet, true);

		// get msisdn
		$recipientIds = $object['recipientIds'];
		foreach($recipientIds as $msisdn)
			break;
		if($msisdn[0] == '0')
			$msisdn = '6'.$msisdn;
		else if($msisdn[0] == '+')
			$msisdn = substr($msisdn,1);
		$this->msisdn = $msisdn;

		// set message
		$title = $object['title'];
		$subTitle = $object['subTitle'];
		$this->message = "*$title*\n$subTitle";

		// numbering
		$no = $this->unicodeNumber();

		// build menu
		$menu = "";
		$menuItems = $object['menuItems'];
		foreach($menuItems as $menuItem)
		{
			$no = $menuItem['payload'];
			$unicodeNo = $this->getUnicodeNumber($no);

			$menu.= "\n".$unicodeNo." ";
			$menu.= " ";
			$menu.= $menuItem['title'];
		}
		$this->message.= $menu;
	}

	// map unicode numbering
        private function getUnicodeNumber($item)
        {
                $maxNo = 10;
                $uni_numbers = array('0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','�');

                $no = (int)$item;
                if($no<0 || $no>$maxNo)
                        $no = 0;

                $uniNo = json_decode('"'.$uni_numbers[$no].'"');


                return $uniNo;
        }

	// map unicode numbering
	private function unicodeNumber()
	{
		$uni_numbers = array('1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣');
		$numbers = array();
		$i = 0;
		foreach($uni_numbers as $no)
			$numbers[$i++] = json_decode('"'.$no.'"');

		return $numbers;
	}
}

?>
