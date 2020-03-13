<?php
$data = \action\statistics::$data['data'];
$Total = \action\statistics::$data['total'];
$currentPage = \action\statistics::$data['currentPage'];
$pagesize = \action\statistics::$data['pagesize'];
$keywords = \action\statistics::$data['keywords'];
$startTime = \action\statistics::$data['startTime'];
$endTime = \action\statistics::$data['endTime'];
$class = \action\statistics::$data['class'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js" ></script>
        <title>无标题文档</title>
        <!-- 日历插件 -->
        <link href="./css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
        <script type="text/javascript" src="./js/bootstrap.min.js"></script>
        <script type="text/javascript" src="./js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script type="text/javascript" src="./js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
        <!-- 日历插件 end -->
        <script>
            $(function () {
                $('.button_find').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=courseList&keywords=' + $('.keywords').val();
                });
                $('.button_time').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=courseList&startTime=' + $('.start_date').val() + '&endTime=' + $('.end_date').val();
                });
                // 日历插件
                $('#start_date').datetimepicker({
                    language: 'zh-CN',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0,
                    format: 'yyyy-mm-dd'
                });
                $('#end_date').datetimepicker({
                    language: 'zh-CN',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0,
                    format: 'yyyy-mm-dd'
                });
            });
        </script>
    </head>

    <body>
        <div class="menu">
            <a class="button_export" href="index.php?a=<?php echo $class; ?>&m=courseList&export=2" >导出本页</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >课程名称</td>
                    <td class="td1" >参与人数</td>
                    <td class="td1" >学习进度</td>
                    <td class="td1" >考试通过率</td>
                    <td class="td1" width="8%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1"><?php echo $v['joinPerson']; ?></td>
                            <td class="td1"><?php echo ($v['progressLesson']*100); ?>%</td>
                            <td class="td1"><?php echo ($v['progressExam']*100); ?>%</td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class;?>&m=getStatisticsCourse&id=<?php echo $v['id'];?>">详细</a>
                                <a href="index.php?a=<?php echo $class;?>&m=getStatisticsCourse&id=<?php echo $v['id'];?>&export=2">导出本条</a>
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
            <?php
            $url = 'index.php?a=' . $class . '&m=courseList&keywords=' . $keywords . '&startTime=' . $startTime . '&endTime=' . $endTime;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
