/**
 * Akeeba Backup
 *
 * @package    akeeba
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU GPL version 3 or, at your option, any later version
 **/

// Object initialisation
if (typeof akeeba == 'undefined')
{
    var akeeba = {};
}

if (typeof akeeba.Transfer == 'undefined')
{
    akeeba.Transfer = {
        'lastUrl': '',
        'lastResult': '',
        'FtpBrowser' : {
            params: {}
        },
        SftpBrowser: {
            params: {}
        },
        FtpTest: {},
        SftpTest: {},
        URLs: {},
        translations: {}
    }
}

(function($){

    /**
     * Check the URL field
     */
    akeeba.Transfer.onUrlChange = function(force)
    {
		if (force == undefined)
		{
			force = false;
		}

        var urlBox = $('#akeeba-transfer-url');
        var url = urlBox.val();

        if (url == '')
        {
            $('#akeeba-transfer-lbl-url').show();
        }

        if ((url.substring(0,7) != 'http://') && (url.substring(0,8) != 'https://'))
        {
            url = 'http://' + url;
        }

        if (!force && (url == akeeba.Transfer.lastUrl))
        {
            akeeba.Transfer.applyUrlCheck({
                'status': akeeba.Transfer.lastResult,
                'url': akeeba.Transfer.lastUrl
            });

            return;
        }

        $('#akeeba-transfer-row-url > div').each(function(i, el){
            $(el).hide();
        });

        urlBox.attr('disabled', 'disabled');
        $('#akeeba-transfer-btn-url').attr('disabled', 'disabled');
        $('#akeeba-transfer-loading').show();

        akeeba.System.doAjax({
            'task': 'checkUrl',
            'url': url
        },
        akeeba.Transfer.applyUrlCheck,
        function(msg) {
            urlBox.removeAttr('disabled');
            $('#akeeba-transfer-btn-url').removeAttr('disabled');
            $('#akeeba-transfer-loading').hide();
        }, false, 10000);
    };

    akeeba.Transfer.applyUrlCheck = function(response)
    {
        var urlBox = $('#akeeba-transfer-url');

        urlBox.removeAttr('disabled');
        $('#akeeba-transfer-btn-url').removeAttr('disabled');
        $('#akeeba-transfer-loading').hide();
        $('#akeeba-transfer-ftp-container').hide();

        urlBox.val(response.url);

        akeeba.Transfer.lastResult = response.status;
        akeeba.Transfer.lastUrl = response.url;

        switch (response.status)
        {
            case 'ok':
                akeeba.Transfer.showConnectionDetails();
                break;

            case 'same':
                $('#akeeba-transfer-err-url-same').show();
                break;

            case 'invalid':
                $('#akeeba-transfer-err-url-invalid').show();
                break;

            case 'notexists':
                $('#akeeba-transfer-err-url-notexists').show();
                break;
        }
    };

    akeeba.Transfer.showConnectionDetails = function()
    {
        $('#akeeba-transfer-url').attr('disabled', 'disabled');
        $('#akeeba-transfer-btn-url').attr('disabled', 'disabled');

        $('#akeeba-transfer-err-url-notexists').hide();
        $('#akeeba-transfer-ftp-container').show();
        akeeba.Transfer.onTransferMethodChange();

        return false;
    };

    akeeba.Transfer.onTransferMethodChange = function(e)
    {
        var method = $('#akeeba-transfer-ftp-method').val();

        $('#akeeba-transfer-ftp-host').parent().parent().hide();
        $('#akeeba-transfer-ftp-port').parent().parent().hide();
        $('#akeeba-transfer-ftp-username').parent().parent().hide();
        $('#akeeba-transfer-ftp-password').parent().parent().hide();
        $('#akeeba-transfer-ftp-pubkey').parent().parent().hide();
        $('#akeeba-transfer-ftp-privatekey').parent().parent().hide();
        $('#akeeba-transfer-ftp-directory').parent().parent().parent().hide();
        $('#akeeba-transfer-ftp-passive-container').hide();
        $('#akeeba-transfer-apply-loading').hide();

        if (method != 'manual')
        {
            $('#akeeba-transfer-ftp-host').parent().parent().show();
            $('#akeeba-transfer-ftp-port').parent().parent().show();
            $('#akeeba-transfer-ftp-username').parent().parent().show();
            $('#akeeba-transfer-ftp-password').parent().parent().show();
            $('#akeeba-transfer-ftp-directory').parent().parent().parent().show();
        }

        if ((method == 'ftp') || (method == 'ftps'))
        {
            $('#akeeba-transfer-ftp-passive-container').show();
        }

        if (method == 'sftp')
        {
            $('#akeeba-transfer-ftp-pubkey').parent().parent().show();
            $('#akeeba-transfer-ftp-privatekey').parent().parent().show();
        }

    };

    /**
     * Initialises an FTP folder browser
     *
     * @param  key        The id of the field holding the FTP directory we're outputting
     * @param  paramsKey  The key prefix of the fields holding FTP connection information
     */
    akeeba.Transfer.FtpBrowser.initialise = function()
    {
        akeeba.Transfer.FtpBrowser.params.host = $('#akeeba-transfer-ftp-host').val();
        akeeba.Transfer.FtpBrowser.params.port = $('#akeeba-transfer-ftp-port').val();
        akeeba.Transfer.FtpBrowser.params.username = $('#akeeba-transfer-ftp-username').val();
        akeeba.Transfer.FtpBrowser.params.password = $('#akeeba-transfer-ftp-password').val();
        akeeba.Transfer.FtpBrowser.params.passive = $('#akeeba-transfer-ftp-passive1').is(':checked') ? 1 : 0;
        akeeba.Transfer.FtpBrowser.params.ssl = ($('akeeba-transfer-ftp-method').val() == 'ftps') ? 1 : 0;
        akeeba.Transfer.FtpBrowser.params.directory = $('#akeeba-transfer-ftp-directory').val();

        akeeba.Transfer.FtpBrowser.open();
    };

    /**
     * Opens the FTP directory browser
     */
    akeeba.Transfer.FtpBrowser.open = function () {
        var ftp_dialog_element = $("#ftpdialog");

        ftp_dialog_element.css('display', 'block');
        ftp_dialog_element.removeClass('ui-state-error');

        $('#ftpdialogOkButton').click(function(e){
            akeeba.Transfer.FtpBrowser.callback(akeeba.Transfer.FtpBrowser.params.directory);
            $("#ftpdialog").modal('hide');
        });

        ftp_dialog_element.modal('show');

        $('#ftpBrowserErrorContainer').css('display', 'none');
        $('#ftpBrowserFolderList').html('');
        $('#ftpBrowserCrumbs').html('');

        if (empty(akeeba.Transfer.FtpBrowser.params.directory))
        {
            akeeba.Transfer.FtpBrowser.params.directory = '';
        }

        var data = {
            'view':         'ftpbrowser',
            'task':         'browse',
            'host':         akeeba.Transfer.FtpBrowser.params.host,
            'port':         akeeba.Transfer.FtpBrowser.params.port,
            'username':     akeeba.Transfer.FtpBrowser.params.username,
            'password':     akeeba.Transfer.FtpBrowser.params.password,
            'passive':      (akeeba.Transfer.FtpBrowser.params.passive ? 1 : 0),
            'ssl':          (akeeba.Transfer.FtpBrowser.params.ssl ? 1 : 0),
            'directory':    akeeba.Transfer.FtpBrowser.params.directory
        };

        // Do AJAX call & Render results
        akeeba.System.doAjax(
            data,
            function (data)
            {
                var akCrumbs = $('#ak_crumbs2');

                if (data.error != false)
                {
                    // An error occured
                    $('#ftpBrowserError').html(data.error);
                    $('#ftpBrowserErrorContainer').css('display', 'block');
                    $('#ftpBrowserFolderList').css('display', 'none');
					$('#ftpBrowserWrapper').css('display', 'none');
                    akCrumbs.css('display', 'none');
                }
                else
                {
                    // Create the interface
                    $('#ftpBrowserErrorContainer').css('display', 'none');
					$('#ftpBrowserWrapper').css('display', 'block');


                    // Display the crumbs
                    if (!empty(data.breadcrumbs)) {
                        akCrumbs.css('display', 'block');
                        akCrumbs.html('');
                        var relativePath = '/';

                        akeeba.Transfer.FtpBrowser.addCrumb(akeeba.Transfer.translations['UI-ROOT'], '/', akCrumbs);

                        $.each(data.breadcrumbs, function (i, crumb) {
                            relativePath += '/' + crumb;

                            akeeba.Transfer.FtpBrowser.addCrumb(crumb, relativePath, $('#ak_crumbs2'));
                        });
                    } else {
                        $('#ftpBrowserCrumbs').css('display', 'none');
                    }

                    // Display the list of directories
                    if (!empty(data.list)) {
                        $('#ftpBrowserFolderList').css('display', 'block');

                        if(!akeeba.Transfer.FtpBrowser.params.directory)
                        {
                            akeeba.Transfer.FtpBrowser.params.directory = data.directory;
                        }

                        $.each(data.list, function (i, item) {
                            akeeba.Transfer.FtpBrowser.createLink(data.directory + '/' + item, item, $('#ftpBrowserFolderList'));
                        });
                    } else {
                        $('#ftpBrowserFolderList').css('display', 'none');
                    }
                }
            },
            function (message) {
                $('#ftpBrowserError').html(message);
                $('#ftpBrowserErrorContainer').css('display', 'block');
                $('#ftpBrowserFolderList').css('display', 'none');
                $('#ftpBrowserCrumbs').css('display', 'none');
				$('#ftpBrowserWrapper').css('display', 'none');
            },
            false
        );
    };

    /**
     * Creates a directory link for the FTP browser UI
     *
     * @param  path       The directory to link to
     * @param  label      How to display it
     * @param  container  The containing element
     */
    akeeba.Transfer.FtpBrowser.createLink = function(path, label, container, ftpObject)
    {
        if (typeof ftpObject == 'undefined')
        {
            ftpObject = akeeba.Transfer.FtpBrowser;
        }

        var row = $(document.createElement('tr'));
        var cell = $(document.createElement('td')).appendTo(row);

        var myElement = $(document.createElement('a'))
            .text(label)
            .click(function () {
                ftpObject.params.directory = path;
                ftpObject.open();
            })
            .appendTo(cell);
        row.appendTo($(container));
    };

    /**
     * Adds a breadcrumb to the FTP browser
     *
     * @param  crumb         How to display it
     * @param  relativePath  The relative path to the current directory
     * @param  container     The containing element
     */
    akeeba.Transfer.FtpBrowser.addCrumb = function (crumb, relativePath, container, ftpObject)
    {
        if (typeof ftpObject == 'undefined')
        {
            ftpObject = akeeba.Transfer.FtpBrowser;
        }

        var li = $(document.createElement('li'));

        $(document.createElement('a'))
            .html(crumb)
            .click(function (e) {
                ftpObject.params.directory = relativePath;
                ftpObject.open();
                e.preventDefault();
            })
            .appendTo(li);

        $(document.createElement('span'))
            .addClass('divider')
            .html('/')
            .appendTo(li);

        li.appendTo(container);
    };

    /**
     * Initialises an SFTP folder browser
     *
     * @param  key        The Akeeba Engine configuration key of the field holding the SFTP directory we're outputting
     * @param  paramsKey  The Akeeba Engine configuration key prefix of the fields holding SFTP connection information
     */
    akeeba.Transfer.SftpBrowser.initialise = function()
    {
        akeeba.Transfer.SftpBrowser.params.host = $('#akeeba-transfer-ftp-host').val();
        akeeba.Transfer.SftpBrowser.params.port = $('#akeeba-transfer-ftp-port').val();
        akeeba.Transfer.SftpBrowser.params.username = $('#akeeba-transfer-ftp-username').val();
        akeeba.Transfer.SftpBrowser.params.password = $('#akeeba-transfer-ftp-password').val();
        akeeba.Transfer.SftpBrowser.params.directory = $('#akeeba-transfer-ftp-directory').val();
        akeeba.Transfer.SftpBrowser.params.privKey = $('#akeeba-transfer-ftp-privatekey').val();
        akeeba.Transfer.SftpBrowser.params.pubKey = $('#akeeba-transfer-ftp-pubkey').val();

        akeeba.Transfer.SftpBrowser.open();
    };

    /**
     * Opens the SFTP directory browser
     */
    akeeba.Transfer.SftpBrowser.open = function ()
    {
        var ftp_dialog_element = $("#sftpdialog");

        ftp_dialog_element.css('display', 'block');
        ftp_dialog_element.removeClass('ui-state-error');

        $('#sftpdialogOkButton').click(function(e){
            akeeba.Transfer.FtpBrowser.callback(akeeba.Transfer.SftpBrowser.params.directory);
            $("#sftpdialog").modal('hide');
        });

        ftp_dialog_element.modal('show');

        $('#sftpBrowserErrorContainer').css('display', 'none');
        $('#sftpBrowserFolderList').html('');
        $('#sftpBrowserCrumbs').html('');

        var data = {
            'view':         'sftpbrowser',
            'task':         'browse',
            'host':         akeeba.Transfer.SftpBrowser.params.host,
            'port':         akeeba.Transfer.SftpBrowser.params.port,
            'username':     akeeba.Transfer.SftpBrowser.params.username,
            'password':     akeeba.Transfer.SftpBrowser.params.password,
            'directory':    akeeba.Transfer.SftpBrowser.params.directory,
            'privkey':      akeeba.Transfer.SftpBrowser.params.privKey,
            'pubkey':       akeeba.Transfer.SftpBrowser.params.pubKey
        };

        // Do AJAX call & Render results
        akeeba.System.doAjax(
            data,
            function (data)
            {
                var akScrumbs = $('#ak_scrumbs');
                if (data.error != false)
                {
                    // An error occured
                    $('#sftpBrowserError').html(data.error);
                    $('#sftpBrowserErrorContainer').css('display', 'block');
                    $('#sftpBrowserFolderList').css('display', 'none');
					$('#sftpBrowserWrapper').css('display', 'none');

                    akScrumbs.css('display', 'none');
                }
                else
                {
                    // Create the interface
                    $('#ftpBrowserErrorContainer').css('display', 'none');
					$('#sftpBrowserWrapper').css('display', 'block');

                    // Display the crumbs
                    if (!empty(data.breadcrumbs)) {
                        akScrumbs.css('display', 'block');
                        akScrumbs.html('');
                        var relativePath = '/';

                        akeeba.Transfer.FtpBrowser.addCrumb(akeeba.Transfer.translations['UI-ROOT'], '/', akScrumbs, akeeba.Transfer.SftpBrowser);

                        $.each(data.breadcrumbs, function (i, crumb) {
                            relativePath += '/' + crumb;

                            akeeba.Transfer.FtpBrowser.addCrumb(crumb, relativePath, $('#ak_scrumbs'), akeeba.Transfer.SftpBrowser);
                        });
                    } else {
                        $('#sftpBrowserCrumbs').css('display', 'none');
                    }

                    // Display the list of directories
                    if (!empty(data.list)) {
                        $('#sftpBrowserFolderList').css('display', 'block');

                        if(!akeeba.Transfer.SftpBrowser.params.directory)
                        {
                            akeeba.Transfer.SftpBrowser.params.directory = data.directory;
                        }

                        $.each(data.list, function (i, item) {
                            akeeba.Transfer.FtpBrowser.createLink(data.directory + '/' + item, item, $('#sftpBrowserFolderList'), akeeba.Transfer.SftpBrowser);
                        });
                    } else {
                        $('#sftpBrowserFolderList').css('display', 'none');
                    }
                }
            },
            function (message) {
                $('#sftpBrowserError').html(message);
                $('#sftpBrowserErrorContainer').css('display', 'block');
                $('#sftpBrowserFolderList').css('display', 'none');
                $('#sftpBrowserCrumbs').css('display', 'none');
				$('#sftpBrowserWrapper').css('display', 'none');
            },
            false
        );
    };

    akeeba.Transfer.applyConnection = function()
    {
		$('#akeeba-transfer-ftp-error').hide();
		$('#akeeba-transfer-apply-loading').show();

        var button = $('#akeeba-transfer-btn-apply');
        button.attr('disabled', 'disabled');

		$('#akeeba-transfer-ftp-method').attr('disabled', 'disabled');
        $('#akeeba-transfer-ftp-host').parent().parent().hide();
        $('#akeeba-transfer-ftp-port').parent().parent().hide();
        $('#akeeba-transfer-ftp-username').parent().parent().hide();
        $('#akeeba-transfer-ftp-password').parent().parent().hide();
        $('#akeeba-transfer-ftp-pubkey').parent().parent().hide();
        $('#akeeba-transfer-ftp-privatekey').parent().parent().hide();
        $('#akeeba-transfer-ftp-directory').parent().parent().parent().hide();
        $('#akeeba-transfer-ftp-passive-container').hide();

        var method = $('#akeeba-transfer-ftp-method').val();

		if (method == 'manual')
		{
			$('#akeeba-transfer-btn-apply').parent().hide();
			$('#akeeba-transfer-manualtransfer').show();

			return;
		}

        var data = {
            'task':         'applyConnection',
            'method':       method,
            host:           $('#akeeba-transfer-ftp-host').val(),
            port:           $('#akeeba-transfer-ftp-port').val(),
            username:       $('#akeeba-transfer-ftp-username').val(),
            password:       $('#akeeba-transfer-ftp-password').val(),
            directory:      $('#akeeba-transfer-ftp-directory').val(),
            passive:        $('#akeeba-transfer-ftp-passive1').is(':checked') ? 1 : 0,
            private:        $('#akeeba-transfer-ftp-privatekey').val(),
            public:         $('#akeeba-transfer-ftp-pubkey').val()
        };

        // Construct the query
        akeeba.System.doAjax(
            data,
            function (res)
            {
                $('#akeeba-transfer-apply-loading').hide();

                if (!res.status)
				{
					$('#akeeba-transfer-btn-apply').removeAttr('disabled');
					$('#akeeba-transfer-ftp-method').removeAttr('disabled');

					$('#akeeba-transfer-ftp-error').html(res.message);
					$('#akeeba-transfer-ftp-error').show();

					$('#akeeba-transfer-ftp-error').focus();

					akeeba.Transfer.onTransferMethodChange();

					return;
				}

                // Successful connection; perform preliminary checks and upload Kickstart
                akeeba.Transfer.uploadKickstart();

            },
            function(res){
                $('#akeeba-transfer-apply-loading').hide();

                $('#akeeba-transfer-btn-apply').removeAttr('disabled');
                $('#akeeba-transfer-ftp-method').removeAttr('disabled');
                $('#akeeba-transfer-ftp-error').html(akeeba.Transfer.translations['UI-TESTFTP-FAIL']).show().focus();

                akeeba.Transfer.onTransferMethodChange();
            }
            , false, 15000
        );
    };

    akeeba.Transfer.uploadKickstart = function()
    {
        var stepKickstart = $('#akeeba-transfer-upload-lbl-kickstart');
        var stepArchive = $('#akeeba-transfer-upload-lbl-archive');
        var uploadErrorBox = $('#akeeba-transfer-upload-error');

        uploadErrorBox.hide();
        stepKickstart.removeClass('label-default').removeClass('label-success')
            .removeClass('label-important').addClass('label-warning');
        stepArchive.addClass('label-default').removeClass('label-success')
            .removeClass('label-important').removeClass('label-warning');

        $('#akeeba-transfer-upload-area-kickstart').hide();
        $('#akeeba-transfer-upload-area-upload').show();
        $('#akeeba-transfer-upload').show();

        var data = {
            'task':         'initialiseUpload'
        };

        // Construct the query
        akeeba.System.doAjax(
            data,
            function (res)
            {
                if (!res.status)
                {
                    stepKickstart.addClass('label-important').removeClass('label-warning');
                    uploadErrorBox.html(res.message);
                    uploadErrorBox.show();

                    return;
                }

                // Success. Now let's upload the backup archive.
                akeeba.Transfer.uploadArchive(1);
            }, null, false, 150000
        );
    };

    akeeba.Transfer.uploadArchive = function(start)
    {
        if (start == undefined)
        {
            start = 0;
        }

        var stepKickstart = $('#akeeba-transfer-upload-lbl-kickstart');
        var stepArchive = $('#akeeba-transfer-upload-lbl-archive');
        var uploadErrorBox = $('#akeeba-transfer-upload-error');

        uploadErrorBox.hide();
        stepKickstart.removeClass('label-default').addClass('label-success')
            .removeClass('label-important').removeClass('label-warning');
        stepArchive.removeClass('label-default').removeClass('label-success')
            .removeClass('label-important').addClass('label-warning');

        var data = {
            'task':         'upload',
            'start':        start
        };

        // Construct the query
        akeeba.System.doAjax(
            data,
            function (res)
            {
                if (!res.result)
                {
                    stepArchive.addClass('label-important').removeClass('label-warning');
                    uploadErrorBox.html(res.message);
                    uploadErrorBox.show();

                    return;
                }

                // Success. Let's update the displayed information and step through the upload.
                if (res.done)
                {
                    $('#akeeba-transfer-upload-percent').html('100 %');
                    $('#akeeba-transfer-upload-size').html('');

                    // We are done. Launch Kickstart.
                    var urlBox = $('#akeeba-transfer-url');
                    var url = urlBox.val().replace(/\/$/, '') + '/kickstart.php';

                    $('#akeeba-transfer-upload-area-kickstart').show();
                    $('#akeeba-transfer-upload-area-upload').hide();

                    $('#akeeba-transfer-upload-btn-kickstart').attr('href', url);

                    return;
                }

                var donePercent = 0;
                var totalSize = res.totalSize * 1.0;
                var doneSize = res.doneSize * 1.0;

                if ((totalSize > 0) && (doneSize > 0))
                {
                    donePercent = 100 * (doneSize / totalSize);
                }

                $('#akeeba-transfer-upload-percent').html(donePercent.toFixed(2) + '%');
                $('#akeeba-transfer-upload-size').html(doneSize.toFixed(0) + ' / ' + totalSize.toFixed(0) + ' bytes');

                // Using setTimeout prevents recursive call issues.
                window.setTimeout(function(){
                    akeeba.Transfer.uploadArchive(0);
                }, 50);
            }, null, false, 150000
        );
    }

    akeeba.Transfer.initFtpSftpBrowser = function()
    {
        var driver = $('#akeeba-transfer-ftp-method').val();

        if ((driver == 'ftp') || (driver == 'ftps'))
        {
            akeeba.Transfer.FtpBrowser.initialise()
        }
        else if (driver == 'sftp')
        {
            akeeba.Transfer.SftpBrowser.initialise()
        }

        return false;
    };

    akeeba.Transfer.testFtpSftpConnection = function()
    {
        var driver = $('#akeeba-transfer-ftp-method').val();

        if ((driver == 'ftp') || (driver == 'ftps'))
        {
            akeeba.Transfer.FtpTest.testConnection('akeeba-transfer-btn-testftp');
        }
        else if (driver == 'sftp')
        {
            akeeba.Transfer.SftpTest.testConnection('akeeba-transfer-btn-testftp');
        }

        return false;
    };

    akeeba.Transfer.FtpBrowser.callback = function(directory)
    {
        if (directory.substring(0, 2) == '//')
        {
            directory = directory.substring(1);
        }

        $('#akeeba-transfer-ftp-directory').val(directory);
    };

//=============================================================================
// 							I N I T I A L I Z A T I O N
//=============================================================================
    $(document).ready(function(){
        $('#akeeba-transfer-ftp-method').change(akeeba.Transfer.onTransferMethodChange);
    });

}(akeeba.jQuery));