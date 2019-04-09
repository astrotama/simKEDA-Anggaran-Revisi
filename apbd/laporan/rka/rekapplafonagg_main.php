<?php
function rekapplafonagg_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$revisi = arg(4);
	$kodeuk = arg(5);
	$jenis = arg(6);
	$detil = arg(7);
	$topmargin = arg(8);
	$exportpdf = arg(9);
	if ($topmargin=='') $topmargin=10;
	if ($detil=='') $detil=0;
	if ($kodeuk=='') $kodeuk='00';

	//drupal_set_message($exportpdf);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-plafon-agg-skpd.pdf';
 
		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($jenis);
		$htmlContent = GenReportFormContent($kodeuk, $jenis, $detil,$revisi);
		
		apbd_ExportPDF3P($topmargin,$topmargin, $htmlHeader, $htmlContent, '', $pdfFile);
		
	} else {
		$url = 'apbd/laporan/rka/rekapplafonagg/'.$revisi.'/' . $topmargin . "/pdf";
		$output = drupal_get_form('rekapplafonagg_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($kodeuk, $jenis, $detil,$revisi);
		return $output;
	}

}

function GenReportForm($kodeuk, $jenis, $detil,$revisi) {
	
	if ($revisi=='9')
			$str_table = '';
		else
			$str_table = $revisi;
		
	if ($detil==1)
		$border = 'border-top: 1px solid black; border-bottom: 1px solid black;';
	else
		$border = '';
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI PLAFON DAN ANGGARAN SKPD', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
	if ($jenis==1)
		$ketjenis = "Belanja Tidak Langsung";
	else if ($jenis==2)
		$ketjenis = "Belanja Langsung";
	else
		$ketjenis = "Keseluruhan";

	$rowsjudul[] = array (array ('data'=> $ketjenis, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
	//P : 535
 
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD / Kegiatan',  'width' => '240px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon (P)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran (A)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						  
						 array('data' => 'P-A',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
					);

	
	$totalp = 0;
	$totala = 0;
	$no =0;
	//SKPD
	
	if ($kodeuk !='00') $where = sprintf(' where kodeuk=\'%s\' ', $kodeuk);
	$sql = 'select kodedinas,kodeuk,namasingkat from unitkerja ' . $where . ' order by kodedinas';
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;
			
			if ($jenis=='') {
				$sql = 'select sum(k.plafon) tplafon,sum(total) tanggaran from {kegiatanskpd} k  where inaktif=0 and kodeuk=\'%s\' ';
				$fsql = sprintf($sql, db_escape_string($data->kodeuk));
			} else {
				$sql = 'select sum(plafon) tplafon,sum(total) tanggaran from {kegiatanskpd} where inaktif=0 and kodeuk=\'%s\' and jenis=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($data->kodeuk), db_escape_string($jenis));
			}
			$resultpa = db_query($fsql);
			if ($resultpa) {
				if ($datapa = db_fetch_object($resultpa)) {
					$plafon = $datapa->tplafon;
					$anggaran = $datapa->tanggaran;
				} else {
					$plafon = 0;
					$anggaran = 0;
				}	
			} else {
				$plafon = 0;
				$anggaran = 0;				
			}
			
			$totalp += $plafon;
			$totala += $anggaran;
			
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $data->kodedinas . ' - '. $data->namasingkat,  'width' => '240px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black;' . $border . '  text-align:left;'),
								 array('data' => apbd_fn($plafon),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 array('data' => apbd_fn($anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 array('data' => apbd_fn($plafon - $anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 );
			
			if ($detil==1) {
				//KEGIATAN
				if ($jenis=='') {
					$sql = 'select kegiatan, plafon, total, inaktif from {kegiatanskpd} where kodeuk=\'%s\' ';
					$fsql = sprintf($sql, db_escape_string($data->kodeuk));
				} else {
					$sql = 'select kegiatan, plafon, total, inaktif from {kegiatanskpd} where kodeuk=\'%s\' and jenis=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($data->kodeuk), db_escape_string($jenis));
				}
				$fsql .= ' order by inaktif, jenis, isppkd, kegiatan';
				$resultkeg = db_query($fsql);
				$nokeg = 0;
				if ($resultkeg) {
					while ($datakeg = db_fetch_object($resultkeg)) {
						
						$nokeg +=1;
						$kegiatan = $datakeg->kegiatan;
						if ($datakeg->inaktif==0) {
							$plafonx = $datakeg->plafon;
							$totalx = $datakeg->total;
						} else {
							$plafonx = 0;
							$totalx = 0;
							$kegiatan .= ' (Inaktif)';
						}
							
						$rowsrek[] = array (
											 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
											 array('data' => $nokeg,  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => $kegiatan,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($plafonx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($totalx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($plafonx - $totalx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );				
					
					}
				}
			}
		}
	}										 
								 			
	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '265px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totala),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp - $totala),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	


	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function GenReportFormHeader($jenis) {

	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI PLAFON DAN ANGGARAN SKPD', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	if ($jenis==1)
		$ketjenis = "Belanja Tidak Langsung";
	else if ($jenis==2)
		$ketjenis = "Belanja Langsung";
	else
		$ketjenis = "Keseluruhan";

	$rowsjudul[] = array (array ('data'=> $ketjenis, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $jenis, $detil,$revisi) {
	
	if ($revisi=='9')
			$str_table = '';
		else
			$str_table = $revisi;
	
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	if ($detil==1)
		$border = 'border-top: 1px solid black; border-bottom: 1px solid black;';
	else
		$border = '';
	
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'width' => '240px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon (P)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran (A)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						  
						 array('data' => 'P-A',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
					);

	$totalp = 0;
	$totala = 0;
	$no =0;
	//SKPD

	if ($kodeuk !='00') $where = sprintf(' where kodeuk=\'%s\' ', $kodeuk);
	$sql = 'select kodedinas,kodeuk,namasingkat from unitkerja ' . $where . ' order by kodedinas';
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;
			
			if ($jenis=='') {
				$sql = 'select sum(plafon) tplafon,sum(total) tanggaran from {kegiatanskpd} where inaktif=0 and kodeuk=\'%s\' ';
				$fsql = sprintf($sql, db_escape_string($data->kodeuk));
			} else {
				$sql = 'select sum(plafon) tplafon,sum(total) tanggaran from {kegiatanskpd} where inaktif=0 and kodeuk=\'%s\' and jenis=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($data->kodeuk), db_escape_string($jenis));
			}			
			$resultpa = db_query($fsql);
			if ($resultpa) {
				if ($datapa = db_fetch_object($resultpa)) {
					$plafon = $datapa->tplafon;
					$anggaran = $datapa->tanggaran;
				} else {
					$plafon = 0;
					$anggaran = 0;
				}	
			} else {
				$plafon = 0;
				$anggaran = 0;				
			}
			
			$totalp += $plafon;
			$totala += $anggaran;
			
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;' . $border . ' text-align:right;'),
								 array('data' => $data->kodedinas . ' - '. $data->namasingkat,  'width' => '240px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black;' . $border . '  text-align:left;'),
								 array('data' => apbd_fn($plafon),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 array('data' => apbd_fn($anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 array('data' => apbd_fn($plafon - $anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; ' . $border . ' text-align:right;'),
								 );

			if ($detil==1) {
				//KEGIATAN
				if ($jenis=='') {
					$sql = 'select kegiatan, plafon, total, inaktif from {kegiatanskpd} where kodeuk=\'%s\' ';
					$fsql = sprintf($sql, db_escape_string($data->kodeuk));
				} else {
					$sql = 'select kegiatan, plafon, total, inaktif from {kegiatanskpd} where kodeuk=\'%s\' and jenis=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($data->kodeuk), db_escape_string($jenis));
				}
				$fsql .= ' order by inaktif, jenis, isppkd, kegiatan';
				$resultkeg = db_query($fsql);
				$nokeg = 0;
				if ($resultkeg) {
					while ($datakeg = db_fetch_object($resultkeg)) {
						
						$nokeg +=1;
						$kegiatan = $datakeg->kegiatan;
						if ($datakeg->inaktif==0) {
							$plafonx = $datakeg->plafon;
							$totalx = $datakeg->total;
						} else {
							$plafonx = 0;
							$totalx = 0;
							$kegiatan .= ' (Inaktif)';
						}
							
						$rowsrek[] = array (
											 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
											 array('data' => $nokeg,  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => $kegiatan,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($plafonx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($totalx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($plafonx - $totalx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );				
					
					}
				}
			}								 

		}
	}										 
								 			
		

	
	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '265px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totala),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp - $totala),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	


	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}

function rekapplafonagg_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$revisi = arg(4);
	$kodeuk = arg(5);
	$jenis = arg(6);
	$detil = arg(7);
	$topmargin = arg(8);
	$exportpdf = arg(9);
	
	if ($kodeuk=='') $kodeuk='00';
	if ($topmargin=='') $topmargin=10;
	if ($detil=='') $detil=0;

	$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] ='00000 - SEMUA SKPD';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
	}

	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#default_value'=> $revisi, 
	);
	
	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		'#default_value' => $jenis,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '1' => t('Tidak Langsung'), 	
			 '2' => t('Langsung'),
		   ),
	);	

	$form['formdata']['ssj'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);		

	$form['formdata']['detil']= array(
		'#type' => 'radios', 
		'#title' => t('Detil Kegiatan'), 
		'#default_value' => $detil,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Ya'),
		   ),
	);	

	$form['formdata']['ssk'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);		
	
	$form['formdata']['topmargin']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Margin Atas', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $topmargin, 
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function rekapplafonagg_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$jenis = $form_state['values']['jenis'];
	$detil = $form_state['values']['detil'];
	$topmargin = $form_state['values']['topmargin'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan'])
		$uri = 'apbd/laporan/rka/rekapplafonagg/'.$revisi.'/'. $kodeuk . '/'. $jenis . '/' . $detil . '/'.  $topmargin . '/' ;
	else
		$uri = 'apbd/laporan/rka/rekapplafonagg/'.$revisi.'/'. $kodeuk . '/'. $jenis . '/' . $detil . '/'.  $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>