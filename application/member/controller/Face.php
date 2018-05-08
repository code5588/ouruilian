<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/8
 * Time: 17:38
 */
namespace app\member\controller;

use app\model\member\MemberModel;

class Face extends \app\member\Base
{

    public function face(){

        include_once EXTEND_PATH . 'Face/AipFace.php';
        $file = $_FILES['image'];
        $user_id = input('user_id');
        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }
        $memberModel = new MemberModel();
        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }

        if(empty($file)){
            return json(['code'=>-1,'data'=>'图片不能为空']);
        }
        $image = file_get_contents($file['tmp_name']);
        $aipConf = config('aipface');
        $options = array();
        $options["max_face_num"] = 1;
        $options["face_fields"] = "age,beauty,expression,gender,glasses,race,qualities";
        $client = new \AipFace($aipConf['app_id'], $aipConf['app_key'], $aipConf['secret_key']);
        $readFace = $client -> detect($image,$options);
        if($readFace['result_num'] == 0){
            return json(['code'=>-4,'data'=>'没有检测到面部']);
        }

        $data['face_q'] = round((1/$readFace['result'][0]['age'])*1000) + round($readFace['result'][0]['beauty'])  +
                          round($readFace['result'][0]['expression_probablity']*50);

        if($readFace['result'][0]['gender'] == 'female'){
            $gender = 10;
        }elseif ($readFace['result'][0]['gender'] == 'male'){
            $gender = 5;
        }

        if($readFace['result'][0]['glasses'] == '0'){
            $glasses = 5;
        }elseif ($readFace['result'][0]['glasses'] == '1'){
            $glasses = 3;
        }elseif ($readFace['result'][0]['glasses'] == '2'){
            $glasses = 1;
        }

        $data['face_gloss'] = round((1/$readFace['result'][0]['age'])*1000) + round($readFace['result'][0]['beauty'])  +
                              $gender + round($readFace['result'][0]['gender_probability']*10) + $glasses + round($readFace['result'][0]['glasses_probability']*10);

        $data['face_water'] = round((1/$readFace['result'][0]['age'])*1000) + round($readFace['result'][0]['beauty'])  +
                              round($readFace['result'][0]['qualities']['blur']*10) + round($readFace['result'][0]['qualities']['illumination']/10) + round($readFace['result'][0]['qualities']['completeness']*10) +
                              round($readFace['result'][0]['qualities']['type']['human']*10);

        $key = rand(1,3);
        if($key == 1){
            $data['face_q'] += 30;
        }elseif ($key == 2){
            $data['face_gloss'] += 30;
        }elseif ($key == 3){
            $data['face_water'] += 30;
        }
        //存七牛
        $return = $this -> uploadPic($file);
        if($return['code'] != 0){
            return json(['code' => $return['code'],'message'=>$return['data']]);
        }else{

            $memberModel -> edit($user_id,$return['data'],$readFace['result'][0]['beauty'],$data['face_q'],$data['face_gloss'],$data['face_water']);
        }

        return json(['code' => 0,'data'=>'评测成功!']);
    }


    /**
     * 上传图片
     * @return array
     * @throws \Exception
     */
    private function uploadPic($file) {

            try {
                if (empty($file)) {
                    return json(['code' => -2,'data'=>'上传文件不能为空']);
                } else {
                    //上传图片七牛云
                    include_once EXTEND_PATH . 'qiniu.php';
                    $qiniu = new \qiniu();
                    //空间
                    $bucket = 'member';
                    //图片域名
                    $domain = Config('qiniu')['member']['domain'];
                    $reslut = $qiniu->upload_image($file, $bucket);
                    if ($reslut['code'] != 0) {
                        return ['code' => $reslut['code'],'data'=>$reslut['message']];
                    }else{
                        return ['code' => $reslut['code'],'data'=>$domain.$reslut['data']];
                    }
                }
            } catch (\think\Exception $e) {
                    return false;
            }

    }


}