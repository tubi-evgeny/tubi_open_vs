
$("#r1").on("click", function(){
    var rates = document.getElementsByName('rate');
    var rate_value;
    for(var i = 0; i < rates.length; i++){
        if(rates[i].checked){
            rate_value = rates[i].value;
        }
    }
    $("#radio_button").val(rate_value);

    console.log("Hello ! "+rate_value);
});
$("#r2").on("click", function(){
    
    var rates = document.getElementsByName('rate');
    var rate_value;
    for(var i = 0; i < rates.length; i++){
        if(rates[i].checked){
            rate_value = rates[i].value;
        }
    }
    $("#radio_button").val(rate_value);
    console.log("Hello 2! "+rate_value);
    //alert("Hello 2! "+rate_value);
});


//файл запущен из .html кнопкой
$("#td").on("click", function(){
    //получаем переменную из файла .html
    $("#td0").val();

    let cell = 0;;
    let tdCell = "td"+cell;
    $("#tdKey").val("hello - tdCell: "+tdCell);

  

});

//файл запущен из .html кнопкой
$("#btnTest").on("click", function(){
    console.log("log test : \n");
    
    let td0 = $("#td0").val();
    console.log("test td00 "+td0+"\n");
    

    let td_arr_js = $("#td_arr").val();
    let td_arr = JSON.parse(td_arr_js);
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

    //$("#tdKey").val("hello btnTest");

});
//слушаем нажатие строки таблицы и пишем значение в инпут
$("#excel_data").on("click", function(){
    console.log("excel_data : inputs : \n");

    var elems = document.getElementsByClassName('inputs');
    for(var i = 0; i < elems.length; i++) {

        elems[i].addEventListener('click', function(){
            $("#counterparty_name").val(this.value);
            //очищаем таблицу
            document.getElementById('excel_data').innerHTML = '';
        }, false);
    }
});

//получить список компаний
$("#getCounterparty").on("click", function(){
    console.log("btn getCounterpartty\n");

    //очищаем таблицу
    document.getElementById('excel_data').innerHTML = '';
    //очищаем кнопку с файлом
    $( "#excel_file" ).val("");

    let counterparty_arr = [];
    counterparty_arr[0]="Yacovlew";
    counterparty_arr[1]="Popov";
    counterparty_arr[2]="Ivanov";

    var table_output = '<table class="table table-striped table-border">';

    for(let i=0;i < counterparty_arr.length;i++){
        table_output += '<tr><td>'+'<input class="inputs" type="submit" value="'+counterparty_arr[i]+'" />'+'</td></tr>';
    }
    document.getElementById('excel_data').innerHTML = table_output;
    

});


$("#getWarehouse").on("click",function(){
    console.log("btn warehouse\n");
});

/*$("#getWarehouse").on("click", function(){
    console.log("btn getWarehouse : \n");

    let warehouse_arr = [];
    warehouse_arr[0]="№3 Соболева 15А";
    warehouse_arr[1]="№9 Попова 1";
    warehouse_arr[2]="№17Полевая 11 ";

    var table_output = '<table class="table table-striped table-border">';

    for(let i=0;i < warehouse_arr.length;i++){
        table_output += '<tr><td>'+warehouse_arr[i]+'</td></tr>';
    }
    document.getElementById('excel_data').innerHTML = table_output;

});*/


$("#excel_file").on("click", function(){
    const excel_file = document.getElementById('excel_file');

    excel_file.addEventListener('change', (event) =>{

        var reader = new FileReader();

        reader.readAsArrayBuffer(event.target.files[0]);

        reader.onload = function(event){

            var data = new Uint8Array(reader.result);

            var work_book = XLSX.read(data, {type:'array'});

            var sheet_name = work_book.SheetNames;

            var sheet_data = XLSX.utils.sheet_to_json(work_book.Sheets[sheet_name[0]], {header:1});

            //var rollno = new Array();
            //var name = new Array();

            if(sheet_data.length > 0)
            {
                let firstFlag = true;
                let empty = "hi";
                
                let arr = [];
                

                var table_output = '<table class="table table-striped table-border">';

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
                    table_output += '<td>' +'<input list="columnName" id="'+tdCell+'" type="text" name="model" placeholder="Имя столбца" />'+ '</td>';

                    //колличество столбцов
                    td_arr[cell] = tdCell;
                }
                table_output += '</tr>'; 

                console.log("excel_file / td_arr_length = "+td_arr.length);

                //показываем excel file
                    for(var row = 0; row < sheet_data.length; row++)
                    {

                        let arr_str = [];
                        
                        table_output += '<tr>';

                        for(var cell = 0; cell < sheet_data[row].length; cell++)
                        {

                            table_output += '<td>' +sheet_data[row][cell]+ '</td>';

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
                document.getElementById('list_str').value = jsonString;

                //конвертируем в JSON
                var td_arr_json_str = JSON.stringify(td_arr);
                //поместим строку в переменную html
                //$("#td_arr").val(td_arr);
                document.getElementById('td_arr').value = td_arr_json_str;
                console.log("excel_file / td_arr_length 2 = "+td_arr.length);

            }


        }

    })

  

});

//файл запущен из .html кнопкой
$("#sendMail").on("click", function(){

    //получаем переменную из файла .html
    var list_str = $("#list_str").val();

    let str = $("#td0").val();
    $("#tdKey").val("hello 21 : "+str);

    //$("#text").val("hello");
    

    $.ajax({
        //создаем url для отправки в .php
        url: 'checkPrice.php',
        type: 'POST',
        cache: false,
        data: { 'list_str': list_str },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#sendMail").prop("disabled", true);
        },
        success: function(data) {
            //функция выполнена ответ получен
            //ответ из .php
            alert(data);

            $("#php_ansver").val(data);

          /* if(!data){
                allert("Были ошибки, загрузка таблицы не выполнена!");
            }else{
                allert("Загрузка таблицы выполнена!");
                //$("#mailForm").trigger("reset");
            }*/
            $("#sendMail").prop("disabled", false);
            //очищаем кнопку с файлом
            $( "#excel_file" ).val("");
        }

    });

});
