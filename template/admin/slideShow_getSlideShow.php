<?php
$data = \action\slideShow::$data['data'];
$image = \action\slideShow::$data['image'];
$config = \action\slideShow::$data['config'];
if (is_array($image)) {
    foreach ($image as $k => $v) {
        if ($data['image_id'] == $v['id']) {
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
        <!-- 图片控件 -->
        <script src="lib/cos-js-sdk-v5-master/dist/cos-js-sdk-v5.js"></script>
        <script type="text/javascript" src="js/tencent_cos.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=slideShow&m=updateSlideShow&id=<?php echo isset($data['id']) ? $data['id'] : ''; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>SRC 素材</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <INPUT TYPE="file" NAME="file_url" id="f1" />
                                <input type="hidden" name="edit_doc" id="edit_doc" value="<?php echo isset($original_src) ? $original_src : './img/no_img.jpg'; ?>" />
                            </div>
                            <div class="r_row">
                                <div class="r_title">&nbsp;</div>
                                <img class="r_row_img" src="<?php echo isset($original_src) ? $original_src : './img/no_img.jpg'; ?>" />
                            </div
                        </div>
                        <div class="leftAlist" >
                            <span>LINK 链接</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="link" type="text" value="<?php echo isset($data['link']) ? $data['link'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>ORDER BY 当前排序</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="order_by" type="text" value="<?php echo isset($data['order_by']) ? $data['order_by'] : 50; ?>" />
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
        <script type="text/javascript">
            $(function () {
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
        </script>
    </body>
</html>