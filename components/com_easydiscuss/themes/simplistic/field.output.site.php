<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$profile	= JTable::getInstance( 'Profile' , 'Discuss' );
$profile->load( $post->user_id );
$siteDetails = DiscussHelper::getRegistry( $profile->get('site') );

$siteUrl 		= $siteDetails->get( 'siteUrl' );
$siteusername 	= $siteDetails->get( 'siteUsername' );
$password		= $siteDetails->get( 'sitePassword' );
$ftpurl			= $siteDetails->get( 'ftpUrl' );
$ftpusername	= $siteDetails->get( 'ftpUsername' );
$ftppassword	= $siteDetails->get( 'ftpPassword' );
$siteinfo		= $siteDetails->get( 'optional' );

$showProfileDetails = true;

if( empty( $siteUrl ) && empty( $siteusername ) && empty( $password ) && empty( $ftpurl ) && empty( $ftpusername ) && empty( $ftppassword ) )
{
	$showProfileDetails = false;
}

$access			= trim( $system->config->get( 'tab_site_access' ) );

// Nobody can view this if access is not set yet.
if( !$access )
{
	return;
}

$access			= explode( ',' , $access );
$gids 			= DiscussHelper::getUserGids();

$url 			= $this->getFieldData( 'siteurl' , $post->params );

if( stristr( $url[0] , 'http://') === false && stristr( $url[0] , 'https://') === false)
{
	$url[0]	= 'http://' . $url[0];
}

if( !$showProfileDetails )
{
	$siteusernameTemp 	= $this->getFieldData( 'siteusername' , $post->params );
	$passwordTemp		= $this->getFieldData( 'sitepassword' , $post->params );
	$ftpurlTemp			= $this->getFieldData( 'ftpurl' , $post->params );
	$ftpusernameTemp	= $this->getFieldData( 'ftpusername' , $post->params );
	$ftppasswordTemp	= $this->getFieldData( 'ftppassword' , $post->params );
	$siteinfoTemp		= $this->getFieldData( 'siteinfo' , $post->params );

	$siteUrl 		= $this->escape( $url[0] );
	$siteusername 	= $siteusernameTemp[0];
	$password		= $passwordTemp[0];
	$ftpurl			= $ftpurlTemp[0];
	$ftpusername	= $ftpusernameTemp[0];
	$ftppassword	= $ftppasswordTemp[0];
	$siteinfo		= $siteinfoTemp[0];
}


if( empty( $siteusername ) && empty( $password ) && empty( $siteinfo ) && empty( $ftpurl ) && empty( $ftpusername ) && empty( $ftppassword ) )
{
	return false;
}

$view 	= JRequest::getVar( 'view' );
?>
<?php foreach( $gids as $gid ){ ?>
	<?php if( in_array( $gid , $access ) ){ ?>
	<div class="pt-20">
		<h3><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_DETAILS' ); ?></h3>
		<hr />
		<table width="100%" class="table table-striped">
			<tr>
				<td width="20%"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_URL' );?>:</td>
				<td>
					<a href="<?php echo $siteUrl; ?>" target="_blank"><?php echo $siteUrl; ?></a>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_USERNAME' );?>:</td>
				<td><?php echo $this->escape( $siteusername ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_PASSWORD' );?>:</td>
				<td><?php echo $this->escape( $password ); ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_URL' );?>:</td>
				<td>
					<?php echo $this->escape( $ftpurl ); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_USERNAME' );?>:</td>
				<td><?php echo $this->escape( $ftpusername ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_PASSWORD' );?>:</td>
				<td><?php echo $this->escape( $ftppassword ); ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_OPTIONAL' );?>:</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo str_ireplace( '\n' , "<br />" , nl2br( $siteinfo ) ); ?>

				</td>
			</tr>
		</table>
	</div>
		<?php
		// If there is match, just return here.
		return;
		?>
	<?php } ?>
<?php } ?>