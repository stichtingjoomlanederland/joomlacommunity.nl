<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarCategory extends ComDocmanControllerToolbarActionbar
{
    protected function _afterBrowse(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $identifier = $context->subject->getIdentifier();
        $request    = $context->subject->getRequest();

        $new_link = 'option=com_'.$identifier->package.'&view='.$identifier->name;

        if ($identifier->name === 'category' && $request->query->parent_id) {
            $new_link .= '&parent_id='.$request->query->parent_id;
        }

        $this->addCommand('new', array(
            'href' => $new_link,
            'allowed' => $controller->canAdd()
        ));

        parent::_afterBrowse($context);
    }
}