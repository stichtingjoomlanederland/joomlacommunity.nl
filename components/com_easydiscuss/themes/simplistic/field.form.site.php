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

$url			= false;
$siteusername	= false;
$password 		= false;
$siteinfo 		= false;
$ftpurl 		= false;
$ftpusername	= false;
$ftppassword	= false;

if( isset( $post ) && is_object( $post ) )
{
	$url 			= $this->getFieldData( 'siteurl' , $post->params );
	$siteusername 	= $this->getFieldData( 'siteusername' , $post->params );
	$password		= $this->getFieldData( 'sitepassword' , $post->params );
	$siteinfo		= $this->getFieldData( 'siteinfo' , $post->params );
	$ftpurl			= $this->getFieldData( 'ftpurl' , $post->params );
	$ftpusername	= $this->getFieldData( 'ftpusername' , $post->params );
	$ftppassword	= $this->getFieldData( 'ftppassword' , $post->params );
}
$app 	= JFactory::getApplication();
$view 	= JRequest::getVar( 'view', 'post' );
?>
<?php if( $view == 'ask' && $system->config->get( 'tab_site_question') || $view == 'post' && $system->config->get( 'tab_site_reply') || $app->isAdmin() ){ ?>
<div class="tab-pane" id="siteTab-<?php echo $composer->id; ?>">
	<div class="field-sites">
		<div class="control-group">
			<div class="row-fluid">
				<div class="span12">
					<label for="params_siteurl"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_URL' );?> :</label>
					<input type="text" name="params_siteurl[]" class="input-xlarge" value="<?php echo $this->escape( $url[0] );?>" autocomplete="off" />
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span5">
				<label for="params_siteusername"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_USERNAME' );?>:</label>
				<input type="text" name="params_siteusername[]" class="input" value="<?php echo $this->escape( $siteusername[0] );?>" autocomplete="off" />
			</div>
			<div class="span7">
				<label for="params_sitepassword"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_PASSWORD' );?>:</label>

				<?php if( $app->isAdmin() ){ ?>
				<input type="text" name="params_sitepassword[]" class="input" value="<?php echo $this->escape( $password[0] );?>" autocomplete="off" />
				<?php } else { ?>
				<input type="password" name="params_sitepassword[]" class="input" value="<?php echo $this->escape( $password[0] );?>" autocomplete="off" />
				<?php } ?>
			</div>
		</div>
		<hr/>
		<div class="control-group">
			<div class="row-fluid">
				<div class="span12">
					<label for="params_siteurl"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_URL' );?>:</label>
					<input type="text" name="params_ftpurl[]" class="input-xlarge" value="<?php echo $this->escape( $ftpurl[0] );?>" />
				</div>
			</div>
		</div>
		<div class="control-group">
			<div class="row-fluid">
				<div class="span5">
					<label for="params_siteusername"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_USERNAME' );?>:</label>
					<input type="text" name="params_ftpusername[]" class="input" value="<?php echo $this->escape( $ftpusername[0] );?>" autocomplete="off" />
				</div>
				<div class="span7">
					<label for="params_sitepassword"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_FTP_PASSWORD' );?>:</label>

					<?php if( $app->isAdmin() ){ ?>
					<input type="text" name="params_ftppassword[]" class="input" value="<?php echo $this->escape( $ftppassword[0] );?>" autocomplete="off" />
					<?php } else { ?>
					<input type="password" name="params_ftppassword[]" class="input" value="<?php echo $this->escape( $ftppassword[0] );?>" autocomplete="off" />
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="control-group">
			<div class="row-fluid">
				<div class="span12">
					<label for="params_siteinfo"><?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_FORM_OPTIONAL' );?>:</label>
					<textarea name="params_siteinfo[]" class="textarea full-width"><?php echo $siteinfo[0];?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
