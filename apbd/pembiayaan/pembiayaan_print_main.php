<?php
function pembiayaan_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	//$topmargin = '20';
	$revisi = arg(3);
	$kodek = arg(4);
	$jenisdok = arg(5);
	$topmargin = arg(6);
	if (!isset($topmargin)) $topmargin=10;
	if (!isset($jenisdok)) $jenisdok='rka';
	
	$exportpdf = arg(7);

	//drupal_set_message($revisi);
	//drupal_set_message($kodek);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-pembiayaan-' . $kodek . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodek, $jenisdok);
		$htmlContent = GenReportFormContent($kodek, $revisi);
		$htmlFooter = GenReportFormFooter($kodek, $jenisdok, $revisi);
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$url = 'apbd/pembiayaan/print/'. $kodek . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('pembiayaan_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($kodek, $revisi);
		return $output;
	}

}

function GenReportFormHeader($kodek, $jenisdok) {
	

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='BENDAHARA UMUM DAERAH';
	$kodeuk = '81';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = '12000 - PEJABAT PENGELOLA KEUANGAN DAERAH';
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		//$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	
	$rows= array();
	if ($jenisdok=='dpa')
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width' => '350px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN', 'width' => '260px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
	else
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA PERUBAHAN KERJA DAN ANGGARAN', 'width' => '350px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN', 'width' => '260px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		
	if ($kodek=='61') {
		$strjenis = 'PENERIMAAN ';
		$strkode = '.1';
	} elseif ($kodek=='62') {
		$strjenis = 'PENGELUARAN ';
		$strkode = '.2';
	}
	if ($jenisdok=='dpa')
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '350px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => $strjenis . 'PEMBIAYAAN', 'width' => '260px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPPA-PPKD 3' . $strkode, 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
	else
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '350px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => $strjenis . 'PEMBIAYAAN', 'width' => '260px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RPKA-PPKD 3' . $strkode, 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '350px', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => '', 'width' => '260px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),	
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodek, $revisi) {
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;	
	
	//drupal_set_message($revisi . '|' . $str_table);
	
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '50px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '220px','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '250px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '250px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH /BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '55px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '55px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '65px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Rupiah', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '%', 'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );

	//JENIS
	//PERUBAHAN	
	$total_penerimaan =0;
	$total_penerimaanp =0;

	$total_pengeluaran =0;
	$total_pengeluaranp =0;

	$penerimaan_sudah = false;
	
	if ($kodek !='') {
		$where = sprintf(' where j.kodek=\'%s\'', $kodek);
		$tag = $kodek;
		
	} else 
		$tag = '61';
	
	if ($kodek=='62') $penerimaan_sudah= true;
	
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperdaperubahan' . $str_table . '} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = $sql;
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			
			if (substr($datajenis->kodej,0,2)=='61') {
				$total_penerimaan += $datajenis->jumlahx;
				$total_penerimaanp += $datajenis->jumlahxp;
			} else {
				$total_pengeluaran += $datajenis->jumlahx;
				$total_pengeluaranp += $datajenis->jumlahxp;
			}
				
			if ($tag != substr($datajenis->kodej,0,2)) {
				$penerimaan_sudah = true;
				$rowsrek[] = array (
									 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;font-size:small;'),
									 array('data' => 'JUMLAH PENERIMAAN PEMBIAYAAN',  'width' => '220px', 'colspan'=>'2', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;font-size:small;'),
									 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => apbd_fn($total_penerimaan),  'width' => '70px', 'style' => ' border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

									 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => apbd_fn($total_penerimaanp),  'width' => '70px', 'style' => ' border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

									 array('data' => apbd_fn($total_penerimaanp - $total_penerimaan),  'width' => '70px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
									 array('data' => apbd_fn1(apbd_hitungpersen($total_penerimaan, $total_penerimaanp)),  'width' => '35px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
									 );				
			}
			
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datajenis->uraian,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
								 );
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperdaperubahan' . $str_table . '} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			//drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
									 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
									 array('data' => $dataobyek->uraian,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
									 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

									 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
									 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

									 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
									 array('data' => apbd_fn1(apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
									 );	

					//REKENING
					$sql = 'select kodero,uraian,jumlah,jumlahp from {anggperdaperubahan' . $str_table . '} k where mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
					
					//drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$rowsrek[] = array (
										 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => $data->uraian,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($data->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($data->jumlahp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),

										 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlah, $data->jumlahp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
									 );
						
							//DETIL
							$sql = 'select * from {anggperdadetilperubahan' . $str_table . '} where kodero=\'%s\' order by iddetil';
							$fsql = sprintf($sql, db_escape_string($data->kodero));
							//drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									if ($datadetil->uraian == $datadetil->uraianp) {
										$uraian_detil = $datadetil->uraianp;
									} else if ($datadetil->uraian == '') {
										$uraian_detil = $datadetil->uraianp;
									} else {
										$uraian_detil = $datadetil->uraianp . ' [' . $datadetil->uraian . ']';
									}
									
									//$uraian_detil = $datadetil->uraian;
									
									$rowsrek[] = array (
													 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => '- ' . $datadetil->uraianp,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan, 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => apbd_fn($datadetil->harga),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => apbd_fn($datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

													 array('data' => $datadetil->unitjumlahp . ' ' . $datadetil->unitsatuanp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => $datadetil->volumjumlahp . ' ' . $datadetil->volumsatuanp, 'width' => '55px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => apbd_fn($datadetil->hargap),  'width' => '65px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => apbd_fn($datadetil->totalp),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

													 array('data' => apbd_fn($datadetil->totalp-$datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
													 array('data' => apbd_fn1(apbd_hitungpersen($datadetil->total, $datadetil->totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
													 );
									
								} //while resultdetil
							}	// if resultdetil
							
						}	//result
					}	//if result
										 
				////////
				}	//while resultobyek
			}	//if resultobyek
		}
	}

	if ($penerimaan_sudah) {
		if ($kodek != '') $str_bottom = 'border-bottom: 1px;';
		$str_bottom = 'border-bottom: 1px solid black;';
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; border-right: 1px solid black; text-align:left;font-size:small;' . $str_bottom),
							 array('data' => 'JUMLAH PENGELUARAN PEMBIAYAAN',  'width' => '220px', 'colspan'=>'2', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;' . $str_bottom),
							 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => apbd_fn($total_pengeluaran),  'width' => '70px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;' . $str_bottom),

							 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;' . $str_bottom),
							 array('data' => apbd_fn($total_pengeluaranp),  'width' => '70px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;' . $str_bottom),

							 array('data' => apbd_fn($total_pengeluaranp - $total_pengeluaran),  'width' => '70px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;' . $str_bottom),
							 array('data' => apbd_fn1(apbd_hitungpersen($total_pengeluaran, $total_pengeluaranp)),  'width' => '35px', 'style' => 'border-top: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;' . $str_bottom),
						 );		
	} else {
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => 'JUMLAH PENERIMAAN PEMBIAYAAN',  'width' => '220px', 'colspan'=>'2', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($total_penerimaan),  'width' => '70px', 'style' => ' border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => '', 'width' => '60px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '55px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '65px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($total_penerimaanp),  'width' => '70px', 'style' => ' border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => apbd_fn($total_penerimaanp - $total_penerimaan),  'width' => '70px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($total_penerimaan, $total_penerimaanp)),  'width' => '35px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black;  border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
							 );				
	
	}
	
	if ($kodek == '') {	
		$rowsrek[] = array (
						 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '270px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => '  border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '55px', 'style' => ' border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total_penerimaan - $total_pengeluaran),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => '', 'width' => '60px', 'style' => 'border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '55px', 'style' => ' border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '65px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total_penerimaanp - $total_pengeluaranp),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => apbd_fn(($total_penerimaanp - $total_pengeluaranp)-($total_penerimaan - $total_pengeluaran)),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen(($total_penerimaan - $total_pengeluaran),($total_penerimaanp - $total_pengeluaranp))),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 );
	}
		
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($kodek, $jenisdok, $revisi) {

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
	
	if ($jenisdok=='dpa') {

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
		
		if ($kodek=='') {
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => 'Jepara, ' . $dpatgl,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
								 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
								 );			
		} else {
			$pquery = "select sum(jumlahp) total from {anggperdaperubahan" . $str_table . "} where left(kodero,2)='" . $kodek . "'";
			$pres = db_query($pquery);
			if ($data = db_fetch_object($pres)) {
				$tw1 = round(($data->total/1000) / 4,0) * 1000;
				$tw2 = $tw1;
				$tw3 = $tw1;
				$tw4 = $data->total - (3*$tw1);
			}
			
			$rowsfooter[] = array (
								 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
								 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
								 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => 'Mengesahkan,',  'width' => '300px','colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
								 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => $pimpinanjabatan,  'width' => '300px', 'colspan'=>'7', 'style' => 'border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
								 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => '',  'width' => '300px','colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
								 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => '',  'width' => '300px','colspan'=>'7',  'style' => 'border-right: 1px solid black; text-align:center;'),
								 );
			$rowsfooter[] = array (
								 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
								 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
								 array('data' => $pimpinannama,  'width' => '300px', 'colspan'=>'7', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
								 );
			$rowsfooter[] = array (
								 array('data' => '',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
								 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

								 array('data' => '',  'width'=> '100px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
								 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'colspan'=>'7', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
								 );
		}
		
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='BENDAHARA UMUM DAERAH';
		$kodeuk = '81';
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			//$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
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

function pembiayaan_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);

	$revisi = arg(3);
	$kodek = arg(4);
	$jenisdok = arg(5);
	$topmargin = arg(6);
	
	$exportpdf = arg(7);
	if (!isset($topmargin)) $topmargin=10;
	if (!isset($jenisdok)) $jenisdok='rka';

	$form['formdata']['kodek']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodek, 
	);
	$form['formdata']['revisi']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $revisi, 
	);
	$form['formdata']['jenisdok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $jenisdok, 
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
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function pembiayaan_print_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	$kodek = $form_state['values']['kodek'];
	$topmargin = $form_state['values']['topmargin'];
	//$jenisdok = $form_state['values']['jenisdok'];
	
	$jenisdok = 'dpa';
	$uri = 'apbd/pembiayaan/print/'.$revisi.'/' . $kodek . '/' . $jenisdok . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>