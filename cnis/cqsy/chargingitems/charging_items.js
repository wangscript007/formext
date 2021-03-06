var charging = {};

$(function ($) {

    MP.on('MP_RECIPE_PRODUCT_CHANGE', function (data) {

        $("#productName").text(data.productName);
        $("#productName").attr("RecipeAndProduct_DBKey", data.RecipeAndProduct_DBKey);

        //回显制剂对应的收费项目
        var sql = "select * from chargingitemsrelation where RecipeAndProduct_DBKey = '{RecipeAndProduct_DBKey}'";
        var sql2 = sql.format({RecipeAndProduct_DBKey:data.RecipeAndProduct_DBKey});
        $(":checkbox").prop("checked", false);
        $.getJSON(formExt.libPath + "query.php", {sql : sql2}, function( data, status, xhr ) {
            for(j = 0; j < data.length; j++) {   
                $(":checkbox[value='" + data[j].ChargingItemID + "']").prop("checked", "true");
            } 
        });   


    });
    
});

//保存对应关系
charging.saveRelation = function(){

    var RecipeAndProduct_DBKey = $("#productName").attr("RecipeAndProduct_DBKey");
    if(!RecipeAndProduct_DBKey){    
        alert("请在左侧列表中点选肠内制剂名称！");
        return;
    }

    $.ajaxSetup({
        async: false
    });

    //先删除
    var sql = "delete from chargingitemsrelation where RecipeAndProduct_DBKey = " + RecipeAndProduct_DBKey;
    $.post(formExt.libPath + "exec.php", { sql:sql },function(data){
        console.log(data);
    },"json");

    //遍历保存明细
    $("input:checked").each(function(){

        var ChargingItemID = $(this).val();
        var sql2 = "insert into chargingitemsrelation (RecipeAndProduct_DBKey, ChargingItemID) values('{RecipeAndProduct_DBKey}', '{ChargingItemID}') ON DUPLICATE KEY UPDATE ChargingItemID=VALUES(ChargingItemID);";
        var sql2 = sql2.format({RecipeAndProduct_DBKey:RecipeAndProduct_DBKey, ChargingItemID:ChargingItemID});

        $.post(formExt.libPath + "exec2.php", { sql:sql2 },function(data){
            console.log(data);
        },"json");

    });

    $.ajaxSetup({
        async: true
    });

    //alert("保存成功！");
    var dialog1 = util.initDialog({
        dialogID:"dialog1",
        context:"保存成功！",
        countdown:1000,
        cfg:{
            title:"1秒后关闭",
            position: { my: "left top", at: "left top", of: $("#mainGridTable")  },
        },
    });
    dialog1.dialog("open");
}

charging.newChargingItem = function(){
    var sql2 = "insert into chargingitems(ChargingItemName, SortNo, Enabled) values ('收费项目', '-1', 1);";

    $.ajaxSetup({
        async: false
    });

    $.post(formExt.libPath + "exec2.php", { sql:sql2 },function(data){
        console.log(data);
    },"json");

    $.ajaxSetup({
        async: true
    });

    self.location.reload();
}