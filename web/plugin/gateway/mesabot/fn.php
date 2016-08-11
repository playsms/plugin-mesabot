<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

// hook_sendsms
// called by main sms sender
// return true for success delivery
// $smsc : smsc
// $sms_sender : sender mobile number
// $sms_footer : sender sms footer or sms sender ID
// $sms_to : destination sms number
// $sms_msg : sms message tobe delivered
// $gpid : group phonebook id (optional)
// $uid : sender User ID
// $smslog_id : sms ID
function mesabot_hook_sendsms($smsc, $sms_sender, $sms_footer, $sms_to, $sms_msg, $uid = '', $gpid = 0, $smslog_id = 0, $sms_type = 'text', $unicode = 0) {
	global $plugin_config;
	
	_log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to, 3, "mesabot_hook_sendsms");
	
	// override plugin gateway configuration by smsc configuration
	$plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);
	
	$sms_sender = stripslashes($sms_sender);
	if ($plugin_config['mesabot']['module_sender']) {
		$sms_sender = $plugin_config['mesabot']['module_sender'];
	}
	
	$sms_footer = stripslashes($sms_footer);
	$sms_msg = stripslashes($sms_msg);
	$ok = false;
	
	if ($sms_footer) {
		$sms_msg = $sms_msg . $sms_footer;
	}
	
	// no sender config yet	
	//if ($sms_sender && $sms_to && $sms_msg) {
	if ($sms_to && $sms_msg) {
		
		$unicode_query_string = '';
		if ($unicode) {
			if (function_exists('mb_convert_encoding')) {
				// $sms_msg = mb_convert_encoding($sms_msg, "UCS-2BE", "auto");
				$sms_msg = mb_convert_encoding($sms_msg, "UCS-2", "auto");
				// $sms_msg = mb_convert_encoding($sms_msg, "UTF-8", "auto");
			}
		}
		
		try {
			$sms_data = array(
				'destination' => $sms_to,
				'text' => $sms_msg 
			);
			
			$mesabot = new Playsms_Mesabot();
			$mesabot->setToken($plugin_config['mesabot']['token']);
			$mesabot->sms($sms_data);
			
			//print_r($mesabot->response());
			$response = $mesabot->response();
		}
		catch (Exception $e) {
			$response_message = $e->getMessage();
		}
		
		if ($status_code = (int) $response->status_code) {
			if ($status_code == 200) {
				_log("sent smslog_id:" . $smslog_id . " status_code:" . $status_code . " smsc:" . $smsc, 2, "mesabot_hook_sendsms");
				$ok = true;
				$p_status = 1;
				dlr($smslog_id, $uid, $p_status);
			} else {
				_log("failed smslog_id:" . $smslog_id . " status_code:" . $status_code . " smsc:" . $smsc, 2, "mesabot_hook_sendsms");
			}
		} else {
			_log("invalid smslog_id:" . $smslog_id . " response_message:[" . $response_message . "] smsc:" . $smsc, 2, "mesabot_hook_sendsms");
		}
	}
	if (!$ok) {
		$p_status = 2;
		dlr($smslog_id, $uid, $p_status);
	}
	
	return $ok;
}
