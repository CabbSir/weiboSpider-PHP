CREATE TABLE `tb_config`
(
    `userinfo_prefix` varchar(10) CHARACTER SET utf8mb4  NOT NULL DEFAULT '' COMMENT '用户信息链接containerid前缀',
    `context_prefix`  varchar(10) CHARACTER SET utf8mb4  NOT NULL DEFAULT '' COMMENT '微博信息链接containerid前缀',
    `pic_prefix`      varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '微博原图前缀'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `tb_proxy`
(
    `address`   varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT 'ip地址',
    `cdatetime` datetime                           NOT NULL COMMENT '入库时间',
    `mdatetime` datetime                           NOT NULL COMMENT '上次检测时间',
    `status`    tinyint(1)                         NOT NULL DEFAULT '1' COMMENT '状态',
    `priority`  int(11)                            NOT NULL DEFAULT '0' COMMENT '优先级，越小越快'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `tb_user`
(
    `id`             int(11) unsigned                    NOT NULL AUTO_INCREMENT,
    `wb_id`          varchar(30) CHARACTER SET utf8mb4   NOT NULL DEFAULT '' COMMENT '微博平台id',
    `follow_count`   int(11)                             NOT NULL COMMENT '关注数',
    `fans_count`     int(11)                             NOT NULL COMMENT '粉丝数',
    `weibo_count`    int(11)                             NOT NULL COMMENT '已发微博条数',
    `avatar_url`     varchar(255) CHARACTER SET utf8mb4  NOT NULL DEFAULT '' COMMENT '头像地址',
    `nickname`       varchar(255) CHARACTER SET utf8mb4  NOT NULL DEFAULT '' COMMENT '昵称',
    `description`    varchar(1000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '签名档',
    `cdatetime`      datetime                            NOT NULL COMMENT '创建时间',
    `mdatetime`      datetime                            NOT NULL COMMENT '上次更新时间',
    `status`         tinyint(1)                          NOT NULL DEFAULT '1' COMMENT '默认启用',
    `history_status` tinyint(1)                          NOT NULL DEFAULT '2' COMMENT '是否已经采集历史数据',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `tb_user_config`
(
    `id`    int(11) unsigned                    NOT NULL AUTO_INCREMENT,
    `wb_id` varchar(30) CHARACTER SET utf8mb4   NOT NULL DEFAULT '' COMMENT '用户的微博id',
    `email` varchar(1000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '需要发送的邮箱1',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `tb_weibo_content`
(
    `id`             int(11) unsigned                  NOT NULL AUTO_INCREMENT,
    `mid`            varchar(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '此条微博id',
    `user_id`        varchar(30) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '关联的userid',
    `content`        text CHARACTER SET utf8mb4        NOT NULL COMMENT '微博内容',
    `source`         varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '来源iPhone....',
    `retweet_count`  int(11)                           NOT NULL DEFAULT '0' COMMENT '转发数量',
    `comment_count`  int(11)                           NOT NULL DEFAULT '0' COMMENT '评论数量',
    `like_count`     int(11)                           NOT NULL DEFAULT '0' COMMENT '点赞数量',
    `pic_num`        int(11)                           NOT NULL COMMENT '微博包含图片数量',
    `pics`           text CHARACTER SET utf8mb4 COMMENT '图片地址',
    `media`          text CHARACTER SET utf8mb4 COMMENT '媒体地址',
    `media_category` tinyint(1)                        NOT NULL DEFAULT '1' COMMENT '媒体类型1-live 2-video 3-story',
    `cdatetime`      datetime                          NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `tb_config` (`userinfo_prefix`, `context_prefix`, `pic_prefix`) VALUES ('100505', '107603', '');