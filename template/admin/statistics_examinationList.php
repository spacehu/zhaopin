<?php
$data = \action\statistics::$data['data'];
$Total = \action\statistics::$data['total'];
$currentPage = \action\statistics::$data['currentPage'];
$pagesize = \action\statistics::$data['pagesize'];
$keywords = \action\statistics::$data['keywords'];
$class = \action\statistics::$data['class'];
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
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=examinationList&keywords=' + $('.keywords').val();
                });
            });
        </script>
    </head>

    <body>
        <div class="menu">
            <a class="button_export" href="index.php?a=<?php echo $class; ?>&m=examinationList&export=2" >导出本页</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >试卷名</td>
                    <td class="td1" >通过人数</td>
                    <td class="td1" >参与人数</td>
                    <td class="td1" >考试通过率（人）</td>
                    <td class="td1" >通过次数</td>
                    <td class="td1" >参与次数</td>
                    <td class="td1" >考试通过率（次）</td>
                    <td class="td1" width="8%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1"><?php echo $v['totalEuPass']; ?></td>
                            <td class="td1"><?php echo $v['totalEu']; ?></td>
                            <td class="td1"><?php echo $v['totalEu']>0?($v['totalEuPass']/$v['totalEu'])*100:0; ?>%</td>
                            <td class="td1"><?php echo $v['totalExPass']; ?></td>
                            <td class="td1"><?php echo $v['totalEx']; ?></td>
                            <td class="td1"><?php echo $v['totalEx']>0?($v['totalExPass']/$v['totalEx'])*100:0; ?>%</td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class;?>&m=getExamination&id=<?php echo $v['id'];?>">详细</a>
                                <a href="index.php?a=<?php echo $class;?>&m=getExamination&id=<?php echo $v['id'];?>&export=2">导出本条</a>
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
            $url = 'index.php?a=' . $class . '&m=courseList&keywords=' . $keywords;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
