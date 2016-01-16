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
<?php if( $system->config->get( 'main_syntax_highlighter') ){ ?>
// Find any response that contains a code syntax.
EasyDiscuss.main_syntax_highlighter = true;

EasyDiscuss.require()
.script( 'prism' )
.done(function($){

	Prism.highlightAll();
	
});
<?php } ?>
</script>

<?php echo $jsToolbar; ?>

<div id="discuss-wrapper" class="discuss-wrap<?php echo $suffix . $categoryClass . $jomsocialClass . $discussView;?>">
	
	

	<?php echo $toolbar; ?>

	<?php echo $contents; ?>

	<?php echo DISCUSS_POWERED_BY; ?>

	<input type="hidden" class="easydiscuss-token" value="<?php echo DiscussHelper::getToken();?>" />

</div>