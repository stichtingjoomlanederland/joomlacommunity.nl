<?php
defined('_JEXEC') or die('Restricted access');
?><?php
include ACYM_CONTROLLER.'campaigns.php';

class FrontcampaignsController extends CampaignsController
{
    public function __construct()
    {
        if (!acym_level(2)) {
            acym_redirect(acym_rootURI(), 'ACYM_UNAUTHORIZED_ACCESS', 'warning');
        }

        $this->loadScripts = [
            'edit' => ['colorpicker', 'datepicker', 'thumbnail', 'foundation-email', 'introjs', 'parse-css', 'vue-applications' => ['code_editor', 'entity_select'], 'vue-prism-editor', 'editor-wysid'],
            'save' => ['colorpicker', 'datepicker', 'thumbnail', 'foundation-email', 'introjs', 'parse-css', 'vue-applications' => ['code_editor', 'entity_select'], 'vue-prism-editor', 'editor-wysid'],
            'duplicate' => ['colorpicker', 'datepicker', 'thumbnail', 'foundation-email', 'parse-css', 'editor-wysid', 'vue-applications' => ['code_editor', 'entity_select'], 'vue-prism-editor',],
            'all' => ['introjs'],
        ];
        $this->authorizedFrontTasks = ['countNumberOfRecipients', 'editEmail', 'saveAjax'];
        $this->urlFrontMenu = 'index.php?option=com_acym&view=frontcampaigns&layout=listing';
        parent::__construct();
    }

    protected function setFrontEndParamsForTemplateChoose()
    {
        return acym_currentUserId();
    }
}

