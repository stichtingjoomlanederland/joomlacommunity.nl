<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgContentDoclink extends JPlugin
{
    /**
     * A reused view instance to render templates
     *
     * @var KViewInterface
     */
    protected static $_view;

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage();
    }

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if ($context == 'com_finder.indexer') {
            return;
        }

        $links = $this->_getLinks($row->text);

        if ($this->params->get('show_player') && count($links->document)) {
            $this->_replaceLinksWithPlayers($links->document, $row->text);
        }

        if (count($links->document)) {
            $this->_enrichDocumentLinks($links->document, $row->text);
        }

        if (count($links->legacy)) {
            $this->_enrichLegacyLinks($links->legacy, $row->text);
        }

        if (count($links->category)) {
            $this->_enrichCategoryLinks($links->category, $row->text);
        }
    }

    /**
     * Adds document icon and size information to the links
     *
     * @param $links
     * @param $text
     */
    protected function _enrichDocumentLinks(&$links, &$text)
    {
        $document_ids = array_map(function($link) { return $link->id;}, $links);

        $entities = $this->getObject('com://admin/docman.model.documents')->id($document_ids)->fetch();

        foreach ($links as &$link)
        {
            $document = $entities->find($link->id);

            if (strpos(trim($link->text), '<img') === 0 || !$document) {
                continue;
            }

            if ($this->params->get('show_player') && $document->isPlayable()) {
                continue;
            }

            $menu = JFactory::getApplication()->getMenu()->getItem($link->query['Itemid']);

            if ($menu && $menu->params->get('document_title_link') === 'download' && $menu->params->get('track_downloads'))
            {
                $title = htmlspecialchars($document->title, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8', false);

                $link->attributes['data-title'] = $title;
                $link->attributes['data-id']    = $document->id;
                $link->attributes['type']       = $document->mimetype;
                $link->attributes['class']     .= ' docman_track_download';
            }

            $this->_replaceLink($link, $this->_renderLink($link, $document), $text);
        }
    }

    /**
     * Replaces audio/video playable links with html5 players
     *
     * @param array  $links   The links to enrich.
     * @param string $text The text containing the links.
     */
    protected function _replaceLinksWithPlayers(&$links, &$text)
    {
        $document_ids = array_map(function($link) { return $link->id;}, $links);

        $entities = $this->getObject('com://admin/docman.model.documents')->id($document_ids)->fetch();

        foreach ($links as &$link)
        {
            $document = $entities->find($link->id);

            if (strpos(trim($link->text), '<img') === 0 || !$document) {
                continue;
            }

            if (! $document->isPlayable()) {
                continue;
            }

            $link_template = 'index.php?option=com_docman&view=download&id=%d&Itemid=%d';
            $document->download_link = JRoute::_(sprintf($link_template, $document->id, $link->query['Itemid']));

            $helper = $this->getObject('com://site/docman.template.helper.player');

            if ($helper->isVideo($document)) {
                $player = $this->_renderVideo($document);
            } elseif ($helper->isAudio($document)) {
                $player = $this->_renderAudio($document);
            } else {
                $player = $helper->render(array('document' => $document));
            }

            if (! empty($player)) {
                $this->_replaceLink($link, $player, $text);

            }
        }
    }


    /**
     * Adds category icon information to the links
     *
     * @param $links
     * @param $text
     */
    protected function _enrichCategoryLinks(&$links, &$text)
    {
        $category_slugs = array_map(function($link) { return $link->slug;}, $links);

        $entities = $this->getObject('com://admin/docman.model.categories')->slug($category_slugs)->fetch();

        foreach ($links as &$link)
        {
            $category = $entities->find(array('slug' => $link->slug));

            if (strpos(trim($link->text), '<img') === 0 || !$category) {
                continue;
            }

            $this->_replaceLink($link, $this->_renderLink($link, $category), $text);
        }
    }

    /**
     * Replaces old icons from Docman 1.6 with the new icons
     *
     * @param $links
     * @param $text
     */
    protected function _enrichLegacyLinks(&$links, &$text)
    {
        $document_ids = $category_ids = array();

        foreach ($links as $link)
        {
            if ($link->query['task'] === 'doc_download' || $link->query['task'] === 'doc_details') {
                $document_ids[] = $link->query['gid'];
            } else if ($link->query['task'] === 'cat_view') {
                $category_ids[] = $link->query['gid'];
            }
        }

        if (count($document_ids))
        {
            $documents = $this->getObject('com://admin/docman.model.documents')->id($document_ids)->fetch();

            foreach ($links as &$link)
            {
                if (!($document = $documents->find($link->query['gid']))) {
                    continue;
                }

                $link->text = preg_replace('#<img(.*?)>#i', '', $link->text, 1);

                $this->_replaceLink($link, $this->_renderLink($link, $document, array('show_size' => false)), $text);
            }
        }

        if (count($category_ids))
        {
            $categories = $this->getObject('com://admin/docman.model.categories')->id($category_ids)->fetch();

            foreach ($links as &$link)
            {
                if (!($category = $categories->find($link->query['gid']))) {
                    continue;
                }

                $old_text   = $link->text;
                $link->text = preg_replace('#<img(.*?)>#i', '', $link->text, 1);

                $html = $this->_renderLink($link, $category);

                $text = str_replace($link->full, str_replace($old_text.'</a>', $html.'</a>', $link->full), $text);
            }
        }
    }

    /**
     * Renders the link output
     *
     * @param       $link
     * @param       $entity
     * @param array $parameters
     * @return mixed
     */
    protected function _renderLink($link, $entity, $parameters = array())
    {
        $view = $this->_getView('file://plugins/content/doclink/view/default');

        if (!$this->params->get('show_size')) {
            $parameters['show_size'] = false;
        }

        if (!$this->params->get('show_icon')) {
            $parameters['show_icon'] = false;
        }

        $link->attributes['data-processed'] = true;

        $link->attributes['class']     .= ' k-ui-namespace';

        return $view->render(array_merge(array(
            'entity' => $entity,
            'link'   => $link
        ), $parameters));
    }

    /**
     * @param object $link Link object
     * @param string $html New link text
     * @param string $text Article text
     */
    protected function _replaceLink($link, $html, &$text)
    {
        $text = str_replace($link->full, $html, $text);
    }

    /**
     * Returns a view
     *
     * @param  $template string Template identifier
     * @return KViewInterface
     */
    protected function _getView($template)
    {
        if (!self::$_view)
        {
            self::$_view = $this->getObject('com:koowa.view.html', array(
                'layout' => $template,
                'template_filters' => array(
                    'style',
                    'script',
                    'com://admin/docman.template.filter.asset'
                )
            ));
        }

        return self::$_view;
    }

    /**
     * Returns a list of links grouped by their types
     *
     * @param $text
     * @return object
     */
    protected function _getLinks(&$text)
    {
        $matches = array();
        $pattern = '~<a\s+.*</a>~isU';

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as &$match)
            {
                $in_double_quote = false;
                $in_single_quote = false;

                for ($i = 0, $j = strlen($match[0]); $i < $j; $i++)
                {
                    $char = $match[0][$i];

                    if ($char === '"' && !$in_single_quote) {
                        $in_double_quote = !$in_double_quote;
                    }
                    elseif ($char === '\'' && !$in_double_quote) {
                        $in_single_quote = !$in_single_quote;
                    }
                    elseif ($char === '>' && !$in_single_quote && !$in_double_quote)
                    {
                        $match['attributes'] = substr($match[0], 2, $i-2); // attributes: -2 for <a at the start
                        $match['text'] = substr($match[0], $i+1, $j-$i-1-4); // link text: -4 for </a> at the end

                        continue 2;
                    }
                }
            }
        }

        $results = (object) array(
            'document' => array(),
            'category' => array(),
            'menu'     => array(),
            'legacy'   => array()
        );

        foreach ($matches as $i => &$match)
        {
            $match['full']  = $match[0];
            unset($match[0]);

            $match = (object) $match;

            $match->link  = preg_match('#href="(.*option=com_docman.*)"#Ui', $match->attributes, $temp) ? $temp[1] : null;

            if (!$match->link || strpos($match->attributes, 'doclink') === false
                || strpos($match->full, 'data-processed') !== false)
            {
                unset($matches[$i]);

                continue;
            }

            // Parse attributes
            if (preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $match->attributes, $attr, PREG_SET_ORDER))
            {
                $result = array();
                foreach ($attr as $a) {
                    $result[$a[1]] = $a[2];
                }

                $match->attributes = $result;
            }
            else continue;

            $query = parse_url($match->link, PHP_URL_QUERY);
            parse_str(str_replace('&amp;', '&', $query), $query);

            $match->query = $query;

            if (isset($query['task'])) {
                $match->type = 'legacy';

                $results->legacy[] = $match;
            }
            elseif (!isset($query['view'])) {
                $match->type = 'menu';

                $results->menu[] = $match;
            }
            elseif (in_array($query['view'], array('document', 'download'))) {
                $match->type = 'document';
                $match->id   = isset($query['alias']) ? (int) $query['alias'] : 0;

                $results->document[] = $match;
            }
            else {
                $match->type = 'category';
                $match->slug = isset($query['slug']) ? $query['slug'] : null;

                $results->category[] = $match;
            }
        }

        return $results;
    }

    /**
     * Render the com:files Plyr in Joomla context
     * 
     * @param KModelEntityInterface $document
     * @return string
     */
    protected function _renderVideo($document)
    {
        $html = $this->getObject('com:files.view.plyr.html')
            ->getTemplate()
            ->addFilter('script')
            ->addFilter('style')
            ->loadFile('com://site/docman.document.player_video_local.html')
            ->render(array('document' => $document));

        return $html;
    }
    
    /**
     * Render the com:files Plyr in Joomla context
     * 
     * @param KModelEntityInterface $document
     * @return string
     */
    protected function _renderAudio($document)
    {
        $html = $this->getObject('com:files.view.plyr.html')
            ->getTemplate()
            ->addFilter('script')
            ->addFilter('style')
            ->loadFile('com://site/docman.document.player_audio_local.html')
            ->render(array('document' => $document));

        return $html;
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array                    $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        return KObjectManager::getInstance()->getObject($identifier, $config);
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        try {
            if (class_exists('Koowa')) {
                $return = parent::update($args);
            }
        }
        catch (Exception $e) {}


        return $return;
    }
}
