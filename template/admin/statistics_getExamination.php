<?php
$data = \action\statistics::$data['data'];
$class = \action\statistics::$data['class'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js" ></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >学员名</td>
                    <td class="td1" >得分</td>
                    <td class="td1" >是否通过</td>
                    <td class="td1" >考试时间</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['uname']; ?></td>
                            <td class="td1"><?php echo $v['point']; ?></td>
                            <td class="td1"><?php echo $v['pass']==1?"通过":"未通过"; ?></td>
                            <td class="td1"><?php echo $v['add_time']; ?></td>
                        </tr>
                        <?php
                        $sum_i++;
                    }
                }
                ?>
            </table>
        </div>
    </body>
</html>
