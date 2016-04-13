<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="container-nav hidden">
	<a class="nav-sidebar-toggle" data-ed-toggle="collapse" data-target=".app-sidebar-collapse">
		<i class="fa fa-bars"></i>
	</a>
	<a class="nav-subhead-toggle" data-ed-toggle="collapse" data-target=".subhead-collapse">
		<i class="fa fa-cog"></i>
	</a>
</div>

<div class="app-sidebar" data-sidebar>
	<ul class="app-sidebar-menu reset-list">
	<?php foreach ($menus as $menu) { ?>
		<li class="<?php echo $view == $menu->view ? ' active' : '';?>" data-sidebar-item>

			<?php if ($menu->childs) { ?>
				<a href="javascript:void(0);" data-sidebar-link>
					<i class="fa <?php echo $menu->icon; ?>"></i> <?php echo $menu->title;?><b></b>
				</a>

				<?php if ($menu->childs) { ?>
				<ul class="app-sidebar__childs" data-sidebar-child>
					<?php foreach ($menu->childs as $child) { ?>
					<li>
						<a href="<?php echo $child->link; ?>" class="toggle-btn <?php echo $child->class;?>">
							<?php echo $child->title;?>
						</a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>

			<?php } else { ?>
				<a href="<?php echo $menu->link; ?>"><i class="fa <?php echo $menu->icon; ?>"></i> <?php echo $menu->title;?></a>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>
</div>
