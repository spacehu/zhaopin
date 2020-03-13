<?php
$data = \action\show::$data['data'];
$Total = \action\show::$data['total'];
$currentPage = \action\show::$data['currentPage'];
$pagesize = \action\show::$data['pagesize'];
$class = \action\show::$data['class'];
$id = \action\show::$data['id'];
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
        <div class="menu">
        </div>
        <div class="content">
            <table class="mytable" cellspacing="0" >
                <tr bgcolor="#656565" style=" font-weight:bold; color:#FFFFFF;">
                    <td class="td1" >姓名</td>
                    <td class="td1" width="20%">操作</td>
                </tr>
                <?php
                $sum_i = 1;
                if (!empty($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr<?php if ($sum_i % 2 != 1) { ?>  class="tr2"<?php } ?>>
                            <td class="td1"><?php echo $v['name']; ?></td>
                            <td class="td1">
                                <a href="index.php?a=<?php echo $class; ?>&m=getUserResume&user_id=<?php echo $v['user_id']; ?>">查看</a>
                                <a href="index.php?a=<?php echo $class; ?>&m=deleteUserResumeArticle&ura_id=<?php echo $v['ura_id']; ?>&article_id=<?php echo $id;?>" onclick="return confirm('确定将此次简历删除?')">删除</a>
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
            $url = 'index.php?a=' . $class . '&m=getResumeList';
            $Totalpage = ceil($Total / mod\init::$config['page_width']);
            include_once 'page.php';
            ?>
        </div>
    </body>
</html>
