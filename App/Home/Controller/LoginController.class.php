<?php 
namespace Home\Controller;
use Think\Controller;
/**
 * 注册与登录控制器
 */
class LoginController extends Controller{
	/**
	 * 登录页面
	 */
	public function index(){
		$this->display();
	}

	/**
	 * 登录表单处理
	 */
	public function login(){
		if(!IS_POST){
			halt('页面不存在');
		}
		//提取表单内容
		$account = I('post.account');
		$pwd = md5(I('post.pwd'));
		$where = array('account'=>$account);
		$user = M('User')->where($where)->find();

		if(!$user||$user['password']!=$pwd){
			$this->error('用户名或者密码不正确');
		}

		if($user['lock']){
			$this->error('用户被锁定');
		}

		if(isset($_POST['auto'])){
			$account = $user['account'];
			$ip = get_client_ip();
			$value = $account.'|'.$ip;
			$value = encryption($value); 
			cookie('auto',$value,C('AUTO_LOGIN_TIME'),'/'); 
		}

		//登录成功写入SESSION并且跳转到首页
		session('uid',$user['id']);
		header('Content-Type:text/html;Charset=UTF-8');
		redirect(__APP__, 3, '登录成功,正在为您跳转...');
		
	}

	/**
	 * 注册页面
	 */
	public function register(){
		if(!C('REGIS_ON')){
			$this->error('网站暂停使用');
		}
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
	 * 异步验证验证码
	 */
	public function runRegis(){
		if(!IS_POST){
			halt('页面不存在');
		}
	    //验证码
		$code = I('post.verify');
		if (!$code) {
			$this->error('验证码不能为空');
		} 
		//验证两次密码
		if(I('post.pwd') != I('post.pwded')){
			$this->error('两次密码不一致');
		}
		//提取POST数据
		$data = array(
			'account' => I('post.account'),
			'password' => md5(I('post.pwd')),
			'registime' => $_SERVER[REQUEST_TIME],
			// 'userinfo' => array(
			// 	'username' => I('post.uname')
			// 	)
			);
		//$id = D('UserRelation')->insert($data);
		$id = M('User')->add($data);
		$arr['uid'] = $id;
		$arr['username'] = I('post.uname');
		$res = M('Userinfo')->add($arr);
		if($res){
			session('uid',$id);
			header('Content-Type:text/html;Charset=UTF-8');
			redirect(__APP__, 3, '注册成功,正在跳转中...');
		}else{
			$this->error('注册失败，请重试');
		}
	}
	/**
	 * 异步验证账号是否已存在
	 */
	public function checkAccount() {
		if (!IS_AJAX) {
			halt('页面不存在');
		}
		$account = I('post.account');
		$where = array('account' => $account);
		if (M('User')->where($where)->getField('id')) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 异步验证昵称是否已存在
	 */
	Public function checkUname () {
		if (!IS_AJAX) {
			halt('页面不存在');
		}
		$username = I('post.uname');
		$where = array('username' => $username);
		if (M('Userinfo')->where($where)->getField('id')) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 异步验证验证码
	 */
	Public function checkVerify () {
		if (!IS_AJAX) {
			halt('页面不存在');
		}
		$verify = new \Think\Verify();
		$code = I('post.verify');
		if ($verify->check($code)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
}

