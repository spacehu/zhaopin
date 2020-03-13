<?php
$data = \action\show::$data['data'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
            <p>简历信息</p>
        </div>
        <div class="content">
            <div class="pathA ">
                <div class="leftA">
                    <div class="leftAlist" >
                        <span>用户名</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['name']) ? $data['name'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>性别</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['sex']) ? $data['sex'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>学历</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['education']) ? $data['education'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>工龄</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['age_work']) ? $data['age_work'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>电话</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['phone']) ? $data['phone'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>邮件</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['email']) ? $data['email'] : ""; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>自我介绍</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['about_self']) ? $data['about_self'] : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="status r_top">
            <p>教育经历</p>
        </div>
        <div class="content">
            <div class="pathA ">
                <?php if (!empty($data['school'])) { ?>
                    <?php foreach ($data['school'] as $k => $v) { ?>
                        <div class="leftA">
                            <div class="leftAlist" >
                                <span>学校</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['school']) ? $v['school'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>专业</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['profession']) ? $v['profession'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>时间</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['start_time']) ? $v['start_time'] : ''; ?> - <?php echo isset($v['end_time']) ? $v['end_time'] : ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="status r_top">
            <p>工作经历</p>
        </div>
        <div class="content">
            <div class="pathA ">
                <?php if (!empty($data['company'])) { ?>
                    <?php foreach ($data['company'] as $k => $v) { ?>
                        <div class="leftA">
                            <div class="leftAlist" >
                                <span>公司</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['company']) ? $v['company'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>职位</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['job']) ? $v['job'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>时间</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['start_time']) ? $v['start_time'] : ''; ?> - <?php echo isset($v['end_time']) ? $v['end_time'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>工作内容</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['infomation']) ? $v['infomation'] : ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="status r_top">
            <p>项目经历</p>
        </div>
        <div class="content">
            <div class="pathA ">
                <?php if (!empty($data['project'])) { ?>
                    <?php foreach ($data['project'] as $k => $v) { ?>
                        <div class="leftA">
                            <div class="leftAlist" >
                                <span>项目名称</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['name']) ? $v['name'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>简介</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['overview']) ? $v['overview'] : ''; ?>
                                </div>
                            </div>
                            <div class="leftAlist" >
                                <span>时间</span>
                            </div>
                            <div class="leftAlist" >
                                <div class="r_row">
                                    <?php echo isset($v['start_time']) ? $v['start_time'] : ''; ?> - <?php echo isset($v['end_time']) ? $v['end_time'] : ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </body>
</html>