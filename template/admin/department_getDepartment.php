<?php
$data = \action\department::$data['data'];
$class = \action\department::$data['class'];
$enterpriseUser = \action\department::$data['enterpriseUser'];
$enterprise_id = \action\department::$data['enterprise_id'];
$enterpriseCourse = \action\department::$data['enterpriseCourse'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <!-- 复选框 -->
        <link rel="stylesheet" type="text/css" href="css/multi-select.css" />
        <script type="text/javascript" src="js/jquery.multi-select.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateDepartment&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 部门名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                            <input class="text" name="enterprise_id" type="hidden" value="<?php echo $enterprise_id; ?>" />
                        </div>
                    </div>
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>CUSTOMER 隶属员工/学员</span>
                        </div>
                        <div class="leftAlist" >
                            <!-- 复选框 未分配的学员 -->
                            <select multiple="multiple" id="pre-selected-options" name="my-selected[]">
                                <?php if (!empty($enterpriseUser)) { ?>
                                    <?php foreach ($enterpriseUser as $k => $v) { ?>
                                        <option value='<?php echo $v['id']; ?>' <?php echo ($v['department_id'] == $data['id'] && $data['id'] != 0) ? "selected" : ""; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <input class="text" name="departmentadd" id="departmentadd" type="hidden" value="" />
                            <input class="text" name="departmentremove" id="departmentremove" type="hidden" value="" />
                        </div>
                    </div>
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>COURSE 私有课程</span>
                        </div>
                        <div class="leftAlist" >
                            <!-- 复选框 未分配的学员 -->
                            <select multiple="multiple" id="course-selected-options" name="my-course[]">
                                <?php if (!empty($enterpriseCourse)) { ?>
                                    <?php foreach ($enterpriseCourse as $k => $v) {$dids= explode(",", $v['d_ids']); ?>
                                <option value='<?php echo $v['id']; ?>' <?php echo (in_array($data['id'],$dids )&& $data['id'] != 0) ? "selected" : ""; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <input class="text" name="courses_add" id="courses_add" type="hidden" value="" />
                            <input class="text" name="courses_remove" id="courses_remove" type="hidden" value="" />
                        </div>
                    </div>
                </div>
                <div class="pathB">
                    <div class="leftA">
                        <input name="" type="submit" id="submit" value="SUBMIT 提交" />
                    </div>
                </div>
            </form>	
        </div>
        <script>
            var users_add = [];
            var users_remove = [];
            $('#pre-selected-options').multiSelect({
                selectableHeader: "<div class='custom-header'>企业员工/学员</div>",
                selectionHeader: "<div class='custom-header'>部门员工/学员</div>",
                afterSelect: function (values) {
                    users_add[users_add.length] = values[0];
                    users_remove.splice($.inArray(values[0], users_remove), 1);
                    console.log(users_add.toString());
                    console.log(users_remove.toString());
                    $("#departmentadd").attr("value", users_add.toString());
                    $("#departmentremove").attr("value", users_remove.toString());
                },
                afterDeselect: function (values) {
                    users_remove[users_remove.length] = values[0];
                    users_add.splice($.inArray(values[0], users_add), 1);
                    console.log(users_add.toString());
                    console.log(users_remove.toString());
                    $("#departmentadd").attr("value", users_add.toString());
                    $("#departmentremove").attr("value", users_remove.toString());
                }
            });
            var courses_add = [];
            var courses_remove = [];
            $('#course-selected-options').multiSelect({
                selectableHeader: "<div class='custom-header'>企业课程</div>",
                selectionHeader: "<div class='custom-header'>部门课程</div>",
                afterSelect: function (values) {
                    courses_add[courses_add.length] = values[0];
                    courses_remove.splice($.inArray(values[0], courses_remove), 1);
                    console.log(courses_add.toString());
                    console.log(courses_remove.toString());
                    $("#courses_add").attr("value", courses_add.toString());
                    $("#courses_remove").attr("value", courses_remove.toString());
                },
                afterDeselect: function (values) {
                    courses_remove[courses_remove.length] = values[0];
                    courses_add.splice($.inArray(values[0], courses_add), 1);
                    console.log(courses_add.toString());
                    console.log(courses_remove.toString());
                    $("#courses_add").attr("value", courses_add.toString());
                    $("#courses_remove").attr("value", courses_remove.toString());
                }
            });
        </script>
    </body>
</html>