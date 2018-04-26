<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('PRC');

class get_wx_userinfo{
	const APPID = "wx41450b7a9c382f03";
	const APPSECRET = "187af171400b875d8677552cd12c0502";
	public $access_token,$openid;

	function __construct(){

	}

	//根据CODE获取access_token和openid
	function get_token_openid($code){
		if(!$code)return;
		$token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.self::APPID.'&secret='.self::APPSECRET.'&js_code='.$code.'&grant_type=authorization_code&scope=snsapi_base';
		$token = json_decode(file_get_contents($token_url));
       // var_dump($token);die;
		$rs=array();
		$rs['openid']= isset($token->openid)?$token->openid:"";
		//$rs['access_token'] = isset($token->access_token)?$token->access_token:"";
		return $rs;
	}
	
	//根据access_token和openid获取用户信息       snsapi_userinfo
	function get_user($access_token,$openid){
		$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$json =  curl_exec($ch);
		curl_close($ch);
		$arr=json_decode($json,1);
		//得到 用户资料
		return $arr;
	}
	
	//根据access_token和openid获取用户信息       snsapi_base
	function get_users($access_token,$openid){
		$url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$json =  curl_exec($ch);
		curl_close($ch);
		$arr=json_decode($json,1);
		//得到 用户资料
		return $arr;
	}
	
}
?>