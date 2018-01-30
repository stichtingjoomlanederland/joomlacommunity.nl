<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerCategory extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.delete', '_checkDocumentCount');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'accessible',
                'findable',
                'organizable',
                'sortable',
                'sluggable'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Halts the delete if the category has documents attached to it.
     *
     * Also makes sure subcategories are deleted correctly when both
     * they and their parents are in the rowset to be deleted.
     *
     * @param KDispatcherContextInterface $context
     * @throws KControllerExceptionActionFailed
     */
    protected function _checkDocumentCount(KControllerContextInterface $context)
    {
        $data = $this->getModel()->fetch();

        if ($count = $data->countDocuments())
        {
            $message = $this->getObject('translator')->choose(array(
                'This category or its children has a document attached. You first need to delete or move it before deleting this category.',
                'This category or its children has {count} documents attached. You first need to delete or move them before deleting this category.'
               ), $count, array('count' => $count));

            throw new KControllerExceptionActionFailed($message);
        }

        /*
         * Removes the child categories from the rowset since they will be deleted by their parent.
         * Otherwise rowset gets confused when it tries to delete a non-existant row.
         */
        if ($data instanceof KModelEntityInterface)
        {
            $to_be_deleted = array();

            // PHP gets confused if you extract a row and then continue iterating on the rowset
            $iterator = clone $data;
            foreach ($iterator as $entity)
            {
                if (in_array($entity->id, $to_be_deleted)) {
                    $data->remove($entity);
                }

                foreach ($entity->getDescendants() as $descendant) {
                    $to_be_deleted[] = $descendant->id;
                }
            }
        }
    }
}
