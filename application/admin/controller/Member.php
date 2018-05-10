<?php

namespace app\admin\controller;

use app\model\member\MemberModel;

class Member extends \app\admin\Auth
{

    public function showList(){

        if ($this->request->isAjax()) {

            $page = $this->request->get('page/d', 1);
            $page = max(0, $page - 1);
            $limit = $this->request->get('limit/d');
            $keywords = $this->request->get('search');
            if($keywords){
                $search = str_replace('\\','_',$this -> unicode_encode($keywords));
            }else{
                $search = '';
            }
            $memberModel = new MemberModel();
            $data = $memberModel->getList($page,$limit,$search);
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]["user_name"] = $this -> unicode2utf8($v['user_name']);
            }
            return json(['code' => 0, 'count' => $data['count'], 'data' => $data['list']]);
        }

        return $this->fetch('showList');
    }

    public function showFriends(){

        $id = $this->request->get('id');

        if ($this->request->isAjax()) {

            $page = $this->request->get('page/d', 1);
            $page = max(0, $page - 1);
            $limit = $this->request->get('limit/d');
            $id = $this->request->param('id');

            $memberModel = new MemberModel();
            $data = $memberModel->getFriendsList($id,$page,$limit);

            return json(['code' => 0, 'count' => $data['count'], 'data' => $data['list']]);
        }

        return $this->fetch('showFriends',['id'=>$id]);
    }


    public function unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0)
            {   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }

    public function unicode2utf8($str){
        if(!$str){
            return $str;
        }
        $decode = json_decode($str);
        if($decode){
            return $decode;
        }
        $str = '["' . $str . '"]';
        $decode = json_decode($str);
        if(count($decode) == 1){
            return $decode[0];
        }
            return $str;
        }

}
