<?php
$data = \action\questionnaire::$data['data'];
$class = \action\questionnaire::$data['class'];
$categorys = \action\questionnaire::$data['categorys'];
$category = \action\questionnaire::$data['category'];
$questionnaire_test = \action\questionnaire::$data['questionnaire_test'];
$questionnaire_test_id = \action\questionnaire::$data['questionnaire_test_id'];
$enterprise_id = \action\questionnaire::$data['enterprise_id'];
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
        <!-- 图片控件 -->
        <script src="lib/cos-js-sdk-v5-master/dist/cos-js-sdk-v5.js"></script>
        <script type="text/javascript" src="js/tencent_cos.js"></script>
        <!-- 复选框 -->
        <link rel="stylesheet" type="text/css" href="css/multi-select.css" />
        <script type="text/javascript" src="js/jquery.multi-select.js"></script>
        <title>无标题文档</title>
    </head>

    <body>
        <div class="status r_top">
        </div>
        <div class="content">
            <form name="theForm" id="demo" action="./index.php?a=<?php echo $class; ?>&m=updateQuestionnaire&id=<?php echo isset($data['id']) ? $data['id'] : ""; ?>" method="post" enctype='multipart/form-data'>
                <div class="pathA ">
                    <div class="leftA">
                        <div class="leftAlist" >
                            <span>问卷名</span>
                        </div>
                        <div class="leftAlist" >
                            <input class="text" name="name" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ""; ?>" />
                            <input class="text" name="enterprise_id" type="hidden" value="<?php echo $enterprise_id; ?>" />
                        </div>
                        <div class="leftAlist" >
                            <span>TESTS 试题列表</span>
                            <select id="change_category">
                                <option value="1">筛选分类</option>
                                <?php if (is_array($categorys)) { ?>
                                    <?php foreach ($categorys as $k => $v) { ?>
                                        <option value="<?php echo $v['id']; ?>" <?php echo $category==$v['id']?' selected="selected" ':'';?> ><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <a href='javascript:void(0);' id='select-all'>全选</a>
                            <a href='javascript:void(0);' id='deselect-all'>全取消</a>
                            <!--
                            <a href='javascript:void(0);' id='refresh'>刷新</a>
                            -->
                        </div>
                        <div class="leftAlist" >
                            <!-- 复选框 未分配的学员 -->
                            <select multiple="multiple" id="pre-selected-options" name="my-course[]">
                                
                            </select>
                            <input class="text" name="test_add" id="test_add" type="hidden" value="" />
                            <input class="text" name="test_remove" id="test_remove" type="hidden" value="" />
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
            var questionnaire_test_id=<?php echo json_encode($questionnaire_test_id);?>;
            $(function(){
                var cat_id=$("#change_category").attr("value");
                getCategoryList(cat_id);
                $("#change_category").on('change',function(){
                    //console.log($(this).attr("value"));
                    getCategoryList($(this).attr("value"));
                });
            });
            var getCategoryList = function (id) {
                var regionlist;
                $.ajax({
                    async: false,
                    url:'./index.php?a=<?php echo $class; ?>&m=getQuestionnaireTestList&enterprise_id=<?php echo $enterprise_id;?>&cat_id=' +id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        regionlist = data.data;
                    },
                    complete: function () {
                    }
                    });
                // return regionlist;
                setSelect(regionlist);
            };
            // 重制select框的数据
            var setSelect = function(list){
                // 清除之前的选项
                $("#pre-selected-options").empty();
                // 清除之前的存储数据
                users_add=[];
                users_remove=[];
                //console.log(list);
                $.each(list,function(k,v){
                    //console.log(v.id);
                    var selected='';
                    if($.inArray(v.id,questionnaire_test_id)>=0){
                        selected='selected';
                    }
                    var _row='<option value="'+v.id+'" '+selected+' >'+v.name+'</option>';
                    // 将数据填充到select框
                    $("#pre-selected-options").append(_row);
                });
                $('#pre-selected-options').multiSelect('refresh');
            };
            // select选择后调用的方法
            var afterSelect=function(values){
                if(values.length==0){return false;}
                if(values.length==1){
                    // 新增数据 在数组结构上追加
                    if($.inArray(values[0], users_add)==-1){
                        // 如果不存在 则追加
                        users_add[users_add.length] = values[0]; 
                    }// 否则不操作
                    // 删除数据 在数组结构上抹去改值对应的key值
                    if($.inArray(values[0], users_remove)!=-1){
                        // 如果存在 则删除
                        users_remove.splice($.inArray(values[0], users_remove), 1);
                    }
                }
                if(values.length>1){
                    $.each(values,function (k,v){
                        users_add[users_add.length] = v; 
                        if($.inArray(v, users_remove)!=-1){
                            users_remove.splice($.inArray(v, users_remove), 1);
                        }
                    });
                }
                $("#test_add").attr("value", users_add.toString());
                $("#test_remove").attr("value", users_remove.toString());
            }
            // select取消选择后调用的方法
            var afterDeselect=function(values){
                if(values==null||values.length==0){return false;}
                if(values.length==1){
                    users_remove[users_remove.length] = values[0];
                    if($.inArray(values[0], users_add)!=-1){
                        users_add.splice($.inArray(values[0], users_add), 1);
                    }
                }
                if(values.length>1){
                    $.each(values,function (k,v){
                        users_remove[users_remove.length] = v;
                        if($.inArray(v, users_add)!=-1){
                            users_add.splice($.inArray(v, users_add), 1);
                        }
                    });
                }
                $("#test_add").attr("value", users_add.toString());
                $("#test_remove").attr("value", users_remove.toString());
            }
            // 定义初始数组 用来赋值到post
            // 新增
            var users_add = [];
            // 删除
            var users_remove = [];
            $('#pre-selected-options').multiSelect({
                selectableHeader: "<div class='custom-header'>题库</div>",
                selectionHeader: "<div class='custom-header'>已选题目</div>",
                afterSelect: function (values) {
                    afterSelect(values);
                },
                afterDeselect: function (values) {
                    afterDeselect(values);
                }
            });
            $('#select-all').click(function(){
                $('#pre-selected-options').multiSelect('select_all');
                return false;
            });
            $('#deselect-all').click(function(){
                $('#pre-selected-options').multiSelect('deselect_all');
                return false;
            });
            $('#refresh').on('click', function(){
                $('#pre-selected-options').multiSelect('refresh');
                return false;
            });
        </script>
    </body>
</html>