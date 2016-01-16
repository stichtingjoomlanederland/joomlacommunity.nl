<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewKomento extends KomentoAdminView
{
	public function display($tpl = null)
	{
		// //Load pane behavior
		// jimport('joomla.html.pane');

		// $slider		= JPane::getInstance( 'sliders' );

		//initialise variables
		$model = Komento::getModel('comments');

		$comments = '';
		$options = array(
			'threaded'	=> 0,
			'sort'		=> 'latest',
			'limit'		=> 10
		);

		$comments = $model->getComments('all', 'all', $options);

		// Set Options
		$optionsPending['published'] = 2;
		$optionsPending['no_tree'] = 1;
		$optionsPending['no_child'] = 1;

		$pendings = $model->getData($optionsPending);

		foreach ($pendings as $pending) {
			$pending = Komento::getHelper( 'comment' )->process( $pending, 1 );
		}

		foreach ($comments as $comment) {
			$comment = Komento::getHelper( 'comment' )->process( $comment, 1 );
		}
		
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		$this->assignRef( 'slider', $slider );
		$this->assignRef( 'user', $user );
		$this->assignRef( 'document', $document );
		$this->assignRef( 'comments', $comments );
		$this->assignRef( 'pendings', $pendings );
		parent::display($tpl);
	}

	public function getTotalComments()
	{
		return Komento::getModel( 'comments', false )->getTotalComment();
	}

	public function addButton( $link, $image, $text, $description = '' , $newWindow = false, $acl = '', $notification = 0 )
	{
		if( !empty( $acl ) && Komento::joomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('komento.manage.' . $acl , 'com_komento') )
			{
				return '';
			}
		}

		$target		= $newWindow ? ' target="_blank"' : '';

		$bubble = $notification > 0 ? '<b>' . $notification . '</b>' : '';
?>
	<li>
		<a href="<?php echo $link;?>"<?php echo $target;?>>
			<img src="<?php echo JURI::root();?>administrator/components/com_komento/assets/images/cpanel/<?php echo $image;?>" width="32" />
			<span class="item-title">
				<span><?php echo $text;?></span>
				<?php if( $notification > 0 ) { ?>
					<b><?php echo $notification; ?></b>
				<?php } ?>
			</span>
		</a>
		<div class="item-description">
			<div class="tipsArrow"></div>
			<div class="tipsBody"><?php echo $description;?></div>
		</div>
	</li>
<?php
	}

	public function registerToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KOMENTO'), 'home');

		JToolBarHelper::preferences('com_komento');

	}
}
