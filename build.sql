CREATE TABLE `client_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态:0锁定,1激活',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户类型',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';



CREATE TABLE `tencent_event_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `stream_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码',
  `channel_id` varchar(128) NOT NULL DEFAULT '' COMMENT '直播码 同stream_id',
  `event_type` int(11) NOT NULL DEFAULT '0' COMMENT '事件类型 0 — 代表断流，1 — 代表推流，100 — 新的录制文件已生成，200 — 新的截图文件已生成',
  `event_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息产生的时间',
  PRIMARY KEY (`id`),
  KEY `idx_stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='腾讯云直播事件消息主表';



CREATE TABLE `tencent_event_push` (
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


CREATE TABLE `tencent_event_record` (
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


CREATE TABLE `tencent_event_screenshot` (
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
