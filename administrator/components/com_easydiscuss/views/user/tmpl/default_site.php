<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_SITE' ); ?></h3>
		<hr />
		<table class="table table-striped">
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_SITE_URL' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'siteUrl' ) ); ?>" name="siteUrl" class="input" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_SITE_USERNAME' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'siteUsername' ) ); ?>" name="siteUsername" class="input" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_SITE_PASSWORD' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'sitePassword' ) ); ?>" name="sitePassword" class="input" style="width:200px" />
				</td>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_FTP_URL' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'ftpUrl' ) ); ?>" name="ftpUrl" class="input" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_FTP_USERNAME' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'ftpUsername' ) ); ?>" name="ftpUsername" class="input" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_FTP_PASSWORD' ); ?>:
				</td>
				<td>
					<input type="text" value="<?php echo $this->escape( $this->siteDetails->get( 'ftpPassword' ) ); ?>" name="ftpPassword" class="input" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_OPTIONAL' ); ?>:
				</td>
				<td>
					<textarea name="optional" id="optional" class="input" style="width:200px" /><?php echo $this->escape( $this->siteDetails->get( 'optional' ) ); ?></textarea>
				</td>
			</tr>
			</tr>
		</table>

	</div>

</div>
