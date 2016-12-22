<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>


<textarea style="display: none" id="documents_list">
    <div class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
        <div class="k-inline-modal">
            <div class="k-content-block preview extension-[%=metadata.extension%]">
                [%
                var url = Files.app.createRoute({option: 'com_docman', view: 'file', format: 'html', folder: folder, name: name});
                %]
                [% if (typeof image !== 'undefined' && metadata.image) {
                var width = metadata.image.width,
                height = metadata.image.height,
                ratio = 200 / (width > height ? width : height); %]
                <img src="[%=url%]" style="
                         width: [%=Math.min(ratio*width, width)%]px;
                         height: [%=Math.min(ratio*height, height)%]px
                     " alt="[%=name%]" border="0" />
                [% } else {
                var icon = 'default',
                extension = name.substr(name.lastIndexOf('.')+1).toLowerCase();

                kQuery.each(Files.icon_map, function(key, value) {
                if (kQuery.inArray(extension, value) !== -1) {
                icon = key;
                }
                });
                %]
                <p>
                    <span class="k-icon-document-[%=icon%] k-icon--size-xlarge"></span>
                </p>
                [% } %]
                <p>
                    [% if (typeof image !== 'undefined') { %]
                    <a class="k-button k-button--default k-button--small" href="[%=url%]" target="_blank">
                        <span class="k-icon-eye"></span> <?= translate('View'); ?>
                    </a>
                    [% } else { %]
                    <a class="k-button k-button--default k-button--small" href="[%=url%]" target="_blank" download="[%=name%]">
                        <span class="k-icon-data-transfer-download"></span> <?= translate('Download'); ?>
                    </a>
                    [% } %]
                </p>
            </div>
            <dl>
                <dt class="detail-label"><?= translate('Name'); ?></dt>
                <dd>[%=name%]</dd>
                <dt class="detail-label"><?= translate('Size'); ?></dt>
                <dd>[%=size.humanize()%]</dd>
                <dt class="detail-label"><?= translate('Modified'); ?></dt>
                <dd>[%=getModifiedDate(true)%]</dd>
            </dl>
            [% if (documents.length) { %]
            <h3><?= translate('Attached Documents') ?></h3>
            <ul>
                [% for (var i = 0; i < documents.length; i++) { var document = documents[i]; %]
                <li>
                    <a class="document-link" href="#" data-id="[%=document.id%]">[%=document.title%]</a>
                    <?= translate('in')?> <a class="category-link" href="#" data-category="[%=document.docman_category_id%]"><em>[%=document.category_title%]</em></a>
                </li>
                [% } %]
            </ul>
            [% } %]
        </div>
    </div>
</textarea>
