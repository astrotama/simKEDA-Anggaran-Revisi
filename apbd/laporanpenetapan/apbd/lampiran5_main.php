<?php
function lampiran5_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = arg(4);
	$hal1 = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran5.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent();
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$url = 'apbd/laporanpenetapan/apbd/lampiran5/'. $topmargin . "/pdf";
		$output = drupal_get_form('lampiran5_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {
	

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup

	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}
	
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN V', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '525px', 'colspan'=>'3', 'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '100px', 'style' => 'border:none; text-align:right;'),
						 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdano , 'width' => '200px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdatgl , 'width' => '200px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
						 );
						 
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI ANGGARAN BELANJA DAERAH UNTUK KESELARASAN DAN KETERPADUAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'URUSAN PEMERINTAHAN DAERAH DAN FUNGSI
DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	


	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '50px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '285px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '180px', 'colspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA LANGSUNG',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '90px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'NON PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BARANG JASA',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'MODAL',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );						 

	//1) FUNGSI
	$t_gaji =0;
	$t_nongaji =0;
	$t_pegawai = 0;
	$t_barangjasa = 0;
	$t_modal = 0;

	//Belanja
	$sql = 'select kodef, fungsi from {fungsi} order by kodef';
	$resultf = db_query($sql);	
	if ($resultf) 	{
		while ($dataf = db_fetch_object($resultf)) {
			
			$gaji_f = 0;
			$nongaji_f = 0;
			$pegawai_f = 0;
			$barangjasa_f = 0;
			$modal_f = 0;
			
			$where = sprintf(' where u.kodef=\'%s\'', db_escape_string($dataf->kodef));
			
			//Belanja
			$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran from {anggperkeg} a inner join 
					{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro inner join {urusan} u on p.kodeu=u.kodeu ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
			//drupal_set_message($sql);
			$res = db_query($sql);	
			if ($res) 	{
				while ($data = db_fetch_object($res)) {
					switch($data->kode) {
						case '511':
							$gaji_f = $data->anggaran;
							break;
						case '521':
							$pegawai_f = $data->anggaran;
							break;
						case '522':
							$barangjasa_f = $data->anggaran;
							break;
						case '523':
							$modal_f = $data->anggaran;
							break;
						default:
							$nongaji_f += $data->anggaran;
							break;
					}
				}
			}		
			
			$t_gaji += $gaji_f;
			$t_nongaji += $nongaji_f;
			$t_pegawai += $pegawai_f;
			$t_barangjasa += $barangjasa_f;
			$t_modal += $modal_f;
			
			$rowsrek[] = array (
								 array('data' => $dataf->kodef,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataf->fungsi,  'width' => '285px', 'style ' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($gaji_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($nongaji_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($pegawai_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($barangjasa_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($modal_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($gaji_f+$nongaji_f+$pegawai_f+$barangjasa_f+$modal_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );		
				
			//2. URUSAN	
			$sql = sprintf(' where kodef=\'%s\'', db_escape_string($dataf->kodef));
			$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
			$resultu = db_query($sql);
			if ($resultu) {
				while ($datau = db_fetch_object($resultu)) {
					$gaji_u = 0;
					$nongaji_u = 0;
					$pegawai_u = 0;
					$barangjasa_u = 0;
					$modal_u = 0;
					
					$where = sprintf(' where p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
					
					//Belanja
					$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran from {anggperkeg} a inner join 
							{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
					$res = db_query($sql);	
					if ($res) 	{
						while ($data = db_fetch_object($res)) {
							switch($data->kode) {
								case '511':
									$gaji_u = $data->anggaran;
									break;
								case '521':
									$pegawai_u = $data->anggaran;
									break;
								case '522':
									$barangjasa_u = $data->anggaran;
									break;
								case '523':
									$modal_u = $data->anggaran;
									break;
								default:
									$nongaji_u += $data->anggaran;
									break;
							}
						}
						
						$rowsrek[] = array (
											 array('data' => $dataf->kodef . '.' . $datau->kodeu,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datau->urusan,  'width' => '285px', 'style ' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($gaji_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($nongaji_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($pegawai_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($barangjasa_u),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
											 array('data' => apbd_fn($modal_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($gaji_u+$nongaji_u+$pegawai_u+$barangjasa_u+$modal_u),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
											 );		
						
					}
				
				}
			}

			
		}	//looping u
	}


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '375px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_gaji),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_nongaji),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_gaji+$t_nongaji+$t_pegawai+$t_barangjasa+$t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;

	
	return $output;
	
}

function GenReportFormHeader($print=0) {
	
	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	//$rows= array();

	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}
	
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN V', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '525px', 'colspan'=>'3', 'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '100px', 'style' => 'border:none; text-align:right;'),
						 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdano , 'width' => '200px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdatgl, 'width' => '200px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
						 );
	
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI ANGGARAN BELANJA DAERAH UNTUK KESELARASAN DAN KETERPADUAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'URUSAN PEMERINTAHAN DAERAH DAN FUNGSI DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent() {
	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '50px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '285px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '180px', 'colspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA LANGSUNG',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '90px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
 
						 );	
	$headersrek[] = array (

						 array('data' => 'PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'NON PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'PEGAWAI',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'BARANG JASA',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'MODAL',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	//1) FUNGSI
	$t_gaji =0;
	$t_nongaji =0;
	$t_pegawai = 0;
	$t_barangjasa = 0;
	$t_modal = 0;

	//Belanja
	$sql = 'select kodef, fungsi from {fungsi} order by kodef';
	$resultf = db_query($sql);	
	if ($resultf) 	{
		while ($dataf = db_fetch_object($resultf)) {
			
			$gaji_f = 0;
			$nongaji_f = 0;
			$pegawai_f = 0;
			$barangjasa_f = 0;
			$modal_f = 0;
			
			$where = sprintf(' where u.kodef=\'%s\'', db_escape_string($dataf->kodef));
			
			//Belanja
			$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran from {anggperkeg} a inner join 
					{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro inner join {urusan} u on p.kodeu=u.kodeu ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
			//drupal_set_message($sql);
			$res = db_query($sql);	
			if ($res) 	{
				while ($data = db_fetch_object($res)) {
					switch($data->kode) {
						case '511':
							$gaji_f = $data->anggaran;
							break;
						case '521':
							$pegawai_f = $data->anggaran;
							break;
						case '522':
							$barangjasa_f = $data->anggaran;
							break;
						case '523':
							$modal_f = $data->anggaran;
							break;
						default:
							$nongaji_f += $data->anggaran;
							break;
					}
				}
			}		
			
			$t_gaji += $gaji_f;
			$t_nongaji += $nongaji_f;
			$t_pegawai += $pegawai_f;
			$t_barangjasa += $barangjasa_f;
			$t_modal += $modal_f;
			
			$rowsrek[] = array (
								 array('data' => $dataf->kodef,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataf->fungsi,  'width' => '285px', 'style ' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($gaji_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($nongaji_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($pegawai_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($barangjasa_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($modal_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($gaji_f+$nongaji_f+$pegawai_f+$barangjasa_f+$modal_f),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );		
				
			//2. URUSAN	
			$sql = sprintf(' where kodef=\'%s\'', db_escape_string($dataf->kodef));
			$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
			$resultu = db_query($sql);
			if ($resultu) {
				while ($datau = db_fetch_object($resultu)) {
					$gaji_u = 0;
					$nongaji_u = 0;
					$pegawai_u = 0;
					$barangjasa_u = 0;
					$modal_u = 0;
					
					$where = sprintf(' where p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
					
					//Belanja
					$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran from {anggperkeg} a inner join 
							{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
					$res = db_query($sql);	
					if ($res) 	{
						while ($data = db_fetch_object($res)) {
							switch($data->kode) {
								case '511':
									$gaji_u = $data->anggaran;
									break;
								case '521':
									$pegawai_u = $data->anggaran;
									break;
								case '522':
									$barangjasa_u = $data->anggaran;
									break;
								case '523':
									$modal_u = $data->anggaran;
									break;
								default:
									$nongaji_u += $data->anggaran;
									break;
							}
						}
						
						$rowsrek[] = array (
											 array('data' => $dataf->kodef . '.' . $datau->kodeu,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datau->urusan,  'width' => '285px', 'style ' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($gaji_u),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
											 array('data' => apbd_fn($nongaji_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($pegawai_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($barangjasa_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($modal_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($gaji_u+$nongaji_u+$pegawai_u+$barangjasa_u+$modal_u),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );		
						
					}
				
				}
			}

			
		}	//looping u
	}


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_gaji),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_nongaji),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_gaji+$t_nongaji+$t_pegawai+$t_barangjasa+$t_modal),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	
	return $output;
	
}

function GenReportFormFooter() {
	
	
	$pimpinannama= 'AHMAD MARZUQI';
	$pimpinanjabatan= 'BUPATI JEPARA';


	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center;'),
						 );

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));	
	
	return $output;
}

function lampiran5_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$topmargin = arg(4);
	$hal1 = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
 
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
	$form['formdata']['hal1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Halaman #1', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Halaman #1 dari laporan, isikan 9999 bila menghendaki agar nomor halaman tidak muncul', 		
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $hal1, 
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
function lampiran5_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporanpenetapan/apbd/lampiran5/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporanpenetapan/apbd/lampiran5/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}
?>