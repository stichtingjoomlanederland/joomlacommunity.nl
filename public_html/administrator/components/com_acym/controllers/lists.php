<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.3.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class ListsController extends acymController
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[acym_translation('ACYM_LISTS')] = acym_completeLink('lists');
        $this->edition = acym_getVar('int', 'edition', 0);
        $this->listClass = acym_get('class.list');
        $this->loadScripts = [
            'edit' => ['colorpicker'],
            'save' => ['colorpicker'],
            'all' => ['vue-applications'],
        ];
    }

    public function listing()
    {
        acym_setVar("layout", "listing");

        $searchFilter = acym_getVar('string', 'lists_search', '');
        $tagFilter = acym_getVar('string', 'lists_tag', '');
        $ordering = acym_getVar('string', 'lists_ordering', 'id');
        $status = acym_getVar('string', 'lists_status', '');
        $format = acym_getVar('string', 'global_listingformat', 'list');
        $orderingSortOrder = acym_getVar('string', 'lists_ordering_sort_order', 'desc');

        $listsPerPage = acym_getCMSConfig('list_limit', 20);
        $page = acym_getVar('int', 'lists_pagination_page', 1);

        $listClass = acym_get('class.list');
        $matchingLists = $listClass->getMatchingElements(
            [
                'ordering' => $ordering,
                'search' => $searchFilter,
                'elementsPerPage' => $listsPerPage,
                'offset' => ($page - 1) * $listsPerPage,
                'tag' => $tagFilter,
                'status' => $status,
                'ordering_sort_order' => $orderingSortOrder,
            ]
        );

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($matchingLists['total'], $page, $listsPerPage);

        $data = [
            "lists" => $matchingLists['elements'],
            "tags" => acym_get('class.tag')->getAllTagsByType('list'),
            "pagination" => $pagination,
            "search" => $searchFilter,
            "tag" => $tagFilter,
            "ordering" => $ordering,
            "listNumberPerStatus" => $matchingLists["status"],
            "status" => $status,
            "format" => $format,
            "orderingSortOrder" => $orderingSortOrder,
        ];

        parent::display($data);
    }

    public function subscribers()
    {
        acym_setVar("layout", "subscribers");
        $listId = acym_getVar("int", "id", 0);

        if (!$listId) {
            $this->listing();

            return;
        }

        $listData = [];
        $listData['listInformation'] = $this->listClass->getOneById($listId);

        $link = $this->edition ? acym_completeLink('lists&task=edit&step=subscribers&edition=1&id=').$listId : '';
        $this->breadcrumb[acym_escape($listData['listInformation']->name)] = $link;

        if (is_null($listData['listInformation'])) {
            acym_enqueueMessage(acym_translation("ACYM_LIST_DOESNT_EXIST"), 'error');
            $this->listing();

            return;
        }

        parent::display($listData);
    }

    public function unsubscribeUser()
    {
        acym_checkToken();
        $listId = acym_getVar("int", "id", 0);
        $userId = acym_getVar("int", "userid", 0);

        if (!empty($listId) && !empty($userId)) {
            $userClass = acym_get('class.user');
            if ($userClass->unsubscribe($userId, $listId)) {
                acym_enqueueMessage(acym_translation("ACYM_THE_USER_HAS_BEEN_UNSUBSCRIBED"), 'success');
            } else {
                acym_enqueueMessage(acym_translation("ACYM_THE_USER_CANT_BE_UNSUBSCRIBED"), 'error');
            }
        } else {
            acym_enqueueMessage(acym_translation("ACYM_THE_USER_CANT_BE_UNSUBSCRIBED"), 'error');
        }
        $this->subscribers();
    }

    public function unsubscribeUsers()
    {
        acym_checkToken();
        $ids = acym_getVar('array', 'elements_checked', []);
        $userClass = acym_get('class.user');
        foreach ($ids as $id) {
            if (!empty($id)) {
                $userClass->unsubscribe($id, acym_getVar('int', 'id', 0));
            }
        }

        $this->edit();
    }

    public function unsubscribeSelected()
    {
        acym_checkToken();
        $listId = acym_getVar("int", "id", 0);
        $selectedUsers = acym_getVar("array", "elements_checked", []);


        if (!empty($selectedUsers) && !empty($listId)) {
            $userClass = acym_get('class.user');
            if ($userClass->unsubscribe($selectedUsers, $listId)) {
                acym_enqueueMessage(acym_translation("ACYM_SUCCESSFULLY_UNSUBSCRIBED"), 'success');
            } else {
                acym_enqueueMessage(acym_translation("ACYM_ERROR_DURING_UNSUBSCRIBE"), 'error');
            }
        } else {
            acym_enqueueMessage(acym_translation("ACYM_THE_USER_CANT_BE_UNSUBSCRIBED"), 'error');
        }

        acym_setVar('search', acym_getVar('string', 'subscribers_search', ''));
        acym_setVar('status', acym_getVar('string', 'subscribers_status', ''));
        acym_setVar('list_limit', acym_getCMSConfig('list_limit', 20));
        acym_setVar('pagination_page', acym_getVar('int', 'subscribers_pagination_page', ''));

        $this->subscribers();
    }

    public function settings()
    {
        acym_setVar("layout", "settings");
        $listId = acym_getVar("int", "id", 0);
        $listTagsName = [];

        $randColor = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];

        if (!$listId) {
            $listInformation = new stdClass();
            $listInformation->id = "";
            $listInformation->name = "";
            $listInformation->active = 1;
            $listInformation->from_name = "";
            $listInformation->from_email = "";
            $listInformation->reply_to_name = "";
            $listInformation->reply_to_email = "";
            $listInformation->color = '#'.$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)].$randColor[rand(0, 15)];

            $this->breadcrumb[acym_translation('ACYM_NEW_LIST')] = acym_completeLink('lists&task=edit&step=settings');
        } else {

            $listsTags = acym_get('class.tag')->getAllTagsByElementId('list', $listId);

            foreach ($listsTags as $oneTag) {
                $listTagsName[] = $oneTag;
            }

            $listInformation = $this->listClass->getOneById($listId);
            if (is_null($listInformation)) {
                acym_enqueueMessage(acym_translation("ACYM_LIST_DOESNT_EXIST"), 'error');
                $this->listing();

                return;
            }

            $this->breadcrumb[acym_escape($listInformation->name)] = acym_completeLink('lists&task=edit&step=settings&id=').$listId;
            acym_setVar('edition', '1');
        }

        $listData = [
            "listInformation" => $listInformation,
            "allTags" => acym_get('class.tag')->getAllTagsByType('list'),
            "listTagsName" => $listTagsName,
        ];

        parent::display($listData);
    }

    private function selectEmail($type)
    {
        acym_setVar('layout', $type);
        $listId = acym_getVar("int", "id");
        if (empty($listId)) {
            return $this->listing();
        }

        $searchFilter = acym_getVar('string', $type.'_search', '');

        $mailsPerPage = 12;
        $page = acym_getVar('int', $type.'_pagination_page', 1);

        $mailClass = acym_get('class.mail');
        $mails = $mailClass->getMailsByType(
            $type,
            [
                'mailsPerPage' => $mailsPerPage,
                'offset' => ($page - 1) * $mailsPerPage,
                'search' => $searchFilter,
            ]
        );

        $currentList = $this->listClass->getOneById($listId);
        $columnId = $type.'_id';
        $selectedTemplate = !empty($currentList->$columnId) ? $currentList->$columnId : '';
        $this->breadcrumb[acym_escape($currentList->name)] = acym_completeLink('lists&task=edit&step='.$type.'&id=').$listId.($this->edition ? '&edition=1' : '');

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($mails['total'], $page, $mailsPerPage);

        $data = [
            $type.'Mails' => $mails['mails'],
            'pagination' => $pagination,
            'search' => $searchFilter,
            'selected'.ucfirst($type) => $selectedTemplate,
            'listId' => $listId,
        ];

        parent::display($data);
    }

    public function welcome()
    {
        $listId = acym_getVar("int", "id", 0);
        $listInformation = $this->listClass->getOneById($listId);

        if (is_null($listInformation)) {
            acym_enqueueMessage(acym_translation("ACYM_LIST_DOESNT_EXIST"), 'error');
            $this->listing();

            return;
        }

        $this->selectEmail('welcome');
    }

    public function unsubscribe()
    {
        $listId = acym_getVar("int", "id", 0);
        $listInformation = $this->listClass->getOneById($listId);

        if (is_null($listInformation)) {
            acym_enqueueMessage(acym_translation("ACYM_LIST_DOESNT_EXIST"), 'error');
            $this->listing();

            return;
        }

        $this->selectEmail('unsubscribe');
    }

    public function saveSettings()
    {
        acym_checkToken();

        $listUseSameReplyTo = acym_getVar("boolean", "list_use_same_reply-to");
        $formData = (object)acym_getVar("array", "list", []);

        $listId = acym_getVar('int', 'id', 0);
        if (!empty($listId)) {
            $formData->id = $listId;
        }

        $allowedFields = acym_getColumns('list');
        $listInformation = new stdClass();
        foreach ($formData as $name => $data) {
            if (!in_array($name, $allowedFields)) {
                continue;
            }
            $listInformation->{$name} = $data;
        }

        $listInformation->tags = acym_getVar("array", "list_tags", []);

        if ($listUseSameReplyTo) {
            $listInformation->reply_to_name = $listInformation->from_name;
            $listInformation->reply_to_email = $listInformation->from_email;
        }

        $listId = $this->listClass->save($listInformation);

        if (!empty($listId)) {
            acym_setVar('id', $listId);
            acym_enqueueMessage(acym_translation_sprintf("ACYM_LIST_IS_SAVED", $listInformation->name), 'success');
        } else {
            acym_enqueueMessage(acym_translation("ACYM_ERROR_SAVING"), 'error');
            if (!empty($this->listClass->errors)) {
                acym_enqueueMessage($this->listClass->errors, 'error');
            }
            acym_setVar('nextstep', 'listing');
        }

        return $this->edit();
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
            acym_query('INSERT IGNORE #__acym_user_has_list (`user_id`, `list_id`, `status`, `subscription_date`) (SELECT id, '.intval($listId).', 1, '.acym_escapeDB(acym_date(time(), 'Y-m-d H:i:s')).' FROM #__acym_user) ON DUPLICATE KEY UPDATE status = 1');
        }


        return true;
    }

    public function saveSubscribers()
    {
        $this->_saveSubscribersTolist();
        acym_checkToken();
        $listId = acym_getVar("int", "id", 0);
        acym_setVar('id', $listId);

        $this->edit();
    }

    public function saveSubscribersExit()
    {
        $this->_saveSubscribersTolist();
        $this->listing();

        return true;
    }

    public function saveWelcome()
    {
        $this->saveMail();
    }

    public function saveUnsubscribe()
    {
        $this->saveMail();
    }

    private function saveMail()
    {
        acym_checkToken();

        $listId = acym_getVar('int', "id");
        $mail = acym_getVar('int', 'mailSelected') == 0 ? null : acym_getVar('int', 'mailSelected');
        $typeMail = acym_getVar('string', 'typeMail');

        $listData = new stdClass();
        $listData->id = $listId;

        if ($typeMail == 'welcome') {
            $listData->welcome_id = $mail;
        } else {
            $listData->unsubscribe_id = $mail;
        }

        $listId = $this->listClass->save($listData);

        if (empty($listId)) {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVING_LIST_TEMPLATE'), 'error');
            if (!empty($this->listClass->errors)) {
                acym_enqueueMessage($this->listClass->errors, 'error');
            }
            acym_setVar('nextstep', 'listing');
        } else {
            acym_enqueueMessage(acym_translation("ACYM_SUCCESSFULLY_SAVED"), 'success');
        }

        return $this->edit();
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

