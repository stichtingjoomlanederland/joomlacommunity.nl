<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<!-- Scopebar -->
<div class="k-scopebar k-js-scopebar">

    <!-- Search -->
    <div class="k-scopebar__item k-scopebar__item--search">
        <?= helper('grid.search', array(
            'submit_on_clear' => false,
            'placeholder' => translate('Find by file or folder name&hellip;')
        )) ?>
    </div>

</div><!-- .k-scopebar -->

