

<?php
require "../../autoload.php";

form_rander\form::$_pageCfg = array(
    'rootPath' => "..\\..\\",
    'libPath' => "..\\..\\form_rander\\",
    'Title' => "住院患者信息",
    'version' => $globalCfg["version"], //系统版本，变动时，js等缓存文件也会刷新
    'isPrintNo' => "0", //是否打印序号列
    'primaryKey' => "PatientHospitalize_DBKey", //主键，复选框对应的值
    'EnableDel' => "1", //是否启用删除按钮
    'pageSize' => 20, //每页显示记录条数
    'debug' => $globalCfg["debug"],
);

$form = new form_rander\form($db);

$form->_sqlCfg = array(
    'deleteSql' => "delete a.*,b.* from patienthospitalizebasicinfo  a
                    inner join patientbasicinfo b on a.PATIENT_DBKEY = b.PATIENT_DBKEY where PatientHospitalize_DBKey in ({0})", //删除sql
    'editSql1' => "update patientbasicinfo set {columnName} = :value where
                     PATIENT_DBKEY = :PATIENT_DBKEY and PatientNo = :PatientNo",
    'editSql2' => "update patienthospitalizebasicinfo set {columnName} = :value where PatientHospitalize_DBKey = :PatientHospitalize_DBKey",
);

$form->_listColumnCfg = array(
    'PatientHospitalize_DBKey' => array('isDisplay' => '1','displayName' => 'PatientHospitalize_DBKey','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'PATIENT_DBKEY' => array('isDisplay' => '1','displayName' => 'PATIENT_DBKEY','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'PatientName' => array('isDisplay' => '1','displayName' => 'PatientName','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '1','editKey' => 'PATIENT_DBKEY,PatientNo', 'editSqlKey' => 'editSql1'),
    'Department_DBKey' => array('isDisplay' => '1','displayName' => 'Department_DBKey','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'BedNumber_DBKey' => array('isDisplay' => '1','displayName' => 'BedNumber_DBKey','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'HospitalizationNumber' => array('isDisplay' => '1','displayName' => 'HospitalizationNumber','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'InHospitalData' => array('isDisplay' => '1','displayName' => 'InHospitalData','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '1','editKey' => 'PatientHospitalize_DBKey', 'editSqlKey' => 'editSql2'),
    'PatientNo' => array('isDisplay' => '1','displayName' => 'PatientNo','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'Age' => array('isDisplay' => '1','displayName' => 'Age','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '1','editKey' => 'PATIENT_DBKEY,PatientNo', 'editSqlKey' => 'editSql1'),
    'Gender' => array('isDisplay' => '1','displayName' => 'Gender','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'TherapyStatus' => array('isDisplay' => '1','displayName' => 'TherapyStatus','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'Height' => array('isDisplay' => '1','displayName' => 'Height','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'Weight' => array('isDisplay' => '1','displayName' => 'Weight','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'MedicalHistory' => array('isDisplay' => '1','displayName' => 'MedicalHistory','width' => '150px','maxLength' => '10','isPrint' => '0','allowEdit' => '1','editKey' => 'PatientHospitalize_DBKey', 'editSqlKey' => 'editSql2'),
    'PastMedicalHistory' => array('isDisplay' => '1','displayName' => 'PastMedicalHistory','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
    'ChiefComplaint' => array('isDisplay' => '1','displayName' => 'ChiefComplaint','width' => '','maxLength' => '','isPrint' => '1','allowEdit' => '0','editKey' => '', 'editSqlKey' => ''),
 
);

$form->_listDisplayCfg = array(
    'Gender' => array('F' => '女','M' => '男'),
    'MaritalStatus' => array('1' => '已婚','2' => '离异'),
    'Department_DBKey' => $db->fetch_cols("select Department_DBKey '0', DepartmentName '1' from department"),
);

//Y-m-d H:i:s
$form->_searcher->_searchCfg = array(
    'HospitalizationNumber' => array('labelName' => '住院号','randerText' => " and a.HospitalizationNumber like '%{value}%' ",'dataType' => 'string', 'defaultValue' => '11','format' => '', 'break' => '0', 'tooltip' => ''),
    'InHospitalData' => array('labelName' => '入院日期 ','randerText' => " and a.InHospitalData >= '{value}' ",'dataType' => 'datetime',  'defaultValue' => '-100','format' => 'Y-m-d H:i:s', 'break' => '0', 'tooltip' => ''),
    'InHospitalData2' => array('labelName' => '至','randerText' => " and a.InHospitalData <= '{value}' ",'dataType' => 'date',  'defaultValue' => '0','format' => 'Y-m-d', 'break' => '1', 'tooltip' => ''),
    'PatientName' => array('labelName' => '患者姓名','randerText' => " and b.PatientName like '{value}%' ",'dataType' => 'string',  'defaultValue' => '','format' => '', 'break' => '0', 'tooltip' => ''),
    'Department' => array('labelName' => '科室','randerText' => " and a.Department_DBKey in ({value}) ",'dataType' => 'string',  'defaultValue' => '','format' => '', 'break' => '0', 'tooltip' => '支持多选'),
    'Gender' => array('labelName' => '性别','randerText' => " and b.Gender = '{value}' ",'dataType' => 'string',  'defaultValue' => '','format' => '', 'break' => '0', 'tooltip' => 'M 男，F 女'),

);

$sql = 'select a.*, b.PatientName,b.PatientNo,b.Age,b.Gender, case when a.PatientHospitalize_DBKey > 135659 then 1 else 0 end isChecked from patienthospitalizebasicinfo a inner join patientbasicinfo b on a.PATIENT_DBKEY = b.PATIENT_DBKEY where 1=1 [w|HospitalizationNumber] [w|InHospitalData] [w|InHospitalData2] [w|PatientName] [w|Department] [w|Gender] [w|TherapyStatus] [w|Sex] order by  a.InHospitalData desc '.$form->_pager->getLimit();

$rows = $form->randerForm($sql);
//$form->getColumns($rows);

function randerSearchCallBack(){
    include_once("includeRanderSearchCallBack.php");
}

function randerSearchWhereCallBack($sql){
    return include_once("includeRanderSearchWhereCallBack.php");
}

function randerToolBarCallBack(){
?>

    <input type="button" value="打开" onclick="patientinfo.openInfo()"/>
<?php
}

function randerScriptCallBack(){
    echo '<script src="js/patientinfo.js?v='.form_rander\form::$_pageCfg["version"].'"></script>';
}

function randerCellCallBack($row, $key, $value){

    return $value;
}

?>

