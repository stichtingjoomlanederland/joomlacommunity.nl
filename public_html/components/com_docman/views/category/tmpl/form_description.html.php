<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<div class="k-form-group">
    <?= helper('editor.display', array(
        'name' => 'description',
        'value' => $category->description,
        'id'   => 'description',
        'width' => '100%',
        'height' => '341',
        'cols' => '100',
        'rows' => '20',
        'buttons' => array('pagebreak')
    )); ?>
</div>
