<?php 
namespace Home\Controller;
use Think\Controller;
/**
 * 用户个人页控制器
 */
class UserController extends CommonController{
	/**
	 * 用户个人视图
	 */
	public function index(){
		$id = I('get.id');
		//读取用户个人信息
		$where = array('uid' => $id);
		$userinfo = M('Userinfo')->where($where)->field('truename,face50,face80,style',true)->find();
		if(!$userinfo){
			header('Content-Type:text/html;Charset=UTF-8');
			redirect(__ROOT__,3,'用户不存在，正在为您跳转至首页...');
		}
		$this->userinfo = $userinfo;
		//导入分页处理也页


		//读取用户发布的微博
		$where = array('uid' => $id);
		$count = M('Weibo')->where($where)->count();

		$page = new \Think\Page($count,5);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		
		$limit = $page->firstRow . ',' . $page->listRows;
		$weibo = D('weiboView')->getAll($where,$limit);

		$this->weibo = $weibo;
		$this->page = $page->show(); 

		//我的关注
		if(S('follow_'.$id)){
			//缓存已成功并且缓存未过期
			$follow = S('follow_'.$id);
		}else{
			$where = array('fans'=>$id);
			$follow = M('Follow')->where($where)->field('follow')->select();
			foreach ($follow as $k => $v) {
				$follow[$k] = $v['follow'];
			}

			$field = array('username','face50'=>'face','uid');
			$where = array('uid'=>array('IN',$follow));
			$follow = M('Userinfo')->where($where)->field($field)->limit(8)->select();
			S('follow_'.$id,$follow,3600);
		}

		//我的粉丝
		if(S('fans_'.$id)){
			//缓存已成功并且缓存未过期
			$fans = S('fans_'.$id);
		}else{
			$where = array('follow'=>$id);
			$fans = M('Follow')->where($where)->field('fans')->select();
			foreach ($fans as $k => $v) {
				$fans[$k] = $v['fans'];
			}

			$field = array('username','face50'=>'face','uid');
			$where = array('uid'=>array('IN',$fans));
			$fans = M('Userinfo')->where($where)->field($field)->limit(8)->select();
			S('fans_'.$id,$fans,3600);
		}
		
		$this->follow = $follow;
		$this->fans = $fans;
		$this->display();
	}

	/**
	 * 用户关注与粉丝列表
	 */
	public function followList(){
		$uid = I('get.uid');
		//区分关注与粉丝(1:关注，0:粉丝)
		$type = I('get.type');
		$db = M('Follow');
		
		//根据type参数不同，读取用户关注与粉丝ID
		$where = $type ? array('fans'=>$uid) : array('follow' => $uid);
		//分页处理
		$count = $db->where($where)->count();
		$page = new \Think\Page($count,3);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow . ',' . $page->listRows;
		$field = $type ? 'follow' : 'fans';
		
		$uids = $db->field($field)->where($where)->limit($limit)->select();

		if($uids){
			//把用户关注或粉丝ID重组为一维数组
			foreach ($uids as $k => $v) {
				$uids[$k] = $type ? $v['follow'] :$v['fans'];
			}
			//提取用户个人信息
			$where = array('uid'=>array('IN',$uids));
			$field = array('face50'=>'face','username','sex','location','follow','fans','weibo','uid');

			$users = M('Userinfo')->field($field)->where($where)->select();
			//分配用户信息的视图
			$this->users = $users;
		}
		//关注列表ID
		$where = array('fans'=> session('uid'));
		$follow = $db->field('follow')->where($where)->select();
		if($follow){
			foreach ($follow as $k => $v) {
				$follow[$k] = $v['follow'];
			}
		}
		//粉丝列表ID
		$where = array('follow'=>session('uid'));
		$fans = $db->field('fans')->where($where)->select();
		if($fans){
			foreach ($fans as $k => $v) {
				$fans[$k] = $v['fans'];
			}
		}

		$this->follow = $follow;
		$this->fans = $fans;
		$this->type = $type;
		$this->count = $count;
		$this->display();
	}

	/**
	 * 微博收藏
	 */
	public function keep(){
		$uid = session('uid');

		$count = M('Keep')->where(array('uid'=>$uid))->count();
		$page = new \Think\Page($count,3);
		$limit = $page->firstRow . ',' . $page->listRows;

		$where = array('keep.uid'=>$uid);
		$weibo = D('KeepView')->getAll($where,$limit);

		$this->page = $page->show();
		$this->weibo = $weibo;
		$this->display('weiboList');
	}

	/**
	 * 异步取消收藏
	 */
	public function cancelKeep(){
		if (!IS_AJAX) {
			halt('页面不存在');
		}

		$kid = I('post.kid');
		$wid = I('post.wid');

		if(M('Keep')->delete($kid)){
			M('Weibo')->where(array('id'=>$wid))->setDec('keep');
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 私信列表
	 */
	public function letter(){
		$uid = session('uid');

		set_msg($uid,2,true);
		//分页处理
		$count = M('Letter')->where(array('uid'=>$uid))->count();
		$page = new \Think\Page($count,3);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow . ',' . $page->listRows;

		$where = array('letter.uid'=>$uid);
		$letter = D('LetterView')->where($where)->order('time desc')->limit($limit)->select();

		$this->letter = $letter;
		$this->count = $count;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 私信发送表单处理
	 */
	public function letterSend(){
		if(!IS_POST){
			halt('页面不存在');
		}
		$name = I('post.name');
		$where = array('username'=>$name);
		$uid = M('Userinfo')->where($where)->getField('uid');

		if(!$uid){
			$this->error('用户不存在');
		}

		$data = array(
			'from' => session('uid'),
			'content' => I('post.content'),
			'time' => time(),
			'uid' => $uid
			);
		if(M('Letter')->data($data)->add()){
			set_msg($uid,2);
			$this->success('私信已发送',U('letter'));
		}else{
			$this->error('私信发送失败',U('letter'));
		}
	}

	/**
	 * 评论列表
	 */
	public function comment(){
		set_msg(session('uid'),1,true);
		$where = array('uid'=>session('uid'));
		//分页处理
		$count = M('Comment')->where($where)->count();
		$page = new \Think\Page($count,3);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow . ',' . $page->listRows;
		//获取评论
		$comment = D('CommentView')->where($where)->limit($limit)->order('time desc')->select();

		$this->comment = $comment;
		$this->count = $count;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 评论回复
	 */
	public function reply(){
		if (!IS_AJAX) {
			halt('页面不存在');
		}
		$wid = I('post.wid');
		$data = array(
			'content'=>I('content'),
			'time' => time(),
			'uid' => session('uid'),
			'wid' => $wid
			);
		
		if(M('Comment')->data($data)->add()){
			M('Weibo')->where(array('id'=>$wid))->setInc('comment');
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 删除评论
	 */
	public function delComment(){
		if (!IS_AJAX) {
			halt('页面不存在');
		}
		$cid = I('post.cid');
		$wid = I('post.wid');

		if(M('Comment')->delete($cid)){
			M('Weibo')->where(array('id'=>$wid))->setDec('comment');
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * @提到我的
	 */
	public function atme(){
		set_msg(session('uid'),3,true);
		$where = array('uid'=>session('uid'));
		$wid = M('Atme')->where($where)->field('wid')->select();
		if($wid){
			foreach ($wid as $k => $v) {
				$wid[$k] = $v['wid'];
			}
		}

		$count = count($wid);
		$page = new \Think\Page($count,3);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow . ',' . $page->listRows;

		$where = array('id'=>array('IN',$wid));
		$weibo = D('WeiboView')->getAll($where,$limit);

		$this->weibo = $weibo;
		$this->page = $page->show();
		$this->count = $count;
		$this->display('weiboList');
	}

	/**
	 * 空操作
	 */
	public function _empty($name){
		$this->_getUrl($name);
	}

	/**
	 * 处理用户名空操作，获取用户ID跳转至用户个人页
	 */
	private function _getUrl($name){
		$name = htmlspecialchars($name);
		$where = array('username'=>$name);
		$uid = M('Userinfo')->where($where)->getField('uid');

		if(!$uid){
			redirect(U('Index/index'));
		}else{
			redirect(U('/'.$uid));
		}
	}
}
