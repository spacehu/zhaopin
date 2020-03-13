<?php
$data = \action\course::$data['data'];
$class = \action\course::$data['class'];
$enterprise_id = \action\course::$data['enterprise_id'];
$cat_id = \action\course::$data['cat_id'];
$image = \action\course::$data['image'];
$enterprise = \action\course::$data['enterprise'];
$enterprise_course = \action\course::$data['enterprise_course'];
$config = \action\course::$data['config'];
if (is_array($image)) {
    foreach ($image as $k => $v) {
        if ($data['media_id'] == $v['id']) {
            $original_src = $v['original_src'];
        }
    }
}
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
        <!-- 图片控件 -->
        <script src="lib/cos-js-sdk-v5-master/dist/cos-js-sdk-v5.js"></script>
        <script type="text/javascript" src="js/tencent_cos.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateCourse&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 课程名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                            <input class="text" name="cat_id" type="hidden" value="<?php echo $cat_id; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>OVERVIEW 课程简述</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="overview" type="text" value="<?php echo isset($data['overview']) ? $data['overview'] : ""; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>DETAIL 课程内容</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container" name="detail" type="text/plain">
<?php echo isset($data['detail']) ? $data['detail'] : ""; ?>
                            </script>
                        </div>
                        <div class="leftAlist" >
                            <span>ORDERBY 当前排序</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="order_by" type="text" value="<?php echo isset($data['order_by']) ? $data['order_by'] : 50; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>IMAGE 封面</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <INPUT TYPE="file" NAME="file_url" id="f1" />
                                <input type="hidden" name="edit_doc" id="edit_doc" value="<?php echo isset($original_src) ? $original_src : './img/no_img.jpg'; ?>" />
                            </div>
                            <div class="r_row">
                                <div class="r_title">&nbsp;</div>
                                <img class="r_row_img" src="<?php echo isset($original_src) ? $original_src : './img/no_img.jpg'; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>TESTMAX 题数</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="text_max" type="text" value="<?php echo isset($data['text_max']) ? $data['text_max'] : 5; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>PERCENTAGE 及格线</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="percentage" type="text" value="<?php echo isset($data['percentage']) ? $data['percentage'] : 60; ?>" />
                        </div>
                        <div class="leftAlist <?php echo!empty($enterprise_id) ? 'hide' : ''; ?>" >
                            <span>IS ENTERPRISE 隶属企业</span>&nbsp;<a href="javascript:void(0);" class="add_image">+</a>
                        </div>
                        <div class="leftAlist <?php echo!empty($enterprise_id) ? 'hide' : ''; ?> list_image" >
                            <?php if (!empty($enterprise_course)) { ?>
                                <?php foreach ($enterprise_course as $lk => $lv) { ?>
                                    <select name="enterprise_id[]">
                                        <option value="0">请选择</option>
                                        <?php if (is_array($enterprise)) { ?>
                                            <?php foreach ($enterprise as $k => $v) { ?>
                                                <option value="<?php echo $v['id']; ?>"  <?php echo $lv['enterprise_id'] == $v['id'] ? 'selected' : ''; ?>><?php echo $v['name']; ?></option>
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
        </div>
        <div class="leftAlist hide mod_image">
            <select name="enterprise_id[]">
                <option value="0">请选择</option>
                <?php if (is_array($enterprise)) { ?>
                    <?php foreach ($enterprise as $k => $v) { ?>
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

                var config = {
                    Bucket: "<?php echo $config['lib']['tencent']['cos']['bucket']; ?>",
                    Region: "<?php echo $config['lib']['tencent']['cos']['region']; ?>",
                    imagePath: "<?php echo $config['path']['image']; ?>",
                    mediaPath: "<?php echo $config['path']['media']; ?>",
                    filename: "<?php echo time(); ?>",
                    url: "<?php echo $config['lib']['tencent']['cos']['url']; ?>"
                };
                // 监听选文件
                $("#f1").on("change", function () {
                    var file = this.files[0];
                    if (!file)
                        return;
                    var fileExtension = file.name.split('.').pop();
                    var _file = config.mediaPath + "/" + config.filename + "." + fileExtension;
                    cos.putObject({
                        Bucket: config.Bucket, /* 必须 */
                        Region: config.Region, /* 必须 */
                        //Key:  file.name,              /* 必须 */
                        Key: _file,
                        Body: file
                    }, function (err, data) {
                        console.log(err || data);
                        $(".r_row_img").attr("src", config.url + _file);
                        $("#edit_doc").attr("value", config.url + _file);
                    });
                });
            });
            var ue = UE.getEditor('container');
        </script>
    </body>
</html>