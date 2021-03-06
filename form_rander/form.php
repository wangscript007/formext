<?php
namespace form_rander;

class form
{
    public $_listColumnCfg;
    public static $_pageCfg;
    public $_sqlCfg;
    public $_listDisplayCfg;
    public $_pager;
    public $_searcher;
    public $_db;


    function __construct($db){
        $this->_pager = new pager();
        $this->_searcher = new searcher();     
        $this->_db = $db;   
    }

    //一次性生成表单列配置
    public function getColumns($rows)
    {
        if(empty($rows)){
            echo "需要至少查询出一行数据！";exit;
        }

        $columus = array_keys($rows[0]);

        echo "\$form->_listColumnCfg = array(</br>";
        foreach ($columus as $key => $value) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;'$value' => array('isDisplay' => '1','displayName' => '$value','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),</br>";
        }
        echo "</br>);";
    }

    public function randerForm($sql)
    {
        $this->header();

        //显示查询框
        $this->_searcher->rander();

        $sql = $this->_searcher->randerWhere($sql);
        $rows = $this->_db->fetch_all($sql);

//显示分页
//是否打印序号列
$printNoClass = "";
if(self::$_pageCfg["isPrintNo"] == "0"){
    $printNoClass = "noPrint";
}

$this->_pager->rander(count($rows));
echo "
<table id='mainGridTable' class='gridtable'>
    <thead>
<tr>
    <th class='noPrint'><input name='tablechoice1' type='checkbox'/></th>
    <th class='$printNoClass'>序号</th>
";

//循环列头
foreach ($this->_listColumnCfg as $ckey => $cvalue) {
    $width = empty($cvalue["width"]) ? "" : "width:".$cvalue['width'].";";
    $isDisplay = $cvalue["isDisplay"] == "1" ? "" : "display:none;";
    //是否打印列
    $printThClass = "";
    if($cvalue["isPrint"] == "0"){
        $printThClass = "noPrint";
    }

    echo "<th class='$printThClass' style='$width $isDisplay'>";
    echo $cvalue["displayName"];
    echo "</th>
    ";
}
echo "
</tr>
    </thead>
    <tbody>
";

if(!empty($rows)){
    //取真实列
    $columes = array_keys($rows[0]);
}

$rowNumber = 0;
//循环行
foreach ($rows as $rkey => $rvalue) {

    //判断该行是否应该默认选中
    $rowChecked = "";
    if(array_key_exists("isChecked", $rvalue)){
        if($rvalue["isChecked"] == 1){
            $rowChecked = "checked='checked'";
        }
    }

    //主键
    $checkBoxValue = $rvalue[self::$_pageCfg["primaryKey"]];

    echo "
<tr>
    <td class='noPrint'><input type='checkbox' $rowChecked name='checkbox_".self::$_pageCfg["primaryKey"]."' value='$checkBoxValue'/></td>
    ";
    //序号列
    $rowNumber += 1;
    $rowSN = $rowNumber + $this->_pager->_pageSize * $this->_pager->_pageIndex;
    echo "<td class='$printNoClass'>".$rowSN."</td>
    ";

    //循环列
    foreach ($this->_listColumnCfg as $ckey => $cvalue) {
        //判断列在sql中是否存在
        if(!in_array($ckey, $columes)){
            echo "列".$ckey."在sql中不存在！";exit;
        }

        //是否显示列
        $isDisplay = $cvalue["isDisplay"] == "1" ? "" : "display:none;";
        //是否打印列
        $printTdClass = "";
        if($cvalue["isPrint"] == "0"){
            $printTdClass = "noPrint";
        }
        //是否允许编辑列
        $contentEditable = "";
        $editSqlKey = "";
        $editKey = "";        
        if($cvalue["allowEdit"] == "1"){

            $contentEditable = "contentEditable='true'";
            $editSqlKey = 'editSqlKey="'.$cvalue["editSqlKey"].'"';
            $editKey = 'editKey="'.$cvalue["editKey"].'"';

        }
        $columnName = 'columnName="'.$ckey.'"';
        $tdId = 'id="td_'.$ckey.'_'.$rowSN.'"';
        
        echo "<td $tdId $columnName $contentEditable $editSqlKey $editKey class='$printTdClass' style='$isDisplay'>";

        //列别名
        $value = $this->displayValue($rvalue[$ckey], $ckey);

        //长度截取
        $value = $this->subLength($value, $cvalue["width"]);

        $value = call_user_func("randerCellCallBack", $rvalue, $ckey, $value);

        echo $value;
        echo "</td>
    ";
    }
    echo "
</tr>
    ";
    
}
echo "</tbody>
</table>";

?>

</form>
</body>
</html>
<?php

        return $rows;
    }

    //获取显示值
    private function displayValue($value, $columnName){
        if(in_array($columnName, array_keys($this->_listDisplayCfg))){
            $items = $this->_listDisplayCfg[$columnName];
            if(in_array($value,array_keys($items))){
                return $items[$value];
            }else{ 
                return $value;
            }
        }else{
            return $value;
        }

    }

    //长度截取
    private function subLength($value, $width){
        if(!empty($width)){
            $entitiesValue = htmlentities($value, ENT_QUOTES,"UTF-8");  //原样输出
            $newValue = "<p title='$entitiesValue' class='breviary' style='width:$width'>$entitiesValue</p>";
            return $newValue;
        }

        return $value;
    }

    private function header(){
        $version = self::$_pageCfg["version"];
        $root = self::$_pageCfg["rootPath"];
        $lib = self::$_pageCfg["libPath"]; 
        $debug = self::$_pageCfg["debug"]; 
        $min = "";
        if($debug != "1"){
            $min = ".min";
        }
        ?>
<!DOCTYPE html>            
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo  self::$_pageCfg["Title"] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $lib ?>css\style.css?v=<?php echo $version?>" />
    <link rel="stylesheet" type="text/css" media="print" href="<?php echo $lib ?>css\style-print.css?v=<?php echo $version?>" />
    <link href="<?php echo $lib ?>js\jquery-ui-1.12.1\jquery-ui.css" rel="stylesheet">
    <script src="<?php echo $lib ?>js\jquery-3.3.1<?php echo $min?>.js"></script>
    <script src="<?php echo $lib ?>js\jquery-ui-1.12.1\jquery-ui<?php echo $min?>.js"></script>
    <script src="<?php echo $lib ?>js\jquery.cookie.js"></script>
    <script src="<?php echo $lib ?>js\mp.js"></script>  
    <script src="<?php echo $lib ?>js\head<?php echo $min?>.js"></script>      
    <script src="<?php echo $lib ?>js\My97DatePicker\WdatePicker.js"></script>
    <script src="<?php echo $lib ?>js\jquery.table2excel.js?v=<?php echo $version?>"></script>
    <script src="<?php echo $lib ?>js\util<?php echo $min?>.js?v=<?php echo $version?>"></script>
    <script src="<?php echo $lib ?>js\form<?php echo $min?>.js?v=<?php echo $version?>"></script>
    <script type="text/javascript">
    <?php 
        echo "formExt.rootPath='".str_replace("\\", "/", $root)."';";
        echo "\r\n";
        echo "formExt.libPath='".str_replace("\\", "/", $lib)."';";
        echo "\r\n";
        foreach ($this->_sqlCfg as $key => $sql) {
            $sql = str_replace(array("\r\n", "\r", "\n"), " ", $sql);    
            echo 'formExt.sqlCfg["'.$key.'"] = "'.$sql.'";';
            echo "\r\n";
        }
    ?>    
    </script>
    <?php
        echo call_user_func("randerScriptCallBack");
    ?>
</head>
<body>
<form method="post" action="">
        <?php        
    }


}

