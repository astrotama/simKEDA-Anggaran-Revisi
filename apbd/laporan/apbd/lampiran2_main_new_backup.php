<?php
//$form['path']['#access'] = FALSE;
function lampiran2_main() {
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
		
		$pdfFile = 'apbd-lampiran2.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent();
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$url = 'apbd/laporan/apbd/lampiran2/'. $topmargin . "/pdf";
		$output = drupal_get_form('lampiran2_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {

	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}
	

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup

	
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN II', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
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
	$rowsjudul[] = array (array ('data'=>'RINGKASAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH DAN ORGANISASI', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	


	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '75px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '300px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'PENDAPATAN', 'width' => '125px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'ANGGARAN BELANJA',  'width' => '375px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'TIDAK LANGSUNG',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'LANGSUNG',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'TOTAL BELANJA',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );						 

	//1) JENIS URUSAN
	$t_pendapatan =0;
	$t_btl =0;
	$t_bl = 0;

	for ($u=1; $u<=2; $u++) {
		
		$pendapatanju = 0;
		$btlju = 0;
		$blju = 0;
		
		$where = sprintf(' where left(u.kodeu, 1)=\'%s\'', db_escape_string($u));
		
		//Pendapatan
		$sql = 'select sum(k.jumlah) anggaran from {anggperuk} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where;
		$resultju = db_query($sql);	
		if ($resultju) 	{
			if ($dataju = db_fetch_object($resultju)) {
				$pendapatanju = $dataju->anggaran;
			}
		}	
		
		//Belanja
		$sql = 'select k.jenis, sum(k.total) as anggaran from {kegiatanskpd} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where . ' and k.inaktif=0 group by k.jenis';
		$resultju = db_query($sql);	
		if ($resultju) 	{
			while ($dataju = db_fetch_object($resultju)) {
				if ($dataju->jenis==1) 
					$btlju = $dataju->anggaran;
				else
					$blju = $dataju->anggaran;
			}
		}		
		
		$t_pendapatan += $pendapatanju;
		$t_btl += $btlju;
		$t_bl += $blju;
		
		//Render
		if ($u==1)
			$ju = 'URUSAN WAJIB';
		else
			$ju = 'URUSAN PILIHAN';
		
		$rowsrek[] = array (
							 array('data' => $u,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $ju,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($pendapatanju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($btlju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($blju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($btlju+$blju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 );		
		//2. URUSAN	
		$sql = sprintf(' where sifat=\'%s\'', db_escape_string($u));
		$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
		$resultu = db_query($sql);
		if ($resultu) {
			while ($datau = db_fetch_object($resultu)) {

				$pendapatanu = 0;
				$btlu = 0;
				$blu = 0;
			
				$whereu = sprintf(' where u.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				
				//PENDAPATAN
				$sql = 'select sum(k.jumlah) anggaran from {anggperuk} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu;
				$res = db_query($sql);	
				if ($res) 	{
					if ($data = db_fetch_object($res)) {
						$pendapatanu = $data->anggaran;
					}
				}	
				
				//Belanja
				$sql = 'select k.jenis, sum(k.total) as anggaran from {kegiatanskpd} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu . ' and k.inaktif=0 group by k.jenis';
				$res = db_query($sql);
				if ($res) 	{
					while ($data = db_fetch_object($res)) {
						if ($data->jenis==1) 
							$btlu = $data->anggaran;
						else
							$blu = $data->anggaran;
					}
				}				
				
				//Render
				$rowsrek[] = array (
									 array('data' => $datau->kodeu,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datau->urusan,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => apbd_fn($pendapatanu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($btlu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($blu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($btlu+$blu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 );					
				
				
				//SKPD
				
				$sql = 'select kodeuk, kodedinas, namauk from {unitkerja} u ' . $whereu . ' order by kodedinas';
				
				//drupal_set_message($sql);
				$resultuk = db_query($sql);
				if ($resultuk) {
					while ($datauk = db_fetch_object($resultuk)) {

						$pendapatanuk = 0;
						$btluk = 0;
						$bluk = 0;
					
						$whereuk = sprintf(' where kodeuk=\'%s\'', db_escape_string($datauk->kodeuk));
						
						//PENDAPATAN
						$sql = 'select sum(jumlah) anggaran from {anggperuk} ' . $whereuk;
						$res = db_query($sql);	
						if ($res) 	{
							if ($data = db_fetch_object($res)) {
								$pendapatanuk = $data->anggaran;
							}
						}	
						
						//Belanja
						$sql = 'select jenis, sum(total) as anggaran from {kegiatanskpd} ' . $whereuk . ' and inaktif=0 group by jenis';
						//drupal_set_message($sql);
						$res = db_query($sql);
						if ($res) 	{
							while ($data = db_fetch_object($res)) {
								if ($data->jenis==1) 
									$btluk = $data->anggaran;
								else
									$bluk = $data->anggaran;
							}
						}				
										
						//$newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
						
						$kode = substr_replace($datauk->kodedinas, '.', 3, 0);
						$rowsrek[] = array (
											 array('data' => $kode,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datauk->namauk,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($pendapatanuk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($btluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($bluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($btluk+$bluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );					

					}
				}
			}
		}	
		
	}	//looping u
	


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '375px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pendapatan),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_btl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_bl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_btl+$t_bl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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
	
	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}
	

	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN II', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
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
	$rowsjudul[] = array (array ('data'=>'RINGKASAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH DAN ORGANISASI', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent() {
	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '75px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '300px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'PENDAPATAN', 'width' => '125px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'ANGGARAN BELANJA',  'width' => '375px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'TIDAK LANGSUNG',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'LANGSUNG',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'TOTAL BELANJA',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );						 

	//1) JENIS URUSAN
	$t_pendapatan =0;
	$t_btl =0;
	$t_bl = 0;

	for ($u=1; $u<=2; $u++) {
		
		$pendapatanju = 0;
		$btlju = 0;
		$blju = 0;
		
		$where = sprintf(' where left(u.kodeu, 1)=\'%s\'', db_escape_string($u));
		
		//Pendapatan
		$sql = 'select sum(k.jumlah) anggaran from {anggperuk} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where;
		$resultju = db_query($sql);	
		if ($resultju) 	{
			if ($dataju = db_fetch_object($resultju)) {
				$pendapatanju = $dataju->anggaran;
			}
		}	
		
		//Belanja
		$sql = 'select k.jenis, sum(k.total) as anggaran from {kegiatanskpd} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where . ' and k.inaktif=0 group by k.jenis';
		$resultju = db_query($sql);	
		if ($resultju) 	{
			while ($dataju = db_fetch_object($resultju)) {
				if ($dataju->jenis==1) 
					$btlju = $dataju->anggaran;
				else
					$blju = $dataju->anggaran;
			}
		}		
		
		$t_pendapatan += $pendapatanju;
		$t_btl += $btlju;
		$t_bl += $blju;
		
		//Render
		if ($u==1)
			$ju = 'URUSAN WAJIB';
		else
			$ju = 'URUSAN PILIHAN';
		
		$rowsrek[] = array (
							 array('data' => $u,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $ju,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => apbd_fn($pendapatanju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($btlju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($blju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($btlju+$blju),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							 );		
		//2. URUSAN	
		$sql = sprintf(' where sifat=\'%s\'', db_escape_string($u));
		$sql = 'select kodeu, urusan from {urusan} ' . $sql . ' order by kodeu';
		$resultu = db_query($sql);
		if ($resultu) {
			while ($datau = db_fetch_object($resultu)) {

				$pendapatanu = 0;
				$btlu = 0;
				$blu = 0;
			
				$whereu = sprintf(' where u.kodeu=\'%s\'', db_escape_string($datau->kodeu));
				
				//PENDAPATAN
				$sql = 'select sum(k.jumlah) anggaran from {anggperuk} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu;
				$res = db_query($sql);	
				if ($res) 	{
					if ($data = db_fetch_object($res)) {
						$pendapatanu = $data->anggaran;
					}
				}	
				
				//Belanja
				$sql = 'select k.jenis, sum(k.total) as anggaran from {kegiatanskpd} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu . ' and k.inaktif=0 group by k.jenis';
				$res = db_query($sql);
				if ($res) 	{
					while ($data = db_fetch_object($res)) {
						if ($data->jenis==1) 
							$btlu = $data->anggaran;
						else
							$blu = $data->anggaran;
					}
				}				
				
				//Render
				
				$rowsrek[] = array (
									 array('data' => $datau->kodeu,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datau->urusan,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($pendapatanu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($btlu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($blu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($btlu+$blu),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
									 );					
				
				
				//SKPD
				
				$sql = 'select kodeuk, kodedinas, namauk from {unitkerja} u ' . $whereu . ' order by kodedinas';
				
				//drupal_set_message($sql);
				$resultuk = db_query($sql);
				if ($resultuk) {
					while ($datauk = db_fetch_object($resultuk)) {

						$pendapatanuk = 0;
						$btluk = 0;
						$bluk = 0;
					
						$whereuk = sprintf(' where kodeuk=\'%s\'', db_escape_string($datauk->kodeuk));
						
						//PENDAPATAN
						$sql = 'select sum(jumlah) anggaran from {anggperuk} ' . $whereuk;
						$res = db_query($sql);	
						if ($res) 	{
							if ($data = db_fetch_object($res)) {
								$pendapatanuk = $data->anggaran;
							}
						}	
						
						//Belanja
						$sql = 'select jenis, sum(total) as anggaran from {kegiatanskpd} ' . $whereuk . ' and inaktif=0 group by jenis';
						//drupal_set_message($sql);
						$res = db_query($sql);
						if ($res) 	{
							while ($data = db_fetch_object($res)) {
								if ($data->jenis==1) 
									$btluk = $data->anggaran;
								else
									$bluk = $data->anggaran;
							}
						}				
										
						
						$kode = substr_replace($datauk->kodedinas, '.', 3, 0);
						$rowsrek[] = array (
											 array('data' => $kode,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $datauk->namauk,  'width' => '300px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($pendapatanuk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($btluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($bluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 array('data' => apbd_fn($btluk+$bluk),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );					

					}
				}
			}
		}	
		
	}	//looping u
	


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '375px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_pendapatan),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_btl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_bl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($t_btl+$t_bl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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

function lampiran2_form () {
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
function lampiran2_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/apbd/lampiran2/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporan/apbd/lampiran2/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}
?>