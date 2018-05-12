<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/5
 * Time: 15:26
 */
namespace app\member\controller;
use  app\model\member\MemberModel;

class Login extends \think\Controller
{

    public function index(){

        $data = 'Hello world!!!';
        return json(['code'=>0,'data'=>$data]);

    }

    public function isFirstLogin(){
        $code = input('code');
        $user_name = addslashes(input('user_name',''));
        $head_url = input('head_url');
        $share_member_id  =input('share_member_id');

        if(empty($code) || empty($head_url)){
            return json(['code'=>-1,'data'=>'缺少参数']);
        }

        $op_data = $this -> wxlogin($code);
        $openid = $op_data;
        $memberModel = new MemberModel();

        $isExist = $memberModel -> isExist($openid);
        $isFaceAip = $memberModel -> isFaceAip($openid);
        if($isFaceAip){
            $face = 1;
        }else{
            $face = 0;
        }
        if($isExist){
             $memberModel -> updateMember($user_name,$head_url,$openid);
            return json(['code'=>0,'data'=>'已存在用户','user_id'=>$isExist[0]['id'],'face'=>$face]);
        }else{
            if($share_member_id){
                $return = $memberModel -> insertMember($user_name,$head_url,$openid,$share_member_id);
            }else{
                $return = $memberModel -> insertMember($user_name,$head_url,$openid);
            }
            $id = $memberModel -> isExist($openid);
            $isFaceAip = $memberModel -> isFaceAip($openid);
            if($isFaceAip){
                $face = 1;
            }else{
                $face = 0;
            }
            return json(['code'=>1,'data'=>'首次登陆','user_id'=>$id[0]['id'],'face'=>$face]);
        }

    }

    public function wxlogin($code){

        if(empty($code)){
            return json(['code'=>-1,'data'=>'code不能为空!']);
        }
        include_once EXTEND_PATH . 'get_wx_userinfo.php';
        $get_wx_userinfo=new \get_wx_userinfo();
        $token_openid=$get_wx_userinfo->get_token_openid($code);
        $openid=isset($token_openid["openid"])?$token_openid["openid"]:"";
        if (!$openid){
            return json(['code'=>-2,'data'=>'授权码已过期，请返回重试!']);
        }
        return $openid;
    }

}