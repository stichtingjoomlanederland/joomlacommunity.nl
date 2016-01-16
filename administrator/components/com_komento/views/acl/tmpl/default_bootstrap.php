<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped" cellspacing="1">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
				<th width="98%"><?php echo JText::_( 'COM_KOMENTO_ACL_COLUMN_GROUP_TITLE' ); ?></th>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_ID' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			$k = 0;
			$x = 0;
			foreach( $this->usergroups as $usergroup ) { ?>
			<tr class="row<?php echo $k; ?>">
				<td align="center"><?php echo ++$x; ?></td>
				<td><?php echo str_repeat( '|—', $usergroup->depth ); ?> <a href="index.php?option=com_komento&view=acl&layout=form&component=<?php echo $this->component; ?>&type=usergroup&id=<?php echo $usergroup->id; ?>"><?php echo $usergroup->title; ?></a></td>
				<td align="center"><?php echo $usergroup->id; ?></td>
			</tr>
			<?php $k = 1 - $k; } ?>
		</tbody>
	</table>
	</div>
</div>
