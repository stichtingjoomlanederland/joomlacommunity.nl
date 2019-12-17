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

class ListsController extends acymController
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[acym_translation('ACYM_LISTS')] = acym_completeLink('lists');
        $this->listClass = acym_get('class.list');
        $this->loadScripts = [
            'settings' => ['colorpicker', 'vue-applications'],
            'saveSubscribers' => ['colorpicker', 'vue-applications'],
            'apply' => ['colorpicker', 'vue-applications'],
        ];
    }

    public function listing()
    {
        acym_setVar('layout', 'listing');

        $searchFilter = acym_getVar('string', 'lists_search', '');
        $tagFilter = acym_getVar('string', 'lists_tag', '');
        $ordering = acym_getVar('string', 'lists_ordering', 'id');
        $status = acym_getVar('string', 'lists_status', '');
        $format = acym_getVar('string', 'global_listingformat', 'list');
        $orderingSortOrder = acym_getVar('string', 'lists_ordering_sort_order', 'desc');

        $listsPerPage = acym_getCMSConfig('list_limit', 20);
        $page = acym_getVar('int', 'lists_pagination_page', 1);

        $requestData = [
            'ordering' => $ordering,
            'search' => $searchFilter,
            'elementsPerPage' => $listsPerPage,
            'offset' => ($page - 1) * $listsPerPage,
            'tag' => $tagFilter,
            'status' => $status,
            'ordering_sort_order' => $orderingSortOrder,
        ];
        $matchingLists = $this->getMatchingElementsFromData($requestData, 'list', $status);

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($matchingLists['total'], $page, $listsPerPage);

        $data = [
            'lists' => $matchingLists['elements'],
            'tags' => acym_get('class.tag')->getAllTagsByType('list'),
            'pagination' => $pagination,
            'search' => $searchFilter,
            'tag' => $tagFilter,
            'ordering' => $ordering,
            'listNumberPerStatus' => $matchingLists['status'],
            'status' => $status,
            'format' => $format,
            'orderingSortOrder' => $orderingSortOrder,
        ];

        parent::display($data);
    }

    public function settings()
    {
        acym_setVar('layout', 'settings');
        $listId = acym_getVar('int', 'id', 0);
        $welcomeId = acym_getVar('int', 'welcomemailid', 0);
        $unsubId = acym_getVar('int', 'unsubmailid', 0);
        $listTagsName = [];
        $subscribers = [];
        $subscribersEntitySelect = '';

        $randColor = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];

        if (!$listId) {
            $listInformation = new stdClass();
            $listInformation->id = '';
            $listInformation->name = '';
            $listInformation->active = 1;
            $listInformation->visible = 1;
            $listInformation->color = '#'.$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)];
            $listInformation->subscribers = ['nbSubscribers' => 0, 'sendable' => 0];
            $listInformation->welcome_id = '';
            $listInformation->unsubscribe_id = '';

            $listStats = ['deliveryRate' => 0, 'openRate' => 0, 'clickRate' => 0, 'failRate' => 0, 'bounceRate' => 0];
            $tmplsData = [];

            $this->breadcrumb[acym_translation('ACYM_NEW_LIST')] = acym_completeLink('lists&task=settings');
        } else {

            $listsTags = acym_get('class.tag')->getAllTagsByElementId('list', $listId);

            foreach ($listsTags as $oneTag) {
                $listTagsName[] = $oneTag;
            }

            $listInformation = $this->listClass->getOneById($listId);
            if (is_null($listInformation)) {
                acym_enqueueMessage(acym_translation('ACYM_LIST_DOESNT_EXIST'), 'error');
                $this->listing();

                return;
            }

            if (empty($listInformation->welcome_id) && !empty($welcomeId)) {
                $listInformation->welcome_id = $welcomeId;
                if (!$this->listClass->save($listInformation)) acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVE_LIST'), 'error');
            }
            if (empty($listInformation->unsubscribe_id) && !empty($unsubId)) {
                $listInformation->unsubscribe_id = $unsubId;
                if (!$this->listClass->save($listInformation)) acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVE_LIST'), 'error');
            }

            $listInformation->subscribers = ['nbSubscribers' => 0, 'sendable' => 0];
            $subscribersCount = $this->listClass->getSubscribersCountPerStatusByListId([$listId]);
            if (!empty($subscribersCount)) {
                $listInformation->subscribers = [
                    'nbSubscribers' => $subscribersCount[0]->users,
                    'sendable' => $subscribersCount[0]->sendable,
                ];
            }
            $listStats = $this->prepareListStat($listId);

            $tmplsData = $this->prepareWelcomeUnsubData($listInformation);

            $this->breadcrumb[acym_escape($listInformation->name)] = acym_completeLink('lists&task=settings&id=').$listId;

            $subscribers = $this->listClass->getSubscribersForList($listId, 0, 500, 1);
            foreach ($subscribers as &$oneSub) {
                $oneSub->subscription_date = acym_getDate($oneSub->subscription_date);
            }
            $subscribersEntitySelect = $this->getSubscribersEntitySelect($listId);
        }
        $svg = acym_getSvg(ACYM_IMAGES.'loader.svg');

        $listData = [
            'listInformation' => $listInformation,
            'allTags' => acym_get('class.tag')->getAllTagsByType('list'),
            'listTagsName' => $listTagsName,
            'listStats' => $listStats,
            'tmpls' => $tmplsData,
            'subscribers' => $subscribers,
            'svg' => $svg,
            'subscribersEntitySelect' => $subscribersEntitySelect,
            'requireConfirmation' => $this->config->get('require_confirmation', 0),
        ];

        parent::display($listData);
    }

    public function loadMoreSubscribers()
    {
        acym_checkToken();
        $listId = acym_getVar('int', 'listid');
        $offset = acym_getVar('int', 'offset');
        $perCalls = acym_getVar('int', 'perCalls');
        $status = acym_getVar('int', 'status');
        $subscribers = $this->listClass->getSubscribersForList($listId, $offset, $perCalls, $status);
        foreach ($subscribers as &$oneSub) {
            $oneSub->subscription_date = acym_getDate($oneSub->subscription_date);
        }
        echo json_encode(['data' => $subscribers]);
        exit;
    }

    public function getSubscribersEntitySelect($listId)
    {
        $entityHelper = acym_get('helper.entitySelect');

        return acym_modal(
            acym_translation('ACYM_ADD_SUBSCRIPTION'),
            $entityHelper->entitySelect(
                'user',
                ['join' => 'join_list-'.$listId],
                [
                    0 => 'email',
                    1 => 'id',
                    'join' => 'userlist.user_id',
                ],
                ['text' => acym_translation('ACYM_CONFIRM'), 'action' => 'saveSubscribers']
            ),
            null,
            '',
            'class="cell medium-shrink  auto margin-horizontal-1 button button-secondary acym__user__button"'
        );
    }

    public function unsetMail($type)
    {
        $id = acym_getVar('int', 'id', 0);
        $list = $this->listClass->getOneById($id);

        if (empty($list)) {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVE_LIST'), 'error');
            $this->listing();

            return;
        }

        $list->$type = null;

        if ($this->listClass->save($list)) {
            acym_setVar('id', $id);
            $this->settings();

            return;
        } else {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVE_LIST'), 'error');
            $this->listing();

            return;
        };
    }

    public function unsetWelcome()
    {
        $this->unsetMail('welcome_id');
    }

    public function unsetUnsubscribe()
    {
        $this->unsetMail('unsubscribe_id');
    }

    protected function prepareListStat($listId)
    {
        $listStats = ['deliveryRate' => 0, 'openRate' => 0, 'clickRate' => 0, 'failRate' => 0, 'bounceRate' => 0];
        $mails = $this->listClass->getMailsByListId($listId);
        if (empty($mails)) return $listStats;

        $mailListClass = acym_get('class.mailstat');
        $mailsStat = $mailListClass->getCumulatedStatsByMailIds($mails);

        if (empty(intval($mailsStat->sent) + intval($mailsStat->fails))) {
            $openRate = 0;
            $deliveryRate = 0;
            $failRate = 0;
            $bounceRate = 0;
            $clickRate = 0;
        } else {
            $totalSent = intval($mailsStat->sent) + intval($mailsStat->fails);
            if (empty($mailsStat->open)) $mailsStat->open = 0;
            if (empty($mailsStat->fails)) $mailsStat->fails = 0;
            if (empty($mailsStat->bounces)) $mailsStat->bounces = 0;

            $openRate = $mailsStat->open / $totalSent * 100;
            $deliveryRate = ($mailsStat->sent - $mailsStat->bounces) / $totalSent * 100;
            $failRate = $mailsStat->fails / $totalSent * 100;
            $bounceRate = $mailsStat->bounces / $totalSent * 100;

            $urlClickClass = acym_get('class.urlclick');
            $nbClicks = $urlClickClass->getClickRateByMailIds($mails);
            $clickRate = $nbClicks / $totalSent * 100;
        }

        $listStats['openRate'] = number_format($openRate, 2);
        $listStats['deliveryRate'] = number_format($deliveryRate, 2);
        $listStats['failRate'] = number_format($failRate, 2);
        $listStats['bounceRate'] = number_format($bounceRate, 2);
        $listStats['clickRate'] = number_format($clickRate, 2);

        return $listStats;
    }

    protected function prepareWelcomeUnsubData($listInformation)
    {
        $mailClass = acym_get('class.mail');

        $returnWelcome = acym_completeLink('lists&task=settings&id='.$listInformation->id.'&edition=1&welcomemailid={mailid}');
        if (empty($listInformation->welcome_id)) {
            $welcomeTmplUrl = acym_completeLink('mails&task=edit&step=editEmail&type=welcome&type_editor=acyEditor&return='.urlencode($returnWelcome));
        } else {
            $welcomeTmplUrl = acym_completeLink('mails&task=edit&id='.$listInformation->welcome_id).'&return='.urlencode($returnWelcome);
        }
        $returnUnsub = acym_completeLink('lists&task=settings&id='.$listInformation->id.'&edition=1&unsubmailid={mailid}');
        if (empty($listInformation->unsubscribe_id)) {
            $unsubTmplUrl = acym_completeLink('mails&task=edit&step=editEmail&type=unsubscribe&type_editor=acyEditor&return='.urlencode($returnUnsub));
        } else {
            $unsubTmplUrl = acym_completeLink('mails&task=edit&id='.$listInformation->unsubscribe_id.'&return='.urlencode($returnUnsub));
        }

        $tmplsData = [
            'welcome' => !empty($listInformation->welcome_id) ? $mailClass->getOneById($listInformation->welcome_id) : '',
            'unsubscribe' => !empty($listInformation->unsubscribe_id) ? $mailClass->getOneById($listInformation->unsubscribe_id) : '',
            'welcomeTmplUrl' => $welcomeTmplUrl,
            'unsubTmplUrl' => $unsubTmplUrl,
        ];

        return $tmplsData;
    }

    public function apply()
    {
        $this->save(false);
    }

    public function save($goToListing = true)
    {
        acym_checkToken();

        $formData = (object)acym_getVar('array', 'list', []);

        $listId = acym_getVar('int', 'id', 0);
        if (!empty($listId)) {
            $formData->id = $listId;
        }

        $allowedFields = acym_getColumns('list');
        $listInformation = new stdClass();
        if (empty($formData->welcome_id)) unset($formData->welcome_id);
        if (empty($formData->unsubscribe_id)) unset($formData->unsubscribe_id);
        foreach ($formData as $name => $data) {
            if (!in_array($name, $allowedFields)) {
                continue;
            }
            $listInformation->{$name} = $data;
        }

        $listInformation->tags = acym_getVar('array', 'list_tags', []);

        $listId = $this->listClass->save($listInformation);

        if (!empty($listId)) {
            acym_setVar('id', $listId);
            acym_enqueueMessage(acym_translation_sprintf('ACYM_LIST_IS_SAVED', $listInformation->name), 'success');
            $this->_saveSubscribersTolist();
        } else {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVING'), 'error');
            if (!empty($this->listClass->errors)) {
                acym_enqueueMessage($this->listClass->errors, 'error');
            }
        }
        if ($goToListing) {
            return $this->listing();
        } else {
            return $this->settings();
        }
    }

    public function deleteOne()
    {
        $listId = acym_getVar('int', 'id');
        $this->listClass->delete($listId);

        return $this->listing();
    }

    private function _saveSubscribersTolist()
    {
        $usersIds = json_decode(acym_getVar('string', 'acym__entity_select__selected', '[]'));
        $usersIdsUnselected = json_decode(acym_getVar('string', 'acym__entity_select__unselected', '[]'));
        $listId = acym_getVar('int', 'id', 0);

        if (empty($listId)) return false;

        acym_arrayToInteger($usersIdsUnselected);
        if (!empty($usersIdsUnselected)) {
            acym_query('UPDATE #__acym_user_has_list SET status = 0, unsubscribe_date = '.acym_escapeDB(acym_date(time(), 'Y-m-d H:i:s')).' WHERE list_id = '.intval($listId).' AND user_id IN ('.implode(', ', $usersIdsUnselected).')');
        }

        acym_arrayToInteger($usersIds);
        if (!empty($usersIds)) {
            acym_query('INSERT IGNORE #__acym_user_has_list (`user_id`, `list_id`, `status`, `subscription_date`) (SELECT id, '.intval($listId).', 1, '.acym_escapeDB(acym_date(time(), 'Y-m-d H:i:s')).' FROM #__acym_user AS user WHERE user.id IN ('.implode(', ', $usersIds).')) ON DUPLICATE KEY UPDATE status = 1');
        }

        return true;
    }

    public function saveSubscribers()
    {
        $this->_saveSubscribersTolist();
        acym_checkToken();
        $listId = acym_getVar('int', 'id', 0);
        acym_setVar('id', $listId);

        $this->settings();
    }

    public function setVisible()
    {
        acym_checkToken();
        $ids = acym_getVar('array', 'elements_checked', []);

        if (!empty($ids)) {
            $this->listClass->setVisible($ids, 1);
        }

        $this->listing();
    }

    public function setInvisible()
    {
        acym_checkToken();
        $ids = acym_getVar('array', 'elements_checked', []);

        if (!empty($ids)) {
            $this->listClass->setVisible($ids, 0);
        }

        $this->listing();
    }

    public function setAjaxListing()
    {
        $showSelected = acym_getVar('string', 'show_selected');
        $matchingListsData = new stdClass();
        $matchingListsData->ordering = 'name';
        $matchingListsData->searchFilter = acym_getVar('string', 'search_lists');
        $matchingListsData->listsPerPage = acym_getVar('string', 'listsPerPage');
        $matchingListsData->idsSelected = json_decode(acym_getVar('string', 'selectedLists'));
        $matchingListsData->idsAlready = json_decode(acym_getVar('string', 'alreadyLists'));
        $matchingListsData->page = acym_getVar('int', 'pagination_page_ajax');
        $matchingListsData->needDisplaySub = acym_getVar('int', 'needDisplaySub');
        $matchingListsData->displayNonActive = acym_getVar('int', 'nonActive');
        if (empty($matchingListsData->page)) {
            $matchingListsData->page = 1;
        }


        $params = [
            'ordering' => $matchingListsData->ordering,
            'search' => $matchingListsData->searchFilter,
            'listsPerPage' => $matchingListsData->listsPerPage,
            'offset' => ($matchingListsData->page - 1) * $matchingListsData->listsPerPage,
            'already' => $matchingListsData->idsAlready,
        ];

        if ($showSelected == 'true') {
            $params['ids'] = $matchingListsData->idsSelected;
        }

        $lists = $this->listClass->getListsWithIdNameCount($params);

        $return = '';

        if (empty($lists['lists'])) {
            $return .= '<h1 class="cell acym__listing__empty__search__modal text-center">'.acym_translation('ACYM_NO_RESULTS_FOUND').'</h1>';
        }

        foreach ($lists['lists'] as $list) {
            if (!empty($matchingListsData->displayNonActive) && $list->active == 0) {
                continue;
            }
            $return .= '<div class="grid-x modal__pagination__listing__lists__in-form__list cell">';

            $return .= '<div class="cell shrink"><input type="checkbox" id="modal__pagination__listing__lists__list'.acym_escape($list->id).'" value="'.acym_escape($list->id).'" class="modal__pagination__listing__lists__list--checkbox" name="lists_checked[]"';

            if (!empty($matchingListsData->idsSelected) && in_array($list->id, $matchingListsData->idsSelected)) {
                $return .= 'checked';
            }

            $return .= '></div><i class="cell shrink fa fa-circle" style="color:'.acym_escape($list->color).'"></i><label class="cell auto" for="modal__pagination__listing__lists__list'.acym_escape($list->id).'"> ';

            $return .= '<span class="modal__pagination__listing__lists__list-name">'.acym_escape($list->name).'</span>';

            if (!empty($matchingListsData->needDisplaySub)) {
                $return .= '<span class="modal__pagination__listing__lists__list-subscribers">('.acym_escape($list->subscribers).')</span>';
            }

            $return .= '</label></div>';
        }

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($lists['total'], $matchingListsData->page, $matchingListsData->listsPerPage);

        $return .= $pagination->displayAjax();

        echo $return;
        exit;
    }

    public function ajaxGetLists()
    {
        $subscribedListsIds = acym_getVar('string', 'ids');
        $echo = '';

        $subscribedListsIds = explode(',', $subscribedListsIds);

        $allLists = $this->listClass->getListsByIds($subscribedListsIds);

        foreach ($allLists as $list) {
            $echo .= '<div class="grid-x cell acym__listing__row">
                        <div class="grid-x medium-5 cell acym__users__display__list__name">
                            <i class="cell shrink fa fa-circle" style="color:'.$list->color.'"></i>
                            <h6 class="cell auto">'.$list->name.'</h6>
                        </div>
                        <div class="medium-2 hide-for-small-only cell text-center acym__users__display__subscriptions__opening"></div>
                        <div class="medium-2 hide-for-small-only cell text-center acym__users__display__subscriptions__clicking"></div>
                        <div id="'.$list->id.'" class="medium-3 cell acym__users__display__list--action acym__user__action--remove">
                            <i class="fa fa-times-circle"></i>
                            <span>'.acym_translation('ACYM_REMOVE').'</span>
                        </div>
                    </div>';
        }
        $return = [];
        $return['html'] = $echo;
        $return['notif'] = acym_translation_sprintf('ACYM_X_CONFIRMATION_SUBSCRIPTION_ADDED_AND_CLICK_TO_SAVE', count($allLists));
        $return = json_encode($return);
        echo $return;
        exit;
    }
}

