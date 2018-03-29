<?php 
namespace Home\Controller;
use Think\Controller;

class SearchController extends Controller{
	//搜索找人
	public function sechUser(){
		$keyword = $this->_getKeyword();
		if($keyword){
			$where = array(
				'username'=>array('like','%'.$keyword.'%'),
				'uid'=>array('neq',session('uid')),
				);
			$field = array('username','sex','location','intro','face80','follow','fans','weibo','uid');
			$db = M('Userinfo');
			//导入分页类
			$count = $db->where($where)->count();
			$page = new \Think\Page($count,3);
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$limit = $page->firstRow.','.$page->listRows;
			$result = $db->where($where)->field($field)->limit($limit)->select();

			//重新组合结果集，得到是否已关注或是否未关注
			$result = $this->_getMutual($result); 
			$this->result = $result ? $result : false;
			$this->count = $count;
			$this->page = $page->show();
			//var_dump($result);exit();
		}
		$this->keyword = $keyword;
		$this->display();
	}

	/**
	 * 搜索微博
	 */
	public function sechWeibo(){
		$keyword = $this->_getKeyword();
		if($keyword){
			$where = array(
				'content'=>array('like','%'.$keyword.'%')
				);
			//$field = array('username','sex','location','intro','face80','follow','fans','weibo','uid');
			$db = D('WeiboView');
			//导入分页类
			$count = $db->where($where)->count();
			$page = new \Think\Page($count,3);
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$limit = $page->firstRow.','.$page->listRows;
			$weibo = $db->getAll($where,$limit);

			$this->weibo = $weibo ? $weibo : false;
			$this->count = $count;
			$this->page = $page->show();
		}
		$this->keyword = $keyword;
		$this->display();
	}

	//返回搜索关键字
	private function _getKeyword(){
		return I('get.keyword') == '搜索微博、找人' ? NULL : I('get.keyword');
	}

	//重新组合结果集，得到是否已关注或是否未关注
	private function _getMutual($result){
		if(!$result) return false;

		$db = M('Follow');

		foreach ($result as $k => $v) {
			//是否互相关注
			$sql = '(SELECT `follow` FROM `mp_follow` WHERE `follow` = ' . $v['uid'] . ' AND `fans` = ' . session('uid') . ') UNION (SELECT `follow` FROM `mp_follow` WHERE `follow` = ' . session('uid') . ' AND `fans` = ' . $v['uid'] . ')';
			$mutual = $db->query($sql);
			if(count($mutual)==2){
				$result[$k]['mutual'] = 1;
				$result[$k]['followed'] = 1;
			}else{
				$result[$k]['mutual'] = 0;
				//未互相关注是检索是否已关注
				$where = array(
					'follow' => $v['uid'],
					'fans' => session('uid')
					);
				$result[$k]['followed'] = $db->where($where)->count();
			}
		}
		return $result;
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
}