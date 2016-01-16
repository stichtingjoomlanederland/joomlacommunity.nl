<?php
/**
 * PHP Class to handle ping-o-matic
 * http://www.barattalo.it/2010/02/24/ping-pingomatic-com-services-with-php/
 *
 * ==============================================================================
 *
 *	--------------------------------------------
 *	 $title contains the title of the page you're sending
 *	 $url is the url of the page
 *	 $debug true print out the debug and show xml call and answer
 *	--------------------------------------------
 *	 the output is an array with two elements:
 * 	 status: ok / ko
 *	 msg: the text response from pingomatic
 *	--------------------------------------------
 * ==============================================================================
 */

 /* ---------------------------------------------------------- */
/* minibots.class.php Ver.1.9                                 */
/* ---------------------------------------------------------- */
/* Mini Bots class is a small php class that allows you to    */
/* use some free web seriveces online to retrive usefull data */
/* and infos. This version includes:                          */
/* smtp validation, check spelling, meteo, exchange rates,    */
/* shorten urls, and geo referencing with IP address and more */
/* Feel free to use in your applications, but link my blog:   */
/* http://www.barattalo.it                                    */
/* Giulio Pons                                                */
/* ---------------------------------------------------------- */
defined('_JEXEC') or die('Restricted access');

class DiscussPingomatic
{
	function DiscussPingomatic() {}

	/*
	--------------------------------------------
	$title contains the title of the page you're sending
	$url is the url of the page
	$debug true print out the debug and show xml call and answer
	--------------------------------------------
	the output is an array with two elements:
	status: ok / ko
	msg: the text response from pingomatic
	--------------------------------------------
	*/
	function ping($title,$url,$debug=false)
	{
		$content='<?xml version="1.0"?>'.
			'<methodCall>'.
			' <methodName>weblogUpdates.ping</methodName>'.
			'  <params>'.
			'   <param>'.
			'    <value>'.$title.'</value>'.
			'   </param>'.
			'   <param>'.
			'    <value>'.$url.'</value>'.
			'   </param>'.
			'  </params>'.
			'</methodCall>';

		$headers="POST / HTTP/1.0\r\n".
		"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729)\r\n".
		"Host: rpc.pingomatic.com\r\n".
		"Content-Type: text/xml\r\n".
		"Content-length: ".strlen($content);

		if ($debug) nl2br($headers);

		$request=$headers."\r\n\r\n".$content;
		$response = "";
		$fs=fsockopen('rpc.pingomatic.com',80, $errno, $errstr);
		if ($fs) {
			fwrite ($fs, $request);
			while (!feof($fs)) $response .= fgets($fs);
			if ($debug) echo "<xmp>".$response."</xmp>";
			fclose ($fs);
			preg_match_all("/<(name|value|boolean|string)>(.*)<\/(name|value|boolean|string)>/U",$response,$ar, PREG_PATTERN_ORDER);
			for($i=0;$i<count($ar[2]);$i++) $ar[2][$i]= strip_tags($ar[2][$i]);
			return array('status'=> ( $ar[2][1]==1 ? 'ko' : 'ok' ), 'msg'=>$ar[2][3] );
		} else {
			if ($debug) echo "<xmp>".$errstr." (".$errno.")</xmp>";
			return array('status'=>'ko', 'msg'=>$errstr." (".$errno.")");
		}
	}
}
