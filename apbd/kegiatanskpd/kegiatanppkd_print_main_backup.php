<?php
function kegiatanppkd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = arg(3);
	$exportpdf = arg(4);

	if ($topmargin=='') $topmargin = arg(4);

	//drupal_set_message('Hai');
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-ppkd-kegiatan.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent();
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		//$url = 'apbd/kegiatanppkd/print/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('kegiatanppkd_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}
function GenReportForm($print=0) {
	

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
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '400x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	
	$total = 0;
	
	//drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $sql;
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$sql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			
			//drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $dataobyek->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,sum(jumlah) jumlahx from {anggperkeg} k where mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by kodero,uraian order by kodero';
							
							//drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 );
									if ($datajenis->kodej<'516') {
										//DETIL
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where kodero=\'%s\' order by kodekeg,iddetil';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										//drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '- ' . $datasub->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
										}
									
									} else {
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah total from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where a.kodero=\'%s\' order by kegiatan';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										//drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												$unitjumlah = '1 kegiatan';
												$volumjumlah = '1 kali';
												$hargasatuan = apbd_fn($datadetil->total);
												
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																	 array('data' => '- ' . $datadetil->kegiatan,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
																	 array('data' => $unitjumlah , 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																	 );

																	 
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
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
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
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RKA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
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

function GenReportFormContent() {

	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '75px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );		
	$total = 0;
	
	//drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $sql;
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '75px','style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian,  'width' => '400x', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 );
			
			//JENIS	
			$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$sql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			
			//drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					//drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
												 );		

							//REKENING
							$sql = 'select kodero,uraian,sum(jumlah) jumlahx from {anggperkeg} k where mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by kodero,uraian order by kodero';
							
							//drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
													 );
									
									if ($datajenis->kodej < '516') {
										//DETIL
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where kodero=\'%s\' order by kodekeg,iddetil';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										//drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '-',  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datasub->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
										}
									
									} else {
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah total from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where a.kodero=\'%s\' order by kegiatan';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										//drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												$unitjumlah = '1 kegiatan';
												$volumjumlah = '1 tahun';
												$hargasatuan = apbd_fn($datadetil->total);
												
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => '-',  'width' => '15px', 'style' => 'text-align:right;'),
																	 array('data' => $datadetil->kegiatan,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
																	 array('data' => $unitjumlah , 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																);

		
												
																	 
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
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter() {
	
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$tipedok = 'dpa';
	
	if ($tipedok='dpa') {
		$pquery = 'select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=1 and inaktif=0 and isppkd=1';
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}

		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan='PEJABAT PENGELOLA KEUANGAN DAERAH';
		}
		$dpatgl = '....Januari 2016';
		$namauk = 'PEJABAT PENGELOLA KEUANGAN DAERAH';
		
		$rowsfooter[] = array (
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
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
	
	$topmargin = arg(3);
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
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function kegiatanppkd_print_form_submit($form, &$form_state) {
	$topmargin = $form_state['values']['topmargin'];
	$uri = 'apbd/kegiatanppkd/print/' . $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>