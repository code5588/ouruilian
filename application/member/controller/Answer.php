<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/11
 * Time: 13:28
 */
namespace app\member\controller;

use  app\model\questions\QuestionsModel;
use  app\model\member\MemberModel;

class Answer extends \app\member\Base{


    public function getQuestion(){

        $questionsModel = new QuestionsModel();
        //随机抽5个问题
        $questions = $questionsModel->getFrontList(5);

        return json(['code' => 0,'data'=>$questions]);

    }

    public function questionHandle(){

        $user_id = input('user_id');
        $data = json_decode(input('data'),true);

        $memberModel = new memberModel();
        if(empty($user_id) || empty($data)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }

        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }

        $questionsModel = new QuestionsModel();
        $right = 0;
        foreach ($data as $k => $v){
            $rightAnswer = $questionsModel->getRightAnswerKey($v['id']);
            if(strtoupper($v['answer']) == $rightAnswer['right_answer']){
                $right += 1;
            }
        }
        //答对一道题得10分
        $score =  $right * 10;

        try{
            $memberModel -> updateAnswerMark($user_id,$score);
        }catch (\Exception $e){
            return json(['code' => -1,'data'=>'系统繁忙!']);
        }
        $mes = '答对了'.$right.'道题,本次获得'.$score.'分';
        return json(['code' => 0,'data'=>$mes]);

    }

    public function sign(){

        $user_id = input('user_id');
        $sign_date = date('Y-m-d',time());
        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }

        $memberModel = new memberModel();
        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }

        $isSign = $memberModel -> checkSign($user_id,$sign_date);
        if(empty($isSign)){
            $sign = $memberModel -> sign($user_id,$sign_date);

            if($sign){
                return json(['code' => 0,'data' =>'签到成功!']);
            }else{
                return json(['code' => -1,'data' =>'系统繁忙']);
            }
        }else{
            return json(['code' => -2,'data' =>'今天已经签到了']);
        }

    }

    public function checkSign($user_id){

        $user_id = input('user_id');
        $sign_date = date('Y-m-d',time());

        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }
        $memberModel = new memberModel();
        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }
        $isSign = $memberModel -> checkSign($user_id,$sign_date);

        $rs = $memberModel -> userInfo($user_id);
        $data['point'] = $rs[0]['point'];
        $data['sign_num'] = $rs[0]['sign_num'];

        if($isSign){
            return json(['code' => -4,'mes' =>'今日已签到','data' =>$data]);
        }else{
            return json(['code' => 0,'mes' =>'今日未签到','data' =>$data]);
        }
    }

//    public function getSignNum(){
//        $user_id = input('user_id');
//        $memberModel = new MemberModel();
//        $isExist = $memberModel -> checkMember($user_id);
//        if(empty($isExist)){
//            return json(['code' => -3,'data'=>'用户不存在!']);
//        }
//
//        $rs = $memberModel -> userInfo($user_id);
//        $data['point'] = $rs[0]['point'];
//        $data['sign_num'] = $rs[0]['sign_num'];
//
//        return json(['code' => 0,'data' =>$data]);
//    }

}