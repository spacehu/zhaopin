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
                    <td class="td1" >姓名</td>
                    <td class="td1" >部门</td>
                    <td class="td1" >职位</td>
                    <td class="td1" >联系方式</td>
                    <td class="td1" >企业必修课程数</td>
                    <td class="td1" width="10%">学习进度</td>
                    <td class="td1" width="10%">总学习时间</td>
                    <td class="td1" width="8%">通过考试数</td>
                    <td class="td1" width="8%">参与课程数</td>
                </tr>
                <tr>
                    <td class="td1"><?php echo $data['info']['name']; ?></td>
                    <td class="td1"><?php echo $data['info']['edname']; ?></td>
                    <td class="td1"><?php echo $data['info']['epname']; ?></td>
                    <td class="td1"><?php echo $data['info']['phone']; ?></td>
                    <td class="td1"><?php echo $data['info']['enterpriseCourseCount']; ?></td>
                    <td class="td1"><?php echo round($data['info']['progress']); ?>%</td>
                    <td class="td1"><?php echo $data['info']['hours']; ?></td>
                    <td class="td1"><?php echo $data['info']['passExamCount']; ?></td>
                    <td class="td1"><?php echo $data['info']['joinCourseCount']; ?></td>
                </tr>
            </table>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >课程</td>
                    <td class="td1" >类别</td>
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
                            <td class="td1"><?php echo !empty($v['enterpriseCourse'])?"企业必修课程":"选修课"; ?></td>
                            <td class="td1"><?php echo round($v['progress']); ?>%</td>
                            <td class="td1"><?php echo !empty($v['passExamCount'])?"通过考试":""; ?></td>
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
