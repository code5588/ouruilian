<?php
namespace app\admin\controller;
use think\App;

class Index extends \app\admin\Auth
{
	const power = [
        1   =>  [
            'title' => '会员列表',
            'src'   => 'member/showlist'
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
		16  => [
			'title' => '管理员列表',
			'src'   => 'admin/showlist'
		]
	];
	public function index()
	{
		$pass = \think\Db::table('admin')->where(['id'=>session('id')])->value('permission');
		$auth = [];
		if(session('isSuper') != 1){
			foreach(self::power as $k=>$v){
				if(($pass&$k) != 0){
					$auth[] = $v['src'];
				}
			}
		}else{
			foreach(self::power as $k=>$v){
				$auth[] = $v['src'];
			}
		}

		$this->assign('auth',$auth);
		return view();
	}


	public function main(){

		echo '欢迎使用';
	}



}
