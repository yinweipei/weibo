<?php 
namespace Home\Controller;
use Think\Controller;

class UserSettingController extends Controller{
	//用户基本信息设置视图
	public function index(){
		$where = array('uid' => session('uid'));
		$field = array('username','truename','sex','location','constellation','intro');
		$user = M('Userinfo')->field($field)->where($where)->find();
		$this->user = $user;
		//p($this->user);
		$this->display();
	}

	//修改用户基本信息
	public function editBasic(){
		if(!IS_POST){
			halt('页面不存在');
		}
		header('Content-Type:text/html;Charset=UTF-8');
		$data = array(
			'username'=>I('post.nickname'),
			'truename'=>I('post.truename'),
			'sex' => (int)I('post.sex'),
			'location'=> I('post.province').' '.I('post.city'),
			'constellation'=>I('post.night'),
			'intro'=>I('post.intro')
			);
		$where = array('uid' => session('uid'));
		if(M('Userinfo')->where($where)->save($data)){
			$this->success('修改成功',U('index'));
		}else{
			$this->error('修改失败');
		}
	}

	//修改用户头像
	public function editFace(){
		if(!IS_POST){
			halt('页面不存在');
		}
		$db = M('Userinfo');
		$where = array('uid'=>session('uid'));
		$field = array('face50','face80','face180');
		$old = $db->where($where)->field($field)->find();
		if($db->where($where)->save($_POST)){
			if(!empty($old['face180'])){
				@unlink('./Uploads/Face/'.$old['face180']);
				@unlink('./Uploads/Face/'.$old['face80']);
				@unlink('./Uploads/Face/'.$old['face50']);
			}
			$this->success('修改成功');
		}
	}

	//修改用户密码
	public function editPwd(){
		if(!IS_POST){
			halt('页面不存在');
		}
		$db = M('User');
		//验证旧密码
		$where = array('id'=>session('uid'));
		$old = $db->where($where)->getField('password');
		if( md5(I('post.old')) != $old){
			$this->error('旧密码错误');
		}
		if(I('post.new') !=I('post.newed')){
			$this->error('两次密码不一致');
		}

		$newPwd = md5(I('post.new'));
		$data = array(
			'id' => session('uid'),
			'password'=>$newPwd
			);
		if($db->save($data)){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}
	}

}

 