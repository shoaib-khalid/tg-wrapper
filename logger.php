<?php

class logger
{
	private $file;

	// set logging
	public function __construct()
	{
		$root = $_SERVER["DOCUMENT_ROOT"];
		$this->file = $root.'/kbot/telegram/log';
	}

	// write to log
	public function log($text)
	{
		file_put_contents($this->file,$text,FILE_APPEND);
	}
}

?>
