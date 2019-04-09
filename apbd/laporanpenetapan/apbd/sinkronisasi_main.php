<?php
function sinkronisasi_main() { 
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$prov = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(7);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
	if ($prov=='1') $tprov = 'prov';

	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-sinkronisasi.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($tprov);
		$htmlContent = GenReportFormContent($tprov);
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$output = drupal_get_form('sinkronisasi_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($tprov);
		return $output;
	}

}
function GenReportForm($prov) {
	
	$rowsjudul[] = array (array ('data'=>'Sinkronisasi Kebijakan Pemerintah Kabupten Jepara dalam', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Daerah tentang APBD Tahun Anggaran 2016 dan', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Kepala Daerah tentang Penjabaran APBD Tahun Anggaran 2016', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	if ($prov=='prov') {
		$strbidang = 'Provinsi';
		$rowsjudul[] = array (array ('data'=>'dengan Bidang Pembangunan Provinsi', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	} else {
		$strbidang = 'Nasional';
		$rowsjudul[] = array (array ('data'=>'dengan Bidang Pembangunan Nasional', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	}

	$headersrek[] = array (
						 
						 array('data' => 'No',  'width'=> '35px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Bidang Pembangunan ' . $strbidang,  'width' => '300px','colspan'=>'2','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian', 'width' => '270px', 'colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Alokasi Anggaran Belanja dalam Rancangan APBD',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'Program',  'width' => '180px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Program (Rp)',  'width' => '90px', 'colspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung (Rp)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah (Rp)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );						 

	//1) BIDANG
	$nomor =0;
	$tbl = 0;
	$tbtl = 0;
	$sql = 'select kodebid, namabid from {bidang} order by kodebid';
	$resultbid = db_query($sql);	
	if ($resultbid) 	{
		while ($databid = db_fetch_object($resultbid)) {
			
			$nomor++;
			$bidnumbl = 0;
			$bidnumbtl = 0;
			
			$where = sprintf(' where k.inaktif=0 and p.kodebid=\'%s\'', db_escape_string($databid->kodebid));
			$sql = 'select k.jenis,sum(k.total) totalbid from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno= pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
			//drupal_set_message($sql);
			$resultbidnum = db_query($sql);	
			if ($resultbid) 	{
				while ($databidnum = db_fetch_object($resultbidnum)) {
					if ($databidnum->jenis==2)
						$bidnumbl = $databidnum->totalbid;
					else
						$bidnumbtl = $databidnum->totalbid;
				}
			}		

			$tbl += $bidnumbl;
			$tbtl += $bidnumbtl;
			
			$rowsrek[] = array (
								 array('data' => $nomor . '.',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $databid->namabid,  'width' => '300px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '180px','colspan'=>'2', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbl + $bidnumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );		
				
			//2. PRIORITASS	
			$where = sprintf(' where p.kodebid=\'%s\'', db_escape_string($databid->kodebid));
			$sql = 'select distinct p.* from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno ' . $where . ' order by p.kodebid,p.prioritasno';
			//drupal_set_message($sql);
			$resultu = db_query($sql);
			
			$pbul = 96;
			if ($resultu) {
				while ($datau = db_fetch_object($resultu)) {
					
					$unumbl = 0;
					$unumbtl = 0;
					
					$where = sprintf(' where k.inaktif=0 and p.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));
					
					$sql = 'select k.jenis,sum(k.total) totalu from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
					$resunom = db_query($sql);	
					
					if ($resunom) 	{
						while ($dataunom = db_fetch_object($resunom)) {
							if ($dataunom->jenis==1)
								$unumbtl = $dataunom->totalu;
							else
								$unumbl = $dataunom->totalu;
						}
					}	
					$pbul++;
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => chr($pbul) . '.',  'width' => '20px', 'style ' => ' text-align:left;'),
										 array('data' => $datau->uraian,  'width' => '20px', 'style ' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '',  'width' => '180px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($unumbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($unumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($unumbl+$unumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:bold;'),
									);		
					
					$where = sprintf(' and k.inaktif=0 and pp.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));

					//BTL
					$sql = 'select j.kodej, j.uraian, sum(agg.jumlah) totalbtl from anggperkeg agg inner join kegiatanskpd k on agg.kodekeg=k.kodekeg
					inner join prioritasprogram' . $prov . ' pp on k.kodepro=pp.kodepro
					inner join jenis j on left(agg.kodero,3)=j.kodej
					where k.jenis=1 ' . $where . ' group by j.kodej, j.uraian';
					$resbtl = db_query($sql);
					
					//BL
					$sql = 'select pro.kodepro, pro.program, sum(k.total) totalbl 
					from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno 
					inner join program pro on pp.kodepro=pro.kodepro 
					inner join kegiatanskpd k on pro.kodepro=k.kodepro  
					where k.jenis=2 ' . $where . ' group by pro.kodepro, pro.program';
					$resbl = db_query($sql);
					if ($resbl) {
						while ($databl = db_fetch_object($resbl)) {
							
							if ($databtl = db_fetch_object($resbtl)) {
								$btluraian = ucfirst(strtolower($databtl->uraian));
								$btlnom = $databtl->totalbtl;
							} else {
								$btluraian = '';
								$btlnom = 0;
							}
								
 
							$rowsrek[] = array (
												 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => '',  'width' => '300px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '-',  'width' => '20px', 'style' => ' text-align:center;'),
												 array('data' => $databl->program,  'width' => '160px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => $btluraian,  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($databl->totalbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($btlnom),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
												 array('data' => apbd_fn($databl->totalbl+$btlnom),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
												 );								
						}
					}
				
				}
			}

			
		}	//looping u
	}


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '375px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '180px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl),  'width' =>  '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbtl),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl+$tbtl),  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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

function GenReportFormHeader($prov) {
	

	$rowsjudul[] = array (array ('data'=>'Sinkronisasi Kebijakan Pemerintah Kabupten Jepara dalam', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Daerah tentang APBD Tahun Anggaran 2016 dan', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Kepala Daerah tentang Penjabaran APBD Tahun Anggaran 2016', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	if ($prov=='prov')
		$rowsjudul[] = array (array ('data'=>'dengan Bidang Pembangunan Provinsi', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	else
		$rowsjudul[] = array (array ('data'=>'dengan Bidang Pembangunan Nasional', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($prov) {

	if ($prov=='prov') {
		$strbidang = 'Provinsi';
	} else {
		$strbidang = 'Nasional';
	}
	
	$headersrek[] = array (
						 
						 array('data' => 'No',  'width'=> '35px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Bidang Pembangunan ' . $strbidang,  'width' => '200px','colspan'=>'2','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian', 'width' => '370px', 'colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Alokasi Anggaran Belanja dalam Rancangan APBD',  'width' => '270px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'Program',  'width' => '280px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Program (Rp)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung (Rp)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Jumlah (Rp)',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	//1) BIDANG
	$nomor =0;
	$tbl = 0;
	$tbtl = 0;
	$sql = 'select kodebid, namabid from {bidang} order by kodebid';
	$resultbid = db_query($sql);	
	if ($resultbid) 	{
		while ($databid = db_fetch_object($resultbid)) {
			
			$nomor++;
			$bidnumbl = 0;
			$bidnumbtl = 0;
			
			$where = sprintf(' where k.inaktif=0 and p.kodebid=\'%s\'', db_escape_string($databid->kodebid));
			$sql = 'select k.jenis,sum(k.total) totalbid from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno= pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
			//drupal_set_message($sql);
			$resultbidnum = db_query($sql);	
			if ($resultbid) 	{
				while ($databidnum = db_fetch_object($resultbidnum)) {
					if ($databidnum->jenis==2)
						$bidnumbl = $databidnum->totalbid;
					else
						$bidnumbtl = $databidnum->totalbid;
				}
			}		

			$tbl += $bidnumbl;
			$tbtl += $bidnumbtl;
			
			$rowsrek[] = array (
								 array('data' => $nomor . '.',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $databid->namabid,  'width' => '200px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '280px','colspan'=>'2', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($bidnumbl + $bidnumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );		
				
			//2. PRIORITASS	
			$where = sprintf(' where p.kodebid=\'%s\'', db_escape_string($databid->kodebid));
			$sql = 'select distinct p.* from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno ' . $where . ' order by p.kodebid,p.prioritasno';
			//drupal_set_message($sql);
			$resultu = db_query($sql);
			
			$pbul = 96;
			if ($resultu) {
				while ($datau = db_fetch_object($resultu)) {
					
					$unumbl = 0;
					$unumbtl = 0;
					
					$where = sprintf(' where k.inaktif=0 and p.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));
					
					$sql = 'select k.jenis,sum(k.total) totalu from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
					$resunom = db_query($sql);	
					
					if ($resunom) 	{
						while ($dataunom = db_fetch_object($resunom)) {
							if ($dataunom->jenis==1)
								$unumbtl = $dataunom->totalu;
							else
								$unumbl = $dataunom->totalu;
						}
					}	
					$pbul++;
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => chr($pbul) . '.',  'width' => '20px', 'style ' => ' text-align:center;'),
										 array('data' => $datau->uraian,  'width' => '180px', 'style ' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '',  'width' => '280px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($unumbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($unumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($unumbl+$unumbtl),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:bold;'),
									);		
					
					$where = sprintf(' and k.inaktif=0 and pp.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));

					//BTL
					$sql = 'select j.kodej, j.uraian, sum(agg.jumlah) totalbtl from anggperkeg agg inner join kegiatanskpd k on agg.kodekeg=k.kodekeg
					inner join prioritasprogram' . $prov . ' pp on k.kodepro=pp.kodepro
					inner join jenis j on left(agg.kodero,3)=j.kodej
					where k.jenis=1 ' . $where . ' group by j.kodej, j.uraian';
					$resbtl = db_query($sql);
					
					//BL
					$sql = 'select pro.kodepro, pro.program, sum(k.total) totalbl 
					from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno 
					inner join program pro on pp.kodepro=pro.kodepro 
					inner join kegiatanskpd k on pro.kodepro=k.kodepro  
					where k.jenis=2 ' . $where . ' group by pro.kodepro, pro.program';
					$resbl = db_query($sql);

					if ($resbl) {
						while ($databl = db_fetch_object($resbl)) {
							
							if ($databtl = db_fetch_object($resbtl)) {
								$btluraian = ucfirst(strtolower($databtl->uraian));
								$btlnom = $databtl->totalbtl;
							} else {
								$btluraian = '';
								$btlnom = 0;
							}
								
 
							$rowsrek[] = array (
												 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => '',  'width' => '200px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '-',  'width' => '20px', 'style' => ' text-align:center;'),
												 array('data' => $databl->program,  'width' => '260px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => $btluraian,  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($databl->totalbl),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($btlnom),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
												 array('data' => apbd_fn($databl->totalbl+$btlnom),  'width' => '90px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
												 );								
						}
					}
				
				}
			}

			
		}	//looping u
	}


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '605px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center; font-weight:bold;'),
						 array('data' => apbd_fn($tbl),  'width' =>  '90px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbtl),  'width' => '90px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl+$tbtl),  'width' => '90px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
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

function sinkronisasi_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$prov = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(5);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
 
	$form['formdata']['prov']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $prov, 
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
function sinkronisasi_form_submit($form, &$form_state) {
	$prov = $form_state['values']['prov'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporanpenetapan/apbd/sinkronisasi/' . $prov . '/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporanpenetapan/apbd/sinkronisasi/' . $prov . '/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}
?>