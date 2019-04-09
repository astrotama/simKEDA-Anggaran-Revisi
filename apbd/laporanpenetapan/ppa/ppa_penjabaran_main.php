<?php
function ppa_penjabaran_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$jenis = arg(3);
	if ($jenis=='') $jenis = 'skpd';
	$tahun = arg(4);
	$exportpdf = arg(5);
	
	//drupal_set_message($kodeuk);
	if (!isset($tahun)) 
		return drupal_get_form('ppa_penjabaran_form');
	

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		$htmlContent = GenReportForm(1);
		$pdfFile = 'analisappa.pdf';
		apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
		
	} elseif (isset($exportpdf) && ($exportpdf=='xls'))  {
		ppa_penjabaran_exportexcel($jenis, $tahun);
		
	} else {
		//PDF
		$urlpdf = 'apbd/laporanpenetapan/ppa_penjabaran/' . $jenis . '/' . $tahun . '/pdf';
		$urlxls = 'apbd/laporanpenetapan/ppa_penjabaran/' . $jenis . '/' . $tahun . '/xls';
		$output .= drupal_get_form('ppa_penjabaran_form');
		$output .= l('Cetak (PDF)', $urlpdf , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		
		if (isSuperuser())
			$output .= "&nbsp;" . l('Excel', $urlxls , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;


		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {

	$jenis = arg(3);
	if ($jenis=='') $jenis = 'skpd';
	$tahun = arg(4);
	
	//---
	$col1 = '70px';
	$col2 = '300';	
	$colrekening = '124px';
	$colplafon = '128px';
	$coltotal = '370px';	//'750px';
	
	$customwhere = ' and k.tahun=\'%s\' ';
	$where = ' where true' . $customwhere ;

	if ($jenis=='skpd') 
		$sql = 'select s.kodeuk, s.kodedinas kode, s.namasingkat uraian, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join unitkerja s on k.kodeuk=s.kodeuk ' . $where . ' group by s.kodeuk, s.kodedinas, s.namasingkat order by s.kodedinas';
	else
		$sql = 'select r.kodeu kode, r.urusansingkat uraian, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join urusan r on p.kodeu=r.kodeu ' . $where . ' group by r.kodeu, r.urusansingkat order by r.kodeu';
	
	$fsql = sprintf($sql, db_escape_string($tahun));
	//drupal_set_message( $fsql);
	$result = db_query($fsql);
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$headers1[] = array (array ('data'=>'PENJABARAN BELANJA PPAS', 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'870px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'6', 'width'=>'870px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
	$headers[] = array (
						array('data' => 'KODE',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;'),
						array('data' => 'URAIAN', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						array('data' => 'PEGAWAI', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						array('data' => 'BARANG JASA', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						array('data' => 'MODAL', 'width' => $colrekening, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
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

	$totalpegawai = 0;
	$totalbarangjasa = 0;
	$totalmodal = 0;
	$totaltotal = 0;
	if ($result) {
		
		while ($data = db_fetch_object($result)) {                
			//SKPD
			$totalpegawai += $data->jpegawai;
			$totalbarangjasa += $data->jbarangjasa;
			$totalmodal += $data->jmodal;
			$totaltotal += $data->jtotal;
			$rows[] = array (
				array('data' => $data->kode, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),
				array('data' => $data->uraian, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jpegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 1em;'),
				array('data' => apbd_fn($data->jtotal), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 0.5px solid grey; border-right: 1px solid black; font-size: 1em;'),
			);			
			
		}

		//TOTAL
		$rows[] = array (
			array('data' => '', 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
			array('data' => 'TOTAL', 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			array('data' => apbd_fn($totalpegawai) , 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			array('data' => apbd_fn($totalbarangjasa), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			array('data' => apbd_fn($totalmodal), 'width' => $colrekening, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
			array('data' => apbd_fn($totaltotal), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
		);		
		
	} 
	 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$rows1[] = array (array('data' => '', 'colspan'=>'2'));
	$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
	
	$output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));

	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
}

function ppa_penjabaran_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Parameter Laporan',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$jenis = arg(3);        
	
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
		'#weight' => 1,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis Rekap'), 
		'#default_value' => $jenis,
		'#options' => array(	
			 'urusan' => t('Urusan'), 	
			 'skpd' => t('SKPD'),	
		   ),
		'#weight' => 2,
	);
	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 3,
	);	
	/*
	$form['formdata']['ttd']= array(
		'#type' => 'radios', 
		'#title' => t('Tanda Tangan'), 
		'#default_value' => $ttd,
		'#options' => array(	
			 'ttd' => t('Ya'), 	
			 'nottd' => t('Tidak'),	
		   ),
		'#weight' => 4,
	);
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);	
	*/
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 6,
	);
	
	return $form;
}

function ppa_penjabaran_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];
	$jenis = $form_state['values']['jenis'];
	$uri = 'apbd/laporanpenetapan/ppa_penjabaran/' . $jenis . '/' . $tahun;
	drupal_goto($uri);
	

}

function ppa_penjabaran_exportexcel($kodeuk, $tahun) {
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
$jenis = arg(3);
if ($jenis=='') $jenis = 'skpd';
$tahun = arg(4);

$customwhere = ' and k.tahun=\'%s\' ';
$where = ' where true' . $customwhere ;

if ($jenis=='skpd') 
	$sql = 'select s.kodeuk, s.kodedinas kode, s.namasingkat uraian, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join unitkerja s on k.kodeuk=s.kodeuk ' . $where . ' group by s.kodeuk, s.kodedinas, s.namasingkat order by s.kodedinas';
else
	$sql = 'select r.kodeu kode, r.urusansingkat uraian, sum(k.nompegawai) jpegawai, sum(k.nombarangjasa) jbarangjasa, sum(k.nommodal) jmodal, sum(k.total) jtotal from kegiatanppa k inner join program p on k.tahun=p.tahun and k.kodepro=p.kodepro inner join urusan r on p.kodeu=r.kodeu ' . $where . ' group by r.kodeu, r.urusansingkat order by r.kodeu';

$fsql = sprintf($sql, db_escape_string($tahun));
//drupal_set_message( $fsql);
$result = db_query($fsql);	

while ($data = db_fetch_object($result)) {
	//URUSAN
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, "'" . $data->kode)
				->setCellValue('B' . $row, $data->uraian)
				->setCellValue('C' . $row, $data->jpegawai)
				->setCellValue('D' . $row, $data->jbarangjasa)
				->setCellValue('E' . $row, $data->jmodal)
				->setCellValue('F' . $row, $data->jtotal);
	
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Penjabaran PPAS');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = "penjabaran_ppa_" . $jenis . "_" . $tahun . ".xlsx";
header('Content-Disposition: attachment;filename="' . $fname . '"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>