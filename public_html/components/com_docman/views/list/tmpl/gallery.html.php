<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? // Document list | Import child template from documents view ?>
<?= import('com://site/docman.documents.gallery.html', array(
    'documents' => $documents,
    'subcategories' => $subcategories,
    'params' => $params
))?>
