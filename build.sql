
-- 申请注册直播服务的用户表
CREATE TABLE `client_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` varchar(256) NOT NULL DEFAULT '' COMMENT '密码',
--   `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态:0锁定,1激活',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户类型',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';


-- 腾讯云直播事件index
CREATE TABLE `live_event_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `stream_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码',
  `channel_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码 同stream_id',
  `event_type` int(11) NOT NULL DEFAULT '0' COMMENT '事件类型 0 — 代表断流，1 — 代表推流，100 — 新的录制文件已生成，200 — 新的截图文件已生成',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播事件消息主表';


-- 腾讯云直播推流事件
CREATE TABLE `live_event_push` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `index_id` int(11) unsigned NOT NULL COMMENT '事件消息主表ID',
  `stream_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码',
  `event_type` int(11) NOT NULL DEFAULT '0' COMMENT '事件类型 0 — 代表断流，1 — 代表推流',
  `idc_id` int(11) NOT NULL DEFAULT '0' COMMENT 'idc_id 未知',
  `set_id` int(11) NOT NULL DEFAULT '0' COMMENT 'set_id 未知',
  `appname` varchar(32) NOT NULL DEFAULT '' COMMENT '推流路径',
  `app` varchar(32) NOT NULL DEFAULT '' COMMENT '推流域名',
  `sequence` varchar(32) NOT NULL DEFAULT '' COMMENT '消息序列号，标识一次推流活动，一次推流活动会产生相同序列号的推流和断流消息',
  `node` varchar(32) NOT NULL DEFAULT '' COMMENT 'Upload 接入点的 IP',
  `errcode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '断流错误码',
  `errmsg` varchar(255) NOT NULL DEFAULT '' COMMENT '断流错误信息',
  `stream_param` varchar(255) NOT NULL DEFAULT '' COMMENT '推流 url 参数',
  `push_duration` varchar(32) NOT NULL DEFAULT '' COMMENT '推流时长',
  `user_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '用户推流 IP',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_index_id` (`index_id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播推流事件';

-- 推流断流错误码

-- 错误码 错误描述    错误原因
-- 1   recv rtmp deleteStream  主播端主动断流
-- 2   recv rtmp closeStream   主播端主动断流
-- 3   recv() return 0 主播端主动断开 TCP 连接
-- 4   recv() return error 主播端 TCP 连接异常
-- 7   rtmp message large than 1M  收到流数据异常
-- 18  push url maybe invalid  推流鉴权失败，服务端禁止推流
-- 19  3rdparty auth failed    第三方鉴权失败，服务端禁止推流
-- 其他错误码   直播服务内部异常    如需处理请联系腾讯商务人员或者 提交工单，联系电话：4009-100-100

-- 腾讯云直播视频录制事件
CREATE TABLE `live_event_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `index_id` int(11) unsigned NOT NULL COMMENT '事件消息主表ID',
  `stream_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码',
  `event_type` int(11) NOT NULL DEFAULT '100' COMMENT '100 — 新的录制文件已生成',
  `video_id` varchar(128) NOT NULL DEFAULT '' COMMENT '点播用 vid，在点播平台可以唯一定位一个点播视频文件',
  `video_url` varchar(255) NOT NULL DEFAULT '' COMMENT '点播视频的下载地址',
  `file_size` varchar(32) NOT NULL DEFAULT '' COMMENT '文件大小',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '分片开始时间戳',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '分片结束时间戳',
  `media_start_time` int(11) NOT NULL DEFAULT '0' COMMENT 'media_start_time 未知',
  `file_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'file_id',
  `file_format` varchar(16) NOT NULL DEFAULT '' COMMENT '文件格式 flv, hls, mp4',
  `vod2Flag` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启点播 0 表示未开启，1 表示开启',
  `record_file_id` varchar(32) NOT NULL DEFAULT '' COMMENT '录制文件 ID,点播 2.0 开启时，才会有这个字段',
  `duration` int(11) NOT NULL DEFAULT '0' COMMENT '推流时长',
  `stream_param` varchar(255) NOT NULL DEFAULT '' COMMENT '推流 url 参数',
  `task_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'task_id 未知',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_index_id` (`index_id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播视频录制事件';


-- 腾讯云直播截图事件
CREATE TABLE `live_event_screenshot` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `index_id` int(11) unsigned NOT NULL COMMENT '事件消息主表ID',
  `stream_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码',
  `event_type` int(11) NOT NULL DEFAULT '200' COMMENT '200 — 新的截图文件已生成',
  `pic_url` varchar(255) NOT NULL DEFAULT '' COMMENT '点播用 vid，在点播平台可以唯一定位一个点播视频文件',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '截图时间戳',
  `pic_full_url` varchar(255) NOT NULL DEFAULT '' COMMENT '截图全路径',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_index_id` (`index_id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播视频录制事件';



-- 创建直播、直播详情
CREATE TABLE `live_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `webcast_id` varchar(255) NOT NULL DEFAULT '' COMMENT '直播创建返回id',
  `stream_id` varchar(255) NOT NULL DEFAULT '' COMMENT '直播码',
  `room_id` varchar(255) NOT NULL DEFAULT '' COMMENT '直播房间ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '直播标题',
  `sub_title` varchar(255) NOT NULL DEFAULT '' COMMENT '直播副标题',
  `description` text COMMENT '直播描述',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '直播类型',
  `start_at` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_at` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面',
  `status` tinyint(4) NOT NULL DEFAULT '101' COMMENT '101创建 201正在直播 301直播完成 401直播关闭',
  `organizer_join_url` varchar(255) NOT NULL DEFAULT '' COMMENT '组织者加入 URL',
  `panelist_join_url` varchar(255) NOT NULL DEFAULT '' COMMENT '嘉宾加入URL',
  `assistant_join_url` varchar(255) NOT NULL DEFAULT '' COMMENT '助理加入URL',
  `panelist_token` int(11) NOT NULL DEFAULT '0' COMMENT '嘉宾口令',
  `assistant_token` int(11) NOT NULL DEFAULT '0' COMMENT '嘉宾参加者口令',
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `deleted_at` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_webcast_id` (`webcast_id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播index';

-- 直播房间
CREATE TABLE `live_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `live_id` int(11) NOT NULL COMMENT '直播id',
  `file_id` varchar(64) NOT NULL DEFAULT '' COMMENT '课件id',
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `deleted_at` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_live_id` (`live_id`),
  KEY `idx_file_id` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- 直播房间
CREATE TABLE `live_room` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `client_user` int(11) NOT NULL COMMENT '用户id',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '直播间名称',
  `stream_id` varchar(32) NOT NULL DEFAULT '' COMMENT '直播码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- 聊天
CREATE TABLE `live_chat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';


-- 问答
CREATE TABLE `live_qa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';


-- 课件PPT
CREATE TABLE `live_courseware` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';


-- 直播间访问记录
CREATE TABLE `live_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';


-- 直播数据统计
CREATE TABLE `live_statistics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';


-- 文件管理表
CREATE TABLE `files_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '上传用户ID',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名 hash(原始文件名+时间戳)',
  `original_name` varchar(255) DEFAULT NULL COMMENT '原始文件名',
  `store_name` varchar(255) DEFAULT NULL COMMENT '上传后真实资源文件名',
  `path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `extension` varchar(255) DEFAULT NULL COMMENT '文件扩展名',
  `size` bigint(20) DEFAULT '0' COMMENT '文件大小',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '文件类型 1图片 2 ppt 3 pdf 4 其他',
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `deleted_at` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_name` (`name`),
  KEY `idx_store_name` (`store_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件管理表';



-- PPT文件转换图片关系表
CREATE TABLE `files_doc_img` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '文件ID',
  `page_number` tinyint(4) NOT NULL DEFAULT '0' COMMENT '页码',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '图片路径',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='PPT文件转换图片关系';



-- 直播聊天室 腾讯云通信事件index
-- CREATE TABLE `im_event_index` (
--   `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
--   `command` varchar(32) NOT NULL DEFAULT '' COMMENT '回调类型',
--   `client_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '客户端IP',
--   `platform` varchar(16) NOT NULL DEFAULT '0' COMMENT '设备类型，分为Windows，Web，Android，iOS，Mac和Unknown',
--   `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
--   PRIMARY KEY (`id`),
--   KEY `idx_stream_id` (`stream_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播事件消息主表';



-- 直播聊天室 腾讯云通信消息事件
CREATE TABLE `im_event_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `room_id` varchar(255) NOT NULL DEFAULT '' COMMENT '直播房间ID',
  `event_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '事件类型 1聊天室发言之前 2聊天室发言之后',
  `from_account` varchar(32) NOT NULL DEFAULT '' COMMENT '发送者',
  `operator_account` varchar(32) NOT NULL DEFAULT '' COMMENT '请求的发起者 可以用来识别是否为管理员请求的',
  `msg_type` varchar(16) NOT NULL DEFAULT '' COMMENT '消息类型 目前用到文本、表情、自定义消息',
  `msg_content` varchar(255) NOT NULL DEFAULT '' COMMENT '消息内容',
  `random` varchar(32) NOT NULL DEFAULT '' COMMENT '发消息请求中的32位随机数',
  `client_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '客户端IP',
  `platform` varchar(16) NOT NULL DEFAULT '0' COMMENT '设备类型，分为Windows，Web，Android，iOS，Mac和Unknown',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云通信消息事件';

-- 目前支持消息类别MsgType见下表

-- MsgType的值 类型
-- TIMTextElem 文本消息。
-- TIMLocationElem 地理位置消息。
-- TIMFaceElem 表情消息。
-- TIMCustomElem 自定义消息，当接收方为IOS系统且应用处在后台时，此消息类型可携带除文本以外的字段到APNS。注意，一条组合消息中只能包含一个TIMCustomElem自定义消息元素。
-- TIMSoundElem  语音消息。（服务端集成Rest API不支持发送该类消息）
-- TIMImageElem  图像消息。（服务端集成Rest API不支持发送该类消息）
-- TIMFileElem 文件消息。（服务端集成Rest API不支持发送该类消息）



-- 直播聊天室 状态变更回调事件
CREATE TABLE `im_event_state` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `room_id` varchar(255) NOT NULL DEFAULT '' COMMENT '直播房间ID',
  `event_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '事件类型 1新人员加入聊天室 2人员离开聊天室',
  `new_account` varchar(32) NOT NULL DEFAULT '' COMMENT '新加入成员',
  `client_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '客户端IP',
  `platform` varchar(16) NOT NULL DEFAULT '0' COMMENT '设备类型，分为Windows，Web，Android，iOS，Mac和Unknown',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播聊天室状态变更回调事件';


-- 多路视频直播 腾讯云流ID 直播码
CREATE TABLE `live_branch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `live_id` int(11) NOT NULL COMMENT '直播id',
  `stream_id` varchar(64) NOT NULL DEFAULT '' COMMENT '直播码',
  `session_id` varchar(64) NOT NULL DEFAULT '' COMMENT 'session_id',
  `last_login_at` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录的时间',
  PRIMARY KEY (`id`),
  KEY `idx_live_id` (`live_id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支路直播码';


CREATE TABLE `variable` (
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` text NOT NULL COMMENT '变量值',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='保存key/value键值对';

