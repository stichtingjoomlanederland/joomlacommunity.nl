<?php
/**
 * @package     DOCman Exporter
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * DOCman Importer Class.
 */
class ComDocmanMigratorImport extends ComMigratorMigratorImportAbstract
{
    protected function _actionCheck(ComMigratorMigratorContext $context)
    {
        $job = $context->getJob();
        $job->append($this->getConfig());

        $translator = $this->getObject('translator');

        $source = substr($this->getConfig()->source_version, 0, 3);
        $current = substr(ComDocmanVersion::VERSION, 0, 3);

        if (version_compare($source, $current, '<'))
        {
            $context->setError($translator->translate(
                'The exported data is from DOCman version {source}. Please first upgrade the source installation to DOCman {current} and then export the data again to import it here.'
                , array('source' => $source, 'current' => $current)
            ));

            return false;
        }

        if (version_compare($source, $current, '>'))
        {
            $context->setError($translator->translate(
                'The exported data is from a newer DOCman version. Please first upgrade this installation to DOCman {source} to import this file.',
                array('source' => $source, 'current' => $current)
            ));

            return false;
        }

        if (!$this->getObject('request')->getQuery()->has('override')
            && ($this->getObject('com://admin/docman.model.categories')->count()
                || $this->getObject('com://admin/docman.model.documents')->count())) {
            $context->setError($translator->translate(
                'You need to delete all existing categories and documents before you start the migration process'
            ));

            return false;
        }

        return true;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $source = substr($config->source_version, 0, 3);

        $config->append(array(
            'label'     => 'DOCman',
            'extension' => 'docman',
            'jobs'      => array()
        ));

        if (version_compare($source, '2.0', '>='))
        {
            $config->jobs->append(array(
                'check' => array(
                    'action' => 'check',
                    'label'     => 'Checking your system',
                ),
                'insert_documents'          => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting documents',
                    'source'    => 'docman_documents',
                    'table'     => 'docman_documents_mig',
                    'create_from'    => 'docman_documents'
                ),
                'insert_categories'         => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting categories',
                    'source'    => 'docman_categories',
                    'table'     => 'docman_categories_mig',
                    'create_from'    => 'docman_categories'
                ),
                'insert_category_relations' => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting category relations',
                    'source'    => 'docman_category_relations',
                    'table'     => 'docman_category_relations_mig',
                    'create_from'    => 'docman_category_relations'
                ),
                'insert_category_orderings' => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting category orderings',
                    'source'    => 'docman_category_orderings',
                    'table'     => 'docman_category_orderings_mig',
                    'create_from'    => 'docman_category_orderings'
                ),
                'insert_menu'   => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting menus',
                    'source'    => 'menu',
                    'table'     => 'docman_menu_mig',
                    'create_from'    => 'menu',
                ),
                'insert_modules'          => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting modules',
                    'source'    => 'modules',
                    'table'     => 'docman_modules_mig',
                    'create_from'    => 'modules',
                ),
                'insert_containers'          => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting containers',
                    'source'    => 'files_containers',
                    'table'     => 'docman_containers_mig',
                    'create_from'    => 'files_containers',
                ),
                'insert_assets'   => array(
                    'action'      => 'insert',
                    'chunkable'   => true,
                    'label'       => 'Inserting assets',
                    'source'      => 'assets',
                    'table'       => 'docman_assets_mig',
                    'create_from' => 'assets'
                ),
                'move_documents'            => array(
                    'action' => 'move',
                    'label'  => 'Moving Documents',
                    'source' => 'docman_documents_mig',
                    'target' => 'docman_documents'
                ),
                'move_categories'           => array(
                    'action' => 'move',
                    'label'  => 'Moving categories',
                    'source' => 'docman_categories_mig',
                    'target' => 'docman_categories'
                ),
                'move_category_relations'   => array(
                    'action' => 'move',
                    'label'  => 'Moving category relations',
                    'source' => 'docman_category_relations_mig',
                    'target' => 'docman_category_relations'
                ),
                'move_category_orderings'   => array(
                    'action' => 'move',
                    'label'  => 'Moving category orderings',
                    'source' => 'docman_category_orderings_mig',
                    'target' => 'docman_category_orderings'
                ),
                'import_modules' => array(
                    'action' => 'copy',
                    'label'  => 'Importing modules',
                    'source' => 'docman_modules_mig',
                    'target' => 'modules',
                    'skip_primary_key' => true,
                ),
                'import_assets' => array(
                    'action'    => 'import_assets',
                    'label'     => 'Importing assets',
                    'source'    => 'docman_assets_mig',
                    'target'    => 'assets',
                    'extension' => 'com_docman',
                    'tables'    => array(
                        array('docman_documents',  'com_docman.document', 'docman_document_id'),
                        array('docman_categories', 'com_docman.category', 'docman_category_id')
                    )
                ),
                'set_asset_parents' => array(
                    'action' => 'query',
                    'label'  => 'Rebuilding assets',
                    'after'  => 'import_assets',
                    'query'  => /** @lang text */"

                        UPDATE #__assets AS current_asset
                        LEFT JOIN #__docman_categories AS c ON c.asset_id = current_asset.id
                        LEFT JOIN #__docman_category_relations AS cr ON cr.descendant_id = c.docman_category_id
                        LEFT JOIN #__assets AS parent_asset ON parent_asset.name = CONCAT('com_docman.category.', cr.ancestor_id)
                        SET current_asset.parent_id = parent_asset.id
                        WHERE cr.descendant_id <> cr.ancestor_id AND cr.level = 1;

                        UPDATE #__assets AS current_asset
                        LEFT JOIN #__docman_documents AS d ON d.asset_id = current_asset.id
                        LEFT JOIN #__docman_categories AS c ON c.docman_category_id = d.docman_category_id
                        LEFT JOIN #__assets AS parent_asset ON parent_asset.name = CONCAT('com_docman.category.', c.docman_category_id)
                        SET current_asset.parent_id = parent_asset.id
                        WHERE current_asset.name LIKE 'com_docman.document.%'
                    "
                ),
                'import_containers' => array(
                    'action' => 'copy',
                    'source' => 'docman_containers_mig',
                    'target' => 'files_containers',
                    'label'  => 'Importing containers',
                    'operation' => 'REPLACE'
                ),
                'import_menu' => array(
                    'action' => 'import_menu',
                    'source' => 'docman_menu_mig',
                    'label'  => 'Importing menu items',
                    'type'   => 'docman-migrated'
                ),
                'fix_modules' => array(
                    'action' => 'fix_modules',
                    'label' => 'Fixing modules'
                ),
                'import_settings' => array(
                    'action' => 'import_settings',
                    'label'  => 'Importing settings'
                ),
                'cleanup'            => array(
                    'action' => 'query',
                    'query'  => "
                        DROP TABLE IF EXISTS `#__docman_containers_mig`;
                        DROP TABLE IF EXISTS `#__docman_modules_mig`;
                        DROP TABLE IF EXISTS `#__docman_menu_mig`;
                        ",
                    'label'  => 'Cleaning up'
                )
            ));
        }

        if ($source == '2.0')
        {
            /*
            * Takes care of converting temporary tables and their data to match those of the target table.
            * This task is usually run in between insert and copy tasks.
            */
            $config->jobs->append(array(
                'convert_tables' => array(
                    'action' => 'query',
                    'query'  => "
                            UPDATE `#__docman_categories_mig` SET `access_raw` = 0 WHERE `access_raw` = -1;

                            UPDATE `#__docman_documents_mig` SET `access` = 0 WHERE `access` = -1;
                            ",
                    'label'  => 'Converting tables',
                    'after'  => 'insert_assets'
                )
            ));
        }

        if (version_compare($source, '2.1', '>='))
        {
            $config->jobs->append(array(
                'insert_levels' => array(
                    'action' => 'insert',
                    'label'  => 'Inserting access levels',
                    'source' => 'docman_levels',
                    'table'  => 'docman_levels_mig',
                    'after'  => 'insert_containers',
                    'create_from' => 'docman_levels'
                ),
                'move_levels'   => array(
                    'action' => 'move',
                    'label'  => 'Moving access levels',
                    'source' => 'docman_levels_mig',
                    'target' => 'docman_levels',
                    'after'  => 'move_category_orderings'
                ),
            ));
        }

        if (version_compare($source, '3.0', '>='))
        {
            $config->jobs->append(array(
                'insert_category_folders' => array(
                    'action' => 'insert',
                    'label'  => 'Inserting category folders',
                    'source' => 'docman_category_folders',
                    'table'  => 'docman_category_folders_mig',
                    'after'  => 'insert_category_orderings',
                    'create_from' => 'docman_category_folders'
                ),
                'insert_tags' => array(
                    'action' => 'insert',
                    'label'  => 'Inserting tags',
                    'source' => 'docman_tags',
                    'table'  => 'docman_tags_mig',
                    'after'  => 'insert_category_folders',
                    'create_from' => 'docman_tags'
                ),
                'insert_tags_relations' => array(
                    'action' => 'insert',
                    'label'  => 'Inserting tag relations',
                    'source' => 'docman_tags_relations',
                    'table'  => 'docman_tags_relations_mig',
                    'after'  => 'insert_tags',
                    'create_from' => 'docman_tags_relations'
                ),
                'move_category_folders'   => array(
                    'action' => 'move',
                    'label'  => 'Moving category folders',
                    'source' => 'docman_category_folders_mig',
                    'target' => 'docman_category_folders',
                    'after'  => 'move_category_orderings'
                ),
                'move_tags'   => array(
                    'action' => 'move',
                    'label'  => 'Moving tags',
                    'source' => 'docman_tags_mig',
                    'target' => 'docman_tags',
                    'after'  => 'move_category_folders'
                ),
                'move_tags_relations'   => array(
                    'action' => 'move',
                    'label'  => 'Moving tag relations',
                    'source' => 'docman_tags_relations_mig',
                    'target' => 'docman_tags_relations',
                    'after'  => 'move_tags'
                )
            ));
        }

        // It was added to export in 3.0.2
        if (version_compare($config->source_version, '3.0.2', '>=')) {
            $config->jobs->append([
                'insert_document_contents'          => array(
                    'action'    => 'insert',
                    'chunkable' => true,
                    'label'     => 'Inserting document contents',
                    'source'    => 'docman_document_contents',
                    'table'     => 'docman_document_contents_mig',
                    'create_from'    => 'docman_document_contents',
                    'after'  => 'insert_tags_relations',
                ),
                'move_document_contents' => [
                    'action' => 'move',
                    'label'  => 'Moving document contents',
                    'source' => 'docman_document_contents_mig',
                    'target' => 'docman_document_contents',
                    'after'  => 'move_tags_relations'
                ]
            ]);
        }

        parent::_initialize($config);
    }

    /**
     * Custom job to fix module's page settings pointing to menu items that got assigned a new ID.
     *
     * @param ComMigratorMigratorContext $context
     */
    protected function _actionFix_modules(ComMigratorMigratorContext $context)
    {
        $modules = $this->getObject('com:migrator.database.table.modules', array('name' => 'modules'))
                        ->select(array(
                            'module' => array(
                                'mod_docman_documents',
                                'mod_docman_categories'
                            )
                        ), KDatabase::FETCH_ROWSET);

        $menus = $this->getObject('com:migrator.database.table.menus', array('name' => 'docman_menu_mig'))
                      ->select(array(), KDatabase::FETCH_ROWSET);

        foreach ($modules as $module)
        {
            $params = @json_decode($module->params);

            if (isset($params) && isset($params->page) && $params->page && $params->page > 0)
            {
                if ($menu = $menus->find(array('id' => $params->page)))
                {
                    $menu_params = @json_decode($menu->params);

                    if (isset($menu_params) && isset($menu_params->migrator_data))
                    {
                        $data = $menu_params->migrator_data;

                        if (isset($data->menu_item_id))
                        {
                            // Change the module page ID
                            $params->page   = $data->menu_item_id;
                            $module->params = json_encode($params);
                            $module->save();
                        }
                    }
                }
            }
        }
    }
}