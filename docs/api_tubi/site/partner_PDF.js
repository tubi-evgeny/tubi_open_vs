console.log("test start 7\n");

let user_info_json=localStorage.getItem("user_info_json");
let user_info_arr = JSON.parse(user_info_json);
console.table(user_info_arr);

//let orders_list = []; 
//let orders_arr = [];
let list = []; 
let arr = [];
let general_info = [];
let counterparty_info = [];
let counterparty_role_list = [];
let role_selected = "";
let whatDocsWriteToPDF = "";//order_list, order_info, invoice_list, invoice_info

let grey_200 = "rgb(216, 214, 214)";
let green_200 = "rgb(150, 241, 122)";

$.ajax({    
    //создаем url для отправки в .php
    url: 'partner_PDF.php',
    type: 'POST',
    cache: false,
    data: {'counterparty_info':0, 'taxpayer_id':user_info_arr[5]},
    dataType: 'html',
    beforeSend: function() {
        //ожидание пока функция выполняется кнопка не активна
        console.log('ajax test');
    },
    success: function(data) {
        console.log(data);
        //функция выполнена ответ получен
        counterparty_info = JSON.parse(data);
        console.table(counterparty_info);

        //document.getElementById('excel_data').innerHTML = table_output;
        $("#counterparty_info").val = counterparty_info['counterparty'];
        $("#counterparty_info_2").value = counterparty_info['counterparty'];
        //получить список ролей компании
        get_counterparty_role_list();
    }    
});

//скрыть блок кнопок выбора просмотра документов
$("#PDF_buttons").hide();

//показать только кнопки если есть в списке
$("#provider").hide();
$("#partner").hide();


$("#allert").on("click", function(){

    allert();    
});
//первое заполнение таблицы
$("#excel_file").on("click", function(){

    createStartExcel();    
});
$("#openPDF").on("click", function(){

    openPDF();    
});
$("#generatePDF").on("click", function(){

    generatePDF();    
});
$("#printPDF").on("click", function(){

    printPDF();    
});
$("#orders").on("click", function(){
    if(role_selected == "partner_warehouse"){
        orders();
    }else{
        alert("Для поставщика временно документы не доступны, мы работаем над решением этой задачи");
    }        
});
$("#invoices").on("click", function(){
    if(role_selected == "partner_warehouse"){
        invoices(); 
    }else{
        alert("Для поставщика временно документы не доступны, мы работаем над решением этой задачи");
    }       
});
$("#provider").on("click", function(){

      console.log("provider");
      document.getElementById('provider').style.background = green_200;
      document.getElementById('partner').style.background = grey_200;
      role_selected = "provider_business";
      //показать блок кнопок выбора просмотра документов
        $("#PDF_buttons").show();
});
$("#partner").on("click", function(){

    console.log("partner"); 
    document.getElementById('provider').style.background = grey_200;
    document.getElementById('partner').style.background = green_200;
    role_selected = "partner_warehouse";
    //показать блок кнопок выбора просмотра документов
    $("#PDF_buttons").show();
});
function allert(){
    console.log("test button "+mb_ucfirst(counterparty_info['counterparty_id'])+' '+counterparty_info['companyInfoString']);    
}
function openPDF(){
    myCreatePDF();
    //pdfMake.createPdf(myCreatePDF()).open({}, window);
}
function generatePDF(){
    
    pdfMake.createPdf(myCreatePDF()).download("Score_Details.pdf");
}
function printPDF(){
    pdfMake.createPdf(myCreatePDF()).print({}, window);
}
//получить список ролей компании
function get_counterparty_role_list(){
    $.ajax({    
        //создаем url для отправки в .php
        url: 'partner_PDF.php',
        type: 'POST',
        cache: false,
        data: {'receive_counterparty_role_list':0
            , 'counterparty_id': counterparty_info['counterparty_id']},
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            //console.log('ajax test');
        },
        success: function(data) {
            console.log(data);
            //функция выполнена ответ получен
            counterparty_role_list = JSON.parse(data);
            console.table(counterparty_role_list);

            console.log("length: "+counterparty_role_list.length);
            for(let i=0;i < counterparty_role_list.length;i++){
                if(counterparty_role_list[i] == 'provider_business'){
                    $("#provider").show();
                }else if(counterparty_role_list[i] == 'partner_warehouse'){
                    $("#partner").show();
                }
            }
        }    
    });        
}
function invoices(){
    console.log("test invoices");
    $.ajax({
        //создаем url для отправки в .php
        url: 'partner_PDF.php',
        type: 'POST',
        cache: false,
        data: {'receive_partner_invoices_list':0
                , 'counterparty_id': counterparty_info['counterparty_id'] 
                , 'document_name':'товарная накладная' },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#invoices").prop("disabled", true);
        },
        success: function(data) {
            list = [];  
            //console.log(data);
            //функция выполнена ответ получен
            //alert(data);

            list = JSON.parse(data);
            
            console.table(list);
            createInvoiceTableList(list);
          
            $("#invoices").prop("disabled", false);
        }
    });
}
function orders(){    
    $.ajax({
        //создаем url для отправки в .php
        url: 'partner_PDF.php',
        type: 'POST',
        cache: false,
        data: {'receive_partner_orders_list':0
              ,'counterparty_id': counterparty_info['counterparty_id'] },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#orders").prop("disabled", true);
        },
        success: function(data) {
            list = [];  
            //console.log(data);
            //функция выполнена ответ получен
            //alert(data);

            list = JSON.parse(data);
            
            console.table(list);
            createTable(list);
          
            $("#orders").prop("disabled", false);
        }
    });
}
//показать выбраный заказ
function getOrderThis(id_str){
    $.ajax({
        //создаем url для отправки в .php
        url: 'partner_PDF.php',
        type: 'POST',
        cache: false,
        data: {'receive_order_buyer_this':0, 'order_id':id_str },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            //$("#orders").prop("disabled", true);
        },
        success: function(data) {
            list = [];  
            //console.log(data);
            //функция выполнена ответ получен
            //alert(data);

            list = JSON.parse(data);            
            //console.table(list[0]);
            //console.table(list);
            createThisOrderTable(list);
          
        }
    });
}
//показать выбраный заказ
function getInvoceThis(doc_num, invoice_key_id){
    $.ajax({
        //создаем url для отправки в .php
        url: 'partner_PDF.php',
        type: 'POST',
        cache: false,
        data: {'receive_invoice_buyer_this':0, 'doc_num':doc_num, 'invoice_key_id':invoice_key_id },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            //$("#orders").prop("disabled", true);
        },
        success: function(data) {
            list = [];  
            console.log(data);
            //функция выполнена ответ получен
            //alert(data);

            list = JSON.parse(data);            
            console.table(list[0]);
            console.table(list);
            createThisInvoiceTable(list);
          
        }
    });
}
function myCreatePDF(){
    let dd;
    if(whatDocsWriteToPDF == 'order_list'){
        alert('Документ не может быть напечатан');
    }else if(whatDocsWriteToPDF == 'order_info'){
        dd = createOrderPDF();
        pdfMake.createPdf(dd).open({}, window);
    }else if(whatDocsWriteToPDF == 'invoice_list'){
        alert('Документ не может быть напечатан');
    }else if(whatDocsWriteToPDF == 'invoice_info'){
        dd = createInvoicePDF();
        pdfMake.createPdf(dd).open({}, window);
    }
    return dd;
}
function createOrderPDF(){
    //получить колличество столбцов
    let len = [];
    let arr_length = 0;
    if(arr.length > 0){
        arr_length = arr[0].length;
    }
    for(var row = 0; row < arr_length; row++){
        len.push(row);
    }
    console.info(JSON.stringify(len));
    var dd = {
        content: [
            { text: document.getElementById('doc_name').innerHTML , style: 'header1', alignment: 'center' },
            ,{ text: 'Дата заказа:  '+general_info[0].created_date , style: 'header', alignment: 'right' },
            ,{ text: 'Дата получения:  '+general_info[0].get_date , style: 'header', alignment: 'right' },
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Поставщик(представитель) '
                    + general_info[0].out_companyInfoString_short , style: 'header' },
            ,{ text: 'Склад получения:  '
                    +general_info[0].out_warehouseInfoString+' ('+general_info[0].out_signboard+')' , style: 'header' },
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Получатель: '
                    + general_info[0].in_companyInfoString_short , style: 'header', margin: [0,0,0,5 ]  },

            tableOrder(arr, len),

            ,{ text: 'Сумма '+general_info[0].order_summ , style: [ 'header1', 'anotherStyle' ] , margin: [0,5 ] },
        ],
        styles: {
            header1: {
            fontSize: 15,
            bold: true
            },
            anotherStyle: {
            italics: true,
            alignment: 'right'
            }
        }
    }
    return dd;
}
function tableOrder(data, columns) {
    return {
        table: {
            headerRows: 1,
            body: buildOrderTableBody(data, columns)
        }
    };
}
function buildOrderTableBody(data, columns) {
    var body = [];
    data.forEach(function(row) {
        var dataRow = [];
        columns.forEach(function(column) {
            if(column == 0 || column == 6){
                if(row[column] == 1){
                    dataRow.push('да');
                }else if(row[column] == 0){
                    dataRow.push('нет');
                }else{
                    dataRow.push(row[column].toString());
                }
            }else{
                dataRow.push(row[column].toString());
            }            
        })
        body.push(dataRow);
    });
    return body;
}
function createInvoiceTableList(sheet_data){
    whatDocsWriteToPDF = "invoice_list";//order_list, order_info, invoice_list, invoice_info
    arr = [];
    let arr_str = [];

    if(sheet_data.length > 0)
    {
        var table_output = '<table class="table table-striped table-border">';
        //вставить имена столбцов
        table_output +='<tr>';
            table_output +='<td>Дата</td>'; 
            table_output +='<td>Номер</td>';                   
            table_output +='<td>Покупатель</td>';  
            table_output +='<td>Сумма</td>';  
            table_output +='<td>Склад</td>';  
            table_output += '</tr>';

            arr_str = ['Дата', 'Номер','Покупатель','Сумма','Склад'];
            arr.push(arr_str);

        for(var row = 0; row < sheet_data.length; row++)
        {
            arr_str = [sheet_data[row]['created_date'], sheet_data[row]['document_num']
                            ,sheet_data[row]['in_companyInfoString_short']
                            ,sheet_data[row]['invoice_summ'],sheet_data[row]['out_warehouseInfoString']];
            arr.push(arr_str);

            table_output +='<tr>';           
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['created_date']+'</div></td>'; 
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['document_num']+'</div></td>';                   
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['in_companyInfoString_short']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['invoice_summ']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['out_warehouseInfoString']+'</div></td>';  
           
            table_output += '</tr>';            
        }
            
        table_output += '</table>';
        //console.info(JSON.stringify(orders_list));

        document.getElementById('doc_name').innerHTML = "Расходные накладные";
        document.getElementById('excel_data').innerHTML = table_output;

        //повесить слушатель
        var elems = document.getElementsByClassName('inputs');
        for(var i = 0; i < elems.length; i++) {
            elems[i].addEventListener('click', function(){
                let id_str = String(this.id);
                if(id_str.length != 0){
                    console.log('input id = '+id_str);
                    console.log('invoice = '+list[id_str].document_num+' invoice_key_id = '+list[id_str].invoice_key_id);
                    //показать выбранную накладную
                    getInvoceThis(list[id_str].document_num, list[id_str].invoice_key_id);
                }

            }, false); 
        }        
    }    
}
function createTable(sheet_data){    
    whatDocsWriteToPDF = "order_list";//order_list, order_info,  
    arr = [];
    let arr_str = [];

    if(sheet_data.length > 0)
    {
        var table_output = '<table class="table table-striped table-border">';
        //вставить имена столбцов
        table_output +='<tr>';
            table_output +='<td>Отгружен</td>'; 
            table_output +='<td>Дата</td>'; 
            table_output +='<td>Номер</td>';                   
            table_output +='<td>Компания</td>';  
            table_output +='<td>Сумма</td>';  
            table_output +='<td>Дата отгрузки</td>';  
            table_output +='<td>Заказ удален</td>';
            table_output += '</tr>';

            arr_str = ['Отгружен', 'Дата', 'Номер','Компания','Сумма','Дата отгрузки','Заказ удален'];
            arr.push(arr_str);

        for(var row = 0; row < sheet_data.length; row++)
        {
            arr_str = [sheet_data[row]['executed'], sheet_data[row]['created_date'],sheet_data[row]['order_id']
                            ,sheet_data[row]['in_companyInfoString_short'],sheet_data[row]['order_summ']
                            ,sheet_data[row]['get_date'],sheet_data[row]['order_deleted']];
            arr.push(arr_str);

            table_output +='<tr>';
            if(sheet_data[row]['executed'] == 1){
                table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">да</div></td>';
            }else{
                table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">нет</div></td>';
            }             
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['created_date']+'</div></td>'; 
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['order_id']+'</div></td>';                   
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['in_companyInfoString_short']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['order_summ']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+sheet_data[row]['get_date']+'</div></td>';  
            if(sheet_data[row]['order_deleted'] == 1){
                table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">да</div></td>';
            }else{
                table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">нет</div></td>';
            }
            table_output += '</tr>';
            
        }
            
        table_output += '</table>';
        //console.info(JSON.stringify(orders_list));

        document.getElementById('doc_name').innerHTML = "Список заказов";
        document.getElementById('excel_data').innerHTML = table_output;

        //повесить слушатель
        var elems = document.getElementsByClassName('inputs');
        for(var i = 0; i < elems.length; i++) {
            elems[i].addEventListener('click', function(){
                let id_str = String(this.id);
                if(id_str.length != 0){
                    console.log('input id = '+id_str);
                    console.log('order = '+sheet_data[id_str].order_id);
                    //показать выбраный заказ
                    getOrderThis(sheet_data[id_str].order_id);
                }

            }, false); 
        }
       
    }
}
//обработать и показать накладную
function createThisInvoiceTable(list){
    whatDocsWriteToPDF = "invoice_info";//order_list, order_info, invoice_list, invoice_info
    if(list.length > 0)
    {
        general_info = list[0];
        let invoice_list = [];
        for(let i=1; i < list.length;i++){
            invoice_list[i-1] = list[i];
        }
        console.table(general_info);
        console.table(invoice_list);

        arr = [];
        let arr_str = [];
        var table_output = '<table class="table table-striped table-border">';
        //вставить имена столбцов
        table_output +='<tr>';
            table_output +='<td>Наименование</td>'; 
            table_output +='<td>Количество</td>'; 
            table_output +='<td>Цена</td>';                   
            table_output +='<td>Сумма</td>';  
            table_output += '</tr>';

            arr_str = ['Наименование', 'Количество', 'Цена','Сумма'];
            arr.push(arr_str);

        for(var row = 0; row < invoice_list.length; row++)
        {
            arr_str = [invoice_list[row]['description_docs']
                        ,invoice_list[row]['quantity'],invoice_list[row]['full_price']
                        ,invoice_list[row]['position_summ']];
            arr.push(arr_str);

            table_output +='<tr>';
                         
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+invoice_list[row]['description_docs']+'</div></td>'; 
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+invoice_list[row]['quantity']+'</div></td>';                   
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+invoice_list[row]['full_price']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+invoice_list[row]['position_summ']+'</div></td>';  
           
            table_output += '</tr>';            
        }           
        table_output +='<tr>';                         
            table_output +='<td></td>'; 
            table_output +='<td></td>';                   
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">Сумма</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+general_info[0].invoice_summ+'</div></td>';             
        table_output += '</tr>';   
        table_output += '</table>';
        //console.info(JSON.stringify(orders_list));
        document.getElementById('doc_name').innerHTML = "Товарная Накладная № "+general_info[0]['docNum'];
        document.getElementById('excel_data').innerHTML = table_output;
    }
}
//обработать и показать заказ
function createThisOrderTable(list){
    whatDocsWriteToPDF = "order_info";//order_list, order_info, invoice_list, invoice_info
    if(list.length > 0)
    {
        general_info = list[0];
        let order_list = [];
        for(let i=1; i < list.length;i++){
            order_list[i-1] = list[i];
        }
        console.table(general_info);
        console.table(order_list);

        arr = [];
        let arr_str = [];
        var table_output = '<table class="table table-striped table-border">';
        //вставить имена столбцов
        table_output +='<tr>';
            table_output +='<td>Наименование</td>'; 
            table_output +='<td>Количество</td>'; 
            table_output +='<td>Цена</td>';                   
            table_output +='<td>Сумма</td>';  
            table_output += '</tr>';

            arr_str = ['Наименование', 'Количество', 'Цена','Сумма'];
            arr.push(arr_str);

        for(var row = 0; row < order_list.length; row++)
        {
            arr_str = [order_list[row]['product_info']
                        +" ("+ order_list[row]['product_name_from_provider']+")"
                        ,order_list[row]['quantity'],order_list[row]['full_price']
                        ,order_list[row]['position_summ']];
            arr.push(arr_str);

            table_output +='<tr>';
                         
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'
                    +order_list[row]['product_info']+" ("+ order_list[row]['product_name_from_provider']+")"+'</div></td>'; 
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+order_list[row]['quantity']+'</div></td>';                   
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+order_list[row]['full_price']+'</div></td>';  
            table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+order_list[row]['position_summ']+'</div></td>';  
           
            table_output += '</tr>';            
        }
        table_output +='<tr>';
                         
        table_output +='<td></td>'; 
        table_output +='<td></td>';                   
        table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">Сумма</div></td>';  
        table_output +='<td><div id="'+(row)+'" class="inputs"  type="text">'+general_info[0].order_summ+'</div></td>';  
       
        table_output += '</tr>'; 
        table_output += '</table>';
        //console.info(JSON.stringify(orders_list));

        document.getElementById('doc_name').innerHTML = "Заказ № "+general_info[0]['order_id'];
        document.getElementById('excel_data').innerHTML = table_output;
    }
}
//
function createInvoicePDF(){
     //получить колличество столбцов
     let len = [];
     let arr_length = 0;
     if(arr.length > 0){
         arr_length = arr[0].length;
     }
     for(var row = 0; row < arr_length; row++){
         len.push(row);
     }
     console.info(JSON.stringify(len));
    var dd = {
        content: [
            { text: document.getElementById('doc_name').innerHTML , style: 'header1', alignment: 'center' },
            ,{ text: 'Дата:  '+general_info[0].date_created_doc , style: 'header', alignment: 'right' },            
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Поставщик(представитель) '
                    + general_info[0].out_companyInfoString , style: 'header' },
            ,{ text: 'Склад получения:  '
                    +general_info[0].out_warehouseInfoString+' ('+general_info[0].out_signboard+')' , style: 'header' },
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Получатель: '
                    + general_info[0].in_companyInfoString , style: 'header' },
            ,{ text: 'Место получения: '
                    + general_info[0].in_warehouseInfoString , style: 'header', margin: [0,0,0,5 ]  },

            tableInvoice(arr, len),

            ,{ text: 'Сумма: '+ general_info[0].invoice_summ, style: [ 'header1', 'anotherStyle' ] , margin: [0,5 ] },
            ,{ text: 'Сумма: '+ general_info[0].invoice_summ_text, style: 'header' , margin: [0,5 ] },
            ,{ text: 'Выдал ______________________________     Получил ___________________________' , style: 'header', margin: [0,15 ] },
        ],
        styles: {
            header1: {
            fontSize: 15,
            bold: true
            },
            anotherStyle: {
            italics: true,
            alignment: 'right'
            }
        }
    }
    return dd;
}

function tableInvoice(data, columns) {
    return {
        table: {
            headerRows: 1,
            body: buildInvoiceTableBody(data, columns)
        }
    };
}
function buildInvoiceTableBody(data, columns) {
    var body = [];

    //body.push(columns);

    data.forEach(function(row) {
        var dataRow = [];

        columns.forEach(function(column) {
            dataRow.push(row[column].toString());
        })

        body.push(dataRow);
    });

    return body;
}
/*
function createOrderListPDF(){
    //получить колличество столбцов
    let len = [];
    let arr_length = 0;
    if(arr.length > 0){
        arr_length = arr[0].length;
    }
    for(var row = 0; row < arr_length; row++){
        len.push(row);
    }
    console.info(JSON.stringify(len));
    var dd = {
        content: [
            { text: document.getElementById('doc_name').innerHTML , style: 'header1', alignment: 'center' },
            ,{ text: 'Дата заказа:  '+general_info[0].created_date , style: 'header', alignment: 'right' },
            ,{ text: 'Дата получения:  '+general_info[0].get_date , style: 'header', alignment: 'right' },
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Поставщик(представитель) '
                    + general_info[0].out_companyInfoString_short , style: 'header' },
            ,{ text: 'Склад получения:  '
                    +general_info[0].out_warehouseInfoString+' ('+general_info[0].out_signboard+')' , style: 'header' },
            ,{ text: '___________________________________________________' , style: 'header' },
            ,{ text: 'Получатель: '
                    + general_info[0].in_companyInfoString_short , style: 'header', margin: [0,0,0,5 ]  },

            tableOrder(arr, len),

            ,{ text: 'Сумма '+general_info[0].order_summ , style: [ 'header1', 'anotherStyle' ] , margin: [0,5 ] },
        ],
        styles: {
            header1: {
            fontSize: 15,
            bold: true
            },
            anotherStyle: {
            italics: true,
            alignment: 'right'
            }
        }
    }
    return dd;
}
function tableOrder(data, columns) {
    return {
        table: {
            headerRows: 1,
            body: buildOrderTableBody(data, columns)
        }
    };
}
function buildOrderTableBody(data, columns) {
    var body = [];
    data.forEach(function(row) {
        var dataRow = [];
        columns.forEach(function(column) {
            if(column == 0 || column == 6){
                if(row[column] == 1){
                    dataRow.push('да');
                }else if(row[column] == 0){
                    dataRow.push('нет');
                }else{
                    dataRow.push(row[column].toString());
                }
            }else{
                dataRow.push(row[column].toString());
            }            
        })
        body.push(dataRow);
    });
    return body;
}*/
/*
function createStartExcel(){
    
    const excel_file = document.getElementById('excel_file');         

    excel_file.addEventListener('change', (event) =>{

        var reader = new FileReader();

        reader.readAsArrayBuffer(event.target.files[0]);

        reader.onload = function(event){

            var data = new Uint8Array(reader.result);

            var work_book = XLSX.read(data, {type:'array'});

            var sheet_name = work_book.SheetNames;

            var sheet_data = XLSX.utils.sheet_to_json(work_book.Sheets[sheet_name[0]], {header:1});

            arr = [];            

            if(sheet_data.length > 0)
            {
                var table_output = '<table class="table table-striped table-border">';

                    for(var row = 0; row < sheet_data.length; row++)
                    {
                        let arr_str = [];

                        table_output += '<tr>';                            

                        for(var cell = 0; cell < sheet_data[row].length; cell++)
                        {

                            table_output += '<td>' +sheet_data[row][cell]+ '</td>';

                            //заполним строку таблицы в массив
                            arr_str[cell] = [sheet_data[row][cell]];//cell , 
                           
                        }

                        table_output += '</tr>';
                        //внесем массив строки в массив таблица
                        arr[row] = arr_str;

                    }

                table_output += '</table>';
                console.info(JSON.stringify(arr));

                document.getElementById('excel_data').innerHTML = table_output;

            }


        }

    });
}*/