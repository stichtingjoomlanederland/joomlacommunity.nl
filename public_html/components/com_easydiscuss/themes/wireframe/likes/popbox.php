<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
    <width>250</width>
    <height>200</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        }
    }
    </bindings>
    <title><?php echo JText::_($action); ?></title>
    <content>
        <div class="o-grid o-grid--1of2 t-lg-mt--md">
            <?php foreach ($result as $user) { ?>
            <div class="o-grid__cell t-lg-mb--md">
                <div class="o-flag">
                    <div class="o-flag__image o-flag--top">
                        <?php echo $this->html('user.avatar', $user, array('status' => true, 'size' => 'l', 'popbox' => false)); ?>
                    </div>
                    <div class="o-flag__body">
                        <?php echo $this->html('user.username', $user, array()); ?>
                    </div>
                </div>    
            </div>
            <?php } ?>
        </div>
    </content>
</dialog>
