<?php
$class = \action\show::$data['class'];
$data = \action\show::$data['data'];
$list = \action\show::$data['enumList'];
$enterprise_id = \action\show::$data['enterprise_id'];
$region = \action\show::$data['region'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <!-- 配置文件 -->
        <script type="text/javascript" src="lib/uEditor/ueditor.config.js"></script>
        <!-- 编辑器源码文件 -->
        <script type="text/javascript" src="lib/uEditor/ueditor.all.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateShow&id=<?php echo $data['id']; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>NAME 标题</span>
                        </div>
                        <div class="leftAlist" >
                            <div class="">
                                <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" />
                                <input class="text" name="cat_id" type="hidden" value="<?php echo isset($cat_id) ? $cat_id : '0'; ?>" />
                            </div>
                        </div>
                        <?php if (!empty($enterprise_id)) { ?>
                            <input type="hidden" name="enterprise_id" value="<?php echo $enterprise_id; ?>" />
                        <?php } else { ?>
                            <input type="hidden" name="enterprise_id" value="" />
                        <?php } ?>
                    </div>
                    <div class="leftA c_17 ">
                        <div class="leftAlist" >
                            <span>行业</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="type" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[1])) { ?>
                                    <?php foreach ($list[1]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['type'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>薪资</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="salary" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[3])) { ?>
                                    <?php foreach ($list[3]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['salary'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>工作经验</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="age_min" id="type">
                                <option value="">请选择</option>
                                <?php if (is_array($list)&&!empty($list[2])) { ?>
                                    <?php foreach ($list[2]['data'] as $k => $v) { ?>
                                        <option value="<?php echo $v; ?>"  <?php echo $data['age_min'] == $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="leftAlist" >
                            <span>地区</span>
                        </div>
                        <div class="leftAlist" >
                            <select name="tempA" id="tempA">
                                <option value="0">请选择</option>
                            </select>
                            <select name="tempB" id="tempB">
                                <option value="0">请选择</option>
                            </select>
                            <select name="tempC" id="tempC">
                                <option value="0">请选择</option>
                            </select>
                            
                            <input type="hidden" name="province" id="province" value="<?php echo isset($data['province']) ? $data['province'] : 0; ?>" />
                            <input type="hidden" name="city" id="city" value="<?php echo isset($data['city']) ? $data['city'] : 0; ?>" />
                            <input type="hidden" name="district" id="district" value="<?php echo isset($data['district']) ? $data['district'] : 0; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>岗位职责</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container2" name="responsibilities" type="text/plain">
<?php echo isset($data['responsibilities']) ? $data['responsibilities'] : ""; ?>
                            </script>
                        </div>
                        <div class="leftAlist" >
                            <span>任职资格</span>
                        </div>
                        <div class="leftAlist" >
                            <script id="container3" name="qualifications" type="text/plain">
<?php echo isset($data['qualifications']) ? $data['qualifications'] : ""; ?>
                            </script>
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
        <script type="text/javascript">
            var ue = UE.getEditor('container2');
            var ue = UE.getEditor('container3');
            
            var _selectA =<?php echo isset($region['1'])?$region['1']:0; ?>;
            var _selectB =<?php echo isset($region['2'])?$region['2']:0; ?>;
            var _selectC =<?php echo isset($region['3'])?$region['3']:0; ?>;
            $(function () {
                var getRegionList = function (id) {
                    var regionlist;
                    $.ajax({
                        async: false,
                        url: "./v2/ApiEnum-getRegion.htm?id=" + id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            regionlist = data.data.list;
                        },
                        complete: function () {
                        }
                    });
                    return regionlist;
                };
                var setRegionInfo = function (id, name) {
                    $("#"+name).val(id);
                };
                var mkOption = function (obj, id) {
                    var html = '<option value="0">请选择</option>';
                    $.each(obj, function (i, n) {
                        var selected = '';
                        if (id == n.id) {
                            selected = 'selected';
                        }
                        html += '<option value="' + n.id + '" ' + selected + '>' + n.name + '</option>';
                    });
                    return html;
                };

                //loading
                $("#tempA").html(mkOption(getRegionList(0), _selectA));
                $("#tempB").html(mkOption(getRegionList(_selectA), _selectB));
                $("#tempC").html(mkOption(getRegionList(_selectB), _selectC));
                //click
                $("#tempA").on('change', function () {
                    $("#tempB").html(mkOption(getRegionList(this.value), 0));
                    $("#tempC").html(mkOption(getRegionList(0), 0));
                    setRegionInfo(this.value, 'province');
                });
                $("#tempB").on('change', function () {
                    $("#tempC").html(mkOption(getRegionList(this.value), 0));
                    setRegionInfo(this.value, 'city');
                });
                $("#tempC").on('change', function () {
                    setRegionInfo(this.value, 'district');
                });
            });
        </script>
    </body>
</html>