<?php
/**
 * @package     DOCman Exporter
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * DOCman Exporter Class.
 */
class ComDocmanMigratorExport extends ComMigratorMigratorExportAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $self = $this;

        $config->append(array(
                'label'     => 'DOCman',
                'extension' => 'docman',
                'jobs'  => array(
                    'export_documents'          => array(
                        'label'      => 'Exporting documents',
                        'table'      => 'docman_documents'
                    ),
                    'export_document_contents' => array(
                        'label'      => 'Exporting document contents',
                        'table'      => 'docman_document_contents'
                    ),
                    'export_categories'         => array(
                        'label'      => 'Exporting categories',
                        'table'      => 'docman_categories'
                    ),
                    'export_category_relations' => array(
                        'label'      => 'Exporting category relations',
                        'table'      => 'docman_category_relations'
                    ),
                    'export_category_orderings' => array(
                        'label'      => 'Exporting category orderings',
                        'table'      => 'docman_category_orderings'
                    ),
                    'export_category_folders' => array(
                        'label'      => 'Exporting category folders',
                        'table'      => 'docman_category_folders'
                    ),
                    'export_tags' => array(
                        'label'      => 'Exporting tags',
                        'table'      => 'docman_tags'
                    ),
                    'export_tags_relations' => array(
                        'label'      => 'Exporting tag relations',
                        'table'      => 'docman_tags_relations'
                    ),
                    'export_menus'             => array(
                        'label'      => 'Exporting menu items',
                        'table'      => 'menu',
                        'callback'   => function($query) use($self) {
                            $db    = $self->getObject('lib:database.adapter.mysqli');
                            $q = $self->getObject('lib:database.query.select')
                                ->columns('extension_id')->table('extensions')
                                ->where('element = :element ')
                                ->bind(array('element' => 'com_docman'));

                            $query
                                ->where("menutype <> :menutype")
                                ->where("component_id = :component_id")->bind(array(
                                    'component_id' => $db->select($q, KDatabase::FETCH_FIELD),
                                    'menutype'     => 'main'
                                ));
                        }
                    ),
                    'export_modules'             => array(
                        'label'      => 'Exporting modules',
                        'table'      => 'modules',
                        'callback'   => function($query) {
                            $query->where("module IN :module")->bind(array(
                                'module' => array('mod_docman_documents', 'mod_docman_categories')
                            ));
                        }
                    ),
                    'export_assets'             => array(
                        'label'      => 'Exporting assets',
                        'table'      => 'assets',
                        'callback'   => function($query) {
                            $query->where("name LIKE :assets")->bind(array(
                                'assets' => 'com_docman%'
                            ));
                        }
                    ),
                    'export_containers'         => array(
                        'label'      => 'Exporting containers',
                        'table'      => 'files_containers',
                        'callback'  => function($query) {
                            $query->where('slug IN :slugs')->bind(array(
                                'slugs' => array('docman-files', 'docman-icons', 'docman-images')
                            ));
                        }
                    ),
                    'export_settings'           => array(
                        'label'      => 'Exporting settings',
                        'table'      => 'extensions',
                        'callback'  => function($query) {
                            $query->where("element = :element")->bind(array(
                                'element' => 'com_docman'
                            ));
                        }
                    ),
                    'export_levels' => array(
                        'label'      => 'Exporting access levels',
                        'table'      => 'docman_levels'
                    )
                )
            )
        );

        parent::_initialize($config);
    }
}