<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2020 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperSlider extends ComKoowaTemplateHelperBehavior
{
    /**
     * Load Splide slider
     * 
     * @param array $config
     * @return string
     */
    public function load($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'selector' => null,
            'options'  => [
                'type'        => 'loop',
                'heightRatio' => 0.6,
                'cover'       => true,
            ]
        ]);

        $html = $this->jquery();

        $signature = md5(serialize([$config->selector, $config->options]));

        if (!static::isLoaded($signature))
        {
            $html .= '<ktml:style src="media://com_docman/css/splide.css"/>
            <ktml:style src="media://com_docman/css/splide-extension-video.css"/>
            <ktml:script src="media://com_docman/js/splide.js" />
            <ktml:script src="media://com_docman/js/splide-extension-video.js" />';

            $html .= '<script>
            kQuery(function($) {
                new Splide("' . $config->selector . '", ' . $config->options . ').mount(window.splide.Extensions);
            });</script>';

            static::setLoaded($signature);
        }

        return $html;
    }
}