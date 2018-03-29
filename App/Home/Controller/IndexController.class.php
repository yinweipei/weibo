<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

	/**
     * 微博首页视图
     */
    public function index(){
    	//实例化微博视图模型
        $db = D('WeiboView');
        //取得当前用户的ID与当前用户所有关注好友的ID
        $uid = array(session('uid'));
        $where = array('fans'=>session('uid'));

        if(isset($_GET['gid'])){
        	$gid = I('get.gid');
        	$where['gid'] = $gid;
        	$uid = array();
        }
        //var_dump($where);exit();

        $result = M('Follow')->field('follow')->where($where)->select();

        if($result){
        	foreach ($result as $k => $v) {
        		$uid[] = $v['follow'];
        	}
        }
        if(!$uid){
            echo '111';
            $uid[]= 0;  
        }
        //组合where条件，条件为当前用户自身的ID与当前用户所有关注好友的ID
        $where = array('uid'=> array('IN',$uid));
        // var_dump($where);exit();
        //统计数据总条数，用于分页
        $count = $db->where($where)->count();
        $page = new \Think\Page($count,5);
        $page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$limit = $page->firstRow.','.$page->listRows;

        //读取所有微博
        $result = $db->getAll($where,$limit);
        $this->weibo = $result;
        $this->page = $page->show();
        // p($result);die();
        $this->display();
    }

    //微博发布处理
    public function sendWeibo(){
    	if(!IS_POST){
    		halt('页面不存在');
    	}
    	$data = array(
    		'content'=> I('post.content'),
    		'time'=> time(),
    		'uid'=>session('uid')
    		);
    	if($wid=M('Weibo')->data($data)->add()){
    		if(!empty($_POST['max'])){
    			$img = array(
    				'mini'=>I('post.mini'),
    				'medium'=>I('post.medium'),
    				'max'=>I('post.max'),
    				'wid'=>$wid
    				);
    			M('Picture')->data($img)->add();
    		}

            M('Userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
            //处理@用户
            $this->_atemHandel($data['content'],$wid);

    		$this->success('发布成功',$_SERVER['HTTP_REFERER']);
    	}else{
    		$this->error('发布失败请重试。。。');
    	}
    }

    /**
     * @用户处理
     */
    private function _atemHandel($content,$wid){
        $preg = '/@(\S+?)\s/is';
        preg_match_all($preg, $content,$arr);
       
        if(!empty($arr[1])){
            $db = M('Userinfo');
            $atme = M('Atme');
            foreach ($arr[1] as $v) {
                $uid = $db->where(array('username'=>$v))->getField('uid');
                if($uid){
                    $data = array(
                        'wid' => $wid,
                        'uid' => $uid
                        );
                    //写入消息推送
                    set_msg($uid,3);
                    $atme->data($data)->add();
                }
            }
            
        }
    }

    /**
     * 转发微博
     */
    public function turn(){
    	if(!IS_POST){
    		halt('页面不存在');
    	}
    	// p($_POST);exit();
    	//原微博ID
    	$id = I('post.id');
    	$tid = I('post.tid');
    	$content = I('post.content');
    	//提取插入数据
    	$data = array(
    		'content' => $content,
    		'isturn' => $tid ? $tid : $id,
    		'time' => time(),
    		'uid' => session('uid')
    		);
    	// p($data);exit();
    	//插入数据到微博表
    	$db = M('Weibo');
    	if($wid = $db->data($data)->add()){
    		//原微博转发数+1
    		$db->where(array('id' => $id))->setInc('turn');

    		if($tid){
    			$db->where(array('id' => $tid))->setInc('turn');
    		}
    		//用户发布微博数+1
    		M('Userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
            //处理@用户
            $this->_atemHandel($data['content'],$wid);
    		//如果同时点击评论插入内容到评论表
    		if(isset($_POST['becomment'])){
    			$data = array(
    				'content' => $content,
    				'time' => time(),
    				'uid' => session('uid'),
    				'wid' =>$id
    				);
    			//插入评论数据后给原微博评论次数+1
    			if(M('Comment')->data($data)->add()){
    				$db->where(array('id'=>$id))->setInc('comment');
    			}
    		}

    		$this->success('转发成功',$_SERVER['HTTP_REFERER']);
    	}else{
    		$this->error('转发失败请重试。。。');
    	}
    }

    /**
     * 异步获得评论内容
     */
    public function getComment(){
    	if(!IS_AJAX){
    		halt('页面不存在');
    	}
    	$wid = I('post.wid');
    	$where = array('wid'=>$wid);
    	//数据总条数
    	$count = M('Comment')->where($where)->count();
    	//数据可分的总页数
    	$total = ceil($count/4);
    	$page = isset($_POST['page']) ? I('post.page') : 1; 
    	$limit = $page < 2 ? '0,4' : (4 * ($page - 1)) . ',4';

    	$result = D('CommentView')->where($where)->order('time desc')->limit($limit)->select();
    	if($result){
    		$str = '';
    		foreach ($result as $v) {
    			$str .= '<dl class="comment_content">';
				$str .= '<dt><a href="' . U('/' . $v['uid']) . '">';
				$str .= '<img src="';
				$str .= __ROOT__;
				if ($v['face']) {
					$str .= '/Uploads/Face/' . $v['face'];
				} else {
					$str .= '/Public/Home/Images/noface.gif';
				}
				$str .= '" alt="' . $v['username'] . '" width="30" height="30"/>';
			    $str .= '</a></dt><dd>';  
			    $str .= '<a href="' . U('/' . $v['uid']) . '" class="comment_name">';
			    $str .= $v['username'] . '</a> : ' . replace_weibo($v['content']);
			    $str .= '&nbsp;&nbsp;( ' . time_format($v['time']) . ' )';
			    $str .= '<div class="reply">';
			    $str .= '<a href="">回复</a>';
				$str .= '</div></dd></dl>';
    		}

    			if ($total > 1) {
				$str .= '<dl class="comment-page">';

				switch ($page) {
					case $page > 1 && $page < $total :
						$str .= '<dd page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
						$str .= '<dd page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
						break;

					case $page < $total : 
						$str .= '<dd page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
						break;

					case $page == $total : 
						$str .= '<dd page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
						break;
				}

				$str .= '</dl>';
			}

    		echo $str;
    	}else{
    		echo 'false';
    	}
    }


    //退出登录操作
    public function loginOut(){
    	//删除session信息
    	session_unset();
    	session_destroy();

    	//清空用于自动登录的COOKIE
    	cookie('auto','',time()-3600,'/');
    	//跳转到登录也
    	redirect(U('Login/index'));
    }

    /**
     * 收藏
     */
    public function keep(){
    	if(!IS_AJAX){
    		halt('页面不存在');
    	}
    	$wid = I('post.wid');
    	$uid = session('uid');

    	$db = M('Keep');

    	//检测用户是否已经收藏该微博
    	$where = array('wid'=>$wid,'uid'=>$uid);
    	if($db->where($where)->getField('id')){
    		echo -1;
    		exit();
    	}

    	//添加收藏
    	$data = array(
    		'uid' => $uid,
    		'wid' => $wid,
    		'time' => time()
    		);
    	if($db->data($data)->add()){
    		M('Weibo')->where(array('id'=>$wid))->setInc('keep');
    		//收藏成功时对该微博数+1
    		echo 1;
    	}else{
    		echo 0;
    	}

    }

    /**
     * 评论
     */
    public function comment(){
    	if(!IS_AJAX){
    		halt('页面不存在');
    	}
    	//提取评论数据
    	$data = array(
    		'content' => I('post.content'),
    		'time' => time(),
    		'uid' => session('uid'),
    		'wid' => I('post.wid')
    		);
    
    	if(M('Comment')->data($data)->add()){
    		$field = array('username','face50'=>'face','uid');
	    	$where = array('uid'=>$data['uid']);
	    	$user = M('Userinfo')->field($field)->where($where)->find();

	    	//被评论微博的发布者用户名
	    	$uid = I('post.uid');
	    	$username = M('Userinfo')->where(array('uid'=>$uid))->getField('username');
	    	
	    	$db = M('Weibo');
	    	//评论数+1
	    	$db->where(array('id'=>$data['wid']))->setInc('comment');
	    	if($_POST['isturn']){
	    		//读取转发微博的ID与内容
	    		$field = array('id','content','isturn');
	    		$weibo = $db->field($field)->find($data['wid']);
	    		$content = $weibo['isturn'] ? $data['content'] .'// @' .$username. ' : ' .$weibo['content'] : $data['content']; 

	    		$cons = array(
	    			'content' => $content,
	    			'isturn' => $weibo['isturn'] ? $weibo['isturn'] : $data['wid'],
	    			'time' => $data['time'],
	    			'uid' => $data['uid']
	    			);
	    		if($db->data($cons)->add()){
	    			$db->where(array('id'=>$weibo['id']))->setInc('turn');
                    M('Userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
	    		}

	    		echo 1;
	    		exit();
	    	}

	   		//组合评论样式字符串返回
			$str = '';
			$str .= '<dl class="comment_content">';
			$str .= '<dt><a href="' . U('/' . $data['uid']) . '">';
			$str .= '<img src="';
			$str .= __ROOT__;
			if ($user['face']) {
				$str .= '/Uploads/Face/' . $user['face'];
			} else {
				$str .= '/Public/Home/Images/noface.gif';
			}
			$str .= '" alt="' . $user['username'] . '" width="30" height="30"/>';
		    $str .= '</a></dt><dd>';  
		    $str .= '<a href="' . U('/' . $data['uid']) . '" class="comment_name">';
		    $str .= $user['username'] . '</a> : ' . replace_weibo($data['content']);
		    $str .= '&nbsp;&nbsp;( ' . time_format($data['time']) . ' )';
		    $str .= '<div class="reply">';
		    $str .= '<a href="">回复</a>';
			$str .= '</div></dd></dl>';

            set_msg(session('uid'),1);

			echo $str;
    	}else{
    		return false;
    	}
    }

    /**
     * 异步删除微博
     */
    public function delWeibo(){
    	if(!IS_AJAX){
    		halt('页面不存在');
    	}
    	//获取删除微博ID
    	$wid = I('post.wid');
    	if(M('Weibo')->delete($wid)){
    		//如果删除的微博含有图片
    		$db = M('Picture');
    		$img = $db->where(array('wid'=>$wid))->find();
    		//对图片记录进行删除
    		if($img){
    			$db->delete($img['id']);
    			@unlink('./Uploads/Pic/'.$img['mini']);
    			@unlink('./Uploads/Pic/'.$img['medium']);
    			@unlink('./Uploads/Pic/'.$img['max']);
    		}
    		M('Userinfo')->where(array('uid'=>session('uid')))->setDec('weibo');
            M('Comment')->where(array('wid'=>$wid))->delete();
    		echo 1;
    	}else{
    		echo 0;
    	}
    }

}