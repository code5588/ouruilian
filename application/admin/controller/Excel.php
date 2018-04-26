<?php
namespace app\admin\controller;
use think\App;
class Excel extends \app\admin\Auth
{
	public function index(){
		return $this ->fetch('index');
	}
 	public function savestudentImport(){  
		//import('phpexcel.PHPExcel', EXTEND_PATH);//方法二  
		vendor("PHPExcel.PHPExcel"); //方法一  
		$objPHPExcel = new \PHPExcel();  
		//获取表单上传文件  
		$file = request()->file('excel');  
		$info = $file->validate(['size'=>15678000,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
			if($info){  
				$exclePath = $info->getSaveName();  //获取文件名  
				$file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址  
				$objReader =\PHPExcel_IOFactory::createReader('Excel2007');  
				$obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8  
				echo "<pre>";  
				$excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式  
				array_shift($excel_array);  //删除第一个数组(标题);  
                $data = [];
				$i=0;
				foreach($excel_array as $k=>$v) {  
					$data[$k]['title'] = $v[0];  
					$i++;  
				}
                d($data);die;
			   $success=Db::name('t_station')->insertAll($data); //批量插入数据  
			   //$i=  
			   $error=$i-$success;  
				echo "总{$i}条，成功{$success}条，失败{$error}条。";  
			   // Db::name('t_station')->insertAll($city); //批量插入数据  
			}else{  
				// 上传失败获取错误信息  
				echo $file->getError();  
			}  
  
		}  
	}
	