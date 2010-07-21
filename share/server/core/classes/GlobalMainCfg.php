<?php
/*****************************************************************************
 *
 * GlobalMainCfg.php - Class for handling the main configuration of NagVis
 *
 * Copyright (c) 2004-2010 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class GlobalMainCfg {
	private $CACHE;
	
	protected $config;
	protected $runtimeConfig;
	protected $stateWeight;
	
	protected $configFile;
	
	protected $validConfig;
	
	/**
	 * Class Constructor
	 *
	 * @param	String	$configFile			String with path to config file
	 * @author Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct($configFile) {
		$this->config = Array();
		$this->runtimeConfig = Array();
		
		$this->validConfig = Array(
			'global' => Array(
				'audit_log' => Array('must' => 1,
					'editable' => 1,
					'default' => true,
					'match' => MATCH_BOOLEAN),
				'authmodule' => Array('must' => 1,
					'editable' => 1,
					'default' => 'CoreAuthModSQLite',
					'match' => MATCH_STRING),
				'authorisationmodule' => Array('must' => 1,
					'editable' => 1,
					'default' => 'CoreAuthorisationModSQLite',
					'match' => MATCH_STRING),
				'dateformat' => Array('must' => 1,
					'editable' => 1,
					'default' => 'Y-m-d H:i:s',
					'match' => MATCH_STRING),
				'displayheader' => Array('must' => 1,
					'editable' => 1,
					'deprecated' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'language_detection' => Array('must' => 1,
					'editable' => 1,
					'array' => true,
					'default' => Array('user', 'session', 'browser', 'config'),
					'match' => MATCH_STRING_NO_SPACE),
				'language_available' => Array('must' => 1,
					'editable' => 1,
					'array' => true,
					'default' => Array('de_DE', 'en_US', 'es_ES', 'fr_FR', 'pt_BR'),
					'match' => MATCH_STRING_NO_SPACE),
				'language' => Array('must' => 1,
					'editable' => 1,
					'default' => 'en_US',
					'field_type' => 'dropdown',
					'match' => MATCH_LANGUAGE_EMPTY),
				'logonmodule' => Array('must' => 1,
					'editable' => 1,
					'default' => 'LogonMixed',
					'match' => MATCH_STRING),
				'logonenvvar' => Array('must' => 1,
					'editable' => 1,
					'default' => 'REMOTE_USER',
					'depends_on' => 'logonmodule',
					'depends_value' => 'LogonEnv',
					'match' => MATCH_STRING),
				'logonenvcreateuser' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'depends_on' => 'logonmodule',
					'depends_value' => 'LogonEnv',
					'match' => MATCH_BOOLEAN),
				'logonenvcreaterole' => Array('must' => 1,
					'editable' => 1,
					'default' => 'Guests',
					'depends_on' => 'logonmodule',
					'depends_value' => 'LogonEnv',
					'match' => MATCH_STRING),
				'refreshtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '60',
					'match' => MATCH_INTEGER),
				//FIXME: auto detect
				'sesscookiedomain' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING),
				'sesscookiepath' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING),
				'sesscookieduration' => Array('must' => 1,
					'editable' => 1,
					'default' => '86400',
					'match' => MATCH_STRING),
				'startmodule' => Array('must' => 1,
					'editable' => 1,
					'default' => 'Overview',
					'match' => MATCH_STRING),
				'startaction' => Array('must' => 1,
					'editable' => 1,
					'default' => 'view',
					'match' => MATCH_STRING)),
			'defaults' => Array(
				'backend' => Array('must' => 0,
					'editable' => 0,
					'default' => 'live_1',
					'field_type' => 'dropdown',
					'match' => MATCH_STRING_NO_SPACE),
				'backgroundcolor' => Array('must' => 0,
					'editable' => 1,
					'default' => 'transparent',
					'match' => MATCH_COLOR),
				'contextmenu' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'contexttemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'contextmenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'stylesheet' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE),
				'eventbackground' => Array('must' => 0,
					'editable' => 1,
					'default' => '0',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'eventhighlight' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'eventhighlightinterval' => Array('must' => 0,
					'editable' => 1,
					'default' => '500',
					'depends_on' => 'eventhighlight',
					'depends_value' => 1,
					'match' => MATCH_INTEGER),
				'eventhighlightduration' => Array('must' => 0,
					'editable' => 1,
					'default' => '10000',
					'depends_on' => 'eventhighlight',
					'depends_value' => 1,
					'match' => MATCH_INTEGER),
				'eventlog' => Array('must' => 0,
					'editable' => 1,
					'default' => '0',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'eventloglevel' => Array('must' => 0,
					'editable' => 1,
					'default' => 'info',
					'depends_on' => 'eventlog',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'eventlogheight' => Array('must' => 0,
					'editable' => 1,
					'default' => '100',
					'depends_on' => 'eventlog',
					'depends_value' => 1,
					'match' => MATCH_INTEGER),
				'eventloghidden' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'depends_on' => 'eventlog',
					'depends_value' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'eventscroll' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'eventsound' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'headermenu' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'headertemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'headermenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'headertemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'headermenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'headerfade' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'depends_on' => 'headermenu',
					'depends_value' => 1,
					'match' => MATCH_BOOLEAN),
				'hovermenu' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'hovertemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'hovertimeout' => Array('must' => 0,
					'editable' => 1,
					'default' => '5',
					'deprecated' => 1,
					'match' => MATCH_INTEGER),
				'hoverdelay' => Array('must' => 0,
					'editable' => 1,
					'default' => '0',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'match' => MATCH_INTEGER),
				'hoverchildsshow' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'hoverchildslimit' => Array('must' => 0,
					'editable' => 1,
					'default' => '10',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'match' => MATCH_INTEGER_PRESIGN),
				'hoverchildsorder' => Array('must' => 0,
					'editable' => 1,
					'default' => 'asc',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'match' => MATCH_ORDER),
				'hoverchildssort' => Array('must' => 0,
					'editable' => 1,
					'default' => 's',
					'depends_on' => 'hovermenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'icons' => Array('must' => 1,
					'editable' => 1,
					'default' => 'std_medium',
					'field_type' => 'dropdown',
					'match' => MATCH_STRING_NO_SPACE),
				'onlyhardstates' => Array('must' => 0,
					'editable' => 1,
					'default' => 0,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'recognizeservices' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'showinlists' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'urltarget' => Array('must' => 0,
					'editable' => 1,
					'default' => '_self',
					'match' => MATCH_STRING_NO_SPACE),
				'mapurl' => Array('must' => 0,
					'default' => '[htmlbase]/index.php?mod=Map&act=view&show=[map_name]',
					'match' => MATCH_STRING_URL_EMPTY),
				'hosturl' => Array('must' => 0,
					'default' => '[htmlcgi]/status.cgi?host=[host_name]',
					'match' => MATCH_STRING_URL_EMPTY),
				'hostgroupurl' => Array('must' => 0,
					'default' => '[htmlcgi]/status.cgi?hostgroup=[hostgroup_name]',
					'match' => MATCH_STRING_URL_EMPTY),
				'serviceurl' => Array('must' => 0,
					'default' => '[htmlcgi]/extinfo.cgi?type=2&host=[host_name]&service=[service_description]',
					'match' => MATCH_STRING_URL_EMPTY),
				'servicegroupurl' => Array('must' => 0,
					'default' => '[htmlcgi]/status.cgi?servicegroup=[servicegroup_name]&style=detail',
					'match' => MATCH_STRING_URL_EMPTY),
				'usegdlibs' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
          'deprecated' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN)),
			'wui' => Array(
				'allowedforconfig' => Array(
					'must' => 0,
					'editable' => 1,
					'deprecated' => 1,
					'default' => Array('EVERYONE'),
					'match' => MATCH_STRING),
				'autoupdatefreq' => Array('must' => 0,
					'editable' => 1,
					'default' => '25',
					'field_type' => 'dropdown',
					'match' => MATCH_INTEGER),
				'headermenu' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'headertemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'headermenu',
					'depends_value' => 1,
					'field_type' => 'dropdown',
					'match' => MATCH_STRING_NO_SPACE),
				'maplocktime' => Array('must' => 0,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'grid_show' => Array('must' => 0,
					'editable' => 1,
					'default' => 0,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'grid_color' => Array('must' => 0,
					'editable' => 1,
					'depends_on' => 'grid_show',
					'depends_value' => 1,
					'default' => '#D5DCEF',
					'match' => MATCH_COLOR),
				'grid_steps' => Array('must' => 0,
					'editable' => 1,
					'default' => 32,
					'depends_on' => 'grid_show',
					'depends_value' => 1,
					'match' => MATCH_INTEGER)),
			'paths' => Array(
				'base' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_PATH),
				'cfg' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'icon' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'images' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'js' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'shape' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'language' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'class' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'map' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'var' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'sharedvar' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'mapcfg' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'automapcfg' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'gadget' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'templates' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlbase' => Array('must' => 1,
					'editable' => 1,
					'default' => '/nagvis',
					'match' => MATCH_STRING_PATH),
				'htmlcgi' => Array('must' => 1,
					'editable' => 1,
					'field_type' => 'hidden',
					'default' => '/nagios/cgi-bin',
					'match' => MATCH_STRING_URL),
				'htmlcss' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlgadgets' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlimages' => Array('must' => 0,
					'editable' => 0,
					'default' => '/',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmljs' => Array('must' => 1,
					'editable' => 1,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmltemplates' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmltemplateimages' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlicon' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlshape' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlsounds' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlstyles' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlmap' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH),
				'htmlsharedvar' => Array('must' => 0,
					'editable' => 0,
					'default' => '',
					'field_type' => 'hidden',
					'match' => MATCH_STRING_PATH)),
			'backend' => Array(
				'backendtype' => Array('must' => 1,
					'editable' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE),
				'backendid' => Array('must' => 1,
					'editable' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE),
				'statushost' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE_EMPTY),
				'htmlcgi' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_URL),
				'custom_1' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_URL_EMPTY),
				'custom_2' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_URL_EMPTY),
				'custom_3' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_STRING_URL_EMPTY),
				'options' => Array()),
			'rotation' => Array(
				'rotationid' => Array('must' => 1,
					'editable' => 1,
					'default' => 'demo',
					'match' =>MATCH_STRING_NO_SPACE),
				'interval' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_INTEGER),
				'maps' => Array('must' => 1,
					'editable' => 1,
					'default' => 'demo,demo2',
					'match' => MATCH_STRING)),
			'automap' => Array(
				'defaultparams' => Array('must' => 0,
					'editable' => 1,
					'default' => '&childLayers=2',
					'match' => MATCH_STRING_URL),
				'defaultroot' => Array('must' => 0,
					'editable' => 1,
					'default' => 'localhost',
					'match' => MATCH_STRING_NO_SPACE_EMPTY),
				'graphvizpath' => Array('must' => 0,
					'editable' => 1,
					'default' => '/usr/local/bin/',
					'match' => MATCH_STRING_PATH),
				'showinlists' => Array('must' => 0,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN)
				),
			'index' => Array(
				'cellsperrow' => Array('must' => 0,
					'editable' => 1,
					'default' => '4',
					'match' => MATCH_INTEGER),
				'headermenu' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'headertemplate' => Array('must' => 0,
					'editable' => 1,
					'default' => 'default',
					'depends_on' => 'headermenu',
					'depends_value' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'showautomaps' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'showmaps' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'showgeomap' => Array('must' => 0,
					'editable' => 1,
					'default' => 0,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'showmapthumbs' => Array('must' => 0,
					'editable' => 1,
					'default' => 0,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'showrotations' => Array('must' => 0,
					'editable' => 1,
					'default' => 1,
					'field_type' => 'boolean',
					'match' => MATCH_BOOLEAN),
				'backgroundcolor' => Array('must' => 0,
					'editable' => 1,
					'default' => '#ffffff',
					'match' => MATCH_COLOR)),
			'worker' => Array(
				'interval' => Array('must' => 0,
					'editable' => 1,
					'default' => '10',
					'match' => MATCH_INTEGER),
				'updateobjectstates' => Array('must' => 0,
					'editable' => 1,
					'default' => '30',
					'match' => MATCH_INTEGER),
				'requestmaxparams' => Array('must' => 0,
					'editable' => 1,
					'default' => 0,
					'match' => MATCH_INTEGER),
				'requestmaxlength' => Array('must' => 0,
					'editable' => 1,
					'default' => 1900,
					'match' => MATCH_INTEGER)),
			'states' => Array(
				'unreachable' => Array('must' => 1,
					'editable' => 1,
					'default' => '9',
					'match' => MATCH_INTEGER),
				'unreachable_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'unreachable_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'unreachable_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#F1811B',
					'match' => MATCH_COLOR),
				'unreachable_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#F1811B',
					'match' => MATCH_COLOR),
				'unreachable_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => 'std_unreachable.mp3',
					'match' => MATCH_MP3_FILE),
				'down' => Array('must' => 1,
					'editable' => 1,
					'default' => '8',
					'match' => MATCH_INTEGER),
				'down_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'down_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'down_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FF0000',
					'match' => MATCH_COLOR),
				'down_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FF0000',
					'match' => MATCH_COLOR),
				'down_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => 'std_down.mp3',
					'match' => MATCH_MP3_FILE),
				'critical' => Array('must' => 1,
					'editable' => 1,
					'default' => '7',
					'match' => MATCH_INTEGER),
				'critical_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'critical_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '5',
					'match' => MATCH_INTEGER),
				'critical_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FF0000',
					'match' => MATCH_COLOR),
				'critical_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FF0000',
					'match' => MATCH_COLOR),
				'critical_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => 'std_critical.mp3',
					'match' => MATCH_MP3_FILE),
				'warning' => Array('must' => 1,
					'editable' => 1,
					'default' => '6',
					'match' => MATCH_INTEGER),
				'warning_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '4',
					'match' => MATCH_INTEGER),
				'warning_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '4',
					'match' => MATCH_INTEGER),
				'warning_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FFFF00',
					'match' => MATCH_COLOR),
				'warning_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FFFF00',
					'match' => MATCH_COLOR),
				'warning_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => 'std_warning.mp3',
					'match' => MATCH_MP3_FILE),
				'unknown' => Array('must' => 1,
					'editable' => 1,
					'default' => '3',
					'match' => MATCH_INTEGER),
				'unknown_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '2',
					'match' => MATCH_INTEGER),
				'unknown_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '2',
					'match' => MATCH_INTEGER),
				'unknown_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FFCC66',
					'match' => MATCH_COLOR),
				'unknown_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#FFCC66',
					'match' => MATCH_COLOR),
				'unknown_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_MP3_FILE),
				'error' => Array('must' => 1,
					'editable' => 1,
					'default' => '3',
					'match' => MATCH_INTEGER),
				'error_ack' => Array('must' => 1,
					'editable' => 1,
					'default' => '2',
					'match' => MATCH_INTEGER),
				'error_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '2',
					'match' => MATCH_INTEGER),
				'error_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#0000FF',
					'match' => MATCH_COLOR),
				'error_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#0000FF',
					'match' => MATCH_COLOR),
				'error_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_MP3_FILE),
				'up' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'match' => MATCH_INTEGER),
				'up_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'match' => MATCH_INTEGER),
				'up_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#00FF00',
					'match' => MATCH_COLOR),
				'up_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#00FF00',
					'match' => MATCH_COLOR),
				'up_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_MP3_FILE),
				'ok' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'match' => MATCH_INTEGER),
				'ok_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '1',
					'match' => MATCH_INTEGER),
				'ok_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#00FF00',
					'match' => MATCH_COLOR),
				'ok_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#00FF00',
					'match' => MATCH_COLOR),
				'ok_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_MP3_FILE),
				'pending' => Array('must' => 1,
					'editable' => 1,
					'default' => '0',
					'match' => MATCH_INTEGER),
				'pending_downtime' => Array('must' => 1,
					'editable' => 1,
					'default' => '0',
					'match' => MATCH_INTEGER),
				'pending_bgcolor' => Array('must' => 1,
					'editable' => 1,
					'default' => '#C0C0C0',
					'match' => MATCH_COLOR),
				'pending_color' => Array('must' => 1,
					'editable' => 1,
					'default' => '#C0C0C0',
					'match' => MATCH_COLOR),
				'pending_sound' => Array('must' => 0,
					'editable' => 1,
					'default' => '',
					'match' => MATCH_MP3_FILE)),
			'internal' => Array(
				'version' => Array('must' => 1,
					'editable' => 0,
					'default' => CONST_VERSION,
					'locked' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'title' => Array('must' => 1,
					'editable' => 0,
					'default' => 'NagVis ' . CONST_VERSION,
					'locked' => 1,
					'match' => MATCH_STRING)));
		
		// Detect the cookie domain to use
		$this->setCookieDomainByEnv();
		
		// Try to get the base path via $_SERVER['SCRIPT_FILENAME']
		$this->validConfig['paths']['base']['default'] = $this->getBasePath();
		$this->setPathsByBase($this->getValue('paths','base'),$this->getValue('paths','htmlbase'));
		
		// Define the main configuration file
		$this->configFile = $configFile;
	}
	
	public function init() {
		// Do preflight checks
		// Only proceed when the configuration file exists and is readable
		if(!$this->checkNagVisConfigExists(TRUE) || !$this->checkNagVisConfigReadable(TRUE)) {
			return FALSE;
		}
		
		// Create instance of GlobalFileCache object for caching the config
		$this->CACHE = new GlobalFileCache(GlobalCore::getInstance(), $this->configFile, CONST_MAINCFG_CACHE.'-'.CONST_VERSION.'-cache');
		
		// Get the valid configuration definitions from the available backends
		$this->getBackendValidConf();
		
		if($this->CACHE->isCached(FALSE) !== -1) {
			$this->config = $this->CACHE->getCache();
		} else {
			
			// Read Main Config file, when succeeded cache it
			if($this->readConfig(TRUE)) {
				$this->CACHE->writeCache($this->config, TRUE);
			}
		}

		// Parse the state weight array
		$this->parseStateWeight();
		
		// want to reduce the paths in the NagVis config, but don't want to hardcode the paths relative from the bases
		$this->setPathsByBase($this->getValue('paths','base'),$this->getValue('paths','htmlbase'));
		
		// set default value
		$this->validConfig['rotation']['interval']['default'] = $this->getValue('global','refreshtime');
		$this->validConfig['backend']['htmlcgi']['default'] = $this->getValue('paths','htmlcgi');
	}
	
	/**
	 * Gets the cookie domain from the webservers environment and sets the 
	 * session cookie domain to this value
	 *
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function setCookieDomainByEnv() {
		if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== '') {
			$this->validConfig['global']['sesscookiedomain']['default'] = $_SERVER['SERVER_NAME'];
		}
	}
	
	/**
	 * Gets the valid configuration definitions from the available backends. The
	 * definitions were moved to the backends so it is easier to create new
	 * backends without any need to modify the main configuration
	 *
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function getBackendValidConf() {
		// Get the configuration options from the backends
		$aBackends = GlobalCore::getInstance()->getAvailableBackends();
		
		foreach($aBackends AS $backend) {
			$class = 'GlobalBackend'.$backend;
			
			// FIXME: Does not work in PHP 5.2 (http://bugs.php.net/bug.php?id=31318)
			//$this->validConfig['backend']['options'][$backend] = $class->getValidConfig();
			// I'd prefer to use the above but for the moment I use the fix below
			
			if (is_callable(array($class, 'getValidConfig'))) {
				$this->validConfig['backend']['options'][$backend] = call_user_func(Array('GlobalBackend'.$backend, 'getValidConfig'));
				//$this->validConfig['backend']['options'][$backend] = call_user_func('GlobalBackend'.$backend.'::getValidConfig');
			}
		}
	}
	
	/**
	 * Gets the base path 
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function setPathsByBase($base,$htmlBase) {
		$this->validConfig['paths']['cfg']['default'] = $base.'etc/';
		$this->validConfig['paths']['mapcfg']['default'] = $base.'etc/maps/';
		$this->validConfig['paths']['automapcfg']['default'] = $base.'etc/automaps/';
		
		$this->validConfig['paths']['var']['default'] = $base.'var/';
		$this->validConfig['paths']['sharedvar']['default'] = $base.'share/var/';
		$this->validConfig['paths']['htmlsharedvar']['default'] = $htmlBase.'/var/';
		
		$this->validConfig['paths']['language']['default'] = $base.'share/frontend/nagvis-js/locale';
		$this->validConfig['paths']['class']['default'] = $base.'share/server/core/classes/';

		$this->validConfig['paths']['htmlcss']['default'] = $htmlBase.'/frontend/nagvis-js/css/';
		
		$this->validConfig['paths']['js']['default'] = $base.'share/frontend/nagvis-js/js/';
		$this->validConfig['paths']['htmljs']['default'] = $htmlBase.'/frontend/nagvis-js/js/';
		
		$this->validConfig['paths']['images']['default'] = $base.'share/frontend/nagvis-js/images/';
		$this->validConfig['paths']['htmlimages']['default'] = $htmlBase.'/frontend/nagvis-js/images/';
		
		$this->validConfig['paths']['templates']['default'] = $base.'share/userfiles/templates/';
		$this->validConfig['paths']['htmltemplates']['default'] = $htmlBase.'/userfiles/templates/';
		
		$this->validConfig['paths']['htmlsounds']['default'] = $htmlBase.'/userfiles/sounds/';
		$this->validConfig['paths']['htmlstyles']['default'] = $htmlBase.'/userfiles/styles/';
		
		$this->validConfig['paths']['gadget']['default'] = $base.'share/userfiles/gadgets/';
		$this->validConfig['paths']['htmlgadgets']['default'] = $htmlBase.'/userfiles/gadgets/';
		
		$this->validConfig['paths']['icon']['default'] = $base.'share/userfiles/images/iconsets/';
		$this->validConfig['paths']['shape']['default'] = $base.'share/userfiles/images/shapes/';
		$this->validConfig['paths']['map']['default'] = $base.'share/userfiles/images/maps/';
		$this->validConfig['paths']['htmlicon']['default'] = $htmlBase.'/userfiles/images/iconsets/';
		$this->validConfig['paths']['htmlshape']['default'] = $htmlBase.'/userfiles/images/shapes/';
		$this->validConfig['paths']['htmlmap']['default'] = $htmlBase.'/userfiles/images/maps/';
		$this->validConfig['paths']['htmltemplateimages']['default'] = $htmlBase.'/userfiles/images/templates/';
		
		// This option directly relies on the configured htmlBase by default
		$this->validConfig['global']['sesscookiepath']['default'] = $htmlBase;
	}
	
	/**
	 * Gets the base path 
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 * @author	Roman Kyrylych <rkyrylych@op5.com>
	 */
	private function getBasePath() {
		// Go 3 levels up from nagvis/share/nagvis to nagvis base path
		return realpath(dirname($_SERVER['SCRIPT_FILENAME']) . '/../../..') . '/';
		// Note: the method below causes problems when <docroot>/nagvis is a symlink to <nagvis-base>/share
		// return realpath(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))).'/';
	}
	
	/**
	 * Reads the config file specified in $this->configFile
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function readConfig($printErr=1) {
		$numComments = 0;
		$sec = '';
		
		// read thx config file line by line in array $file
		$file = file($this->configFile);
		
		// Count the lines before the loop (only counts once)
		$countLines = count($file);
		
		// loop trough array
		for ($i = 0; $i < $countLines; $i++) {
			// cut spaces from beginning and end
			$line = trim($file[$i]);
			
			// don't read empty lines
			if(isset($line) && $line != '') {
				// get first char of actual line
				$firstChar = substr($line,0,1);
				
				// check what's in this line
				if($firstChar == ';') {
					// comment...
					$key = 'comment_'.($numComments++);
					$val = trim($line);
					
					if(isset($sec) && $sec != '') {
						$this->config[$sec][$key] = $val;
					} else {
						$this->config[$key] = $val;
					}
				} elseif ((substr($line, 0, 1) == '[') && (substr($line, -1, 1)) == ']') {
					// section
					$sec = strtolower(trim(substr($line, 1, strlen($line)-2)));
					
					// write to array
					if(preg_match('/^backend_/i', $sec)) {
						$this->config[$sec] = Array();
						$this->config[$sec]['backendid'] = str_replace('backend_','',$sec);
					} elseif(preg_match('/^rotation_/i', $sec)) {
						$this->config[$sec] = Array();
						$this->config[$sec]['rotationid'] = str_replace('rotation_','',$sec);
					} else {
						$this->config[$sec] = Array();
					}
				} else {
					// parameter...
					
					// separate string in an array
					$arr = explode('=',$line);
					// read key from array and delete it
					$key = strtolower(trim($arr[0]));
					unset($arr[0]);
					// build string from rest of array
					$val = trim(implode('=', $arr));
					
					// remove " at beginning and at the end of the string
					if ((substr($val,0,1) == '"') && (substr($val,-1,1)=='"')) {
						$val = substr($val,1,strlen($val)-2);
					}
					
					// Special options (Arrays)
					if(isset($this->validConfig[$sec][$key]['array']) && $this->validConfig[$sec][$key]['array'] === true) {
						$val = $this->stringToArray($val);
					} elseif(preg_match('/^rotation_/i', $sec) && $key == 'maps') {
						// Explode comma separated list to array
						$val = explode(',', $val);
						
						// Check if an element has a label defined
						foreach($val AS $id => $element) {
							if(preg_match("/^([^\[.]+:)?(\[(.+)\]|(.+))$/", $element, $arrRet)) {
								$label = '';
								$map = '';
								$automap = '';
								
								// When no label is set, set map or url as label
								if($arrRet[1] != '') {
									$label = substr($arrRet[1],0,-1);
								} else {
									if($arrRet[3] != '') {
										$label = $arrRet[3];
									} else {
										$label = $arrRet[4];
									}
								}
								
								if(isset($arrRet[4]) && $arrRet[4] != '') {
									// Remove leading/trailing spaces
									$map = $arrRet[4];
								}

								// Remove surrounding spaces
								$label = trim($label);
								$map = trim($map);
								
								// Check if the map is an automap
								if(substr($map, 0, 1) === '@') {
									$automap = substr($map, 1);
									$map = '';
								}
								
								// Save the extracted information to an array
								$val[$id] = Array('label' => $label, 'map' => $map, 'automap' => $automap, 'url' => $arrRet[3], 'target' => '');
							}
						}
					}
					
					// write in config array
					if(isset($sec)) {
						$this->config[$sec][$key] = $val;
					} else {
						$this->config[$key] = $val;
					}
				}
			} else {
				$sec = '';
				$this->config['comment_'.($numComments++)] = '';
			}
		}
		
		if($this->checkMainConfigIsValid(1)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks if the main config file is valid
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkMainConfigIsValid($printErr) {
		// check given objects and attributes
		foreach($this->config AS $type => &$vars) {
			if(!preg_match('/^comment_/',$type)) {
				if(isset($this->validConfig[$type]) || preg_match('/^(backend|rotation)_/', $type)) {
					// loop validConfig for checking: => missing "must" atributes
					if(preg_match('/^backend_/', $type)) {
						if(isset($this->validConfig['backend']['options'][$this->getValue($type,'backendtype')]) 
							 && is_array($this->validConfig['backend']['options'][$this->getValue($type,'backendtype')])) {
							$arrValidConfig = array_merge($this->validConfig['backend'], $this->validConfig['backend']['options'][$this->getValue($type,'backendtype')]);
						} else {
							$arrValidConfig = $this->validConfig['backend'];
						}
					} elseif(preg_match('/^rotation_/', $type)) {
						$arrValidConfig = $this->validConfig['rotation'];
					} else {
						$arrValidConfig = $this->validConfig[$type];
					}
					foreach($arrValidConfig AS $key => &$val) {
						if((isset($val['must']) && $val['must'] == '1')) {
							// value is "must"
							if($this->getValue($type,$key) == '') {
								// a "must" value is missing or empty
								new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('The needed attribute [ATTRIBUTE] is missing in section [TYPE] in main configuration file. Please take a look at the documentation.', Array('ATTRIBUTE' => $key, 'TYPE' => $type)));
								return FALSE;
							}
						}
					}
					
					// loop given elements for checking: => all given attributes valid
					foreach($vars AS $key => $val) {
						if(!preg_match('/^comment_/', $key)) {
							if(preg_match('/^backend_/', $type)) {
								if(isset($this->validConfig['backend']['options'][$this->getValue($type,'backendtype')]) 
									 && is_array($this->validConfig['backend']['options'][$this->getValue($type,'backendtype')])) {
									$arrValidConfig = array_merge($this->validConfig['backend'], $this->validConfig['backend']['options'][$this->getValue($type,'backendtype')]);
								} else {
									$arrValidConfig = $this->validConfig['backend'];
								}
							} elseif(preg_match('/^rotation_/', $type)) {
								$arrValidConfig = $this->validConfig['rotation'];
							} else {
								$arrValidConfig = $this->validConfig[$type];
							}
							
							if(!isset($arrValidConfig[$key])) {
								// unknown attribute
								if($printErr) {
									new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('Unknown value [ATTRIBUTE] used in section [TYPE] in main configuration file.', Array('ATTRIBUTE' => $key, 'TYPE' => $type)));
								}
								return FALSE;
							} elseif(isset($arrValidConfig[$key]['deprecated']) && $arrValidConfig[$key]['deprecated'] == 1) {
								// deprecated option
								if($printErr) {
									new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('The attribute [ATTRIBUTE] in section [TYPE] in main configuration file is deprecated. Please take a look at the documentation for updating your configuration.', Array('ATTRIBUTE' => $key, 'TYPE' => $type)));
								}
								return FALSE;
							} else {
								// Workaround to get the configured string back
								if(preg_match('/^rotation_/', $type) && $key == 'maps') {
									foreach($val AS $intId => $arrStep) {
										if(isset($arrStep['label']) && $arrStep['label'] != '') {
											$label = $arrStep['label'].':';
										}
										
										$val[$intId] = $label.$arrStep['url'].$arrStep['map'];
									}
								}
								
								if(isset($val) && is_array($val)) {
									$val = implode(',',$val);
								}
								
								// valid attribute, now check for value format
								if(!preg_match($arrValidConfig[$key]['match'],$val)) {
									// wrong format
									if($printErr) {
										new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('The attribute [ATTRIBUTE] in section [TYPE] in main configuration file does not match the correct format. Please review your configuration.', Array('ATTRIBUTE' => $key, 'TYPE' => $type)));
									}
									return FALSE;
								}
								
								// Check if the configured backend is defined in main configuration file
								if($type == 'defaults' && $key == 'backend' && !isset($this->config['backend_'.$val])) {
									if($printErr) {
										new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('backendNotDefined', Array('BACKENDID' => $val)));
									}
									return FALSE;
								}
							}
						}
					}	
				} else {
					// unknown type
					if($printErr) {
						new GlobalMessage('ERROR', GlobalCore::getInstance()->getLang()->getText('The section [TYPE] is not supported in main configuration. Please take a look at the documentation.', 'TYPE~'.$type));
					}
					return FALSE;
				}
			}
		}
		return TRUE;
	}
	
	/**
	 * Checks for existing config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkNagVisConfigExists($printErr) {
		return GlobalCore::getInstance()->checkExisting($this->configFile, $printErr);
	}
	
	/**
	 * Checks for readable config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkNagVisConfigReadable($printErr) {
		return GlobalCore::getInstance()->checkReadable($this->configFile, $printErr);
	}
	
	/**
	 * Returns the last modification time of the configuration file
	 *
	 * @return	Integer	Unix Timestamp
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getConfigFileAge() {
		return filemtime($this->configFile);
	}
	
	/**
	 * Public Adaptor for the isCached method of CACHE object
	 *
	 * @return  Boolean  Result
	 * @return  Integer  Unix timestamp of cache creation time or -1 when not cached
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function isCached() {
		return $this->CACHE->isCached();
	}
	
	/**
	 * Sets a config setting
	 *
	 * @param	String	$sec	Section
	 * @param	String	$var	Variable
	 * @param	String	$val	Value
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function setValue($sec, $var, $val) {
		if(isset($this->config[$sec][$var]) && $val == '') {
			// Value is empty and there is an entry in the config array
			unset($this->config[$sec][$var]);
		} elseif(!isset($this->config[$sec][$var]) && $val == '') {
			// Value is empty and there is nothing in config array yet
		} else {
			// Value is set
			if(isset($this->validConfig[$sec][$var]['array']) && $this->validConfig[$sec][$var]['array'] == true && !is_array($val)) {
				$val = $this->stringToArray($val);
			}
			
			$this->config[$sec][$var] = $val;
		}
		return TRUE;
	}
	
	/**
	 * Gets a config setting
	 *
	 * @param	String	$sec	Section
	 * @param	String	$var	Variable
	 * @param   Bool	$ignoreDefault Don't read default value
	 * @return	String	$val	Value
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 * FIXME: Needs to be simplified
	 */
	public function getValue($sec, $var, $ignoreDefault=FALSE) {
		// if nothing is set in the config file, use the default value
		// (Removed "&& is_array($this->config[$sec]) due to performance issues)
		if(isset($this->config[$sec]) && isset($this->config[$sec][$var])) {
			return $this->config[$sec][$var];
		} elseif(!$ignoreDefault) {
			// Speed up this method by first checking for major sections and only if 
			// they don't match try to match the backend_ and rotation_ sections
			if($sec == 'global' || $sec == 'defaults' || $sec == 'paths') {
				return $this->validConfig[$sec][$var]['default'];
			} elseif(strpos($sec, 'backend_') === 0) {
				
				// Choose the backend type (Configured one or the system default)
				$backendType = '';
				if(isset($this->config[$sec]['backendtype']) && $this->config[$sec]['backendtype'] !== '') {
					$backendType = $this->config[$sec]['backendtype'];
				} else {
					$backendType = $this->validConfig['backend']['backendtype']['default'];
				}
				
				// This value could be emtpy - so only check if it is set
				if(isset($this->validConfig['backend']['options'][$backendType][$var]['default'])) {
					return $this->validConfig['backend']['options'][$backendType][$var]['default'];
				} else {
					// This value could be emtpy - so only check if it is set
					if(isset($this->validConfig['backend'][$var]['default'])) {
						return $this->validConfig['backend'][$var]['default'];
					}
				}
			} elseif(strpos($sec, 'rotation_') === 0) {
				if(isset($this->config[$sec]) && is_array($this->config[$sec])) {
					return $this->validConfig['rotation'][$var]['default'];
				} else {
					return FALSE;
				}
			} else {
				return $this->validConfig[$sec][$var]['default'];
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * A getter to provide all section names of main configuration
	 *
	 * @return  Array  List of all sections as values
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getSections() {
		$aRet = Array();
		foreach($this->config AS $key => $var) {
			$aRet[] = $key;
		}
		return $aRet;
	}
	
	/**
	 * Sets a runtime config value
	 *
	 * @param	String	$var	Variable
	 * @param	String	$val	Value
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function setRuntimeValue($var, $val) {
		$this->runtimeConfig[$var] = $val;
		return TRUE;
	}
	
	/**
	 * Gets a runtime config value
	 *
	 * @param	String	$var	Variable
	 * @return	String	$val	Value
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getRuntimeValue($var) {
		if(isset($this->runtimeConfig[$var])) {
			return $this->runtimeConfig[$var];
		} else {
			return '';
		}
	}
	
	/**
	 * Parses general settings
	 *
	 * @return	String 	JSON Code
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function parseGeneralProperties() {
		$arr = Array();
		
		$arr['date_format'] = $this->getValue('global', 'dateformat');
		$arr['path_base'] = $this->getValue('paths','htmlbase');
		$arr['path_cgi'] = $this->getValue('paths','htmlcgi');
		$arr['path_sounds'] = $this->getValue('paths','htmlsounds');
		$arr['path_iconsets'] = $this->getValue('paths','htmlicon');
		$arr['path_templates'] = $this->getValue('paths','htmltemplates');
		$arr['path_images'] = $this->getValue('paths','htmlimages');
		$arr['path_server'] = $this->getValue('paths','htmlbase').'/server/core/ajax_handler.php';
		$arr['internal_title'] = $this->getValue('internal', 'title');
		
		return json_encode($arr);
	}
	
	/**
	 * Parses the settings for the javascript worker
	 *
	 * @return	String 	JSON Code
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function parseWorkerProperties() {
		$arr = Array();
		
		$arr['worker_interval'] = $this->getValue('worker', 'interval');
		$arr['worker_update_object_states'] = $this->getValue('worker', 'updateobjectstates');
		$arr['worker_request_max_params'] = $this->getValue('worker', 'requestmaxparams');
		$arr['worker_request_max_length'] = $this->getValue('worker', 'requestmaxlength');
		
		return json_encode($arr);
	}

	/**$
	 * Parses the state weight configuration array
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	private function parseStateWeight() {
		$arr = Array();

		foreach($this->validConfig['states'] AS $lowState => $aVal) {
			$key = explode('_', $lowState);
			
			// First create array when not exists
			if(!isset($arr[strtoupper($key[0])])) {
				$arr[strtoupper($key[0])] = Array();
			}
			
			if(isset($key[1])) {
				// ack/downtime
				$arr[strtoupper($key[0])][$key[1]] = $this->getValue('states', $lowState);
			} else {
				$arr[strtoupper($key[0])]['normal'] = $this->getValue('states', $lowState);
			}
		}

		$this->stateWeight = $arr;
	}
	
	/**
	 * Returns an array with the state weight configuration
	 *
	 * @return  Array
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getStateWeight() {
		return $this->stateWeight;
	}
	
	/**
	 * FIXME: Below you will find all WUI specific function. All need to be reviewed
	 */
	
	/**
	 * Gets all information about an object type
	 *
	 * @param   String  Type to get the information for
	 * @return  Array   The validConfig array
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	function getValidObjectType($type) {
		return $this->validConfig[$type];
	}
	
	/**
	 * Gets the valid configuration array
	 *
	 * @return	Array The validConfig array
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function getValidConfig() {
		return $this->validConfig;
	}
	
	/**
	 * Gets the configuration array
	 *
	 * @return	Array The validConfig array
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function getConfig() {
		return $this->config;
	}
	
	/**
	 * Sets a config section in the config array
	 *
	 * @param	String	$sec	Section
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function setSection($sec) {
		// Try to append new backends after already defined
		if(preg_match('/^backend_/', $sec)) { 
		    $lastBackendIndex = 0;
		    $i = 0;
		    // Loop all sections to find the last defined backend
		    foreach($this->config AS $type => $vars) {
		        // If the current section is a backend
						if(preg_match('/^backend_/', $type)) { 
		            $lastBackendIndex = $i;
		        }
		        $i++;
		    }
		    
		    if($lastBackendIndex != 0) {
		        // Append the new section after the already defined
		        $slicedBefore = array_slice($this->config, 0, ($lastBackendIndex + 1));
		        $slicedAfter = array_slice($this->config, ($lastBackendIndex + 1));
		        $tmp[$sec] = Array();
		        $this->config = array_merge($slicedBefore,$tmp,$slicedAfter);
		    } else {
		        // If no defined backend found, add it to the EOF
		        $this->config[$sec] = Array();
		    }
	    } else {
	        $this->config[$sec] = Array();
	    }
		
		return TRUE;
	}
	
	/**
	 * Deletes a config section in the config array
	 *
	 * @param	String	$sec	Section
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	function delSection($sec) {
		$this->config[$sec] = '';
		unset($this->config[$sec]);
		
		return TRUE;
	}
	
	/**
	 * Writes the config file completly from array $this->configFile
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function writeConfig() {
		// Check for config file write permissions
		if($this->checkNagVisConfigWriteable(1)) {
			$content = '';
			foreach($this->config as $key => $item) {
				if(is_array($item)) {
					$content .= '['.$key.']'."\n";
					foreach ($item as $key2 => $item2) {
						if(substr($key2,0,8) == 'comment_') {
							$content .= $item2."\n";
						} else {
							if(is_numeric($item2) || is_bool($item2)) {
								$content .= $key2."=".$item2."\n";
							} else {
								if(is_array($item2) && preg_match('/^rotation_/i', $key) && $key2 == 'maps') {
									$val = '';
									// Check if an element has a label defined
									foreach($item2 AS $intId => $arrStep) {
										$seperator = ',';
										$label = '';
										$step = '';
										
										if($intId == 0) {
											$seperator = '';
										}
										
										if(isset($arrStep['map']) && $arrStep['map'] != '') {
											$step = $arrStep['map'];
										} else {
											$step = '['.$arrStep['url'].']';
										}
										
										if(isset($arrStep['label']) && $arrStep['label'] != '' && $arrStep['label'] != $step) {
											$label = $arrStep['label'].':';
										}
										
										// Save the extracted information to an array
										$val .= $seperator.$label.$step;
									}
									
									$item2 = $val;
								}
								
								// Don't write the backendid/rotationid attributes (Are internal)
								if($key2 !== 'backendid' && $key2 !== 'rotationid') {
									if(isset($this->validConfig[$key][$key2]['array']) && $this->validConfig[$key][$key2]['array'] === true) {
										$item2 = implode(',', $item2);
									}
									
									$content .= $key2.'="'.$item2.'"'."\n";
								}
							}
						}
					}
				} elseif(substr($key,0,8) == 'comment_') {
					$content .= $item."\n";
				}
			}
			
			if(!$handle = fopen($this->configFile, 'w+')) {
				new GlobalMessage('ERROR', WuiCore::getInstance()->getLang()->getText('mainCfgNotWriteable'), WuiCore::getInstance()->getMainCfg()->getValue('paths','htmlbase'));
				return FALSE;
			}
			
			if(!fwrite($handle, $content)) {
				new GlobalMessage('ERROR', WuiCore::getInstance()->getLang()->getText('mainCfgCouldNotWriteMainConfigFile'), WuiCore::getInstance()->getMainCfg()->getValue('paths','htmlbase'));
				return FALSE;
			}
			
			fclose($handle);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for writeable config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function checkNagVisConfigWriteable($printErr) {
		return GlobalCore::getInstance()->checkWriteable($this->configFile, $printErr);
	}

	/**
   * Transforms a string option to an array with trimmed values
   *
   * @param  String  Comma separated value
   * @return Array   Exploded Array
   */
	private function stringToArray($val) {
		// Explode comma separated list to array
		$val = explode(',', $val);
		
		// Trim surrounding spaces on each element
		foreach($val AS $trimKey => $trimVal) {
			$val[$trimKey] = trim($trimVal);
		}

		return $val;
	}
}
?>
