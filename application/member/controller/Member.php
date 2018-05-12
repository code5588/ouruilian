<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/12
 * Time: 13:51
 */
namespace app\member\controller;

use app\model\member\MemberModel;

class Member extends \app\member\Base{

    public function getUserInfo(){

        $user_id = input('user_id');

        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }
        $memberModel = new MemberModel();

        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }

        $rank = $memberModel -> getRank($user_id);
        $count = $memberModel -> getMemberCount();

        $userRankBehind = $count - $rank['allRank'];
        $memberCount = $count - 1;
        $proportion = round(($userRankBehind/$memberCount)*100,2);

        $data['rank'] = $rank;
        $data['rank']['proportion'] = $proportion;
        $data['user_info'] = $memberModel -> userInfo($user_id);
        if($data['user_info'][0]['face_mark'] == 0){
            return json(['code' => -4,'data'=>'未使用过面部评分']);
        }else{
            return json(['code' => 0,'data'=>$data]);
        }

    }

    public function getAllRank(){

        $user_id = input('user_id');

        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }

        $memberModel = new MemberModel();

        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }


        //全国排名
        $allRank = $memberModel -> getAllRankList();
        $rank = $memberModel -> getRank($user_id);
        $count = $memberModel -> getMemberCount();

        $userRankBehind = $count - $rank['allRank'];
        $memberCount = $count - 1;
        $proportion = round(($userRankBehind/$memberCount)*100,2);
        return json(['code' => 0,'data'=>$allRank,'rank'=>$rank['allRank'],'proportion'=>$proportion]);
    }

    public function getFriendsRank(){

        $user_id = input('user_id');
        if(empty($user_id)){
            return json(['code' => -2,'data'=>'缺少参数']);
        }

        $memberModel = new MemberModel();

        $isExist = $memberModel -> checkMember($user_id);
        if(empty($isExist)){
            return json(['code' => -3,'data'=>'用户不存在!']);
        }

        $FriendsRank = $memberModel -> getFriendsRankList($user_id);
        $rank = $memberModel -> getRank($user_id);
        $count = $memberModel -> getMemberCount($user_id) + 1;
        $userRankBehind = $count - $rank['friendsRank'];
        $userFriends = $count - 1;
        if($userFriends > 0){
            $proportion = round(($userRankBehind/$userFriends)*100,2);
        }else{
            $proportion = 100;
        }

        return json(['code' => 0,'data'=>$FriendsRank,'rank'=>$rank['friendsRank'],'proportion'=>$proportion]);
    }
}