<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class acymheaderHelper
{
    public function display($breadcrumb)
    {
        $news = @simplexml_load_file(ACYM_ACYWEBSITE.'acymnews.xml');
        $config = acym_config();
        $header = '';
        if (!empty($news->news)) {

            $currentLanguage = acym_getLanguageTag();

            $latestNews = null;
            $doNotRemind = json_decode($config->get('remindme', '[]'));
            foreach ($news->news as $oneNews) {
                if (!empty($latestNews) && strtotime($latestNews->date) > strtotime($oneNews->date)) break;

                if (empty($oneNews->published) || (strtolower($oneNews->language) != strtolower($currentLanguage) && (strtolower($oneNews->language) != 'default' || !empty($latestNews)))) continue;

                if (!empty($oneNews->extension) && strtolower($oneNews->extension) != 'acymailing') continue;

                if (!empty($oneNews->cms) && strtolower($oneNews->cms) != 'Joomla') continue;

                if (!empty($oneNews->level) && strtolower($oneNews->level) != strtolower($config->get('level'))) continue;

                if (!empty($oneNews->version)) {
                    list($version, $operator) = explode('_', $oneNews->version);
                    if (!version_compare($config->get('version'), $version, $operator)) continue;
                }

                if (in_array($oneNews->name, $doNotRemind)) continue;

                $latestNews = $oneNews;
            }

            if (!empty($latestNews)) {
                $header .= '<div id="acym__header__banner__news" data-news="'.acym_escape($latestNews->name).'">';

                if (!empty($latestNews)) {
                    $header .= $latestNews->content;
                }

                $header .= '</div>';
            }
        }

        $links = [];
        foreach ($breadcrumb as $oneLevel => $link) {
            if (!empty($link)) {
                $oneLevel = '<a href="'.$link.'">'.$oneLevel.'</a>';
            }
            $links[] = '<li>'.$oneLevel.'</li>';
        }

        if (count($links) > 1) {
            $links[count($links) - 1] = str_replace('<li>', '<li class="last_link cell auto"><span class="show-for-sr">Current: </span>', $links[count($links) - 1]);
        }


        $header .= '<div id="acym_header" class="grid-x hide-for-small-only margin-bottom-1">';

        $header .= '<i class="cell medium-shrink acym-logo"></i>';

        $header .= '<div id="acym_global_navigation" class="cell medium-auto"><nav aria-label="You are here:" role="navigation"><ul class="breadcrumbs grid-x">';
        $header .= implode('', $links);
        $header .= '</ul></nav></div>';

        $header .= '<div id="checkVersionArea" class="cell grid-x align-right large-auto check-version-area acym_vcenter margin-right-1">';
        $header .= $this->checkVersionArea();
        $header .= '</div>';

        $config = acym_config();

        $lastLicenseCheck = $config->get('lastlicensecheck', 0);
        $time = time();
        $checking = '0';
        if ($time > $lastLicenseCheck + 604800) $checking = '1';
        if (empty($lastLicenseCheck)) $lastLicenseCheck = $time;

        $header .= '<div class="cell grid-x align-right large-shrink">';
        $header .= acym_tooltip(
            '<a id="checkVersionButton" type="button" class="grid-x align-center button_header medium-shrink acym_vcenter" data-check="'.acym_escape($checking).'"><i class="cell shrink acymicon-autorenew"></i></a>',
            acym_translation('ACYM_LAST_CHECK').' <span id="acym__check__version__last__check">'.acym_date($lastLicenseCheck, 'Y/m/d H:i').'</span>'
        );

        $header .= acym_tooltip('<a type="button" class="grid-x align-center button_header medium-shrink acym_vcenter" target="_blank" href="'.ACYM_DOCUMENTATION.'"><i class="cell shrink fa fa-book"></i></a>', acym_translation('ACYM_DOCUMENTATION'));
        $header .= $this->getNotificationCenter();
        $header .= '</div></div>';

        return $header;
    }

    public function checkVersionArea()
    {
        $config = acym_config();

        $currentLevel = $config->get('level', '');
        $currentVersion = $config->get('version', '');
        $latestVersion = $config->get('latestversion', '');

        $version = '<div id="acym_level_version_area" class="text-right">';
        $version .= '<div id="acym_level">'.ACYM_NAME.' '.$currentLevel.' ';

        if (version_compare($currentVersion, $latestVersion, '>=')) {
            $version .= acym_tooltip('<span class="acym__color__green">'.$currentVersion.'</span>', acym_translation('ACYM_UP_TO_DATE'));
        } elseif (!empty($latestVersion)) {
            if ('wordpress' === ACYM_CMS) {
                $downloadLink = admin_url().'update-core.php';
            } else {
                $downloadLink = ACYM_REDIRECT.'update-acymailing-'.$currentLevel.'&version='.$config->get('version').'" target="_blank';
            }
            $version .= acym_tooltip(
                '<span class="acy_updateversion acym__color__red">'.$currentVersion.'</span>',
                acym_translation_sprintf('ACYM_CLICK_UPDATE', $latestVersion),
                '',
                acym_translation('ACYM_OLD_VERSION'),
                $downloadLink
            );
        }

        $version .= '</div></div>';

        if (!acym_level(1)) return $version;

        $expirationDate = $config->get('expirationdate', 0);
        if (empty($expirationDate) || $expirationDate == -1) return $version;

        $version .= '<div id="acym_expiration" class="text-right cell">';
        if ($expirationDate == -2) {
            $version .= '<div class="acylicence_expired">
                            <a class="acy_attachlicence acymbuttons acym__color__red" href="'.ACYM_REDIRECT.'acymailing-assign" target="_blank">'.acym_translation('ACYM_ATTACH_LICENCE').'</a>
                        </div>';
        } elseif ($expirationDate < time()) {
            $version .= acym_tooltip(
                '<span class="acy_subscriptionexpired acym__color__red">'.acym_translation('ACYM_SUBSCRIPTION_EXPIRED').'</span>',
                acym_translation('ACYM_SUBSCRIPTION_EXPIRED_LINK'),
                '',
                '',
                ACYM_REDIRECT.'renew-acymailing-'.$currentLevel
            );
        } else {
            $version .= '<div class="acylicence_valid">
                            <span class="acy_subscriptionok acym__color__green">'.acym_translation_sprintf('ACYM_VALID_UNTIL', acym_getDate($expirationDate, acym_translation('ACYM_DATE_FORMAT_LC4'))).'</span>
                        </div>';
        }
        $version .= '</div>';

        return $version;
    }

    public function getNotificationCenter()
    {
        $config = acym_config();
        $notifications = json_decode($config->get('notifications', '{}'), true);
        $message = '';
        $notificationLevel = 0;
        if (!empty($_SESSION['acym_success'])) {
            $message = $_SESSION['acym_success'];
            $_SESSION['acym_success'] = '';
            $notificationLevel = 1;
        }

        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                if ($notification['read']) continue;
                if ($notification['level'] == 'info' && $notificationLevel < 2) $notificationLevel = 2;
                if ($notification['level'] == 'warning' && $notificationLevel < 3) $notificationLevel = 3;
                if ($notification['level'] == 'error' && $notificationLevel < 4) $notificationLevel = 4;
            }
        }

        $iconToDisplay = '';
        $tooltip = '';

        switch ($notificationLevel) {
            case 0:
                $iconToDisplay = 'fa-bell-o';
                $notificationLevel = '';
                break;
            case 1:
                $iconToDisplay = 'fa-check-circle acym__color__green';
                $notificationLevel = 'acym__header__notification__button__success acym__header__notification__pulse';
                $tooltip = 'data-tooltip="'.acym_escape($message).'" data-tooltip-position="left"';
                break;
            case 2:
                $iconToDisplay = 'fa-bell-o acym__color__blue';
                $notificationLevel = 'acym__header__notification__button__info';
                break;
            case 3:
                $iconToDisplay = 'fa-exclamation-triangle acym__color__orange';
                $notificationLevel = 'acym__header__notification__button__warning';
                break;
            case 4:
                $iconToDisplay = 'fa-exclamation-circle acym__color__red';
                $notificationLevel = 'acym__header__notification__button__error';
                break;
        }

        $notificationCenter = '<div class="cell grid-x align-center acym_vcenter medium-shrink acym__header__notification '.$notificationLevel.' button_header cursor-pointer" '.$tooltip.'><i class="fa '.$iconToDisplay.'"></i>';
        $notificationCenter .= '<div class="cell grid-x acym__header__notification__center align-center">';
        $notificationCenter .= $this->getNotificationCenterInner($notifications);
        $notificationCenter .= '</div></div>';

        return $notificationCenter;
    }

    public function getNotificationCenterInner($notifications)
    {
        $notificationCenter = '';
        if (empty($notifications)) {
            $notificationCenter .= '<div class="cell grid-x acym__header__notification__one acym__header__notification__one__empty acym_vcenter">';
            $notificationCenter .= '<h2 class="cell text-center">'.acym_translation('ACYM_YOU_DONT_HAVE_NOTIFICATIONS').'</h2>';
            $notificationCenter .= '</div>';
        } else {
            $notificationCenter .= '<div class="cell grid-x acym__header__notification__toolbox"><p class="cell auto">'.acym_translation('ACYM_NOTIFICATIONS').'</p><div class="cell shrink cursor-pointer acym__header__notification__toolbox__remove text-right">'.acym_translation('ACYM_DELETE_ALL').'</div></div>';
            foreach ($notifications as $key => $notif) {
                if (strlen($notif['message']) > 150) $notif['message'] = substr($notif['message'], 0, 150).'...';
                $logo = $notif['level'] == 'info' ? 'fa-bell' : ($notif['level'] == 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
                $read = $notif['read'] ? 'acym__header__notification__one__read' : '';
                $notificationCenter .= '<div class="'.$read.' cell grid-x acym__header__notification__one acym_vcenter acym_vcenter acym__header__notification__one__'.$notif['level'].'">';
                $notificationCenter .= '<div class="cell small-3 align-center acym__header__notification__one__icon"><i class="cell fa '.$logo.'"></i></div>';
                $notificationCenter .= '<div class="cell grid-x small-8"><p class="cell acym__header__notification__message">'.$notif['message'];
                $notificationCenter .= '<div class="cell acym__header__notification__one__date">'.acym_date($notif['date']).'</div></div>';
                $notificationCenter .= '<i class="cell small-1 acym__header__notification__one__delete acymicon-close" data-id="'.acym_escape($key).'"></i>';
                $notificationCenter .= '</div>';
            }
        }

        return $notificationCenter;
    }

    public function addNotification($notif)
    {
        if ($notif->level == 'success') {
            $_SESSION['acym_success'] = $notif->message;

            return true;
        }
        $config = acym_config();

        $notifications = json_decode($config->get('notifications', '[]'), true);

        $notif->message = strip_tags($notif->message);

        foreach ($notifications as $key => $oneNotif) {
            if ($oneNotif['message'] === $notif->message && $oneNotif['level'] === $notif->level) unset($notifications[$key]);
        }
        $notifications = array_values($notifications);

        $notif->id = uniqid();
        array_unshift($notifications, $notif);

        if (count($notifications) > 10) unset($notifications[10]);

        $config->save(['notifications' => json_encode($notifications)]);

        return $notif->id;
    }
}

