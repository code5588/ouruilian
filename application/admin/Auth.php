<?php
namespace app\admin;
use think\App;

class Auth extends \think\Controller
{

    const power = [
        1   =>  [
            'title' => '会员列表',
            'src'   => 'member/list'
        ],
        2   =>  [
            'title' => '会员好友列表',
            'src'   => 'member/friendslist'
        ],
        4   => [
            'title' => '题库列表',
            'src'   => 'questions/showlist'
        ],
        8   => [
            'title' => '添加题库',
            'src'   => 'questions/create'
        ],
        /*16  => [
            'title' => '',
            'src'   => ''
        ],
        32  => [
            'title' => '',
            'src'   => ''
        ],
        64  => [
            'title' => '',
            'src'   => ''
        ],
        128 => [
            'title' => '',
            'src'   => ''
        ],
        256 => [
            'title' => '',
            'src'   => ''
        ],
        512 => [
            'title' => '',
            'src'   => ''
        ],
        1024 => [
            'title' => '',
            'src'   => ''
        ],
        2048 => [
            'title' => '',
            'src'   => ''
        ],
        4096 =>[
            'title' => '',
            'src'   => ''
        ],
        8192 => [
            'title' => '',
            'src'   => ''
        ],
        16384 => [
            'title' => '',
            'src'   => ''
        ],
        32768 =>[
            'title' => '',
            'src'   => ''
        ],
        65536 =>[
            'title' => '',
            'src'   => ''
        ],
        131072 =>[
            'title' => '',
            'src'   => ''
        ],
        262144 =>[
            'title' => '',
            'src'   => ''
        ],
        524288 =>[
            'title' => '',
            'src'   => ''
        ],*/
    ];

    public function _initialize()
    {
        $controller = \think\Request::instance()->controller();
        $action     = \think\Request::instance()->action();
        if(session('isAdmin') != 1){
            $this->redirect(url('site/login'));
        }

        $pass = \think\Db::table('admin')->where(['id'=>session('id')])->value('permission');

        if(session('isSuper')=='0'){
            if(empty($pass)){
                $this->error('您没有权限');
                exit;
            }
            foreach(self::power as $k=>$v){
                if($v['src'] == strtolower($controller.'/'.$action)){
                    if(($pass&$k) == 0){
                        $this->error('您没有权限!','admin/index/main');
                        exit;
                    }
                }
            }
        }
    }
}
