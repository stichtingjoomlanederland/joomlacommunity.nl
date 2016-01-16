<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2013 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

########################################
##### Configuration options.
########################################

// This should not contain http:// or https://
$host		= 'yoursite.com';

########################################

// In case the host name is not configured or contain http:// https://
if( stripos( $host, 'http://' ) !== false || stripos( $host, 'https://' ) !== false || $host == 'yoursite.com' )
{
	return;
}

$fp 	= @fsockopen( $host , 80 , $errorNum , $errorStr );

if( !$fp )
{
	echo 'There was an error connecting to the site.';
	exit;
}


function connect( $fp , $host, $url )
{
	$request 	= "GET /" . $url . " HTTP/1.1\r\n";
	$request 	.= "Host: " . $host . "\r\n";
	$request 	.= "Connection: Close\r\n\r\n";

	fwrite( $fp , $request );
}

connect( $fp , $host , 'index.php?option=com_komento&task=cron' );

fclose( $fp );

echo "Cronjob processed.\r\n";
return;
