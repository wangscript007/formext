var printLabel = {};

printLabel.printDesign = function () {
    printLabel.printInit();
    LODOP.PRINT_DESIGN();
}

printLabel.printSetup = function () {
    printLabel.printInit();
    LODOP.PRINT_SETUP();
}

printLabel.preview = function () {
    printLabel.printInit();
    LODOP.PREVIEW();
}

printLabel.print = function () {
    printLabel.printInit();
    LODOP.PRINT();
}

printLabel.printInit = function(){
    LODOP = getLodop();

    LODOP.PRINT_INITA(0,0,"90.01mm","50.01mm","标签打印");
    LODOP.SET_PRINT_PAGESIZE(2,900,500,"");
    var strStyle=  document.getElementById("cssPrint").outerHTML;//"<style> table,td,th {border-width: 1px;border-style: solid;border-collapse: collapse}</style>"

    LODOP.ADD_PRINT_HTM("1.01mm","1.01mm","85.01mm","10mm",strStyle + document.getElementById("divBaseInfo").outerHTML);

    LODOP.ADD_PRINT_TABLE("11.59mm","1.01mm","85.01mm","31mm",strStyle + document.getElementById("tblNutrientadvicedetail").outerHTML);
    LODOP.SET_PRINT_STYLEA(0,"Vorient",3);
    LODOP.SET_PRINT_STYLEA(0,"Offset2Top","-10mm"); //设置次页偏移把区域向上扩

    LODOP.ADD_PRINT_TEXT("43.6mm","1.32mm","21.17mm","5.29mm","第#页/共&页");
    LODOP.SET_PRINT_STYLEA(0,"ItemType",2);
    LODOP.SET_PRINT_STYLEA(0,"LinkedItem",-1);

    LODOP.SET_SHOW_MODE("LANDSCAPE_DEFROTATED",1);//横向时的正向显示

}

