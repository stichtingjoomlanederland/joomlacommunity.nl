<?php
/**
 * @package     mod_slider
 *
 * @copyright   Copyright (C) 2015 Perfect Web Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="carousel slide" id="jc-home-slider">
	<div class="carousel-inner">
		<?php foreach ($slides as $key => $slide) : ?>
		<div class="item <?php echo ($key == 0 ? "active" : "")?>">
			<img alt="<?php echo $slide->image_alt; ?>" src="<?php echo $slide->image; ?>">
			<div class="carousel-caption left">
				<h2><?php echo $slide->title; ?></h2>
				<div class="lead">
					<?php echo $slide->text; ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="carousel-controls">
		<a class="left carousel-control" href="#jc-home-slider" data-slide="prev">
			<span class="icon-prev"></span>
		</a>
		<a class="right carousel-control" href="#jc-home-slider" data-slide="next">
			<span class="icon-next"></span>
		</a>
	</div>
</div>
