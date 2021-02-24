<?php

namespace AcyMailing\Helpers;

use AcyMailing\Classes\ListClass;
use AcyMailing\Classes\UserClass;
use AcyMailing\Libraries\acymObject;

class RegacyHelper extends acymObject
{
    var $options = [];

    var $label = '';
    var $lists = null;

    public function prepareLists($options)
    {
        if (empty($options['baseOption'])) $options['baseOption'] = 'regacy';
        $this->options = $options;

        $visibleLists = $this->config->get($options['baseOption'].'_lists');
        if (empty($visibleLists)) return false;
        $visibleLists = explode(',', $visibleLists);
        acym_arrayToInteger($visibleLists);

        $listsClass = new ListClass();
        $allLists = $listsClass->getAllWithoutManagement();

        $isAdmin = acym_isAdmin();
        foreach ($visibleLists as $i => $oneListId) {
            if (in_array($oneListId, array_keys($allLists)) && $allLists[$oneListId]->active && ($allLists[$oneListId]->visible || $isAdmin)) continue;
            unset($visibleLists[$i]);
        }
        if (empty($visibleLists)) return false;


        $checkedLists = explode(',', $this->config->get($options['baseOption'].'_checkedlists'));
        acym_arrayToInteger($checkedLists);
        $userClass = new UserClass();

        if ('wordpress' === ACYM_CMS) {
            $currentCMSId = acym_getVar('int', 'user_id', 0);
            if (empty($currentCMSId)) $currentCMSId = acym_currentUserId();
        } else {
            if (acym_isAdmin()) {
                $currentCMSId = acym_getVar('int', 'id', 0);
            } else {
                $currentCMSId = acym_currentUserId();
            }
        }

        if (!empty($currentCMSId)) {
            $currentUser = $userClass->getOneByCMSId($currentCMSId);
            if (!empty($currentUser)) {
                $checkedLists = [];
                $currentSubscription = $userClass->getSubscriptionStatus($currentUser->id, $visibleLists);

                foreach ($currentSubscription as $listId => $oneSubsciption) {
                    if ($oneSubsciption->status == '1') $checkedLists[] = $listId;
                }
            }
        }

        $autolist = explode(',', $this->config->get($options['baseOption'].'_autolists'));
        acym_arrayToInteger($autolist);
        if (!empty($autolist)) {
            $visibleLists = array_diff($visibleLists, $autolist);
        }


        $this->label = $this->config->get($options['baseOption'].'_text');
        if (empty($this->label)) $this->label = 'ACYM_SUBSCRIPTION';
        $this->label = acym_translation($this->label);

        $this->lists = [];

        foreach ($visibleLists as $oneListId) {
            $this->lists[$oneListId] = ['name' => $allLists[$oneListId]->name, 'checked' => in_array($oneListId, $checkedLists)];
        }

        if ('joomla' === ACYM_CMS || !empty($options['formatted'])) $this->_formatResults();

        return true;
    }

    private function _formatResults()
    {
        $base = $this->options['baseOption'];
        $result = '<table class="acym__'.$base.'__lists" style="border:0">';
        foreach ($this->lists as $id => $oneList) {
            $checked = $oneList['checked'] ? 'checked="checked"' : '';
            $result .= '<tr style="border:0">
                            <td style="border:0">
                                <input type="checkbox" id="acym__'.$base.'__lists-'.intval(
                    $id
                ).'" class="acym_checkbox" name="'.$base.'_visible_lists_checked[]" '.$checked.' value="'.intval($id).'"/>
                            </td>
                            <td style="border:0; padding-left:10px;" nowrap="nowrap">
                                <label for="acym__'.$base.'__lists-'.intval($id).'" class="acym__'.$base.'__lists__label">'.acym_escape($oneList['name']).'</label>
                            </td>
                        </tr>';
        }
        $result .= '</table>';
        $result .= '<input type="hidden" value="'.implode(',', array_keys($this->lists)).'" name="'.$base.'_visible_lists" />';
        $source = !empty($base) && $base != 'regacy' ? $base : ACYM_CMS;
        $result .= '<input type="hidden" value="'.$source.' registration form" name="acy_source" />';
        $this->lists = $result;
    }
}
