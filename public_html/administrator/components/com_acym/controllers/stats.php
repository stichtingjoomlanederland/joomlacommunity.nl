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

class StatsController extends acymController
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[acym_translation('ACYM_STATISTICS')] = acym_completeLink('stats');
        $this->loadScripts = [
            'all' => ['datepicker'],
        ];
    }

    public function saveSendingStatUser($userId, $mailId, $sendDate = null)
    {
        $userStatClass = acym_get('class.userstat');

        if ($sendDate == null) {
            $sendDate = acym_date();
        }

        $userStat = new stdClass();
        $userStat->mail_id = $mailId;
        $userStat->user_id = $userId;
        $userStat->send_date = $sendDate;

        $userStatClass->save($userStat);
    }

    public function listing()
    {
        acym_setVar('layout', 'listing');

        $data = [];
        $data['tab'] = acym_get('helper.tab');
        $data['selectedMailid'] = acym_getVar('int', 'mail_id', '');
        $mailStatClass = acym_get('class.mailstat');
        $data['sentMails'] = $mailStatClass->getAllMailsForStats();
        $data['stats_export'] = acym_tooltip(
            '<button type="button" class="button primary">'.acym_translation('ACYM_EXPORT_REPORT').'</button>',
            '<span class="acy_coming_soon"><i class="acymicon-new_releases acy_coming_soon_icon"></i>'.acym_translation('ACYM_COMING_SOON').'</span>'
        );
        $data['show_date_filters'] = true;
        $data['page_title'] = false;

        $this->prepareDetailedListing($data);
        $this->prepareMailFilter($data);
        $this->prepareClickStats($data);
        $this->preparecharts($data);
        $this->prepareDefaultRoundCharts($data);
        $this->prepareDefaultLineChart($data);

        parent::display($data);
    }

    private function prepareDetailedListing(&$data)
    {
        $userStatClass = acym_get('class.userstat');

        $search = acym_getVar('string', 'detailed_stats_search', '');
        $ordering = acym_getVar('string', 'detailed_stats_ordering', 'send_date');
        $orderingSortOrder = acym_getVar('string', 'detailed_stats_ordering_sort_order', 'desc');

        $detailedStatsPerPage = acym_getCMSConfig('list_limit', 20);
        $page = acym_getVar('int', 'detailed_stats_pagination_page', 1);

        $matchingDetailedStats = $userStatClass->getDetailedStats(
            [
                'ordering' => $ordering,
                'search' => $search,
                'detailedStatsPerPage' => $detailedStatsPerPage,
                'offset' => ($page - 1) * $detailedStatsPerPage,
                'ordering_sort_order' => $orderingSortOrder,
                'mail_id' => $data['selectedMailid'],
            ]
        );

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($matchingDetailedStats['total'], $page, $detailedStatsPerPage);

        $data['search'] = $search;
        $data['ordering'] = $ordering;
        $data['orderingSortOrder'] = $orderingSortOrder;
        $data['pagination'] = $pagination;
        $data['detailed_stats'] = $matchingDetailedStats['detailed_stats'];
    }

    private function prepareMailFilter(&$data)
    {
        $data['mail_filter'] = acym_select(
            [],
            'mail_id',
            $data['selectedMailid'],
            'class="acym__select acym_select2_ajax" data-placeholder="'.acym_translation('ACYM_SELECT_A_MAIL', true).'" data-ctrl="stats" data-task="searchSentMail" data-min="0" data-selected="'.$data['selectedMailid'].'"'
        );
    }

    private function prepareClickStats(&$data)
    {
        if (empty($data['selectedMailid'])) return;

        $urlClickClass = acym_get('class.urlclick');
        $allClickInfo = $urlClickClass->getAllLinkFromEmail($data['selectedMailid']);

        $data['url_click'] = [];
        $data['url_click']['allClick'] = $allClickInfo['allClick'];

        $allPercentage = [];
        foreach ($allClickInfo['urls_click'] as $url) {
            $percentage = 0;
            if (empty($url->click)) {
                $data['url_click'][$url->name] = ['percentage' => $percentage, 'numberClick' => '0'];
            } else {
                $percentage = intval(($url->click * 100) / $allClickInfo['allClick']);
                $data['url_click'][$url->name] = ['percentage' => $percentage, 'numberClick' => $url->click];
            }
            $allPercentage[] = $percentage;
        }

        $mailClass = acym_get('class.mail');
        $data['mailInformation'] = $mailClass->getOneById($data['selectedMailid']);
        $data['mailInformation']->body = $this->_replaceLink($data['mailInformation']->body);


        if (!empty($allPercentage)) {
            $maxPercentage = max($allPercentage);

            foreach ($data['url_click'] as $name => $val) {
                if ($name === 'allClick') continue;
                $percentageRecalc = intval(($val['percentage'] * 100) / $maxPercentage);
                if ($percentageRecalc <= 33) {
                    $data['url_click'][$name]['color'] = '0, 164, 255';
                } elseif ($percentageRecalc <= 66) {
                    $data['url_click'][$name]['color'] = '248, 31, 255';
                } else {
                    $data['url_click'][$name]['color'] = '255, 82, 89';
                }
            }
        }

        $data['url_click'] = json_encode($data['url_click']);
    }

    public function preparecharts(&$data)
    {
        $mailStatClass = acym_get('class.mailstat');

        $data['mail'] = $mailStatClass->getOneByMailId($data['selectedMailid']);
        if (empty($data['mail'])) return;

        $campaignClass = acym_get('class.campaign');
        $urlClickClass = acym_get('class.urlclick');

        $data['mail']->totalMail = $data['mail']->sent + $data['mail']->fail;
        $data['mail']->pourcentageSent = empty($data['mail']->totalMail) ? 0 : intval(($data['mail']->sent * 100) / $data['mail']->totalMail);
        $data['mail']->allSent = empty($data['mail']->totalMail) ? acym_translation_sprintf('ACYM_X_MAIL_SUCCESSFULLY_SENT_OF_X', 0, 0) : acym_translation_sprintf('ACYM_X_MAIL_SUCCESSFULLY_SENT_OF_X', $data['mail']->sent, $data['mail']->totalMail);

        $openRateCampaign = empty($data['selectedMailid']) ? $campaignClass->getOpenRateAllCampaign() : $campaignClass->getOpenRateOneCampaign($data['selectedMailid']);
        $data['mail']->pourcentageOpen = empty($openRateCampaign->sent) ? 0 : intval(($openRateCampaign->open_unique * 100) / $openRateCampaign->sent);
        $data['mail']->allOpen = empty($openRateCampaign->sent) ? acym_translation_sprintf('ACYM_X_MAIL_OPENED_OF_X', 0, 0) : acym_translation_sprintf('ACYM_X_MAIL_OPENED_OF_X', $openRateCampaign->open_unique, $openRateCampaign->sent);

        $clickRateCampaign = $urlClickClass->getClickRate($data['selectedMailid']);
        $data['mail']->pourcentageClick = empty($data['mail']->sent) ? 0 : intval(($clickRateCampaign->click * 100) / $data['mail']->sent);
        $data['mail']->allClick = empty($data['mail']->sent) ? acym_translation_sprintf('ACYM_X_MAIL_CLICKED_OF_X', 0, 0) : acym_translation_sprintf('ACYM_X_MAIL_CLICKED_OF_X', $clickRateCampaign->click, $data['mail']->sent);

        $bounceRateCampaign = empty($data['selectedMailid']) ? $campaignClass->getBounceRateAllCampaign() : $campaignClass->getBounceRateOneCampaign($data['selectedMailid']);
        $data['mail']->pourcentageBounce = empty($data['mail']->sent) ? 0 : intval(($bounceRateCampaign->bounce_unique * 100) / $data['mail']->sent);
        $data['mail']->allBounce = empty($data['mail']->sent) ? acym_translation_sprintf('ACYM_X_BOUNCE_OF_X', 0, 0) : acym_translation_sprintf('ACYM_X_BOUNCE_OF_X', $bounceRateCampaign->bounce_unique, $data['mail']->sent);

        $this->prepareLineChart($data['mail'], $data['selectedMailid']);
    }

    public function prepareDefaultRoundCharts(&$data)
    {
        $charts = [
            'delivery' => [
                'percentage' => 95,
                'text' => 'ACYM_SUCCESSFULLY_SENT',
            ],
            'open' => [
                'percentage' => 25,
                'text' => 'ACYM_OPEN_RATE',
            ],
            'click' => [
                'percentage' => 10,
                'text' => 'ACYM_CLICK_RATE',
            ],
            'fail' => [
                'percentage' => 5,
                'text' => 'ACYM_FAIL',
            ],
        ];

        $data['example_round_chart'] = '';
        foreach ($charts as $type => $oneChart) {
            $data['example_round_chart'] .= '<div class="acym__stats__donut__one-chart medium-3 small-12">';
            $data['example_round_chart'] .= acym_round_chart(
                '',
                $oneChart['percentage'],
                $type,
                '',
                acym_translation($oneChart['text'])
            );
            $data['example_round_chart'] .= '</div>';
        }
    }

    public function prepareDefaultLineChart(&$data)
    {
        $dataMonth = [];
        $dataMonth['Jan 18'] = ['open' => '150', 'click' => '40'];
        $dataDay = [];
        $dataDay['23 Jan'] = ['open' => '150', 'click' => '40'];
        $dataHour = [];
        $dataHour['23 Jan 08:00'] = ['open' => '25', 'click' => '10'];
        $dataHour['23 Jan 09:00'] = ['open' => '50', 'click' => '10'];
        $dataHour['23 Jan 10:00'] = ['open' => '16', 'click' => '10'];
        $dataHour['23 Jan 11:00'] = ['open' => '59', 'click' => '10'];
        $data['example_line_chart'] = acym_line_chart('', $dataMonth, $dataDay, $dataHour);
    }

    private function _replaceLink($body)
    {
        $urlClass = acym_get('class.url');
        if ($urlClass === null) return $body;

        preg_match_all('#href[ ]*=[ ]*"(?!mailto:|\#|ymsgr:|callto:|file:|ftp:|webcal:|skype:|tel:)([^"]+)"#Ui', $body, $results);
        if (empty($results)) return $body;

        $countLinks = array_count_values($results[1]);
        if (array_product($countLinks) != 1) {
            foreach ($results[1] as $key => $url) {
                if ($countLinks[$url] == 1) continue;

                $countLinks[$url]--;

                $toAddUrl = (strpos($url, '?') === false ? '?' : '&').'idU='.$countLinks[$url];

                $posHash = strpos($url, '#');
                if ($posHash !== false) {
                    $newURL = substr($url, 0, $posHash).$toAddUrl.substr($url, $posHash);
                } else {
                    $newURL = $url.$toAddUrl;
                }

                $body = preg_replace('#href="('.preg_quote($url, '#').')"#Uis', 'href="'.$newURL.'"', $body, 1);
            }
        }

        return $body;
    }

    public function setDataForChartLine()
    {
        $newStart = acym_date(acym_getVar('string', 'start'), 'Y-m-d H:i:s');
        $newEnd = acym_date(acym_getVar('string', 'end'), 'Y-m-d H:i:s');
        $mailIdOfCampaign = acym_getVar('int', 'id');

        if ($newStart >= $newEnd) {
            echo 'error';
            exit;
        }

        $statsCampaignSelected = new stdClass();

        $this->prepareLineChart($statsCampaignSelected, $mailIdOfCampaign, $newStart, $newEnd);

        echo @acym_line_chart('', $statsCampaignSelected->month, $statsCampaignSelected->day, $statsCampaignSelected->hour);
        exit;
    }

    private function getValues($modifier, $intervalCode, $campaignOpens, $campaignClicks, $dateCode, $hour = false)
    {
        $opens = [];
        foreach ($campaignOpens as $one) {
            $opens[acym_date(acym_getTime($one->open_date), $dateCode)] = $one->open;
        }

        $clicks = [];
        foreach ($campaignClicks as $one) {
            $clicks[acym_date(acym_getTime($one->date_click), $dateCode)] = $one->click;
        }

        $begin = new DateTime(empty($campaignClicks) ? $campaignOpens[0]->open_date : min([$campaignOpens[0]->open_date, $campaignClicks[0]->date_click]));
        $end = new DateTime(empty($campaignClicks) ? end($campaignOpens)->open_date : max([end($campaignOpens)->open_date, end($campaignClicks)->date_click]));

        $end->modify('+1 '.$modifier);

        $interval = new DateInterval($intervalCode);
        $daterange = new DatePeriod($begin, $interval, $end);

        $result = [];
        foreach ($daterange as $date) {
            $one = acym_date(acym_getTime($date->format('Y-m-d H:i:s')), $dateCode);

            $current = [];
            $current['open'] = empty($opens[$one]) ? 0 : $opens[$one];
            $current['click'] = empty($clicks[$one]) ? 0 : $clicks[$one];

            $key = $hour ? $one.':00' : $one;
            $result[$key] = $current;
        }

        return $result;
    }

    public function prepareLineChart(&$statsCampaignSelected, $mailIdOfCampaign, $newStart = '', $newEnd = '')
    {
        $campaignClass = acym_get('class.campaign');
        $statsCampaignSelected->hasStats = true;

        $campaignOpenByMonth = $campaignClass->getOpenByMonth($mailIdOfCampaign, $newStart, $newEnd);
        $campaignOpenByDay = $campaignClass->getOpenByDay($mailIdOfCampaign, $newStart, $newEnd);
        $campaignOpenByHour = $campaignClass->getOpenByHour($mailIdOfCampaign, $newStart, $newEnd);

        if (empty($campaignOpenByMonth) || empty($campaignOpenByDay) || empty($campaignOpenByHour)) {
            $statsCampaignSelected->hasStats = false;

            return;
        }

        $urlClickClass = acym_get('class.urlclick');
        $campaignClickByMonth = $urlClickClass->getAllClickByMailMonth($mailIdOfCampaign, $newStart, $newEnd);
        $campaignClickByDay = $urlClickClass->getAllClickByMailDay($mailIdOfCampaign, $newStart, $newEnd);
        $campaignClickByHour = $urlClickClass->getAllClickByMailHour($mailIdOfCampaign, $newStart, $newEnd);

        $statsCampaignSelected->month = $this->getValues('day', 'P1M', $campaignOpenByMonth, $campaignClickByMonth, 'M Y');
        $statsCampaignSelected->day = $this->getValues('hour', 'P1D', $campaignOpenByDay, $campaignClickByDay, 'd M Y');
        $statsCampaignSelected->hour = $this->getValues('min', 'PT1H', $campaignOpenByHour, $campaignClickByHour, 'd M Y H', true);

        $allHour = array_keys($statsCampaignSelected->hour);

        $statsCampaignSelected->startEndDateHour = [];
        $statsCampaignSelected->startEndDateHour['start'] = $allHour[0];
        $statsCampaignSelected->startEndDateHour['end'] = end($allHour);
    }

    public function searchSentMail()
    {
        $idSelected = acym_getVar('int', 'id', 0);
        if (!empty($idSelected)) {
            $mailClass = acym_get('class.mail');
            $mail = $mailClass->getOneById($idSelected);
            $name = empty($mail->name) ? '' : $mail->name;

            echo json_encode(
                [
                    'value' => $idSelected,
                    'text' => $name,
                ]
            );
            exit;
        }

        $return = [];
        $search = acym_getVar('cmd', 'search', '');

        $mailstatClass = acym_get('class.mailstat');
        $mails = $mailstatClass->getAllMailsForStats($search);

        foreach ($mails as $oneMail) {
            $return[] = [$oneMail->id, $oneMail->name];
        }

        echo json_encode($return);
        exit;
    }
}

