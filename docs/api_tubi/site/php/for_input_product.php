<?php
    include '../../connect.php';
    require_once 'function_input_product.php';
    require_once '../../PHPExcel.php';

    mysqli_query($con,"SET NAMES 'utf8'");
 
    $price_list = get_price($con);
    
    //print_r($price_list);

    $objPHPExcel = new PHPExcel();
    //создать первый лист
    $objPHPExcel->setActiveSheetIndex(0);
    //получить активный лист
    $active_sheet = $objPHPExcel->getActiveSheet();

    $active_sheet->getPageSetup()
            ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $active_sheet->getPageSetup()
                ->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    //Поля документа		
    $active_sheet->getPageMargins()->setTop(1);
    $active_sheet->getPageMargins()->setRight(0.75);
    $active_sheet->getPageMargins()->setLeft(0.75);
    $active_sheet->getPageMargins()->setBottom(1);
    //Название листа
    $active_sheet->setTitle("Прайс-лист");	
    //Шапа и футер 
    $active_sheet->getHeaderFooter()->setOddHeader("&CШапка нашего прайс-листа");	
    $active_sheet->getHeaderFooter()->setOddFooter('&L&B'.$active_sheet->getTitle().'&RСтраница &P из &N');
    //Настройки шрифта
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);

    $active_sheet->getColumnDimension('A')->setWidth(15);
    $active_sheet->getColumnDimension('B')->setWidth(15);
    $active_sheet->getColumnDimension('C')->setWidth(15);
    $active_sheet->getColumnDimension('D')->setWidth(15);
    $active_sheet->getColumnDimension('E')->setWidth(15);
    $active_sheet->getColumnDimension('F')->setWidth(15);
    $active_sheet->getColumnDimension('G')->setWidth(15);
    $active_sheet->getColumnDimension('H')->setWidth(15);
    $active_sheet->getColumnDimension('I')->setWidth(15);
    $active_sheet->getColumnDimension('J')->setWidth(15);
    $active_sheet->getColumnDimension('K')->setWidth(15);
    $active_sheet->getColumnDimension('L')->setWidth(15);
    $active_sheet->getColumnDimension('M')->setWidth(60);
    $active_sheet->getColumnDimension('N')->setWidth(15);
    $active_sheet->getColumnDimension('O')->setWidth(15);
    $active_sheet->getColumnDimension('P')->setWidth(20);
    $active_sheet->getColumnDimension('Q')->setWidth(20);
    $active_sheet->getColumnDimension('R')->setWidth(15);
    $active_sheet->getColumnDimension('S')->setWidth(15);

    
    //$active_sheet->mergeCells('A1:D1');
    //$active_sheet->getRowDimension('1')->setRowHeight(40);
    //$active_sheet->setCellValue('A1','Техно мир');    
    //$active_sheet->mergeCells('A2:D2');
    //$active_sheet->setCellValue('A2','Компьютеы и комплектующие на любой вкус и цвет');    
    //$active_sheet->mergeCells('A4:C4');
    //$active_sheet->setCellValue('A4','Дата создания прайс-листа');
    //Записываем данные в ячейку
    //$date = date('d-m-Y');
    //$active_sheet->setCellValue('D4',$date);
    //Устанавливает формат данных в ячейке - дата
   //$active_sheet->getStyle('D4')
    //            ->getNumberFormat()
   // ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14);
    //Создаем шапку таблички данных
    $active_sheet->setCellValue('A1','catalog');
    $active_sheet->setCellValue('B1','category');
    $active_sheet->setCellValue('C1','product_name');
    $active_sheet->setCellValue('D1','brand');
    $active_sheet->setCellValue('E1','characteristic');
    $active_sheet->setCellValue('F1','type_packaging');
    $active_sheet->setCellValue('G1','unit_measure');
    $active_sheet->setCellValue('H1','weight_volume');
    $active_sheet->setCellValue('I1','quantity_package');
    $active_sheet->setCellValue('J1','min_sell');
    $active_sheet->setCellValue('K1','multiple_of');
    $active_sheet->setCellValue('L1','storage_conditions');
    $active_sheet->setCellValue('M1','in_product_name');
    $active_sheet->setCellValue('N1','description');
    $active_sheet->setCellValue('O1','abbreviation');
    $active_sheet->setCellValue('P1','counterparty');
    $active_sheet->setCellValue('Q1','taxpayer_id_number');
    $active_sheet->setCellValue('R1','warehouse_id');
    $active_sheet->setCellValue('S1','creator_user_id');

    $active_sheet->setCellValue('A2','каталог');
    $active_sheet->setCellValue('B2','категория');
    $active_sheet->setCellValue('C2','имя товара');
    $active_sheet->setCellValue('D2','бренд');
    $active_sheet->setCellValue('E2','характеристика');
    $active_sheet->setCellValue('F2','тип упаковки');
    $active_sheet->setCellValue('G2','мера веса');
    $active_sheet->setCellValue('H2','вес/объем');
    $active_sheet->setCellValue('I2','кол-во в упаковке');
    $active_sheet->setCellValue('J2','минимальный заказ');
    $active_sheet->setCellValue('K2','продажа кратно');
    $active_sheet->setCellValue('L2','условия хранения товара');
    $active_sheet->setCellValue('M2','входящее имя товара');
    $active_sheet->setCellValue('N2','описание товара');
    $active_sheet->setCellValue('O2','аббревиатура компании');
    $active_sheet->setCellValue('P2','имя компании');
    $active_sheet->setCellValue('Q2','инн компании');
    $active_sheet->setCellValue('R2','номер склада');
    $active_sheet->setCellValue('S2','ваш id');
    
    //В цикле проходимся по элементам массива и выводим все в соответствующие ячейки
    $row_start = 3;
    $i = 0;
    foreach($price_list as $item) {
        $row_next = $row_start + $i;
        
        $active_sheet->setCellValue('L'.$row_next,"обычное");
        $active_sheet->setCellValue('M'.$row_next,$item['in_product_name']);
        $active_sheet->setCellValue('N'.$row_next,"нет описания");
        $active_sheet->setCellValue('O'.$row_next,$item['abbreviation']);
        $active_sheet->setCellValue('P'.$row_next,$item['counterparty']);
        $active_sheet->setCellValue('Q'.$row_next,$item['taxpayer_id_number']);
        $active_sheet->getStyle('Q'.$row_next)
                    ->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);        
        $active_sheet->setCellValue('R'.$row_next,$item['warehouse_id']);
        $active_sheet->setCellValue('S'.$row_next,"2");
        
        $i++;
    }

    //внешняя рамка ячеек
    $border = array(
        'borders'=>array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            ),
        )
    );    
     $finish_table = ($row_start + $i)-1;
    $active_sheet->getStyle("A1:S$finish_table")->applyFromArray($border);
    //Внутриния рамка у ячеек
    $border = array(
        'borders'=>array(
            'inside' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            ),
        )
    );     
    $active_sheet->getStyle("A1:S$finish_table")->applyFromArray($border);    

    //создать новый лист
    //$objPHPExcel->createSheet();
    $date = date('d-m-Y');  
    $doc_name="input_product_".$date;

    header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$doc_name.xls");
    //header("Content-Disposition: attachment; filename=file.xls");
        
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $objWriter->save('php://output');     
    exit();
 
?>