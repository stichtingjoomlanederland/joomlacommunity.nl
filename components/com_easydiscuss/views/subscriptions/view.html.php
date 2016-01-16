<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewSubscriptions extends EasyDiscussView
{
	public function display( $tpl = null )
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		DiscussHelper::setPageTitle( JText::_('COM_EASYDISCUSS_PAGETITLE_SUBSCRIPTIONS') );

		$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_SUBSCRIPTIONS') );

		$subs		= array();
		$model		= $this->getModel( 'subscribe' );
		$rows		= $model->getSubscriptions();

		$email		= $user->id ? $user->email : DiscussStringHelper::escape(JRequest::getVar('email'));

		if( $rows )
		{
			foreach($rows as $row)
			{
				$obj			= new stdClass();
				$obj->id		= $row->id;
				$obj->type		= $row->type;
				$obj->unsublink	= Discusshelper::getUnsubscribeLink($row, false);

				switch($row->type)
				{
					case 'site':
						$obj->title	= '';
						$obj->link	= '';
						break;
					case 'post':
						$post		= DiscussHelper::getTable( 'Post' );
						$post->load( $row->cid );
						$obj->title	= $post->title;
						$obj->link	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id );
						break;
					case 'category':
						$category	= DiscussHelper::getTable( 'Category' );
						$category->load( $row->cid );
						$obj->title	= $category->title;
						$obj->link	= DiscussRouter::getCategoryRoute( $category->id );
						break;
					case 'user':
						$profile	= DiscussHelper::getTable( 'Profile' );
						$profile->load( $row->cid );
						$obj->title	= $profile->getName();
						$obj->link	= $profile->getLink();
						break;
					default:
						unset($obj);
						break;
				}

				if (!empty($obj))
				{
					$obj->title	= DiscussStringHelper::escape($obj->title);
					$subs[$row->type][]	= $obj;
					unset($obj);
				}
			}
		}

		$tpl	= new DiscussThemes();
		$tpl->set( 'subscriptions', $subs );
		$tpl->set( 'email', $email );

		echo $tpl->fetch( 'subscription.list.php' );
	}
}
