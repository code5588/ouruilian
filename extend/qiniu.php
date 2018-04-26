<?php
/**
* Class qiniu
*/

class qiniu
{

    //上传图片
    public function upload_image($file,$bucket)
    {

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);  //后缀
        $arr = array('jpg','png','jpeg');
        if(!in_array($ext,$arr)){
            return ["code"=>-2,"message"=>"图片格式不正确"];
        }
        $filePath = $file['tmp_name'];
        // 上传到七牛后保存的文件名
        $key =substr(md5($file['tmp_name']) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
        require_once  EXTEND_PATH. '/Qiniu/functions.php';

        $accessKey = Config('qiniu')['accessKey'];
        $secretKey = Config('qiniu')['secretKey'];
        // 构建鉴权对象
        $auth = new \Qiniu\Auth($accessKey, $secretKey);

        // 要上传的空间
        $token = $auth->uploadToken($bucket);

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new \Qiniu\Storage\UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return ["code"=>1,"message"=>$err,"data"=>""];
        } else {
            return ["code"=>0,"message"=>"上传完成","data"=> $ret['key']];
        }
    }

}
?>