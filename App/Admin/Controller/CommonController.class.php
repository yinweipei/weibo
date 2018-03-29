<?php 
namespace Admin\Controller;
use Think\Controller;
/**
 * 公共控制器
 */
class CommonController extends Controller{
	/**
	*判断用户是否已登录 
	*/	
	public function _initialize(){
		if(!isset($_SESSION['uid']) || !isset($_SESSION['username'])){
			redirect(U('Login/index'));
		}
	}
}

