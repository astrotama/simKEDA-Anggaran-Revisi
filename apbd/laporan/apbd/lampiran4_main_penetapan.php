<?php
function lampiran4_main() {
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
	drupal_set_title('Lampiran IV APBD - Revisi ' . $str_revisi);
	
	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran4.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($revisi);
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$url = 'apbd/laporan/apbd/lampiran4/'.$revisi.'/'. $topmargin . "/pdf";
		$output = drupal_get_form('lampiran4_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm(1);
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
						 array('data' => 'LAMPIRAN IV', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
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
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI BELANJA MENURUT URUSAN PEMERINTAHAN DAERAH - ORGANISASI - PROGRAM KEGIATAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	


	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '100px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '375px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JENIS BELANJA', 'width' => '300px','colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right:1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '100px', 'rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'PEGAWAI',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'BARANG JASA',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'MODAL',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	//1) JENIS URUSAN
	$t_pegawai =0;
	$t_barangjasa =0;
	$t_modal = 0;

	for ($u=0; $u<=2; $u++) {
		
		$pegawai_ju = 0;
		$barangjasa_ju = 0;
		$modal_ju = 0;
		
		$where = sprintf(' and left(p.kodeu, 1)=\'%s\'', db_escape_string($u));
		
		//Belanja
		$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k
		on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2  and k.inaktif=0 ' . $where . ' group by left(a.kodero,3);';
		$resultju = db_query($sql);	
		if ($resultju) 	{
			while ($dataju = db_fetch_object($resultju)) {
				if ($dataju->kode == '521') 
					$pegawai_ju = $dataju->anggaran;
				else if ($dataju->kode == '522') 
					$barangjasa_ju = $dataju->anggaran;
				else
					$modal_ju = $dataju->anggaran;
			}
		}		
		
		$t_pegawai += $pegawai_ju;
		$t_barangjasa += $barangjasa_ju;
		$t_modal += $modal_ju;
		
		//Render
		if ($u==0)
			$ju = 'URUSAN PADA SEMUA SKPD';
		else if ($u==1)
			$ju = 'URUSAN WAJIB';
		else
			$ju = 'URUSAN PILIHAN';
		
		$rowsrek[] = array (
							 array('data' => $u,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $ju,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => apbd_fn($pegawai_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($barangjasa_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($modal_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($pegawai_ju+$barangjasa_ju+$modal_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );		
		//2. URUSAN	
		$sql = sprintf(' where sifat=\'%s\'', db_escape_string($u));
		$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
		
		//drupal_set_message($sql);
		
		$result_u = db_query($sql);
		if ($result_u) {
			while ($datau = db_fetch_object($result_u)) {

				$pegawai_u = 0;
				$barangjasa_u = 0;
				$modal_u = 0;
			
				$whereu = sprintf(' and p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				
				$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join 
						{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $whereu . ' group by left(a.kodero,3)';
				//drupal_set_message($sql);
				$res = db_query($sql);	
				if ($res) 	{
					while ($data = db_fetch_object($res)) {
						if ($data->kode == '521') 
							$pegawai_u = $data->anggaran;
						else if ($data->kode == '522') 
							$barangjasa_u = $data->anggaran;
						else
							$modal_u = $data->anggaran;
					}				
				}	
				
				//Render
				$rowsrek[] = array (
									 array('data' => $datau->kodeu,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datau->urusan,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left; font-weight:bold;'),
									 array('data' => apbd_fn($pegawai_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($barangjasa_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($modal_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($pegawai_u+$barangjasa_u+$modal_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 );					
				
				
				//SKPD
				$sql = sprintf(' and p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				$sql = 'select distinct u.kodeuk, u.kodedinas, u.namauk from {unitkerja} u inner join {kegiatanskpd} k 
						on u.kodeuk=k.kodeuk inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $sql . ' order by u.kodedinas';
				
				//drupal_set_message($sql);
				$result_uk = db_query($sql);
				if ($result_uk) {
					while ($datauk = db_fetch_object($result_uk)) {

						$pegawai_uk = 0;
						$barangjasa_uk = 0;
						$modal_uk = 0;
					
						$whereuk = sprintf(' and k.kodeuk=\'%s\' and p.kodeu=\'%s\'', db_escape_string($datauk->kodeuk), db_escape_string($datau->kodeu));
						
						//Belanja
						$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join 
								{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $whereuk . ' group by left(a.kodero,3)';
						//drupal_set_message($sql);
						$res = db_query($sql);
						if ($res) 	{
							while ($data = db_fetch_object($res)) {
								if ($data->kode == '521') 
									$pegawai_uk = $data->anggaran;
								else if ($data->kode == '522') 
									$barangjasa_uk = $data->anggaran;
								else
									$modal_uk = $data->anggaran;
								}
						}				
										
				
						$rowsrek[] = array (
											 array('data' => $datau->kodeu . '.' . $datauk->kodedinas,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datauk->namauk,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($pegawai_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
											 array('data' => apbd_fn($barangjasa_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
											 array('data' => apbd_fn($modal_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 array('data' => apbd_fn($pegawai_uk+$barangjasa_uk+$modal_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 );	
						//PROGRAM
						$sql = sprintf(' and p.kodeu=\'%s\' and k.kodeuk=\'%s\'', db_escape_string($datau->kodeu), db_escape_string($datauk->kodeuk));
						$sql = 'select distinct p.kodepro, p.program from {kegiatanskpd} k 
								inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $sql . ' order by p.kodepro';
						
						//drupal_set_message($sql);
						$result_pro = db_query($sql);
						if ($result_pro) {
							while ($datapro = db_fetch_object($result_pro)) {

								$pegawai_pro = 0;
								$barangjasa_pro = 0;
								$modal_pro = 0;
							
								$wherepro = sprintf(' and k.kodeuk=\'%s\' and k.kodepro=\'%s\'', db_escape_string($datauk->kodeuk), db_escape_string($datapro->kodepro));
								
								//Belanja
								$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $wherepro . ' group by left(a.kodero,3)';
								//drupal_set_message($sql);
								$res = db_query($sql);
								if ($res) 	{
									while ($data = db_fetch_object($res)) {
										if ($data->kode == '521') 
											$pegawai_pro = $data->anggaran;
										else if ($data->kode == '522') 
											$barangjasa_pro = $data->anggaran;
										else
											$modal_pro = $data->anggaran;
										}
								}
								$rowsrek[] = array (
													 array('data' => $datau->kodeu . '.' . $datauk->kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $datapro->program,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => apbd_fn($pegawai_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($barangjasa_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($modal_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($pegawai_pro+$barangjasa_pro+$modal_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 );	
													 
								//KEGIATAN
								$sql = sprintf(' and kodepro=\'%s\' and kodeuk=\'%s\'', db_escape_string($datapro->kodepro), db_escape_string($datauk->kodeuk));
								$sql = 'select kodekeg, kodepro, nomorkeg, kegiatan from {kegiatanskpd} where jenis=2 and inaktif=0 ' . $sql . ' order by kodepro, nomorkeg';													 
								$result_keg = db_query($sql);
								if ($result_keg) {
									while ($datakeg = db_fetch_object($result_keg)) {

										$pegawai_keg = 0;
										$barangjasa_keg = 0;
										$modal_keg = 0;
									
										$wherekeg = sprintf(' and k.kodekeg=\'%s\'', db_escape_string($datakeg->kodekeg));									
										//Belanja
										$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.jenis=2 and k.inaktif=0 ' . $wherekeg . ' group by left(a.kodero,3)';
										//drupal_set_message($sql);
										$res = db_query($sql);
										if ($res) 	{
											while ($data = db_fetch_object($res)) {
												if ($data->kode == '521') 
													$pegawai_keg = $data->anggaran;
												else if ($data->kode == '522') 
													$barangjasa_keg = $data->anggaran;
												else
													$modal_keg = $data->anggaran;
												}
										}		
										
										//Render
										$rowsrek[] = array (
															 array('data' => $datau->kodeu . '.' . $datauk->kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datakeg->kegiatan,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($pegawai_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($barangjasa_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($modal_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($pegawai_keg+$barangjasa_keg+$modal_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 );	
										
									}
								}
								
							}
						}
					}
				}
			}
		}	
		
	}	//looping u
	


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '475px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai+$t_barangjasa+$t_modal),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	return $output;
}

function GenReportFormHeader($print=0) {
	
	/*
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI BELANJA MENURUT URUSAN PEMERINTAHAN DAERAH - ORGANISASI - PROGRAM KEGIATAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));


	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	$output .= $toutput;
	*/

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup

	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}

	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN IV', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '525px', 'colspan'=>'3', 'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '100px', 'style' => 'border:none; text-align:right;'),
						 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdano, 'width' => '200px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdatgl, 'width' => '200px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
						 );
						 
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI BELANJA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'MENURUT URUSAN PEMERINTAHAN DAERAH - ORGANISASI - PROGRAM - KEGIATAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	//$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	return $output;	
	
}

function GenReportFormContent() {

	set_time_limit(0);
	ini_set('memory_limit', '640M');
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '100px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '375px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JENIS BELANJA', 'width' => '300px','colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right:1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '100px', 'rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'PEGAWAI',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'BARANG JASA',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'MODAL',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	//1) JENIS URUSAN
	$t_pegawai =0;
	$t_barangjasa =0;
	$t_modal = 0;

	for ($u=0; $u<=2; $u++) {
		
		$pegawai_ju = 0;
		$barangjasa_ju = 0;
		$modal_ju = 0;
		
		$where = sprintf(' and left(p.kodeu, 1)=\'%s\'', db_escape_string($u));
		
		//Belanja
		$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k
		on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2  and k.inaktif=0 ' . $where . ' group by left(a.kodero,3);';
		$resultju = db_query($sql);	
		if ($resultju) 	{
			while ($dataju = db_fetch_object($resultju)) {
				if ($dataju->kode == '521') 
					$pegawai_ju = $dataju->anggaran;
				else if ($dataju->kode == '522') 
					$barangjasa_ju = $dataju->anggaran;
				else
					$modal_ju = $dataju->anggaran;
			}
		}		
		
		$t_pegawai += $pegawai_ju;
		$t_barangjasa += $barangjasa_ju;
		$t_modal += $modal_ju;
		
		//Render
		if ($u==0)
			$ju = 'URUSAN PADA SEMUA SKPD';
		else if ($u==1)
			$ju = 'URUSAN WAJIB';
		else
			$ju = 'URUSAN PILIHAN';
		
		$rowsrek[] = array (
							 array('data' => $u,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $ju,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => apbd_fn($pegawai_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($barangjasa_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($modal_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($pegawai_ju+$barangjasa_ju+$modal_ju),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );		
		//2. URUSAN	
		$sql = sprintf(' where sifat=\'%s\'', db_escape_string($u));
		$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
		
		//drupal_set_message($sql);
		
		$result_u = db_query($sql);
		if ($result_u) {
			while ($datau = db_fetch_object($result_u)) {

				$pegawai_u = 0;
				$barangjasa_u = 0;
				$modal_u = 0;
			
				$whereu = sprintf(' and p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				
				$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join 
						{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $whereu . ' group by left(a.kodero,3)';
				//drupal_set_message($sql);
				$res = db_query($sql);	
				if ($res) 	{
					while ($data = db_fetch_object($res)) {
						if ($data->kode == '521') 
							$pegawai_u = $data->anggaran;
						else if ($data->kode == '522') 
							$barangjasa_u = $data->anggaran;
						else
							$modal_u = $data->anggaran;
					}				
				}	
				
				//Render
				$rowsrek[] = array (
									 array('data' => $datau->kodeu,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datau->urusan,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left; font-weight:bold;'),
									 array('data' => apbd_fn($pegawai_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($barangjasa_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($modal_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($pegawai_u+$barangjasa_u+$modal_u),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 );					
				
				
				//SKPD
				$sql = sprintf(' and p.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				$sql = 'select distinct u.kodeuk, u.kodedinas, u.namauk from {unitkerja} u inner join {kegiatanskpd} k 
						on u.kodeuk=k.kodeuk inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $sql . ' order by u.kodedinas';
				
				//drupal_set_message($sql);
				$result_uk = db_query($sql);
				if ($result_uk) {
					while ($datauk = db_fetch_object($result_uk)) {

						$pegawai_uk = 0;
						$barangjasa_uk = 0;
						$modal_uk = 0;
					
						$whereuk = sprintf(' and k.kodeuk=\'%s\' and p.kodeu=\'%s\'', db_escape_string($datauk->kodeuk), db_escape_string($datau->kodeu));
						
						//Belanja
						$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join 
								{kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $whereuk . ' group by left(a.kodero,3)';
						//drupal_set_message($sql);
						$res = db_query($sql);
						if ($res) 	{
							while ($data = db_fetch_object($res)) {
								if ($data->kode == '521') 
									$pegawai_uk = $data->anggaran;
								else if ($data->kode == '522') 
									$barangjasa_uk = $data->anggaran;
								else
									$modal_uk = $data->anggaran;
								}
						}				
										
				
						$rowsrek[] = array (
											 array('data' => $datau->kodeu . '.' . $datauk->kodedinas,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datauk->namauk,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($pegawai_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
											 array('data' => apbd_fn($barangjasa_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
											 array('data' => apbd_fn($modal_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 array('data' => apbd_fn($pegawai_uk+$barangjasa_uk+$modal_uk),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 );	
						//PROGRAM
						$sql = sprintf(' and p.kodeu=\'%s\' and k.kodeuk=\'%s\'', db_escape_string($datau->kodeu), db_escape_string($datauk->kodeuk));
						$sql = 'select distinct p.kodepro, p.program from {kegiatanskpd} k 
								inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $sql . ' order by p.kodepro';
						
						//drupal_set_message($sql);
						$result_pro = db_query($sql);
						if ($result_pro) {
							while ($datapro = db_fetch_object($result_pro)) {

								$pegawai_pro = 0;
								$barangjasa_pro = 0;
								$modal_pro = 0;
							
								$wherepro = sprintf(' and k.kodeuk=\'%s\' and k.kodepro=\'%s\'', db_escape_string($datauk->kodeuk), db_escape_string($datapro->kodepro));
								
								//Belanja
								$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {program} p on k.kodepro=p.kodepro where k.jenis=2 and k.inaktif=0 ' . $wherepro . ' group by left(a.kodero,3)';
								//drupal_set_message($sql);
								$res = db_query($sql);
								if ($res) 	{
									while ($data = db_fetch_object($res)) {
										if ($data->kode == '521') 
											$pegawai_pro = $data->anggaran;
										else if ($data->kode == '522') 
											$barangjasa_pro = $data->anggaran;
										else
											$modal_pro = $data->anggaran;
										}
								}
								$rowsrek[] = array (
													 array('data' => $datau->kodeu . '.' . $datauk->kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $datapro->program,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => apbd_fn($pegawai_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($barangjasa_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($modal_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 array('data' => apbd_fn($pegawai_pro+$barangjasa_pro+$modal_pro),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 );	
													 
								//KEGIATAN
								$sql = sprintf(' and kodepro=\'%s\' and kodeuk=\'%s\'', db_escape_string($datapro->kodepro), db_escape_string($datauk->kodeuk));
								$sql = 'select kodekeg, kodepro, nomorkeg, kegiatan from {kegiatanskpd} where jenis=2 and inaktif=0 ' . $sql . ' order by kodepro, nomorkeg';													 
								$result_keg = db_query($sql);
								if ($result_keg) {
									while ($datakeg = db_fetch_object($result_keg)) {

										$pegawai_keg = 0;
										$barangjasa_keg = 0;
										$modal_keg = 0;
									
										$wherekeg = sprintf(' and k.kodekeg=\'%s\'', db_escape_string($datakeg->kodekeg));									
										//Belanja
										$sql = 'select left(a.kodero,3) kode, sum(a.jumlah) anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.jenis=2 and k.inaktif=0 ' . $wherekeg . ' group by left(a.kodero,3)';
										//drupal_set_message($sql);
										$res = db_query($sql);
										if ($res) 	{
											while ($data = db_fetch_object($res)) {
												if ($data->kode == '521') 
													$pegawai_keg = $data->anggaran;
												else if ($data->kode == '522') 
													$barangjasa_keg = $data->anggaran;
												else
													$modal_keg = $data->anggaran;
												}
										}		
										
										//Render
										$rowsrek[] = array (
															 array('data' => $datau->kodeu . '.' . $datauk->kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datakeg->kegiatan,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($pegawai_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($barangjasa_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($modal_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($pegawai_keg+$barangjasa_keg+$modal_keg),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 );	
										
									}
								}
								
							}
						}
					}
				}
			}
		}	
		
	}	//looping u
	


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '475px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_barangjasa),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_modal),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pegawai+$t_barangjasa+$t_modal),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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

function lampiran4_form () {
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
	$form['formdata']['revisi'] = array (
		'#type' => 'value',
		'#default_value' => $revisi,
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
function lampiran4_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/apbd/lampiran4/' .$revisi.'/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporan/apbd/lampiran4/9'.$revisi.'/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}
?>