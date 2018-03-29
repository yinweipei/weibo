<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends  CommonController {

	/**
     *后台首页 
     */
    public function index(){
        //var_dump($_SESSION);
        $this->display();
    }

    /**
     * 后台消息页
     */
    public function copy(){
        $db = M('User');
        $this->user = $db->count();
        $this->lock = $db->where(array('lock'=>1))->count();

        $db = M('Weibo');
        $this->weibo = $db->where(array('isturn'=>0))->count();
        $this->turn = $db->where(array('isturn'=>array('GT',0)))->count();
        $this->comment = M('comment')->count();

        $this->display();
    }

    /**
     * 退出登录
     */
    public function loginOut(){
        session_unset();
        session_destroy();
        redirect(U('Login/index'));
    }

}