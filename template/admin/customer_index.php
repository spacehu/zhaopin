<?php
$data = \action\customer::$data['data'];
$Total = \action\customer::$data['total'];
$currentPage = \action\customer::$data['currentPage'];
$pagesize = \action\customer::$data['pagesize'];
$keywords = \action\customer::$data['keywords'];
$class = \action\customer::$data['class'];
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
                var ids = [];
                $('.button_find').click(function () {
                    window.location.href = 'index.php?a=<?php echo $class; ?>&m=index&keywords=' + $('.keywords').val();
                });
            });
        </script>
    </head>

    <body>
        <div class="menu">
            <input type="text" name="keywords" class="keywords" value="<?php echo isset($keywords) ? $keywords : ""; ?>" placeholder="请输入用户名中的关键字" />
            <a class="button_find " href="javascript:void(0);">查找</a>
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >用户名</td>
                    <td class="td1" >企业名</td>
                    <td class="td1" >职位名</td>
                    <td class="td1" width="20%">授权时间</td>
                    <td class="td1" width="30%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1"><?php echo $v['eName']; ?></td>
                            <td class="td1"><?php echo $v['aName']; ?></td>
                            <td class="td1"><?php echo $v['add_time']; ?></td>
                            <td class="td1">
                                <a href="index.php?a=show&m=getUserResume&user_id=<?php echo $v['id']; ?>">查看简历</a>
                                <a href="index.php?a=show&m=getUserResume&user_id=<?php echo $v['id']; ?>&export=2">导出简历</a>
                                <!--
                                <a href="index.php?a=<?php echo $class; ?>&m=getCustomer&id=<?php echo $v['id']; ?>">查看</a>
                                -->
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
            $url = 'index.php?a=' . $class . '&m=index&keywords=' . $keywords;
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
