<?php

/******************************************************************************
 * History:
 * $Log$
 *
 ******************************************************************************
 * Date :           $Date$
 * Revision :       $Revision$
 ******************************************************************************
 */

/**
 * Contains error information.
 */
class ErrorResponse
{
	var $errCode;

	var $errMsg;

	var $consumerMsg;

	var $errorMessage = true;

	function getErrorCode()
	{
		return $this->errCode;
	}

	function getErrorMessage()
	{
		return $this->errMsg;
	}

	function getConsumerMessage()
	{
		return $this->consumerMsg;
	}

	function setErrorCode($errCode)
	{
		$this->errCode = $errCode;
	}

	function setErrorMessage($errMsg)
	{
		$this->errMsg = $errMsg;
	}

	function setConsumerMessage($consumerMsg)
	{
		$this->consumerMsg = $consumerMsg;
	}

	function IsResponseError()
	{
		return true;
	}
}
