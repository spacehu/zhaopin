<?php
$data = \action\enterprise::$data['data'];
$class = \action\enterprise::$data['class'];
$list = \action\enterprise::$data['list'];
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
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateEnterprise&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 企业名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>CODE 社会识别号</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="code" type="text" value="<?php echo isset($data['code']) ? $data['code'] : ""; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>USERNAME 联系人</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="username" type="text" value="<?php echo isset($data['username']) ? $data['username'] : ""; ?>" /><span class="red"> * </span>
                        </div>
                        <div class="leftAlist" >
                            <span>PHONE 联系人电话</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="phone" type="text" value="<?php echo isset($data['phone']) ? $data['phone'] : ""; ?>" /><span class="red"> * </span>
                        </div>
                        <div class="leftAlist" >
                            <span>ADDRESS 企业地址</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="address" type="text" value="<?php echo isset($data['address']) ? $data['address'] : ""; ?>" /><span class="red"> * </span>
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