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
?>

<script type="text/javascript">
EasyDiscuss.ready(function($){
	$.Joomla( 'submitbutton' , function(action)
	{
		$.Joomla( 'submitform' , [action] );
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<?php if( $this->config->get( 'main_backend_statistics' ) ) { ?>
	<div class="span6">
		<?php echo $this->loadTemplate( 'chart' ); ?>
		<?php echo $this->loadTemplate( 'stats' ); ?>
	</div>
	<?php } ?>
	<div class="span<?php echo $this->config->get( 'main_backend_statistics' ) ? '6' : '12'; ?>">
		<?php echo $this->loadTemplate( 'news' ); ?>
	</div>
</div>

<?php echo $this->loadTemplate( 'about' ); ?>


<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="discuss" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="discuss" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
