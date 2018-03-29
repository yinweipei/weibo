-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-09-22 10:07:52
-- 服务器版本： 5.6.28
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weibo`
--

-- --------------------------------------------------------

--
-- 表的结构 `mp_admin`
--

CREATE TABLE IF NOT EXISTS `mp_admin` (
  `id` int(10) unsigned NOT NULL COMMENT '管理员ID',
  `username` char(20) NOT NULL DEFAULT '''''' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '''''' COMMENT '密码',
  `logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上一次登录时间',
  `loginip` char(20) NOT NULL DEFAULT '''''' COMMENT '上一次登录IP',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:正常,1:锁定',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:超级管理员，1:普通管理员'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `mp_admin`
--

INSERT INTO `mp_admin` (`id`, `username`, `password`, `logintime`, `loginip`, `lock`, `admin`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1504939857, '0.0.0.0', 0, 0),
(2, 'admin01', 'e10adc3949ba59abbe56e057f20f883e', 1504581421, '0.0.0.0', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `mp_atme`
--

CREATE TABLE IF NOT EXISTS `mp_atme` (
  `id` int(11) NOT NULL,
  `wid` int(11) NOT NULL COMMENT '提到我的微博ID',
  `uid` int(11) NOT NULL COMMENT '所属用户ID'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='@提到我的微博';

--
-- 转存表中的数据 `mp_atme`
--

INSERT INTO `mp_atme` (`id`, `wid`, `uid`) VALUES
(1, 11, 2),
(2, 11, 3),
(3, 11, 5),
(4, 12, 1),
(5, 13, 1),
(6, 13, 3),
(7, 14, 1);

-- --------------------------------------------------------

--
-- 表的结构 `mp_comment`
--

CREATE TABLE IF NOT EXISTS `mp_comment` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
  `time` int(10) unsigned NOT NULL COMMENT '评论时间',
  `uid` int(11) NOT NULL COMMENT '评论用户的ID',
  `wid` int(11) NOT NULL COMMENT '所属微博ID'
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='微博评论表';

--
-- 转存表中的数据 `mp_comment`
--

INSERT INTO `mp_comment` (`id`, `content`, `time`, `uid`, `wid`) VALUES
(1, '知道了，我会关注的[抱抱]', 1504078591, 1, 3),
(2, '[可怜]明天开学了', 1504146522, 1, 2),
(3, '哈哈哈[哈哈]', 1504146599, 1, 3),
(4, '你们好[嘻嘻]', 1504146657, 1, 2),
(5, '打发似的[衰]', 1504146765, 1, 2),
(6, '咋又是我的评论[害羞]', 1504165272, 1, 2),
(7, '我是表情大王[怒骂]', 1504165294, 1, 2),
(8, '我才不信 // @admin : [可怜]明天开学了', 1504274613, 1, 6),
(10, '我也不信[可爱]', 1504426251, 1, 9),
(11, '哈哈，只要你请我就去[委屈]', 1504507930, 5, 11),
(12, '阿敏', 1504508229, 5, 14),
(16, '再见，广州见[疑问]', 1504786204, 1, 19),
(14, '鸟叔你好[吃惊]', 1504509900, 2, 11),
(15, '我给自己漂亮', 1504613990, 1, 15),
(17, '带上我啊[可怜]', 1504787859, 1, 20),
(18, '我也去！！！！[哈哈]', 1504788136, 1, 19),
(19, '呵呵哒[哈哈]', 1504788798, 1, 21),
(20, '1111111111111111111', 1504789199, 1, 22),
(21, '回复@admin：呵呵哒牛', 1504875280, 1, 22);

-- --------------------------------------------------------

--
-- 表的结构 `mp_follow`
--

CREATE TABLE IF NOT EXISTS `mp_follow` (
  `follow` int(10) unsigned NOT NULL COMMENT '关注用户的ID',
  `fans` int(10) unsigned NOT NULL COMMENT '粉丝用户ID',
  `gid` int(11) NOT NULL COMMENT '所属关注分组ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注与粉丝表';

--
-- 转存表中的数据 `mp_follow`
--

INSERT INTO `mp_follow` (`follow`, `fans`, `gid`) VALUES
(2, 1, 1),
(3, 1, 1),
(1, 2, 0),
(4, 2, 0),
(1, 3, 0),
(2, 3, 0),
(4, 3, 0);

-- --------------------------------------------------------

--
-- 表的结构 `mp_group`
--

CREATE TABLE IF NOT EXISTS `mp_group` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT '' COMMENT '分组名称',
  `uid` int(11) NOT NULL COMMENT '所属用户的ID'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='关注分组表';

--
-- 转存表中的数据 `mp_group`
--

INSERT INTO `mp_group` (`id`, `name`, `uid`) VALUES
(1, '后盾网友', 1),
(2, '同学', 1),
(3, '网友', 2),
(4, '朋友', 1);

-- --------------------------------------------------------

--
-- 表的结构 `mp_keep`
--

CREATE TABLE IF NOT EXISTS `mp_keep` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '收藏用户的ID',
  `time` int(10) unsigned NOT NULL COMMENT '收藏时间',
  `wid` int(11) NOT NULL COMMENT '收藏微博的ID'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='收藏表';

--
-- 转存表中的数据 `mp_keep`
--

INSERT INTO `mp_keep` (`id`, `uid`, `time`, `wid`) VALUES
(6, 1, 1504871574, 21),
(3, 1, 1504426224, 9),
(4, 1, 1504613970, 15),
(5, 1, 1504833672, 23);

-- --------------------------------------------------------

--
-- 表的结构 `mp_letter`
--

CREATE TABLE IF NOT EXISTS `mp_letter` (
  `id` int(11) NOT NULL,
  `from` int(11) NOT NULL COMMENT '发私用户ID',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '私信内容',
  `time` int(10) unsigned NOT NULL COMMENT '私信发送时间',
  `uid` int(11) NOT NULL COMMENT '所属用户ID（收信人）'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='私信表';

--
-- 转存表中的数据 `mp_letter`
--

INSERT INTO `mp_letter` (`id`, `from`, `content`, `time`, `uid`) VALUES
(1, 1, '我是超级用户，我来邀请您的加入', 1504401562, 2),
(2, 2, 'admin你好，我来找你去大保健', 1504403028, 1),
(3, 1, '好啊，只要你出钱，我马上去！！！', 1504403960, 2),
(4, 2, '你快来啊', 1504509951, 1),
(5, 2, '吉萨第', 1504510808, 1),
(6, 1, '您好，私聊啊。。。', 1504874929, 4);

-- --------------------------------------------------------

--
-- 表的结构 `mp_picture`
--

CREATE TABLE IF NOT EXISTS `mp_picture` (
  `id` int(11) NOT NULL,
  `mini` varchar(60) NOT NULL DEFAULT '' COMMENT '小图',
  `medium` varchar(60) NOT NULL DEFAULT '' COMMENT '中图',
  `max` varchar(60) NOT NULL DEFAULT '' COMMENT '大图',
  `wid` int(11) NOT NULL COMMENT '所属微博ID'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='微博配图表';

--
-- 转存表中的数据 `mp_picture`
--

INSERT INTO `mp_picture` (`id`, `mini`, `medium`, `max`, `wid`) VALUES
(1, '2017-09-06/mini_59affece41d27.jpg', '2017-09-06/medium_59affece41d27.jpg', '2017-09-06/max_59affece41d27.jpg', 18),
(2, '2017-09-07/mini_59b12effaee41.jpg', '2017-09-07/medium_59b12effaee41.jpg', '2017-09-07/max_59b12effaee41.jpg', 19);

-- --------------------------------------------------------

--
-- 表的结构 `mp_user`
--

CREATE TABLE IF NOT EXISTS `mp_user` (
  `id` int(11) NOT NULL,
  `account` char(20) NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `registime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定（0：否，1：是）'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户表';

--
-- 转存表中的数据 `mp_user`
--

INSERT INTO `mp_user` (`id`, `account`, `password`, `registime`, `lock`) VALUES
(1, 'admin', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503965827, 0),
(2, 'admin01', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503965976, 1),
(3, 'admin02', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503966153, 0),
(4, 'admin03', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503970148, 0),
(5, 'admin04', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503970177, 0),
(6, 'admin05', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1503972062, 0),
(7, 'admin06', 'd0dcbf0d12a6b1e7fbfa2ce5848f3eff', 1504767616, 0);

-- --------------------------------------------------------

--
-- 表的结构 `mp_userinfo`
--

CREATE TABLE IF NOT EXISTS `mp_userinfo` (
  `id` int(11) NOT NULL,
  `username` varchar(45) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `truename` varchar(45) DEFAULT NULL COMMENT '真实名称',
  `sex` enum('男','女') NOT NULL DEFAULT '男' COMMENT '性别',
  `location` varchar(45) NOT NULL DEFAULT '' COMMENT '所在地',
  `constellation` char(10) NOT NULL DEFAULT '' COMMENT '星座',
  `intro` varchar(100) NOT NULL DEFAULT '' COMMENT '一句话介绍自己',
  `face50` varchar(60) NOT NULL DEFAULT '' COMMENT '50*50头像',
  `face80` varchar(60) NOT NULL DEFAULT '' COMMENT '80*80头像',
  `face180` varchar(60) NOT NULL DEFAULT '' COMMENT '180*180头像',
  `style` varchar(45) NOT NULL DEFAULT 'default' COMMENT '个性模版',
  `follow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
  `fans` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `weibo` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '微博数',
  `uid` int(11) NOT NULL COMMENT '所属用户ID'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户信息表';

--
-- 转存表中的数据 `mp_userinfo`
--

INSERT INTO `mp_userinfo` (`id`, `username`, `truename`, `sex`, `location`, `constellation`, `intro`, `face50`, `face80`, `face180`, `style`, `follow`, `fans`, `weibo`, `uid`) VALUES
(1, 'admin', '法海', '女', '重庆 南岸', '处女座', '别说我不懂爱，你才不懂', '2017-09-08/mini_59b1ff11c3292.jpg', '2017-09-08/medium_59b1ff11c3292.jpg', '2017-09-08/max_59b1ff11c3292.jpg', 'style4', 2, 2, 13, 1),
(2, 'admin01', NULL, '男', '', '', '', '', '', '', 'default', 2, 2, 1, 2),
(3, 'admin02', NULL, '男', '', '', '', '', '', '', 'default', 3, 1, 0, 3),
(4, 'admin03', NULL, '男', '', '', '', '', '', '', 'default', 0, 2, 1, 4),
(5, 'admin04', NULL, '男', '', '', '', '', '', '', 'default', 0, 0, 1, 5),
(6, 'admin05', NULL, '男', '', '', '', '', '', '', 'default', 0, 0, 0, 6),
(7, 'admin06', '大王', '男', '天津 红桥', '巨蟹座', '妈卖批', '2017-09-07/mini_59b0f11956037.jpg', '2017-09-07/medium_59b0f11956037.jpg', '2017-09-07/max_59b0f11956037.jpg', 'default', 0, 0, 0, 7);

-- --------------------------------------------------------

--
-- 表的结构 `mp_weibo`
--

CREATE TABLE IF NOT EXISTS `mp_weibo` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '微博内容',
  `isturn` int(11) NOT NULL DEFAULT '0' COMMENT '是否转发（0：原创， 如果是转发的则保存该转发微博的ID）',
  `time` int(10) unsigned NOT NULL COMMENT '发布时间',
  `turn` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转发次数',
  `keep` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏次数',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论次数',
  `uid` int(11) NOT NULL COMMENT '所属用户的ID'
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='微博表';

--
-- 转存表中的数据 `mp_weibo`
--

INSERT INTO `mp_weibo` (`id`, `content`, `isturn`, `time`, `turn`, `keep`, `comment`, `uid`) VALUES
(11, '@admin01 @admin02 @admin04  我叫你们吃饭', 0, 1504430221, 1, 0, 2, 1),
(2, '我是asmin01发布的微博', 0, 1504013234, 2, 0, 5, 2),
(3, '今天我来微博发布东西了[害羞]喜欢我的小伙伴记得关注哦[嘻嘻][嘻嘻][嘻嘻]', 0, 1504062244, 1, 0, 2, 1),
(4, '知道了，我会关注的[抱抱]', 3, 1504078591, 1, 0, 0, 1),
(5, '// @admin : 知道了，我会关注的[抱抱]', 4, 1504093167, 0, 0, 0, 1),
(6, '[可怜]明天开学了', 2, 1504146522, 1, 0, 1, 1),
(10, '@admin01 @admin02 @admin03 我来哎特你们了[可爱]', 0, 1504429883, 0, 0, 0, 1),
(9, '我才不信 // @admin : [可怜]明天开学了', 2, 1504274612, 0, 1, 1, 1),
(12, '@admin 我给自己转发[挖鼻屎]', 11, 1504430479, 0, 0, 0, 1),
(13, '@admin @admin02 sdadsad', 0, 1504438617, 0, 0, 0, 4),
(14, '@admin 你收到我的私信吗？', 0, 1504508007, 0, 0, 1, 5),
(16, '今天我要测试图片上传[哈哈]', 0, 1504705226, 0, 0, 0, 1),
(17, 'fsaf', 0, 1504705578, 0, 0, 0, 1),
(18, '今天我来测试图片上传了[太开心][太开心]', 0, 1504706258, 1, 0, 0, 1),
(19, '明天阿凯就要广州了！！[拜拜]', 0, 1504784131, 2, 0, 2, 1),
(20, '再见，广州见[疑问]', 19, 1504786204, 0, 0, 1, 1),
(21, '我也去！！！！[哈哈]', 19, 1504788136, 0, 1, 1, 1),
(22, '测试', 18, 1504789070, 1, 0, 2, 1),
(23, '1111111111111111111// @admin : 测试', 18, 1504789199, 0, 1, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mp_admin`
--
ALTER TABLE `mp_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mp_atme`
--
ALTER TABLE `mp_atme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `wid` (`wid`);

--
-- Indexes for table `mp_comment`
--
ALTER TABLE `mp_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `wid` (`wid`);

--
-- Indexes for table `mp_follow`
--
ALTER TABLE `mp_follow`
  ADD KEY `follow` (`follow`),
  ADD KEY `fans` (`fans`),
  ADD KEY `gid` (`gid`);

--
-- Indexes for table `mp_group`
--
ALTER TABLE `mp_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `mp_keep`
--
ALTER TABLE `mp_keep`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wid` (`wid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `mp_letter`
--
ALTER TABLE `mp_letter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `mp_picture`
--
ALTER TABLE `mp_picture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wid` (`wid`);

--
-- Indexes for table `mp_user`
--
ALTER TABLE `mp_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account` (`account`);

--
-- Indexes for table `mp_userinfo`
--
ALTER TABLE `mp_userinfo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `mp_weibo`
--
ALTER TABLE `mp_weibo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mp_admin`
--
ALTER TABLE `mp_admin`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `mp_atme`
--
ALTER TABLE `mp_atme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `mp_comment`
--
ALTER TABLE `mp_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `mp_group`
--
ALTER TABLE `mp_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `mp_keep`
--
ALTER TABLE `mp_keep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `mp_letter`
--
ALTER TABLE `mp_letter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `mp_picture`
--
ALTER TABLE `mp_picture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `mp_user`
--
ALTER TABLE `mp_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `mp_userinfo`
--
ALTER TABLE `mp_userinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `mp_weibo`
--
ALTER TABLE `mp_weibo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
