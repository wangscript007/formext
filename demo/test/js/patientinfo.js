var patientinfo = {};

$(function($){

    //患者姓名
    var para = {
        id : "where_PatientName",
        source : null,
        urlsource : {
                url : formExt.libPath + "query.php",
                sql : "select CONCAT(PatientName, '  [ ', PatientNameFirstLetter, ' ]') label, PatientName value from patientbasicinfo where (PatientName like :term or PatientNameFirstLetter like :term2) limit 0, 30",
                requestPara : '{":term" : "%{0}%", ":term2" : "{0}%"}',    
                cache : false,
                cachedata : {},
            },
        delay : 1000,
        minLength : 2,
        multiple : false,
        category : false,
        renderitem : function( ul, item ) {
                return $( "<li>" )
                .append( "<div>" + item.label + "<br>" + item.value + "</div>" )
                .appendTo( ul );
            },
        select: function( event, ui ) {
            $( "#where_PatientName" ).val( ui.item.value );

/*                 $( "#project" ).val( ui.item.label );
                $( "#project-id" ).val( ui.item.value );
                $( "#project-description" ).html( ui.item.desc );
                $( "#project-icon" ).attr( "src", "images/" + ui.item.icon );
 */            
                return false;
            }
    };
    util.autocomplete(para);

    //科室
    var para = {
        id : "where_Department",
        source : null,
        urlsource : {
                url : formExt.libPath + "query.php",
                sql : "select CONCAT(DepartmentName, '  [ ', Department_DBKey, ' ]') label, Department_DBKey value, case when Department_DBKey > 170 then '分类1' else '分类2' end category from department where (DepartmentName like :term or Department_DBKey like :term2) limit 0, 30",
                requestPara : '{":term" : "%{0}%", ":term2" : "%{0}%"}',   
                cache : true, 
                cachedata : {},                
            },
        delay : 1000,
        minLength : 2,
        multiple : true,
        category : true,
        renderitem : null,
        select : null,
    };
    util.autocomplete(para);

    //性别
    var dataSource = [{label: "男", value: "M"}, {label: "女", value: "F"}];
    var para = {
        id : "where_Gender",        
        source : dataSource,
        urlsource : {
                url : "",
                sql : "",
                requestPara : '',
                cache : false,
                cachedata : {},                
            },
        delay : 1000,
        minLength : 1,
        multiple : false,
        category : false,
        renderitem: null,
        select : null,
    };
    util.autocomplete(para);
    

});

patientinfo.openInfo = function(){
    var str = formExt.getSelectRecords().selStr;

    alert(str);
}