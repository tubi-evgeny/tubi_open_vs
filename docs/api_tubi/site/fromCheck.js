
console.log("test start 3 \n");

let document_name_selected = "";//какой документ выбран для загрузки цены/запасы
let provider_info_list = [];  //список всех поставщиков
let provider_or_warehouse="";//какой список сейчас показывает в таблице
let provider_selected = null;//выбран поставщик
let warehouse_info_list = [];//список складов поставщика
let warehouse_selected = null;//выбран склад
let excelArrCreated = [];//Приготовленный к загрузке массив таблицы с именами столбцов
let stock_status = "table"; // вопрос от куда берем остаки( таблица, просто делаем по 10уп, делаем = 0)

   /*     let btn = document.getElementById('test_div_button');
        btn.addEventListener('click',function(){
            console.log("test_div_button");
        }); 
        let btn2 = document.getElementById('test_div_button_2');
        btn2.addEventListener('click',function(){
            console.log("test_div_button_2");
        }); */
//удалить указанное кол. строк сверху
$("#deletsString").on("click", function(){
    deleteStringToTable();
    $("#deleteStrCount").val(0);
    excelArrCreated = [];
});

//файл запущен из .html кнопкой
$("#btnTest").on("click", function(){
    console.log("log test : \n");
    
    let td0 = $("#td0").val();
    console.log("test td000 "+td0+"\n");
    

    let td_arr_js = $("#td_arr").val();
    let td_arr  = [];
   // alert("td_arr_js.length = "+td_arr_js.length);
    if(td_arr_js.length != 0){
         td_arr = JSON.parse(td_arr_js);
    }    
    console.log("td_arr length = "+td_arr.length+"\n");
    console.log("btnTest / td_arr_length 2 = "+td_arr.length); 

    for(let i=0;i < td_arr.length;i++){
        console.log("td for="+td_arr[i]);
    }
    for(let i=0;i < td_arr.length;i++){
        let elem = "td"+i;
        let v = $("#"+elem).val();
        console.log("test td"+i+": "+v+"\n");
    }


});
//первое заполнение таблицы
$("#excel_file").on("click", function(){

    createStartExcel();    
});
//отправить файл для записи в БД
$("#sendFile").on("click", function(){

    sendFileExcell();    
});
//получить список складов этого поставщика
$("#getWarehouse").on("click", function(){
    console.log("btn getWarehouse : \n");

    getWarehouseList();//getWarehouse();
});
//получить список поставщиков
$("#getCounterparty").on("click", function(){
    console.log("btn getCounterpartty\n");

    getCounterparty_list();
});
//получить список складов этого поставщика
/*function getWarehouse(){   
    getWarehouseList();
}*/

$("#btn_radio1").on("click", function(){
    var rates = document.getElementsByName('rate');
   // var rate_value;
    for(var i = 0; i < rates.length; i++){
        if(rates[i].checked){
            //rate_value = rates[i].value;
            document_name_selected = "цены";
        }
    }
    let list_name = 'Укажите <b>Наименование товара</b>  /  <b>Цена упаковки</b> / <b>Цена единицы</b> / <b>Учет(уп/шт) </b>/<b> Штрихкод/Артикул</b>';
    list_name +=  '<br>Укажите сколько строк надо <b>удалить Сверху таблицы</b> чтобы остались только значения для записи';
    document.getElementById('list_name').innerHTML = list_name;

    console.log("btn_radio1 = "+document_name_selected);
    document.getElementById("rb_to_tabe").checked = true;
    stock_status = "table";
    let form = document.getElementById("rates2");
    form.style.display = "none";
      
});
$("#btn_radio2").on("click", function(){
    
    var rates = document.getElementsByName('rate');
    //var rate_value;
    for(var i = 0; i < rates.length; i++){
        if(rates[i].checked){
            //rate_value = rates[i].value;
            document_name_selected = "запасы";
        }
    }
    let list_name = 'Укажите <b>Наименование товара</b>  /  <b>Учет(уп/шт)</b> / <b>Колличество</b>';
    list_name +=  '<br>Укажите сколько строк надо <b>удалить Сверху таблицы</b> чтобы остались только значения для записи';
    document.getElementById('list_name').innerHTML = list_name;
   // $("#radio_button").val(rate_value);
    console.log("btn_radio2 = "+document_name_selected);
    document.getElementById('rb_to_tabe').checked = true;
    stock_status = "table";
    let form = document.getElementById("rates2");
    form.style.display = "block";
});
$("#rb_to_tabe").on("click", function(){ 
    stock_status = "table";
    let list_name = 'Укажите <b>Наименование товара</b>  /  <b>Учет(уп/шт)</b> / <b>Колличество</b>'; 
    list_name +=  '<br>Укажите сколько строк надо <b>удалить Сверху таблицы</b> чтобы остались только значения для записи';
    document.getElementById('list_name').innerHTML = list_name;

    console.log("rb_to_tabe / stock_status="+stock_status);
   
});
$("#rb_work_full").on("click", function(){ 
    stock_status = "full";
    let list_name = 'Укажите <b>Наименование товара</b>   ';
    list_name +=  '<br>Укажите сколько строк надо <b>удалить Сверху таблицы</b> чтобы остались только значения для записи';
    document.getElementById('list_name').innerHTML = list_name;

    console.log("rb_work_full / stock_status="+stock_status);
   
});
$("#rb_work_null").on("click", function(){ 
    stock_status = "null";
    let list_name = 'Укажите <b>Наименование товара</b>   ';
    list_name +=  '<br>Укажите сколько строк надо <b>удалить Сверху таблицы</b> чтобы остались только значения для записи';
    document.getElementById('list_name').innerHTML = list_name;

    console.log("rb_work_null / stock_status="+stock_status);
   
});
//слушаем нажатие строки таблицы и пишем значение в инпут
$("#db_data").on("click", function(){
    console.log("excel_data : inputs : \n");
    console.table(provider_info_list);

    writeClickToInput();    
});
//подгтовленная таблица удалены столбцы лишние и пустые значения
$("#createNewTable").on("click", function(){

    createNewTable();    
    
});
//слушаем нажатие строки таблицы и пишем значение в инпут
function writeClickToInput(){
    var elems = document.getElementsByClassName('inputs');
    for(var i = 0; i < elems.length; i++) {
        elems[i].addEventListener('click', function(){
            //получить имя таблицы
            if(provider_or_warehouse === "Список компаний"){//if(list_name === "Список компаний"){                
                let id_str = String(this.id);
                if(id_str.length != 0){
                    $("#counterparty_name").val(this.innerHTML);
                    let id = Number(this.id);

                    provider_selected = [provider_info_list[id]['counterparty_id'], provider_info_list[id]['companyInfoString']];                   
                    warehouse_selected = null;
                    $("#warehouse_info").val("");
                }
            }
            else if(provider_or_warehouse ==="Список складов"){               

               // warehouse_selected = 
               let id_str = String(this.id);
                if(id_str.length != 0){
                    $("#warehouse_info").val(this.innerHTML);
                    let id = Number(this.id);

                    warehouse_selected = [warehouse_info_list[id]['warehouse_id'], warehouse_info_list[id]['warehouseInfoString']];                   
                }
            }  
            provider_or_warehouse=""; 
            //очищаем таблицу
            document.getElementById('db_data').innerHTML = '';
            //document.getElementById('excel_data').innerHTML = '';
            //document.getElementById('list_name').innerHTML = '<b>Список товаров</b>';
        }, false);
    }
    console.log("provider_selected ");
    console.log(provider_selected);
    console.log("warehouse_selected ");
    console.log(warehouse_selected);   
    
}
//удалить указанное кол. строк сверху
function deleteStringToTable(){
    let excel_data_arr = [];
    let columnName = "";
    let emptyVarCount = 0;
    let deleteStrCount = $("#deleteStrCount").val();
    let excel_data_json = $("#list_str").val();
    if(excel_data_json.length != 0){
        excel_data_arr = JSON.parse(excel_data_json);        
    }else{
        alert("Таблица не выбрана, массив пустой");
        return;
    }    
    //получить значения выбранных столбцов
    let my_td_arr = getColumnNameFromTable();
    

    var table_output = '<table class="table table-striped table-border">';
    let arr = [];
    let number_of_columns = 0;
    //проверить количество столбцов
    for(var row = 0; row < excel_data_arr.length; row++){   
        for(var cell = 0; cell < excel_data_arr[row].length; cell++){
            if(number_of_columns < cell){
                number_of_columns = cell;
            }
        }
    }
    console.log("excel_file / number_of_columns: "+number_of_columns); 
    //собрать первую строку со спинером 
    table_output += '<tr>';
    for(var cell = 0; cell < (number_of_columns+1); cell++){
        let tdCell = "td"+cell;
        //получить имя столбца и вписать в спинер
        if(my_td_arr.length != 0){                   //if(my_td_arr.length != 0 && cell < my_td_arr.length){
           
            let valueFlag = false;
            columnName = "Имя столбца";
            for(let i = 0;i < my_td_arr.length;i++){
                if(cell == my_td_arr[i][0]){
                    columnName = my_td_arr[i][1];
                    valueFlag = true;
                }
            }
                       
            if(cell == 0){
                table_output += '<td>' +'</td>';
                if(valueFlag)
                table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" value="'+columnName+'" />'+ '</td>';
                else  table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
            }else{
                if(valueFlag)
                table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" value="'+columnName+'" />'+ '</td>';
                else  table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
            }
            valueFlag = false;
        }else{//если столбцы не подписаны то писать подсказку
            if(cell == 0){
                table_output += '<td>' +'</td>';
                table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
            }else{
                table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
            }
        }
    }
    table_output += '</tr>'; 
    //теперь убрать лишние строки 
    let arr_count = 0;
    
    for(let i=0;i < excel_data_arr.length;i++){
        let my_str = excel_data_arr[i];
        let arr_str = [];
        //пропускаем строки для удаления, сколько установили 
        if(i < deleteStrCount ){
            emptyVarCount++;
        }else{
            let table_output_temp = '<tr>';
            //прокручиваем строку
            for(let j=0;j < my_str.length;j++){

                //table_output_temp += '<td>' +my_str[j]+ '</td>';
                 //добавить номер строки
                 if(j == 0){
                    table_output_temp += '<td>' +((i+1)-emptyVarCount)+". "+ '</td>';
                    table_output_temp += '<td>' +my_str[j]+ '</td>';
                }else{
                    table_output_temp += '<td>' +my_str[j]+ '</td>';
                }
                //заполним строку таблицы в массив
                arr_str[j] = my_str[j];           
            } 
            table_output_temp += '</tr>'; 
            table_output += table_output_temp;   
            //внесем массив строки в массив таблица
            arr[arr_count++] = arr_str;      
        }
        
    }
    document.getElementById('excel_data').innerHTML = table_output;

    if(emptyVarCount != 0){
        alert("Из таблицы удалено строк - "+emptyVarCount+" шт.");
    }
    //конвертируем в JSON
    var jsonString = JSON.stringify(arr);
    //поместим строку в переменную html
    document.getElementById('list_str').value = jsonString;

    console.log("emptyVarCount = "+emptyVarCount);
    console.log(jsonString);
}
//получить значения выбранных столбцов
// let my_td_arr = getColumnNameFromTable();
function getColumnNameFromTable(){    
    let td_arr_js = $("#td_arr").val();
    let my_td_arr = [];let count = 0;//номера столбцов и имена
    let td_arr  = [];
    if(td_arr_js.length != 0){
         td_arr = JSON.parse(td_arr_js);
    } 
    //let td_arr = JSON.parse(td_arr_js);
    let arr = [];
    for(let i=0;i < td_arr.length;i++){
        
        let elem = "td"+i;//получаем номер столбца
        let v = $("#"+elem).val();//получаем значение столбца
        //номера столбцов и имена
        if(v === '' || v === undefined){
        }else{
            my_td_arr[count++] = [i, v];
        }      
        
    } 
    console.table(my_td_arr);
    return my_td_arr;
}
//получить имя столбца в БД
function get_column_name_for_database(col_name){
    if(col_name === 'Наим. товара'){
        return 'in_product_name';
    }else if(col_name === 'Цена единицы'){
        return 'unit_price';
    }else if(col_name === 'Цена упаковки'){
        return 'package_price';
    }else if(col_name === 'Количество'){
        return 'quantity';
    }else if(col_name === 'Учет(уп/шт)'){
        return 'accounting_unit';
    }else if(col_name === 'Штрихкод/Артикул'){
        return 'barcode_article';
    }
}
//подгтовленная таблица удалены столбцы лишние и пустые значения
function createNewTable(){
    //let my_td_arr = [];let count = 0;//номера столбцов и имена
    let emptyVarCount = 0;
    let arr = [];
    excelArrCreated = [];
    let deleteStrCount = $("#deleteStrCount").val();
    let excel_data_json = $("#list_str").val();//document.getElementById('excel_data').innerHTML;
    var table_output = '<table class="table table-striped table-border">';

    //получить значения выбранных столбцов
    let my_td_arr = getColumnNameFromTable();
    //если имена столбцов не выбраны, вернутся
    if(my_td_arr.length == 0){
        alert("Не указано имя столбцов");
        return;
    }
    let arr_str = [];
    //let count = 0;
    //собрать первую строку со спинером 
    table_output += '<tr>';
    for(var cell = 0; cell < (my_td_arr.length); cell++){
        
        let tdCell = "td"+cell;
        table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" value="'+my_td_arr[cell][1]+'" />'+ '</td>';
        
        let column_name = get_column_name_for_database(my_td_arr[cell][1]);
        //заполним строку таблицы в массив
        arr_str[cell] = column_name;
    }
    excelArrCreated[0] = arr_str;
    console.table(excelArrCreated);
    table_output += '</tr>'; 
    //теперь убрать лишние строки и столбцы
    let nullFlag = false;
    let arr_count = 0;
    let excel_data_arr = JSON.parse(excel_data_json);
    for(let i=0;i < excel_data_arr.length;i++){
        let my_str = excel_data_arr[i];
        arr_str = [];
        //пропускаем строки сколько установили 
        if(i < deleteStrCount ){//|| !my_str){
        // nullFlag = true;
        }else{
            let table_output_temp = '<tr>';
            //прокручиваем строку
            for(let j=0;j < my_str.length;j++){
                //проверяем этот столбец нам нужен?
                for(let a=0;a < my_td_arr.length;a++){
                    //let column_info = my_td_arr[a];
                    let num = Number(my_td_arr[a][0]);
                    if(j == num){
                        
                        table_output_temp += '<td>' +my_str[num]+ '</td>';

                        //заполним строку таблицы в массив
                        arr_str[a] = my_str[num];
                        //проверить на null
                        if(!my_str[num] || "" == String(my_str[num]).trim()){ 
                            //emptyVarCount++;
                            nullFlag = true;
                        }
                    }
                }                
            } 
            table_output_temp += '</tr>'; 
                                            
            //проверить на пусто
            if(arr_str.length != my_td_arr.length || nullFlag){  
                emptyVarCount++;
                nullFlag = false;
            }else{
                table_output += table_output_temp;   
                //внесем массив строки в массив таблица
                arr[arr_count++] = arr_str;             
            }         
        }        
    }
    document.getElementById('excel_data').innerHTML = table_output;

    if(emptyVarCount != 0){
        alert("Из таблицы удалено строк с пустыми значениями или значениями null - "+emptyVarCount+" шт.");
    }else {
        alert("Таблица готова");
    }
    //добавляем массив таблицы к массиву с именами столбцов (в конец)
    excelArrCreated = excelArrCreated.concat(arr);
    //console.table(excelArrCreated);
    //конвертируем в JSON
    var jsonString = JSON.stringify(arr);
    //поместим строку в переменную html
    document.getElementById('list_str').value = jsonString;

    //console.log("emptyVarCount = "+emptyVarCount);
    //console.log(jsonString);
    //конвертируем в JSON
    var td_arr_json_str = JSON.stringify(my_td_arr);
    //поместим строку в переменную html
    document.getElementById('td_info_arr').value = td_arr_json_str;
    $("#deleteStrCount").val(0);
}
//первое заполнение таблицы
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
            //console.log("sheet_data="+sheet_data);

            if(sheet_data.length > 0){                
                let arr = [];                

                var table_output = '<table class="table table-striped table-border">';
                //document.getElementById('list_name').innerHTML = '<b>Список товаров</b>';
                let number_of_columns = 0;
                //проверить количество столбцов
                for(var row = 0; row < sheet_data.length; row++){   
                    for(var cell = 0; cell < sheet_data[row].length; cell++){
                        if(number_of_columns < cell){
                            number_of_columns = cell;
                        }
                    }
                }
                console.log("excel_file / number_of_columns: "+number_of_columns);
                //alert("number_of_columns: "+number_of_columns);
                //собрать первую строку со спинером 
                let td_arr = [];
                table_output += '<tr>';
                for(var cell = 0; cell < (number_of_columns+1); cell++){                    
                    let tdCell = "td"+cell;
                    if(cell == 0){
                        table_output += '<td>' +'</td>';
                        table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
                    }else{
                        table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';
                    }
                    //колличество столбцов
                    td_arr[cell] = tdCell;
                }
                table_output += '</tr>'; 

                //показываем excel file
                    for(var row = 0; row < sheet_data.length; row++){
                        let arr_str = [];
                        
                        table_output += '<tr>';
                        for(var cell = 0; cell < sheet_data[row].length; cell++){
                            //добавить номер строки
                            if(cell == 0){
                                table_output += '<td>' +(row+1)+". "+ '</td>';
                                table_output += '<td>' +sheet_data[row][cell]+ '</td>';
                            }else{
                                table_output += '<td>' +sheet_data[row][cell]+ '</td>';
                            }
                            //заполним строку таблицы в массив
                            arr_str[cell] = sheet_data[row][cell];
                        }
                        table_output += '</tr>';
                        //внесем массив строки в массив таблица
                        arr[row] = arr_str;
                    }
                table_output += '</table>';

                document.getElementById('excel_data').innerHTML = table_output;
                //конвертируем в JSON
                var jsonString = JSON.stringify(arr);
                //поместим строку в переменную html
                document.getElementById('start_list_str').value = jsonString;
                document.getElementById('list_str').value = jsonString;
                //конвертируем в JSON
                var td_arr_json_str = JSON.stringify(td_arr);
                //поместим строку в переменную html
                //$("#td_arr").val(td_arr);
                document.getElementById('td_arr').value = td_arr_json_str;     
            }
        }
    });
}


//получить список складов этого поставщика
function getWarehouseList(){ 

    if(provider_selected != null ){
        //очищаем таблицу
        document.getElementById('db_data').innerHTML = '';
        provider_or_warehouse = ""; 
        let provider_id = provider_selected[0];  
        console.log("provider_id = "+provider_id);
        $.ajax({
            //создаем url для отправки в .php
            url: 'checkPrice.php',
            type: 'POST',
            cache: false,
            data: { 'warehouse_list_this_provider':0, "provider_id":provider_id},
            dataType: 'html',
            beforeSend: function() {
                //ожидание пока функция выполняется кнопка не активна
                $("#getWarehouse").prop("disabled", true);
            },
            success: function(data) {
                //функция выполнена ответ получен, ответ из .php            
                warehouse_info_list = JSON.parse(data);
                console.table(warehouse_info_list);
                //функция выполнена, кнопка активна
                $("#getWarehouse").prop("disabled", false);

                var table_output = '<table class="table table-bordered ">';
                provider_or_warehouse = "Список складов";

                for(let i=0;i < warehouse_info_list.length;i++){
                    table_output += '<tr><td>'+'<div id="'+i+'" class="inputs"  type="text">"'+warehouse_info_list[i]['warehouseInfoString']+'"</div>'+'</td></tr>';
                }
                document.getElementById('db_data').innerHTML = table_output;                      
            }
        });   
    }else{
        alert("Выберите поставщика");
    }  
}
//получить список поставщиков
function getCounterparty_list(){ 
    //let provider_info_list = [];   
    //очищаем таблицу
    document.getElementById('db_data').innerHTML = '';
    //document.getElementById('excel_data').innerHTML = '';
    //document.getElementById('list_name').innerHTML = '<b>Список</b>';
    //$("#list_name").val("Список");
    provider_or_warehouse = "";
    //очищаем кнопку с файлом
    //$( "#excel_file" ).val("");
    
    $.ajax({
        //создаем url для отправки в .php
        url: 'checkPrice.php',
        type: 'POST',
        cache: false,
        data: { 'provider':0 },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#getCounterparty").prop("disabled", true);
        },
        success: function(data) {
            //console.log(data);
            //функция выполнена ответ получен
            //ответ из .php     
            provider_info_list = JSON.parse(data);
            console.table(provider_info_list);
            //функция выполнена, кнопка активна
            $("#getCounterparty").prop("disabled", false);

            var table_output = '<table class="table table-bordered ">';//table-striped table-border caption-top

            //table_output += '<caption><ya-tr-span data-translated="true" data-translation="Список пользователей"  data-type="trSpan">Список пользователей</ya-tr-span></caption>';

            //document.getElementById('list_name').innerHTML = '<b>Список компаний</b>';
            //$("#list_name").val("Список компаний");
            provider_or_warehouse = "Список компаний";
            //table_output += '<div id="input_list" class="input_list">';
            for(let i=0;i < provider_info_list.length;i++){
                table_output += '<tr><td>'+'<div id="'+i+'" class="inputs"  type="text">"'+provider_info_list[i]['companyInfoString']+'"</div>'+'</td></tr>';
                //table_output += '<tr><td>'+'<input class="inputs" type="submit" value="'+provider_info_list[i]['companyInfoString']+'" />'+'</td></tr>';
            }

            //table_output += '</div>';
            document.getElementById('db_data').innerHTML = table_output;    
            
            //let list_name = $("#list_name").val();
            //console.log("list_name getCounterparty_list = "+list_name);         
        }
    });     
}
//отправить файл для записи в БД
function sendFileExcell(){     
    let counterparty_id = 0;
    let warehouse_id = 0;  
    if(document_name_selected.length == 0){
        alert("Выберите имя документа цены/запасы");
        return;
    }
    if(excelArrCreated.length == 0){
        alert("Подготовте таблицу для загрузки в БД");
        return;
    }
    if(provider_selected == null){
        alert("Выберите поставщика");
        return;
    }
    if(warehouse_selected == null){
        alert("Выберите склад поставщика");
        return;
    }
    //проверить первую строку таблицы. правильно выбраны имена столбцов
    let correctFlag = checkFirstStringTableForCorrectDaata();
    let numCorrectFlag;
    
    if(correctFlag){
        //проверить таблицу перед записью данные указанны коректно?
        numCorrectFlag = checkTableForCorrectData();
    }
    
    if(numCorrectFlag){
        
    }else{
        alert("В таблице обнаруженны не корректные данные (возможно вместо цифр указан текст или ....");
        return;        
    }
    
    counterparty_id = provider_selected[0];
    warehouse_id = warehouse_selected[0];

    console.log('counterparty_id = '+provider_selected[0]);
    console.log('warehouse_id = '+warehouse_selected[0]);

    let tableUpload = '';
    //какую таблицу загружаем
    if(document_name_selected === 'запасы'){
        tableUpload = 'upload_stock_file';
    }else if(document_name_selected === 'цены'){
        tableUpload = 'upload_price_file';
    }
    console.log('tableUpload = '+tableUpload+' counterparty_id='+counterparty_id+' warehouse_id='+warehouse_id);
    $.ajax({
        //создаем url для отправки в .php
        url: 'checkPrice.php',
        type: 'POST',
        cache: false,
        data: {'upload_file':0, 'tableUpload':tableUpload, 'counterparty_id':counterparty_id
                , 'warehouse_id':warehouse_id, 'excelArrCreated':excelArrCreated, 'stock_status':stock_status },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#sendFile").prop("disabled", true);
        },
        success: function(data) {
            //функция выполнена ответ получен
            //ответ из .php
            //alert("table go to DB");
            alert(data);

            $("#php_ansver").val(data);
          
            $("#sendFile").prop("disabled", false);
            //очищаем кнопку с файлом
            $( "#excel_file" ).val("");
            excelArrCreated = [];
        }
    });
}
//проверить таблицу перед записью данные указанны коректно?
function checkTableForCorrectData(){
    console.log("checkTableForCorrectData");
    //let nameForStringArr = ['in_product_name', 'accounting_unit'];  
    let nameForNumberArr  = ['unit_price', 'package_price', 'quantity'];
    let strFirstArr = [];
    //let columnMeaningArr = [];
    let resFlag = true;
    if(excelArrCreated.length > 0){
        strFirstArr = excelArrCreated[0];
        console.log("strFirstArr ");
        console.table(strFirstArr);
        //получаем значения имен столбцов для сверки значени в столбцах цифры/текст 
        check:
        for(let i=0;i < strFirstArr.length;i++){
            for(let j=0;j < nameForNumberArr.length;j++){
                if(strFirstArr[i] === nameForNumberArr[j]){
                    //проверяем значения в этом столбце
                    resFlag = checkColumnNumber(i);//checkColumnNumber(i, 'number');
                    if(resFlag){

                    }else{
                        break check;
                    }
                }
            }
        }
    }
    return resFlag;
}
//проверяем значения в этом столбце
function checkColumnNumber( numColumn){
    let flag = false;
    //проверяем в столбце точно номер
    //проверяем со 2 строки 
    for(let i=1;i < excelArrCreated.length;i++){
        let num = excelArrCreated[i][numColumn];
        flag = isNumber(num);

        if(flag == false){
            console.log("numColumn = "+numColumn+" str = "+i);
            console.log(excelArrCreated[i][numColumn]);
            console.table(excelArrCreated[i]);
            break;
        }
    }
    return flag;
}
//проверить первую строку таблицы. правильно выбраны имена столбцов
function checkFirstStringTableForCorrectDaata(){
    console.log("checkFirstStringTableForCorrectDaata");
    let flag = true;
    let trueCount = 0;
    let stockArr = ['in_product_name', 'accounting_unit', 'quantity'];  
    let priceArr = ['in_product_name', 'unit_price', 'package_price', 'accounting_unit'];  
    for(let i=0;i < excelArrCreated.length;i++){
        if(i == 0 && document_name_selected === 'запасы'){//цены
            console.log("запасы  stock_status == " + stock_status);
            if(stock_status == 'table'){
                //крутим первую строку проверяем имена столбцов
                for(let j=0;j < excelArrCreated[i].length;j++){
                    console.log("excelArrCreated[0] = "+excelArrCreated[i][j]);
                    //ищем совпадения имен столбцов 
                    for(let a=0;a < stockArr.length;a++){
                        //сколько совпадений имен столбцов найдено
                        if(excelArrCreated[i][j] === stockArr[a]){                        
                            trueCount++;
                        }
                    }
                }
                console.log("trueCount = "+trueCount);
                if(trueCount != 3){
                    console.log("trueCount error= "+trueCount);
                    alert("Имена столбцов выбраны не правильно, таблица не может быть загружена");
                    flag = false;
                    //break;
                }

            }else{
                if(excelArrCreated[0][0] != stockArr[0]){                        
                    console.log("trueCount error= in_product_name !="+excelArrCreated[0][0]);
                    alert("Имена столбцов выбраны не правильно, таблица не может быть загружена");
                    flag = false;
                }
            }
            
        }else if(i == 0 && document_name_selected === 'цены'){
            console.log("цены");
            //крутим первую строку проверяем имена столбцов
            for(let j=0;j < excelArrCreated[i].length;j++){
                console.log("excelArrCreated[0] = "+excelArrCreated[i][j]);
                //ищем совпадения имен столбцов 
                for(let a=0;a < priceArr.length;a++){
                    //сколько совпадений имен столбцов найдено
                    if(excelArrCreated[i][j] === priceArr[a]){                        
                        trueCount++;
                    }
                }
            }
            console.log("trueCount = "+trueCount);
            if(trueCount < 3){
                console.log("trueCount error= "+trueCount);
                alert("Имена столбцов выбраны не правильно, таблица не может быть загружена");
                flag = false;
                //break;
            }
        }
        break;
    }
    return flag;
}
//проверь это число? true/false
function isNumber(num) {
	return typeof num === 'number' && !isNaN(num);
}
/*if(excelArrCreated[i][j] === 'in_product_name'){
                    in_product_nameFlag = true;
                }else if(excelArrCreated[i][j] === 'quantity'){
                    quantityFlag = true;
                }else if(excelArrCreated[i][j] === 'accounting_unit'){
                    accounting_unitFlag = true;
                }  
                
    if(col_name === 'Наим. товара'){
        return 'in_product_name';
    }else if(col_name === 'Цена единицы'){
        return 'unit_price';
    }else if(col_name === 'Цена упаковки'){
        return 'package_price';
    }else if(col_name === 'Количество'){
        return 'quantity';
    }else if(col_name === 'Учет(уп/шт)'){
        return 'accounting_unit';
    }*/

