<?php
/*
curl wrapper for api calls
*/

namespace lcurl;

class client
{
	private $URL;
	private $httpHeader;
	private $response;
	private $userpwd;
	private $timeout;
	
	public function __construct($url=NULL)
	{
		$this->URL = $url;
		$this->response = "";
	}

	public function seturl($url)
	{
		$this->URL = $url;
	}
	
	public function setheader($header)
	{
		$this->httpHeader = $header;
	}

	public function setuserpwd($userpwd)
	{
		$this->userpwd = $userpwd;
	}

	public function settimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	public function get($postData="")
	{
		$httpCode=0;
		try
		{
			$ch  = curl_init($this->URL);
			
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			if(isset($this->httpHeader))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader);
			if(isset($this->userpwd))
				curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
			if(isset($this->timeout))
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$response = curl_exec($ch);
			if(curl_errno($ch))
			{
				$response = "GET error. ".curl_error($ch);
			}
				
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->response = $response;
			curl_close($ch);
		}
		catch(Exception $e)
		{
			$this->response = "GET exception = ".$e->getMessage();
		}
		finally
		{
			return $httpCode;
		}
	}

	public function post($postData)
	{
		$httpCode=0;
		try
		{
			$ch  = curl_init($this->URL);

			curl_setopt($ch, CURLOPT_POST, 1);
			if(isset($this->httpHeader))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader);
			if(isset($this->userpwd))
				curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
			if(isset($this->timeout))
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			if (curl_errno($ch))
			{
				$response = "POST error. ".curl_error($ch);
			}
			
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->response = $response;
			curl_close($ch);
		}
		catch(Exception $e)
		{
			$this->response = "Post exception = ".$e->getMessage();
		}
		finally
		{
			return $httpCode;
		}
	}

	public function delete($postData="")
	{
		$httpCode=0;
		try
		{
			$ch  = curl_init($this->URL);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			if(isset($this->httpHeader))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader);
			if(isset($this->userpwd))
				curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
			if(isset($this->timeout))
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			if (curl_errno($ch))
			{
				$response = "POST error. ".curl_error($ch);
			}
			
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->response = $response;
			curl_close($ch);
		}
		catch(Exception $e)
		{
			$this->response = "Post exception = ".$e->getMessage();
		}
		finally
		{
			return $httpCode;
		}
	}

	public function put($postData="")
	{
		$httpCode=0;
		try
		{
			$ch  = curl_init($this->URL);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			if(isset($this->httpHeader))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader);
			if(isset($this->userpwd))
				curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
			if(isset($this->timeout))
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			if (curl_errno($ch))
			{
				$response = "PUT error. ".curl_error($ch);
			}
			
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->response = $response;
			curl_close($ch);
		}
		catch(Exception $e)
		{
			$this->response = "PUT exception = ".$e->getMessage();
		}
		finally
		{
			return $httpCode;
		}
	}

	public function response()
	{
		return $this->response;
	}
}

?>

