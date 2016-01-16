<?php
/**
 * @package RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldCategoryParent extends JFormFieldList
{
    protected $type = 'categoryparent';
	
    protected function getOptions()
    {
        // Initialize the extension value.
        $extension = 'com_rseventspro';
            
        // Initialize the options array.
        $options = array();
            
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the old category id.
        $oldCat = $jinput->getInt('id', 0);
            
        // Get the old parent id.
        $oldParent = $this->form->getValue($this->element['name'], 0);
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Create a new database query.
        $query = $db->getQuery(true)
               ->select( $db->qn('a.id', 'value') . ', ' . $db->qn('a.title', 'text') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.published') )
               ->from( $db->qn('#__categories', 'a') )
               ->leftJoin( $db->qn('#__categories', 'b') . ' ON ' . $db->qn('a.lft') .  ' > ' . $db->qn('b.lft') . ' AND ' . $db->qn('a.rgt') . ' < ' . $db->qn('b.rgt') )
               ->where( '(' . $db->qn('a.extension') . ' = ' . $db->q($extension) . ' OR ' . $db->qn('a.parent_id') . ' = ' . $db->q(0) . ')' );
                
        if ($oldCat != 0)
        {
            // Prevent parenting to children of this item.
            // To rearrange parents and children move the children up, not the parents down.
            $query->leftJoin( $db->qn('#__categories', 'p') . ' ON ' . $db->qn('p.id') . ' = ' . $db->q($oldCat) )
                  ->where( 'NOT(' . $db->qn('a.lft') . ' >= ' . $db->qn('p.lft') . ' AND ' . $db->qn('a.rgt') . ' <= ' . $db->qn('p.rgt') . ')' );
                    
            $rowQuery = $db->getQuery(true)
                      ->select( $db->qn('a.id', 'value') . ', ' . $db->qn('a.title', 'text') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.parent_id') )
                      ->from( $db->qn('#__categories', 'a') )
                      ->where( $db->qn('a.id') . ' = ' . $db->q($oldCat) );
                      
            $db->setQuery($rowQuery);
            $row = $db->loadObject();
        }
            
        $query->group( $db->qn('a.id') . ', ' . $db->qn('a.title') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.lft') . ', ' . $db->qn('a.rgt') . ', ' . $db->qn('a.extension') . ', ' . $db->qn('a.parent_id') . ', ' . $db->qn('a.published') )
              ->order( $db->qn('a.lft') . ' ASC' );
            
        // Get the options.
        $db->setQuery($query);
            
        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage);
        }
            
        // Pad the option text with spaces using depth level as a multiplier.
        for ( $i = 0, $n = count($options); $i < $n; $i++ )
        {
            // Translate ROOT
            if ($options[$i]->level == 0)
            {
                $options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
            }
                
            $options[$i]->text = str_repeat('- ', $options[$i]->level) . ($options[$i]->published == 1 ? $options[$i]->text : '[' .$options[$i]->text . ']') ;
        }
            
        // Get the current user object.
        $user = JFactory::getUser();

        // For new items we want a list of categories you are allowed to create in.
        if ($oldCat == 0)
        {
            foreach ($options as $i => $option)
            {
                // To take save or create in a category you need to have create rights for that category
                // unless the item is already in that category.
                // Unset the option if the user isn't authorised for it. In this field assets are always categories.
                if ( !$user->authorise('core.create', "$extension.category.$option->value") )
                {
                    unset($options[$i]);
                }
            }
        }
        // If you have an existing category id things are more complex.
        else
        {
            // If you are only allowed to edit in this category but not edit.state, you should not get any
            // option to change the category parent for a category or the category for a content item,
            // but you should be able to save in that category.
            foreach ($options as $i => $option)
            {
                if ( !$user->authorise('core.edit.state', "$extension.category.$oldCat") && !isset($oldParent) )
                {
                    if ($option->value != $oldCat  )
                    {
                        unset($options[$i]);
                    }
                }
                    
                if ( !$user->authorise('core.edit.state', "$extension.category.$oldCat") && isset($oldParent) && $option->value != $oldParent)
                {
                    unset($options[$i]);
                }
                    
                // However, if you can edit.state you can also move this to another category for which you have
                // create permission and you should also still be able to save in the current category.
                if ( !$user->authorise('core.create', "$extension.category.$option->value") && !isset($oldParent) && $option->value != $oldCat )
                {
                    unset($options[$i]);
                }
                    
                if ( !$user->authorise('core.create', "$extension.category.$option->value") && isset($oldParent) && $option->value != $oldParent )
                {
                    unset($options[$i]);
                }
            }
        }
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}