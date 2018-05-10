<?php

function d($arr){
    echo '<pre>';
    var_dump($arr);
}

function getConf(){

    $conf = \think\Db::table('conf')->select();
    return $conf;

}
