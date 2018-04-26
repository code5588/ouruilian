<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/6
 * Time: 20:20
 */
namespace app\model\member;

use app\model\ApiModel;

class MemberModel extends ApiModel{

    public function getList($page = 0, $pageSize = 10, $user_name = ''){

        $order = ['id ASC'];
        $where = ['status = 1'];
        if ($user_name) {
            $where[] = "member like '%{$user_name}%'";
        }

        $sql = "SELECT *, sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point FROM `member` ";

        $sql_count = "SELECT count(*) as count FROM `member`";

        $return = $this->_getList($sql, $sql_count, $where, $order, true, $page, $pageSize);

        return $return;
    }

    public function getFriendsList($id, $page = 0, $pageSize = 10){

        $order = ['id ASC'];
        $where = ['status = 1','share_member_id = '.$id];
        $sql = "SELECT *, sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point FROM `member` ";

        $sql_count = "SELECT count(*) as count FROM `member`";

        $return = $this->_getList($sql, $sql_count, $where, $order, true, $page, $pageSize);

        return $return;
    }

    /**
     * @return boolean
     */
    public function updateAnswerMark($id,$score)
    {
        $sql = 'update `member` set answer_mark = answer_mark + '.$score.' where id = '.$id;
        $return = \think\Db::query($sql);
        return $return;
    }

    public function checkMember($id){

        $isExist =  \think\Db::table('member')->field('id') -> where('id = '.$id) -> find();
        return $isExist;
    }

    public function isExist($openid){
        $sql = "select id from `member` WHERE openid = '".$openid."'";
        $id = \think\Db::query($sql);
        return $id;
    }
    public function isFaceAip($openid){
        $sql = "select id from `member` WHERE openid = '".$openid."' and face_mark > 0 ";
        $id = \think\Db::query($sql);
        return $id;
    }
    public function sign($user_id,$sign_date){

        \think\Db::startTrans();
        try{
            $sql = "delete from `sign` where user_id = ".$user_id;
            $return = \think\Db::execute($sql);

            $sql = "insert into `sign` (user_id,sign_date)VALUES($user_id,'$sign_date')";
            $return = \think\Db::execute($sql);

            $sql = "update `member` set sign_mark = sign_mark + 5,sign_num = sign_num + 1 where id = ".$user_id;
            $return = \think\Db::execute($sql);

            \think\Db::commit();
        }catch (\Exception $e){

            \think\Db::rollback();
            return $return = '';
        }

        return $return;
    }

    public function checkSign($user_id,$sign_date){

        $sql = "select * from `sign` where user_id = $user_id and sign_date = '$sign_date'";
        $sign_mes = \think\Db::query($sql);

        return $sign_mes;
    }

    public function userInfo($user_id){

        $sql = "select id, user_name, head_url, sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point ,photo_url,sign_mark,face_mark,answer_mark,face_q,face_gloss,face_water,sign_num,share_member_id from `member` where id = ".$user_id;
        $user_info = \think\Db::query($sql);
        return $user_info;
    }

    public function getRank($user_id){

        $rank_sql = "select id,share_member_id,sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point from `member` order by sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water desc limit 100";
        $member = \think\Db::query($rank_sql);
        $friends_member = [];
        foreach ($member as $k => $v){
            if($v['id'] == $user_id){
                $allRank = $k +1;
            }
            if($v['share_member_id'] == $user_id){
                $friends_member[] = $v;
            }
        }

        $user_info = $this -> userInfo($user_id);
        $user['id'] = $user_info[0]['id'];
        $user['point'] = $user_info[0]['point'];
        $user['head_url'] = $user_info[0]['head_url'];
        //var_dump($friends_member);die;

        if($user_info[0]['share_member_id']){

            array_push($friends_member,$user);

            $share_user_info =  $this -> userInfo($user_info[0]['share_member_id']);
            $share_user['user_name'] = $share_user_info[0]['user_name'];
            $share_user['id'] = $share_user_info[0]['id'];
            $share_user['point'] = $share_user_info[0]['point'];
            $share_user['head_url'] = $share_user_info[0]['head_url'];
            array_push($friends_member,$share_user);

            //按照键值point排序
            foreach ($friends_member as $k => $v){
                $tmp[$k] = $v['point'];
            }
            array_multisort($tmp,SORT_DESC,$friends_member);
            foreach ($friends_member as $k => $v){
                if($v['id'] == $user_id){
                    $friendsRank = $k +1;
                }
            }
            $data['friendsRank'] = $friendsRank;
        }else{
            $data['friendsRank'] = 0;
        }


        $data['allRank'] = $allRank;


        return $data;
    }

    public function getAllRankList(){

        $rank_sql = "select user_name,sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point,head_url from `member` order by sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water desc limit 100";
        $member = \think\Db::query($rank_sql);
        foreach ($member as $k => $v){
            $member[$k]['rank'] = $k+1;
        }

        return $member;
    }

    public function getFriendsRankList($user_id){

        $rank_sql = "select user_name,sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water as point,share_member_id,head_url from `member` where share_member_id = ".$user_id." order by sign_mark+face_mark+answer_mark+face_q+face_gloss+face_water desc limit 98";
        $member = \think\Db::query($rank_sql);

        $user_info = $this -> userInfo($user_id);
        $user['user_name'] = $user_info[0]['user_name'];
        $user['point'] = $user_info[0]['point'];
        $user['head_url'] = $user_info[0]['head_url'];
        array_push($member,$user);

        if($user_info[0]['share_member_id']){
            $share_user_info =  $this -> userInfo($user_info[0]['share_member_id']);
            $share_user['user_name'] = $share_user_info[0]['user_name'];
            $share_user['point'] = $share_user_info[0]['point'];
            $share_user['head_url'] = $share_user_info[0]['head_url'];
            array_push($member,$share_user);
        }

        foreach ($member as $k => $v){
            $tmp[$k] = $v['point'];
        }
        array_multisort($tmp,SORT_DESC,$member);
        foreach ($member as $k => $v){
            $member[$k]['rank'] = $k +1;
        }
        return $member;
    }

    public function getMemberCount($user_id = ''){

        if($user_id){
            $sql = "select count(*) as count from `member` where share_member_id = ".$user_id;
            $count = \think\Db::query($sql);
        }else{
            $sql = "select count(*) as count from `member` ";
            $count = \think\Db::query($sql);
        }

        return $count[0]['count'];
    }

    public function edit($user_id,$photo_url,$face_mark,$face_q,$face_gloss,$face_water){

        $sql = "update `member` set photo_url = '".$photo_url."' , face_mark =  ".$face_mark." , face_q = ".$face_q." , face_gloss = ".$face_gloss." ,face_water = ".$face_water." where id = ".$user_id;
        $return = \think\Db::execute($sql);

        return $return;
    }

    public function insertMember($user_name,$head_url,$openid,$share_member_id = ''){

        $time = date('Y-m-d H:i:s',time());
        if($share_member_id){
            $sql = "insert into `member` (user_name,openid,head_url,share_member_id,add_time) VALUES ('$user_name','$openid','$head_url',$share_member_id,'$time')";
        }else{
            $sql = "insert into `member` (user_name,openid,head_url,add_time) VALUES ('$user_name','$openid','$head_url','$time')";
        }
        $return = \think\Db::execute($sql);

        return $return;
    }
    public function updateMember($user_name,$head_url,$openid){

        $sql = "update `member` set user_name = '$user_name', head_url = '$head_url' where openid = '$openid'";
        $return = \think\Db::execute($sql);

        return $return;
    }
}