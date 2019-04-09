<?php
function lampiran5_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(7);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	if ($revisi=='9') {
		$system_revisi =  variable_get('apbdrevisi', 1);
		$str_revisi = 'Terakhir (#' . $system_revisi . ')';		
		
		
	} else
		$str_revisi = '#' . $revisi;
	drupal_set_title('Lampiran V APBD - Revisi ' . $str_revisi);
	
	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran5.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($revisi);
		$htmlFooter = GenReportFormFooter();
		
		//apbd_ExportPDF3_CFM($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		apbd_ExportPDF3_CFM_5($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$url = 'apbd/laporan/apbd/lampiran5/'.$revisi.'/'. $topmargin . "/pdf";
		$output = drupal_get_form('lampiran5_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		//$output .= GenReportFormHeader(1);
		$output .= GenReportFormContent($revisi);
		return $output;
	}

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
							 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN V', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 array('data' => ':', 'width' => '10px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => 'PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '160px', 'colspan'=>'7',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'Nomor', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 array('data' => ':', 'width' => '10px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => $perdano , 'width' => '160px', 'colspan'=>'7',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'Tanggal', 'width' => '50px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 array('data' => ':', 'width' => '10px', 'style' => 'border-bottom: 1px solid black; text-align:right;font-size: 75%;'),
							 array('data' => $perdatgl , 'width' => '160px', 'colspan'=>'7',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							);
	
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI PERUBAHAN ANGGARAN BELANJA DAERAH UNTUK KESELARASAN DAN KETERPADUAN', 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'URUSAN PEMERINTAHAN DAERAH DAN FUNGSI DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA', 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContentBLBTL($revisi) {
	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '50px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '285px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '170px', 'colspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BELANJA LANGSUNG',  'width' => '305px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '90px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
 
						 );	
	$headersrek[] = array (

						 array('data' => 'PEGAWAI',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'NON PEGAWAI',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'PEGAWAI',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'BARANG JASA',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'MODAL',  'width' => '85px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
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
			$sql = 'select left(a.kodero,3) kode, sum(a.jumlahp) as anggaran from {anggperkegperubahan'.$str_table.'} a inner join 
					{kegiatanperubahan'.$str_table.'} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro inner join {urusan} u on p.kodeu=u.kodeu ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
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
								 array('data' => $dataf->kodef,  'width'=> '30px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataf->fungsi,  'width' => '285px', 'style ' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($gaji_f),  'width' => '85px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($nongaji_f),  'width' => '85px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($pegawai_f),  'width' => '85px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($barangjasa_f),  'width' => '85px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($modal_f),  'width' => '85px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
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
					$sql = 'select left(a.kodero,3) kode, sum(a.jumlahp) as anggaran from {anggperkegperubahan'.$str_table.'} a inner join 
							{kegiatanperubahan'.$str_table.'} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
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

function GenReportFormContent($revisi) {
	
	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '30px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 65%;'),
						 array('data' => '',  'width' => '170px','style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '260px', 'colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'SETELAH PERUBAHAN',  'width' => '260px', 'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'BERTAMBAH/',  'width' => '90px',  'colspan'=>'2','style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 65%;'),
 
						 );	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '30px', 'style' => ' border-left: 1px solid black; border-right: 1px solid black;  text-align:center;font-size: 65%;'),
						 array('data' => 'URAIAN',  'width' => '170px','style' => ' border-right: 1px solid black;  text-align:center;font-size: 65%;'),
						 array('data' => 'JENIS BELANJA', 'width' => '195px', 'colspan'=>'3','style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'JUMLAH', 'width' => '65px', 'style' => 'border-right: 1px solid black;  text-align:center;font-size: 65%;'),
						 array('data' => 'JENIS BELANJA', 'width' => '195px', 'colspan'=>'3','style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'JUMLAH', 'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:center;font-size: 65%;'),
						 array('data' => 'BERKURANG',  'width' => '90px',  'colspan'=>'2','style' => ' border-right: 1px solid black; border-bottom: 1px solid black;  text-align:center;font-size: 65%;'),
 
						 );						 
	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '30px', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => '',  'width' => '170px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'PEGAWAI', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'BARANG & JASA', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'MODAL', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => '', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'PEGAWAI', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'BARANG & JASA', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'MODAL', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => '', 'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'RUPIAH', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => '%',  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
 
						 );			
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	//1) FUNGSI
	$t_pegawai = 0;
	$t_barangjasa = 0;
	$t_modal = 0;
	$t_pegawai_p = 0;
	$t_barangjasa_p = 0;
	$t_modal_p = 0;

	//Belanja
	$sql = 'select kodef, fungsi from {fungsi} order by kodef';
	$resultf = db_query($sql);	
	if ($resultf) 	{
		while ($dataf = db_fetch_object($resultf)) {
			
			$pegawai_f = 0;
			$barangjasa_f = 0;
			$modal_f = 0;
			
			$pegawai_f_p = 0;
			$barangjasa_f_p = 0;
			$modal_f_p = 0;
			
			$where = sprintf(' where k.jenis=2 and u.kodef=\'%s\'', db_escape_string($dataf->kodef));
			
			//Belanja
			$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran, sum(a.jumlahp) as anggaranp from {anggperkegperubahan'.$str_table.'} a inner join 
					{kegiatanperubahan'.$str_table.'} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro inner join {urusan} u on p.kodeu=u.kodeu ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
			//drupal_set_message($sql);
			$res = db_query($sql);	
			if ($res) 	{
				while ($data = db_fetch_object($res)) {
					switch($data->kode) {
						case '521':
							$pegawai_f = $data->anggaran;
							$pegawai_f_p = $data->anggaranp;
							break;
						case '522':
							$barangjasa_f = $data->anggaran;
							$barangjasa_f_p = $data->anggaranp;
							break;
						case '523':
							$modal_f = $data->anggaran;
							$modal_f_p = $data->anggaranp;
							break;
					}
				}
			}		
			
			$t_pegawai += $pegawai_f;
			$t_barangjasa += $barangjasa_f;
			$t_modal += $modal_f;
			
			$t_pegawai_p += $pegawai_f_p;
			$t_barangjasa_p += $barangjasa_f_p;
			$t_modal_p += $modal_f_p;
			
			$rowsrek[] = array (
								 array('data' => $dataf->kodef,  'width'=> '30px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size: 65%;'),
								 array('data' => $dataf->fungsi,  'width' => '170px', 'style ' => ' border-right: 1px solid black; text-align:left;font-size: 65%;font-weight:bold;'),

								 array('data' => apbd_fn($pegawai_f),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($barangjasa_f),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($modal_f),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($pegawai_f + $barangjasa_f + $modal_f),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),

								 array('data' => apbd_fn($pegawai_f_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($barangjasa_f_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($modal_f_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn($pegawai_f_p + $barangjasa_f_p + $modal_f_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),

								 
								 array('data' => apbd_fn(($pegawai_f_p + $barangjasa_f_p + $modal_f_p)-($pegawai_f + $barangjasa_f + $modal_f)),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen(($pegawai_f + $barangjasa_f + $modal_f), ($pegawai_f_p + $barangjasa_f_p + $modal_f_p))),  'width' => '30px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;font-weight:bold;'),
								 );		
				
			//2. URUSAN	
			$sql = sprintf(' where kodef=\'%s\'', db_escape_string($dataf->kodef));
			$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
			$resultu = db_query($sql);
			if ($resultu) {
				while ($datau = db_fetch_object($resultu)) {
					$pegawai_u = 0;
					$barangjasa_u = 0;
					$modal_u = 0;

					$pegawai_u_p = 0;
					$barangjasa_u_p = 0;
					$modal_u_p = 0;
					
					$where = sprintf(' where k.jenis=2 and p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
					
					//Belanja
					$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) as anggaran, sum(a.jumlahp) as anggaranp from {anggperkegperubahan'.$str_table.'} a inner join 
							{kegiatanperubahan'.$str_table.'} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro ' . $where . ' and k.inaktif=0 group by left(a.kodero,3)';
					$res = db_query($sql);	
					if ($res) 	{
						while ($data = db_fetch_object($res)) {
							switch($data->kode) {
								case '521':
									$pegawai_u = $data->anggaran;
									$pegawai_u_p = $data->anggaranp;
									break;
								case '522':
									$barangjasa_u = $data->anggaran;
									$barangjasa_u_p = $data->anggaranp;
									break;
								case '523':
									$modal_u = $data->anggaran;
									$modal_u_p = $data->anggaranp;
									break;
							}
						}
						
						$rowsrek[] = array (
											 array('data' => $dataf->kodef . '.' . $datau->kodeu,  'width'=> '30px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size: 65%;'),
											 array('data' => $datau->urusan,  'width' => '170px', 'style ' => ' border-right: 1px solid black; text-align:left;font-size: 65%;'),

											 array('data' => apbd_fn($pegawai_u),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($barangjasa_u),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($modal_u),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($pegawai_u + $barangjasa_u + $modal_u),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),

											 array('data' => apbd_fn($pegawai_u_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($barangjasa_u_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($modal_u_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn($pegawai_u_p + $barangjasa_u_p + $modal_u_p),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),

											 
											 array('data' => apbd_fn(($pegawai_u_p + $barangjasa_u_p + $modal_u_p)-($pegawai_u + $barangjasa_u + $modal_u)),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 array('data' => apbd_fn1(apbd_hitungpersen(($pegawai_u + $barangjasa_u + $modal_u), ($pegawai_u_p + $barangjasa_u_p + $modal_u_p))),  'width' => '30px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 65%;'),
											 );				
						
					}
				
				}
			}

			
		}	//looping u
	}


	$rowsrek[] = array (
						 array('data' => '',  'width'=> '30px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-left: 1px solid black; text-align:left;font-size: 65%;'),
						 array('data' => 'TOTAL',  'width' => '170px', 'style ' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;font-size: 65%;font-weight:bold;'),

						 array('data' => apbd_fn($t_pegawai),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai + $t_barangjasa + $t_modal),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),

						 array('data' => apbd_fn($t_pegawai_p),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa_p),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_modal_p),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai_p + $t_barangjasa_p + $t_modal_p),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),

						 
						 array('data' => apbd_fn(($t_pegawai_p + $t_barangjasa_p + $t_modal_p)-($t_pegawai + $t_barangjasa + $t_modal)),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen(($t_pegawai + $t_barangjasa + $t_modal), ($t_pegawai_p + $t_barangjasa_p + $t_modal_p))),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size: 65%;font-weight:bold;'),
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
	$revisi = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(7);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
 
	
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
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/apbd/lampiran5/'.$revisi.'/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporan/apbd/lampiran5/'.$revisi.'/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}

function apbd_ExportPDF3_CFM_5($topmargin, $footermargin, $htmlContent1, $htmlContent2, $htmlContent3, $printlogo, $pdfFiel, $startpage) {
    require_once('files/tcpdf/config/lang/eng.php');
    require_once('files/tcpdf/tcpdf.php');
   
	$startpage -= 1;
	if ($startpage<0) $startpage = 0;
	$_SESSION["start"] = $startpage;
	class MYPDF extends TCPDF {  
	   // Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			//$this->SetY(-10);
			// Set font 
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			//$this->Cell(0, 10, 'Hal. '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');   
			//$halaman = $this->PageNo() + $_SESSION["start"];
			
			$this->Cell(0,0,'PEMERINTAH KABUPATEN JEPARA TA. ' . apbd_tahun(),'T',0,'L');
			$base = $_SESSION["start"];
			if ($base < 9998) {
				$halaman = $this->PageNo() + $base;
				$this->Cell(4,0,$halaman ,'T',0,'R');
			} else
				$this->Cell(0,0,'' , 'T',0,'');
			
		}      
	} 
	
    //$pdf = new TCPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
	$pdf = new MYPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
    set_time_limit(0);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('simAnggaran Online');
    $pdf->SetTitle('simAnggaran-Online');
    $pdf->SetSubject('PDF Gen');
    $pdf->SetKeywords('APBD');
    $pdf->setPrintHeader(false);
    $pdf->setFooterFont(array('helvetica','', 10));
	//$pdf->setFooterFont(array('times','', 12));
    
	$pdf->setRightMargin(1);
	$pdf->setLeftMargin(1);
    //$pdf->setFooterMargin(PDF_MARGIN_FOOTER);	
	
	$pdf->setHeaderMargin($topmargin);
	
	//$pdf->setFooterMargin($footermargin);
	//$pdf->SetAutoPageBreak(true, $footermargin);
	
	$pdf->setFooterMargin(10);
	$pdf->SetAutoPageBreak(true, 10);
	
	//$pdf->SetMargins(5,20);
	$pdf->SetMargins(22,$topmargin);
	
    //$pdf->SetAutoPageBreak(true, 11);
    $pdf->setLanguageArray($l);
    $pdf->SetFont('helvetica','', 10);
    $pdf->AddPage();

	//Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', 
	//$resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, 
	//$border=0, $fitbox=false, $hidden=false, $fitonpage=false)

	if ($printlogo)
		$pdf->Image('files/logo_kecil.png', 16, 20+$topmargin-10, 20, 18, 'PNG', '', '', 
				true, 150, '', false, false, 0, false, false, false);
	
	
    $pdf->writeHTML($htmlContent1, true, 0, true, 0);
 
	$ypos = $pdf->GetY()-13;
	$pdf->SetY($ypos, true, false);
	
	$pdf->writeHTML($htmlContent2, true, 0, true, 0);

	$ypos = $pdf->GetY()-13;
	$pdf->SetY($ypos, true, false);
	
	$pdf->writeHTML($htmlContent3, true, 0, true, 0);

    $pdf->Output($pdfFiel, 'I');
	
}


?>