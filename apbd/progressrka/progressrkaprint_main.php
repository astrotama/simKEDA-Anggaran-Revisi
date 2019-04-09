<?php
function progressrkaprint_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kelompok = arg(3);
	$order = arg(4);
	$topmargin = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = 10;

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-progress' . $kelompok . '.pdf';
 
		$htmlHeader = GenReportFormHeader($kelompok);
		$htmlContent = GenReportFormContent($kelompok, $order);
		
		apbd_ExportPDF2P($topmargin,$topmargin, $htmlHeader, $htmlContent, $pdfFile);
		
	} else {
		$url = 'apbd/progressrka/'. $kelompok . '/' . $order . '/'. $topmargin . "/pdf";
		$output = drupal_get_form('progressrkaprint_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($kelompok, $order);
		return $output;
	}

}

function GenReportForm($kelompok, $order) {
	
	$rowskegiatan[]= array (
						 array('data' => 'PROGRESS PENYUSUNAN RKA-SKPD', 'width' => '535px', 'colspan'=>'8',  'style' => 'border:none;text-align:center;'),
						 );
	switch($kelompok) {
		case '':
			$skelompok = 'SELURUH SKPD';				
			break;	
		case '0':
			$skelompok = 'DINAS / BADAN / KANTOR';				
			break;	
		case '1':
			$skelompok = 'KECAMATAN';				
			break;	
		case '2':
			$skelompok = 'PUSKESMAS';				
			break;	
		case '3':
			$skelompok = 'SEKOLAH';				
			break;	
		case '4':
			$skelompok = 'UPT DISDIKPORA';				
			break;	
	}

	$rowskegiatan[]= array (
						 array('data' => 'KELOMPOK : ' . $skelompok, 'width' => '535px', 'colspan'=>'8',  'style' => 'border:none;text-align:center;'),
						 );

	$headersrek[] = array (
						 array('data' => 'No.',  'width'=> '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'width' => '190px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '115px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Keg',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Blm',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Sbgn',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Sls',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '%',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 );

	if (strlen($kelompok)>0) {
		$where = sprintf(" where u.kelompok='%s' ", db_escape_string($kelompok));
	}
	//$sqlorder = ' order by p.persen';
	//if ($order!='1') $sqlorder .= ' desc';
	
	if ($order=='0') 
		$sqlorder = ' order by p.persen desc, p.selesai desc, p.sebagian desc, p.plafonjml, p.belum, p.plafonnom desc';
	else
		$sqlorder = ' order by p.persen, p.sebagian, p.selesai, p.plafonjml desc, p.belum desc, p.plafonnom';

    $sql = 'select u.kodedinas, u.namasingkat, p.plafonjml, p.plafonnom, p.belum, p.sebagian, p.selesai, p.persen from {unitkerja} u inner join {progressrka} p on u.kodeuk=p.kodeuk ' . $where . $sqlorder;
	
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			/*
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );		
			*/
            $no++;
            $rowsrek[] = array (
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $data->kodedinas . ' - ' . $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->plafonnom), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->plafonjml), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->belum), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->sebagian), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->selesai), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn2($data->persen), 'align' => 'right', 'valign'=>'top'),
						);			
								 
		}
	}			
		

	
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function GenReportFormHeader($kelompok) {
	

	//$rowskegiatan[]= array (
	//					array('data' => 'Kelompok',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
	//					 array('data' => ':', 'width' => '30px', 'style' => 'border:none; text-align:right;'),
	//					 array('data' => $kelompok, 'width' => '370px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
	//					 );

	//echo $skelompok;
	switch($kelompok) {
		case '':
			$skelompok = 'SELURUH SKPD';				
			break;	
		case '0':
			$skelompok = 'DINAS / BADAN / KANTOR';				
			break;	
		case '1':
			$skelompok = 'KECAMATAN';				
			break;	
		case '2':
			$skelompok = 'PUSKESMAS';				
			break;	
		case '3':
			$skelompok = 'SEKOLAH';				
			break;	
		case '4':
			$skelompok = 'UPT DISDIKPORA';				
			break;	
	}
	$rowskegiatan[]= array (
						 array('data' => 'PROGRESS PENYUSUNAN RKA-SKPD', 'width' => '535px', 'colspan'=>'8',  'style' => 'border:none;text-align:center;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'KELOMPOK : ' . $skelompok, 'width' => '535px', 'colspan'=>'8',  'style' => 'border:none;text-align:center;'),
						 );

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kelompok, $order) {
	
	$headersrek[] = array (
						 array('data' => 'No.',  'width'=> '30px', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'width' => '190px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '115px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Keg',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Blm',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Sbgn',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '#Sls',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '%',  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 );

	if (strlen($kelompok)>0) {
		$where = sprintf(" where u.kelompok='%s' ", db_escape_string($kelompok));
	}

	if ($order=='0') 
		$sqlorder = ' order by p.persen desc, p.selesai desc, p.sebagian desc, p.plafonjml, p.belum, p.plafonnom desc';
	else
		$sqlorder = ' order by p.persen, p.sebagian, p.selesai, p.plafonjml desc, p.belum desc, p.plafonnom';

    $sql = 'select u.kodedinas, u.namasingkat, p.plafonjml, p.plafonnom, p.belum, p.sebagian, p.selesai, p.persen from {unitkerja} u inner join {progressrka} p on u.kodeuk=p.kodeuk ' . $where . $sqlorder;
	
	//drupal_set_message( $fsql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
            $no++;
			
			$tplafonjml += $data->plafonjml;
			$tplafonnom += $data->plafonnom;
			$tbelum += $data->belum;
			$tsebagian += $data->sebagian;
			$tselesai += $data->selesai;
			
            $rowsrek[] = array (
						 array('data' => $no,  'width'=> '30px', 'style' => 'border-left: 1px solid black;border-right: 1px solid black; text-align:right;'),
						 array('data' => $data->kodedinas . ' - ' . $data->namasingkat,  'width' => '190px', 'style' => 'border-right: 1px solid black; text-align:left;'),
						 array('data' => apbd_fn($data->plafonnom),  'width' => '115px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($data->plafonjml),  'width' => '40px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($data->belum),  'width' => '40px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($data->sebagian),  'width' => '40px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($data->selesai),  'width' => '40px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn2($data->persen),  'width' => '40px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 						);		
								 
		}

		$rowsrek[] = array (
					 array('data' => '',  'width'=> '30px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-left: 1px solid black;border-right: 1px solid black; text-align:right;'),
					 array('data' => 'TOTAL',  'width' => '190px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:left;'),
					 array('data' => apbd_fn($tplafonnom),  'width' => '115px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
					 array('data' => apbd_fn($tplafonjml),  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
					 array('data' => apbd_fn($tbelum),  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
					 array('data' => apbd_fn($tsebagian),  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
					 array('data' => apbd_fn($tselesai),  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
					 array('data' => apbd_fn2(($tselesai/$tplafonjml)*100),  'width' => '40px', 'style' => 'border-bottom: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right;'),
											);		
		
	}			
		

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}



function progressrkaprint_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kelompok = arg(3);
	$order = arg(4);
	$topmargin = arg(5);
	if ($order=='') $order=0;
	if ($topmargin=='') $topmargin=10;

	$kelompokopt = array();
	$kelompokopt[''] = '-Semua-';
	$kelompokopt['0'] = 'Dinas/Badan/Kantor';
	$kelompokopt['1'] = 'Kecamatan';
	$kelompokopt['2'] = 'Puskesmas';
	$kelompokopt['3'] = 'SMP/SMA/SMK';
	$kelompokopt['4'] = 'UPT Disdikpora';
	$form['formdata']['kelompok']= array(
		'#type'         => 'select', 
		'#title'        => 'Kelompok',
		'#options'	=> $kelompokopt,
		'#width'         => 20, 
		'#default_value'=> $kelompok, 
		'#weight' => 0,
	);	
	
	
	$form['formdata']['order']= array(
		'#type' => 'radios', 
		'#title' => t('Pengurutan'), 
		'#default_value' => $order,
		'#options' => array(	
			 '0' => t('Besar ke Kecil'), 	
			 '1' => t('Kecil ke Besar'),	
		   ),
		'#weight' => 1,
	);
	
	
	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 2,
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 3,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 4,
	); 
	
	return $form;
}
function progressrkaprint_form_submit($form, &$form_state) {
	$kelompok = $form_state['values']['kelompok'];
	$order = $form_state['values']['order'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/progressrka/print/' . $kelompok . '/'. $order . '/10' ;
	else
		$uri = 'apbd/progressrka/print/' . $kelompok . '/'. $order . '/10/pdf' ;
	drupal_goto($uri);
	
}
?>