<?php 
namespace Home\Model;
use Think\Model\ViewModel;

Class WeiboViewModel extends ViewModel{
	protected $viewFields = array(
		'weibo' => array(
			'id','content','isturn','time','turn','keep','comment','uid',
			'_type' => 'LEFT'
			),
		'userinfo' => array(
			'username','face50'=>'face',
			'_on' =>'weibo.uid = userinfo.uid',
			'_type' => 'LEFT'
			),
		'picture' => array(
			'mini','medium','max',
			'_on' => 'weibo.id = picture.wid'
			)
		);

	//返回查询所有记录
	public function getAll($where,$limit){
		$result = $this->where($where)->limit($limit)->order('time desc')->select();
		//重组结果集数组，得到转发微博
		if($result){
			foreach ($result as $k => $v) {
				if($v['isturn']){
					$result[$k]['isturn'] = $this->find($v['isturn']);
				}
			}
		}
		return $result;
	}
}
