<?php
require "../../../autoload.php";

$detailDBKeys = $_GET["detailDBKeys"];

$sql = "select  c.NutrientAdviceSummary_DBKey,d.HospitalizationNumber, e.PatientName, f.DepartmentName, g.Bed, h.SysCodeName PreparationMode,a.TakeOrder from nutrientadvicedetail a INNER JOIN nutrientadvice b on a.NutrientAdvice_DBKey = b.NutrientAdvice_DBKey
inner JOIN nutrientadvicesummary c on b.NutrientAdviceSummary_DBKey = c.NutrientAdviceSummary_DBKey
inner join patienthospitalizebasicinfo d on d.PatientHospitalize_DBKey = c.PatientHospitalize_DBKey
inner join patientbasicinfo e on e.PATIENT_DBKEY = d.PATIENT_DBKEY
left join department f on f.Department_DBKey = d.Department_DBKey
left join bednumber g on g.BedNumber_DBKey = d.BedNumber_DBKey
left join syscode h on h.SysCode = a.PreparationMode and h.SystemCodeTypeName = 'PreparationMode'
where a.NutrientAdviceDetail_DBKEY in ($detailDBKeys) limit 0,1";
$result = $db->fetch_row($sql);

//制剂数据
$sql = "select b.RecipeAndProductName,cast(a.AdviceAmount as SIGNED INTEGER) AdviceAmount,a.NutrientAdviceDetailRemark,c.MeasureUnitName, b.wrapperType, a.Specification
,a.NutrientAdviceDetailRemark, a.LiquidAmount
from nutrientadvicedetail a
INNER JOIN recipeandproduct b on a.RecipeAndProduct_DBKey = b.RecipeAndProduct_DBKey
left join measureunit c on c.MeasureUnit_DBKey = b.MeasureUnit_DBKey
where a.NutrientAdviceDetail_DBKEY in ($detailDBKeys)";
$tblDetail = $db->fetch_all($sql);
?>

<div class="labelContent">

<table class="baseTable">
    <tr>
    <td>姓名：<?php echo $result["PatientName"] ?></td>
    <td>科室：<?php echo $result["DepartmentName"] ?></td>
    <td>床号：<?php echo $result["Bed"] ?></td>
    </tr>
</table>

<table  style="margin-top:-1px" >
    <tr>
    <td style="border-right:none">品名</td>
    <td style="padding:0px">
        <table style="margin-top:-1px;margin-bottom:-1px">
            <tr>
                <td style="padding:0px">
                <table  class="recipeTable" id="tblNutrientadvicedetail" >
    <tbody>
        <?php
foreach ($tblDetail as $key => $value) {
    // $unit = "";
    // if ($value["wrapperType"] == "1") {
    //     $unit = $value["MeasureUnitName"];
    // } else {
    //     $unit = $value["MeasureUnitName"];
    // }

    $NutrientAdviceDetailRemark = $value["NutrientAdviceDetailRemark"] == "无" ? "" : $value["NutrientAdviceDetailRemark"];
    $LiquidAmount = $value["LiquidAmount"] == 0 ? "" : $value["LiquidAmount"] . "ml";
    
    echo "<tr>
            <td>" . $value["RecipeAndProductName"] . "</td>
            <td>规格：".$value["Specification"]."</td>
            <td>*".$value["AdviceAmount"] ."</td>
            <td>". $NutrientAdviceDetailRemark . "</td>
            <td>". $LiquidAmount . "</td>
            </tr>";
}
?>
    </tbody>
</table>
                </td>
            </tr>
            <tr>
                <td style="padding:0px">
                    <table style="margin-top:-1px;">
                        <tr>
                        <td><nobr>时间：</nobr><br/><nobr><?php echo $result["TakeOrder"] ?></nobr></td>                        
                        <td>制剂方式：<?php echo $result["PreparationMode"] ?> 备注：
                                室常温保存不超过6小时，4℃保存不超过12小时<br/>
                                <?php
                                $PreparationMode = $result["PreparationMode"];
                                if ($PreparationMode == "粉剂") {
                                    echo "用法：温水冲服<br/>";
                                } else if ($PreparationMode == "管饲") {
                                    echo "输注速度：<br/>";
                                }

                                ?>
                        </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
    </tr>
</table>

</div>