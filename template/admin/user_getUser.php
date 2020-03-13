<?php
$data = \action\user::$data['data'];
$list = \action\user::$data['list'];
$enterprise = \action\user::$data['enterprise'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <!-- 日历插件 -->
        <link href="./css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
        <script type="text/javascript" src="./js/bootstrap.min.js"></script>
        <script type="text/javascript" src="./js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script type="text/javascript" src="./js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
        <!-- 日历插件 end -->
        <script>
            $(function () {
                $('#start_date').datetimepicker({
                    language: 'zh-CN',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0,
                    format: 'yyyy-mm-dd'
                });
                $('#just_date').datetimepicker({
                    language: 'zh-CN',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0,
                    format: 'yyyy-mm-dd'
                });
            });
        </script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=user&m=updateUser&id=<?php echo $data['id']; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 用户名</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <?php echo isset($data['name']) ? $data['name'] : '<input class="text" name="name" type="text" value="" />'; ?>
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>PASSWORD 密码</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <input class="text" name="password" type="password" value="" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>EMAIL 电子邮箱地址</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <input class="text" name="email" type="text" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span><input type="checkbox" name="is_email" <?php echo ($data['is_email'] == 0) ? '' : 'checked'; ?>>是否发送学习报告</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <textarea name="mail_content" ><?php echo isset($data['mail_content']) ? $data['mail_content'] : ''; ?></textarea>
                                发送时间设置
                                开始时间：<input type="text" name="start_date" id="start_date" value="<?php echo isset($data['start_date']) ? $data['start_date'] : ''; ?>" readonly />
                                提醒间隔：<select name="times" class="times">
                                    <option value="0">每天</option>
                                    <option value="1">每周</option>
                                    <option value="2">特定时间</option>
                                </select>
                                <input type="text" name="just_date" id="just_date" value="<?php echo isset($data['just_date']) ? $data['just_date'] : ''; ?>" readonly />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>REGISTRATION TIME 注册时间</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <?php echo isset($data['add_time']) ? $data['add_time'] : "****-**-** **:**:**"; ?>
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>ROLE 角色</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <select name="role_id" >
                                    <option value="0" >无角色</option>
                                    <?php if (!empty($list) && is_array($list)) { ?>
                                        <?php foreach ($list as $k => $v) { ?>
                                            <option value="<?php echo $v['id']; ?>" <?php echo $data['role_id'] == $v['id'] ? "selected" : ""; ?> ><?php echo $v['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>ENTERPRISE 所属企业</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <select name="enterprise_id" >
                                    <option value="0" >暂无</option>
                                    <?php if (!empty($enterprise) && is_array($enterprise)) { ?>
                                        <?php foreach ($enterprise as $k => $v) { ?>
                                            <option value="<?php echo $v['id']; ?>" <?php echo $data['enterprise_id'] == $v['id'] ? "selected" : ""; ?> ><?php echo $v['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pathB">
                    <div class="leftA">
                        <input name="" type="submit" id="submit" value="SUBMIT 提交" />
                    </div>
                </div>
            </form>	
        </div>
    </body>
</html>