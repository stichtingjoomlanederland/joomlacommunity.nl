<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperPlayer extends ComFilesTemplateHelperPlayer
{
    /**
     * @param array $config
     * @return string html
     */
    public function render($config = [])
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'document' => null
        ]);

        $document = $config->document;
        $html = '';

        if ($this->_isYoutube($document)) {
            $html = $this->_renderYoutube($document);
        }

        if ($this->_isVimeo($document)) {
            $html = $this->_renderVimeo($document);
        }

        if ($this->_isAudio($document)) {
            $html = $this->_renderAudio($document);
        }

        if ($this->_isVideo($document)) {
            $html = $this->_renderVideo($document);
        }

        return $html;
    }

    /**
     * @param $document
     * @return string
     */
    public function getVideoId($config = [])
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'document' => null
        ]);

        $document = $config->document;

        if ($this->_isYoutube($document)) {
            return $this->_getYoutubeId($document);
        }

        if ($this->_isVimeo($document)) {
            return $this->_getVimeoId($document);
        }

        return '';
    }

    /**
     * @param $document
     * @return bool
     */
    protected function _isYoutube($document)
    {
        return $document->isYoutube();
    }

    /**
     * @param $document
     * @return string
     */
    protected function _getYoutubeId($document)
    {
        $url = parse_url($document->storage->path);

        if ($url)
        {
            if ($url['host'] === 'youtu.be') {
                return trim($url['path'], '/');
            }
            elseif (isset($url['query']))
            {
                parse_str($url['query'], $result);

                if (array_key_exists('v', $result)) {
                    return $result['v'];
                }
            }
        }

        return '';
    }

    /**
     * @param $document
     * @return string
     */
    protected function _renderYoutube($document)
    {
        $video_id = $this->_getYoutubeId($document);

        if ($video_id === '') {
            return '';
        }

        $html = $this->getTemplate()
                     ->loadFile('com://site/docman.document.player_video_remote.html')
                     ->render(array('service' => 'youtube', 'id' => $video_id, 'document' => $document));

        return $html;
    }

    /**
     * @param $document
     * @return bool
     */
    protected function _isVimeo($document)
    {
        return $document->isVimeo();
    }

    /**
     * @param $document
     * @return string
     */
    protected function _getVimeoId($document)
    {
        $video_id = substr(parse_url($document->storage->path, PHP_URL_PATH), 1);

        if ($video_id !== '') {
            return $video_id;
        }

        return '';
    }

    /**
     * @param $document
     * @return string
     */
    protected function _renderVimeo($document)
    {
        $id = substr(parse_url($document->storage->path, PHP_URL_PATH), 1);

        if ($id == '') {
            return '';
        }

        $html = $this->getTemplate()
                     ->loadFile('com://site/docman.document.player_video_remote.html')
                     ->render(array('service' => 'vimeo', 'id' => $id, 'document' => $document));

        return $html;
    }

    /**
     * @param $document
     * @return bool
     */
    protected function _isVideo($document)
    {
        if (in_array($document->extension, self::$_SUPPORTED_FORMATS['video'])) {
            return true;
        }

        return false;
    }

    /**
     * @param $document
     * @return string
     */
    protected function _renderVideo($document)
    {
        $html = $this->getTemplate()
                     ->loadFile('com://site/docman.document.player_video_local.html')
                     ->render(array('document' => $document));

        return $html;
    }

    /**
     * @param $document
     * @return bool
     */
    protected function _isAudio($document)
    {
        if (in_array($document->extension, self::$_SUPPORTED_FORMATS['audio'])) {
            return true;
        }

        return false;
    }

    /**
     * @param $document
     * @return string
     */
    protected function _renderAudio($document)
    {
        $html = $this->getTemplate()
                     ->loadFile('com://site/docman.document.player_audio_local.html')
                     ->render(array('document' => $document));

        return $html;
    }
}