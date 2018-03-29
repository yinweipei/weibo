<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'weibo',
    'DB_USER' => 'root',
    'DB_PWD' => '123123',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'mp_',
    'TMPL_ENGINE_TYPE' => 'Think',
    'ENCTYPTION_KEY' => 'www.yinweipei.top',
    'AUTO_LOGIN_TIME' => time()+3600*24*7,
    'URL_MODEL' => 2,
    'DEFAULT_FILTER' => 'trim,htmlspecialchars,strip_tags,stripslashes',

    //图片上传
	'UPLOAD_MAX_SIZE' => 2000000,	//最大上传大小
	'UPLOAD_PATH' => './Uploads/',	//文件上传保存路径
	'UPLOAD_EXTS' => array('jpg', 'jpeg', 'gif', 'png'),	//允许上传文件的后缀

	//URL路由配置
	'URL_ROUTER_ON' => true,	//开启路由功能
	'URL_ROUTE_RULES' => array(	//定义路由规则
		':id\d' => 'User/index',
		'follow/:uid\d' => array('User/followList', 'type=1'),
		'fans/:uid\d' => array('User/followList', 'type=0'),
		),

	//缓存设置
	'DATA_CACHE_SUBDIR' => true,	//开启以哈唏形式生成缓存目录
	'DATA_PATH_LEVEL' => 2,	//目录层次
	// 'DATA_CACHE_TYPE' => 'Memcache',
	// 'MEMCACHE_HOST' => '127.0.0.1',
	// 'MEMCACHE_PORT' => 11211,
	//加载扩展配置
    'LOAD_EXT_CONFIG' => 'system',
);