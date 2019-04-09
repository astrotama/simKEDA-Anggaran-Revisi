<?php
function rekapaggblprogram_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(4); 
	$topmargin = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = 10;

	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-rekapaggblprogram-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($kodeuk);
		$htmlFooter = GenReportFormFooter($kodeuk);
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile);
		
	} else {
		$url = 'apbd/laporanpenetapan/rka/rekapaggblprogram/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('rekapaggblprogram_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {
	
	$kodeuk = arg(4); 

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
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
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI ANGGARAN PER PROGRAM KEGIATAN', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

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
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Pegawai',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Barang Jasa',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Modal',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	*/

	$headersrek[] = array (
						 
						 array('data' => 'Kode',  'width'=> '75px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '240px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jenis Belanja',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '90px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );	
	$headersrek[] = array (

						 array('data' => 'Pegawai',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Barang Jasa',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Modal',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );						 

	//PROGRAM
	$total = 0;
	$t_pegawai =0;
	$t_barangjasa =0;
	$t_modal = 0;

	$where = ' where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\'';
	$sql = 'select p.kodepro,p.program,sum(total) jumlahx from {kegiatanskpd} k left join {program} p 
			on k.kodepro=p.kodepro ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by p.kodepro,p.program order by p.kodepro';
	
	//drupal_set_message( $fsql);
	$resultpro = db_query($fsql);
	if ($resultpro) {
		while ($datapro = db_fetch_object($resultpro)) {
			$total += $datapro->jumlahx;
			
			//rekap rekening
			$p_pegawai =0;
			$p_barangjasa =0;
			$p_modal = 0;
			$sql = 'select left(a.kodero,3) kodej, sum(a.jumlah) jumlahx from {kegiatanskpd} k left join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.jenis=2 and k.kodeuk=\'%s\' and k.kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' group by left(a.kodero,3)';
			
			//drupal_set_message($fsql);
			$resultprorek = db_query($fsql);
			if ($resultprorek) {
				while ($dataprorek = db_fetch_object($resultprorek)) {
					if ($dataprorek->kodej == '521') 
						$p_pegawai = $dataprorek->jumlahx;
					else if ($dataprorek->kodej == '522') 
						$p_barangjasa = $dataprorek->jumlahx;
					else if ($dataprorek->kodej == '523')
						$p_modal = $dataprorek->jumlahx;
				}
			}

			$t_pegawai += $p_pegawai;
			$t_barangjasa += $p_barangjasa;
			$t_modal += $p_modal;
			
			$rowsrek[] = array (
								 array('data' => $datapro->kodepro,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datapro->program,  'width' => '240px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($p_pegawai),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($p_barangjasa),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($p_modal),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//KEGIATAN
			$sql = 'select kodekeg,nomorkeg,lokasi,programtarget,kegiatan,total from {kegiatanskpd} 
					where jenis=2 and inaktif=0 and kodeuk=\'%s\' and kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' order by nomorkeg';
			
			//drupal_set_message( $fsql);
			$resultkeg = db_query($fsql);
			if ($resultkeg) {
				while ($datakeg = db_fetch_object($resultkeg)) {

					//rekap rekening
					$k_pegawai =0;
					$k_barangjasa =0;
					$k_modal = 0;
					$sql = 'select left(a.kodero,3) kodej, sum(a.jumlah) jumlahx from {kegiatanskpd} k left join {anggperkeg} a on k.kodekeg=a.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and k.kodekeg=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
					$fsql .= ' group by left(a.kodero,3)';
					
					//drupal_set_message($fsql);
					$resultkegrek = db_query($fsql);
					if ($resultkegrek) {
						while ($datakegrek = db_fetch_object($resultkegrek)) {
							if ($datakegrek->kodej == '521') 
								$k_pegawai = $datakegrek->jumlahx;
							else if ($datakegrek->kodej == '522') 
								$k_barangjasa = $datakegrek->jumlahx;
							else if ($datakegrek->kodej == '523') 
								$k_modal = $datakegrek->jumlahx;
						}
					}
			
					$rowsrek[] = array (
										 array('data' => $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->kegiatan,  'width' => '240px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => str_replace('||',', ', $datakeg->lokasi), 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->programtarget, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($k_pegawai),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($k_barangjasa),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($k_modal),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 );		

				 
				////////
				}
			}
		}
	}

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA LANGSUNG',  'width'=> '515px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$rowsfooter[] = array (
						 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'7',  'style' => 'text-align:center'),
						 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'7',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'7',  'style' => 'text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'7',  'style' => 'text-align:center'),
						 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'text-align:center;'),
						 );

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

function GenReportFormHeader($print=0) {
	
	$kodeuk = arg(4);
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
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
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'REKAPITULASI ANGGGARAN PER PROGRAM KEGIATAN', 'width' => '500px', 'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.2em; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk) {
	
	/*
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '255x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Pegawai',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Barang Jasa',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Modal',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	*/
	
	$headersrek[] = array (
						 
						 array('data' => 'Kode',  'width'=> '60px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center; cellpadding:5;'),
						 array('data' => 'Uraian',  'width' => '255px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi', 'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target Kinerja', 'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jenis Belanja',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '90px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );	
	$headersrek[] = array (

						 array('data' => 'Pegawai',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => 'Barang Jasa',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => 'Modal',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );		
	//PROGRAM
	$total = 0;
	$t_pegawai =0;
	$t_barangjasa =0;
	$t_modal = 0;

	$where = ' where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\'';
	$sql = 'select p.kodepro,p.program,sum(total) jumlahx from {kegiatanskpd} k left join {program} p 
			on k.kodepro=p.kodepro ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by p.kodepro,p.program order by p.kodepro';
	
	//drupal_set_message( $fsql);
	$resultpro = db_query($fsql);
	if ($resultpro) {
		while ($datapro = db_fetch_object($resultpro)) {
			$total += $datapro->jumlahx;
			
			//rekap rekening
			$p_pegawai =0;
			$p_barangjasa =0;
			$p_modal = 0;
			$sql = 'select left(a.kodero,3) kodej, sum(a.jumlah) jumlahx from {kegiatanskpd} k left join {anggperkeg} a on k.kodekeg=a.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' group by left(a.kodero,3)';
			
			//drupal_set_message($fsql);
			$resultprorek = db_query($fsql);
			if ($resultprorek) {
				while ($dataprorek = db_fetch_object($resultprorek)) {
					if ($dataprorek->kodej == '521') 
						$p_pegawai = $dataprorek->jumlahx;
					else if ($dataprorek->kodej == '522') 
						$p_barangjasa = $dataprorek->jumlahx;
					else if ($dataprorek->kodej == '523') 
						$p_modal = $dataprorek->jumlahx;
				}
			}

			$t_pegawai += $p_pegawai;
			$t_barangjasa += $p_barangjasa;
			$t_modal += $p_modal;
			
			$rowsrek[] = array (
								 array('data' => $datapro->kodepro,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datapro->program,  'width' => '255x', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($p_pegawai),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($p_barangjasa),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($p_modal),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datapro->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//KEGIATAN
			$sql = 'select kodekeg,nomorkeg,lokasi,programtarget,kegiatan,total from {kegiatanskpd} 
					where jenis=2 and inaktif=0 and kodeuk=\'%s\' and kodepro=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
			$fsql .= ' order by nomorkeg';
			
			//drupal_set_message( $fsql);
			$resultkeg = db_query($fsql);
			if ($resultkeg) {
				while ($datakeg = db_fetch_object($resultkeg)) {

					//rekap rekening
					$k_pegawai =0;
					$k_barangjasa =0;
					$k_modal = 0;
					$sql = 'select left(a.kodero,3) kodej, sum(a.jumlah) jumlahx from {kegiatanskpd} k left join {anggperkeg} a on k.kodekeg=a.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and k.kodekeg=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
					$fsql .= ' group by left(a.kodero,3)';
					
					//drupal_set_message($fsql);
					$resultkegrek = db_query($fsql);
					if ($resultkegrek) {
						while ($datakegrek = db_fetch_object($resultkegrek)) {
							if ($datakegrek->kodej == '521') 
								$k_pegawai = $datakegrek->jumlahx;
							else if ($datakegrek->kodej == '522') 
								$k_barangjasa = $datakegrek->jumlahx;
							else if ($datakegrek->kodej == '523') 
								$k_modal = $datakegrek->jumlahx;
						}
					}
			
					$rowsrek[] = array (
										 array('data' => $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->kegiatan,  'width' => '255x', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => str_replace('||',', ', $datakeg->lokasi), 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakeg->programtarget, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($k_pegawai),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($k_barangjasa),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($k_modal),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($datakeg->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 );		

				 
				////////
				}
			}
		}
	}

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA LANGSUNG',  'width'=> '515px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($kodeuk) {
	
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
						 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function rekapaggblprogram_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kodeuk = arg(4); 
	$topmargin = arg(5);
	$exportpdf = arg(6);

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
function rekapaggblprogram_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporanpenetapan/rka/rekapaggblprogram/' . $kodeuk . '/'. $topmargin . '/' ;
	else
		$uri = 'apbd/laporanpenetapan/rka/rekapaggblprogram/' . $kodeuk . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>