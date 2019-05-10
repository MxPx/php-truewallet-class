<?php

/**
* TrueWallet Class
 *
 * @category  Payment Gateway
 * @package   php-truewallet-api
 * @author    Likecyber <cyber2friends@gmail.com>
 * @copyright Copyright (c) 2018-2019
 * @license   https://creativecommons.org/licenses/by/4.0/ Attribution 4.0 International (CC BY 4.0)
 * @link      https://github.com/likecyber/php-truewallet-api
 * @version   1.0.0
**/

class TrueWallet {
	public $credentials = array();
	public $access_token = null;

	public $curl_options = null;

	public $mobile_number = null;
	public $otp_reference = null;

	public $response = null;
	public $http_code = null;

	private $mobile_api_gateway = "https://mobile-api-gateway.truemoney.com/mobile-api-gateway/";
	private $secret_key = "9LXAVCxcITaABNK48pAVgc4muuTNJ4enIKS5YzKyGZ";

	private $device_id = ""; // Set device_id here
	private $mobile_tracking = ""; // Set mobile_tracking here

	public function generate_identity () {
		$this->mobile_tracking = base64_encode(openssl_random_pseudo_bytes(40));
		$this->device_id = substr(md5($this->mobile_tracking), 16);
		return implode(array($this->device_id, $this->mobile_tracking), "|");
	}

	public function __construct ($username = null, $password = null) {
		if (empty($this->device_id) || empty($this->mobile_tracking)) {
			$identity_file = dirname(__FILE__)."/".basename(__FILE__, ".php").".identity";
			if (file_exists($identity_file)) {
				list($this->device_id, $this->mobile_tracking) = explode("|", file_get_contents($identity_file));
			} else {
				file_put_contents($identity_file, $this->generate_identity());
			}
		}
		if (!is_null($username) && !is_null($password)) {
			$this->setCredentials($username, $password);
		} elseif (!is_null($username)) {
			$this->setAccessToken($username);
		}
	}

	public function setCredentials ($username, $password, $type = null) {
		if (is_null($type)) $type = filter_var($username, FILTER_VALIDATE_EMAIL) ? "email" : "mobile";
		$this->credentials["username"] = $username;
		$this->credentials["password"] = $password;
		$this->credentials["type"] = $type;
		$this->access_token = null;
	}

	public function setAccessToken ($access_token) {
		$this->access_token = $access_token;
	}

	public function request ($api_path, $headers = array(), $data = null) {
		$handle = curl_init($this->mobile_api_gateway.ltrim($api_path, "/"));
		if (!is_null($data)) {
			curl_setopt_array($handle, array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => is_array($data) ? json_encode($data) : $data
			));
			if (is_array($data)) $headers = array_merge(array("Content-Type" => "application/json"), $headers);
		}
		curl_setopt_array($handle, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => "okhttp/3.8.0",
			CURLOPT_HTTPHEADER => $this->buildHeaders($headers)
		));
		if (is_array($this->curl_options)) curl_setopt_array($handle, $this->curl_options);
		$this->response = curl_exec($handle);
		$this->http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($result = json_decode($this->response, true)) return $result;
		return $this->response;
	}

	public function buildHeaders ($array) {
		$headers = array();
		foreach ($array as $key => $value) {
			$headers[] = $key.": ".$value;
		}
		return $headers;
	}

	public function getTimestamp() {
		return strval(floor(microtime(true)*1000));
	}

	public function RequestLoginOTP () {
		if (!isset($this->credentials["username"]) || !isset($this->credentials["username"]) || !isset($this->credentials["type"])) return false;
		$timestamp = $this->getTimestamp();
		$result = $this->request("/api/v1/login/otp/", array(
			"username" => $this->credentials["username"],
			"password" => sha1($this->credentials["username"].$this->credentials["password"])
		), array(
			"type" => $this->credentials["type"],
			"device_id" => $this->device_id,
			"timestamp" => $timestamp,
			"signature" => hash_hmac("sha1", implode(array($this->credentials["type"], $this->device_id, $timestamp), "|"), $this->secret_key)
		));
		if (isset($result["data"]) && !is_null($result["data"])) {
			$this->mobile_number = $result["data"]["mobile_number"];
			$this->otp_reference = $result["data"]["otp_reference"];
		}
		return $result;
	}

	public function SubmitLoginOTP ($otp_code, $mobile_number = null, $otp_reference = null) {
		if (is_null($mobile_number) && !is_null($this->mobile_number)) $mobile_number = $this->mobile_number;
		if (is_null($otp_reference) && !is_null($this->otp_reference)) $otp_reference = $this->otp_reference;
		if (is_null($mobile_number) || is_null($otp_reference)) return false;
		$timestamp = $this->getTimestamp();
		$result = $this->request("/api/v1/login/otp/verification/", array(
			"username" => $this->credentials["username"],
			"password" => sha1($this->credentials["username"].$this->credentials["password"])
		), array(
			"type" => $this->credentials["type"],
			"otp_code" => $otp_code,
			"mobile_number" => $mobile_number,
			"otp_reference" => $otp_reference,
			"device_id" => $this->device_id,
			"mobile_tracking" => $this->mobile_tracking,
			"timestamp" => $timestamp,
			"signature" => hash_hmac("sha1", implode(array($this->credentials["type"], $otp_code, $mobile_number, $otp_reference, $this->device_id, $this->mobile_tracking, $timestamp), "|"), $this->secret_key)
		));
		if (isset($result["data"]["access_token"])) $this->setAccessToken($result["data"]["access_token"]);
		return $result;
	}

	public function GetProfile () {
		if (is_null($this->access_token)) return false;
		return $this->request("/api/v1/profile/".$this->access_token);
	}

	public function GetBalance () {
		if (is_null($this->access_token)) return false;
		return $this->request("/api/v1/profile/balance/".$this->access_token);
	}

	public function GetTransaction ($limit = 50, $start_date = null, $end_date = null) {
		if (is_null($this->access_token)) return false;
		if (is_null($start_date) && is_null($end_date)) $start_date = date("Y-m-d", strtotime("-30 days"));
		if (is_null($end_date)) $end_date = date("Y-m-d", strtotime("+1 day"));
		if (is_null($start_date) || is_null($end_date)) return false;
		return $this->request("/user-profile-composite/v1/users/transactions/history?start_date=".$start_date."&end_date=".$end_date."&limit=".$limit, array(
			 "Authorization" => $this->access_token
		));
	}

	public function GetTransactionReport ($report_id) {
		if (is_null($this->access_token)) return false;
		return $this->request("/user-profile-composite/v1/users/transactions/history/detail/".$report_id, array(
			 "Authorization" => $this->access_token
		));
	}

	public function TopupCashcard ($cashcard) {
		if (is_null($this->access_token)) return false;
		return $this->request("/api/v1/topup/mobile/".time()."/".$this->access_token."/cashcard/".$cashcard, array(), "");
	}
}

?>
