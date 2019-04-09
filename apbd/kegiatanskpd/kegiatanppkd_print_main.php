<?php
function kegiatanppkd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = arg(3);
	$topmargin = arg(4);
	$exportpdf = arg(5);
	$jenisdok = 'rka';

	if ($topmargin=='') $topmargin = arg(5);

	//drupal_set_message('Hai');
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-ppkd-kegiatan.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		//if ($jenisdok=='rka')
		//	$htmlContent = GenReportFormContentRKA();
		//else
		$htmlContent = GenReportFormContent($revisi);
		$htmlFooter = GenReportFormFooter($revisi);
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		//$url = 'apbd/kegiatanppkd/print/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('kegiatanppkd_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		//if ($jenisdok=='rka')
		//	$output .= GenReportFormContentRKA();
		//else
			$output .= GenReportFormContent($revisi);
		
		return $output;
	}

}

function GenReportFormHeader($print=0) {
	
	$tipedok = 'dpa';
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	
	if ($tipedok=='dpa') {
		$kodedinas = '12000';
		$urusan = '120 -  OTONOMI DAERAH, PEMERINTAHAN UMUM, ADMINISTRASI KEUANGAN DAERAH, PERANGKAT DAERAH, KEPEGAWAIAN DAN PERSANDIAN';
		$skpd = $kodedinas . ' - PEJABAT PENGELOLA KEUANGAN DAERAH';

		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		
		
		if ($data = db_fetch_object($pres)) {
			$pimpinannama = $data->setdanama;
			$pimpinannip = $data->setdanip;
			$pimpinanjabatan = $data->setdajabatan;
			$dpatgl = '.........................';
		}	

		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '270px', 'colspan'=>'7', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '270px', 'colspan'=>'7', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPPA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '270px', 'colspan'=>'7', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $urusan, 'width' => '710', 'colspan'=>'10',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );
		$rowskegiatan[]= array (
							 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $skpd,  'width' => '710', 'colspan'=>'10',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
							);		
	} else {
		$pquery = sprintf("select '12000' kodedinas, 'PEJABAT PENGELOLA KEUANGAN DAERAH' namauk, uk.pimpinannama, uk.pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
					from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string('81')) ;
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


	  
		/*
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 );
		*/
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RKA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );
		$rowskegiatan[]= array (
							 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
							);
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
} 

function GenReportFormContent($revisi) {

	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	//echo 'x';

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '50px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230px','rowspan'=>'2','colspan'=>'1', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '245px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '245px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH /BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Rupiah', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '%', 'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );

	
	$total = 0;
	$totalp = 0;
	
	//drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(k.jumlah) jumlahx, sum(k.jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $sql;
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datakel->uraian,  'width' => '230px','colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datakel->jumlahxp - $datakel->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$sql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			
			//drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => $datajenis->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => $dataobyek->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-size:small;'),

												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),

												 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),
												 array('data' => apbd_fn1(apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k where mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by kodero,uraian order by kodero';
							
							//drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
													 array('data' => $data->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;'),

													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => apbd_fn($data->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),

													 array('data' => apbd_fn($data->jumlahxp - $data->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 );
									if ($datajenis->kodej<='516') {
										 
										$penrekening = $data->jumlahx;
										
										//DETIL
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetilperubahan' . $str_table . '} where kodero=\'%s\' order by kodekeg,iddetil';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											
											//DETIL PENETAPAN
											$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where kodero=\'%s\' and iddetil=\'%s\'';
											$fsql = sprintf($sql, db_escape_string($data->kodero), db_escape_string($datasub->iddetil));
											//drupal_set_message($fsql);
											$resultsubpen = db_query($fsql);
											if ($datasubpen = db_fetch_object($resultsubpen)) {
												$unitpen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
												$volumpen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
												$hargapen = $datasubpen->harga;
												$totalpen = $datasubpen->total;
												
											} else {
												$unitpen = '';
												$volumpen = '';
												$hargapen = '';
												$totalpen = 0;
											}
											
											if (($datasub->total>0) or ($totalpen>0)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
														 array('data' => '- ' . $datasub->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $unitpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $volumpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($hargapen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($totalpen),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

														 array('data' => apbd_fn($datasub->total - $totalpen),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn1(apbd_hitungpersen($totalpen, $datasub->total)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 );
											}
										}
									
									
									} else {
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah,a.jumlahp from {kegiatanperubahan' . $str_table . '} k inner join {anggperkegperubahan' . $str_table . '} a on k.kodekeg=a.kodekeg where a.kodero=\'%s\' order by kegiatan';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										//drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												if ($datadetil->jumlah == 0) {
													$unitjumlah = '';
													$volumjumlah = '';
												} else {
													$unitjumlah = '1 kegiatan';
													$volumjumlah = '1 kali';
												}
												if ($datadetil->jumlahp == 0) {
													$unitjumlahp = '';
													$volumjumlahp = '';
												} else {
													$unitjumlahp = '1 kegiatan';
													$volumjumlahp = '1 kali';
												}
												
												if (($datadetil->jumlah>0) or ($datadetil->jumlahp>0)) {
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																	 array('data' => '- ' . $datadetil->kegiatan,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $unitjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlahp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datadetil->jumlahp - $datadetil->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn1(apbd_hitungpersen($datadetil->jumlah, $datadetil->jumlahp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );
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
						 array('data' => 'JUMLAH BELANJA',  'width'=> '280px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($totalp),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;'),

						 array('data' => apbd_fn($totalp - $total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total, $totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;'),
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContentRKA() {

	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '50px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230px','rowspan'=>'2','colspan'=>'1', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '245px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '245px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH /BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Rupiah', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '%', 'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );

	
	$total = 0;
	$totalp = 0;
	
	//drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(k.jumlah) jumlahxp from {anggperkegrevisi} k  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek  inner join {kegiatanrevisi} kr on k.kodekeg=kr.kodekeg ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $sql;
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			
			$jumlah_penetapan = 0;
			$sql = "select sum(a.jumlah) jumlahx from {anggperkeg} a inner join {kegiatanskpd} k
					on a.kodekeg=k.kodekeg where k.inaktif=0 and left(a.kodero,3)>'511' and left(a.kodero,2)='" . $datakel->kodek . "'";
			//drupal_set_message($sql);
			$resultkel_pen = db_query($sql);
			if ($resultkel_pen) {
				if ($datakel_pen = db_fetch_object($resultkel_pen)) {
					$jumlah_penetapan = $datakel_pen->jumlahx;
				}
			}
			
			
			$total += $jumlah_penetapan;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datakel->uraian,  'width' => '230px','colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($jumlah_penetapan),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datakel->jumlahxp - $jumlah_penetapan),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($jumlah_penetapan, $datakel->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahxp from {anggperkegrevisi} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanrevisi} kr on k.kodekeg=kr.kodekeg ' . $where;
			$sql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			
			//drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$jumlah_penetapan = 0;
					$sql = "select sum(jumlah) jumlahx from {anggperkeg} where left(kodero,3)='" . $datajenis->kodej . "'";
					$resultjenis_pen = db_query($sql);
					if ($resultjenis_pen) {
						if ($datajen_pen = db_fetch_object($resultjenis_pen)) {
							$jumlah_penetapan = $datajen_pen->jumlahx;
						}
					}
				
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => $datajenis->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($jumlah_penetapan),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($datajenis->jumlahxp - $jumlah_penetapan),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($jumlah_penetapan, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahxp from {anggperkegrevisi} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo inner join {kegiatanrevisi} kr on k.kodekeg=kr.kodekeg where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {

							$jumlah_penetapan = 0;
							$sql = "select sum(jumlah) jumlahx from {anggperkeg} where left(kodero,5)='" . $dataobyek->kodeo . "'";
							$resultobyek_pen = db_query($sql);
							if ($resultobyek_pen) {
								if ($dataobyek_pen = db_fetch_object($resultobyek_pen)) {
									$jumlah_penetapan = $dataobyek_pen->jumlahx;
								}
							}
						
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => $dataobyek->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($jumlah_penetapan),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-size:small;'),

												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),

												 array('data' => apbd_fn($dataobyek->jumlahxp - $jumlah_penetapan),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),
												 array('data' => apbd_fn1(apbd_hitungpersen($jumlah_penetapan, $dataobyek->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;  text-align:right;font-size:small;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {kegiatanrevisi} kr on k.kodekeg=kr.kodekeg where mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by kodero,uraian order by kodero';
							
							//drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {

									$jumlah_penetapan = 0;
									$sql = "select sum(jumlah) jumlahx from {anggperkeg} where kodero='" . $data->kodero . "'";
									$resultrek_pen = db_query($sql);
									if ($resultrek_pen) {
										if ($data_pen = db_fetch_object($resultrek_pen)) {
											$jumlah_penetapan = $data_pen->jumlahx;
										}
									}	
									
									$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
													 array('data' => $data->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => apbd_fn($jumlah_penetapan),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;'),

													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
													 array('data' => apbd_fn($data->jumlahxp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),

													 array('data' => apbd_fn($data->jumlahxp - $jumlah_penetapan),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 array('data' => apbd_fn1(apbd_hitungpersen($jumlah_penetapan, $data->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 );
									if ($datajenis->kodej<'516') {
										
										$penrekening = $data->jumlahx;
										
										//DETIL
										$sql = 'select d.kodekeg,d.iddetil,d.uraian,d.unitjumlah,d.unitsatuan,d.volumjumlah, d.volumsatuan,d.harga,d.total from {anggperkegdetilrevisi} d 
												inner join {kegiatanrevisi} kr on d.kodekeg=kr.kodekeg where kodero=\'%s\' order by d.kodekeg,d.iddetil';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										//drupal_set_message($fsql);
										
										//if ($data->kodero=='51406001') drupal_set_message($fsql);
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											
											//DETIL PENETAPAN
											$sql = 'select d.iddetil,d.uraian,d.unitjumlah,d.unitsatuan,d.volumjumlah, d.volumsatuan,d.harga,d.total from {anggperkegdetil} d inner join {kegiatanskpd} kr on d.kodekeg=kr.kodekeg where d.kodero=\'%s\' and d.iddetil=\'%s\' and d.kodekeg=\'%s\'';
											$fsql = sprintf($sql, db_escape_string($data->kodero), db_escape_string($datasub->iddetil), db_escape_string($datasub->kodekeg));
											//drupal_set_message($fsql);
											$resultsubpen = db_query($fsql);
											if ($datasubpen = db_fetch_object($resultsubpen)) {
												$unitpen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
												$volumpen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
												$hargapen = $datasubpen->harga;
												$totalpen = $datasubpen->total;
												
												//$uraianp = $datasubpen->uraian;
												
											} else {
												$unitpen = '';
												$volumpen = '';
												$hargapen = '';
												$totalpen = 0;
											}
											
											if (($datasub->total>0) or ($totalpen>0)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
														 array('data' => '- ' . $datasub->uraian ,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $unitpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $volumpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($hargapen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($totalpen),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

														 array('data' => apbd_fn($datasub->total - $totalpen),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn1(apbd_hitungpersen($totalpen, $datasub->total)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
														 );
											}
										}
									
								
									} else {
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah jumlahp from {kegiatanrevisi} k inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg where a.kodero=\'%s\' order by k.kegiatan';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										//drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												

												$sql = 'select jumlah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datadetil->kodekeg),db_escape_string($data->kodero));
												$resultdetil_pen = db_query($fsql);
												if ($datadetil_pen = db_fetch_object($resultdetil_pen)) {
													$unitjumlah = '1 kegiatan';
													$volumjumlah = '1 kali';
													$totaldetil_pen = $datadetil_pen->jumlah;
												} else {
													$unitjumlah = '';
													$volumjumlah = '';
													$totaldetil_pen = 0;
												}
										
												if ($datadetil->jumlahp == 0) {
													$unitjumlahp = '';
													$volumjumlahp = '';
												} else {
													$unitjumlahp = '1 kegiatan';
													$volumjumlahp = '1 kali';
												}
												
												if (($datadetil->jumlah>0) or ($datadetil->jumlahp>0)) {
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																	 array('data' => '- ' . $datadetil->kegiatan,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($totaldetil_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($totaldetil_pen),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $unitjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlahp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlahp),  'width' => '65px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datadetil->jumlahp - $totaldetil_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn1(apbd_hitungpersen($totaldetil_pen, $datadetil->jumlahp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );
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
						 array('data' => 'JUMLAH BELANJA',  'width'=> '280px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total),  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($totalp),  'width' => '65px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => apbd_fn($totalp - $total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total, $totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormFooter($revisi) {

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
	$tipedok = 'dpa';
	
	if ($tipedok='dpa') {
		//$pquery = 'select sum(tw1p) tw1t,sum(tw2p) tw2t,sum(tw3p) tw3t,sum(tw4p) tw4t from {kegiatanperubahan} where isppkd=1';
		
		$pquery = 'select sum(tw1p) tw1t,sum(tw2p) tw2t,sum(tw3p) tw3t,sum(tw4p) tw4t, sum(totalp) total from {kegiatanperubahan} where jenis=1 and inaktif=0 and isppkd=1';
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
			
			if ($tw1+$tw2+$tw3+$tw4 != $data->total) {
				$tw4 = $data->total - ($tw1+$tw2+$tw3);
			}
		}

		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan='PEJABAT PENGELOLA KEUANGAN DAERAH';
		}
		$namauk = 'PEJABAT PENGELOLA KEUANGAN DAERAH';
		$dpatgl = '....Januari 2016';

		$pquery = sprintf("select dpatgl".$revisi." dpatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$dpatgl = $data->dpatgl;
		}	
		
		$rowsfooter[] = array (
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'10',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '275px',  'colspan'=>'8',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
							 );

	 } else {
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'BENDAHARA UMUM DAERAH',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
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

function kegiatanppkd_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$revisi = arg(3);
	$topmargin = arg(4);
	if (!isset($topmargin)) $topmargin=10;

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
	$form['formdata']['revisi'] = array (
		'#type' => 'value',
		'#value' => $revisi
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function kegiatanppkd_print_form_submit($form, &$form_state) {
	$topmargin = $form_state['values']['topmargin'];
	$revisi = $form_state['values']['revisi'];
	$uri = 'apbd/kegiatanppkd/print/'.$revisi.'/' . $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>