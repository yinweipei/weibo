<?php 
namespace Admin\Controller;
use Think\Controller;
/**注册与登录控制器**/
class LoginController extends Controller{
	/**
	 * 登录页面
	 */
	public function index(){
		// var_dump($_SESSION);
		$this->display();
	}

	/**
	 * 获取验证码
	 */
	public function verify(){
		$config = array(
	        'fontSize'  =>  14,              // 验证码字体大小(px)
	        'imageH'    =>  24,               // 验证码图片高度
	        'imageW'    =>  105,               // 验证码图片宽度
	        'length'    =>  1,               // 验证码位数
	        'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
				);
		$code= new \Think\Verify($config);
		$code->entry();
	}

	/**
	 * 登录操作处理
	 */
	public function login(){
		if(!IS_POST){
			halt('页面不存在');
		}

		// $code = I('post.verify');
		// $verify = new \Think\Verify();
		// if (!$verify->check($code)){
		// 	$this->error('验证码错误');
		// }
		$name = I('post.uname');
		$pwd = md5(I('post.pwd'));
		$db =  M('Admin');
		$user =$db->where(array('username'=>$name))->find();

		if(!$user || $user['password'] != $pwd){
			$this->error('账号或密码错误');
		}

		if($user['lock']){
			$this->error('账号被锁定');
		}

		$data = array(
			'id'=>$user['id'],
			'logintime'=>time(),
			'loginip'=>get_client_ip()
			);
		$db->data($data)->save();

		session('uid',$user['id']);
		session('username',$user['username']);
		session('logintime',date('Y-m-d H:i',$user['logintime']));
		session('now',date('Y-m-d H:i',time()));
		session('loginip',$user['loginip']);
		$this->success('正在登录...', U('Index/index'));
	}

}

