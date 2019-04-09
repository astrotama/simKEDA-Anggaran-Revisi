<?php
function pendapatanppkd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$revisi = arg(3);
	$topmargin = arg(4);
	$exportpdf = arg(5);

	if ($topmargin=='') $topmargin = arg(5);

	//drupal_set_message($topmargin);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-ppkd-pendapatan.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($revisi);
		$htmlFooter = GenReportFormFooter($revisi);
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		//$url = 'apbd/pendapatanppkd/print/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('pendapatanppkd_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($revisi);
		return $output;
	}

}
function GenReportForm($print=0,$revisi) {
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
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

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN PEJABAT PENGELOLA KEUANGAN DAERAH ', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

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

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230px','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '240px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '240px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH /BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
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
	$where = ' where left(k.kodero,2)>\'%s\'';
	$sql = 'select l.kodek,l.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k  left join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$fsql = sprintf($sql, '41');
	$fsql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $fsql;
	
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
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
			$where = ' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)>\'%s\'';
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$fsql = sprintf($sql, db_escape_string($datakel->kodek), '41');
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			//$bold .= 'font-weight:bold;'
			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
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
							$sql = 'select kodero,uraian,jumlah as jumlahx ,jumlahp as jumlahxp from {anggperukperubahan' . $str_table . '} k where mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							
							//drupal_set_message( $fsql);
							$fsql .= ' order by k.kodero';
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
														 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
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
									$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan from {anggperukdetilperubahan'.$str_table.'} where kodero=\'%s\' order by iddetil';
									$fsql = sprintf($sql, db_escape_string($data->kodero));
									//drupal_set_message($fsql);
									
									$resdetil = db_query($fsql);
									if ($resdetil) {
											$ind=0;
											$datavolume=array();
											while ($datdetil = db_fetch_object($resdetil)) {
													if ($datdetil->pengelompokan) {
														$unitjumlahp = '';
														$volumjumlahp = '';
														$hargasatuanp = '';
														
													} else {
														$unitjumlahp = $datdetil->unitjumlah . ' ' . $datdetil->unitsatuan;
														$volumjumlahp = $datdetil->volumjumlah . ' ' . $datdetil->volumsatuan;
														$hargasatuanp = apbd_fn($datdetil->harga);
													}
													$datavolume[$ind]=array($unitjumlahp,$volumjumlahp,$hargasatuanp,$datdetil->total);$ind++;
												}
											}
									//.................................................................
									$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan from {anggperukdetil} where kodero=\'%s\' order by iddetil';
									$fsql = sprintf($sql, db_escape_string($data->kodero));
									//drupal_set_message($fsql);
									
									$resultdetil = db_query($fsql);
									if ($resultdetil) {$in=0;
										while ($datadetil = db_fetch_object($resultdetil)) {
											if ($datadetil->pengelompokan) {
												$unitjumlah = '';
												$volumjumlah = '';
												$hargasatuan = '';
												
											} else {
												$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
												$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
												$hargasatuan = apbd_fn($datadetil->harga);
											}
											
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => '- ' . $datadetil->uraian,  'width' => '230px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datavolume[$in][0], 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datavolume[$in][1], 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datavolume[$in][2],  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datavolume[$in][3]),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datadetil->total-$datavolume[$in][3]),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(apbd_hitungpersen($datadetil->total, $datavolume[$in][3])),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );$in++;

											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperukdetilsub} where iddetil=\'%s\' order by idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												//drupal_set_message($fsql);
												
												$resultsub = db_query($fsql);
												while ($datasub = db_fetch_object($resultsub)) {
													$rowsrek[] = array (
																 
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => '- ' . $datasub->uraian,  'width' => '230px', 'colspan'=>'1', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => 'pengele', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '',  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
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
						 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '290px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total),  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($totalp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;'),

						 array('data' => apbd_fn($totalp-$total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total,$totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;'),
						 );
	
	$rowsfooter[] = array (
						 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
						 array('data' => 'BENDAHARA UMUM DAERAH',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
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
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '300px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'P E N D A P A T A N', 'width' => '300px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPA-PPKD 1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
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

		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'P E N D A P A T A N', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RKA-PPKD 1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );		
	}

	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	/*
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 );
	*/

	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => '', 'width' => '300px', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
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

function GenReportFormContent($revisi) {
	
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
						 array('data' => 'KODE',  'width'=> '50px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '220px','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SEBELUM PERUBAHAN', 'width' => '250px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'SETELAH PERUBAHAN', 'width' => '250px','colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 array('data' => 'BERTAMBAH /BERKURANG',  'width' => '105px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '@Harga',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => 'Jumlah',  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),

						 array('data' => 'Rupiah', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '%', 'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;font-size:small;'),
						 );

	
	$total = 0;
	$totalp = 0;
	
	//KELOMPOK
	$where = ' where left(k.kodero,2)>\'%s\'';
	$sql = 'select l.kodek,l.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperukperubahan'.$str_table.'} k  left join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$fsql = sprintf($sql, '41');
	$fsql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $fsql;
	
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datakel->uraian,  'width' => '220px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datakel->jumlahxp - $datakel->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = ' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)>\'%s\'';
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$fsql = sprintf($sql, db_escape_string($datakel->kodek), '41');
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			//$bold .= 'font-weight:bold;'
			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => $datajenis->uraian,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperukperubahan} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
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
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
												 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

												 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
												 array('data' => apbd_fn1(apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,jumlah as jumlahx ,jumlahp as jumlahxp from {anggperukperubahan} k where mid(k.kodero,1,5)=\'%s\'';
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
														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => apbd_fn($data->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;'),

														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
														 array('data' => apbd_fn($data->jumlahxp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),

														 array('data' => apbd_fn($data->jumlahxp - $data->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
														 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;'),
													 );
									//DETIL
									//PERUBAHAN
									$sql = 'select * from {anggperukdetilperubahan'.$str_table.'} where kodero=\'%s\' order by iddetil';
									$fsql = sprintf($sql, db_escape_string($data->kodero));
									
									//if ($data->kodero=='43402001') drupal_set_message($fsql);
									
									$resdetil = db_query($fsql);
									if ($resdetil) {
										while ($datdetil = db_fetch_object($resdetil)) {

											$rowsrek[] = array (
																	 array('data' => '',  'width'=> '50px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => '- ' . $datdetil->uraianp,  'width' => '220px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datdetil->unitjumlah . ' ' . $datdetil->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datdetil->volumjumlah . ' ' . $datdetil->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datdetil->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datdetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $datdetil->unitjumlahp . ' ' . $datdetil->unitsatuanp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datdetil->volumjumlahp . ' ' . $datdetil->volumsatuanp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datdetil->hargap),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datdetil->totalp),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datdetil->totalp-$datdetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn1(apbd_hitungpersen($datdetil->total, $datdetil->totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );
												
											
										}
									}
	
									
								}	//loop REKENING
							}	//REKENING
												 
						////////
						}	//looping obyek
					}	//if obyek
					
				}	//looping jenis
			}	//if jenis
			
		}	//KELOMPOK LOOPING
	}	//KELOMPOK

	$rowsrek[] = array (
						 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '270px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($totalp),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => apbd_fn($totalp-$total),  'width' => '70px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total,$totalp)),  'width' => '35px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
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

		$pquery = "select sum(k.jumlahp) total from {anggperukperubahan" . $str_table . "} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea where left(k.kodero,2)>'41'";

		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$totalp = $data->total;
		}

		$pquery = "select sum(k.jumlah/1000) total from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea where left(k.kodero,2)>'41'";

		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = round($data->total/4,0)*1000;
		}
		
		$tw2 = $tw1;
		$tw3 = $tw2;
		$tw4 = ($totalp) - $tw1 - $tw2 - $tw3;		
		
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
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'colspan'=>'8', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
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

function pendapatanppkd_print_form () {
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
		'#default_value' => $revisi
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function pendapatanppkd_print_form_submit($form, &$form_state) {
	$topmargin = $form_state['values']['topmargin'];
	$revisi = $form_state['values']['revisi'];
	$uri = 'apbd/pendapatanppkd/print/'.$revisi.'/' . $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>