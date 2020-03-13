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
                    <td class="td1" >课程</td>
                    <td class="td1" >参与人数</td>
                    <td class="td1" >学习进度</td>
                    <td class="td1" >考试通过率</td>
                </tr>
                <tr>
                    <td class="td1"><?php echo $data['info']['name']; ?></td>
                    <td class="td1"><?php echo $data['info']['joinPerson']; ?></td>
                    <td class="td1"><?php echo round($data['info']['progressLesson']*100); ?>%</td>
                    <td class="td1"><?php echo round($data['info']['progressExam']*100); ?>%</td>
                </tr>
            </table>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >姓名</td>
                    <td class="td1" >部门</td>
                    <td class="td1" >职位</td>
                    <td class="td1" >学习进度</td>
                    <td class="td1" >考试通过</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data['courseList'])) {
                    foreach ($data['courseList'] as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1"><?php echo $v['edname']; ?></td>
                            <td class="td1"><?php echo $v['epname']; ?></td>
                            <td class="td1"><?php echo round($v['progressLesson']*100); ?>%</td>
                            <td class="td1"><?php echo !empty($v['totalE'])?"YES":"NO"; ?></td>
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
