<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussGdprProfile extends EasyDiscussGdprAbstract
{
	public $type = 'profile';

	/**
	 * Event trigger to process user's comments for GDPR download on EasySocial
	 *
	 * @since 4.1
	 * @access public
	 */
	public function onEasySocialGdprExport(SocialGdprSection &$section, SocialGdprItem $adapter)
	{
		// manually set type here.
		$adapter->type = $section->key . '_' . $this->type;

		// create tab in section
		$adapter->tab = $section->createTab($adapter);

		$items = $this->getProfileData($adapter->tab);

		if ($items) {
			foreach ($items as $data) {

				$item = $adapter->getTemplate($data->id, $adapter->type);

				$item->view = false;
				$item->title = '';
				$item->intro = $this->getIntro($data);

				$adapter->tab->addItem($item);
			}
		}

		$adapter->tab->finalize();

		return true;
	}

	/**
	 * Main function to process user profile data for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function execute(EasyDiscussGdprSection &$section)
	{
		$this->tab = $section->createTab($this);

		$items = $this->getProfileData($this->tab);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $data) {
			$item = $this->getTemplate($data->id, $this->type);

			$item->view = false;
			$item->title = '';
			$item->intro = $this->getIntro($data);

			$this->tab->addItem($item);
		}
	}

	/**
	 * Populate all the user details data
	 *
	 * @since	4.1
	 * @access	public
	 */
	private function getIntro($data)
	{
		$badges = $this->formatUserBadges($data);
		$rank = $this->formatUserRank($data);
		$social = $this->formatUserSocialData($data);

		ob_start();
		?>
			<table class="gdpr-table" style="width:520px;">
				<thead>
				    <th colspan="2" style="float:left;">
						<?php echo JText::_('COM_ED_GDPR_PROFILE_USER_ACCOUNT'); ?>
				    </th>
				</thead>
				<tbody>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_NAME') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->user->username ? $data->user->username : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_FULLNAME') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->user->name ? $data->user->name : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_NICKNAME') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->nickname ? $data->nickname : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_EMAIL') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->user->email ? $data->user->email : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_SIGNATURE') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->signature ? $data->signature : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_DESCRIPTION') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->description ? $data->description : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_LOCATION') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->location ? $data->location : '-'; ?></td>
					</tr>
					<tr>
						<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_POINTS') . ' : ';?></td>
					 	<td style="text-align:left;"><?php echo $data->points ? $data->points : '-'; ?></td>
					</tr>

					<?php if ($badges) { ?>
						<tr>
							<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_BADGES') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $badges ? $badges : '-'; ?></td>
						</tr>
					<?php } ?>

					<?php if ($rank) { ?>
						<tr>
							<td width="180"><?php echo JText::_('COM_ED_GDPR_PROFILE_USER_RANK') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $rank ? $rank : '-'; ?></td>
						</tr>
					<?php } ?>

					<?php if ($social) { ?>
						<tr>
							<td width="180"><?php echo JText::_('Facebook') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $social->get('facebook', '-'); ?></td>
						</tr>
						<tr>
							<td width="180"><?php echo JText::_('Twitter') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $social->get('twitter', '-'); ?></td>
						</tr>
						<tr>
							<td width="180"><?php echo JText::_('Linkedin') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $social->get('linkedin', '-'); ?></td>
						</tr>
						<tr>
							<td width="180"><?php echo JText::_('Skype') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $social->get('skype', '-'); ?></td>
						</tr>
						<tr>
							<td width="180"><?php echo JText::_('Website') . ' : ';?></td>
						 	<td style="text-align:left;"><?php echo $social->get('website', '-'); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * format user rank
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function formatUserSocialData($data)
	{
		if (empty($data->params)) {
			return false;
		}

		$userparams = ED::getRegistry($data->params);
		return $userparams;
	}

	/**
	 * format user rank
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function formatUserRank($data)
	{
		$config = ED::config();

		if (!$config->get('main_ranking')) {
			return false;
		}

		$userRank = ED::ranks()->getRank($data->id);
		return $userRank;
	}

	/**
	 * format user badge value to string
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function formatUserBadges($data)
	{
		$badgesItem = array();
		$badges = $data->getBadges();

		if (empty($badges)) {
			return false;
		}

		foreach ($badges as $row) {
			$badgesItem[] = $row->title;
		}

		array_unique($badgesItem);
		$badgesItem = implode(', ', $badgesItem);

		return $badgesItem;
	}

	/**
	 * Retrieves user profile data that needs to be processed
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getProfileData($tab)
	{
		$ids = $tab->getProcessedIds();

		$options = array();
		$options['userid'] = $this->user->id;
		$options['exclusion'] = $ids;
		$options['limit'] = $this->getLimit();

		$usersModel = ED::model('Users');
		$profileData = $usersModel->getProfileDataGDPR($options);

		return $profileData;
	}
}
