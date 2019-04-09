<?php
//$form['path']['#access'] = FALSE;
function lampiran2_main() {
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
	drupal_set_title('Lampiran II APBD - Revisi ' . $str_revisi);

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran2.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($revisi);
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CFM_2($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$url = 'apbd/laporan/apbd/lampiran2/'.$revisi.'/'. $topmargin . "/pdf";
		$output = drupal_get_form('lampiran2_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($revisi);
		return $output;
	}

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
						 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN II', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 array('data' => ':', 'width' => '10px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => 'PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '160px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
						 array('data' => 'Nomor', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 array('data' => ':', 'width' => '10px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => $perdano , 'width' => '160px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
						 array('data' => 'Tanggal', 'width' => '50px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
						 array('data' => ':', 'width' => '10px', 'style' => 'border-bottom: 1px solid black; text-align:right;font-size: 75%;'),
						 array('data' => $perdatgl , 'width' => '160px', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
						);
	/*
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '575px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN II', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
						 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '475px', 'colspan'=>'3', 'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '100px', 'style' => 'border:none; text-align:right;'),
						 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdano, 'width' => '200px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
						 );
	$rowslampiran[]= array (
						 array('data' => '',  'width'=> '525px','colspan'=>'3',  'style' => 'border:none; text-align:left;'),
						 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
						 array('data' => ': ' . $perdatgl, 'width' => '200px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
						 );
	*/					 
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'RINGKASAN PERUBAHAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH DAN ORGANISASI', 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'810px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($revisi) {
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;


	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '25px','style' => ' border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 75%;'),
						 array('data' => '',  'width' => '105px','style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'PENDAPATAN', 'width' => '210px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'BELANJA',  'width' => '470px', 'colspan'=>'8', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size: 75%;'),

						 );	
	$headersrek[] = array (
						 
						 array('data' => 'KODE',  'width'=> '25px','style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'URAIAN',  'width' => '105px','style' => 'border-right: 1px solid black; text-align:center;font-size: 75%;'),

						 array('data' => 'SEBELUM', 'width' => '65px','style' => 'border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'SETELAH', 'width' => '65px','style' => 'border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'BERTAMBAH/ BERKURANG', 'width' => '80px','colspan'=>'2','style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 60%;'),
						 
						 array('data' => 'SEBELUM PERUBAHAN',  'width' => '195px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'SETELAH PERUBAHAN',  'width' => '195px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 75%;'),

						 array('data' => 'BERTAMBAH/ BERKURANG',  'width' => '80px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size: 60%;'),
						 
						 );	

	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '25px','style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => '',  'width' => '105px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),

						 array('data' => 'PERUBAHAN', 'width' => '65px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'PERUBAHAN', 'width' => '65px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'RUPIAH', 'width' => '60px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => '%', 'width' => '20px','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						  
						 array('data' => 'TIDAK LANGSUNG',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'LANGSUNG',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'JUMLAH',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),

						 array('data' => 'TIDAK LANGSUNG',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 65%;'),
						 array('data' => 'LANGSUNG',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => 'JUMLAH',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 
						 array('data' => 'RUPIAH',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 array('data' => '%',  'width' => '20px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size: 75%;'),
						 
						 );							 


	//1) JENIS URUSAN
	$t_pendapatanju =0;
	$t_pendapatanjup =0;
	$t_pendapatanjut =0;
	$t_btlju =0;
	$t_btljup =0;
	$t_blju =0;
	$t_bljup =0;
	$tot_1 =0;
	$tot_2 =0;
	$tot_3 =0;

	for ($u=1; $u<=2; $u++) {
		
		$pendapatanju = 0;
		$btlju = 0;
		$blju = 0;
		
		$where = sprintf(' where left(u.kodeu, 1)=\'%s\'', db_escape_string($u));
		
		//Pendapatan
		$sql = 'select sum(k.jumlah) jumlahx, sum(k.jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where;
		$resultju = db_query($sql);	
		if ($resultju) 	{
			if ($dataju = db_fetch_object($resultju)) {
				$pendapatanju = $dataju->jumlahx;
				$pendapatanjup = $dataju->jumlahxp;
				$pendapatanjut = $dataju->jumlahxp-$dataju->jumlahx;
			}
		}	
		
		//Belanja
		$sql = 'select k.jenis, sum(k.total) as jumlahx,sum(k.totalp) as jumlahxp from {kegiatanperubahan' . $str_table . '} k inner join {unitkerja} u on
				k.kodeuk=u.kodeuk ' . $where . ' and k.inaktif=0 group by k.jenis';
		$resultju = db_query($sql);	
		if ($resultju) 	{
			while ($dataju = db_fetch_object($resultju)) {
				if ($dataju->jenis==1) 
					{
						$btlju = $dataju->jumlahx;
						$btljup = $dataju->jumlahxp;
						$btljut = $dataju->jumlahxp-$dataju->jumlahx;
					}
				else
					{
						$blju = $dataju->jumlahx;
						$bljup = $dataju->jumlahxp;
						$bljut = $dataju->jumlahxp-$dataju->jumlahx;
					}
			}
		}		
		
		$t_pendapatanju += $pendapatanju;
		$t_pendapatanjup += $pendapatanjup;
		$t_pendapatanjut += $pendapatanjut;
		$t_btlju += $btlju;
		$t_btljup += $btljup;
		$t_blju += $blju;
		$t_bljup += $bljup;
		$tot_1 +=($btlju+$blju);
		$tot_2 +=($btljup+$bljup);
		$tot_3 +=(($btljup+$bljup)-($btlju+$blju));
		//Render
		if ($u==1)
			$ju = 'URUSAN WAJIB';
		else
			$ju = 'URUSAN PILIHAN';
		
		$rowsrek[] = array (
							 array('data' => $u,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => $ju,  'width' => '105px', 'style' => ' border-right: 1px solid black; text-align:left;font-size: 75%;'),
							 
							 array('data' => apbd_fn($pendapatanju),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($pendapatanjup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($pendapatanjut),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($pendapatanju, $pendapatanjup)),  'width' => '20px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 
							 array('data' => apbd_fn($btlju),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($blju),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($btlju+$blju),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 
							 
							 array('data' => apbd_fn($btljup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($bljup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' => apbd_fn($btljup+$bljup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),

							 array('data' => apbd_fn(($btljup+$bljup)-($btlju+$blju)),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
							 array('data' =>apbd_fn1(apbd_hitungpersen(($btlju+$blju),($btljup+$bljup))),  'width' => '20px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
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
				$sql = 'select sum(k.jumlah) jumlahx, sum(k.jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu;
				$res = db_query($sql);	
				if ($res) 	{
					if ($data = db_fetch_object($res)) {
						$pendapatanu = $data->jumlahx;
						$pendapatanup = $data->jumlahxp;
						$pendapatanut = $data->jumlahxp-$data->jumlahx;
					}
				}	
				
				//Belanja
				$sql = 'select k.jenis, sum(k.total) as jumlahx, sum(k.totalp) jumlahxp from {kegiatanperubahan' . $str_table . '} k inner join {unitkerja} u on
						k.kodeuk=u.kodeuk ' . $whereu . ' and k.inaktif=0 group by k.jenis';
				$res = db_query($sql);
				if ($res) 	{
					while ($data = db_fetch_object($res)) {
						if ($data->jenis==1) 
							{
							$btlu = $data->jumlahx;
							$btlup = $data->jumlahxp;
							}
						else
						{
							$blu = $data->jumlahx;
							$blup = $data->jumlahxp;
						}
							
					}
				}				
				
				//Render
				$rowsrek[] = array (
									 array('data' => $datau->kodeu,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size: 75%;'),
									 array('data' => $datau->urusan,  'width' => '105px', 'style' => ' border-right: 1px solid black; text-align:left;font-size: 75%;'),
									 
									 array('data' => apbd_fn($pendapatanu),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($pendapatanup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($pendapatanut),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn1(apbd_hitungpersen($pendapatanu, $pendapatanup)),  'width' => '20px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 
									 
									 array('data' => apbd_fn($btlu),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($blu),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($btlu+$blu),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),

									 array('data' => apbd_fn($btlup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($blup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' => apbd_fn($btlup+$blup),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 

									 array('data' => apbd_fn(($btlup+$blup)-($btlu+$blu)),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
									 array('data' =>apbd_fn1(apbd_hitungpersen(($btlu+$blu),($btlup+$blup))),  'width' => '20px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;font-size: 75%;'),
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
						$sql = 'select sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} ' . $whereuk;
						$res = db_query($sql);	
						if ($res) 	{
							if ($data = db_fetch_object($res)) {
								$pendapatanuk = $data->jumlahx;
								$pendapatanukp = $data->jumlahxp;
								$pendapatanukt = $data->jumlahxp-$data->jumlahx;
							}
						}	
						
						//Belanja
						$sql = 'select jenis, sum(total) as jumlahx,  sum(totalp) as jumlahxp from {kegiatanperubahan' . $str_table . '} ' . $whereuk . ' and inaktif=0 group by jenis';
						//drupal_set_message($sql);
						$res = db_query($sql);
						if ($res) 	{
							while ($data = db_fetch_object($res)) {
								if ($data->jenis==1)
								{
									$btluk = $data->jumlahx;
									$btlukp = $data->jumlahxp;
								}
									
								else
								{
									$bluk = $data->jumlahx;
									$blukp = $data->jumlahxp;
								}
							}
						}				
										
						//$newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
						
						$kode = substr_replace($datauk->kodedinas, '.', 3, 0);
						$rowsrek[] = array (
											 array('data' => $kode,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size: 75%;'),
											 array('data' => $datauk->namauk,  'width' => '105px', 'style' => ' border-right: 1px solid black; text-align:left;font-size: 75%;'),
											 
											 array('data' => apbd_fn($pendapatanuk),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($pendapatanukp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($pendapatanukt),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn1(apbd_hitungpersen($pendapatanuk, $pendapatanukp)),  'width' => '20px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 
											 array('data' => apbd_fn($btluk),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($bluk),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($btluk+$bluk),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),

											 array('data' => apbd_fn($btlukp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($blukp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' => apbd_fn($btlukp+$blukp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 
											 
											 array('data' => apbd_fn(($btlukp+$blukp)-($btluk+$bluk)),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 array('data' =>apbd_fn1(apbd_hitungpersen(($btluk+$bluk),($btlukp+$blukp))),  'width' => '20px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size: 75%;'),
											 );					

					}
				}
			}
		}	
		
	}	//looping u
	


	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '130px',  'colspan'=>'2',  'style' => 'border-left: 0.5px solid black; border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 1px solid black; text-align:right;font-size: 90%; '),
						 
						  array('data' => apbd_fn($t_pendapatanju),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right; font-weight:bold;font-size: 75%;'),
						 array('data' => apbd_fn($t_pendapatanjup),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						  array('data' => apbd_fn($t_pendapatanjut),  'width' => '60px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						  array('data' => apbd_fn1(apbd_hitungpersen($t_pendapatanju, $t_pendapatanjup)),  'width' => '20px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						  
						 array('data' => apbd_fn($t_btlju),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),						 
						array('data' => apbd_fn($t_blju),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						array('data' => apbd_fn($t_btlju+$t_blju),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						 
						 array('data' => apbd_fn($t_btljup),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right;font-weight:bold;font-size: 75%; '),
						 array('data' => apbd_fn($t_bljup),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right; font-weight:bold;font-size: 75%;'),
						 array('data' => apbd_fn($t_btljup+$t_bljup),  'width' => '65px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right; font-weight:bold;font-size: 75%;'),

						 
						 array('data' => apbd_fn(($t_btljup+$t_bljup)-($t_btlju+$t_blju)),  'width' => '60px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right; font-weight:bold;font-size: 75%;'),

						 array('data' => apbd_fn1(apbd_hitungpersen(($t_btlju+$t_blju),($t_btljup+$t_bljup))),  'width' => '20px', 'style' => 'border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-top: 0.5px solid black; text-align:right; font-weight:bold;font-size: 75%;'),
						 );
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();
	
	//$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	//$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);

	
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
	$revisi =arg(4);
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
function lampiran2_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/apbd/lampiran2/'.$revisi.'/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporan/apbd/lampiran2/' .$revisi.'/' .  $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}

function apbd_ExportPDF3_CFM_2($topmargin, $footermargin, $htmlContent1, $htmlContent2, $htmlContent3, $printlogo, $pdfFiel, $startpage) {
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