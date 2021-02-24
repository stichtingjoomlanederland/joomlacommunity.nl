<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Controller permission to allow all CRUD actions
 *
 * @author Ercan Ozkaya <https://github.com/ercanozkaya>
 *
 */
class ComDocmanControllerPermissionYesman extends KControllerPermissionAbstract
{
    public function canAdmin()
    {
        return true;
    }

    /**
     * Permission handler for add actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canAdd()
    {
        return ($this->getMixer() instanceof KControllerModellable);
    }

    /**
     * Permission handler for edit actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canEdit()
    {
        return ($this->getMixer() instanceof KControllerModellable);
    }

    /**
     * Permission handler for delete actions
     *
     * Method returns true of the controller implements KControllerModellable interface
     *
     * @return  boolean  Returns TRUE if action is permitted. FALSE otherwise.
     */
    public function canDelete()
    {
        return ($this->getMixer() instanceof KControllerModellable);
    }
}