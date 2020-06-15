<?php 
$data = \action\admin::$data['data'];
$role = \action\admin::$data['role'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--
        <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic,700italic">
        -->
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript">
            $(function () {
                $(".first").click(function () {
                    //$(".first").removeClass('mainRed').next().hide();
                    //$(this).addClass('mainRed').next().show();
                    $(".first").removeClass('mainRed').next();
                    $(".second").removeClass('mainRed');
                    if ($(this).parent().find(".sub_title").length) {
                        var siblings_second = $(this).siblings(".sub_title").find(".title").eq(0).find(".second");
                        siblings_second.addClass('mainRed').next();
                    } else {
                        $(this).addClass('mainRed').next();
                    }
                });
                $(".second").click(function () {
                    $(".first").removeClass('mainRed');
                    $(".second").removeClass('mainRed');
                    $(this).addClass('mainRed');
                });
            });
        </script>
        <title>无标题文档</title>
    </head>
    <body>
        <div id="Menu-left">
            <!-- 企业管理员模块 -->
            <?php if(!empty($data)){?>
                <?php foreach($data as $k=>$v){?>
                    <?php if(in_array($k,$role)){?>
                        <div class="title">
                            <a class="first" <?php if(!empty($v['url'])){?>onclick="javascript:parent.mainFrame.location.href = '<?php echo $v['url'];?>'"<?php }?> href="javascript:void(0);" ><?php echo $v['title'];?></a>
                        </div>
                        <?php if(!empty($v['subMenu'])){?>
                            <div class="sub_title">
                                <?php foreach($v['subMenu'] as $key=>$val){?>
                                    <?php if(in_array($key,$role)){?>
                                        <div class="title">
                                            <a class="second" <?php if(!empty($val['url'])){?>onclick="javascript:parent.mainFrame.location.href = '<?php echo $val['url'];?>'"<?php }?> href="javascript:void(0);" ><?php echo $val['title'];?></a>
                                        </div>
                                    <?php }?>
                                <?php }?>
                            </div>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php }?>
            <!-- 企业管理员模块 end -->
        </div>
    </body>

</html>