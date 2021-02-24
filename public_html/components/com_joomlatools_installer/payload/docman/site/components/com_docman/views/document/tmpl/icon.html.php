<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? if (substr($icon, 0, 5) === 'icon:'): $icon = substr($icon, 5); ?>
<span class="koowa_header__image_container">
  <img src="icon://<?= $icon ?>" class="koowa_header__image" />
</span>
<? else: ?>
<span class="k-icon-document-<?= $icon ?> <?= isset($class) ? $class : '' ?>" aria-hidden="true"></span>
<span class="k-visually-hidden"><?= translate($icon); ?></span>
<? endif; ?>
