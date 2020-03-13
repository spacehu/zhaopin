<?php
$class = \action\show::$data['class'];
$data = \action\show::$data['data'];
$list = \action\show::$data['enumList'];
$enterprise_id = \action\show::$data['enterprise_id'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <!-- 配置文件 -->
        <script type="text/javascript" src="lib/uEditor/ueditor.config.js"></script>
        <!-- 编辑器源码文件 -->
        <script type="text/javascript" src="lib/uEditor/ueditor.all.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateShow&id=<?php echo $data['id']; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 标题</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" />
                                <input class="text" name="cat_id" type="hidden" value="<?php echo isset($cat_id) ? $cat_id : '0'; ?>" />
                            </div>
                        </div>
                        <?php if (!empty($enterprise_id)) { ?>
                            <input type="hidden" name="enterprise_id" value="<?php echo $enterprise_id; ?>" />
                        <?php } else { ?>
                            <input type="hidden" name="enterprise_id" value="" />
                        <?php } ?>
                    </div>
                    <div class="leftA c_17 ">
                        <div class="leftAlist" >
                            <span>行业</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="type" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[1])) { ?>
                                    <?php foreach ($list[1]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['type'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>薪资</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="salary" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[3])) { ?>
                                    <?php foreach ($list[3]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['salary'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>工作经验</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="age_min" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[2])) { ?>
                                    <?php foreach ($list[2]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['age_min'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>省</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="province" type="text" value="<?php echo isset($data['province']) ? $data['province'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>市</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="city" type="text" value="<?php echo isset($data['city']) ? $data['city'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>区</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="district" type="text" value="<?php echo isset($data['district']) ? $data['district'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>岗位职责</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container2" name="responsibilities" type="text/plain">
<?php echo isset($data['responsibilities']) ? $data['responsibilities'] : ""; ?>
                            </script>
                        </div>
                        <div class="leftAlist" >
                            <span>任职资格</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container3" name="qualifications" type="text/plain">
<?php echo isset($data['qualifications']) ? $data['qualifications'] : ""; ?>
                            </script>
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
        <script type="text/javascript">
            var ue = UE.getEditor('container2');
            var ue = UE.getEditor('container3');
        </script>
    </body>
</html>