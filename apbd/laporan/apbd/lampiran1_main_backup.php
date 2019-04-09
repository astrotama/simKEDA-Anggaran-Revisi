<?php
function lampiran1_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	 
	$kodeuk = arg(4);
	$tingkat = arg(5);
	$topmargin = arg(6);

	$hal1 = arg(7);
	$exportpdf = arg(8);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	//drupal_set_message($kodeuk);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-lampiran1-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($tingkat);
		$htmlContent = GenReportFormContent($kodeuk, $tingkat);
		$htmlFooter = GenReportFormFooter($kodeuk);
		
		apbd_ExportPDF3P_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1);

		
	} else {
		$url = 'apbd/laporan/apbd/lampiran1/'. $kodeuk . '/' . $topmargin . '/' . $hal1 . '/pdf';
		$output = drupal_get_form('lampiran1_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}

function GenReportForm($print=0) {
	
	$kodeuk = arg(4);
	$tingkat = arg(5);
	//drupal_set_message($kodeuk);

	$tahun = variable_get('apbdtahun', 0);
	if ($kodeuk!='00') {
		$pquery = sprintf("select namauk from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$juduluk = $data->namauk;
		}
	} else {
		$juduluk = 'KABUPATEN JEPARA';
	}
	
	if ($tingkat==3) {
		$strlamp ='DAERAH KABUPATEN';

		$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", db_escape_string($tahun)) ;
		$res = db_query($query);
		if ($data = db_fetch_object($res)) {
			$perdano = $data->perdano;
			$perdatgl = $data->perdatgl;
		}
		
	} else {
		$strlamp ='BUPATI';
		$query = sprintf("select perbupno,perbuptgl from {setupapp} where tahun='%s'", db_escape_string($tahun)) ;
		$res = db_query($query);
		if ($data = db_fetch_object($res)) {
			$perdano = $data->perbupno;
			$perdatgl = $data->perbuptgl;
		}
	}
	
	if ($kodeuk  == '00') {	
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '255px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN I', 'width' => '60px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => ': PERATURAN ' . $strlamp . ' JEPARA', 'width' => '220px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px',  'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border:none; text-align:right;'),
							 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdano, 'width' => '150px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
							 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdatgl, 'width' => '150px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 );
	}
	
	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rowsjudul[] = array (array ('data'=>'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> $juduluk, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . $tahun, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//****PENDAPATAN
	$totalp = 0;
	if ($kodeuk!='00') {
		
		$where = ' where k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					//drupal_set_message( $fsql);
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
												 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
															 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
																		 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH PENDAPATAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );

	}
	
	
	
	//****BELANJA
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					//drupal_set_message( $fsql);
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
												 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
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
															 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											//drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH BELANJA',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalb),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => 'SURPLUS / DEFISIT',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );
	
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
	
	
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		//drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm = $datakel->jumlahx;
				else
					$totalpk = $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
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
											 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
														 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
																	 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; font-weight:bold;'),
							 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
							 
							 
	}	//END PEMBIAYAAN	
	
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => '',  'width' => '375px', 'style' => ' border-right: 1px solid black; '),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;'),
						 );

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();
	
	if ($kodeuk=='00') $output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function GenReportFormHeader($tingkat) {
	
	$kodeuk = arg(4);
	
	$tahun = variable_get('apbdtahun', 0);
	if ($kodeuk!='00') {
		$pquery = sprintf("select namauk from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$juduluk = $data->namauk;
		}
	} else {
		$juduluk = 'KABUPATEN JEPARA';
	}

	if ($tingkat==3) {
		$strlamp ='DAERAH KABUPATEN';

		$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", db_escape_string($tahun)) ;
		$res = db_query($query);
		if ($data = db_fetch_object($res)) {
			$perdano = $data->perdano;
			$perdatgl = $data->perdatgl;
		}
		
	} else {
		$strlamp ='BUPATI';
		$query = sprintf("select perbupno,perbuptgl from {setupapp} where tahun='%s'", db_escape_string($tahun)) ;
		$res = db_query($query);
		if ($data = db_fetch_object($res)) {
			$perdano = $data->perbupno;
			$perdatgl = $data->perbuptgl;
		}
	}
	
	if ($kodeuk  == '00') {	
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN I', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => ': PERATURAN ' . $strlamp . ' JEPARA', 'width' => '200px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px',  'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border:none; text-align:right;'),
							 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdano, 'width' => '150px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
							 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdatgl, 'width' => '150px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 );
	}
	
	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rowsjudul[] = array (array ('data'=>'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> $juduluk, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . $tahun, 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	//$rowsjudul[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));


	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();
	
	if ($kodeuk  == '00')	
		$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	else
		$output = '';
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $tingkat) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****PENDAPATAN
	$totalp = 0;
	if ($kodeuk!='00') {
		
		$where = ' where k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					//drupal_set_message( $fsql);
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
												 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
															 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
																		 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH PENDAPATAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );

	}
	
	
	
	//****BELANJA
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					//drupal_set_message( $fsql);
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
												 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
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
															 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											//drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH BELANJA',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalb),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => 'SURPLUS / DEFISIT',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );
	
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
	
	
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		//drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm = $datakel->jumlahx;
				else
					$totalpk = $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
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
											 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
														 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
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
																	 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
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
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;'),
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width' => '375px', 'style' => ' border-right: 1px solid black; font-weight:bold;'),
							 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
							 
							 
	}	//END PEMBIAYAAN	

	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; border-bottom: 1px solid black; '),
						 array('data' => '',  'width' => '375px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; '),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; '),
						 );
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($kodeuk) {
	
	if ($kodeuk == '00') {
		$pimpinannama= 'AHMAD MARZUQI';
		$pimpinanjabatan= 'BUPATI JEPARA';
	

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'text-align:center;'),
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
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center;'),
							 );

		$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
		$headerkosong = array();

		//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
		$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
		
		$output .= $toutput;
	
	} else {
		$output = '';
	}
	
	return $output;
	
}

function lampiran1_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	 
	$kodeuk = arg(4);
	$tingkat = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	if ($topmargin=='') $topmargin=10;
	if ($tingkat=='') $tingkat=3;
	if ($hal1=='') $hal1=9999;

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - KABUPATEN';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		'#description'  => 'SKPD yang akan ditampilkan/dicetak', 
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
		'#description'  => 'Margin atas laporan saat dicetak', 
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
		'#weight' => 7,
	);
	
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 8,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 9,
	); 
	
	return $form;
}

function lampiran1_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporan/apbd/lampiran1/' . $kodeuk . '/' . $tingkat . '/'. $topmargin . '/' . $hal1;
	else	
		$uri = 'apbd/laporan/apbd/lampiran1/' . $kodeuk . '/' . $tingkat . '/'. $topmargin . '/' . $hal1 . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>