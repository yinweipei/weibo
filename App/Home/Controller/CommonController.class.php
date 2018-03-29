<?php 
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller{
	public function _initialize(){
		//处理自动登录
		if(isset($_COOKIE['auto']) && !isset($_SESSION['uid'])){
			$auto = explode('|',encryption($_COOKIE['auto'],1));
			$ip = get_client_ip();
			//本次登录IP与上一次登录IP一致时
			if($ip == $auto[1]){
				$account = $auto[0];
				$where = array('account'=>$account);

				$user = M('User')->where($where)->field(array('id','lock'))->find();
				//检查用户是否被锁定 
				if($user && !$user['lock']){
					session('uid',$user['id']);
				}
			}
		
		}

		if(!isset($_SESSION['uid'])){
        	redirect(U('Login/index'));
        }
	}

	/**
	 * 头像上传
	 */
	public function uploadFace(){
		if(!IS_POST){
			halt('页面不存在');
		}
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     2000000 ;// 设置附件上传大小
		$upload->replace   =     true;//存在同名文件是否是覆盖，默认为false
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =      './Uploads/Face/'; // 设置附件上传根目录
		$upload->savePath  =      ''; // 设置附件上传（子）目录
		// 上传文件 
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
		    $this->error($upload->getError());
		}else{// 上传成功 获取上传文件信息
		   $pic = $info['Filedata'];
		   $old_pic = $upload->rootPath.$pic['savepath'].$pic['savename'];
		   //以下为生成缩略图
		   $image = new \Think\Image(); 
		   $image->open($old_pic);
		   $image->thumb(50,50);
		   $mini_pic = $upload->rootPath.$pic['savepath'].'mini_'.$pic['savename'];
		   $image->save($mini_pic);

		   $image->open($old_pic);
		   $image->thumb(80,80);
		   $medium_pic = $upload->rootPath.$pic['savepath'].'medium_'.$pic['savename'];
		   $image->save($medium_pic);

		   $image->open($old_pic);
		   $image->thumb(180,180);
		   $max_pic = $upload->rootPath.$pic['savepath'].'max_'.$pic['savename'];
		   $image->save($max_pic);
		   @unlink($old_pic);
		   
		   //返回消息到前台处理
		   $uploads = array(
				'status' => 1,
				'path' => array(
					'max' => $pic['savepath'].'max_'.$pic['savename'],
					'medium' => $pic['savepath'].'medium_'.$pic['savename'],
					'mini' => $pic['savepath'].'mini_'.$pic['savename']
					)
				);
		   echo json_encode($uploads);

		}

	}

	//异步创建分组
	public function addGroup(){
		if(!IS_AJAX){
			halt('页面不存在');
		}
		$data = array(
			'name'=> I("post.name"),
			'uid'=>session('uid')
			);
		if(M('Group')->data($data)->add()){
			echo json_encode(array('status'=>1,'msg'=>'创建成功'));
		}else{
			echo json_encode(array('status'=>0,'msg'=>'创建失败，请重试...'));
		}
		
	}

	//异步添加关注
	public function addFollow(){
		if(!IS_AJAX){
			halt('页面不存在');
		}
		$data = array(
			'follow' => I('post.follow'),
			'fans' => session('uid'),
			'gid' => I('post.gid')
			);
		if(M('Follow')->data($data)->add()){
			$db = M('Userinfo');
			$db->where(array('uid'=>$data['follow']))->setInc('fans');
			$db->where(array('uid'=>session('uid')))->setInc('follow');
			echo json_encode(array('status'=>1,'msg'=>'关注成功'));
		}else{
			echo json_encode(array('status'=>0,'msg'=>'关注失败，请重试...'));
		}
	}

	/**
	 * 图片上传
	 */
	public function uploadPic(){
		if(!IS_POST){
			halt('页面不存在');
		}
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     2000000 ;// 设置附件上传大小
		$upload->replace   =     true;//存在同名文件是否是覆盖，默认为false
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =      './Uploads/Pic/'; // 设置附件上传根目录
		$upload->savePath  =      ''; // 设置附件上传（子）目录
		// 上传文件 
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
		    $this->error($upload->getError());
		}else{// 上传成功 获取上传文件信息
		   $pic = $info['Filedata'];
		   $old_pic = $upload->rootPath.$pic['savepath'].$pic['savename'];
		   //以下为生成缩略图
		   $image = new \Think\Image(); 
		   $image->open($old_pic);
		   $image->thumb(120,120);
		   $mini_pic = $upload->rootPath.$pic['savepath'].'mini_'.$pic['savename'];
		   $image->save($mini_pic);

		   $image->open($old_pic);
		   $image->thumb(380,380);
		   $medium_pic = $upload->rootPath.$pic['savepath'].'medium_'.$pic['savename'];
		   $image->save($medium_pic);

		   $image->open($old_pic);
		   $image->thumb(800,800);
		   $max_pic = $upload->rootPath.$pic['savepath'].'max_'.$pic['savename'];
		   $image->save($max_pic);
		   @unlink($old_pic);
		   
		   //返回消息到前台处理
		   $uploads = array(
				'status' => 1,
				'path' => array(
					'max' => $pic['savepath'].'max_'.$pic['savename'],
					'medium' => $pic['savepath'].'medium_'.$pic['savename'],
					'mini' => $pic['savepath'].'mini_'.$pic['savename']
					)
				);
		   echo json_encode($uploads);

		}
	}

	/**
	 * 异步移除关注与粉丝
	 */
	public function delFollow(){
		if(!IS_AJAX){
			halt('页面不存在');
		}
		$uid = I('post.uid');
		$type = I('post.type');
		$where = $type ? array('follow'=>$uid,'fans'=>session('uid')) : array('fans'=>$uid,'follow'=>session('uid'));

		if(M('Follow')->where($where)->delete()){
			$db = M('Userinfo');
				if($type){
					$db->where(array('uid'=>session('uid')))->setDec('follow');
					$db->where(array('uid'=>$uid))->setDec('fans');
				}else{
					$db->where(array('uid'=>session('uid')))->setDec('fans');
					$db->where(array('uid'=>$uid))->setDec('follow');
				}
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 异步修改模板风格
	 */
	public function editStyle(){
		if(!IS_AJAX){
			halt('页面不存在');
		}
		$style = I('post.style');
		$where = array('uid'=>session('uid'));
		if(M('Userinfo')->where($where)->save(array('style'=>$style))){
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 异步轮询推送消息
	 */
	public function getMsg(){
		if(!IS_AJAX){
			halt('页面不存在');
		}

		$uid = session('uid');
		$msg = S('usermsg' . $uid);

		if($msg){
			//评论推送
			if($msg['comment']['status']){
			   $msg['comment']['status']=0;
			   S('usermsg'.$uid,$msg,0);
				echo json_encode(array(
					'status'=>1,
					'total'=>$msg['comment']['total'],
					'type' =>1
					));
				exit();
			}
			//私信推送
			if($msg['letter']['status']){
			   $msg['letter']['status']=0;
			   S('usermsg'.$uid,$msg,0);
				echo json_encode(array(
					'status'=>1,
					'total'=>$msg['letter']['total'],
					'type' =>2
					));
				exit();
			}
			//@me推送
			if($msg['atme']['status']){
			   $msg['atme']['status']=0;
			   S('usermsg'.$uid,$msg,0);
				echo json_encode(array(
					'status'=>1,
					'total'=>$msg['atme']['total'],
					'type' =>3
					));
				exit();
			}
		}
		echo json_encode(array('status'=>0));
	}

}












