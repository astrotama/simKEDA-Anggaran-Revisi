<?php
function kegiatanrkpd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = '20';
	$kodekeg = arg(3);
	
	$exportpdf = arg(5);

	if (isset($topmargin)) $topmargin = arg(4);

	//drupal_set_message($kodekeg);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'dpa-skpd-belanja-' . $kodekeg . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodekeg);
		$htmlContent = GenReportFormContentDPA($kodekeg);
		$htmlFooter = GenReportFormFooter($kodekeg);
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile);
		//return $htmlHeader . $htmlContent . $htmlFooter;
		//apbd_ExportPDF('L', 'F4', $htmlHeader . $htmlContent . $htmlFooter, $pdfFile);
		
	} else {
		$url = 'apbd/kegiatanskpd/printdpa/'. $kodekeg . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('kegiatanrkpd_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($kodekeg);
		return $output;
	}

}
function GenReportForm($kodekeg) {
	
	
	/*
	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetil} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetil} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	echo $jumrincian;
	*/
	
	//drupal_set_message($dpa);

	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select kodedinas, namauk from {unitkerja} u left join {kegiatanskpd} k 
			  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$skpd = $kodedinas . ' - ' . $data->namauk;
}

	$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$budnama = $data->budnama;
		$budnip = $data->budnip;
		$budjabatan = $data->budjabatan;
		$dpatgl = $data->dpatgl;
	}

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanskpd} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu' . $where, db_escape_string($kodekeg));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodeu . '.' . $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		
		$jenis = $data->jenis;
		$tahun = $data->tahun;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		$masukantarget = $data->masukantarget;
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$plafon = $data->plafon;
		$totalsebelum = $data->totalsebelum;
		$totalsesudah = $data->totalsesudah;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		$tw1 = $data->tw1;
		$tw2 = $data->tw2;
		$tw3 = $data->tw3;
		$tw4 = $data->tw4;
		
	}	
	$tahunsebelum = $tahun-1;
	$tahunsesudah = $tahun+1;
	 
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	
	if ($jenis==1) $strbelanja = 'TIDAK ';
	
	$rowsjudul[] = array (array ('data'=>'DPA-SKPD - BELANJA ' . $strbelanja . 'LANGSUNG', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	
	
	$rowskegiatan[]= array (
						 //array('data' => 'Fungsi',  'width'=> '150px', 'style' => ' border-bottom: 1px solid black;  text-align:left;'),
						 //array('data' => ':', 'width' => '25px', 'style' => 'border-bottom: 1px solid black;  text-align:left;'),
						 //array('data' => 'Fungsinnya', 'width' => '700', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black;  text-align:left;'),

						 array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border:none;; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $fungsi, 'width' => '710', 'colspan'=>'5',  'style' => 'border:none; text-align:left;'),

						 );
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
	$rowskegiatan[]= array (
						 array('data' => 'Program',   'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $program,   'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $kegiatan,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Lokasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $lokasi,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahun,  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($total),  'width' => '160', 'colspan'=>'2',  'style' => 'text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	

	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahunsebelum ,  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($totalsebelum),  'width' => '160', 'colspan'=>'2',  'style' => 'text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($totalsebelum),  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahunsesudah,   'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($totalsesudah),  'width' => '160', 'colspan'=>'2',  'style' => 'text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($totalsesudah),  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	

	
	if ($jenis==2) {
		//TUK
		$rowskegiatan[]= array (
							 array('data' => 'Indikator',  'width'=> '175px', 'colspan'=>'2',  'style' => ' border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Tolok Ukur Kinerja', 'width' => '350px', 'colspan'=>'3',  'style' => 'border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Target Kinerja', 'width' => '350', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Capaian Program',  'width'=> '175px', 'colspan'=>'2',  'style' => '   text-align:left;'),
							 array('data' => $programsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $programtarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Masukan',  'width'=> '175px', 'colspan'=>'2',  'style' => '  text-align:left;'),
							 array('data' => $masukansasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $masukantarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Keluaran',  'width'=> '175px', 'colspan'=>'2',  'style' => '   text-align:left;'),
							 array('data' => $keluaransasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $keluarantarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Hasil',  'width'=> '175px', 'colspan'=>'2',  'style' => '  text-align:left;'),
							 array('data' => $hasilsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $hasiltarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	

		//Kelompok Sasaran Kegiatan
		$rowskegiatan[]= array (
							 array('data' => 'Kelompok Sasaran',   'width'=> '150px', 'style' => 'text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $kelompoksasaran,   'width' => '710', 'colspan'=>'5',  'style' => 'text-align:left;'),
							 );				
	}
	 
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '400x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//JENIS
	$total = 0;
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
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
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					//drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );
							//DETIL
							$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							//drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
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
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '- ' . $datadetil->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										//drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '. ' . $datasub->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
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

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );


							 
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );

	$rowsfooter[] = array (
						 array('data' => 'RENCANA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 1',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => $budjabatan,  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 2',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 3',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 4',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => $budnama,  'width' => '300px', 'style' => 'text-align:center;text-decoration: underline'),
						 );	
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:center'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'NIP. ' . $budnip,  'width' => '300px', 'style' => 'text-align:center;'),
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

function GenReportFormHeader($kodekeg) {
	

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, k.kegiatan, k.lokasi, k.jenis, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran, k.keluarantarget, 
				k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, uk.namauk from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
	
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodepro . ' - ' . $data->program;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;

		$tahun = $data->tahun;
		$jenis = $data->jenis;
		
		$tahunsebelum = $tahun-1;
		$tahunsesudah = $tahun+1;
		 
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		$masukantarget = $data->masukantarget;
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$plafon = $data->plafon;
		$totalsebelum = $data->totalsebelum;
		$totalsesudah = $data->totalsesudah;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		
	}	

 
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	/*
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 );
	*/
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'DOKUMEN PELAKSANAAN DAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
						 );
	if ($jenis==1) $strjenis = 'T I D A K  -  ';
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'DPA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
						 );
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'NOMOR : ', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
						 );
	 
	$rowskegiatan[]= array (
						 //array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => ':', 'width' => '25px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => 'Fungsinnya', 'width' => '700', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),

						 array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $fungsi, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),

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
	$rowskegiatan[]= array (
						 array('data' => 'Program',   'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $program,   'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $kegiatan,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Lokasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $lokasi,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahun,  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($total),  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	

	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahunsebelum ,  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($totalsebelum),  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($totalsebelum),  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran ' . $tahunsesudah,   'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($totalsesudah),  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($totalsesudah),  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	


	//TUK
	if ($jenis==2) {
		$rowskegiatan[]= array (
							 array('data' => 'Indikator',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Tolok Ukur Kinerja', 'width' => '350px', 'colspan'=>'3',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Target Kinerja', 'width' => '350', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Capaian Program',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $programsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $programtarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Masukan',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukansasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukantarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Keluaran',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluaransasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluarantarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Hasil',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasilsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasiltarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	

		//Kelompok Sasaran Kegiatan
		$rowskegiatan[]= array (
							 array('data' => 'Kelompok Sasaran',   'width'=> '150px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => ' border-top: 1px solid black; text-align:right;'),
							 array('data' => $kelompoksasaran,   'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );							 
		 
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContentDPA($kodekeg) {


	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetil} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetil} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	if ($jumrincian > 350) {
		set_time_limit(0);
		ini_set('memory_limit', '640M');
	}
	
	$total=0;
	/*
	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	*/

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
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			//drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					//drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 );
							//DETIL
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							//drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '•';
										
									}
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datadetil->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										//drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;
												$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' =>  '- ' . $datasub->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );												
												//$$$
											}
										}
										
										//###
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
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormFooter($kodekeg) {
	
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';

	$pquery = sprintf("select tw1, tw2, tw3, tw4 {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		
		$tw1 = $data->tw1;
		$tw2 = $data->tw2;
		$tw3 = $data->tw3;
		$tw4 = $data->tw4;
		
	}

	$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$budnama = $data->budnama;
		$budnip = $data->budnip;
		$budjabatan = $data->budjabatan;
		$dpatgl = $data->dpatgl;
	}
	
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );

	$rowsfooter[] = array (
						 array('data' => 'RENCANA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 1',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => $budjabatan,  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 2',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 3',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => 'Tri Wulan 4',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
						 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
						 array('data' => $budnama,  'width' => '300px', 'style' => 'text-align:center;text-decoration: underline'),
						 );	
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:center'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
						 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'NIP. ' . $budnip,  'width' => '300px', 'style' => 'text-align:center;'),
						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function kegiatanrkpd_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kodekeg = arg(3);
	$topmargin = arg(4);
	if (!isset($topmargin)) $topmargin=10;

	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodekeg, 
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
function kegiatanrkpd_print_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodekeg = $form_state['values']['kodekeg'];
	$topmargin = $form_state['values']['topmargin'];
	$uri = 'apbd/kegiatanskpd/printdpa/' . $kodekeg . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>