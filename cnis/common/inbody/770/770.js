var inbody770 = {};
inbody770.urlParams = util.urlToObject(window.location.search);
inbody770.patient = null;
inbody770.report = null;

$(function ($) {

    util.bootstrapLodop(3, function(){

        if(util.printerSetting.PrinterName){
            $("#printerName").html(util.printerSetting.PrinterName);
        }else{
            $("#printerName").html("#未设置#");
        }

        var timer1 = window.setInterval(function () {
            if(inbody770.patient && inbody770.report){

                window.clearInterval(timer1);
                if ($("#printerName").html() == "#未设置#") {
                    if (confirm("未设置打印机，是否输出到默认打印机？")) {
                        $("#preview").click();
                    }
                } else {
                    $("#preview").click();
                }    
            }
        }, 500);

    });

    if(!inbody770.urlParams || typeof(inbody770.urlParams.reportId) == "undefined"){
        alert("未获取到报告ID！");
        return;
    }

    var sql = "select a.*,b.Height,b.HospitalizationNumber,date_format(c.DateOfBirth,'%Y-%m-%d') DateOfBirth from inbodyreport a inner join patienthospitalizebasicinfo b on a.PatientHospitalize_DBKey = b.PatientHospitalize_DBKey inner join patientbasicinfo c on c.PATIENT_DBKEY = b.PATIENT_DBKEY where  a.InBodyReport_DBKey = " + inbody770.urlParams.reportId + ";";
    $.getJSON(pageExt.libPath + "query.php", { sql: sql }, function (data, status, xhr) {
        inbody770.patient = data[0];
    });

    sql = "select a.*,b.ItemName from inbodyresult a inner join inbodyitem b on a.ItemCode = b.ItemCode and b.InbodyModel = '770' where InBodyReport_DBKey = " + inbody770.urlParams.reportId + ";";
    $.getJSON(pageExt.libPath + "query.php", { sql: sql }, function (data, status, xhr) {
        inbody770.report = data;
    });


});
    

inbody770.printSetting = function () {
    window.open(pageExt.libPath + "printerSet.php");
}

inbody770.printDesign = function () {
    inbody770.printLoad(1);
}

inbody770.printSetup = function () {
    inbody770.printLoad(2);
}

inbody770.preview = function () {
    inbody770.printLoad(3);
}

inbody770.print = function () {

    inbody770.printLoad(4);
    // alert("请等待打印完毕后，再关闭该页面！");
}

inbody770.printLoad = function (flag) {
    // LODOP = getLodop();

    inbody770.createPrintPage();
    if (flag == 1) {
        LODOP.PRINT_DESIGN();
        return false;
    } else if (flag == 2) {
        LODOP.PRINT_SETUP();
        return false;
    } else if (flag == 3) {
        LODOP.PREVIEW();
        return false;
    } else if (flag == 4) {
        LODOP.PRINT();
    }
}

inbody770.createPrintPage = function () {

    LODOP.PRINT_INITA(0, 0, "210mm", "297mm", "InBody770报告纸打印");
    if (util.printerSetting.PrinterName == "#未设置#") {
        $("#lsMsg").html("尚未设置默认的人体成分报告打印机！");
    } else {
        if (!LODOP.SET_PRINTER_INDEXA(util.printerSetting.PrinterName)) {
            $("#lsMsg").html("未检测到该打印机，将输出到默认打印机！");
        }
    }

    //LODOP.SET_PRINT_PAGESIZE(0,0,0,getSelectedPageSize());
    LODOP.SET_PRINT_PAGESIZE(util.printerSetting.Orient, util.printerSetting.PageWidth, util.printerSetting.PageHeigth, util.printerSetting.PageName);

    LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='../通用模板.jpg'>");
    LODOP.SET_SHOW_MODE("BKIMG_WIDTH","210mm");
    LODOP.SET_SHOW_MODE("BKIMG_HEIGHT","297mm");
    LODOP.SET_SHOW_MODE("BKIMG_IN_PREVIEW",true);
    LODOP.SET_SHOW_MODE("BKIMG_PRINT",true);    
    LODOP.SET_SHOW_MODE("SHOW_SCALEBAR",true);  //语句控制显示标尺
    LODOP.SET_SHOW_MODE("PREVIEW_NO_MINIMIZE",true);    //设置预览窗口禁止最小化，并始终在各个窗口的最前面        
    LODOP.SET_PRINT_MODE("POS_BASEON_PAPER", true); //是否控制位置基点，true时，对套打有利        
    LODOP.SET_PRINT_MODE("RESELECT_PRINTER",true); //是否可以重新选择打印机
    
    LODOP.SET_PRINT_STYLE("FontName", "微软雅黑");
    LODOP.SET_PRINT_STYLE("FontSize", "10.5");



    LODOP.ADD_PRINT_TEXT(100,64,100,20,inbody770.patient.HospitalizationNumber); //ID
    LODOP.ADD_PRINT_TEXT(119,64,100,20,"(" + inbody770.patient.PatientName + ")"); //姓名
    LODOP.ADD_PRINT_TEXT(110,193,100,20,inbody770.report[4].ItemValue + "cm"); //身高
    LODOP.ADD_PRINT_TEXT(107,269,100,20,inbody770.report[5].ItemValue); //年龄
    LODOP.ADD_PRINT_TEXT(122,268,100,20,"(" + inbody770.patient.DateOfBirth + ")"); //生日
    LODOP.ADD_PRINT_TEXT(108,337,100,20,inbody770.patient.Gender == "M" ? "男" : "女"); //性别
    LODOP.ADD_PRINT_TEXT(112,374,166,20,inbody770.patient.TestTime); //测试时间
    // LODOP.ADD_PRINT_TEXT(116,389,150,20,inbody770.patient.TestTime.split(" ")[1]);
    // LODOP.ADD_PRINT_TEXT(209,697,100,20,inbody770.toFixed2(1) + "kg");
    // LODOP.ADD_PRINT_TEXT(208,617,100,20,inbody770.toFixed2(9) + "kg");
    // LODOP.ADD_PRINT_TEXT(208,548,100,20,inbody770.toFixed2(8) + "kg");
    // LODOP.ADD_PRINT_TEXT(191,476,100,20,inbody770.toFixed2(7) + "kg");
    // LODOP.ADD_PRINT_TEXT(185,304,100,20,inbody770.range(70, 69));
    // LODOP.ADD_PRINT_TEXT(205,304,100,20,inbody770.range(72, 71));
    // LODOP.ADD_PRINT_TEXT(226,303,100,20,inbody770.range(74, 73));
    // LODOP.ADD_PRINT_TEXT(247,303,100,20,inbody770.range(76, 75));
    // LODOP.ADD_PRINT_TEXT(267,303,100,20,inbody770.range(91, 92));
    // LODOP.ADD_PRINT_TEXT(184,230,50,20,inbody770.toFixed2(2));
    // LODOP.ADD_PRINT_TEXT(204,230,50,20,inbody770.toFixed2(3));
    // LODOP.ADD_PRINT_TEXT(225,229,50,20,inbody770.toFixed2(4));
    // LODOP.ADD_PRINT_TEXT(246,229,50,20,inbody770.toFixed2(5));
    // LODOP.ADD_PRINT_TEXT(266,229,50,20,inbody770.toFixed2(6));
    // LODOP.ADD_PRINT_TEXT(338,227,50,20,inbody770.toFixed2(1));
    // LODOP.ADD_PRINT_TEXT(363,227,50,20,inbody770.toFixed2(12));
    // LODOP.ADD_PRINT_TEXT(387,226,50,20,inbody770.toFixed2(6));
    // LODOP.ADD_PRINT_TEXT(414,226,50,20,inbody770.toFixed2(16));
    // LODOP.ADD_PRINT_TEXT(443,226,100,20,inbody770.toFixed2(15));
    // LODOP.ADD_PRINT_TEXT(339,301,100,20,inbody770.range(78, 77));
    // LODOP.ADD_PRINT_TEXT(364,301,100,20,inbody770.range(80, 79));
    // LODOP.ADD_PRINT_TEXT(388,300,100,20,inbody770.range(91, 92));
    // LODOP.ADD_PRINT_TEXT(415,300,100,20,inbody770.range(84, 83));
    // LODOP.ADD_PRINT_TEXT(444,300,100,20,inbody770.range(82, 81));
    // LODOP.ADD_PRINT_TEXT(518,227,50,20,inbody770.toFixed2(18));
    // LODOP.ADD_PRINT_TEXT(544,227,50,20,inbody770.toFixed2(19));
    // LODOP.ADD_PRINT_TEXT(568,226,50,20,inbody770.toFixed2(20));
    // LODOP.ADD_PRINT_TEXT(594,226,50,20,inbody770.toFixed2(21));
    // LODOP.ADD_PRINT_TEXT(623,226,50,20,inbody770.toFixed2(22));
    // LODOP.ADD_PRINT_TEXT(519,301,100,20,inbody770.range(130, 131));
    // LODOP.ADD_PRINT_TEXT(545,301,100,20,inbody770.range(130, 131));
    // LODOP.ADD_PRINT_TEXT(569,300,100,20,inbody770.range(132, 133));
    // LODOP.ADD_PRINT_TEXT(595,300,100,20,inbody770.range(134, 135));
    // LODOP.ADD_PRINT_TEXT(624,300,100,20,inbody770.range(134, 135));
    // LODOP.ADD_PRINT_TEXT(183,169,50,20,"L");
    // LODOP.ADD_PRINT_TEXT(203,169,50,20,"L");
    // LODOP.ADD_PRINT_TEXT(224,169,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(246,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(267,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(338,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(363,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(387,167,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(414,167,50,20,"%");
    // LODOP.ADD_PRINT_TEXT(443,167,100,20,"kg/m²");
    // LODOP.ADD_PRINT_TEXT(519,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(545,168,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(569,167,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(595,167,50,20,"kg");
    // LODOP.ADD_PRINT_TEXT(624,167,50,20,"kg");
    // LODOP.ADD_PRINT_SHAPE(4,338,385,inbody770.rangeWidth(1, 78, 77),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,362,385,inbody770.rangeWidth(12, 80, 79),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,386,385,inbody770.rangeWidth(6, 91, 92),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,412,385,inbody770.rangeWidth(16, 84, 83),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,441,385,inbody770.rangeWidth(15, 82, 81),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,517,385,inbody770.rangeWidth(18, 130, 131),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,543,385,inbody770.rangeWidth(19, 130, 131),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,567,385,inbody770.rangeWidth(20, 132, 133),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,593,385,inbody770.rangeWidth(21, 134, 135),12,0,1,"#808080");
    // LODOP.ADD_PRINT_SHAPE(4,621,385,inbody770.rangeWidth(22, 134, 135),12,0,1,"#808080");
    // var dzk = inbody770.loadDZk();
    // LODOP.ADD_PRINT_HTM("174.84mm","5.77mm","131.05mm","85.99mm", dzk);
}

inbody770.toFixed2 = function(index){
    return parseFloat(inbody770.report[index].ItemValue).toFixed(2);
}

inbody770.range = function(min, max){
    return parseFloat(inbody770.report[min].ItemValue).toFixed(2)  + "~" + parseFloat(inbody770.report[max].ItemValue).toFixed(2);
}

inbody770.rangeWidth = function(value, min, max){
    var w = inbody770.toFixed2(value);
    var s1 = inbody770.toFixed2(min);
    var s2 =inbody770.toFixed2(max);

    var dw = 89;//mm 低标准的范围宽度
    var ww = 68;//mm 标准值的范围宽度

    var width = dw + (w - s1) * ww / (s2 - s1);

    return width;
}

inbody770.loadDZk = function(){
    var dzkObj = {};
    for(var i = 39; i <= 68; i++){
        dzkObj[inbody770.report[i].ItemName] = inbody770.toFixed2(i);
    }
    var style = $("#style1")[0].outerHTML;
    var dzk = $("#divDzk").html().format(dzkObj);
    return style + dzk;
}