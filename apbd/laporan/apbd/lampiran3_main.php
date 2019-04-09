<?php
function lampiran3_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = arg(4);
	$kodeuk = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	$lampiran = arg(8);
	$judul = arg(9);
	$ttd = arg(10);
	$exportpdf = arg(11);

	//drupal_set_message('j ' . $judul);
	
	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	if ($revisi=='9') {
		$system_revisi =  variable_get('apbdrevisi', 1);
		$str_revisi = 'Terakhir (#' . $system_revisi . ')';		
		
		
	} else
		$str_revisi = '#' . $revisi;
	drupal_set_title('Lampiran III APBD - Revisi ' . $str_revisi);
	
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lampiran3-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		//$htmlHeader = GenReportFormHeader($kodeuk, $lampiran, $judul,$revisi);
		$htmlContent = GenReportFormContent($kodeuk, $revisi, $lampiran, $judul);
		$htmlFooter = GenReportFormFooter($ttd);
		
		//apbd_ExportPDF3P_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1);
		apbd_ExportPDF3_CFM($topmargin,$topmargin, null, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$output = drupal_get_form('lampiran3_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($kodeuk, $revisi, $lampiran, $judul);
		return $output;
	}

}

function GenReportForm($kodeuk, $lampiran, $judul,$revisi) {
	
	drupal_set_message($kodeuk);
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
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
		$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
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
						 
						 array('data' => 'Kode',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '210px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penetapan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Tambah/Kurang',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '%',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penjelasan',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//****PENDAPATAN
	$totalp = 0;$totalpp = 0;$totalpt = 0;
		
	$where = ' where k.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$totalpp += $dataakun->jumlahxp;
			$totalpt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.'. $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
								 );
				
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where k.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),									 
										 );		


					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where k.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							

							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );
												 
						}
					}										 
										 
				////////
				}
			}			
		}
	}
	
	
	
	//****BELANJA
	$totalb = 0;$totalbp = 0;$totalbt = 0;
	$where = ' and g.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(k.jumlah) jumlahx,sum(k.jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg inner join {kegiatanperubahan' . $str_table . '} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$totalbp += $dataakun->jumlahxp;
			$totalbt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.' . $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
								 );
				
			//KELOMPOK - BTL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
										 );		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), db_escape_string('51'));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$uraianj = ucfirst(strtolower($data->uraian));
							
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );
							
						}	//while jenis
					}	//res jenis									 
										 
				}	//while kel
			}	//res kel	

			//KELOMPOK - BL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('52'));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';			
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
										 );	
					
					//PROGRAM
					$sql = 'select k.kodepro, p.program uraian,sum(total) jumlahx, sum(totalp) jumlahxp from {kegiatanperubahan' . $str_table . '} k 
					inner join {program} p on k.kodepro=p.kodepro where k.inaktif=0 and k.kodeuk=\'%s\' and k.jenis=2';
					$fsql = sprintf($sql, db_escape_string($kodeuk));
					$fsql .= ' group by k.kodepro,p.program order by k.kodepro';			
					$resultpro = db_query($fsql);
					if ($resultpro) {
						while ($datapro = db_fetch_object($resultpro)) {
							if (($datapro->jumlahx+$datapro->jumlahxp)>0) 
								$rowsrek[] = array (
												 array('data' => $kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($datapro->uraian),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($datapro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($datapro->jumlahxp-$datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn2(apbd_hitungpersen($datapro->jumlahx, $datapro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );	
							 
							//KEGIATAN
							$sql = 'select kodekeg, nomorkeg, kegiatan uraian, total jumlahx , totalp jumlahxp from {kegiatanperubahan' . $str_table . '} k where k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\' and k.jenis=2';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
							$fsql .= ' order by k.nomorkeg';			
							$resultkeg = db_query($fsql);
							if ($resultkeg) {
								while ($datakeg = db_fetch_object($resultkeg)) {
									if (($datakeg->jumlahx+$datakeg->jumlahxp)>0) {
										$rowsrek[] = array (
														 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3) . 'A',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => strtoupper($datakeg->uraian),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datakeg->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datakeg->jumlahxp-$datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn2(apbd_hitungpersen($datakeg->jumlahx, $datakeg->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
														 );	
										//REK JENIS
										$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
										$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
										$resultrek = db_query($fsql);
										if ($resultrek) {
											while ($datarek = db_fetch_object($resultrek)) {
												$rowsrek[] = array (
																	 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3) . '.' . $datarek->kodej,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => ucfirst(strtolower($datarek->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																	 array('data' => apbd_fn($datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($datarek->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($datarek->jumlahxp-$datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn2(apbd_hitungpersen($datarek->jumlahx, $datarek->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
																	 );												
											}
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
	$totalpm2 = 0;
	$totalpm3 = 0;
	$totalpk = 0;
	$totalpk2 = 0;
	$totalpk3 = 0;
	
	if ($kodeuk=='81') {
		
		$rowsrek[] = array (
							 array('data' => $kodedinas . '.6',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
			
		$sql = 'select x.kodek,x.uraian,sum(jumlah), sum(jumlahp) jumlahxp from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek group by x.kodek,x.uraian order by x.kodek';
			
		//drupal_set_message( $fsql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					{
						$totalpm += $datakel->jumlahx;
						$totalpm2 += $datakel->jumlahxp;
						$totalpm3 += $datakel->jumlahxp-$datakel->jumlahx;
					}
				else
					{
						$totalpk += $datakel->jumlahx;
						$totalpk2 += $datakel->jumlahxp;
						$totalpk3 += $datakel->jumlahxp-$datakel->jumlahx;
					}
				
				$rowsrek[] = array (
									 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
									 );		


				$sql = 'select j.kodej,j.uraian,sum(jumlah), sum(jumlahp) jumlahxp from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						

						$rowsrek[] = array (
											 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 );
											 
					}
				}										 
									 
			////////
			}
		}			
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpm - $totalpk),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 );
		
	}	
	
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '325px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm - $totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpp-$totalbp+$totalpm2 - $totalpk2),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpt-$totalbt+$totalpm3 - $totalpk3),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn2(apbd_hitungpersen($totalp-$totalb+$totalpm - $totalpk,$totalpp-$totalbp+$totalpm2 - $totalpk2)),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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
						 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
						 array('data' => 'LAMPIRAN III', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
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
							 array('data' => '',  'width'=> '285px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN III', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => ': PERATURAN DAERAH KABUPATEN JEPARA', 'width' => '210px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
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
		*/
	}

	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	if ($judul =='1') {
		$rowsjudul[] = array (array ('data'=>'RINCIAN PERUBAHAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'PENDAPATAN, BELANJA DAN PEMBIAYAAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
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

function GenReportFormContent($kodeuk, $revisi, $lampiran, $judul) {

	ini_set('memory_limit', '1024M');
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
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
							 array('data' => '',  'width'=> '590px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN III', 'width' => '50px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
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
	}	

	if ($judul =='1') {
		//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
		$rowsjudul[] = array (array ('data'=>'RINCIAN PERUBAHAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI', 'width'=>'855px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'PENDAPATAN, BELANJA DAN PEMBIAYAAN', 'width'=>'855px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'855px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	}
	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '670px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'SKPD',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '670px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );
	/*
	$headersrek[] = array (
						 
						 array('data' => 'Kode',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '210px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penetapan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Tambah/Kurang',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '%',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penjelasan',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );			
	*/
	
	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '',  'width' => '210px', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'width' => '200px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BERTAMBAH/BERKURANG',  'width' => '150px','colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => '',  'width' => '150px', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );						 
	$headersrek[] = array (
						 
						 array('data' => 'NOMOR',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'width' => '210px', 'style' => 'border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Sebelum',  'width' => '100px', 'style' => 'border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Setelah',  'width' => '100px', 'style' => 'border-right: 1px solid black;text-align:center;'),
						 array('data' => 'Rupiah',  'width' => '100px', 'style' => 'border-right: 1px solid black; text-align:center;'),
						 array('data' => '%',  'width' => '50px', 'style' => 'border-right: 1px solid black; text-align:center;'),
						 array('data' => 'KETERANGAN',  'width' => '150px', 'style' => 'border-right: 1px solid black; text-align:center;'),
						 );
	$headersrek[] = array (
						 
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => '',  'width' => '210px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => '',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 array('data' => '',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black;  text-align:center;'),
						 

						 );			
	//****PENDAPATAN
	$totalp = 0;$totalpp = 0;$totalpt = 0;
		
	$where = ' where k.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$totalpp += $dataakun->jumlahxp;
			$totalpt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.'. $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
								 );
				
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where k.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),									 
										 );		


					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperukperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where k.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							

							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );
												 
						}
					}										 
										 
				////////
				}
			}			
		}
	}
	
	
	
	//****BELANJA
	$totalb = 0;$totalbp = 0;$totalbt = 0;
	$where = ' and g.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$totalbp += $dataakun->jumlahxp;
			$totalbt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.' . $dataakun->kodea,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
								 );
				
			//KELOMPOK - BTL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
										 );		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek), db_escape_string('51'));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$uraianj = ucfirst(strtolower($data->uraian));
							
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );
							
						}	//while jenis
					}	//res jenis									 
										 
				}	//while kel
			}	//res kel	

			//KELOMPOK - BL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('52'));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';			
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => 'border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
										 );	
					
					//PROGRAM
					$sql = 'select k.kodepro, p.program uraian,sum(total) jumlahx, sum(totalp) jumlahxp from {kegiatanperubahan' . $str_table . '} k 
					inner join {program} p on k.kodepro=p.kodepro where k.inaktif=0 and k.kodeuk=\'%s\' and k.jenis=2';
					$fsql = sprintf($sql, db_escape_string($kodeuk));
					$fsql .= ' group by k.kodepro,p.program order by k.kodepro';			
					$resultpro = db_query($fsql);
					if ($resultpro) {
						while ($datapro = db_fetch_object($resultpro)) {
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.' . $datapro->kodepro,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($datapro->uraian),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn($datapro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn($datapro->jumlahxp-$datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn2(apbd_hitungpersen($datapro->jumlahx, $datapro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
												array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
												 );	
							
							//KEGIATAN
							$sql = 'select kodekeg, nomorkeg, kegiatan uraian, total jumlahx , totalp jumlahxp from {kegiatanperubahan' . $str_table . '} k where k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\' and k.jenis=2';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
							$fsql .= ' order by k.kodepro, k.nomorkeg';			
							$resultkeg = db_query($fsql);
							if ($resultkeg) {
								while ($datakeg = db_fetch_object($resultkeg)) {
									
									//drupal_set_message(substr($datakeg->kodekeg, -3));
									
									if (($datakeg->jumlahx+$datakeg->jumlahxp)>0) {
										$rowsrek[] = array (
															 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => strtoupper($datakeg->uraian),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datakeg->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datakeg->jumlahxp-$datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn2(apbd_hitungpersen($datakeg->jumlahx, $datakeg->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
															 );	
										
										
										//REK JENIS
										$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
										$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
										$resultrek = db_query($fsql);
										if ($resultrek) {
											while ($datarek = db_fetch_object($resultrek)) {
												$rowsrek[] = array (
																	 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg, -3) . '.' . $datarek->kodej,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => ucfirst(strtolower($datarek->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																	 array('data' => apbd_fn($datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($datarek->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($datarek->jumlahxp-$datarek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn2(apbd_hitungpersen($datarek->jumlahx, $datarek->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
																	 );												
											}
										}
										
										 
									}	
								}	//WHILE KEG
							}
							
						}
					}
					
				}
			}
			
		}	//while belanja


	}
	
	//PEMBIAYAAN
	$totalpm = 0;
	$totalpm2 = 0;
	$totalpm3 = 0;
	$totalpk = 0;
	$totalpk2 = 0;
	$totalpk3 = 0;
	
	if ($kodeuk=='81') {
		
		$rowsrek[] = array (
							 array('data' => $kodedinas . '.6',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );
			
		$sql = 'select x.kodek,x.uraian,sum(jumlah), sum(jumlahp) jumlahxp from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek group by x.kodek,x.uraian order by x.kodek';
			
		//drupal_set_message( $fsql);
		$resultkel = db_query($sql);
		if ($resultkel) {$net_t=array();$ind=0;
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					{
						$totalpm += $datakel->jumlahx;
						$totalpm2 += $datakel->jumlahxp;
						$totalpm3 += $datakel->jumlahxp-$datakel->jumlahx;
					}
				else
					{
						$totalpk += $datakel->jumlahx;
						$totalpk2 += $datakel->jumlahxp;
						$totalpk3 += $datakel->jumlahxp-$datakel->jumlahx;
					}
				
				$net_t[$ind]= array($data->jumlahx,$datakel->jumlahxp,$datakel->jumlahxp-$datakel->jumlahx);
				$rowsrek[] = array (
									 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
									 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
									 );	$ind++;	


				$sql = 'select j.kodej,j.uraian,sum(jumlah), sum(jumlahp) jumlahxp from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						
						$rowsrek[] = array (
											 array('data' => $kodedinas . '.000.000.' . ($data->kodej),  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => ucfirst(strtolower($data->uraian)),  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
											 );
											 
					}
				}										 
									 
			////////
			}
		}			
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '210px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							  array('data' => apbd_fn($net_t[0][0]-$net_t[1][0]),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							   array('data' => apbd_fn($net_t[0][1]-$net_t[1][1]),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							    array('data' => apbd_fn($net_t[0][2]-$net_t[1][2]),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn2(apbd_hitungpersen(($net_t[0][0]-$net_t[1][0]), ($net_t[0][1]-$net_t[1][1]))),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 );
		
	}	
	
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '310px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb+$totalpm - $totalpk),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpp-$totalbp+$totalpm2 - $totalpk2),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalpt-$totalbt+$totalpm3 - $totalpk3),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn2(apbd_hitungpersen($totalp-$totalb+$totalpm - $totalpk,$totalpp-$totalbp+$totalpm2 - $totalpk2)),  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
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

function GenReportFormFooter($ttd) {
	if ($ttd==1) {
		$pimpinannama= 'AHMAD MARZUQI';
		$pimpinanjabatan= 'BUPATI JEPARA';
	

		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '210px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '210px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '600px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '210px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '210px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '210px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '210px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '210px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '210px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '210px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '600px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '210px', 'style' => 'text-align:center;'),
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
	 
	$revisi = arg(4);
	$kodeuk = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	$lampiran = arg(8);
	$judul = arg(9);
	$ttd = arg(10);
	
	if ($topmargin=='') $topmargin=10;
	if ($hal1=='') $hal1=1;

	$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where kodeuk in (select kodeuk from kegiatanperubahan where periode=2) order by kodedinas" ;
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
	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#default_value'=> $revisi, 
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
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	$lampiran = $form_state['values']['lampiran'];
	$judul = $form_state['values']['judul'];
	$ttd = $form_state['values']['ttd'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporan/apbd/lampiran3/'. $revisi . '/'  . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $lampiran . '/' . $judul . '/'. $ttd ;
	else	
		$uri = 'apbd/laporan/apbd/lampiran3/' . $revisi. '/' . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $lampiran . '/' . $judul . '/'. $ttd . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>