<?php
$data = \action\purv::$data['data'];
$Total = \action\purv::$data['total'];
$class = \action\purv::$data['class'];
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
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=index';
                });
            });
        </script>
    </head>

    <body>
        <div class="menu">
            <a href="javascript:void(0);" class="updateButton"  onclick="javascript:parent.mainFrame.location.href = 'index.php?a=<?php echo $class; ?>&m=getPurv'">添加新权限</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >权限名</td>
                    <td class="td1" width="10%">CODE</td>
                    <td class="td1" width="10%">属于</td>
                    <td class="td1" width="20%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr class="<?php if($sum_i % 2 != 1){echo 'tr2 ';} if($v['level']=='0'){echo 'tr-level-one ';}else{echo 'tr-level-two ';} ?>" >
                            <td class="td1"><?php
                                for ($i = 1; $i <= $v['level']; $i++) {
                                    echo '- ';
                                }echo $v['name'];
                                ?></td>
                            <td class="td1"><?php echo $v['code']; ?></td>
                            <td class="td1"><?php echo $v['add_by']; ?></td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class; ?>&m=getPurv&id=<?php echo $v['id']; ?>">编辑</a>
                                <a class="del" href="index.php?a=<?php echo $class; ?>&m=deletePurv&id=<?php echo $v['id']; ?>" onclick="return confirm('确定将此权限删除?')">删除</a>
                            </td>
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
        </div>
    </body>
</html>
