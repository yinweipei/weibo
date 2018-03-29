<?php 
namespace Admin\Controller;
use Think\Controller;
/**
 * 微博管理控制器
 */
Class WeiboController extends CommonController{
	/**
	 * 微博视图首页
	 */
	public function index(){
		$where = array('isturn'=>0);
		//微博分页处理
		$count = M('Weibo')->where($where)->count();
        $page = new \Think\Page($count,5);
        $page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow.','.$page->listRows;
		$weibo = D('WeiboView')->where($where)->limit($limit)->order('time desc')->select();
		// p($weibo);

		$this->weibo = $weibo;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 删除微薄
	 */
	public function delWeibo(){
		$id = I('get.id');
		$uid = I('get.uid');

		//删除微博
		if(D('WeiboRelation')->relation(true)->delete($id)){
			//用户发布微博数-1
			M('Userinfo')->where(array('uid'=>$uid))->setDec('weibo');
			$this->success('删除成功',U('index'));
		}else{
			$this->error('删除失败，请重试。。。');
		}
	}

	/**
	 * 转发微博列表
	 */
	public function turn(){
		$where = array('isturn'=>array('gt',0));
		//微博分页处理
		$count = M('Weibo')->where($where)->count();
        $page = new \Think\Page($count,5);
        $page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow.','.$page->listRows;
		$db = D('WeiboView');
		unset($db->viewFields['picture']);
		$turn = $db->where($where)->limit($limit)->order('time desc')->select();
		
		$this->turn = $turn;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 微博检索
	 */
	public function sechWeibo(){
		if(isset($_GET['sech'])){
			$where = array('content'=>array('like','%'.I('get.sech').'%'));
			$weibo = D('WeiboView')->where($where)->order('time desc')->select();
			$this->weibo = $weibo ? $weibo : false;
		}
		$this->display();
	}

	/**
	 * 评论列表
	 */
	public function comment(){
		$count = M('Comment')->count();
        $page = new \Think\Page($count,5);
        $page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow.','.$page->listRows;
		$comment = D('CommentView')->limit($limit)->order('time desc')->select();

		$this->num = $num = I('get.p') ? (I('get.p')-1)*5 : 0; 
		$this->comment = $comment;
		$this->page = $page->show();
		$this->display();
	}

	/**
	 * 删除微博
	 */
	public function delComment(){
		$id = I('get.id');
		$wid = I('get.wid');

		if(M('Comment')->delete($id)){
			M('Weibo')->where(array('id'=>$wid))->setDec('comment');
			$this->success('删除成功',$_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败，请重试。。。');
		}
	}

	/**
	 * 评论检索
	 */
	public function sechComment(){
		if(isset($_GET['sech'])){
			$where = array('content'=>array('like','%'.I('get.sech').'%'));
			$comment = D('CommentView')->where($where)->order('time desc')->select();
			// var_dump($comment);exit();
			$this->comment = $comment ? $comment : false;
		}
		$this->display();
	}

}
