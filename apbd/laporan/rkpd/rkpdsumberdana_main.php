<?php
function rkpdsumberdana_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(3);
	if ($kodeuk=='') $kodeuk='00';
	$tahun = arg(4);
	$exportpdf = arg(5);
	
	//drupal_set_message($kodeuk);
	if (!isset($tahun)) 
		return drupal_get_form('rkpdsumberdana_form');
	

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		$htmlContent = GenReportForm(1);
		$pdfFile = 'analisappa.pdf';
		apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
		
	} elseif (isset($exportpdf) && ($exportpdf=='xls'))  {
		rkpdsumberdana_exportexcel($kodeuk, $tahun);
		
	} else {
		//PDF
		$urlpdf = 'apbd/laporan/rkpdsumberdana/' . $kodeuk . '/' . $tahun . '/pdf';
		$urlxls = 'apbd/laporan/rkpdsumberdana/' . $kodeuk . '/' . $tahun . '/xls';
		$output .= drupal_get_form('rkpdsumberdana_form');
		$output .= l('Cetak (PDF)', $urlpdf , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		
		if (isSuperuser())
			$output .= "&nbsp;" . l('Excel', $urlxls , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;


		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {

	$kodeuk = arg(3);
	if ($kodeuk=='') $kodeuk='00';
	$tahun = arg(4);
	
	if ($kodeuk !='00') {
		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
		$presult = db_query($pquery);
		if ($data=db_fetch_object($presult)) {
			$namauk = $data->namasingkat ;
		}
	} 
	
	//---
	$col1 = '70px';
	$col2 = '300';	
	$colrekening = '124px';
	$colplafon = '128px';
	$coltotal = '370px';	//'750px';
	
	$customwhere = ' and k.tahun=\'%s\' ';
	$where = ' where true' . $customwhere . $qlike ;

	$sql = 'select r.kodeu, r.urusansingkat, sum(k.apbdkab) jkab, sum(k.apbdprov) jprov, sum(k.apbdnas) jnas, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join urusan r on p.kodeu=r.kodeu ' . $where . ' group by r.kodeu, r.urusansingkat order by r.kodeu';
	
	$fsql = sprintf($sql, db_escape_string($tahun));
	//drupal_set_message( $fsql);
	$result = db_query($fsql);
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$headers1[] = array (array ('data'=>'SUMBER DANA KEGIATAN PPAS PER URUSAN - SKPD', 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'6', 'width'=>'870px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers[] = array (
							array('data' => 'KODE',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;'),
							array('data' => 'URUSAN - SKPD', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'DAU/PAD/LAINNYA', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'BANPROV', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'DAK', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							array('data' => 'JUMLAH',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						);

	$headers[] = array (
							array('data' => '1',  'width'=> $col1, 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '2', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '3', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '4', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '5', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							array('data' => '6',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						);

	if ($result) {
		
		while ($data = db_fetch_object($result)) {                
			//URUSAN
			$rows[] = array (
				array('data' => $data->kodeu, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => $data->urusansingkat, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($data->jkab) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($data->jprov), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($data->jnas), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
				array('data' => apbd_fn($data->jtotal), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			);
			
			//SKPD
			$wheresub = sprintf(' and p.kodeu=\'%s\' ', $data->kodeu);
			
			$sql = 'select p.kodeu, s.kodedinas, s.namasingkat, sum(k.apbdkab) jkab, sum(k.apbdprov) jprov, sum(k.apbdnas) jnas, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join unitkerja s on k.kodeuk=s.kodeuk ' . $where . $wheresub . ' group by p.kodeu, s.kodedinas, s.namasingkat order by p.kodeu, s.kodedinas';
			
			$fsql_1 = sprintf($sql, db_escape_string($tahun));
			//drupal_set_message( $fsql_1);
			$result_1 = db_query($fsql_1);

			while ($data_1 = db_fetch_object($result_1)) {                
				//URUSAN
				$rows[] = array (
					array('data' => $data_1->kodeu . '.' . $data_1->kodedinas, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),
					array('data' => $data_1->namasingkat, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data_1->jkab) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data_1->jprov), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data_1->jnas), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
					array('data' => apbd_fn($data_1->jtotal), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
				);			
			}
		}
		
	} 
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$rows1[] = array (array('data' => '', 'colspan'=>'2'));
	$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
	
	$output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));

	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
}

function rkpdsumberdana_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Parameter Laporan',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$kodeuk = arg(2);        
	if ($kodeuk=='') $kodeuk='00';
	
	//FILTER TAHUN-----
	$tahun = variable_get('apbdtahun', 0);
	$form['formdata']['tahun']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Tahun',
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	);

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		//drupal_set_message('user kec');
	} else {
		$type='select';
		$pquery = "select kodedinas,kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}	
	}	
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,
	);		
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan'
	);
	
	return $form;
}

function rkpdsumberdana_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];
	$kodeuk = $form_state['values']['kodeuk'];
	$uri = 'apbd/laporan/rkpdsumberdana/' . $kodeuk . '/' . $tahun;
	drupal_goto($uri);
	

}

function rkpdsumberdana_exportexcel($kodeuk, $tahun) {
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SiPPD Online")
							 ->setLastModifiedBy("SiPPD Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SiPPD Online.")
							 ->setKeywords("office 2007 SiPPD openxml php")
							 ->setCategory("SiPPD Online PPA");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'KODE')
			->setCellValue('B' . $row ,'URAIAN')
            ->setCellValue('C' . $row ,'PEGAWAI')
            ->setCellValue('D' . $row ,'BARANG JASA')
			->setCellValue('E' . $row ,'MODAL')
			->setCellValue('F' . $row ,'JUMLAH');

//Open data							 
$kodeuk = arg(3);
if ($kodeuk=='') $kodeuk='00';
$tahun = arg(4);

if ($kodeuk !='00') {
	$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
}
$customwhere = ' and k.tahun=\'%s\' ';
$where = ' where true' . $customwhere . $qlike ;

$sql = 'select r.kodeu, r.urusansingkat, sum(k.apbdkab) jkab, sum(k.apbdprov) jprov, sum(k.apbdnas) jnas, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join urusan r on p.kodeu=r.kodeu ' . $where . ' group by r.kodeu, r.urusansingkat order by r.kodeu';

$fsql = sprintf($sql, db_escape_string($tahun));
//drupal_set_message( $fsql);
$result = db_query($fsql);	

while ($data = db_fetch_object($result)) {
	//URUSAN
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, "'" . $data->kodeu)
				->setCellValue('B' . $row, $data->urusansingkat)
				->setCellValue('C' . $row, $data->jkab)
				->setCellValue('D' . $row, $data->jprov)
				->setCellValue('E' . $row, $data->jnas)
				->setCellValue('F' . $row, $data->jtotal);
	
	//SKPD
	$wheresub = sprintf(' and p.kodeu=\'%s\' ', $data->kodeu);
	$sql = 'select p.kodeu, s.kodedinas, s.namasingkat, sum(k.apbdkab) jkab, sum(k.apbdprov) jprov, sum(k.apbdnas) jnas, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join unitkerja s on k.kodeuk=s.kodeuk ' . $where . $wheresub . ' group by p.kodeu, s.kodedinas, s.namasingkat order by p.kodeu, s.kodedinas';
	
	$fsql_1 = sprintf($sql, db_escape_string($tahun));
	//drupal_set_message( $fsql_1);
	$result_1 = db_query($fsql_1);

	while ($data_1 = db_fetch_object($result_1)) {	
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . $row, "'" . $data->kodeu . '.' . $data_1->kodedinas)
					->setCellValue('B' . $row, $data_1->namasingkat)
					->setCellValue('C' . $row, $data_1->jkab)
					->setCellValue('D' . $row, $data_1->jprov)
					->setCellValue('E' . $row, $data_1->jnas)
					->setCellValue('F' . $row, $data_1->jtotal);	
	}
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Penjabaran PPAS');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client�s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Penjabaran_PPAS_Urusan_SKPD.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>