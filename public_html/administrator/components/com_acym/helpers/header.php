<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.1.4
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class acymheaderHelper
{
    function display($breadcrumb)
    {
        $news = @simplexml_load_file(ACYM_ACYWEBSITE.'acymnews.xml');
        $config = acym_config();
        $header = '';
        if (!empty($news->news)) {

            $currentLanguage = acym_getLanguageTag();

            $latestNews = null;
            $review = null;
            $doNotRemind = json_decode($config->get('remindme', '[]'));
            foreach ($news->news as $oneNews) {
                if (!empty($oneNews->name) && $oneNews->name == 'reviews') {
                    if (!empty($oneNews->published) && ($config->get('install_date') + $oneNews->wait) < time() && !in_array($oneNews->name, $doNotRemind) && version_compare($config->get('version'), $oneNews->version, '>=')) $review = $oneNews;
                    continue;
                }
                if (!empty($latestNews) && strtotime($latestNews->date) > strtotime($oneNews->date)) break;

                if (empty($oneNews->published) || (strtolower($oneNews->language) != strtolower($currentLanguage) && (strtolower($oneNews->language) != 'default' || !empty($latestNews)))) continue;

                if (!empty($oneNews->extension) && strtolower($oneNews->extension) != 'acymailing') continue;

                if (!empty($oneNews->cms) && strtolower($oneNews->cms) != 'Joomla') continue;

                if (!empty($oneNews->level) && strtolower($oneNews->level) != strtolower($config->get('level'))) continue;

                if (!empty($oneNews->version)) {
                    list($version, $operator) = explode('_', $oneNews->version);
                    if (!version_compare($config->get('version'), $version, $operator)) continue;
                }
                $latestNews = $oneNews;
            }
            if (!empty($latestNews) || !empty($review)) {
                $header .= '<div id="acym__header__banner__news">';
                if (!empty($review)) {
                    if ('wordpress' === ACYM_CMS) {
                        $header .= str_replace('{__LINK_5_STARS__}', 'https://wordpress.org/support/plugin/acymailing/reviews/', $review->content);
                    } else {
                        $header .= str_replace('{__LINK_5_STARS__}', 'https://www.acyba.com/component/updateme/reviews/save.html?review_note=5&url='.urlencode(ACYM_LIVE), $review->content);
                    }

                    $header = str_replace('{__URL_SITE__}', urlencode(ACYM_LIVE), $header);
                }

                if (!empty($latestNews)) {
                    $header .= $latestNews;
                }

                $header .= '</div>';
            }
        }
        $links = array();
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

        $header .= '<div id="checkVersionArea" class="cell text-right medium-auto check-version-area">';
        $header .= $this->checkVersionArea();
        $header .= '</div>';

        $config = acym_config();
        $lastLicenseCheck = $config->get('lastlicensecheck', '');
        $checking = false;

        if (empty($lastLicenseCheck) || $lastLicenseCheck < (time() - 604800)) {
            $checking = "check";
        }
        $header .= acym_tooltip('<a id="checkVersionButton" type="button" class="button button-secondary medium-shrink" data-check="'.$checking.'">'.acym_translation('ACYM_CHECK_MY_VERSION').'</a>', acym_translation('ACYM_LAST_CHECK').' <span id="acym__check__version__last__check">'.acym_date($config->get('lastlicensecheck', time()), 'Y/m/d H:i').'</span>');

        $header .= '<a type="button" class="button medium-shrink" target="_blank" href="'.ACYM_UPDATEMEURL.'doc&task=doc&product=acymailing&for='.(empty($_REQUEST['ctrl']) ? 'dashboard' : $_REQUEST['ctrl']).'-'.$_REQUEST['layout'].'">'.acym_translation('ACYM_DOCUMENTATION').'</a>';
        $header .= '</div>';

        return $header;
    }

    public function checkVersionArea()
    {

        $config = acym_config();
        if (!acym_isAllowed($config->get('acl_configuration_manage', 'all'))) {
            return '';
        }

        $currentLevel = $config->get('level', '');
        $currentVersion = $config->get('version', '');
        $latestVersion = $config->get('latestversion', '');

        $version = '<div id="acym_level_version_area">';
        $version .= '<div id="acym_level">'.ACYM_NAME.' '.$currentLevel.' '.$currentVersion.'</div>';

        $version .= '<div id="acyma_version">';

        if (version_compare($currentVersion, $latestVersion, '>=')) {
            $version .= '<div class="acyversion_uptodate acym__color__green myacymailingbuttons">'.acym_translation('ACYM_UP_TO_DATE').'</div>';
        } elseif (!empty($latestVersion)) {
            $version .= '<div class="acyversion_needtoupdate acymbuttons"><a class="acy_updateversion acym__color__light-blue" href="';
            if ('wordpress' === ACYM_CMS) {
                $version .= admin_url().'update-core.php">';
            } else {
                $version .= ACYM_REDIRECT.'update-acymailing-'.$currentLevel.'" target="_blank">';
            }
            $version .= '<i class="acyicon-import"></i>'.acym_translation_sprintf('ACYM_UPDATE_NOW', $latestVersion).'</a></div>';
        }

        $version .= '</div></div>';

        if (acym_level(1)) {
            $expirationDate = $config->get('expirationdate', '');
            $version .= '<div id="acym_expiration">';
            if (empty($expirationDate) || $expirationDate == -1) {
                $version .= '</div>';
            } elseif ($expirationDate == -2) {
                $version .= '<div class="acylicence_expired"><a class="acy_attachlicence acymbuttons acym__color__red" href="'.ACYM_REDIRECT.'acymailing-assign" target="_blank">'.acym_translation('ACYM_ATTACH_LICENCE').' :<br>'.acym_translation('ACYM_ATTACH_LICENCE_BUTTON').'</a></div></div>';
            } elseif ($expirationDate < time()) {
                $version .= '<div class="acylicence_expired"><span class="acylicenceinfo">'.acym_translation('ACYM_SUBSCRIPTION_EXPIRED').'</span><a class="acy_subscriptionexpired acymbuttons" href="'.ACYM_REDIRECT.'renew-acymailing-'.$currentLevel.'" target="_blank"><i class="acyicon-renew"></i>'.acym_translation('ACYM_SUBSCRIPTION_EXPIRED_LINK').'</a></div></div>';
            } else {
                $version .= '<div class="acylicence_valid acymbuttons"><span class="acy_subscriptionok acym__color__green">'.acym_translation_sprintf('ACYM_VALID_UNTIL', acym_getDate($expirationDate, acym_translation('ACYM_DATE_FORMAT_LC4'))).'</span></div></div>';
            }
        }

        return $version;
    }
}
