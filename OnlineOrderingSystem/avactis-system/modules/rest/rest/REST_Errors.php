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

class REST_Errors extends RESTResponse
{
	function UndefinedRequestType()
	{
		$e_type = __FUNCTION__;
		$e_message = 'HTTP request type is undefined or unsupported.';
		$this->setResponseError($e_type, $e_message);
	}

	function UndefinedRESTQuery()
	{
		$e_type = __FUNCTION__;
		$e_message = 'REST query is empty. Nothing to do.';
		$this->setResponseError($e_type, $e_message);
	}

	function UndefinedHandler()
	{
		$e_type = __FUNCTION__;
		$e_message = 'Cannot find an appropriate handler for the REST query.';
		$this->setResponseError($e_type, $e_message);
	}

	function UndefinedHandlerClass()
	{
		$e_type = __FUNCTION__;
		$e_message = 'The REST query handler cannot be loaded.';
		$this->setResponseError($e_type, $e_message);
	}

	function UndefinedLoginPassword()
	{
		$e_type = __FUNCTION__;
		$e_message = 'Username or password undefined.';
		$this->setResponseError($e_type, $e_message);
	}

	function IncorrectLoginPassword()
	{
		$e_type = __FUNCTION__;
		$e_message = 'Username or password is incorrect.';
		$this->setResponseError($e_type, $e_message);
	}

	function IncorrectRestProtocol()
	{
		$e_type = __FUNCTION__;
		$e_message = 'Only HTTPS supported.';
		$this->setResponseError($e_type, $e_message);
	}

}