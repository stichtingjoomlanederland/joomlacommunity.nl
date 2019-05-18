<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
?>

<h3><?php echo $module->title; ?></h3>
<div class="list-group list-group-flush <?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) : ?>
		<?php $images = json_decode($item->images); ?>
        <a class="list-group-item" href="<?php echo $item->link; ?>">
			<?php
			$src = 'templates/' . Factory::getApplication()->getTemplate() . '/images/jclogo.png';
			$alt = '';
			if (isset($images->image_intro) && !empty($images->image_intro) and file_exists($images->image_intro)):
				$src = $images->image_intro;
			endif;
			?>
            <div class="news-image"><?php
				echo HTMLHelper::_('image', $src, $alt);
			?></div>
            <p class="list-group-item-meta">
                <strong><?php echo HTMLHelper::_('date', $item->publish_up, Text::_('j M Y')); ?></strong>
                door <?php echo $item->displayAuthorName; ?></p>
            <h4 class="list-group-item-heading"><?php echo $item->title; ?></h4>
            <p class="list-group-item-text"><?php echo strip_tags($item->displayIntrotext); ?></p>
        </a>
	<?php endforeach; ?>
</div>
<?php
    $href = Route::_('index.php?Itemid=240');
    $text = 'Meer Joomla-nieuws';
    $data = array(
            'class' => 'btn btn-nieuws btn-block'
    );
    echo HTMLHelper::_('link', $href, $text, $data);
