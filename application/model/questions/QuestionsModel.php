<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/5
 * Time: 16:32
 */

namespace app\model\questions;


class QuestionsModel extends \app\model\ApiModel{

    public function getList($page = 0, $pageSize = 10, $title = ''){

            $order = ['id ASC'];
            $where = ['1 = 1'];
            if ($title) {
                $where[] = "question like '%{$title}%'";
            }

            $sql = "SELECT * FROM `questions` ";

            $sql_count = "SELECT count(*) as count FROM `questions`";

            $return = $this->_getList($sql, $sql_count, $where, $order, true, $page, $pageSize);

            return $return;
    }

    public function getOne($id){

        $sql = "SELECT * FROM `questions` where id = ".$id;

        $data = $this->find($sql);

        return $data;
    }

    public  function getRightAnswer($id){

        $sql = "SELECT * FROM `questions` where id = ".$id;

        $data = $this->find($sql);
        switch ($data['right_answer']){
            case 'A':
                $rightAnswer = $data['answer_a'];
                break;
            case 'B':
                $rightAnswer = $data['answer_b'];
                break;
            case 'C':
                $rightAnswer = $data['answer_c'];
                break;
            case 'D':
                $rightAnswer = $data['answer_d'];
                break;
        }

        return $rightAnswer;
    }

    public function updateData($data,$id){

        $return = \think\Db::table('questions')->where(['id'=>$id])->update($data);

        return $return;
    }

    public function insertData($data){

        $return = \think\Db::table('questions')->insertGetId($data);
        return $return;
    }

    public  function deleteData($id){

        $return = \think\Db::table('questions')->delete($id);
        return $return;
    }

    public function getFrontList($number){

        $sql = "SELECT id,question,answer_a,answer_b,answer_c,answer_d,right_answer FROM `questions` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `questions`))) ORDER BY id LIMIT ".$number;

        $return = \think\Db::query($sql);

        foreach ($return as $k => $v){
            $data[$k]['id'] = $v['id'];
            $data[$k]['question'] = $v['question'];
            $data[$k]['answer'][] = $v['answer_a'];
            $data[$k]['answer'][] = $v['answer_b'];
            $data[$k]['answer'][] = $v['answer_c'];
            $data[$k]['answer'][] = $v['answer_d'];
            if($v['right_answer'] == 'A'){
                $data[$k]['right_answer'][] = 0;
            }elseif ($v['right_answer'] == 'B'){
                $data[$k]['right_answer'][] = 1;
            }elseif ($v['right_answer'] == 'C'){
                $data[$k]['right_answer'][] = 2;
            }elseif ($v['right_answer'] == 'D'){
                $data[$k]['right_answer'][] = 3;
            }

        }
        return $data;
    }

    public  function getRightAnswerKey($id){

        $sql = "SELECT right_answer FROM `questions` where id = ".$id;

        $data = $this->find($sql);

        return $data;
    }
}
