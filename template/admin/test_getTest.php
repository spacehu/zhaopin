<?php
$data = \action\test::$data['data'];
$class = \action\test::$data['class'];
$list = \action\test::$data['list'];
$select = \action\test::$data['select'];
$option = \action\test::$data['option'];
$lesson_id = \action\test::$data['lesson_id'];
$categorys = \action\test::$data['categorys'];
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
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateTest&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 试题名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                        </div>
                        <?php if (is_array($categorys)) { ?>
                            <div class="leftAlist" >
                                <span>CATEGORY 分类</span>
                            </div>
                            <div class="leftAlist" >
                                <select name="cat_id">
                                    <option value="0">请选择</option>
                                    <?php foreach ($categorys as $k => $v) { ?>
                                        <option value="<?php echo $v['id']; ?>"  <?php echo $data['cat_id'] == $v['id'] ? 'selected' : ''; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <div class="leftAlist" >
                            <span>DETAIL 试题内容</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container" name="detail" type="text/plain">
<?php echo isset($data['detail']) ? $data['detail'] : ""; ?>
                            </script>
                        </div>
                        <div class="leftAlist" >
                            <span>TYPE 类型</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="type" class="select_type">
                                <option value="text" <?php echo $data['type'] == "text" ? 'selected' : ''; ?>>文字</option>
                                <option value="select" <?php echo $data['type'] == "select" ? 'selected' : ''; ?>>单选</option>
                                <option value="selects" <?php echo $data['type'] == "selects" ? 'selected' : ''; ?>>多选</option>
                            </select>
                        </div>
                        <div class="leftAlist <?php echo $data['type'] == "text" ? 'hide' : ''; ?> list_type" >
                            <span>OPTION 选项</span>&nbsp;<a href="javascript:void(0);" class="add_image">+</a>
                        </div>
                        <div class="leftAlist <?php echo $data['type'] == "text" ? 'hide' : ''; ?> list_type list_image" >
                            <?php if (is_array($option)) { ?>
                                <?php foreach ($option as $k => $v) { ?>
                                    <div class="option">
                                        <span><?php echo $k; ?>: </span><input class="text" name="overview[]" type="text" value="<?php echo $v; ?>" />
                                        <a href="javascript:void(0);" class="option_remove">delete</a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="leftAlist" >
                            <span>SERIALIZATION 答案（序列化）</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="serialization" type="text" value="<?php echo isset($data['serialization']) ? $data['serialization'] : ""; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>ORDERBY 当前排序</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="order_by" type="text" value="<?php echo isset($data['order_by']) ? $data['order_by'] : 50; ?>" />
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
        <div class="leftAlist hide mod_image">
            <div class="option">
                <span></span><input class="text" name="overview[]" type="text" value="" />
                <a href="javascript:void(0);" class="option_remove">delete</a>
            </div>
        </div>
        <script type="text/javascript">
            var key = <?php echo json_encode($select); ?>;
            $(function () {
                $(".add_image").on("click", function () {
                    //console.log(key[$(".list_image > .option").size()]);
                    //$(".mod_image > .option > span").html(key[$(".list_image > .option").size()] + ": ");
                    $(".mod_image").children().clone().appendTo('.list_image');
                    resetKey();
                });
                $(".option_remove").live("click",function (){
                    $(this).parent().remove();
                    resetKey();
                });
                $(".select_type").change(function () {
                    if ($(this).val() === "text") {
                        $(".list_type").addClass("hide");
                    } else {
                        $(".list_type").removeClass("hide");
                    }
                });
            });
            var resetKey=function (){
                $(".list_image > .option > span").each(function(i){
                    $(this).html(key[i]+": ");
                });
            }
            var ue = UE.getEditor('container');
        </script>
    </body>
</html>