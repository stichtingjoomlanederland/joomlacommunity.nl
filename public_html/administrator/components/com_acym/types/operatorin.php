<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class operatorinType extends acymObject
{
    var $values = [];
    var $class = 'acym__select';
    var $extra = '';

    public function __construct()
    {
        parent::__construct();

        $this->values[] = acym_selectOption('in', 'ACYM_IN');
        $this->values[] = acym_selectOption('not-in', 'ACYM_NOT_IN');
    }

    public function display($name, $valueSelected = '')
    {
        return acym_select($this->values, $name, $valueSelected, $this->extra.' class="'.$this->class.'"');
    }
}

