<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://mySites.guru/
 * @see       https://www.phil-taylor.com/
 *
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 *
 * If you have any questions regarding this code, please contact phil@phil-taylor.com
 */

class bfEvents
{
    // on viewing global config
    const onViewedGlobalConfig = 'bf.config.global.viewed';
    // On saving global config
    const onSavedGlobalConfig = 'bf.config.global.saved';
    // On viewing component options
    const onViewedComponentOptions = 'bf.config.component.viewed';
    // On saving component options
    const onSavedComponentOptions = 'bf.config.component.saved';
    // When an admin logs into /administrator/
    const onAdminLogin = 'bf.admin.login';
    // When an admin logs out of /administrator/
    const onAdminLogout = 'bf.admin.logout';
    // On viewing a user in admin
    const onUserViewed = 'bf.user.viewed';
    // When a user logs out
    const onUserLogout = 'bf.user.logout';
    // When a user is modified
    const onUserModified = 'bf.user.modified';
    // When a new user is created
    const onUserCreated = 'bf.user.created';
    // When a configured file alert file is modified
    const onFileModified = 'bf.file.modified';
    // When a snapshot is taken
    const onSnapshotTaken = 'bf.snapshot.started';
    // When an audit is started
    const onAuditStarted = 'bf.audit.started';
    // When an audit is finished
    const onAuditFinished = 'bf.audit.finished';
    // When content is searched for in com_search
    const onContentSearch = 'bf.search.performed';
    // when our connector is automatically upgraded
    const onConnectorUpgrade = 'bf.connector.upgraded';
    // when connection error resolved
    const onConnectorReconnect = 'bf.connector.reconnect';
    // Not going to log normal users login or logout - not a maintenance or security event
    const onUserLogin = 'bf.user.login';
    // @TODO
    const onFileDeleted = 'bf.file.deleted';
    // @TODO
    const onBackupStarted = 'bf.backup.started';
    // @TODO
    const onBackupFinished = 'bf.backup.finished';
    // @TODO
    const onBackupProfileChanged = 'bf.backup.profile.changed';
    // @TODO
    const onDowntimeStarted = 'bf.downtime.started';
    // @TODO
    const onDowntimeFinished = 'bf.downtime.finished';
    // @TODO
    const onExtensionInstall = 'bf.ext.install';
    // @TODO
    const onExtensionRemove = 'bf.ext.remove';
    // @TODO
    const onExtensionUpgrade = 'bf.ext.upgrade';
    // @TODO
    const onExtensionPublish = 'bf.ext.publish';
    // @TODO
    const onExtensionUnpublish = 'bf.ext.unpublish';
    // @TODO
    const onToolApplied = 'bf.tools.applied';
    // @TODO
    const onAlertTriggered = 'bf.alert.triggered';
    // @TODO
    const onSavedPluginOptions = 'bf.config.plugin.saved';
}
