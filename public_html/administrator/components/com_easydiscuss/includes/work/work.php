<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussWork extends EasyDiscuss
{
	private $inputdate = null;
	private $enabled = null;
	private $options = null;

	// status
	private $isOnline = null;
	private $isOffline = null;
	private $isOffday = null;
	private $isHoliday = null;
	private $holidays = null;

	// options
	private $showPopbox = null;
	private $showTime = null;
	private $includeHoliday = null;
	private $showHolidayDesc = null;


	public function __construct(EasyDiscussDate $date = null, $options = array())
	{
		parent::__construct();

		$this->enabled = $this->config->get('main_work_schedule');
		$this->inputdate = $date;
		$this->options = $options;

		if ($this->enabled && $this->inputdate) {
			// setting options
			$this->showPopbox = isset($options['showPopbox']) ? $options['showPopbox'] : true;
			$this->showTime = isset($options['showTime']) ? $options['showTime'] : true;
			$this->includeHoliday = isset($options['includeHoliday']) ? $options['includeHoliday'] : true;
			$this->showHolidayDesc = isset($options['showHolidayDesc']) ? $options['showHolidayDesc'] : true;

			// we will compute here.
			$this->init();
		}
	}

	private function setOffline($isOffday = false)
	{
		$this->isOnline = false;
		$this->isOffline = true;
		$this->isHoliday = false;
		$this->isOffday = $isOffday;
	}

	private function setOnline()
	{
		$this->isOnline = true;
		$this->isOffline = false;
		$this->isHoliday = false;
	}

	private function setHolidays($holidays)
	{
		$this->isOnline = false;
		$this->isOffline = true;
		$this->isHoliday = true;
		$this->holidays = $holidays;
	}


	private function init()
	{

		$daysMap = array('0' => 'sun',
			'1' => 'mon',
			'2' => 'tue',
			'3' => 'wed',
			'4' => 'thu',
			'5' => 'fri',
			'6' => 'sat');

		$myDate = $this->inputdate;

		$dayIndex = $myDate->format('w');
		$myDay = $daysMap[$dayIndex];

		$myDateTime = $myDate->display('Y-m-d H:i');

		//today
		$today = ED::date()->display('Y-m-d H:i:s');


		$dateSegments = explode(' ', $today);
		$todayDate = $dateSegments[0];

		// config time
		$startTime = $this->config->get('main_work_starthour') . ':' . $this->config->get('main_work_startminute');
		$endTime = $this->config->get('main_work_endhour') . ':' . $this->config->get('main_work_endminute');

		// lets check if today is a holiday or not.
		$model = ED::model('Holidays');
		$holidays = $model->getTodayHolidays($myDateTime);

		if ($holidays) {
			$this->setHolidays($holidays);
		} else {
			if ($this->config->get('main_work_' . strtolower($myDay))) {

				// now we need to check againts the time duration
				$start = $todayDate . ' ' . $startTime;
				$end = $todayDate . ' ' . $endTime;

				if ($myDateTime > $start && $myDateTime < $end) {
					$this->setOnline();
				} else {
					$this->setOffline();
				}

			} else {
				$this->setOffline(true);
			}
		}

	}

	public function enabled()
	{
		return $this->enabled;
	}

	public function status()
	{
		if (! $this->enabled) {
			return false;
		}

		if ($this->isOnline) {
			return JText::_('COM_EASYDISCUSS_WORK_ONLINE');
		} else if ($this->isHoliday) {
			return JText::_('COM_EASYDISCUSS_WORK_OFFLINE');
		} else {
			return JText::_('COM_EASYDISCUSS_WORK_OFFLINE');
		}
	}

	/**
	 * Generates the html codes for online / offline status
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function html()
	{
		if (! $this->enabled) {
			return '';
		}

		$theme = ED::themes();

		$label = '';
		$namespace = "site/work/";


		if ($this->isHoliday) {
			$namespace .= 'holiday';
			$label = JText::_('COM_EASYDISCUSS_WORK_OFFLINE');

		} else if ($this->isOnline) {
			$namespace .= 'online';

			$label = JText::_('COM_EASYDISCUSS_WORK_ONLINE');
		} else {
			$label = JText::_('COM_EASYDISCUSS_WORK_OFFLINE');

			if ($this->isOffday) {
				$namespace .= 'offday';
			} else {
				$namespace .= 'offline';
			}
		}


		$startTime = $this->config->get('main_work_starthour') . ':' . $this->config->get('main_work_startminute');

		if ($this->config->get('main_work_hourformat', '12') == '12') {
			$startTime = ($this->config->get('main_work_starthour') > 12) ? $this->config->get('main_work_starthour') - 12 : (int) $this->config->get('main_work_starthour');
			$startTime .= ':' . $this->config->get('main_work_startminute');

			$prefix = 'AM';
			if ($this->config->get('main_work_starthour') >= 12) {
				$prefix = 'PM';
			}
			$startTime .= $prefix;
		}

		$endTime = $this->config->get('main_work_endhour') . ':' . $this->config->get('main_work_endminute');
		if ($this->config->get('main_work_hourformat', '12') == '12') {
			$endTime = ($this->config->get('main_work_endhour') > 12) ? $this->config->get('main_work_endhour') - 12 : (int) $this->config->get('main_work_endhour');
			$endTime .= ':' . $this->config->get('main_work_endminute');

			$prefix = 'AM';
			if ($this->config->get('main_work_endhour') >= 12) {
				$prefix = 'PM';
			}
			$endTime .= $prefix;
		}

		$workdays = array();
		$exception = array();
		$idx = 0;

		$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
		foreach($days as $dd) {
			if ($this->config->get('main_work_' . $dd)) {
				$workdays[] = JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd));
			} else {
				$exception[] = JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd));
			}
		}

		$isEverydayWork = false;

		$workDayLabel = '';
		$workExceptionLabel = '';
		if ($workdays) {
			$workDayLabel = $workdays[0] . ' ' . JText::_('COM_EASYDISCUSS_WORK_TO') . ' ' . $workdays[count($workdays)-1];

			if (count($workdays) == 7) {
				$workDayLabel = JText::_('COM_EASYDISCUSS_WORK_EVERYDAY');
				$isEverydayWork = true;
			}

			if ($exception) {
				if (count($exception) > 1) {
					$last = array_pop($exception);
					$workExceptionLabel = JText::sprintf('COM_EASYDISCUSS_WORK_EXCEPTONS_AND', implode(', ', $exception), $last);
				} else {
					$workExceptionLabel = JText::sprintf('COM_EASYDISCUSS_WORK_EXCEPTONS', $exception[0]);
				}
			}

		}

		$workTimeLabel = JText::sprintf('COM_EASYDISCUSS_WORK_TIME_FROM_TO', $startTime, $endTime);

		// Get the current offset
		$offset = $this->inputdate->getOffset() / 60 / 60;

		$theme->set('date', $this->inputdate);
		$theme->set('offset', $offset);
		$theme->set('namespace', $namespace);
		$theme->set('label', $label);
		$theme->set('workDayLabel', $workDayLabel);
		$theme->set('workTimeLabel', $workTimeLabel);
		$theme->set('workExceptionLabel', $workExceptionLabel);
		$theme->set('isOnline', $this->isOnline);
		$theme->set('isOffline', $this->isOffline);
		$theme->set('isHoliday', $this->isHoliday);
		$theme->set('isOffday', $this->isOffday);
		$theme->set('holiday', $this->holidays);
		$theme->set('isEverydayWork', $isEverydayWork);

		$content = $theme->output('site/work/status');
		return $content;
	}

	public function getData()
	{
		$workDayLabel = '';
		$workExceptionLabel = '';
		$workTimeLabel = '';
		$workdays = array();
		$exception = array();

		$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
		foreach($days as $dd) {
			if ($this->config->get('main_work_' . $dd)) {
				$workdays[] = JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd));
			} else {
				$exception[] = JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd));
			}
		}

		if ($workdays) {
			$workDayLabel = $workdays[0] . ' ' . JText::_('COM_EASYDISCUSS_WORK_TO') . ' ' . $workdays[count($workdays)-1];

			if ($exception) {
				if (count($exception) > 1) {
					$last = array_pop($exception);
					$workExceptionLabel = JText::sprintf('COM_EASYDISCUSS_WORK_EXCEPTONS_AND', implode(', ', $exception), $last);
				} else {
					$workExceptionLabel = JText::sprintf('COM_EASYDISCUSS_WORK_EXCEPTONS', $exception[0]);
				}
			}
		}

		$startTime = $this->config->get('main_work_starthour') . ':' . $this->config->get('main_work_startminute');
		if ($this->config->get('main_work_hourformat', '12') == '12') {
			$startTime = ($this->config->get('main_work_starthour') > 12) ? $this->config->get('main_work_starthour') - 12 : (int) $this->config->get('main_work_starthour');
			$startTime .= ':' . $this->config->get('main_work_startminute');

			$prefix = 'AM';
			if ($this->config->get('main_work_starthour') >= 12) {
				$prefix = 'PM';
			}
			$startTime .= $prefix;
		}

		$endTime = $this->config->get('main_work_endhour') . ':' . $this->config->get('main_work_endminute');
		if ($this->config->get('main_work_hourformat', '12') == '12') {
			$endTime = ($this->config->get('main_work_endhour') > 12) ? $this->config->get('main_work_endhour') - 12 : (int) $this->config->get('main_work_endhour');
			$endTime .= ':' . $this->config->get('main_work_endminute');

			$prefix = 'AM';
			if ($this->config->get('main_work_endhour') >= 12) {
				$prefix = 'PM';
			}
			$endTime .= $prefix;
		}

		$workTimeLabel = JText::sprintf('COM_EASYDISCUSS_WORK_TIME_FROM_TO', $startTime, $endTime);

		$options = array(
			'workDayLabel' => $workDayLabel,
			'workExceptionLabel' => $workExceptionLabel,
			'workTimeLabel' => $workTimeLabel
			);

		return $options;
	}
}
