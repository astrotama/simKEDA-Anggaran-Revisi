<?php
function lampiran3_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$lampiran = arg(7);
	$judul = arg(8);
	$ttd = arg(9);
	$exportpdf = arg(10);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran3-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodeuk, $lampiran, $judul);
		$htmlContent = GenReportFormContent($kodeuk);
		$htmlFooter = GenReportFormFooter($ttd);
		
		apbd_ExportPDF3P_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1);
		
	} else {
		$output = drupal_get_form('lampiran3_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($kodeuk, $lampiran, $judul);
		return $output;
	}

}

function GenReportForm($kodeuk, $lampiran, $judul) {

	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}
		
	if ($lampiran =='1') {
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '245px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN III', 'width' => '70px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '220px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '245px',  'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '70px', 'style' => 'border:none; text-align:right;'),
							 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdano, 'width' => '170px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '245px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '70px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
							 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdatgl, 'width' => '170px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 );
	}	

	if ($judul =='1') {
		//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
		$rowsjudul[] = array (array ('data'=>'RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'PENDAPATAN, BELANJA DAN PEMBIAYAAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN 2016', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	}
	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '370px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'SKPD',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );

	$headersrek[] = array (
						 
						 array('data' => 'Kode',  'width'=> '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '335px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//****PENDAPATAN
	$totalp = 0;
		
	$where = ' where k.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.'. $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where k.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		


					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where k.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							

							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
												 );
												 
						}
					}										 
										 
				////////
				}
			}			
		}
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	$where = ' and g.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.' . $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK - BTL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), db_escape_string('51'));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$uraianj = ucfirst(strtolower($data->uraian));
							
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
												 );
							
						}	//while jenis
					}	//res jenis									 
										 
				}	//while kel
			}	//res kel	

			//KELOMPOK - BL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('52'));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';			
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );	
					
					//PROGRAM
					$sql = 'select k.kodepro, p.program uraian,sum(total) jumlahx from {kegiatanskpd} k 
					inner join {program} p on k.kodepro=p.kodepro where k.inaktif=0 and k.kodeuk=\'%s\' and k.jenis=2';
					$fsql = sprintf($sql, db_escape_string($kodeuk));
					$fsql .= ' group by k.kodepro,p.program order by k.kodepro';			
					$resultpro = db_query($fsql);
					if ($resultpro) {
						while ($datapro = db_fetch_object($resultpro)) {
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($datapro->uraian),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 );	
							
							//KEGIATAN
							$sql = 'select kodekeg, nomorkeg, kegiatan uraian, total jumlahx from {kegiatanskpd} k where k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\' and k.jenis=2';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
							$fsql .= ' order by k.nomorkeg';			
							$resultkeg = db_query($fsql);
							if ($resultkeg) {
								while ($datakeg = db_fetch_object($resultkeg)) {
									$rowsrek[] = array (
														 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => strtoupper($datakeg->uraian),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									//REK JENIS
									$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
									$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
									$resultrek = db_query($fsql);
									if ($resultrek) {
										while ($datarek = db_fetch_object($resultrek)) {
											$rowsrek[] = array (
																 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg . '.' . $datarek->kodej,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => ucfirst(strtolower($datarek->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																 array('data' => apbd_fn($datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 );												
										}
									}
								}
							}
							
						}
					}
					
				}
			}
			
		}	//while belanja


	}
	
	//PEMBIAYAAN
	$totalpm = 0;
	$totalpk = 0;
	
	if ($kodeuk=='81') {
		
		$rowsrek[] = array (
							 array('data' => $kodedinas . '.6',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 );
			
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek group by x.kodek,x.uraian order by x.kodek';
			
		//drupal_set_message( $fsql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm += $datakel->jumlahx;
				else
					$totalpk += $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 );		


				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						

						$rowsrek[] = array (
											 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 );
											 
					}
				}										 
									 
			////////
			}
		}			
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpm - $totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 );
		
	}	
	
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm - $totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();
	
	if ($lampiran==1) $output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;	
}

function GenReportFormHeader($kodeuk, $lampiran, $judul) {
	
	$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	$res = db_query($query);
	if ($data = db_fetch_object($res)) {
		$perdano = $data->perdano;
		$perdatgl = $data->perdatgl;
	}

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
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
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	//POTRAIT = 535
	//LANDSCAPE = 875
	if ($lampiran =='1') {
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN III', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '200px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px',  'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border:none; text-align:right;'),
							 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdano , 'width' => '150px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
							 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdatgl , 'width' => '150px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 );
	}

	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	if ($judul =='1') {
		$rowsjudul[] = array (array ('data'=>'RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'PENDAPATAN, BELANJA DAN PEMBIAYAAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN 2016', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	}
	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan',  'width'=> '150px', 'style' => 'text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '370',   'style' => 'text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'SKPD',  'width'=> '150px', 'style' => 'text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370',  'style' => 'text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	if ($lampiran =='1') 
		$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	else
		$output = '';	
	if ($judul =='1') $output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk) {

	$pquery = sprintf("select kodedinas from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
	}
	$headersrek[] = array (
						 
						 array('data' => 'Kode',  'width'=> '100px', 'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '335px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	//****PENDAPATAN
	$totalp = 0;
		
	$where = ' where k.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.'. $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where k.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		


					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where k.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							

							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
												 );
												 
						}
					}										 
										 
				////////
				}
			}			
		}
	}
	
	
	
	//****BELANJA
	$totalb = 0;
	$where = ' and g.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.' . $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
				
			//KELOMPOK - BTL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek ,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$uraianj = ucfirst(strtolower($data->uraian));
							
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
												 );
							
						}	//while jenis
					}	//res jenis									 
										 
				}	//while kel
			}	//res kel	

			//KELOMPOK - BL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('52'));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';			
			$resultkel = db_query($fsql); 
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' =>  $kodedinas . '.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );	
					
					//PROGRAM
					$sql = 'select k.kodepro, p.program uraian,sum(total) jumlahx from {kegiatanskpd} k 
					inner join {program} p on k.kodepro=p.kodepro where k.inaktif=0 and k.kodeuk=\'%s\' and k.jenis=2';
					$fsql = sprintf($sql, db_escape_string($kodeuk));
					$fsql .= ' group by k.kodepro,p.program order by k.kodepro';			
					$resultpro = db_query($fsql);
					if ($resultpro) {
						while ($datapro = db_fetch_object($resultpro)) {
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($datapro->uraian),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 );	
							
							//KEGIATAN
							$sql = 'select kodekeg, nomorkeg, kegiatan uraian, total jumlahx from {kegiatanskpd} k where k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\' and k.jenis=2';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
							$fsql .= ' order by k.nomorkeg';			
							$resultkeg = db_query($fsql);
							if ($resultkeg) {
								while ($datakeg = db_fetch_object($resultkeg)) {
									$rowsrek[] = array (
														 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => strtoupper($datakeg->uraian),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									//REK JENIS
									$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
									$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
									$resultrek = db_query($fsql);
									if ($resultrek) {
										while ($datarek = db_fetch_object($resultrek)) {
											$rowsrek[] = array (
																 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . $datakeg->nomorkeg . '.' . $datarek->kodej,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => ucfirst(strtolower($datarek->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																 array('data' => apbd_fn($datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 );												
										}
									}
								}
							}
							
						}
					}
					
				}
			}
			
		}	//while belanja


	}

	//PEMBIAYAAN
	$totalpm = 0;
	$totalpk = 0;
	
	if ($kodeuk=='81') {
		
		$rowsrek[] = array (
							 array('data' => $kodedinas . '.6',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 );
			
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek group by x.kodek,x.uraian order by x.kodek';
			
		//drupal_set_message( $fsql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					$totalpm += $datakel->jumlahx;
				else
					$totalpk += $datakel->jumlahx;
				
				$rowsrek[] = array (
									 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
									 );		


				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						

						$rowsrek[] = array (
											 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 );
											 
					}
				}										 
									 
			////////
			}
		}			
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '335px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpm - $totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 );
		
	}	
	
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm - $totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($ttd) {
	if ($ttd==1) {
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
	}	
	return $output;	
}

function lampiran3_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	 
	$kodeuk = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$lampiran = arg(7);
	$judul = arg(8);
	$ttd = arg(9);
	if ($topmargin=='') $topmargin=10;
	if ($hal1=='') $hal1=1;

	$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] = '--PILIH SKPD--';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		//'#weight' => 2,
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
		//'#weight' => 5,
	);
	$form['formdata']['lampiran']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak Lampiran'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $lampiran,
	);	
	$form['formdata']['ssl'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	

	$form['formdata']['judul']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak Judul'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $judul,
	);	
	$form['formdata']['sst'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	

	$form['formdata']['ttd']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak TTD'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $ttd,
	);	
	
	$form['formdata']['ss0'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
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
		//'#weight' => 7,
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		//'#weight' => 9,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		//'#weight' => 10,
	); 
	
	return $form;
}

function lampiran3_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	$lampiran = $form_state['values']['lampiran'];
	$judul = $form_state['values']['judul'];
	$ttd = $form_state['values']['ttd'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporanpenetapan/apbd/lampiran3/' . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $lampiran . '/' . $judul . '/'. $ttd ;
	else	
		$uri = 'apbd/laporanpenetapan/apbd/lampiran3/' . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $lampiran . '/' . $judul . '/'. $ttd . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>