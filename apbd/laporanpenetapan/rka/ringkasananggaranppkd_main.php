<?php
function ringkasananggaranppkd_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	 
	$tingkat = arg(4);
	$topmargin = arg(5);
	$exportpdf = arg(6);
	$isppkd = 1;

	if ($tingkat=='') $tingkat = 3;
	if ($topmargin=='') $topmargin = 10;

	//drupal_set_message($tingkat);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-ringkasananggaranppkd.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($kodeuk, $tingkat);
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3P($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$output = drupal_get_form('ringkasananggaranppkd_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}

function GenReportForm($print=0) {
	
	$tingkat = arg(4);
	if ($tingkat=='') $tingkat = 3;
	//drupal_set_message($kodeuk);

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$pquery = sprintf("select '00000' kodedinas, 'PEJABAT PENGELOLA KEUANGAN DAERAH' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
				from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
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
	$rowsjudul[] = array (array ('data'=>'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

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
						 array('data' => 'Uraian',  'width' => '375px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//****PENDAPATAN
	$totalp = 0;
	$where = ' where left(k.kodero,2)>\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, '41');
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where left(k.kodero,2) >\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, '41', db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				

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
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\' and left(k.kodero,2) >\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datakel->kodek), '41');
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
								$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\' and left(k.kodero,2) >\'%s\'';
								$fsql = sprintf($sql, db_escape_string($data->kodej), '41');
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
											$sql = 'select r.kodero,r.uraian,jumlah jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\' and left(k.kodero,2) >\'%s\'';
											$fsql = sprintf($sql, db_escape_string($datao->kodeo), '41');
											$fsql .= ' order by r.kodero';
												
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
		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		*/	
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 ';
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
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
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,2)=\'%s\'';
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
								$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,3)=\'%s\'';
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
											$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,5)=\'%s\'';
											$fsql = sprintf($sql, db_escape_string($datao->kodeo));
											$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
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

		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );	
		*/
	}
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	//PEMBIAYAAN

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
						 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );

	$rowsrek[] = array (
						 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
							 
	
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
	
	$isppkd = 1;
	$kodeuk = arg(4);
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$pquery = sprintf("select '12000' kodedinas, 'PEJABAT PENGELOLA KEUANGAN DAERAH' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '120' kodeu, 'OTONOMI DAERAH, PEMERINTAHAN UMUM' urusan 
				from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
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
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	//POTRAIT = 353
	//LANDSCAPE = 875
	if ($isppkd) 
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '200px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARAN PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '260px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
							 array('data' => $tahun, 'width' => '75',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
							 );
	else
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '200px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '260px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => $tahun, 'width' => '75',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '370',   'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);
	$rowskegiatan[]= array (
						 array('data' => 'RINGKASAN ANGGARAN PENDAPATAN DAN BELANJA PPKD',  'width'=> '535px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
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
	$where = ' where left(k.kodero,2)>\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, '41');
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where left(k.kodero,2) >\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, '41', db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				

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
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\' and left(k.kodero,2) >\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datakel->kodek), '41');
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
								$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\' and left(k.kodero,2) >\'%s\'';
								$fsql = sprintf($sql, db_escape_string($data->kodej), '41');
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
											$sql = 'select r.kodero,r.uraian,jumlah jumlahx from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\' and left(k.kodero,2) >\'%s\'';
											$fsql = sprintf($sql, db_escape_string($datao->kodeo), '41');
											$fsql .= ' order by r.kodero';
												
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
		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH PENDAPATAN',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );
		*/	
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 ';
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
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
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,2)=\'%s\'';
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
								$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,3)=\'%s\'';
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
											$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.isppkd=1 and mid(k.kodero,1,5)=\'%s\'';
											$fsql = sprintf($sql, db_escape_string($datao->kodeo));
											$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
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

		/*
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
							 );	
		*/
	}
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	//PEMBIAYAAN

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
						 array('data' => 'PEMBIAYAAN NETTO',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );

	$rowsrek[] = array (
						 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter() {
	
	$tipedok = 'dpa';
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	if ($tipedok=='dpa') {
							 
		$rowsfooter[] = array (
							 array('data' => 'Rencana Pelaksanaan Anggaran Pejabat Pengelola Keuangan Daerah',  'width'=> '532px', 'colspan'=>'6', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'No',  'width'=> '20px','rowspan'=>'2',  'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'Uraian',  'width'=> '112px', 'rowspan'=>'2',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'Triwulan',  'width'=> '400px', 'colspan'=>'4',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'I',  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'II',  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'III',  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:center;'),
							 array('data' => 'IV',  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
							 );
		
		/*
		$pquery = 'select sum(bagihasil+dau+dak+hibah+darurat+bagihasilp+dpok) tw from {anggkaspendapatan} where bulan in (1,2,3)';
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw;
		}
		$pquery = 'select sum(bagihasil+dau+dak+hibah+darurat+bagihasilp+dpok) tw from {anggkaspendapatan} where bulan in (4,5,6) ';
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw2 = $data->tw;
		}
		$pquery = 'select sum(bagihasil+dau+dak+hibah+darurat+bagihasilp+dpok) tw from {anggkaspendapatan} where bulan in (7,8,9) ';
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw3 = $data->tw;
		}
		$pquery = 'select sum(bagihasil+dau+dak+hibah+darurat+bagihasilp+dpok) tw from {anggkaspendapatan} where bulan in (10,11,12) ';
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw4 = $data->tw;
		}
		*/
		$tw1 = 433249290000;
		$tw2 = 433249290000;
		$tw3 = 433249290000;
		$tw4 = 433249291000;
		
		$rowsfooter[] = array (
							 array('data' => '4',  'width'=> '20px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  text-align:left;'),
							 array('data' => 'Pendapatan',  'width'=> '112px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;   text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );
		
		//BTL
		$tw1 = 0;
		$tw2 = 0;
		$tw3 = 0;
		$tw4 = 0;
		$pquery = 'select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd} where jenis=1 and inaktif=0 and isppkd=1';
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}
		$rowsfooter[] = array (
							 array('data' => '5.1',  'width'=> '20px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;  text-align:left;'),
							 array('data' => 'Belanja Tidak Langsung',  'width'=> '112px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 );							 

		//Penerimaan Pembiayaan
		$tw1 = 42584406000;
		$tw2 = 42584406000;
		$tw3 = 42584406000;
		$tw4 = 42584409000;
		$rowsfooter[] = array (
							 array('data' => '6.1',  'width'=> '20px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black;   text-align:left;'),
							 array('data' => 'Penerimaan Pembiayaan',  'width'=> '112px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;   text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;   text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 );								 
		//Pengeluaran Pembiayaan
		$tw1 = 2190000000;
		$tw2 = 2190000000;
		$tw3 = 2190000000;
		$tw4 = 2190000000;
		$rowsfooter[] = array (
							 array('data' => '6.2',  'width'=> '20px', 'style' => 'border-top: 1px solid black;  border-left: 1px solid black; border-bottom: 1px solid black;   text-align:left;'),
							 array('data' => 'Pengeluaran Pembiayaan',  'width'=> '112px',  'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black;
							 text-align:left;'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:right;'),
							 );								 
		
		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$namauk = 'PEJABAT PENGELOLA KEUANGAN DAERAH';
			$pimpinannama = $data->setdanama;
			$pimpinannip = $data->setdanip;
			$pimpinanjabatan = $data->setdajabatan;
			$dpatgl = '.........................';
		}	

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Jepara,' . $dpatgl ,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => 'Menyetujui,',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );							 
		
	} 	else {
		$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
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

function ringkasananggaranppkd_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	 
	$tingkat = arg(4);
	$topmargin = arg(5);
	if ($tingkat=='') $tingkat=3;
	if ($topmargin=='') $topmargin=10;


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

function ringkasananggaranppkd_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$topmargin = $form_state['values']['topmargin'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporanpenetapan/rka/ringkasananggaranppkd/' . $tingkat . '/'. $topmargin . '/' ;
	else	
		$uri = 'apbd/laporanpenetapan/rka/ringkasananggaranppkd/' . $tingkat . '/'. $topmargin . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>