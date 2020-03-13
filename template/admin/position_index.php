<?php
$data = \action\position::$data['data'];
$Total = \action\position::$data['total'];
$currentPage = \action\position::$data['currentPage'];
$pagesize = \action\position::$data['pagesize'];
$keywords = \action\position::$data['keywords'];
$class = \action\position::$data['class'];
$enterprise_id = \action\position::$data['enterprise_id'];
$department_id = \action\position::$data['department_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <title>无标题文档</title>
    </head>

    <body>

        <div class="menu">
            <a href="javascript:void(0);" class="updateButton"  onclick="javascript:parent.mainFrame.location.href = 'index.php?a=<?php echo $class; ?>&m=getPosition&enterprise_id=<?php echo $enterprise_id; ?>&department_id=<?php echo $department_id; ?>'">添加新职位</a>
            <a href="index.php?a=department&m=index" class="backButton">返回上一级</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1">名称</td>
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
                            <td class="td1"><?php
                                if ($v['delete'] == 0) {
                                    echo '使用中';
                                } else {
                                    echo '已删除';
                                }
                                ?></td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class; ?>&m=getPosition&id=<?php echo $v['id']; ?>&department_id=<?php echo $department_id; ?>">编辑</a>
                                | <a href="index.php?a=<?php echo $class; ?>&m=deletePosition&id=<?php echo $v['id']; ?>&department_id=<?php echo $department_id; ?>" onclick="return confirm('确定将此职位删除?')">删除</a></td>
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
            $url = 'index.php?a=' . $class . '&m=index&keywords=' . $keywords . '&enterprise_id=' . $enterprise_id . '&department_id=' . $department_id;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
