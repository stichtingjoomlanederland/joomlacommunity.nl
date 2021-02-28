<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\Layout\LayoutHelper;
?>
<div class="forum-avatar"><?php
    echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $displayData, 'type' => 'avatar']);
?></div>
