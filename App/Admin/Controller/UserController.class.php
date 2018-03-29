<?php 
namespace Admin\Controller;
use Think\Controller;
/**
 * 用户控制器
 */
Class UserController extends CommonController{
	/**
	 * 微博用户列表
	 */
	public function index(){
		//统计数据总条数，用于分页
        $count = M('User')->count();
        $page = new \Think\Page($count,5);
        $page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow.','.$page->listRows;

		$users = D('UserView')->limit($limit)->select();
		$this->users = $users;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 锁定用户
	 */
	public function lockUser(){
		$data = array(
			'id'=>I('get.id'),
			'lock'=>I('get.lock')
			);
		$msg = $data['lock'] ? '锁定' : '解锁';
		if(M('User')->save($data)){
			$this->success($msg.'成功',$_SERVER['HTTP_REFERER']);
		}else{
			$this->error('失败，请重试。。。');	
		}
	}

	/**
	 * 用户检索
	 */
	public function sechUser(){
		if(isset($_GET['sech']) && isset($_GET['type'])){
			$where = $_GET['type'] ? array('id'=>I('get.sech')) : array('username'=>array('like','%'.I('get.sech').'%'));
			$user = D('UserView')->where($where)->select();
			$this->user = $user ? $user : false;
		}
		$this->display();
	}

	/**
	 * 后台管理员列表
	 */
	public function admin(){
		$this->admin = M('Admin')->select();
		$this->display();
	}

	/**
	 * 添加后台管理员
	 */
	public function addAdmin(){
		$this->display();
	}

	/**
	 * 执行添加管理员操作
	 */
	public function runAddAdmin(){
		if($_POST['pwd'] != $_POST['pwded']){
			$this->error('两次密码不一致');
		}
		$data = array(
			'username'=>I('post.username'),
			'password'=>md5(I('post.pwd')),
			'logintime'=>time(),
			'loginip'=>get_client_ip(),
			'admin'=>I('post.admin')
			);

		if(M('Admin')->data($data)->add()){
			$this->success('添加成功',U('admin'));
		}else{
			$this->error('失败，请重试。。。');	
		}
	}

	/**
	 * 锁定后台管理员
	 */
	public function lockAdmin(){
		$data = array(
			'id'=>I('get.id'),
			'lock'=>I('get.lock'),
			);
		$msg = $data['lock'] ? '锁定' : '解锁';
		if(M('Admin')->save($data)){
			$this->success($msg.'成功',U('admin'));
		}else{
			$this->error($msg.'失败，请重试。。。');	
		}
	}

	/**
	 * 删除后台管理员
	 */
	public function delAdmin(){
		$id = I('get.id');

		if(M('Admin')->delete($id)){
			$this->success('删除成功',U('admin'));
		}else{
			$this->success('删除失败,请重试。。。');
		}
	}

	/**
	 * 修改密码视图
	 */
	public function editPwd(){
		$this->display();
	}

	/**
	 * 修改密码操作
	 */
	public function runEditPwd(){
		$db = M('Admin');
		$old = $db->where(array('id'=>session('uid')))->getField('password');

		if($old != md5($_POST['old'])){
			$this->error('旧密码错误');
		}

		if($_POST['pwd'] != $_POST['pwded']){
			$this->error('两次密码不一致');
		}

		$data = array(
			'id'=>session('uid'),
			'password'=>md5(I('post.pwd'))
			);
		if($db->save($data)){
			$this->success('修改成功',U('Index/copy'));
		}else{
			$this->error('修改失败');
		}
	}


}