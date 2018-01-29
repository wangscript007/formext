


 /*
 两种调用方式
 var template1="我是{0}，今年{1}了";
 var template2="我是{name}，今年{age}了";
 var result1=template1.format("loogn",22);
 var result2=template2.format({name:"loogn",age:22});
 两个结果都是"我是loogn，今年22了"
 */
String.prototype.format = function (args) {
    var result = this;
    if (arguments.length > 0) {
        if (arguments.length == 1 && typeof (args) == "object") {
            for (var key in args) {
                args[key] = args[key] ? args[key] : "";
                var reg = new RegExp("({" + key + "})", "g");
                result = result.replace(reg, args[key]);
            }
        }
        else {
            for (var i = 0; i < arguments.length; i++) {
                arguments[key] = arguments[key] ? arguments[key] : "";
                var reg = new RegExp("({)" + i + "(})", "g");
                result = result.replace(reg, arguments[i]);
            }
        }
    }
    return result;
}


/*   
jQuery实现点击复选框即高亮显示选中行 全选、反选   
*/  
$(function($){
    $.fn.extend({  
        "alterBgColor": function (options) {  
            var prevselitem = null;  
            //设置默认值  
            options = $.extend({  
                style: "4",  
                odd: "odd", /* 偶数行样式*/  
                even: "even", /* 奇数行样式*/  
                over: "over", /* 鼠标掠过*/  
                selected: "selected" /* 选中行样式*/  
            }, options);  
            //交替背景  
            $("tbody>tr:odd", this).addClass(options.odd);  
            $("tbody>tr:even", this).addClass(options.even);  
            //鼠标移动背景  
            $("tbody>tr", this).hover(function () { $(this).addClass(options.over); }, function () { $(this).removeClass(options.over); });  
            //初始背景 (判断第一列中有被选中的话就高亮)  
            $("tbody>tr td:first-child:has(:checked)", this).parents('tr').addClass(options.selected);  

            //样式1  
            if (options.style == "1") {  
                $("tbody>tr", this).click(function () { $(this).toggleClass(options.selected); });  
            }  

            //样式2  
            if (options.style == "2") {  
                $('tbody>tr', this).click(function () {  
                    //判断当前是否选中  
                    var hasSelected = $(this).hasClass(options.selected);  
                    //如果选中，则移出selected类，否则就加上selected类  
                    $(this)[hasSelected ? "removeClass" : "addClass"](options.selected)  
                    //查找内部的checkbox,设置对应的属性。  
                .find(':checkbox:first').prop('checked', !hasSelected);  
                });  
            }  

            //样式3  
            if (options.style == "3") {  
                // 如果单选框默认情况下是选择的，则高色.  
                $('tbody>tr', this).click(function () {  
                    $(this).siblings().removeClass(options.selected); //除掉同胞的样式。  
                    $(this).addClass(options.selected).find(':radio:first').prop('checked', true);  
                });  
            }  

            //样式4  
            if (options.style == "4") {  
                //第一列 为多选  
                $('tbody>tr td:first-child', this).click(function () {  
                    var p = $(this).parents("tr");  
                    var hasSelected = p.hasClass(options.selected);  
                    p[hasSelected ? "removeClass" : "addClass"](options.selected).find(':checkbox:first').prop('checked', !hasSelected);  

                });  
                //其他列 为单选  
                $('tbody>tr td:not(:first-child)', this).click(function () {  
                    var p = $(this).parents("tr");  
                    p.addClass(options.selected).siblings().removeClass(options.selected).find(':checkbox:first').prop('checked', false);  
                    p.find(':checkbox:first').prop('checked', true);  
                });  
            }  


            //表头中的checkbox （全选 反选）  
            $("thead>tr th:first-child :checkbox:first", this).click(function () {  
                //判断当前是否选中  
                var hasSelected = $(this).prop('checked');  
                //如果选中，则移出selected类，否则就加上selected类  
                var tab = $(this).parents("table");  
                tab.find("tbody>tr")[!hasSelected ? "removeClass" : "addClass"](options.selected);  
                tab.find('tbody>tr td:first-child :checkbox').prop("checked", hasSelected ? true : false);  

            });  
            return this;  //返回this，使方法可链。  
        }  
    }); 
    $(".gridtable").alterBgColor({ style: "4" }); //可以链式操作  .find("th").css("color", "red")

    $.datepicker.regional['zh-CN'] = { 
        clearText: '清除', 
        clearStatus: '清除已选日期', 
        closeText: '关闭', 
        closeStatus: '不改变当前选择', 
        prevText: '< 上月', 
        prevStatus: '显示上月', 
        prevBigText: '<<', 
        prevBigStatus: '显示上一年', 
        nextText: '下月>', 
        nextStatus: '显示下月', 
        nextBigText: '>>', 
        nextBigStatus: '显示下一年', 
        currentText: '今天', 
        currentStatus: '显示本月', 
        monthNames: ['一月','二月','三月','四月','五月','六月', '七月','八月','九月','十月','十一月','十二月'], 
        monthNamesShort: ['一月','二月','三月','四月','五月','六月', '七月','八月','九月','十月','十一月','十二月'], 
        monthStatus: '选择月份', 
        yearStatus: '选择年份', 
        weekHeader: '周', 
        weekStatus: '年内周次', 
        dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'], 
        dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'], 
        dayNamesMin: ['日','一','二','三','四','五','六'], 
        dayStatus: '设置 DD 为一周起始', 
        dateStatus: '选择 m月 d日, DD', 
        dateFormat: 'yy-mm-dd', 
        firstDay: 1, 
        initStatus: '请选择日期', 
        isRTL: false}; 
        $.datepicker.setDefaults($.datepicker.regional['zh-CN']); 

});