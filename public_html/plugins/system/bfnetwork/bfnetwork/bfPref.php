<?php
/**
 * @author    Phil Taylor <phil@phil-taylor.com>
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Limited. All rights reserved.
 * @license   Commercial License - Not to be distributed!
 * @link      https://manage.myjoomla.com
 * @source    https://github.com/PhilETaylor/bfnetwork
 *
 */
require 'bfEncrypt.php';

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */
$preferences = new bfPreferences ($dataObj);

$preferences->run($dataObj->preferencesaction);

bfEncrypt::reply(bfReply::SUCCESS, $preferences->getPreferences());