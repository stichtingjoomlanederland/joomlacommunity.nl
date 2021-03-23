<?php

namespace AcyMailing\Classes;

use AcyMailing\Helpers\MailerHelper;
use AcyMailing\Helpers\PaginationHelper;
use AcyMailing\Libraries\acymClass;

class UserClass extends acymClass
{
    var $table = 'user';
    var $pkey = 'id';
    var $nameColumn = 'email';

    var $sendWelcome = true;
    var $sendUnsubscribe = true;

    var $checkVisitor = true;
    var $restrictedFields = ['id', 'key', 'confirmed', 'active', 'cms_id', 'creation_date'];
    var $allowModif = false;
    var $requireId = false;
    var $sendConf = true;
    var $forceConf = false;
    var $confirmationSentSuccess = false;
    var $newUser = false;
    var $blockNotifications = false;
    var $subscribed = false;
    var $confirmationSentError;

    var $forceConfAdmin = false;

    public function getMatchingElements($settings = [])
    {
        $columns = 'user.*';
        if (!empty($settings['columns'])) {
            foreach ($settings['columns'] as $key => $value) {
                $settings['columns'][$key] = $key === 'join' ? $value : 'user.'.$value;
            }
            $columns = implode(', ', $settings['columns']);
        }

        $query = 'SELECT DISTINCT '.$columns.' FROM #__acym_user AS user';
        $queryCount = 'SELECT COUNT(DISTINCT user.id) FROM #__acym_user AS user';
        $queryStatus = 'SELECT COUNT(DISTINCT user.id) AS number, user.confirmed + user.active*2 AS score FROM #__acym_user AS user';
        $filters = [];

        if (!empty($settings['join'])) $query .= $this->getJoinForQuery($settings['join']);

        if (!empty($settings['joins'])) {
            foreach ($settings['joins'] as $oneJoin) {
                $query .= ' '.$oneJoin.' ';
                $queryCount .= ' '.$oneJoin.' ';
                $queryStatus .= ' '.$oneJoin.' ';
            }
        }

        if (!acym_isAdmin()) {
            $settings['creator_id'] = acym_currentUserId();
            if (empty($settings['creator_id'])) $settings['creator_id'] = '-1';
        }
        if (!empty($settings['creator_id'])) {
            $userGroups = acym_getGroupsByUser($settings['creator_id']);
            $groupCondition = 'list.access LIKE "%,'.implode(',%" OR list.access LIKE "%,', $userGroups).',%"';
            $joinList = ' JOIN #__acym_user_has_list as user_list ON user_list.user_id = user.id JOIN #__acym_list AS list ON list.id = user_list.list_id AND (list.cms_user_id = '.intval(
                    $settings['creator_id']
                ).' OR '.$groupCondition.')';
            $query .= $joinList;
            $queryCount .= $joinList;
            $queryStatus .= $joinList;
        }

        if (!empty($settings['search'])) {
            $searchValue = acym_escapeDB('%'.$settings['search'].'%');
            $searchFilter = 'user.email LIKE '.$searchValue.' OR user.name LIKE '.$searchValue.' OR user.id = '.intval($settings['search']);

            $listingFields = acym_loadResultArray('SELECT `id` FROM #__acym_field WHERE `'.(acym_isAdmin() ? 'back' : 'front').'end_listing` = 1');

            if (!empty($listingFields)) {
                $join = ' LEFT JOIN #__acym_user_has_field AS userfield ON user.id = userfield.user_id AND userfield.field_id IN ('.implode(', ', $listingFields).') ';
                $query .= $join;
                $queryCount .= $join;
                $queryStatus .= $join;
                $searchFilter .= ' OR userfield.value LIKE '.$searchValue;

                $fieldsWithMultipleValues = acym_loadObjectList(
                    'SELECT * 
                    FROM #__acym_field 
                    WHERE id IN ('.implode(', ', $listingFields).')
                        AND `value` LIKE '.acym_escapeDB('%"title":"'.$settings['search'].'"%')
                );
                if (!empty($fieldsWithMultipleValues)) {
                    foreach ($fieldsWithMultipleValues as $oneField) {
                        $oneField->value = json_decode($oneField->value, true);
                        foreach ($oneField->value as $value) {
                            if (stripos($value['title'], $settings['search']) !== false) {
                                $searchFilter .= ' OR (userfield.field_id = '.intval($oneField->id).' AND userfield.value LIKE '.acym_escapeDB('%'.$value['value'].'%').')';
                            }
                        }
                    }
                }
            }

            $filters[] = $searchFilter;
        }

        if (!empty($settings['conditions'])) {
            $filters = array_merge($filters, $settings['conditions']);
        }

        if (!empty($filters)) {
            $queryStatus .= ' WHERE ('.implode(') AND (', $filters).')';
        }

        if (!empty($settings['status'])) {
            $allowedStatus = [
                'active' => 'active = 1',
                'inactive' => 'active = 0',
                'confirmed' => 'confirmed = 1',
                'unconfirmed' => 'confirmed = 0',
            ];
            if (empty($allowedStatus[$settings['status']])) {
                die('Injection denied');
            }
            $filters[] = 'user.'.$allowedStatus[$settings['status']];
        }

        if (isset($settings['hiddenElements'])) {
            if (empty($settings['hiddenElements'])) {
                $filters[] = 'user.id IS NOT NULL';
            } else {
                acym_arrayToInteger($settings['hiddenElements']);
                $filters[] = 'user.id NOT IN('.implode(',', $settings['hiddenElements']).')';
            }
        }

        if (isset($settings['onlyElements'])) {
            if (empty($settings['onlyElements'])) {
                $filters[] = 'user.id IS NULL';
            } else {
                acym_arrayToInteger($settings['onlyElements']);
                $filters[] = 'user.id IN('.implode(',', $settings['onlyElements']).')';
            }
        }

        if (!empty($settings['showOnlySelected'])) {
            if (!empty($settings['selectedUsers'])) {
                acym_arrayToInteger($settings['selectedUsers']);
                $filters[] = 'user.id IN('.implode(',', $settings['selectedUsers']).')';
            } else {
                return ['users' => [], 'total' => 0];
            }
        }

        if (!empty($settings['relation']) && !empty($settings['relation']['target']) && !empty($settings['relation']['target_id']) && isset($settings['relation']['is_related'])) {
            if ($settings['relation']['is_related']) {
                switch ($settings['relation']['target']) {
                    case 'list':
                        $join = ' JOIN #__acym_user_has_list AS userlist ON user.id = userlist.user_id';
                        $filters[] = '(userlist.list_id = '.acym_escapeDB($settings['relation']['target_id']).') AND (userlist.status = 1)';
                        break;
                    default:
                        $join = '';
                        break;
                }
            } else {
                switch ($settings['relation']['target']) {
                    case 'list':
                        $join = ' LEFT JOIN #__acym_user_has_list AS userlist ON user.id = userlist.user_id AND userlist.list_id = '.acym_escapeDB(
                                $settings['relation']['target_id']
                            );
                        $filters[] = '(userlist.user_id IS NULL OR userlist.status = 0)';
                        break;
                    default:
                        $join = '';
                        break;
                }
            }

            $query .= $join;
            $queryCount .= $join;
        }

        if (!empty($filters)) {
            $query .= ' WHERE ('.implode(') AND (', $filters).')';
            $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';
        }

        if (!empty($settings['ordering']) && !empty($settings['ordering_sort_order'])) {
            $query .= ' ORDER BY user.'.acym_secureDBColumn($settings['ordering']).' '.acym_secureDBColumn(strtoupper($settings['ordering_sort_order']));
        }

        if (empty($settings['offset']) || $settings['offset'] < 0) {
            $settings['offset'] = 0;
        }

        if (empty($settings['elementsPerPage']) || $settings['elementsPerPage'] < 1) {
            $pagination = new PaginationHelper();
            $settings['elementsPerPage'] = $pagination->getListLimit();
        }

        acym_query('SET SQL_BIG_SELECTS=1');
        $results['elements'] = acym_loadObjectList($query, '', $settings['offset'], $settings['elementsPerPage']);
        $results['total'] = acym_loadResult($queryCount);

        $usersPerStatus = acym_loadObjectList($queryStatus.' GROUP BY score', 'score');

        for ($i = 0 ; $i < 4 ; $i++) {
            $usersPerStatus[$i] = empty($usersPerStatus[$i]) ? 0 : $usersPerStatus[$i]->number;
        }

        $results['status'] = [
            'all' => array_sum($usersPerStatus),
            'active' => $usersPerStatus[2] + $usersPerStatus[3],
            'inactive' => $usersPerStatus[0] + $usersPerStatus[1],
            'confirmed' => $usersPerStatus[1] + $usersPerStatus[3],
            'unconfirmed' => $usersPerStatus[0] + $usersPerStatus[2],
        ];

        return $results;
    }

    public function getJoinForQuery($joinType)
    {
        if (strpos($joinType, 'join_list') !== false) {
            $listId = explode('-', $joinType);

            return ' LEFT JOIN #__acym_user_has_list AS userlist ON user.id = userlist.user_id AND userlist.status = 1 AND userlist.list_id = '.intval($listId[1]);
        }

        return '';
    }

    public function getOneByCMSId($id)
    {
        $query = 'SELECT * FROM #__acym_user WHERE cms_id = '.intval($id);

        return acym_loadObject($query);
    }


    public function getOneByEmail($email)
    {
        $query = 'SELECT * FROM #__acym_user WHERE email = '.acym_escapeDB($email);

        return acym_loadObject($query);
    }

    public function getByColumnValue($column, $value)
    {
        $userColumns = acym_getColumns('user');
        if (!in_array($column, $userColumns)) return [];

        $query = 'SELECT * FROM #__acym_user WHERE '.acym_secureDBColumn($column).' = '.acym_escapeDB($value);

        return acym_loadObjectList($query);
    }

    public function getUserSubscriptionById($userId, $key = 'id', $includeManagement = false, $visible = false, $needTranslation = false)
    {
        $query = 'SELECT list.id, list.translation, list.name, list.color, list.active, list.visible, list.description, userlist.status, userlist.subscription_date, userlist.unsubscribe_date 
                FROM #__acym_list AS list 
                JOIN #__acym_user_has_list AS userlist 
                    ON list.id = userlist.list_id 
                WHERE userlist.user_id = '.intval($userId);

        if (empty($includeManagement)) {
            $listClass = new ListClass();
            $types = [$listClass::LIST_TYPE_STANDARD, $listClass::LIST_TYPE_FOLLOWUP];
            $query .= ' AND list.type in ("'.implode('","', $types).'")';
        }

        if ($visible) {
            $query .= ' AND list.visible = 1';
        }

        $lists = acym_loadObjectList($query, $key);

        if (acym_isMultilingual() && $needTranslation) {
            $listClass = new ListClass();
            $lists = $listClass->getTranslatedNameDescription($lists);
        }

        return $lists;
    }

    public function getAllListsUserSubscriptionById($userId, $key = 'id', $needTranslation = false)
    {
        $query = 'SELECT list.id, list.translation, list.name, list.color, list.active, list.visible, userlist.status, userlist.subscription_date, userlist.unsubscribe_date 
                FROM #__acym_list AS list 
                LEFT JOIN #__acym_user_has_list AS userlist 
                    ON list.id = userlist.list_id 
                    AND userlist.user_id = '.intval($userId);

        $lists = acym_loadObjectList($query, $key);

        if (acym_isMultilingual() && $needTranslation) {
            $listClass = new ListClass();
            $lists = $listClass->getTranslatedNameDescription($lists);
        }

        return $lists;
    }

    public function getUsersSubscriptionsByIds($usersId, $creatorId = 0)
    {
        $listClass = new ListClass();
        $query = 'SELECT id, user_id, l.color, l.name
                FROM #__acym_list AS l
                JOIN #__acym_user_has_list AS userlist 
                    ON l.id = userlist.list_id
                WHERE user_id IN ('.implode(',', $usersId).')
                AND userlist.status = 1
                AND l.type = '.acym_escapeDB($listClass::LIST_TYPE_STANDARD);

        if (!empty($creatorId)) {
            $userGroups = acym_getGroupsByUser($creatorId);
            $groupCondition = 'l.access LIKE "%,'.implode(',%" OR l.access LIKE "%,', $userGroups).',%"';
            $query .= ' AND (l.cms_user_id = '.intval($creatorId).' OR '.$groupCondition.')';
        }

        return acym_loadObjectList($query);
    }

    public function getCountTotalUsers()
    {
        $query = 'SELECT COUNT(id) FROM #__acym_user';

        return acym_loadResult($query);
    }

    public function getSubscriptionStatus($userId, $listids = [])
    {
        $query = 'SELECT status, list_id
                    FROM #__acym_user_has_list  
                    WHERE user_id = '.intval($userId);
        if (!empty($listids)) {
            acym_arrayToInteger($listids);
            $query .= ' AND list_id IN ('.implode(',', $listids).')';
        }

        return acym_loadObjectList($query, 'list_id');
    }

    public function identify($onlyValue = false)
    {
        $id = acym_getVar('int', 'id', 0);
        $key = acym_getVar('string', 'key', '');

        if (empty($id) || empty($key)) {
            $currentUserid = acym_currentUserId();
            if (!empty($currentUserid)) {
                return $this->getOneByCMSId($currentUserid);
            }
            if (!$onlyValue) {
                acym_enqueueMessage(acym_translation('ACYM_LOGIN'), 'error');
            }

            return false;
        }

        $userIdentified = acym_loadObject('SELECT * FROM #__acym_user WHERE `id` = '.intval($id).' AND `key` = '.acym_escapeDB($key));

        if (!empty($userIdentified)) {
            return $userIdentified;
        }

        if (!$onlyValue) {
            acym_enqueueMessage(acym_translation('ACYM_USER_NOT_FOUND'), 'error');
        }

        return false;
    }

    public function subscribe($userIds, $addLists, $trigger = true)
    {
        if (empty($addLists)) return false;

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        if (!is_array($addLists)) {
            $addLists = [$addLists];
        }

        $listClass = new ListClass();
        $historyClass = new HistoryClass();

        $confirmationRequired = $this->config->get('require_confirmation', 1);
        $subscribedToLists = false;
        $historyData = acym_translationSprintf('ACYM_LISTS_NUMBERS', implode(', ', $addLists));

        foreach ($userIds as $userId) {
            $user = $this->getOneById($userId);
            if (empty($user)) continue;
            $currentSubscription = $this->getUserSubscriptionById($userId, 'id', true);

            $currentlySubscribed = [];
            $currentlyUnsubscribed = [];
            foreach ($currentSubscription as $oneList) {
                if ($oneList->status == 1) {
                    $currentlySubscribed[$oneList->id] = $oneList;
                }
                if ($oneList->status == 0) {
                    $currentlyUnsubscribed[$oneList->id] = $oneList;
                }
            }

            $subscribedLists = [];
            foreach ($addLists as $oneListId) {
                if (empty($oneListId) || !empty($currentlySubscribed[$oneListId])) continue;

                $subscription = new \stdClass();
                $subscription->user_id = $userId;
                $subscription->list_id = $oneListId;
                $subscription->status = 1;
                $subscription->subscription_date = date('Y-m-d H:i:s', time());

                if (empty($currentSubscription[$oneListId])) {
                    acym_insertObject('#__acym_user_has_list', $subscription);
                } elseif (!empty($currentlyUnsubscribed[$oneListId])) {
                    acym_updateObject('#__acym_user_has_list', $subscription, ['user_id', 'list_id']);
                }

                $subscribedLists[] = $oneListId;
                $subscribedToLists = true;
            }

            $historyClass->insert($userId, 'subscribed', [$historyData]);

            if ($trigger) acym_trigger('onAcymAfterUserSubscribe', [&$user, &$subscribedLists]);

            if (($confirmationRequired == 0 || $user->confirmed == 1) && $user->active == 1) {
                $listClass->sendWelcome($userId, $subscribedLists);
            }
        }

        return $subscribedToLists;
    }

    private function registerUnsubUser($userIds)
    {
        $mailId = acym_getVar('int', 'mail_id', 0);
        if (empty($mailId)) return;

        $mailStatClass = new MailStatClass();
        $userStatClass = new UserStatClass();

        $countUnsubscribe = 0;

        foreach ($userIds as $id) {
            $userStat = $userStatClass->getOneByMailAndUserId($mailId, $id);
            $newUserStat = [
                'mail_id' => $mailId,
                'user_id' => $id,
                'unsubscribe' => empty($userStat->unsubscribe) ? 1 : $userStat->unsubscribe + 1,
            ];

            if (empty($userStat->unsubscribe)) $countUnsubscribe++;

            $userStatClass->save($newUserStat);
        }

        $mailStat = [
            'mail_id' => $mailId,
            'unsubscribe_total' => $countUnsubscribe,
        ];

        $mailStatClass->save($mailStat);
    }

    public function unsubscribe($userIds, $lists)
    {
        if (empty($lists)) return false;

        $savingProfile = acym_getVar('int', 'acyprofile', 0);

        if (!is_array($userIds)) $userIds = [$userIds];
        if (!is_array($lists)) $lists = [$lists];

        $listClass = new ListClass();
        $unsubscribedFromLists = false;
        foreach ($userIds as $userId) {
            $user = $this->getOneById($userId);
            if (empty($user)) continue;

            $currentSubscription = $this->getUserSubscriptionById($userId);

            $currentlySubscribed = [];
            $currentlyUnsubscribed = [];
            foreach ($currentSubscription as $oneList) {
                if ($oneList->status == 0) {
                    $currentlyUnsubscribed[$oneList->id] = $oneList;
                }elseif ($oneList->status == 1){
                    $currentlySubscribed[$oneList->id] = $oneList;
                }
            }

            $unsubscribedLists = [];
            foreach ($lists as $oneListId) {
                if (empty($oneListId) || !empty($currentlyUnsubscribed[$oneListId])) continue;

                if ($savingProfile === 1 && empty($currentlySubscribed[$oneListId])) continue;

                $subscription = new \stdClass();
                $subscription->user_id = $userId;
                $subscription->list_id = $oneListId;
                $subscription->status = 0;
                $subscription->unsubscribe_date = date('Y-m-d H:i:s', time());

                if (empty($currentSubscription[$oneListId])) {
                    acym_insertObject('#__acym_user_has_list', $subscription);
                } else {
                    acym_updateObject('#__acym_user_has_list', $subscription, ['user_id', 'list_id']);
                }

                $unsubscribedLists[] = $oneListId;
                $unsubscribedFromLists = true;
            }

            acym_query(
                'DELETE FROM #__acym_queue WHERE user_id = '.intval($userId).' AND mail_id IN (
                    SELECT followup_mail.mail_id FROM #__acym_followup_has_mail AS followup_mail
                    JOIN #__acym_followup AS followup ON followup.id = followup_mail.followup_id AND followup.list_id IN ('.implode(',', $lists).')
                )'
            );

            $historyClass = new HistoryClass();
            $historyData = acym_translationSprintf('ACYM_LISTS_NUMBERS', implode(', ', $lists));
            $historyClass->insert($userId, 'unsubscribed', [$historyData]);

            acym_trigger('onAcymAfterUserUnsubscribe', [&$user, &$unsubscribedLists]);

            $listClass->sendUnsubscribe($userId, $unsubscribedLists);

            if (!empty($unsubscribedLists)) {
                foreach ($unsubscribedLists as $i => $oneListId) {
                    $currentList = $listClass->getOneById($oneListId);
                    $unsubscribedLists[$i] = $currentList->name;
                }
                $this->sendNotification(
                    $user->id,
                    'acy_notification_unsub',
                    ['lists' => implode(', ', $unsubscribedLists)]
                );
            }
        }

        if ($unsubscribedFromLists) $this->registerUnsubUser($userIds);

        return $unsubscribedFromLists;
    }

    public function unsubscribeOnSubscriptions($userId, $listIds)
    {
        if (empty($listIds)) return false;

        if (!is_array($listIds)) $listIds = [$listIds];
        acym_arrayToInteger($listIds);

        $subscribedLists = acym_loadResultArray('SELECT list_id FROM #__acym_user_has_list WHERE user_id = '.intval($userId).' AND list_id IN ('.implode(',', $listIds).')');

        if (!empty($subscribedLists)) $this->unsubscribe($userId, $subscribedLists);

        return true;
    }

    public function removeSubscription($userIds, $listIds = null)
    {
        if (!is_array($userIds)) $userIds = [$userIds];
        if (!is_array($listIds) || empty($listIds) || empty($userIds)) return false;

        acym_arrayToInteger($listIds);
        $query = 'DELETE FROM #__acym_user_has_list WHERE user_id IN ('.implode(',', $userIds).')';
        if (!empty($listIds)) $query .= ' AND list_id IN ('.implode(',', $listIds).')';

        return acym_query($query);
    }

    public function onlyManageableUsers(&$elements)
    {
        if (acym_isAdmin()) return;

        $listClass = new ListClass();
        $manageableLists = $listClass->getManageableLists();
        if (empty($manageableLists)) return;

        $elements = acym_loadResultArray(
            'SELECT user_id 
            FROM #__acym_user_has_list 
            WHERE `list_id` IN ('.implode(',', $manageableLists).') 
                AND `user_id` IN ('.implode(',', $elements).')'
        );
    }

    public function delete($elements)
    {
        if (!is_array($elements)) $elements = [$elements];
        acym_arrayToInteger($elements);
        $this->onlyManageableUsers($elements);

        if (empty($elements)) return 0;

        if (acym_isAdmin() || 'delete' === $this->config->get('frontend_delete_button', 'delete')) {
            acym_query('DELETE FROM #__acym_user_has_list WHERE user_id IN ('.implode(',', $elements).')');
            acym_query('DELETE FROM #__acym_queue WHERE user_id IN ('.implode(',', $elements).')');
            acym_query('DELETE FROM #__acym_user_has_field WHERE user_id IN ('.implode(',', $elements).')');
            acym_query('DELETE FROM #__acym_history WHERE user_id IN ('.implode(',', $elements).')');
            acym_query('DELETE FROM #__acym_user_stat WHERE user_id IN ('.implode(',', $elements).')');

            return parent::delete($elements);
        } else {
            $listClass = new ListClass();
            $manageableLists = $listClass->getManageableLists();
            $this->removeSubscription($elements, $manageableLists);
            acym_query(
                'DELETE queue 
                FROM #__acym_queue AS queue 
                JOIN #__acym_mail_has_list AS maillist 
                    ON queue.mail_id = maillist.mail_id 
                WHERE queue.user_id IN ('.implode(',', $elements).') 
                    AND maillist.list_id IN ('.implode(',', $manageableLists).')'
            );

            return count($elements);
        }
    }

    public function save($user, $customFields = null, $ajax = false)
    {
        if (empty($user->email) && empty($user->id)) return false;


        if (isset($user->email)) {
            $user->email = strtolower($user->email);
            if (!acym_isValidEmail($user->email)) {
                $this->errors[] = acym_translation('ACYM_VALID_EMAIL');

                return false;
            }
        }

        if (empty($user->id)) {
            if (!isset($user->active)) $user->active = 1;

            if (empty($user->language) && acym_isMultilingual()) {
                if (!acym_isAdmin()) $cmsUserLanguage = acym_getCmsUserLanguage();
                $user->language = empty($cmsUserLanguage) ? acym_getLanguageTag() : $cmsUserLanguage;
            }

            $currentUserid = acym_currentUserId();
            $currentEmail = acym_currentUserEmail();
            if ($this->checkVisitor && !acym_isAdmin() && intval($this->config->get('allow_visitor', 1)) != 1 && (empty($currentUserid) || strtolower(
                        $currentEmail
                    ) != $user->email)) {
                $this->errors[] = acym_translation('ACYM_ONLY_LOGGED');

                return false;
            }

            if (empty($user->name) && $this->config->get('generate_name', 1)) {
                $user->name = ucwords(trim(str_replace(['.', '_', ')', ',', '(', '-', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0], ' ', substr($user->email, 0, strpos($user->email, '@')))));
            }

            $source = acym_getVar('string', 'acy_source', '');
            if (empty($user->source) && !empty($source)) $user->source = $source;

            if (empty($user->key)) $user->key = acym_generateKey(14);

            $user->creation_date = date('Y-m-d H:i:s', time());
        } elseif (!empty($user->confirmed)) {
            $oldUser = $this->getOneById($user->id);
            if (!empty($oldUser) && empty($oldUser->confirmed)) {
                $user->confirmation_date = date('Y-m-d H:i:s', time());
                $user->confirmation_ip = acym_getIP();

                acym_trigger('onAcymAfterUserConfirm', [&$user]);
            }
        }

        foreach ($user as $oneAttribute => $value) {
            if (empty($value)) continue;

            $oneAttribute = trim(strtolower($oneAttribute));
            if (!in_array($oneAttribute, $this->restrictedFields)) {
                $user->$oneAttribute = strip_tags($value);
            }

            if (is_numeric($user->$oneAttribute)) continue;

            if (function_exists('mb_detect_encoding')) {
                if (mb_detect_encoding($user->$oneAttribute, 'UTF-8', true) != 'UTF-8') {
                    $user->$oneAttribute = utf8_encode($user->$oneAttribute);
                }
            } elseif (!preg_match(
                '%^(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$%xs',
                $user->$oneAttribute
            )) {
                $user->$oneAttribute = utf8_encode($user->$oneAttribute);
            }
        }

        if (empty($user->id)) {
            if (empty($user->cms_id) && !empty($user->email)) {
                $userCmsID = acym_loadResult(
                    'SELECT '.acym_secureDBColumn($this->cmsUserVars->id).' FROM '.$this->cmsUserVars->table.' WHERE '.acym_secureDBColumn(
                        $this->cmsUserVars->email
                    ).' = '.acym_escapeDB($user->email)
                );
                if (!empty($userCmsID)) $user->cms_id = $userCmsID;
            }
            acym_trigger('onAcymBeforeUserCreate', [&$user]);
        } else {
            acym_trigger('onAcymBeforeUserModify', [&$user]);
        }

        $userID = parent::save($user);

        $fieldClass = new FieldClass();
        $fieldClass->store($userID, $customFields, $ajax);
        if (!empty($fieldClass->errors)) $this->errors = array_merge($this->errors, $fieldClass->errors);

        $historyClass = new HistoryClass();
        if (empty($user->id)) {
            $user->id = $userID;
            $historyClass->insert($user->id, 'created');
            acym_trigger('onAcymAfterUserCreate', [&$user]);
        } else {
            $historyClass->insert($user->id, 'modified');
            acym_trigger('onAcymAfterUserModify', [&$user]);
        }

        $this->sendConfirmation($userID);

        return $userID;
    }

    public function saveForm($ajax = false)
    {
        $allowUserModifications = (bool)($this->config->get('allow_modif', 'data') == 'all') || $this->allowModif;
        $allowSubscriptionModifications = (bool)($this->config->get('allow_modif', 'data') != 'none') || $this->allowModif;

        $user = new \stdClass();
        $connectedUser = $this->identify(true);

        $userData = acym_getVar('array', 'user', []);
        if (!empty($userData)) {
            foreach ($userData as $attribute => $value) {
                $user->$attribute = $value;
            }
        }

        if (empty($user->email)) {
            if (!empty($connectedUser->email)) {
                $user->email = $connectedUser->email;
            }
        }

        if (empty($user->email)) {
            $this->errors[] = acym_translation('ACYM_VALID_EMAIL');

            return false;
        }

        if (!$this->allowModif && !empty($connectedUser)) {
            if ($connectedUser->email != $user->email) {
                $this->errors[] = acym_translation('ACYM_NOT_ALLOWED_MODIFY_USER');

                return false;
            }

            $allowUserModifications = true;
            $allowSubscriptionModifications = true;
        }

        if (!empty($user->email)) {
            if (empty($user->id)) {
                $user->id = 0;
            }
            $existUser = acym_loadObject('SELECT * FROM #__acym_user WHERE email = '.acym_escapeDB($user->email).' AND id != '.intval($user->id));
            if (!empty($existUser->id)) {
                if (!$this->allowModif && !$allowUserModifications) {
                    $this->errors[] = acym_translation('ACYM_ADDRESS_TAKEN');

                    return false;
                } else {
                    $user->id = $existUser->id;
                }
            }
        }

        if (!empty($user->id) && !empty($user->email)) {
            $existUser = $this->getOneById($user->id);
            if (trim(strtolower($user->email)) != strtolower($existUser->email)) {
                $user->confirmed = 0;
            }
        }

        $this->newUser = empty($user->id);
        if (empty($user->id) || $allowUserModifications) {
            if (isset($user->confirmed) && $user->confirmed != 1) {
                $user->confirmed = 0;
            }
            if (isset($user->active) && $user->active != 1) {
                $user->active = 0;
            }
            $customFieldData = acym_getVar('array', 'customField', []);
            $id = $this->save($user, $customFieldData, $ajax);
            $allowSubscriptionModifications = true;
        } else {
            $id = $user->id;
            if (isset($user->confirmed) && empty($user->confirmed)) {
                $this->sendConfirmation($id);
            }
        }

        if (empty($id)) return false;

        $formData = acym_getVar('array', 'data', []);
        acym_setVar('id', $id);

        if (!acym_isAdmin()) {
            $hiddenlistsString = acym_getVar('string', 'hiddenlists', '');
            $hiddenlists = explode(',', $hiddenlistsString);
            acym_arrayToInteger($hiddenlists);
            $visibleSubscription = acym_getVar('array', 'subscription', []);
            $subscribeLists = array_merge($hiddenlists, $visibleSubscription);
            if (!empty($subscribeLists)) {
                foreach ($subscribeLists as $oneListId) {
                    $formData['listsub'][$oneListId] = ['status' => 1];
                }
            }
        }

        $fromProfile = acym_getVar('int', 'acyprofile', 0) == 1;

        if (empty($formData['listsub'])) {
            if (!$fromProfile) $this->sendNotification($id, 'acy_notification_subform');

            $this->sendNotification(
                $id,
                $this->newUser ? 'acy_notification_create' : 'acy_notification_profile'
            );

            return true;
        }

        if (!$allowSubscriptionModifications) {
            $this->requireId = true;
            $this->errors[] = acym_translation('ACYM_NOT_ALLOWED_MODIFY_USER');

            return false;
        }

        $addLists = [];
        $unsubLists = [];
        foreach ($formData['listsub'] as $listID => $oneList) {
            if ($oneList['status'] == 1) {
                $addLists[] = $listID;
            } else {
                $unsubLists[] = $listID;
            }
        }

        $this->subscribed = $this->subscribe($id, $addLists);

        if (!$fromProfile) $this->sendNotification($id, 'acy_notification_subform');

        $this->sendNotification(
            $id,
            $this->newUser ? 'acy_notification_create' : 'acy_notification_profile'
        );

        if (!$this->newUser) $this->unsubscribe($id, $unsubLists);

        return true;
    }

    public function sendConfirmation($userID)
    {
        if (!$this->forceConf && !$this->sendConf) return true;

        if ($this->config->get('require_confirmation', 1) != 1 || (acym_isAdmin() && !$this->forceConfAdmin)) return false;

        $myuser = $this->getOneById($userID);

        if (!empty($myuser->confirmed)) return false;

        $mailerHelper = new MailerHelper();

        $mailerHelper->checkConfirmField = false;
        $mailerHelper->checkEnabled = false;
        $mailerHelper->report = $this->config->get('confirm_message', 0);

        $alias = 'acy_confirm';

        $this->confirmationSentSuccess = $mailerHelper->sendOne($alias, $myuser);
        $this->confirmationSentError = $mailerHelper->reportMessage;
    }

    public function deactivate($userId)
    {
        acym_query('UPDATE `#__acym_user` SET `active` = 0 WHERE `id` = '.intval($userId));
    }

    public function confirm($userId)
    {
        $user = $this->getOneById($userId);
        if (empty($user)) return;

        $confirmDate = date('Y-m-d H:i:s', time());
        $ip = acym_getIP();
        $query = 'UPDATE `#__acym_user`';
        $query .= ' SET `confirmed` = 1, `confirmation_date` = '.acym_escapeDB($confirmDate).', `confirmation_ip` = '.acym_escapeDB($ip);
        $query .= ' WHERE `id` = '.intval($userId).' LIMIT 1';
        $res = acym_query($query);
        if ($res === false) {
            $msg = 'Please contact the admin of this website with the error message:<br />'.substr(strip_tags(acym_getDBError()), 0, 200).'...';
            acym_display($msg, 'error');
            exit;
        }

        acym_trigger('onAcymAfterUserConfirm', [&$user]);
        $this->sendNotification($userId, 'acy_notification_confirm');

        $historyClass = new HistoryClass();
        $historyClass->insert($userId, 'confirmed');

        $listIDs = acym_loadResultArray('SELECT `list_id` FROM `#__acym_user_has_list` WHERE `status` = 1 AND `user_id` = '.intval($userId));

        if (empty($listIDs)) return;

        $listClass = new ListClass();
        $listClass->sendWelcome($userId, $listIDs);
    }

    public function getOneByIdWithCustomFields($id)
    {
        $user = $this->getOneById($id);
        $user = get_object_vars($user);

        $fieldsValue = acym_loadObjectList(
            'SELECT user_field.value as value, field.name as name 
            FROM #__acym_user_has_field as user_field 
            LEFT JOIN #__acym_field as field ON user_field.field_id = field.id 
            WHERE user_field.user_id = '.intval($id),
            'name'
        );

        foreach ($fieldsValue as $key => $value) {
            $fieldsValue[$key] = $value->value;
        }

        return array_merge($user, $fieldsValue);
    }

    public function getAllColumnsUserAndCustomField($inAction = false)
    {
        $return = [];

        $userFields = acym_getColumns('user');
        foreach ($userFields as $value) {
            $return[$value] = $value;
        }

        $fieldClass = new FieldClass();
        $languageFieldId = $this->config->get($fieldClass::LANGUAGE_FIELD_ID_KEY, 0);

        $customFields = acym_loadObjectList(
            'SELECT * FROM #__acym_field WHERE id NOT IN (1, 2, '.intval($languageFieldId).') '.($inAction ? 'AND type != "phone"' : ''),
            'id'
        );
        if (!empty($customFields)) {
            foreach ($customFields as $key => $value) {
                $return[$key] = $value->name;
            }
        }

        return $return;
    }

    public function getAllUserFields($user)
    {
        if (empty($user->id)) return $user;
        $query = 'SELECT field.*, field.value AS field_value, userfield.* 
                    FROM #__acym_field AS field 
                    LEFT JOIN #__acym_user_has_field AS userfield ON field.id = userfield.field_id AND userfield.user_id = '.intval($user->id).' 
                    WHERE field.id NOT IN(1, 2)';

        $allFields = acym_loadObjectList($query);

        foreach ($allFields as $oneField) {
            if (in_array($oneField->type, ['multiple_dropdown', 'radio', 'checkbox', 'single_dropdown'])) {
                $oneField->field_value = json_decode($oneField->field_value, true);
                if (!empty($oneField->value)) {
                    $oneField->value = explode(',', $oneField->value);
                    $values = [];
                    foreach ($oneField->field_value as $oneFieldValue) {
                        foreach ($oneField->value as $oneValue) {
                            if ($oneFieldValue['value'] == $oneValue) $values[] = $oneFieldValue['title'];
                        }
                    }
                    $oneField->value = implode(',', $values);
                }
            }
            $user->{$oneField->namekey} = empty($oneField->value) ? '' : $oneField->value;
        }

        return $user;
    }

    public function getAllSimpleData()
    {
        return acym_loadObjectList('SELECT email, name FROM #__acym_user');
    }

    public function synchSaveCmsUser($user, $isnew, $oldUser = null)
    {
        $source = acym_getVar('string', 'acy_source', '');
        if (empty($source)) acym_setVar('acy_source', ACYM_CMS);

        if (!$this->config->get('regacy', 0)) return;

        $this->checkVisitor = false;
        $this->sendConf = false;

        $regacyForceConf = $this->config->get('regacy_forceconf', 0);
        $cmsUser = new \stdClass();
        $cmsUser->email = trim(strip_tags($user['email']));
        if (!acym_isValidEmail($cmsUser->email)) return;
        if (!empty($user['name'])) $cmsUser->name = trim(strip_tags($user['name']));
        if (!$regacyForceConf) $cmsUser->confirmed = 1;
        $cmsUser->active = 1 - intval($user['block']);
        $cmsUser->cms_id = $user['id'];

        if (!$isnew && !empty($oldUser['email']) && $user['email'] != $oldUser['email']) {
            $acyUser = $this->getOneByEmail($oldUser['email']);
            if (!empty($acyUser)) $cmsUser->id = $acyUser->id;
        }

        if (empty($cmsUser->id) && !empty($cmsUser->cms_id)) {
            $acyUser = $this->getOneByCMSId($cmsUser->cms_id);
            if (!empty($acyUser)) $cmsUser->id = $acyUser->id;
        }

        $acyUser = $this->getOneByEmail($cmsUser->email);
        if (!empty($acyUser)) {
            if (empty($cmsUser->id)) {
                $cmsUser->id = $acyUser->id;
            } elseif ($cmsUser->id != $acyUser->id) {
                $this->delete($acyUser->id);
            }
        } else {
            $cmsUser->source = $source;
        }

        $isnew = (bool)($isnew || empty($cmsUser->id));

        $id = $this->save($cmsUser);

        $confirmationRequired = $this->config->get('require_confirmation', 1);
        if (!$isnew && !$regacyForceConf && $user['block'] == 0 && !empty($oldUser['block']) && $confirmationRequired == 1) {
            $this->confirm($id);
        }


        $currentSubscription = $this->getSubscriptionStatus($id);

        $autoLists = $isnew ? $this->config->get('regacy_autolists') : '';
        $autoLists = explode(',', $autoLists);
        acym_arrayToInteger($autoLists);

        $listsClass = new ListClass();
        $allLists = $listsClass->getAll();

        $visibleLists = acym_getVar('string', 'regacy_visible_lists');
        $visibleLists = explode(',', $visibleLists);
        acym_arrayToInteger($visibleLists);

        $visibleListsChecked = acym_getVar('array', 'regacy_visible_lists_checked', []);
        acym_arrayToInteger($visibleListsChecked);


        if (!$isnew && !empty($visibleLists)) {
            $currentlySubscribedLists = [];
            foreach ($currentSubscription as $oneSubscription) {
                if ($oneSubscription->status == 1) $currentlySubscribedLists[] = $oneSubscription->list_id;
            }
            $unsubscribeLists = array_intersect($currentlySubscribedLists, array_diff($visibleLists, $visibleListsChecked));
            $this->unsubscribe($id, $unsubscribeLists);
        }

        $listsToSubscribe = [];
        foreach ($allLists as $oneList) {
            if (!$oneList->active) continue;
            if (!empty($currentSubscription[$oneList->id]) && $currentSubscription[$oneList->id]->status == 1) continue;

            if (in_array($oneList->id, $visibleListsChecked) || (in_array($oneList->id, $autoLists) && !in_array(
                        $oneList->id,
                        $visibleLists
                    ) && empty($currentSubscription[$oneList->id]))) {
                $listsToSubscribe[] = $oneList->id;
            }
        }

        if (!empty($listsToSubscribe)) $this->subscribe($id, $listsToSubscribe);

        if ($isnew) $this->sendNotification($id, 'acy_notification_create');

        $acymailingUser = $this->getOneById($id);
        if (!empty($user['block']) || !empty($acymailingUser->confirmed)) return;

        if ($isnew || !empty($oldUser['block'])) {
            if ($confirmationRequired && $regacyForceConf) {
                $this->forceConf = true;
                $this->sendConfirmation($id);
            }

            if (!$confirmationRequired && !empty($oldUser['email'])) {
                $listIDs = acym_loadResultArray('SELECT `list_id` FROM `#__acym_user_has_list` WHERE `status` = 1 AND `user_id` = '.intval($id));
                if (empty($listIDs)) return;
                $listsClass->sendWelcome($id, $listIDs);
            }
        }
    }

    public function synchDeleteCmsUser($userEmail)
    {
        $acyUser = $this->getOneByEmail($userEmail);

        if (empty($acyUser)) return;

        if ($this->config->get('regacy', '0') == 1 && $this->config->get('regacy_delete', '0') == 1) {
            $this->delete($acyUser->id);
        } else {
            acym_query('UPDATE #__acym_user SET `cms_id` = 0 WHERE `id` = '.intval($acyUser->id));
        }
    }

    public function getUsersLikeEmail($pattern)
    {
        $query = 'SELECT id, email FROM #__acym_user WHERE email LIKE '.acym_escapeDB('%'.$pattern.'%');

        return acym_loadObjectList($query);
    }

    public function sendNotification($userId, $notification, $params = [])
    {
        if (empty($userId) || $this->blockNotifications) return;

        $notifyUsers = explode(',', $this->config->get($notification));
        if (acym_isAdmin() || empty($notifyUsers)) return;

        $mailer = new MailerHelper();
        $mailer->report = false;
        $mailer->autoAddUser = true;

        $user = $this->getOneById($userId);
        $userField = $this->getAllUserFields($user);
        if (!empty($userField)) {
            foreach ($userField as $map => $value) {
                $mailer->addParam('user:'.$map, $value);
            }
        }

        $rawSubscription = $this->getUserSubscriptionById($userId);
        $subscription = [''];
        foreach ($rawSubscription as $listId => $listData) {
            $currentList = $listData->name.' => ';
            if ($listData->status === '1') {
                $currentList .= acym_translation('ACYM_SUBSCRIBED').' - '.$listData->subscription_date;
            } else {
                $currentList .= acym_translation('ACYM_UNSUBSCRIBED').' - '.$listData->unsubscribe_date;
            }
            $subscription[] = $currentList;
        }
        $mailer->addParam('user:subscription', implode('<br/>', $subscription));

        if (!empty($params)) {
            foreach ($params as $name => $value) {
                $mailer->addParam($name, $value);
            }
        }

        foreach ($notifyUsers as $oneUser) {
            if (!acym_isValidEmail($oneUser)) continue;
            $mailer->sendOne($notification, $oneUser);
        }
    }

    public function getMailHistory($userId)
    {
        $query = 'SELECT user_stat.*, mail.subject, SUM(url_click.click) as click FROM #__acym_user_stat AS user_stat
                  JOIN #__acym_mail AS mail ON mail.id = user_stat.mail_id
                  LEFT JOIN #__acym_url_click AS url_click ON user_stat.mail_id = url_click.mail_id AND url_click.user_id = '.intval($userId).'
                  WHERE user_stat.user_id = '.intval($userId).' GROUP BY mail_id ORDER BY send_date DESC LIMIT 50';

        $mailHistory = acym_loadObjectList($query, 'mail_id');
        $mailClass = new MailClass();

        return $mailClass->decode($mailHistory);
    }

    public function deleteHistoryPeriod()
    {
        if (empty($this->config->get('delete_user_history_enabled', 0))) return;
        $deleteOverSecond = $this->config->get('delete_user_history', 0);
        if (empty($deleteOverSecond)) return;
        $date = time() - $deleteOverSecond;

        $query = 'DELETE FROM #__acym_history WHERE date < '.intval($date);

        try {
            $status = acym_query($query);
            $message = empty($status) ? '' : acym_translationSprintf('ACYM_DELETE_X_ROWS_TABLE_X', $status, strtolower(acym_translation('ACYM_USER_HISTORY')));
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
        }

        return ['status' => $status !== false, 'message' => $message];
    }
}
