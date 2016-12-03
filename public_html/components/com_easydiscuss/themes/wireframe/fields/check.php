<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($options) { ?>
    <?php foreach ($options as $option) { ?>
    <?php $uid = uniqid(); ?>
    <div <?php echo $field->required ? 'data-ed-custom-fields-required' : '' ?>>
        <div class="checkbox">
          <label for="checkbox-<?php echo $uid;?>">
              <input type="checkbox" 
                     value="<?php echo $this->html('string.escape', $option);?>" 
                     name="fields[<?php echo $field->id;?>][]" 
                     <?php echo $value && is_array($value) && in_array($option, $value) ? ' checked="checked"' : '';?>
                     id="checkbox-<?php echo $uid;?>" 
                     <?php echo $field->required ? 'data-ed-checkbox-fields' : '' ?>
              />
              <?php echo $option;?>
          </label>
        </div>
    </div>
    <?php } ?>
<?php } ?>