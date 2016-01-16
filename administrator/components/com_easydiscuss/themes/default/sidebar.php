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
defined('_JEXEC') or die();
$activeChild = JRequest::getString( 'child' );
$layout = JRequest::getString( 'layout' );
if( count($menus) ) { ?>
<script type="text/javascript">
EasyDiscuss.ready(function($){
	EasyDiscuss.ajax( 'admin.views.discuss.getUpdates', {})
		.done(function( state, content, local, version ){
			var container = $('div.ed-version');

			if( state == 'latest' )
			{
				container.addClass('version_latest');
			}
			if( state == 'outdated' )
			{
				container.addClass('version_outdated');
			}

			container.attr('data-content', content);

		});
})
</script>

<div class="sidebar clearfix">
	<?php
		$version	= DiscussHelper::getVersion();
		$local		= DiscussHelper::getLocalVersion();
	?>
	<div class="ed-version" rel="ed-popover" data-placement="right" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_VERSION'); ?>" data-content="" >
		<?php echo JText::_('COM_EASYDISCUSS_VERSION') . $local ?>
		<?php if( $local < $version ){ ?>
			<a href="http://stackideas.com/downloads.html" target="_BLANK" style="text-decoration:underline"><?php echo JText::_('COM_EASYDISCUSS_DOWNLOAD_HERE') ?></a>
		<?php } ?>
	</div>

	<ul id="sinav" class="accordion unstyled">
	<?php foreach ($menus as $menu) { ?>
		<?php
			$menuActive = ($active == $menu->name);
			$menuActiveClass = $menuActive ? 'active' : '';
			$menuActiveUl = $menuActive ? 'in' : '';
			$menuHasChild = count( $menu->child );
		?>
		<li class="accordion-group">

			<?php if( $menuHasChild ) { ?>
				<a href="<?php echo '#' . $menu->name; ?>" data-parent="#sinav" data-foundry-toggle="collapse" class="toggle-btn <?php echo $menuActiveClass; ?>"><i class="<?php echo $menu->class; ?>"></i> <?php echo JText::_( $menu->title ); ?> <b></b></a>
				<ul class="unstyled accordion-body collapse <?php echo $menuActiveUl; ?>" id="<?php echo $menu->name; ?>">
				<?php foreach ($menu->child as $child) { ?>
					<?php
						$childActive = ($activeChild == $child->name);
						$childActiveClass = $childActive ? 'active' : '';
						$childActiveUl = $childActive ? 'in' : '';
						$childHasChild = count( $child->child );
					?>
					<li class="accordion-group">
						<?php if( $childHasChild ) { ?>
							<a href="<?php echo '#' . $menu->name . '-' . $child->name; ?>" data-parent="#<?php echo $menu->name; ?>" class="toggle-btn <?php echo $childActiveClass; ?>" data-foundry-toggle="collapse"><?php echo JText::_( $child->title ); ?> <b></b></a>
							<ul class="unstyled accordion-body collapse <?php echo $childActiveUl; ?>" id="<?php echo $menu->name.'-'.$child->name; ?>">
							<?php foreach ($child->child as $grandchild) { ?>
								<li class="">
									<?php
									$tmp = explode( '_', $layout );
									$currentGrandChild = $tmp[count($tmp) - 1];
									$grandchildActiveClass = ($currentGrandChild == $grandchild->name) ? ' class="active"' : ''; ?>
									<a href="<?php echo $grandchild->url; ?>"<?php echo $grandchildActiveClass; ?>><?php echo JText::_( $grandchild->title ); ?></a>
								</li>
							<?php } ?>
							</ul>
						<?php } else { ?>
							<?php
								$layout = str_ireplace('default_', '', $layout);
								$tmp = str_ireplace( ' ', '', strtolower( JText::_($child->title)) );
								$childActiveClass = ($layout == $tmp) ? ' active' : '';
							?>
							<a href="<?php echo $child->url; ?>" data-parent="#<?php echo $menu->name; ?>" class="toggle-btn <?php echo $childActiveClass; ?>"><?php echo JText::_( $child->title ); ?></a>
						<?php } ?>
					</li>
				<?php } ?>
				</ul>
			<?php } else { ?>
				<a href="<?php echo $menu->url; ?>" class="toggle-btn <?php echo $menuActiveClass; ?>"><i class="<?php echo $menu->class; ?>"></i> <?php echo JText::_( $menu->title ); ?></a>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>
</div>
<?php } ?>
