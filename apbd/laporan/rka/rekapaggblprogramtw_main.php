<?php
function rekapaggblprogramtw_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	//$revisi = arg(4); 
	
	$kodeuk = arg(4); 
	$topmargin = arg(5);
	$tipedok = arg(6);
	$revisi= arg(7);
	$exportpdf = arg(8);
	
	if ($topmargin=='') $topmargin = 10;

	//drupal_set_message($revisi);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		if($tipedok=='dpaperubahan')
		{
				$pdfFile = $tipedok . '-skpd-rekapaggblprogramtw-' . $kodeuk . '.pdf';

				//$htmlContent = GenReportForm(1);
				//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

				$htmlHeader = GenReportFormPerubahanHeader($kodeuk, $tipedok);
				$htmlContent = GenReportFormPerubahanContent($kodeuk, $tipedok, $revisi);
				$htmlFooter = GenReportFormPerubahanFooter($kodeuk, $tipedok, $revisi);
				
				apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter,false, $pdfFile);
		}
		else
		{
				$pdfFile = $tipedok . '-skpd-rekapaggblprogramtw-' . $kodeuk . '.pdf';

				//$htmlContent = GenReportForm(1);
				//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

				$htmlHeader = GenReportFormHeader($kodeuk, $tipedok);
				$htmlContent = GenReportFormPerubahanContent($kodeuk, $tipedok,$revisi);
				$htmlFooter = GenReportFormFooter($kodeuk, $tipedok,$revisi);
				
				apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter,false, $pdfFile);
		}
		
		
	} else {
		$url = 'apbd/laporan/rka/rekapaggblprogramtw/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('rekapaggblprogramtw_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		if($tipedok=='dpaperubahan'){
		$output .= GenReportFormPerubahan($kodeuk, $tipedok);
		}
		else{
			$output .= GenReportForm($kodeuk, $tipedok,$revisi);
		}
		return $output;
	}

}

function GenReportFormPerubahan($kodeuk, $tipedok,$revisi) {
	

	if ($revisi=='9')
			$str_table = '';
		else
			$str_table = $revisi;
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$where = ' where k.kodeuk=\'%s\'';



	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'Rekapitulasi Dokumen Pelaksanaan Perubahan Anggaran Belanja Langsung', 'width'=>'865px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );
	/*
	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '135px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumber Dana', 'width' => '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'I',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'II',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'III',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'IV',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	*/
	
	$headersrek[] = array (
							 array('data' => 'Kode Program Kegiatan', 'width' => '95px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Uraian', 'width' => '120px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Lokasi Kegiatan', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Target Kinerja(Kuantitatif)', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Sumber dana', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Jumlah (Rp)','colspan'=>'2', 'width' => '220px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Bertambah/berkurang','colspan'=>'2', 'width' => '173px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
						 );	
	$headersrek[] = array (
							 array('data' => 'Sebelum perubahan', 'width' => '110px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Setelah perubahan', 'width' => '109px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),	
							 array('data' => '(Rp)', 'width' => '115px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => '%', 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
						 );						 

	//PROGRAM
	$total = 0;
	$totalp = 0;
	$t_tw1 =0;
	$t_tw2 =0;
	$t_tw3 = 0;
	$t_tw4 = 0;

	$where = ' where k.inaktif=0 and k.jenis=2 and k.kodeuk=\'%s\'';
	$sql = 'select p.kodepro,p.program,sum(k.total) jumlahx,sum(k.totalp) jumlahxp,sum(k.tw1) tw1x,sum(k.tw2) tw2x,
			sum(k.tw3) tw3x,sum(k.tw4) tw4x from {kegiatanperubahan}' . $str_table . ' k left join {program} p 
			on k.kodepro=p.kodepro ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by p.kodepro,p.program order by p.kodepro';
	
	//drupal_set_message( $fsql);
	$resultpro = db_query($fsql);
	if ($resultpro) {
		while ($datapro = db_fetch_object($resultpro)) {
			$total += $datapro->jumlahx;
			$totalp += $datapro->jumlahxp;

			$t_tw1 += $datapro->tw1x;
			$t_tw2 += $datapro->tw2x;
			$t_tw3 += $datapro->tw3x;
			$t_tw4 += $datapro->tw4x;
			
			$rowsrek[] = array (
									array('data' => $datapro->kodepro, 'width' => '95px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
									 array('data' => $datapro->program, 'width' => '120px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn($datapro->jumlahx), 'width' => '110px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn($datapro->jumlahxp), 'width' => '109px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),	
									 array('data' => apbd_fn($datapro->jumlahxp-$datapro->jumlahx), 'width' => '115px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datapro->jumlahx,$datapro->jumlahxp)), 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
								 );
			    
			//KEGIATAN
			$sql = 'select kodekeg,nomorkeg,lokasi,programtarget,kegiatan,sumberdana1,sumberdana2,total,totalp,tw1,tw2,tw3,tw4 
					from {kegiatanperubahan}' . $str_table . ' where inaktif=0 and jenis=2 and kodeuk=\'%s\' and kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' order by nomorkeg';
			
			//drupal_set_message( $fsql);
			$resultkeg = db_query($fsql);
			if ($resultkeg) {
				while ($datakeg = db_fetch_object($resultkeg)) {
					
					$sumberdana = $datakeg->sumberdana1;
					if ($datakeg->sumberdana2!='') $sumberdana .= ', ' . $datakeg->sumberdana2;
					
					$rowsrek[] = array (
											 array('data' => $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3), 'width' => '95px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $datakeg->kegiatan, 'width' => '120px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => str_replace('||',', ', $datakeg->lokasi), 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $datakeg->programtarget, 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $sumberdana, 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => apbd_fn($datakeg->total), 'width' => '110px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),
											 array('data' => apbd_fn($datakeg->totalp), 'width' => '109px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),	
											 array('data' => apbd_fn($datakeg->totalp-$datakeg->totalp), 'width' => '115px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),
											 array('data' => apbd_fn2(apbd_hitungpersen($datakeg->total,$datakeg->totalp)), 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;text-align:right;font-size: 100%;'),
										 );		

				 
				////////
				}
			}
		}
	}
	$rowsrek[] = array (
						 
							 array('data' => '', 'width' => '95px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Jumlah', 'width' => '393px','colspan'=>'4', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($total), 'width' => '110px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($totalp), 'width' => '109px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($totalp-$total), 'width' => '115px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),	
							 array('data' => apbd_fn2(apbd_hitungpersen($total,$totalp)), 'width' => '57px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
						 );
	if ($tipedok=='dpaperubahan') {
		//BL
							 
	
		$pquery = sprintf("select dpatgl,dpatgl1, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl1;
		}

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '375px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '375px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $budjabatan,  'width' => '375px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '375px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $budnama,  'width' => '375px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '500px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width' => '375px', 'style' => 'text-align:center;'),
							 );	
							 
	} else {

		
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '555px',  'colspan'=>'6',  'style' => 'text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width'=> '320px',  'colspan'=>'4', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '555px',  'colspan'=>'6',  'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '320px',  'colspan'=>'4', 'style' => 'text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '555px',  'colspan'=>'6',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width'=> '320px',  'colspan'=>'4', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '555px',  'colspan'=>'6',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width'=> '320px',  'colspan'=>'4', 'style' => 'text-align:center;'),
							 );
	}
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
}

function GenReportFormHeader($kodeuk, $tipedok) {
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}


	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'865px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	if ($tipedok=='dpa') {
		$rowskegiatan[]= array ( 
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARANX',  'width'=> '350px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; ; text-align:center;'),
							 array('data' => 'REKAPITULASI BELANJA LANGSUNG', 'width' => '400px', 'colspan'=>'5', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.2em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '125',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => 'SATUAN KERJA PERANGKAT DAERAH',  'width'=> '350px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 array('data' => 'MENURUT PROGRAM DAN KEGIATAN', 'width' => '400px', 'colspan'=>'5', 'style' => 'border-right: 1px solid black; font-size:1.2em; text-align:center;'),
							 array('data' => 'DPAP-SKPD 2.2', 'width' => '125',  'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '350px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 array('data' => '', 'width' => '400px', 'colspan'=>'5', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.2em; text-align:center;'),
							 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 );
	
	} else {
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'REKAPITULASI ANGGGARAN TRIWULAN PER PROGRAM KEGIATAN', 'width' => '500px', 'colspan'=>'5', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.2em; text-align:center;'),
							 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 );
	}
	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'colspan'=>'2', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormPerubahanHeader($kodeuk, $tipedok) {
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}


	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'865px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	if ($tipedok=='dpaperubahan') {
		$rowskegiatan[]= array ( 
							 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN',  'width'=> '755px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; ; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '125',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => 'SATUAN KERJA PERANGKAT DAERAH',  'width'=> '755px', 'colspan'=>'4', 'style' => 'border-bottom:1px solid black;border-left: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 array('data' => 'DPPA-SKPD 2.2', 'width' => '125',  'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => 'KABUPATEN JEPARA',  'width'=> '755px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; ; text-align:center;'),
							 array('data' => $tahun, 'width' => '125','rowspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => 'Tahun Anggaran '.$tahun,  'width'=> '755px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; ; text-align:center;'),
							 );						
							 
	} else {
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'REKAPITULASI ANGGGARAN TRIWULAN PER PROGRAM KEGIATAN', 'width' => '500px', 'colspan'=>'5', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.2em; text-align:center;'),
							 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 );
	}
	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'colspan'=>'2', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);
	$rowskegiatan[]= array ( 
							 array('data' => 'Rekapitulasi Dokumen Pelaksanaan Perubahan Anggaran Belanja Langsung ',  'width'=> '881px', 'colspan'=>'4', 'style' => 'border-top:1px solid black;border-left: 1px solid black; border-right: 1px solid black;font-weight:bold ; text-align:center;'),
							 );	
	$rowskegiatan[]= array ( 
						 array('data' => 'Menurut Program Dan Kegiatan',  'width'=> '881px', 'colspan'=>'4', 'style' => 'border-left: 1px solid black; border-right: 1px solid black;font-weight:bold ; text-align:center;'),
						 );	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $tipedok,$revisi) {
	
	if ($revisi=='9')
			$str_table = '';
		else
			$str_table = $revisi;
	/*
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '65px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '140x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumber Dana', 'width' => '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'I',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'II',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'III',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'IV',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
 
						 );
	*/
	
	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '60px', 'rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '140px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumber Dana', 'width' => '75px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Tri Wulan',  'width' => '320px', 'colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '80px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );	
	$headersrek[] = array (
						 array('data' => 'I',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'II',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'III',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'IV',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );	
	//PROGRAM
	$total = 0;
	$t_tw1 =0;
	$t_tw2 =0;
	$t_tw3 = 0;
	$t_tw4 = 0;

	$where = ' where k.inaktif=0 and k.jenis=2 and k.kodeuk=\'%s\'';
	$sql = 'select p.kodepro,p.program,sum(k.total) jumlahx,sum(k.tw1) tw1x,sum(k.tw2) tw2x,
			sum(k.tw3) tw3x,sum(k.tw4) tw4x from {kegiatanskpd} k left join {program} p 
			on k.kodepro=p.kodepro ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by p.kodepro,p.program order by p.kodepro';
	
	//drupal_set_message( $fsql);
	$resultpro = db_query($fsql);
	if ($resultpro) {
		while ($datapro = db_fetch_object($resultpro)) {
			$total += $datapro->jumlahx;

			
			$rowsrek[] = array (
								 array('data' => $datapro->kodepro,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datapro->program,  'width' => '140x', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '75px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datapro->tw1x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw2x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw3x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw4x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->jumlahx),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//KEGIATAN
			$sql = 'select kodekeg,nomorkeg,lokasi,programtarget,kegiatan,total,tw1,tw2,tw3,tw4,sumberdana1,sumberdana2
					from {kegiatanskpd} where inaktif=0 and jenis=2 and kodeuk=\'%s\' and kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' order by nomorkeg';
			
			//drupal_set_message( $fsql);
			$resultkeg = db_query($fsql);
			if ($resultkeg) {
				while ($datakeg = db_fetch_object($resultkeg)) {

					$t_tw1 += $datakeg->tw1;
					$t_tw2 += $datakeg->tw2;
					$t_tw3 += $datakeg->tw3;
					$t_tw4 += $datakeg->tw4;
				
					$sumberdana = $datakeg->sumberdana1;
					if ($datakeg->sumberdana2!='') $sumberdana .= ', ' . $datakeg->sumberdana2;

					$rowsrek[] = array (
										 array('data' => $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->kegiatan,  'width' => '140x', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => str_replace('||',', ', $datakeg->lokasi), 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->programtarget, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => $sumberdana, 'width' => '75px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($datakeg->tw1),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->tw2),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->tw3),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->tw4),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->total),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 );		

				 
				////////
				}
			}
		}
	}

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA LANGSUNG',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_tw1),  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_tw2),  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_tw3),  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_tw4),  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}
function GenReportFormPerubahanContent($kodeuk, $tipedok,$revisi) {
	
	if (($revisi==variable_get('apbdrevisi', 0)) or ($revisi=='9'))
		$str_table = '';
	else
		$str_table = $revisi;

	/*
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '65px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '140x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumber Dana', 'width' => '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'I',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'II',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'III',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'IV',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
 
						 );
	*/
	
	$headersrek[] = array (
							 array('data' => 'Kode Program Kegiatan', 'width' => '75px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Uraian', 'width' => '140px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Lokasi Kegiatan', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Target Kinerja(Kuantitatif)', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Sumber dana', 'width' => '90px','rowspan'=>'2', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Jumlah (Rp)', 'width' => '220px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Bertambah/berkurang', 'width' => '173px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
						 );	
	$headersrek[] = array (
							array('data' => 'Sebelum perubahan', 'width' => '110px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Setelah perubahan', 'width' => '109px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),	
							 array('data' => '(Rp)', 'width' => '115px', 'style' => 'border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => '%', 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
						 );	
	//PROGRAM
	$total = 0;$totalp = 0;
	$t_tw1 =0;
	$t_tw2 =0;
	$t_tw3 = 0;
	$t_tw4 = 0;

	$where = ' where k.inaktif=0 and k.totalp>0 and k.jenis=2 and k.kodeuk=\'%s\'';
	$sql = 'select p.kodepro,p.program,sum(k.total) jumlahx,sum(k.totalp) jumlahxp,sum(k.tw1) tw1x,sum(k.tw2) tw2x,
			sum(k.tw3) tw3x,sum(k.tw4) tw4x from {kegiatanperubahan}' . $str_table . ' k left join {program} p 
			on k.kodepro=p.kodepro ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by p.kodepro,p.program order by p.kodepro';
	
	//drupal_set_message( $fsql);
	$resultpro = db_query($fsql);
	if ($resultpro) {
		while ($datapro = db_fetch_object($resultpro)) {
			$total += $datapro->jumlahx;
			$totalp += $datapro->jumlahxp;

			
			$rowsrek[] = array (
									 array('data' => $datapro->kodepro, 'width' => '75px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
									 array('data' => $datapro->program, 'width' => '140px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => '', 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn($datapro->jumlahx), 'width' => '110px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn($datapro->jumlahxp), 'width' => '109px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),	
									 array('data' => apbd_fn($datapro->jumlahxp-$datapro->jumlahx), 'width' => '115px', 'style' => 'border-left:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datapro->jumlahx,$datapro->jumlahxp)), 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
									 
								 /*array('data' => $datapro->kodepro,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datapro->program,  'width' => '140x', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '75px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datapro->tw1x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw2x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw3x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->tw4x),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->jumlahx),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),*/
								 );
			    
			//KEGIATAN
			$sql = 'select kodekeg,nomorkeg,lokasi,programtarget,kegiatan,total,totalp,tw1,tw2,tw3,tw4,sumberdana1,sumberdana2
					from {kegiatanperubahan}' . $str_table . ' where inaktif=0 and totalp>0 and jenis=2 and kodeuk=\'%s\' and kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' order by nomorkeg';
			
			//drupal_set_message( $fsql);
			$resultkeg = db_query($fsql); 
			if ($resultkeg) { 
				while ($datakeg = db_fetch_object($resultkeg)) {

					$t_tw1 += $datakeg->tw1;
					$t_tw2 += $datakeg->tw2;
					$t_tw3 += $datakeg->tw3;
					$t_tw4 += $datakeg->tw4;
				
					$sumberdana = $datakeg->sumberdana1;
					if ($datakeg->sumberdana2!='') $sumberdana .= ', ' . $datakeg->sumberdana2;

					$rowsrek[] = array (
											array('data' => $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3), 'width' => '75px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $datakeg->kegiatan, 'width' => '140px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => str_replace('||',', ', $datakeg->lokasi), 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $datakeg->programtarget, 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => $sumberdana, 'width' => '90px', 'style' => 'border-left:1px solid black;text-align:left;font-size: 100%;'),
											 array('data' => apbd_fn($datakeg->total), 'width' => '110px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),
											 array('data' => apbd_fn($datakeg->totalp), 'width' => '109px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),	
											 array('data' => apbd_fn($datakeg->totalp-$datakeg->totalp), 'width' => '115px', 'style' => 'border-left:1px solid black;text-align:right;font-size: 100%;'),
											 array('data' => apbd_fn2(apbd_hitungpersen($datakeg->total,$datakeg->totalp)), 'width' => '57px', 'style' => 'border-left:1px solid black;border-right:1px solid black;text-align:right;font-size: 100%;'),
										 
										 );		

				 
				////////
				}
			}
		}
	}

	$rowsrek[] = array (
							 array('data' => '', 'width' => '75px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;font-size: 100%;'),
							 array('data' => 'Jumlah', 'width' => '413px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($total), 'width' => '110px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($totalp), 'width' => '109px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
							 array('data' => apbd_fn($totalp-$total), 'width' => '115px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),	
							 array('data' => apbd_fn2(apbd_hitungpersen($total,$totalp)), 'width' => '57px', 'style' => 'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;text-align:right;font-weight:bold;font-size: 100%;'),
						 
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormFooter($kodeuk, $tipedok,$revisi) {
	
	if ($tipedok=='dpa') {
		$pquery = sprintf("select dpatgl".$revisi." as dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Mengesahkan,',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budjabatan,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budnama,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );		
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='';
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}
function GenReportFormPerubahanFooter($kodeuk, $tipedok, $revisi) {
	
	if ($tipedok=='dpaperubahan') {
		$pquery = sprintf("select dpatgl".$revisi." as dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}
		
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Mengesahkan,',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budjabatan,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budnama,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '520px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width'=> '355px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );		
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='';
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '655px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width'=> '220px',  'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}


function rekapaggblprogramtw_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kodeuk = arg(4); 
	$topmargin = arg(5);
	$tipedok = arg(6);
	$revisi= arg(7);
	$exportpdf = arg(8);
	
	

	if ($topmargin=='') $topmargin = 10;

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		//$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
		
	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipedok, 
	);
	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#default_value'=> $revisi, 
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
function rekapaggblprogramtw_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	$tipedok = $form_state['values']['tipedok'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan'])
		$uri = 'apbd/laporan/rka/rekapaggblprogramtw/'. $kodeuk . '/'. $topmargin . '/' . $tipedok . '/' . $revisi ;
	else
		$uri = 'apbd/laporan/rka/rekapaggblprogramtw/'. $kodeuk . '/'. $topmargin .  '/' . $tipedok . '/' . $revisi . '/pdf' ;
	drupal_goto($uri);
	
}
?>