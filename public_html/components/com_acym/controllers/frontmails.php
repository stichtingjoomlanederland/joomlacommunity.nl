<?php
defined('_JEXEC') or die('Restricted access');
?><?php
include ACYM_CONTROLLER.'mails.php';

class FrontmailsController extends MailsController
{
    public function __construct()
    {
        $this->authorizedFrontTasks = ['autoSave', 'setNewIconShare', 'edit', 'setNewThumbnail', 'getTemplateAjax', 'apply', 'saveAjax', 'save'];
        $this->loadScripts = [
            'edit' => ['colorpicker', 'datepicker', 'editor', 'thumbnail', 'foundation-email', 'introjs', 'parse-css', 'vue-applications' => ['code_editor'], 'vue-prism-editor', 'editor-wysid'],
            'apply' => ['colorpicker', 'datepicker', 'editor', 'thumbnail', 'foundation-email', 'introjs', 'parse-css', 'vue-applications' => ['code_editor'], 'vue-prism-editor', 'editor-wysid'],
        ];
        parent::__construct();
    }

    protected function setFrontEndParamsForTemplateChoose()
    {
        return acym_currentUserId();
    }
}
