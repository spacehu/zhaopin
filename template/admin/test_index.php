<?php
$data = \action\test::$data['data'];
$Total = \action\test::$data['total'];
$currentPage = \action\test::$data['currentPage'];
$pagesize = \action\test::$data['pagesize'];
$keywords = \action\test::$data['keywords'];
$class = \action\test::$data['class'];
$lesson_id = \action\test::$data['lesson_id'];
$course_id = \action\test::$data['course_id'];
$category = \action\test::$data['category'];
$categorys = \action\test::$data['categorys'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js" ></script>
        <title>无标题文档</title>
        <script>
            $(function () {
                $('.list_select').on("change", function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=index&cat_id=' + $('.list_select').val();
                });
                $('.button_find').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=index&lesson_id=<?php echo $lesson_id;?>&keywords=' + $('.keywords').val();
                });
            });
        </script>
    </head>

    <body>

        <div class="menu">
            <input type="text" name="keywords" class="keywords" value="<?php echo isset($keywords) ? $keywords : ""; ?>" placeholder="请输入关键字" />
            <a class="button_find " href="javascript:void(0);">查找</a>
            <?php if (!empty($categorys)) { ?>
                <select class="listSelect list_select" >
                    <option value="">请选择</option>
                    <?php foreach ($categorys as $k => $v) { ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == $category ? "selected" : ""; ?>><?php echo $v['name']; ?></option>
                    <?php } ?>              
                </select>
            <?php } ?>
            <a href="javascript:void(0);" class="updateButton"  onclick="javascript:parent.mainFrame.location.href = 'index.php?a=<?php echo $class; ?>&m=getTest&lesson_id=<?php echo $lesson_id; ?>'">添加新试题</a>
            <?php if(!empty($lesson_id)){?>
            <a href="index.php?a=lesson&m=index&course_id=<?php echo $course_id;?>&cat_id=<?php echo $category;?>" class="backButton">返回上一级</a>
            <?php }?>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1">名称</td>
                    <td class="td1" width="20%">企业</td>
                    <td class="td1" width="10%">状态</td>
                    <td class="td1" width="20%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1"><?php echo $v['eName']; ?></td>
                            <td class="td1"><?php
                                if ($v['delete'] == 0) {
                                    echo '使用中';
                                } else {
                                    echo '已删除';
                                }
                                ?></td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class; ?>&m=getTest&lesson_id=<?php echo $lesson_id; ?>&id=<?php echo $v['id']; ?>">编辑</a>
                                | <a href="index.php?a=<?php echo $class; ?>&m=deleteTest&id=<?php echo $v['id']; ?>" onclick="return confirm('确定将此试题删除?')">删除</a></td>
                        </tr>
                        <?php
                        $sum_i++;
                    }
                }
                ?>
            </table>
            <div class="num_bar">
                总数<b><?php echo $Total; ?></b>
            </div>
            <?php
            $url = 'index.php?a=' . $class . '&m=index&cat_id='.$category.'&lesson_id=' . $lesson_id . '&keywords=' . $keywords;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
