<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class FrontusersViewFrontusers extends acymView
{
    public function __construct()
    {
        global $Itemid;
        $this->Itemid = $Itemid;

        parent::__construct();
    }
}

