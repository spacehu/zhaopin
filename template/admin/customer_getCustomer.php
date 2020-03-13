<?php
$data = \action\customer::$data['data'];
$class = \action\customer::$data['class'];
$userCourse = \action\customer::$data['userCourse'];
$course = \action\customer::$data['course'];
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
            <p>用户信息</p>
        </div>
        <div class="content">
            <div class="pathA ">
                <div class="leftA">
                    <div class="leftAlist" >
                        <span>NAME 用户名</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['name']) ? $data['name'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>PHONE 手机</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['phone']) ? $data['phone'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>NICKNAME 昵称</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['nickname']) ? $data['nickname'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>PHOTO 头像</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <img src="<?php echo isset($data['photo']) ? $data['photo'] : ''; ?>" />
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>BRITHDAT 生日</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['brithday']) ? $data['brithday'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>CITY 城市</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['city']) ? $data['city'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>PROVINCE 省</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['province']) ? $data['province'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>DISTRICT 区域</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['district']) ? $data['district'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>EMAIL 电子邮件</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['email']) ? $data['email'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>SEX 性别</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['sex']) ? $data['sex'] : ''; ?>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>REGISTER TIME 注册时间</span>
                    </div>
                    <div class="leftAlist" >
                        <div class="r_row">
                            <?php echo isset($data['add_time']) ? $data['add_time'] : "****-**-** **:**:**"; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateCustomer&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>COURSE 企业课程</span>&nbsp;<a href="javascript:void(0);" class="add_image">+</a>
                        </div>
                        <div class="leftAlist list_image" >
            <?php if (!empty($userCourse)) { ?>
                <?php foreach ($userCourse as $lk => $lv) { ?>
                                            <select name="user_course_ids[]">
                                                <option value="0">请选择</option>
                    <?php if (is_array($course)) { ?>
                        <?php foreach ($course as $k => $v) { ?>
                                                                <option value="<?php echo $v['id']; ?>"  <?php echo $lv['course_id'] == $v['id'] ? 'selected' : ''; ?>><?php echo $v['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                                            </select>
                <?php } ?>
            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="pathB">
                    <div class="leftA">
                        <input name="" type="submit" id="submit" value="SUBMIT 提交" />
                    </div>
                </div>
            </form>	
            -->
        </div>
        <div class="leftAlist hide mod_image">
            <select name="user_course_ids[]">
                <option value="0">请选择</option>
                <?php if (is_array($course)) { ?>
                    <?php foreach ($course as $k => $v) { ?>
                        <option value="<?php echo $v['id']; ?>" ><?php echo $v['name']; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
        <script type="text/javascript">
            $(function () {
                $(".add_image").click(function () {
                    $(".mod_image").children().clone().appendTo('.list_image');
                });
            });
        </script>
    </body>
</html>