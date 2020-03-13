<?php
$data = \action\lesson::$data['data'];
$class = \action\lesson::$data['class'];
$list = \action\lesson::$data['list'];
$image = \action\lesson::$data['image'];
$media = \action\lesson::$data['media'];
$lesson_image = \action\lesson::$data['lesson_image'];
$course_id = \action\lesson::$data['course_id'];
$config = \action\lesson::$data['config'];
if (is_array($image)) {
    foreach ($media as $k => $v) {
        if ($data['media_id'] == $v['id']) {
            $original_src = $v['src'];
            $type = $v['type'];
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
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateLesson&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 课时名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        </div>
                        <div class="leftAlist" >
                            <span>OVERVIEW 课时简述</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="overview" type="text" value="<?php echo isset($data['overview']) ? $data['overview'] : ""; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>DETAIL 课时内容</span>
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
                            <span>TYPE 课类型</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="type" class="select_type">
                                <option value="music" <?php echo $data['type'] == "music" ? 'selected' : ''; ?>>音频</option>
                                <option value="video" <?php echo $data['type'] == "video" ? 'selected' : ''; ?>>视频</option>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>MEDIA 媒体资料</span> <a href="javascript:void(0);" class="remove_media">DELETE</a>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <INPUT TYPE="file" NAME="file_url" id="f1" />
                                <input type="hidden" name="edit_doc" id="edit_doc" value="<?php echo isset($original_src) ? $original_src : ''; ?>" />
                            </div>
                            <div class="r_row music <?php echo $data['type'] == "music" ? "" : "hide"; ?>">
                                <audio class="r_row_img" id="r_row_media" src="<?php echo isset($original_src) ? $original_src : ''; ?>" controls >
                                </audio>
                            </div>
                            <div class="r_row video <?php echo $data['type'] == "video" ? "" : "hide"; ?>">
                                <video class="r_row_img" id="r_row_media" src="<?php echo isset($original_src) ? $original_src : ''; ?>" controls >
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="leftAlist" >
                        <span>IMAGES 详细多图</span>&nbsp;<a href="javascript:void(0);" class="add_image">+</a>
                    </div>
                    <div class="leftAlist list_image" >
                        <?php if (!empty($lesson_image)) { ?>
                            <?php foreach ($lesson_image as $lk => $lv) { ?>
                                <?php if (is_array($image)) { ?>
                                    <?php foreach ($image as $k => $v) { ?>
                                        <?php if ($lv['image_id'] == $v['id']) { ?>
                                            <div class="leftAlist" >
                                                <div class="r_row">
                                                    <INPUT TYPE="file" NAME="file_url" id="f2" class="f2" />
                                                    <input type="hidden" name="lesson_image[]" class="edit_doc" value="<?php echo isset($v['original_src']) ? $v['original_src'] : './img/no_img.jpg'; ?>" />
                                                </div>
                                                <div class="r_row">
                                                    <div class="r_title"><a href="javascript:void(0);" class="remove_image">DELETE</a></div>
                                                    <img class="r_row_img" src="<?php echo isset($v['original_src']) ? $v['original_src'] : './img/no_img.jpg'; ?>" />
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
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
            <div class="leftAlist" >
                <div class="r_row">
                    <INPUT TYPE="file" NAME="file_url" id="f2" class="f2" />
                    <input type="hidden" name="lesson_image[]" class="edit_doc" value="./img/no_img.jpg" />
                </div>
                <div class="r_row">
                    <div class="r_title">&nbsp;</div>
                    <img class="r_row_img" src="./img/no_img.jpg" />
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $(".remove_media").live('click',function(){
                    $("#edit_doc").attr("value",0);
                    $("#r_row_media").attr("src", "");
                });
                $(".add_image").click(function () {
                    $(".mod_image").children().clone().appendTo('.list_image');
                });
                $(".remove_image").live('click', function () {
                    $(this).parent().parent().parent().remove();
                });
                $(".select_type").on("change", function () {
                    if ($(this).val() === "music") {
                        $(".music").show();
                        $(".video").hide();
                    }
                    if ($(this).val() === "video") {
                        $(".video").show();
                        $(".music").hide();
                    }
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
                    var _file = config.mediaPath + "/" + (new Date()).valueOf() + "." + fileExtension;
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
                        $("#f1").attr("value", "");
                    });
                });
                $(".f2").live("change", function () {
                    var file = this.files[0];
                    var obj = $(this);
                    if (!file)
                        return;
                    var fileExtension = file.name.split('.').pop();
                    var _file = config.imagePath + "/" +  (new Date()).valueOf() + "." + fileExtension;
                    cos.putObject({
                        Bucket: config.Bucket, /* 必须 */
                        Region: config.Region, /* 必须 */
                        //Key:  file.name,              /* 必须 */
                        Key: _file,
                        Body: file
                    }, function (err, data) {
                        console.log(err || data);
                        obj.parent().next().find(".r_row_img").attr("src", config.url + _file);
                        obj.next().attr("value", config.url + _file);
                        $(".f2").attr("value", "");
                    });
                });
            });
            var ue = UE.getEditor('container');
        </script>
    </body>
</html>