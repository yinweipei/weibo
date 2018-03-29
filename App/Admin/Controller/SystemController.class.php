<?php 
namespace Admin\Controller;
use Think\Controller;
/**
 * 系统设置控制器
 */
Class SystemController extends CommonController{
	/**
	 * 网站设置
	 */
	public function index(){
		$config = include(CONF_PATH.'system.php');
		$config = array_change_key_case($config,CASE_LOWER);
		$this->webname = $config['webname'];
		$this->copy = $config['copy'];
		$this->regis_on = $config['regis_on'];
		$this->display();
	}

	/**
	 * 修改网站设置
	 */
	public function runEdit(){
		$path = CONF_PATH.'system.php';
		$config = include $path;
		$config['WEBNAME'] = I('post.webname');
		$config['COPY'] = I('post.copy');
		$config['REGIS_ON'] = I('post.regis_on');
		
		$data = "<?php\r\nreturn " . var_export($config,true) . ";\r\n?>";
		if(file_put_contents($path, $data)){
			$this->success('修改成功',U('index'));
		}else{
			$this->display('修改失败,请修改'.$path.'的写入权限');
		}	
	}

	/**
	 * 关键设置视图
	 */
	public function filter(){
		$config = include(CONF_PATH.'system.php');
		//var_dump($config);
		$this->filter = implode('|', $config['FILTER']);
		$this->display();
	}

	/**
	 * 执行修改关键字
	 */
	public function runEditFilter(){
		$path = CONF_PATH.'system.php';
		$config = include $path;
		$config['FILTER'] = explode('|', I('post.filter'));

		$data = "<?php\r\nreturn " . var_export($config,true) . ";\r\n?>";
		if(file_put_contents($path, $data)){
			$this->success('修改成功',U('filter'));
		}else{
			$this->display('修改失败,请修改'.$path.'的写入权限');
		}
	}
}