<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<!-- Scopebar -->
<div class="k-scopebar k-js-scopebar">

    <!-- Search -->
    <div class="k-scopebar__item k-scopebar__item--search">
        <?= helper('grid.search', array('submit_on_clear' => true)) ?>
    </div><!-- .k-scopebar__item--search -->

</div><!-- .k-scopebar -->
