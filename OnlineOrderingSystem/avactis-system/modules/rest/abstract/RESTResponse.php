<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

class RESTResponse
{
	function __construct()
	{
	}

	function send()
	{
	    header('Content-Type: application/json'/*, true, $this->http_status*/);
		if (false && function_exists('json_encode'))
		{
			echo json_encode($this->response);
		}
		else
		{
			$json = new Services_JSON();
			echo $json->encode($this->response);
		}
	}

	protected function setResponseError($type, $message)
	{
		$this->response['status'] = 1;
		$this->response['error'] = array('type'=>$type, 'message'=>$message);
		$this->response['data'] = null;
		$this->http_status = 400;
	}

	protected function setResponseOk($data)
	{
		$this->response['status'] = 0;
		$this->response['error'] = null;
		$this->response['data'] = $data;
		$this->http_status = 200;
	}

	protected $http_status = 200;
	protected $response = array(
				'status' => 0,
				'error' => null,
				'data' => null,
			);
}