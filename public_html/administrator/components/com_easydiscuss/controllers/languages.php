<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerLanguages extends EasyDiscussController
{
    /**
     * Purge all discovered language files
     *
     * @since   4.0
     * @access  public
     */
    public function purge()
    {
        // Check for request forgeries here
        ED::checkToken();

        // Get the model
        $model = ED::model('Languages');
        $model->purge();

        ED::setMessage('COM_EASYDISCUSS_LANGUAGE_PURGED_SUCCESSFULLY', 'success');

        $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
    }

    /**
     * Discovery of language files
     *
     * @since   5.0
     * @access  public  
     */
    public function discover()
    {
        $model = ED::model('Languages');
        $result = $model->discover();

        if (!$result) {
            ED::setMessage($model->getError(), 'error');
        } else {
            ED::setMessage('COM_EASYDISCUSS_LANGUAGE_DISCOVERED_SUCCESSFULLY', 'success');
        }
        

        return $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
    }

    /**
     * Install language file on the site
     *
     * @since   4.0
     * @access  public
     */
    public function install()
    {
        // Check for request forgeries here
        ED::checkToken();

        // Get the language id
        $ids = $this->input->get('cid', array(), 'array');

        foreach ($ids as $id) {
            $table = ED::table('Language');
            $table->load($id);

            $state = $table->install();

            if (!$state) {
                ED::setMessage($table->getError(), 'error');
                return $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
            }
        }

        ED::setMessage('COM_EASYDISCUSS_LANGUAGE_INSTALLED_SUCCESSFULLY', 'success');

        $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
    }

    /**
     * Uninstall language file on the site
     *
     * @since   4.0.21
     * @access  public
     */
    public function uninstall()
    {
        // Check for request forgeries here
        ED::checkToken();

        // Get the language id
        $ids = $this->input->get('cid', array(), 'array');

        foreach ($ids as $id) {
            $id = (int) $id;

            $table = ED::table('Language');
            $table->load($id);

            if (!$table->isInstalled()) {
                $table->delete();
                continue;
            }

            $table->uninstall();
            $table->delete();
        }

        ED::setMessage(JText::_('COM_ED_LANGUAGE_UNINSTALLED_SUCCESSFULLY'), 'success');

        $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
    }    
}
