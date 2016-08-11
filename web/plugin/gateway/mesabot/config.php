<?php
defined('_SECURE_') or die('Forbidden');

if (!class_exists('Mesabot')) {
	include $core_config['apps_path']['plug'] . '/gateway/mesabot/lib/Mesabot.php';
}

if (!class_exists('Playsms_Mesabot')) {
	include $core_config['apps_path']['plug'] . '/gateway/mesabot/lib/Playsms_Mesabot.php';
}

$data = registry_search(0, 'gateway', 'mesabot');
$plugin_config['mesabot'] = $data['gateway']['mesabot'];
$plugin_config['mesabot']['name'] = 'mesabot';

// smsc configuration
$plugin_config['mesabot']['_smsc_config_'] = array(
	'token' => _('Token'),
	/* 'module_sender' => _('Module sender ID'), */
	'datetime_timezone' => _('Module timezone') 
);
