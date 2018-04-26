<?php
/**
 * Created by PhpStorm.
 * User: Xubin
 * Date: 2018/3/5
 * Time: 16:25
 */
namespace app\admin\controller;

use app\model\questions\QuestionsModel;

class Questions extends \app\admin\Auth{


    public function showList(){

        if ($this->request->isAjax()) {

            $page = $this->request->get('page/d', 1);
            $page = max(0, $page - 1);
            $limit = $this->request->get('limit/d');
            $search = $this->request->get('search');

            $questionsModel = new QuestionsModel();
            $data = $questionsModel->getList($page,$limit,$search);
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]['right_answer'] = $questionsModel->getRightAnswer($v['id']);
            }
            return json(['code' => 0, 'count' => $data['count'], 'data' => $data['list']]);
        }

        return $this->fetch('showList');
    }

    public function create(){

        if($this->request->isPost()){
            $param['question'] = $this->request->post('question');
            $param['answer_a'] = $this->request->post('answer_a');
            $param['answer_b'] = $this->request->post('answer_b');
            $param['answer_c'] = $this->request->post('answer_c');
            $param['answer_d'] = $this->request->post('answer_d');
            $param['right_answer'] = strtoupper($this->request->post('right_answer'));

            if(empty($param['question'])){
                $this->error('请输入问题!');
            }

            $arr = array('A','B','C','D');
            if(!in_array($param['right_answer'],$arr)){
                $this->error('请重新输入答案序号,如A或者B');
            }

            $questionsModel = new QuestionsModel();
            $return = $questionsModel->insertData($param);

            if($return){
                $this->success('添加成功', 'admin/questions/showList');
            }else{
                $this->error('系统繁忙，稍后再试');
            }
        }

        return $this->fetch('create');

    }

    public function edit(){

        $id = $this->request->param('id');

        if(empty($id)){
            $this->error('id不能为空');
        }
        $questionsModel = new QuestionsModel();
        $data = $questionsModel->getOne($id);

        if($this->request->isPost()){

            $param['question'] = $this->request->post('question');
            $param['answer_a'] = $this->request->post('answer_a');
            $param['answer_b'] = $this->request->post('answer_b');
            $param['answer_c'] = $this->request->post('answer_c');
            $param['answer_d'] = $this->request->post('answer_d');
            $param['right_answer'] = strtoupper($this->request->post('right_answer'));

            if(empty($param['question'])){
                $this->error('请输入问题!');
            }

            $return = $questionsModel->updateData($param,$id);

            if($return){
                $this->success('编辑成功', 'admin/questions/showList');
            }else{
                $this->error('系统繁忙，稍后再试');
            }
        }

        return $this->fetch('edit', ['data' => $data]);

    }

    public function delete(){

        $id = $this->request->param('id');

        if(empty($id)){
            $this->error('ID不存在');
        }

        $questionsModel = new QuestionsModel();

        $return = $questionsModel->deleteData($id);

        if($return){
            return json(['code'=>0,'msg'=>'操作成功']);
        }else{
            return json(['code'=>-1,'msg'=>'系统繁忙，稍后再试']);
        }

    }

}