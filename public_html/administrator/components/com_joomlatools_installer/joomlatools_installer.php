<?php
/**
 * @package     Joomlatools Installer
 * @copyright   Copyright (C) 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

namespace Joomlatools\Installer;

class Installer extends \JInstaller
{
    public $error = 'An error occurred during installation';

    public function abort($msg = null, $type = null)
    {
        if ($msg) {
           $this->error = $msg;
        }

        return parent::abort($msg, $type);
    }
}

class Controller
{
    public function display()
    {
        \JHtml::_('jquery.framework');

        $url = \JRoute::_('index.php?option=com_joomlatools_installer&format=json', false);
        $url = \JRoute::link('site', 'index.php?option=com_joomlatools_installer&format=json', false, 0, true);

        $manifest = json_decode(file_get_contents(JPATH_ROOT.'/components/com_joomlatools_installer/payload/manifest.json'));

        $packages = $manifest->packages;

        $success_url =  isset($manifest->success_url) ? \JRoute::_($manifest->success_url) : null;
        $success_text = isset($manifest->success_text) ? $manifest->success_text : 'Go to extension';

        if (version_compare(JVERSION, '4.0', '>=')) { ?>
        <style>
            @-webkit-keyframes progress-bar-stripes {
            from {
            background-position: 40px 0;
            }
            to {
            background-position: 0 0;
            }
            }
            @keyframes progress-bar-stripes {
            from {
            background-position: 40px 0;
            }
            to {
            background-position: 0 0;
            }
            }
            .js-installer .progress {
            overflow: hidden;
            height: 20px;
            margin-bottom: 20px;
            background-color: #f7f7f7;
            background-image: linear-gradient(to bottom, #f5f5f5, #f9f9f9);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FFF5F5F5", endColorstr="#FFF9F9F9", GradientType=0);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            }
            .js-installer .progress .bar {
            width: 0%;
            height: 100%;
            color: #fff;
            float: left;
            font-size: 12px;
            text-align: center;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
            background-color: #0e90d2;
            background-image: linear-gradient(to bottom, #149bdf, #0480be);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FF149BDF", endColorstr="#FF0480BE", GradientType=0);
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
            transition: width 0.6s ease;
            }
            .js-installer .progress .bar + .bar {
            box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
            }
            .js-installer .progress-striped .bar {
            background-color: #149bdf;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 40px 40px;
            }
            .js-installer .progress.active .bar {
            -webkit-animation: progress-bar-stripes 2s linear infinite;
            animation: progress-bar-stripes 2s linear infinite;
            }
            .js-installer .progress-danger .bar, .js-installer .progress .bar-danger {
            background-color: #dd514c;
            background-image: linear-gradient(to bottom, #ee5f5b, #c43c35);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FFEE5F5B", endColorstr="#FFC43C35", GradientType=0);
            }
            .js-installer .progress-danger.progress-striped .bar, .js-installer .progress-striped .bar-danger {
            background-color: #ee5f5b;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            }
            .js-installer .progress-success .bar, .js-installer .progress .bar-success {
            background-color: #5eb95e;
            background-image: linear-gradient(to bottom, #62c462, #57a957);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FF62C462", endColorstr="#FF57A957", GradientType=0);
            }
            .js-installer .progress-success.progress-striped .bar, .js-installer .progress-striped .bar-success {
            background-color: #62c462;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            }
            .js-installer .progress-info .bar, .js-installer .progress .bar-info {
            background-color: #4bb1cf;
            background-image: linear-gradient(to bottom, #5bc0de, #339bb9);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FF5BC0DE", endColorstr="#FF339BB9", GradientType=0);
            }
            .js-installer .progress-info.progress-striped .bar, .js-installer .progress-striped .bar-info {
            background-color: #5bc0de;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            }
            .js-installer .progress-warning .bar, .js-installer .progress .bar-warning {
            background-color: #faa732;
            background-image: linear-gradient(to bottom, #fbb450, #f89406);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FFFBB450", endColorstr="#FFF89406", GradientType=0);
            }
            .js-installer .progress-warning.progress-striped .bar, .js-installer .progress-striped .bar-warning {
            background-color: #fbb450;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            }
        </style>
        <?php }
        ?>
        <div class="js-installer">
            <h1 class="js-heading js-heading-in-progress">Installation in progress</h1>
            <h1 class="js-heading js-heading-complete" style="display: none">Installation successfully completed!</h1>
            <h1 class="js-heading js-heading-failed" style="display: none">Installation failed!</h1>
            <div class="js-error-container"></div>

            <?php foreach ($packages as $package): ?>
                <h3><?php echo $package->title; ?></h3>
                <div class="progress progress-striped active">
                    <div class="js-bar-<?php echo str_replace(array('.', ' '), '', $package->path); ?> bar" style="width: 0"></div>
                </div>
            <?php endforeach; ?>

            <?php if ($success_url): ?>
                <div>
                    <a style="display: none; margin-bottom: 30px" class="js-redirect-button btn btn-primary" href="<?php echo $success_url; ?>">
                        <?php echo $success_text; ?>
                    </a>
                </div>
            <?php endif ?>
        </div>

        <script type="text/javascript">

            var jtinstaller_in_progress = false;

            window.onbeforeunload = function() {
                if (jtinstaller_in_progress) {
                    return 'Installation is not complete yet. Are you sure you want to continue?';
                }
            };

            window.addEventListener('DOMContentLoaded', function() {
                var $ = jQuery;

                // Joomla 4
                document.querySelectorAll('joomla-alert[type="success"]').forEach(function(node) { node.remove() } );

                // Joomla 3
                $('.alert-success').parent().remove();

                var steps = <?php echo json_encode($packages); ?>;
                var url   = <?php echo json_encode($url) ?>;

                var error_container = $('.js-error-container');
                var showMessages = function(messages) {
                    $.each(messages, function(type, messages) {

                        var c = error_container.find('.alert-'+type);

                        if (c.length == 0) {
                            c = $('<div class="alert alert-'+type+'"></div>');
                            error_container.append(c);
                        }

                        $.each(messages, function(i, message) {
                            c.append($('<div class="alert-message">'+message+'</div>'));
                        });
                    });
                };

                var runTask = function(task, data) {
                    if (typeof data === 'undefined') {
                        data = {};
                    }

                    data['task'] = task;

                    var bar = (data.path || data.task).replace(/\s/g, '').replace(/\./g, '');

                    bar = $('.js-bar-'+bar);
                    bar.css('width', '50%');

                    return $.ajax(url, {
                        type: 'POST',
                        dataType: 'json',
                        data: data
                    }).done(function(response) {
                        showMessages(response.messages);

                        bar.css('width', '100%')
                            .addClass('bar-success')
                            .parent().removeClass('active');
                    }).fail(function(data) {
                        $('.js-heading').hide();
                        $('.js-heading-failed').show();

                        var json = $.parseJSON(data.responseText);

                        if (json && json.messages) {
                            showMessages(json.messages);
                        }

                        jtinstaller_in_progress = false;

                        bar.css('width', '50%')
                            .removeClass('bar-success').addClass('bar-danger')
                            .parent().removeClass('active');

                        if (task !== 'selfdestruct') {
                            runTask('selfdestruct');
                        }
                    });
                };

                jtinstaller_in_progress = true;

                var res = null;

                $.each(steps, function(i, step) {
                    if (!res) {
                        res = runTask('install', {path: step.path});
                    } else {
                        res = res.then(function() {
                            return runTask('install', {path: step.path});
                        });
                    }
                });

                res.then(function() {
                    runTask('selfdestruct');

                    $('.js-heading').hide();
                    $('.js-heading-complete').show();

                    $('.js-redirect-button').show();

                    jtinstaller_in_progress = false;
                });
            });
        </script>
        <?php
    }

    public function selfdestruct()
    {
        $result = false;
        $query = /** @lang text */"SELECT extension_id FROM #__extensions
            WHERE type = 'component' AND element = 'com_joomlatools_installer'
            LIMIT 1
        ";

        $extension_id = \JFactory::getDbo()->setQuery($query)->loadResult();

        if ($extension_id) {
            $installer = new \JInstaller();
            $result = $installer->uninstall('component', $extension_id, 1);
        }

        return $this->sendResponse(array('result' => $result));
    }

    public function install()
    {
        $result = false;
        $path = \JFactory::getApplication()->input->getPath('path');

        if ($path)
        {
            $path = __DIR__.'/payload/'.$path;

            if (is_dir($path)) {
                $installer = new Installer();
                $result = $installer->install($path);

                if ($result === false) {
                    return $this->sendResponse(array('error' => $installer->error));
                }
                
            }
        }

        return $this->sendResponse(array('result' => $result));
    }

    public function sendResponse($response) {
        \JFactory::getDocument()->setMimeEncoding('application/json');

        $messages = \JFactory::getApplication()->getMessageQueue();

        $response['messages'] = array();

        foreach ($messages as $message) {
            $type = $message['type'];
            $text = $message['message'];

            if (!isset($response['messages'][$type])) {
                $response['messages'][$type] = array();
            }

            $response['messages'][$type][] = $text;
        }

        if ((isset($response['result']) && !$response['result']) || array_key_exists('error', $response)) {
            \JFactory::getApplication()->setHeader('Status', 500, true);
        }

        echo json_encode($response);

        return null;
    }
}

try
{
    $app = \JFactory::getApplication();
    $installer = new Controller();

    $task = $app->input->getCmd('task');

    if (method_exists($installer, $task)) {
        $installer->$task();
    }
}
catch (\Exception $e) {
    if (\JFactory::getDocument()->getType() === 'json') {
        $installer = new Controller();
        $installer->sendResponse(array('error' => $e->getMessage()));
    }
}
