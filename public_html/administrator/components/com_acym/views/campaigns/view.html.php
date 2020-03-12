<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class CampaignsViewCampaigns extends acymView
{
    public function __construct()
    {
        parent::__construct();

        $this->steps = [
            'chooseTemplate' => 'ACYM_CHOOSE_TEMPLATE',
            'editEmail' => 'ACYM_EDIT_EMAIL',
            'recipients' => 'ACYM_RECIPIENTS',
            'sendSettings' => 'ACYM_SEND_SETTINGS',
            'tests' => 'ACYM_TEST',
            'summary' => 'ACYM_SUMMARY',
        ];
    }
}

