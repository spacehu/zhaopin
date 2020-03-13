<?php
$data = \action\course::$data['data'];
$Total = \action\course::$data['total'];
$currentPage = \action\course::$data['currentPage'];
$pagesize = \action\course::$data['pagesize'];
$keywords = \action\course::$data['keywords'];
$class = \action\course::$data['class'];
$cat_id = \action\course::$data['cat_id'];
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
                $('.button_find').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=index&cat_id=<?php echo $cat_id;?>&keywords=' + $('.keywords').val();
                });
            });
        </script>
    </head>

    <body>

        <div class="menu">
            <input type="text" name="keywords" class="keywords" value="<?php echo isset($keywords) ? $keywords : ""; ?>" placeholder="请输入关键字" />
            <a class="button_find " href="javascript:void(0);">查找</a>
            <a href="javascript:void(0);" class="updateButton"  onclick="javascript:parent.mainFrame.location.href = 'index.php?a=<?php echo $class; ?>&m=getCourse&cat_id=<?php echo $cat_id; ?>'">添加新课程</a>
            <a href="index.php?a=category&m=index&type=view" class="backButton">返回上一级</a>
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
                                <a href="index.php?a=lesson&m=index&course_id=<?php echo $v['id']; ?>&cat_id=<?php echo $cat_id;?>">课时</a>
                                | <a href="index.php?a=<?php echo $class; ?>&m=getCourse&id=<?php echo $v['id']; ?>&cat_id=<?php echo $cat_id; ?>">编辑</a>
                                | <a href="index.php?a=<?php echo $class; ?>&m=deleteCourse&id=<?php echo $v['id']; ?>" onclick="return confirm('确定将此企业删除?')">删除</a></td>
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
            $url = 'index.php?a=' . $class . '&m=index&keywords=' . $keywords . '&cat_id=' . $cat_id;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
