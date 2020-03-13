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
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=customerList&keywords=' + $('.keywords').val();
                });
                $('.button_time').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=customerList&startTime=' + $('.start_date').val() + '&endTime=' + $('.end_date').val();
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
            <input type="text" name="keywords" class="keywords" value="<?php echo isset($keywords) ? $keywords : ""; ?>" />
            <a class="button_find " href="javascript:void(0);">查找</a>
            <input type="text" name="start_date" id="start_date" class="start_date" value="<?php echo isset($startTime) ? $startTime : ""; ?>" readonly /> -
            <input type="text" name="end_date" id="end_date" class="end_date" value="<?php echo isset($endTime) ? $endTime : ""; ?>" readonly />
            <a class="button_time " href="javascript:void(0);">查找</a>
            <a class="button_export" href="index.php?a=<?php echo $class; ?>&m=customerList&startTime=<?php echo $startTime;?>&endTime=<?php echo $endTime;?>&keywords=<?php echo $keywords;?>&export=2" >导出本页</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >姓名</td>
                    <td class="td1" >部门</td>
                    <td class="td1" >职位</td>
                    <td class="td1" >企业必修课程数</td>
                    <td class="td1" width="10%">学习进度</td>
                    <td class="td1" width="10%">总学习时间</td>
                    <td class="td1" width="8%">通过考试数</td>
                    <td class="td1" width="8%">参与课程数</td>
                    <td class="td1" width="8%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['NAME']; ?></td>
                            <td class="td1"><?php echo $v['edname']; ?></td>
                            <td class="td1"><?php echo $v['epname']; ?></td>
                            <td class="td1"><?php echo $v['enterpriseCourseCount']; ?></td>
                            <td class="td1"><?php echo round($v['progress']); ?>%</td>
                            <td class="td1"><?php echo $v['hours']; ?></td>
                            <td class="td1"><?php echo $v['passExamCount']; ?></td>
                            <td class="td1"><?php echo $v['joinCourseCount']; ?></td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class;?>&m=getStatisticsCustomer&id=<?php echo $v['id'];?>">详细</a>
                                <a href="index.php?a=<?php echo $class;?>&m=getStatisticsCustomer&id=<?php echo $v['id'];?>&export=2">导出本条</a>
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
            $url = 'index.php?a=' . $class . '&m=customerList&keywords=' . $keywords . '&startTime=' . $startTime . '&endTime=' . $endTime;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
