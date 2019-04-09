<?php
function pendapatan_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(3);
	$revisi = arg(4);
	$tipedok = arg(5);
	$topmargin = arg(6);
	$exportpdf = arg(7);
	$sampul = arg(8);

	if ($topmargin=='') $topmargin = 10;
	if ($revisi=='') $revisi = '9';


	//drupal_set_message($tipedok);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		if (isset($sampul))  {
			$pdfFile = 'sampul-dppa-pendapatan-' . $kodeuk . '.pdf';
			$htmlContent = GenReportFormSampulPendapatan($kodeuk, $revisi);
			apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
		
		} else {
			$pdfFile = 'rka-ppkd-pendapatan.pdf';

			//$htmlContent = GenReportForm(1);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

			$htmlHeader = GenReportFormHeader($kodeuk, $tipedok);
			$htmlContent = GenReportFormContent($kodeuk, $tipedok);
			$htmlFooter = GenReportFormFooter($kodeuk, $tipedok, $revisi);
			
			apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		}
		
	} else {
		//$url = 'apbd/pendapatanppkd/print/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('pendapatan_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($kodeuk, $tipedok, $revisi);
		return $output;
	}

}

function GenReportFormHeader($kodeuk, $tipedok) {
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();

	$pquery = sprintf("select uk.kodedinas, uk.namauk, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
	}		
	
	if ($tipedok=='dpa') {

		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width' => '360px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN', 'width' => '250px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '360px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'P E N D A P A T A N', 'width' => '250px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPPA-SKPD 1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );			
	
	} else {

		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA PERUBAHAN KERJA DAN ANGGARAN', 'width' => '360px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN', 'width' => '250px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '360px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'P E N D A P A T A N', 'width' => '250px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RKA-SKPD 1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );		
	}

	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '360px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => '', 'width' => '250', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'12',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'12',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $tipedok) {
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	$total=0;
	$totalp=0;
	/*
	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	*/
	$headersrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => '',  'width' => '230px','colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '240px','colspan'=>'4','style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '240px','colspan'=>'4','style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH ',  'width' => '105px','colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230px','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '240px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '240px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '/BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Rupiah', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '%', 'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );

	
	$total = 0;
	$totalp = 0;
	
	//KELOMPOK
	$where = ' where k.kodeuk=\'%s\' and left(k.kodero,2)<\'%s\'';
	$sql = 'select l.kodek,l.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperukperubahan} k  left join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$fsql = sprintf($sql, $kodeuk, '42');
	$fsql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//drupal_set_message($fsql);
	
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datakel->uraian,  'width' => '230px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datakel->jumlahxp - $datakel->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = ' where k.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\'';
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$fsql = sprintf($sql, $kodeuk, db_escape_string($datakel->kodek), '41');
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			//$bold .= 'font-weight:bold;'
			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => $datajenis->uraian,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where k.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, $kodeuk, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => $dataobyek->uraian,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

												 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
												 array('data' => apbd_fn1(apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,jumlah as jumlahx ,jumlahp as jumlahxp from {anggperukperubahan} k where (jumlah+jumlahp)>0 and k.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, $kodeuk, db_escape_string($dataobyek->kodeo));
							
							//drupal_set_message( $fsql);
							$fsql .= ' order by k.kodero';
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
														 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
														 array('data' => $data->uraian,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => apbd_fn($data->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;'),

														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => apbd_fn($data->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),

														 array('data' => apbd_fn($data->jumlahxp - $data->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
														 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 );
									//DETIL
									//PERUBAHAN
									$sql = 'select * from {anggperukdetilperubahan'.$str_table.'} where kodeuk=\'%s\' and kodero=\'%s\' order by iddetil';
									$fsql = sprintf($sql, $kodeuk, db_escape_string($data->kodero));
									//drupal_set_message($fsql);
									
									$resultdetil = db_query($fsql);
									if ($resultdetil) {
										while ($datadetil = db_fetch_object($resultdetil)) {
											if ($datadetil->pengelompokan) {
												$unitjumlah = '';
												$volumjumlah = '';
												$hargasatuan = '';

												$unitjumlahp = '';
												$volumjumlahp = '';
												$hargasatuanp = '';
												
											} else {
												$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
												$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
												$hargasatuan = apbd_fn($datadetil->harga);

												$unitjumlahp = $datadetil->unitjumlahp . ' ' . $datadetil->unitsatuanp;
												$volumjumlahp = $datadetil->volumjumlahp . ' ' . $datadetil->volumsatuanp;
												$hargasatuanp = apbd_fn($datadetil->hargap);
												
											}
											
											//ddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => '- ' . $datadetil->uraianp,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $unitjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $volumjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->totalp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datadetil->totalp-$datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(apbd_hitungpersen($datadetil->total, $datadetil->totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );
																 

											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select * from {anggperukdetilsubperubahan} where iddetil=\'%s\' order by idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												
												//drupal_set_message($fsql);
												
												$resultsub = db_query($fsql);
												while ($datasub = db_fetch_object($resultsub)) {
													
													//sub penetapan
													$sql = 'select * from {anggperukdetilsub} where idsub=\'%s\'';
													$fsql = sprintf($sql, db_escape_string($datasub->idsub));
													$resultsub_p = db_query($fsql);
													if ($datasub_p = db_fetch_object($resultsub_p)) {
														if ($datasub->uraian == $datasub_p->uraian) {
															//ADA DI PENETAPAN, SAMA
															$rowsrek[] = array (
																		 
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => '.',  'width'=> '15px', 'style' => 'border: none; text-align:center;'),
																		 array('data' => $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub_p->unitjumlah . ' ' . $datasub_p->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub_p->volumjumlah . ' ' . $datasub_p->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub_p->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub_p->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total - $datasub_p->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn1(apbd_hitungpersen($datasub->total, $datasub_p->total)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 );
															
														} else {

															//ADA DI PENETAPAN, TIDAK SAMA
															//DOWN FIRST
															$rowsrek[] = array (
																		 
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => '.',  'width'=> '15px', 'style' => 'border: none; text-align:center;'),
																		 array('data' => $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub_p->unitjumlah . ' ' . $datasub_p->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub_p->volumjumlah . ' ' . $datasub_p->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub_p->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub_p->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn(-$datasub_p->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 );
															//up
															$rowsrek[] = array (
																		 
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => '.',  'width'=> '15px', 'style' => 'border: none; text-align:center;'),
																		 array('data' => $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 );															
															
														}
														
													} else {
														//TIDAK ADA PADA PENETAPAN
														$rowsrek[] = array (
																	 
																	 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => '.',  'width'=> '15px', 'style' => 'border: none; text-align:center;'),
																	 array('data' => $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );														
													}
													

												}
												
											}														 
																 
										}
									}
								}
							}
												 
						////////
						}
					}
				}
			}	
		}	//KELOMPOK LOOPING
	}	//KELOMPOK

	$rowsrek[] = array (
						 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '290px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total),  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($totalp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => apbd_fn($totalp-$total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total,$totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($kodeuk, $tipedok, $revisi) {

	if ($revisi=='9')
	{
		$revisi = variable_get('apbdrevisi', 0);
		$str_table='';
	}
	else
	{
		$revisi = $revisi;
		$str_table = $revisi;
	}
	
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	//drupal_set_message('Y '. $tipedok);
	
	if ($tipedok=='dpa') {

		$pquery = "select sum(k.jumlah) total from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea where k.kodeuk='" . $kodeuk . "' and left(k.kodero,2)<'42'";

		//$pquery = "select sum(jumlahp/1000) total from {anggperukperubahan} where left(kodero,2)>'41'";
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			//$total = $data->total;
			$tw1 = round(($data->total/1000)/4,0)*1000;
		}

		$pquery = "select sum(k.jumlahp) total from {anggperukperubahan" . $str_table . "} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea where k.kodeuk='" . $kodeuk . "' and left(k.kodero,2)<'42'";

		//$pquery = "select sum(jumlahp/1000) total from {anggperukperubahan} where left(kodero,2)>'41'";
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$total = $data->total;
			//$tw1 = round($data->total/4,0)*1000;
		}
		
		/*
		$tw1 = 433249290000;
		$tw2 = 433249290000;
		$tw3 = 433249290000;
		$tw4 = 433249291000;
		*/
		$tw2 = $tw1;
		$tw3 = $tw1;
		$tw4 = $total - $tw1 - $tw2 - $tw3;		
		
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan='PEJABAT PENGELOLA KEUANGAN DAERAH';
		}

		$namauk = 'PEJABAT PENGELOLA KEUANGAN DAERAH';

		$pquery = sprintf("select dpatgl".$revisi." dpatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$dpatgl = $data->dpatgl;
		}	
		
		
		$rowsfooter[] = array (
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '410px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '120px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '120px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '120px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '120px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '120px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '190px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '120px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '165px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
							 );
							 
	
		
	} else {
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
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
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
	}
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}


function pendapatan_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);

	$kodeuk = arg(3);
	$revisi = arg(4);
	$tipedok = arg(5);
	$topmargin = arg(6);
	$exportpdf = arg(7);

	if ($topmargin=='') $topmargin = 10;
	if ($revisi=='') $revisi = 9;
	
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
	$form['formdata']['kodeuk']= array(
		'#type'         => 'value', 
		'#value'=> $kodeuk, 
	);
	$form['formdata']['tipedok']= array(
		'#type'         => 'value', 
		'#value'=> $tipedok, 
	);
	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#value'=> $revisi, 
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}

function pendapatan_print_form_submit($form, &$form_state) {

	$topmargin = $form_state['values']['topmargin'];
	$kodeuk = $form_state['values']['kodeuk'];
	$revisi = $form_state['values']['revisi'];
	$tipedok = $form_state['values']['tipedok'];

	$uri = 'apbd/pendapatan/print/' . $kodeuk . '/' . $revisi . '/' . $tipedok . '/' . $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}

function GenReportFormSampulPendapatan($kodeuk, $revisi) {
	//$revisi='2';
	if ($revisi=='9')
	{
		$revisi = variable_get('apbdrevisi', 0);
		$str_table='';
	}
	else
	{
		$revisi = $revisi;
		$str_table = $revisi;
	}
	
	$where = ' where uk.kodeuk=\'%s\'';
	$pquery = sprintf('select u.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, 
				uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip 
				from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu ' . $where, db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$kodedinas = $data->kodedinas;
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
		$total = $data->total;
		$totalp = $data->totalp;

		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
		
	}
	
	$pquery = sprintf('select penno dpano from {dpanomor'.$revisi.'} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	if ($dpano !='') {
		$tahun = variable_get('apbdtahun', 0);
		//$tahun=2016;
		$pquery = sprintf('select dpapenformat'.$revisi.' dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
			
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($pres) {
			if ($data = db_fetch_object($pres)) {
				$dpaformat = $data->dpaformat;
			}
		}
		
		$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
		$dpanolengkap = str_replace('KODEDINAS',$kodedinas,$dpanolengkap);
		$dpanolengkap = str_replace('NOKEG',$kodedinas,$dpanolengkap);
		
	} else 
		$dpanolengkap = '........................';
	
	//Jumlah
	$pquery = "select sum(k.jumlah) total, sum(k.jumlahp) totalp from {anggperukperubahan" . $str_table . "} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea where k.kodeuk='" . $kodeuk . "' and left(k.kodero,2)<'42'";

	//$pquery = "select sum(jumlahp/1000) total from {anggperukperubahan} where left(kodero,2)>'41'";
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$total = $data->total;
		$totalp = $data->totalp;
	}
		
	$rows[] = array (array ('data'=>'', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'KABUPATEN JEPARA', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[] = array (array ('data'=>'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPPA-SKPD)', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));
 
	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'P  E  N  D  A  P  A  T  A  N', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'NO. DPPA-SKPD : ' . $dpanolengkap, 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array ( 
				array('data' => '',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'URUSAN PEMERINTAHAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $urusan, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'ORGANISASI',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $organisasi, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$rows[]= array (
				array('data' => 'JUMLAH ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ' . apbd_fn($totalp) . ',00', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'TERBILANG',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($totalp)), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);			
	$rows[]= array (
				array('data' => 'PENGGUNA ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NAMA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannama, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NIP',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannip, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- JABATAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinanjabatan, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}


?>