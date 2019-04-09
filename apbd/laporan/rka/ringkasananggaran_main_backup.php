<?php
function ringkasananggaran_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	 
	$kodeuk = arg(4);
	$tingkat = arg(5);
	$topmargin = arg(6);
	$tipedok = arg(7);
	$exportpdf = arg(8);

	if ($tingkat=='') $tingkat = 3;
	if ($topmargin=='') $topmargin = 10;

	////drupal_set_message($tingkat);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = $tipedok . '-skpd-ringkasananggaran-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodeuk, $tipedok);
		$htmlContent = GenReportFormContent($kodeuk, $tingkat, $tipedok);
		$htmlFooter = GenReportFormFooter($kodeuk, $tipedok);
		
		apbd_ExportPDF3P($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$output = drupal_get_form('ringkasananggaran_form');
		//$output .= GenReportForm($kodeuk, $tingkat, $tipedok);
		$output .= GenReportFormPerubahan($kodeuk, $tingkat, $tipedok);
		return $output;
	}

}

function GenReportForm($kodeuk, $tingkat, $tipedok) {
	

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	if ($kodeuk!='00') {
		$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
					from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	} else {
		$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
	}
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
	
	if ($tipedok=='dpa') {
		$rowsjudul[] = array (array ('data'=>'RINGKASAN DOKUMEN PELAKSANAAN ANGGARAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
	} else {
		$rowsjudul[] = array (array ('data'=>'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	}

	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '370px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );

	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian', 'width' => '375px', 'colspan'=>'4',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//****PENDAPATAN
	$totalp = 0;
	if ($kodeuk!='00') {
		
		$where = ' where left(k.kodero,2)=\'%s\' and k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, '41', db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
		
		
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	////drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), '41', db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\' and left(k.kodero,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), '41');
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
												 
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
									
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	
										
										//RINCIAN OBYEK
										if ($tingkat>=5) {
											if ($kodeuk!='00') {
												$sql = 'select r.kodero,r.uraian,jumlah jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' order by r.kodero';
												
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											}
											
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
												
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
		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '775px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		*/	
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	////drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											////drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
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

		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );	
		*/
	}
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN', 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
	
	
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		////drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm = $datakel->jumlahx;
				else
					$totalpk = $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				////drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						if ($tingkat==3) {
							$uraianj = ucfirst(strtolower($data->uraian));
							$bold ='';
						} else {
							$uraianj = $data->uraian;
							$bold ='font-weight:bold;';
						}						
						$rowsrek[] = array (
											 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 );

						//OBYEK
						if ($tingkat>=4) {
							$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($data->kodej));
							$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
							
							$resulto = db_query($fsql);
							if ($resulto) {
								while ($datao = db_fetch_object($resulto)) {
									$rowsrek[] = array (
														 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									
									//RINCIAN OBYEK
									if ($tingkat>=5) {
										$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($datao->kodeo));
										$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
										
										$resultro = db_query($fsql);
										if ($resultro) {
											while ($dataro = db_fetch_object($resultro)) {
												$rowsrek[] = array (
																	 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 );	
												
											}
											
										}
										
									}
								}
							}
						}
											 
					}		//END JENIS
				}										 
									 
			////////
			}
		}			
			
		$rowsrek[] = array (
							 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );

		$rowsrek[] = array (
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
							 
	}	//END PEMBIAYAAN	
	
	//TW DPA
	if ($tipedok=='dpa') {
		$rowsrek[] = array (
							 array('data' => 'Rencana Pelaksanaan Anggaran Satuan Kerja Perangkat Daerah per SKPD',  'width'=> '435px', 'colspan'=>'6', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'No',  'width'=> '10px','rowspan'=>'2',  'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'Uraian',  'width'=> '95px', 'rowspan'=>'2',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'Triwulan',  'width'=> '340px', 'colspan'=>'4',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'I',  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'II',  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'III',  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'IV',  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );

		$where = ' and kodeuk=\'%s\'';
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (1,2,3) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (4,5,6) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw2 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (7,8,9) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw3 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (10,11,12) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw4 = $data->tw;
		}
		$rowsrek[] = array (
							 array('data' => '4',  'width'=> '10px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Pendapatan',  'width'=> '95px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );
		
		//BTL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=1 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.1',  'width'=> '10px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Belanja Tidak Langsung',  'width'=> '95px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );							 

		//BL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=2 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.2',  'width'=> '10px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Belanja Langsung',  'width'=> '95px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '85px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );								 
	
		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$setdanama = $data->setdanama;
			$setdanip = $data->setdanip;
			$setdajabatan = $data->setdajabatan;
			$dpatgl = $data->dpatgl;
		}

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Menyetujui,',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdajabatan,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdanama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $setdanip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );		
	
	//DPA	
	}	else {	
	
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
	}
	
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

function GenReportFormPerubahan($kodeuk, $tingkat, $tipedok) {
	

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	if ($kodeuk!='00') {
		$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
					from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	} else {
		$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
	}
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
	
	if ($tipedok=='dpa') {
		$rowsjudul[] = array (array ('data'=>'RINGKASAN DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
	} else {
		$rowsjudul[] = array (array ('data'=>'RINGKASAN PERUBAHAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	}

	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '710px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );

	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian', 'width' => '485px', 'colspan'=>'4',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penetapan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Tambah/ Kurang',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Persen',  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );

	//****PENDAPATAN
	$totalp = 0;
	if ($kodeuk!='00') {
		
		$where = ' where left(k.kodero,2)=\'%s\' and k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, '41', db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
		
		
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	////drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalpendapatan_p += $dataakun->jumlahxp;
			$totalpendapatan += $dataakun->jumlahx;
			
			//
			
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp - $dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), '41', db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp - $datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\' and left(k.kodero,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), '41');
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp - $data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
												 
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
									
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp - $datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn1(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	
										
										//RINCIAN OBYEK
										if ($tingkat>=5) {
											if ($kodeuk!='00') {
												$sql = 'select r.kodero,r.uraian,jumlah jumlahx, jumlahp jumlahxp from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' order by r.kodero';
												
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											}
											
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp - $dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn1(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 
																		 );	
													
												}
												
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
		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '545px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpendapatan),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpendapatan_),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpendapatan_p-$totalpendapatan),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($totalpendapatan, $totalpendapatan_p)),  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		*/	
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalbelanja += $dataakun->jumlahx;
			$totalbelanja_p += $dataakun->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn1(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlahsebelum) jumlahx, sum(jumlah) jumlahxp from {anggperkegrevisi} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											////drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn1(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
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

		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '545px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalbelanja),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalbelanja_p),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalbelanja_p - $totalbelanja),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($totalbelanja, $totalbelanjap)),  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );	
		*/
	}
	
	$surplus = $totalpendapatan - $totalbelanja;
	$surplus_p = $totalpendapatan_p - $totalbelanja_p;
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '545px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surplus),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surplus_p),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surplus_p - $surplus),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($surplus, $surplus_p)),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN', 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
	
	
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		////drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61') {
					$totalpm = $datakel->jumlahx;
					$totalpm_p = $datakel->jumlahxp;	
				} else {
					$totalpk = $datakel->jumlahx;
					$totalpk_p = $datakel->jumlahxp;
				}
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahx)),  'width' => '30px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				////drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						if ($tingkat==3) {
							$uraianj = ucfirst(strtolower($data->uraian));
							$bold ='';
						} else {
							$uraianj = $data->uraian;
							$bold ='font-weight:bold;';
						}						
						$rowsrek[] = array (
											 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $uraianj, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlahx, $data->jumlahx)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 );

						//OBYEK
						if ($tingkat>=4) {
							$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($data->kodej));
							$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
							
							$resulto = db_query($fsql);
							if ($resulto) {
								while ($datao = db_fetch_object($resulto)) {
									$rowsrek[] = array (
														 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $datao->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn1(apbd_hitungpersen($datao->jumlahx, $datao->jumlahx)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									
									//RINCIAN OBYEK
									if ($tingkat>=5) {
										$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($datao->kodeo));
										$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
										
										$resultro = db_query($fsql);
										if ($resultro) {
											while ($dataro = db_fetch_object($resultro)) {
												$rowsrek[] = array (
																	 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => $dataro->uraian, 'width' => '485px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn1(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahx)),  'width' => '30px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 );	
												
											}
											
										}
										
									}
								}
							}
						}
											 
					}		//END JENIS
				}										 
									 
			////////
			}
		}			
		
		
		$netto = $totalpm-$totalpk;
		$netto_p = $totalpm_p - $totalpk_p;
		$rowsrek[] = array (
							 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '545px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($netto),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($netto_p),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($netto_p - $netto),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($netto, $netto_p)),  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		
		$silpa = $surplus + $netto;
		$silpa_p = $surplus_p + $netto_p;
		$rowsrek[] = array (
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width'=> '$silpa = $surplus + $netto;',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($silpa),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($silpa_p),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($silpa_p - $silpa),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn1(apbd_hitungpersen($silpa, $silpa_p)),  'width' => '30px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
							 
	}	//END PEMBIAYAAN	
	
	//TW DPA
	if ($tipedok=='dpa') {
		$rowsrek[] = array (
							 array('data' => 'Rencana Pelaksanaan Perubahan Anggaran Satuan Kerja Perangkat Daerah per SKPD',  'width'=> '875px', 'colspan'=>'9', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'No',  'width'=> '25px','rowspan'=>'2',  'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'Uraian',  'width'=> '100px', 'rowspan'=>'2',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'Triwulan',  'width'=> '500px', 'colspan'=>'4',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'Keterangan',  'width'=> '250px', 'colspan'=>'3', 'rowspan'=>'2',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'I',  'width'=> '125px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'II',  'width'=> '125px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'III',  'width'=> '125px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 array('data' => 'IV',  'width'=> '125px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );

		$tw1 = round($totalpendapatan_p /4,-3);
		$tw2 = $tw1;
		$tw3 = $tw1;
		$tw4 = $totalpendapatan_p - (3*$tw1);
		
		$rowsrek[] = array (
							 array('data' => '4',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Pendapatan',  'width'=> '100px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '250px', 'colspan'=>'3',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );
		
		//BTL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanrevisi} g where jenis=1 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.1',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Belanja Tidak Langsung',  'width'=> '100px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '250px','colspan'=>'3',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );							 

		//BL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanrevisi} g where jenis=2 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.2',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Belanja Langsung',  'width'=> '100px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );								 
	
		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$setdanama = $data->setdanama;
			$setdanip = $data->setdanip;
			$setdajabatan = $data->setdajabatan;
			$dpatgl = $data->dpatgl;
		}

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Menyetujui,',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdajabatan,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdanama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $setdanip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );		
	
	//DPA	
	}	else {	
	
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
	}
	
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


function GenReportFormHeader($kodeuk, $tipedok) {
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	if ($kodeuk!='00') {
		$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
					from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$kodedinas = $data->kodedinas;
			$urusan = $data->kodeu . ' - ' . $data->urusan;
			$skpd = $kodedinas . ' - ' . $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}
	} else {
			$urusan = '000 - SEMUA URUSAN';
			$skpd = 'PEMERINTAH KABUPATEN JEPARA';
	}

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	//POTRAIT = 353
	//LANDSCAPE = 875
	if ($tipedok=='dpa') {
		$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH',  'width'=> '200px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINGKASAN DOKUMEN PELAKSANAAN ANGGARAN', 'width' => '260px','colspan'=>'4', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'DPA-SKPD', 'width' => '75',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
		$rowskegiatan[]= array ( 
						 array('data' => 'KABUPATEN JEPARA',  'width'=> '200px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '260px', 'colspan'=>'4','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => $tahun, 'width' => '75',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 );  

						 
	} else {
		$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '200px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '260px','colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => $tahun, 'width' => '75',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );

	}

	$rowskegiatan[]= array (
					 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
					 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
					 array('data' => $urusan, 'width' => '370',  'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:left;'),
					 );
	$rowskegiatan[]= array (
					 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
					 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
					 array('data' => $skpd,  'width' => '370', 'colspan'=>'4', 'style' => 'border-right: 1px solid black; text-align:left;'),					 
					);
	$rowskegiatan[]= array (
					 array('data' => 'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA SKPD',  'width'=> '535px', 'colspan'=>'6', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
					 );	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $tingkat, $tipedok) {
	////drupal_set_message($tingkat);
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375x','colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****PENDAPATAN
	$totalp = 0;
	if ($kodeuk!='00') {
		
		$where = ' where left(k.kodero,2)=\'%s\' and k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, '41', db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
		
		
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	////drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), '41', db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\' and left(k.kodero,3)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), '41');
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
												 
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
									
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	
										
										//RINCIAN OBYEK
										if ($tingkat>=5) {
											if ($kodeuk!='00') {
												$sql = 'select r.kodero,r.uraian,jumlah jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' order by r.kodero';
												
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											}
											
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
												
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
		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		*/	
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	////drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			////drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											////drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
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

		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );	
		*/
	}
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN', 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
	
	 
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		////drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm = $datakel->jumlahx;
				else
					$totalpk = $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				////drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						if ($tingkat==3) {
							$uraianj = ucfirst(strtolower($data->uraian));
							$bold ='';
						} else {
							$uraianj = $data->uraian;
							$bold ='font-weight:bold;';
						}						
						$rowsrek[] = array (
											 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $uraianj, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 );

						//OBYEK
						if ($tingkat>=4) {
							$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($data->kodej));
							$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
							
							$resulto = db_query($fsql);
							if ($resulto) {
								while ($datao = db_fetch_object($resulto)) {
									$rowsrek[] = array (
														 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $datao->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									
									//RINCIAN OBYEK
									if ($tingkat>=5) {
										$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($datao->kodeo));
										$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
										
										$resultro = db_query($fsql);
										if ($resultro) {
											while ($dataro = db_fetch_object($resultro)) {
												$rowsrek[] = array (
																	 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => $dataro->uraian, 'width' => '375px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 );	
												
											}
											
										}
										
									}
								}
							}
						}
											 
					}		//END JENIS
				}										 
									 
			////////
			}
		}			
			
		$rowsrek[] = array (
							 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );

		$rowsrek[] = array (
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width'=> '435px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 );
							 
	}	//END PEMBIAYAAN	
	
	if ($tipedok=='dpa') {
		$rowsrek[] = array (
							 array('data' => 'Rencana Pelaksanaan Anggaran Satuan Kerja Perangkat Daerah per SKPD',  'width'=> '535px', 'colspan'=>'6', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'No',  'width'=> '25px','rowspan'=>'2',  'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'Uraian',  'width'=> '150px', 'rowspan'=>'2',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'Triwulan',  'width'=> '360px', 'colspan'=>'4',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsrek[] = array (
							 array('data' => 'I',  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'II',  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'III',  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'IV',  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );

		$where = ' and kodeuk=\'%s\'';
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (1,2,3) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (4,5,6) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw2 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (7,8,9) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw3 = $data->tw;
		}
		$pquery = sprintf('select sum(pajak+retribusi+hpkd+padlain) tw from {anggkaspendapatan} where bulan in (10,11,12) ' . $where, db_escape_string($kodeuk));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw4 = $data->tw;
		}
		$rowsrek[] = array (
							 array('data' => '4',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black; text-align:left;'),
							 array('data' => 'Pendapatan',  'width'=> '150px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );
		
		//BTL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=1 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.1',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black; text-align:left;'),
							 array('data' => 'Belanja Tidak Langsung',  'width'=> '150px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );							 

		//BL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = sprintf('select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=2 ' . $where, db_escape_string($kodeuk));
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsrek[] = array (
							 array('data' => '5.2',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  text-align:left;'),
							 array('data' => 'Belanja Langsung',  'width'=> '150px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '90px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '25px', 'style' => 'border-top: 1px solid black;  text-align:left;'),
							 array('data' => '',  'width'=> '150px',  'style' => 'border-top: 1px solid black;  text-align:left;'),
							 array('data' => '',  'width'=> '90px', 'style' => 'border-top: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '90px', 'style' => 'border-top: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '90px', 'style' => 'border-top: 1px solid black; text-align:right;'),
							 array('data' => '',  'width'=> '90px', 'style' => 'border-top: 1px solid black; text-align:right;'),
							 );
	}							
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}
 
function GenReportFormFooter($kodeuk, $tipedok) {
	


	//TW DPA
	if ($tipedok=='dpa') {
							 
	
		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$setdanama = $data->setdanama;
			$setdanip = $data->setdanip;
			$setdajabatan = $data->setdajabatan;
			$dpatgl = $data->dpatgl;
		}

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Menyetujui,',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdajabatan,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $setdanama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $setdanip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );		
	
	//DPA	
	} else {	

		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='';
		if ($kodeuk!='00') {
			$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, 'KEPALA SKPD' pimpinanjabatan 
						from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		} else {
			$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan 
						from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		}
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}	
	
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
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

function ringkasananggaran_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	 
	$kodeuk = arg(4);
	$tingkat = arg(5);
	$topmargin = arg(6);
	$tipedok = arg(7);
	
	if ($tingkat=='') $tingkat=3;
	if ($topmargin=='') $topmargin=10;

	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'	=> $tipedok,
	);
	
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 2,
	);
	

	$form['formdata']['tingkat']= array(
		'#type' => 'radios', 
		'#title' => t('Tingkat'), 
		'#options' => array(	
			 '3' => t('Jenis'), 	
			 '4' => t('Obyek'), 	
			 '5' => t('Rincian Obyek'),	
		   ),
		'#default_value' => $tingkat,
		'#weight' => 3,		
	);	
	
	$form['formdata']['ss0'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 4,
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
		'#weight' => 5,
	);
	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 6,
	);		
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 7,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 8,
	); 
	
	return $form; 
}

function ringkasananggaran_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tipedok = $form_state['values']['tipedok'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$topmargin = $form_state['values']['topmargin'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporan/rka/ringkasananggaran/' . $kodeuk . '/' . $tingkat . '/'. $topmargin . '/' . $tipedok . '/' ;
	else	
		$uri = 'apbd/laporan/rka/ringkasananggaran/' . $kodeuk . '/' . $tingkat . '/'. $topmargin . '/' . $tipedok . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>