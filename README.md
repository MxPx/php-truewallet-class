# TrueWallet Class

Unoffical TrueMoney Wallet API Class for PHP.

```php
// Login with Credentials.
$tw = new TrueWallet($username, $password);
print_r($tw->RequestLoginOTP());
print_r($tw->SubmitLoginOTP($otp_code, $mobile_number, $otp_reference));
print_r($tw->access_token); // Access Token

// Login with Access Token.
$tw = new TrueWallet($access_token);

```

```php
// Example Usage with Transaction History.
$transactions = $tw->getTransaction(50); // Fetch last 50 transactions. (within the last 30 days)
foreach ($transactions["data"]["activities"] as $report) {
	// Fetch transaction details.
	print_r($tw->GetTransactionReport($report["report_id"]));
}
```


---

## Support Me?

If you like my project, consider donating me. Thank you! <3

[ [Donate with Paypal](https://paypal.me/likecyber) or [Donate with PromptPay](https://promptpay.io/0913147533) ]

Paypal: likecyber.unlimit@gmail.com

PromptPay: 091-314-7533 (Thiranat Mahattanobon)

## Installation

It is pretty simple to utilize this class, you just need to require it.

```php
require_once("TrueWallet.class.php");
```

## Initialization

Simple initialization with Login Credentials/Access Token.

```php
$tw = new TrueWallet("[USERNAME]", "[PASSWORD]"); // Login Credentials
$tw = new TrueWallet("[ACCESS_TOKEN]"); // Access Token

```
## Functions

### [function setCredentials ($username, $password, $type = null)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L55-L61)

You can set Login Credentials with this function.

Email or Mobile Number can be used as Username. PIN can be used as Password as well.

### [function setAccessToken ($access_token)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L63-L65)

You can set Access Token with this function.

### [function RequestLoginOTP ()](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L100-L117)

You can request for Login OTP with this function, Login Credentials are required.

### [function SubmitLoginOTP ($otp_code, $mobile_number = null, $otp_reference = null)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L119-L139)

You can submit Login OTP with this function, $mobile_number and $otp_reference parameters are required.

### [function GetProfile ()](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L141-L144)

You can get your Profile information with this function.

### [function GetBalance ()](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L146-L149)

You can get your current wallet balance with this function.

### [function GetTransaction ($limit = 50, $start_date = null, $end_date = null)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L151-L159)

You can fetch your transaction(s) with this function, $start_date and $end_date parameters are needed to be "Y-m-d" format.

### [function GetTransactionReport ($report_id)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L161-L166)

You can fetch your transaction report with this function, $report_id parameter is required.

### [function TopupCashcard ($cashcard)](https://github.com/likecyber/php-truewallet-class/blob/master/TrueWallet.class.php#L168-L171)

You can topup your wallet balance with this function, $cashcard parameter is required.

---

## Useful Variables
These variables can be used if you need them.

- (string) $this->response : Plain body Response from Curl execution.
- (int) $this->http_code : HTTP Code from Curl execution.

- (string) $this->access_token : You can take Access Token from this variable.

- (array) $this->curl_options : Allow you to set extra Curl Options. (You can modify this variable.)
- (int) $this->device_id : Allow you to change device_id parameter. (You can modify this variable.)
- (int) $this->mobile_tracking : Allow you to change mobile_tracking parameter. (You can modify this variable.)

---

### Note
- CURLOPT_TIMEOUT can be set with $this->curl_options.
- CURLOPT_SSL_VERIFYPEER can be turn off with $this->curl_options.
- It is best to combine $this->http_code to check for the status of API.
- $this->request()  will automatically json_decode if possible.
- $mobile_number and $otp_reference are not required to be fill if you use RequestLoginOTP() function before.
- GetTransaction() function will fetch last 50 transactions within the last 30 days by default.

---

## Licenses

TrueWallet Class is 100% free and open-source.

<a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>.

Copyright 2018 Likecyber

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

---

Special thanks to @Noxturnix for original gen-wallet-signature.py script.

https://gist.github.com/Noxturnix/2333671ffe093386b477c2edca323cbf
