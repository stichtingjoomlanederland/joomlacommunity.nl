<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_HELPERS . '/helper.php';

class DiscussHoneypotHelper
{
	private $host = 'dnsbl.httpbl.org';
	public $ip;
	public $api;
	public $result;

	public $seg_prefix;
	public $seg_days;
	public $seg_threat;
	public $seg_type;

	function check()
	{
		if( $this->enabled() && $this->lookup() )
		{
			return $this->isSpammer();
		}

		return true;
	}

	function isSpammer()
	{
		$blocktype = $this->config->get( 'antispam_honeypot_block' );

		if( !empty( $blocktype ) )
		{
			switch( $blocktype )
			{
				case 'threat':
					return $this->blockByThreat();
					break;
				case 'type':
					return $this->blockByType();
					break;
				case 'both':
					return ( $this->blockByThreat() || $this->blockByType() );
					break;
			}
		}

		return false;
	}

	private function blockByThreat()
	{
		// if seg_type == 0, it means search engine, and seg_threat becomes identifier value instead of threat value
		if( $this->seg_type == 0 )
		{
			return false;
		}

		$threatValue = $this->config->get( 'antispam_honeypot_threatvalue' );

		return ( $this->seg_threat >= $threatValue );
	}

	private function blockByType()
	{
		if( $this->config->get( 'antispam_honeypot_threat_searchengine' ) && $this->seg_type == 0 ) return true;

		if( $this->config->get( 'antispam_honeypot_threat_suspicious' ) && in_array( $this->seg_type, array( 1, 3, 5, 7 ) ) ) return true;

		if( $this->config->get( 'antispam_honeypot_threat_harvester' ) && in_array( $this->$seg_type, array( 3, 6, 7 ) ) ) return true;

		if( $this->config->get( 'antispam_honeypot_threat_spammer' ) && in_array( $this->$seg_type, array( 4, 5, 6, 7 ) ) ) return true;

		return false;
	}

	private function reverseIp()
	{
		return implode( '.', array_reverse( explode( '.', $this->getIp() ) ) );
	}

	private function getIp()
	{
		if( empty( $this->ip ) )
		{
			$this->ip = JRequest::getVar('REMOTE_ADDR', '', 'SERVER');
		}

		return $this->ip;
	}

	private function getApi()
	{
		if( empty( $this->api ) )
		{
			$this->api = $this->config->get( 'antispam_honeypot_key' );
		}

		return $this->api;
	}

	private function getConfig()
	{
		if( empty( $this->config ) )
		{
			$this->config = DiscussHelper::getConfig();
		}

		return $this->config;
	}

	private function enabled()
	{
		$api = $this->getApi();
		return ( $this->config->get( 'antispam_honeypot' ) && !empty( $api ) );
	}

	private function lookup()
	{
		$link = $this->getApi() . '.' . $this->reverseIp() . '.' . $this->host;

		$this->result = dns_get_record( $link, DNS_A );

		if( empty( $this->result ) )
		{
			return false;
		}

		list( $this->seg_prefix, $this->seg_days, $this->seg_threat, $this->seg_type ) = $this->result;

		return true;
	}
}
