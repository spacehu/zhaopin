<?php
$data = \action\material::$data['data'];
$type = \action\material::$data['type'];
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
            <form name="theForm" id="demo" action="./index.php?a=material&m=updateMedia&id=<?php echo $data['id']; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 素材名</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" />
                                <input class="text" name="type" type="hidden" value="<?php echo $type; ?>" />
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>SRC 路径</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <INPUT TYPE="file" NAME="file_url" id="f1" onchange="document.getElementById('edit_doc').value = 1" />
                                <input type="hidden" name="edit_doc" id="edit_doc" value="0" />
                            </div>
                            <div class="r_row">
                                <div class="r_title">&nbsp;</div>
                                <?php if ($type == "music") { ?>
                                    <audio class="r_row_img" src=".<?php echo isset($data['src']) ? $data['src'] : ''; ?>" controls >
                                    </audio>
                                <?php } else if ($type == "video") { ?>
                                    <video class="r_row_img" src=".<?php echo isset($data['src']) ? $data['src'] : ''; ?>" controls >
                                    </video>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="leftAlist" >
                            <span>DURATION 时长</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="r_row">
                                <input class="text" name="duration" type="text" value="<?php echo isset($data['duration']) ? $data['duration'] : ''; ?>" />
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