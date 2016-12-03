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

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerHoliday extends EasyDiscussController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This occurs when the user tries to create a new discussion or edits an existing discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function save()
    {
        // Check for request forgeries
        ED::checkToken();

        // Get the id if available
        $id = $this->input->get('id', 0, 'int');

        // Load the holiday library
        $holiday = ED::holiday($id);

        $isNew = $holiday->id? false : true;

        // Get the date POST
        $data = JRequest::get('post');
        
        $holiday->bind($data);
        $holiday->save();

        $message = ($isNew)? JText::_('COM_EASYDISCUSS_HOLIDAY_SAVED') : JText::_('COM_EASYDISCUSS_EDIT_HOLIDAY_SUCCESS');
        
        ED::setMessage($message, 'info');
        $this->app->redirect(EDR::_('view=dashboard', false));

    }
}
