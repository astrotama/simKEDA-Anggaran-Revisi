<?php
function kegiatanskpd_printdpap_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	//$topmargin = '20';
	$kodekeg = arg(3);
	$topmargin = arg(4);
	$tipedok = arg(5);  //'dpa' / 'rka'
	$revisi = arg(6);
	$exportpdf = arg(7);
	$sampul = arg(8);
	$hal1 = '9999';

	if ($revisi=='') $revisi = variable_get('apbdrevisi', 1);
	if ($revisi=='9') $revisi = variable_get('apbdrevisi', 1);
	
	if (!isset($topmargin)) $topmargin = '20';

	////////drupal_set_message($kodekeg);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		if (isset($sampul))  {

			if ($sampul=='sampuldppa') {
				$pdfFile = 'sampul-dppa-' . $kodekeg . '.pdf';
				$htmlContent = GenReportFormSampulBelanja($kodekeg, $revisi);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
				
			} elseif ($sampul=='sampuld') {
				$pdfFile = 'dppa-skpd-sampul.pdf';
				$htmlContent = GenReportFormSampulDepan($kodekeg);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
				//return 'Hello';
			} elseif ($sampul=='preview') {
			
				$htmlContent = GenReportFormContentDPAResume($kodekeg, $revisi);
				return $htmlContent;
			}
			
		} else {
			//require_once('test.php');
			//myt();
			

			$pdfFile = 'dppa-skpd-' . $kodekeg . '.pdf';
			
 
			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok, $revisi);
			$htmlContent = GenReportFormContentDPA($kodekeg, $revisi);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok, $revisi);
			
			//VERIFIKATOR
			$sql = 'SELECT u.ttd FROM {userskpd} us inner join {kegiatanperubahan} k on us.kodeuk=k.kodeuk inner join {apbdop} u on us.username=u.username where k.kodekeg=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$link=array();
			$ind=0;
			//////drupal_set_message( $fsql);
			$res = db_query($fsql);
			if ($res) {
				while ($data = db_fetch_object($res)) {
					//drupal_set_message($data->ttd);
					$link[$ind]=$data->ttd;
					$ind++;
					
				}
			}
			
			$_SESSION["link_ttd1"] = $link[0];
			$_SESSION["link_ttd2"] = $link[1];
			$_SESSION["link_ttd3"] = $link[2];
			$_SESSION["link_ttd4"] = $link[3];
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'eqeq');
			
			//apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
			
			//return $htmlContent;
		}
	} else if ($exportpdf == 'lsg'){
		/*
		$sql = 'SELECT u.ttd FROM {userskpd} us inner join {kegiatanperubahan} k on us.kodeuk=k.kodeuk inner join {apbdop} u on us.username=u.username where k.kodekeg=\'%s\'';
		$fsql = sprintf($sql, db_escape_string($kodekeg));
		$link=array();
		$ind=0;
		//////drupal_set_message( $fsql);
		$res = db_query($fsql);
		if ($res) {
			while ($data = db_fetch_object($res)) {
				drupal_set_message($data->ttd);
				$link[$ind]=$data->ttd;
				$ind++;
				
			}
		}
		
		$_SESSION["link_ttd1"] = $link[0];
		$_SESSION["link_ttd2"] = $link[1];
		$_SESSION["link_ttd3"] = $link[2];
		$_SESSION["link_ttd4"] = $link[3];
		*/
		
		GenReportFormContentlangsung($kodekeg, $revisi,$tipedok, $topmargin);
		
	} else if ($exportpdf == 'resume'){
		//$output = '<p>Prepare DPPA berhasil</p>';
		
		$offset = arg(8);
		if ($offset=='') $offset = '0';
		$x = resume_dpa($kodekeg, $revisi, $offset);
		
		$output = drupal_get_form('kegiatanskpd_printdpap_form');
		$output .= getDescription($kodekeg, $tipedok);
		return $output;
	
	} else if ($exportpdf == 'pdf1'){
		
			$pdfFile = 'dppa-skpd-' . $kodekeg . '_01.pdf';
			
 
			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok, $revisi);
			$htmlContent = GenReportFormContentDPAResume($kodekeg, '1');
			$htmlFooter = '';	//GenReportFormFooter($kodekeg, $tipedok, $revisi);
			
			//VERIFIKATOR
			$sql = 'SELECT u.ttd FROM {userskpd} us inner join {kegiatanperubahan} k on us.kodeuk=k.kodeuk inner join {apbdop} u on us.username=u.username where k.kodekeg=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$link=array();
			$ind=0;
			//////drupal_set_message( $fsql);
			$res = db_query($fsql);
			if ($res) {
				while ($data = db_fetch_object($res)) {
					//drupal_set_message($data->ttd);
					$link[$ind]=$data->ttd;
					$ind++;
					
				}
			}
			
			$_SESSION["link_ttd1"] = $link[0];
			$_SESSION["link_ttd2"] = $link[1];
			$_SESSION["link_ttd3"] = $link[2];
			$_SESSION["link_ttd4"] = $link[3];
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'eqeq');
			
	} else if ($exportpdf == 'pdf2'){
		
			$pdfFile = 'dppa-skpd-' . $kodekeg . '_02.pdf';
			
 
			$htmlHeader = '';	//GenReportFormHeader($kodekeg, $tipedok, $revisi);
			$htmlContent = GenReportFormContentDPAResume($kodekeg, '2');
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok, $revisi);
			
			//VERIFIKATOR
			$sql = 'SELECT u.ttd FROM {userskpd} us inner join {kegiatanperubahan} k on us.kodeuk=k.kodeuk inner join {apbdop} u on us.username=u.username where k.kodekeg=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$link=array();
			$ind=0;
			//////drupal_set_message( $fsql);
			$res = db_query($fsql);
			if ($res) {
				while ($data = db_fetch_object($res)) {
					//drupal_set_message($data->ttd);
					$link[$ind]=$data->ttd;
					$ind++;
					
				}
			}
			
			$_SESSION["link_ttd1"] = $link[0];
			$_SESSION["link_ttd2"] = $link[1];
			$_SESSION["link_ttd3"] = $link[2];
			$_SESSION["link_ttd4"] = $link[3];
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile,'eqeq');
			
			
	} else {
		
		$output = drupal_get_form('kegiatanskpd_printdpap_form');
		$output .= getDescription($kodekeg, $tipedok);
		return $output;
	}
	
}

function GenReportFormSampulDepan($kodeuk) {
	$where = ' where kodeuk=\'%s\'';
	$pquery = sprintf('select kodedinas, namauk from {unitkerja} ' . $where, db_escape_string($kodeuk));
	////////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
	}
	$rows[] = array (array ('data'=>'', 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'KABUPATEN JEPARA', 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPPA-SKPD)', 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=> $organisasi, 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'765px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array (
				array('data' => '',  'width'=> '190px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'KODE',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'NAMA FORMULIR', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Ringkasan Dokumen Pelaksanaan Perubahan Anggaran Satuan Kerja Perangkat Daerah', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Perubahan Anggaran Pendapatan Satuan Kerja Perangkat Daerah', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Perubahan Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.2',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rekapitulasi Belanja Langsung menurut Program dan Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.2.1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Perubahan Anggaran Belanja Langsung Program dan Per Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '585px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);


	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}


function getDescription($kodekeg, $tipedok){
	$sql = 'select kegiatan from {kegiatanperubahan'.$revisi.'} where kodekeg=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
	if ($data = db_fetch_object($res)) {
		$kegiatan = strtoupper($data->kegiatan);	
	}
	
	$rows[]= array (
				array('data' => '- Kegiatan : ' . $kegiatan, 'style' => 'border-none; text-align:left;'),
			 );							 
	if ($tipedok=='dpa') {
		$rows[]= array (
					array('data' => '- Untuk mencetak DPPA-SKPD, klik tombol DPPA-SKPD', 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak Sampul DPPA, klik tombol Sampul DPPA', 'style' => 'border-none; text-align:left;'),
				 );		
		
		$rows[]= array (
					array('data' => '- Bila hasil cetakan tidak sesuai, misalnya tanda tangan terpotong, tambahkan Margin Atas untuk menggesernya',    'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Bila DPPA tidak bisa dicetak dengan cara normal, hal itu disebabkan uraian DPPA yang terlalu panjang, jadi harus dipecah mejadi dua. Untuk memecahnya, klik tombol Prepare. Lalu klik tombol Bagian 1 untuk menceta bagian 1 dan klik Bagian 2 untuk mencatak bagian 2.',    'style' => 'border-none; text-align:left;'),
				 );							 
	} else {
		$rows[]= array (
					array('data' => '- Untuk mencetak RKPA-SKPD, klik tombol RKPA-SKPD', 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak Sampul RKPA, klik tombol Sampul RKPA', 'style' => 'border-none; text-align:left;'),
				 );		
		
		$rows[]= array (
					array('data' => '- Bila hasil cetakan tidak sesuai, misalnya tanda tangan terpotong, tambahkan Margin Atas untuk menggesernya',    'style' => 'border-none; text-align:left;'),
				 );							 
	}
	
	$headerkosong = array();
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttbl));
	
	return $output;
}

function GenReportFormHeader($kodekeg, $tipedok, $revisi) {

	$revisi_tbl = $revisi;
	
	if ($revisi==variable_get('apbdrevisi', 1)) $revisi_tbl = '';
	if ($revisi=='9') $revisi_tbl = '';
	

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran, k.keluarantarget, 
				k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran
				from {kegiatanskpd} k ' . $where, db_escape_string($kodekeg));
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_pen = str_replace('||',', ', $data->lokasi);
		$programsasaran_pen = $data->programsasaran;
		$programtarget_pen = $data->programtarget;
		$masukansasaran_pen = $data->masukansasaran;
		//$masukantarget_pen = $data->masukantarget;
		$masukantarget_pen = 'Rp ' . apbd_fn($data->total);
		$keluaransasaran_pen = $data->keluaransasaran;
		$keluarantarget_pen = $data->keluarantarget;
		$hasilsasaran_pen = $data->hasilsasaran;
		$hasiltarget_pen = $data->hasiltarget;
		$total_pen = $data->total;
		$plafon_pen = $data->plafon;
		$waktupelaksanaan_pen = $data->waktupelaksanaan;
		$sumberdana1_pen = $data->sumberdana1;
		$sumberdana2_pen = $data->sumberdana2;
		$sumberdana1rp_pen = $data->sumberdana1rp;
		$sumberdana2rp_pen = $data->sumberdana2rp;
		$latarbelakang_pen = $data->latarbelakang;
		$kelompoksasaran_pen = $data->kelompoksasaran;		
	}
	
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, k.kegiatan, k.lokasi, k.jenis, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran, k.keluarantarget, 
				k.hasilsasaran,  k.hasiltarget, k.totalp, k.plafon, k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, uk.namauk from {kegiatanperubahan'.$revisi_tbl.'} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);
	
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodepro . ' - ' . $data->program;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3) . ' - ' .  $data->kegiatan;

		$tahun = $data->tahun;
		$jenis = $data->jenis;
		
		$tahunsebelum = $tahun-1;
		$tahunsesudah = $tahun+1;
		 
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		//$masukantarget = $data->masukantarget;
		$masukantarget = 'Rp ' . apbd_fn($data->totalp);
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->totalp;
		$plafon = $data->plafon;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		
	}	
 
	
	if ($latarbelakang=='') {
		$where = ' where kodekeg=\'%s\'';
		$pquery = sprintf('select latarbelakang from {kegiatanrevisi} ' . $where, db_escape_string($kodekeg));
		////////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$latarbelakang = $data->latarbelakang;
		}
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
	if ($jenis==2)  
		$strjenis = 'B E L A N J A  -  L A N G S U N G';
	else  
		$strjenis = 'BELANJA TIDAK LANGSUNG';
	
	if ($tipedok=='dpa') {
		if (isSuperuser())
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => '', 'width' => '250px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'FORMULIR - R.' . $revisi, 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => '<p style="color:red">****************** DPPA UNTUK REVIEW ******************</p>', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => '', 'width' => '250px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'REVISI KE-' . $revisi, 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
								 );

								 
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => $strjenis, 'width' => '250px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPPA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => $strjenis, 'width' => '250px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPPA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '250px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
	} else {
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RKA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RKA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								);
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );		
	} 
	$rowskegiatan[]= array (
						 //array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => ':', 'width' => '25px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => 'Fungsinnya', 'width' => '700', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),

						 array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $fungsi, 'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),

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
	if ($jenis==2)					 
		$rowskegiatan[]= array (
							 array('data' => 'Program',   'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $program,   'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $kegiatan,  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Lokasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $lokasi,  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran Penetapan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => 'Rp ' . apbd_fn($total_pen) . ',00',  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'9',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total_pen),  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran Perubahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => 'Rp ' . apbd_fn($total) . ',00',  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'9',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	

	$rowskegiatan[]= array (
						 array('data' => 'Sumber Dana',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $sumberdana1,  'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	


	//TUK
	if ($jenis==2) {
		$rowskegiatan[]= array (
							 array('data' => 'Indikator',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Tolok Ukur Kinerja', 'width' => '350px', 'colspan'=>'9',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							 array('data' => 'Target Kinerja', 'width' => '350', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => '',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Sebelum Perubahan', 'width' => '175px', 'colspan'=>'8',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Setelah Perubahan', 'width' => '175px',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Sebelum Perubahan', 'width' => '175px',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => 'Setelah Perubahan', 'width' => '175px',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 );	

		 $rowskegiatan[]= array (
							 array('data' => 'Capaian Program',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $programsasaran_pen, 'width' => '175px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $programsasaran, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $programtarget_pen, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $programtarget, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Masukan',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukansasaran_pen, 'width' => '175px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukansasaran, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukantarget_pen, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukantarget, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Keluaran',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluaransasaran_pen, 'width' => '175px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluaransasaran, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluarantarget_pen, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluarantarget, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Hasil',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasilsasaran_pen, 'width' => '175px', 'colspan'=>'8',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasilsasaran, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasiltarget_pen, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasiltarget, 'width' => '175px',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	

		//Kelompok Sasaran Kegiatan
		$rowskegiatan[]= array (
							 array('data' => 'Kelompok Sasaran',   'width'=> '150px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => ' border-top: 1px solid black; text-align:right;'),
							 array('data' => $kelompoksasaran,   'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );							 
		//latar BELAKANG
		$rowskegiatan[]= array (
							 array('data' => 'Latar Belakang Perubahan',   'width'=> '150px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => ' border-top: 1px solid black; text-align:right;'),
							 array('data' => $latarbelakang,   'width' => '710', 'colspan'=>'11',  'style' => 'border-right: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );							 
		 
	}
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Langsung per Program dan Kegiatan Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'13',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
							 );							 
	else
	$rowskegiatan[]= array (
						 array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'13',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
						 );							 
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContentlangsung($kodekeg, $revisi,$tipedok, $topmargin) {
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	
	if ($revisi==variable_get('apbdrevisi', 1)) $revisi = '';
	if ($revisi=='9') $revisi = '';

	$bintangkegiatan = false;

	
	
	set_time_limit(0);
	ini_set('memory_limit', '2048M');
	require_once('files/tcpdf/config/lang/eng.php');
    require_once('files/tcpdf/tcpdf.php');
    //$n='cek';
	class MYPDF extends TCPDF {  
	   // Page footer
		public function Footer($n) {//$ttd=$n;
			// Position at 15 mm from bottom
			//$this->SetY(-10);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
		  //$this->Cell(0, 10, 'Hal. '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');     
		  //$this->Cell(0,0,$this->PageNo(),'T',0,'R');
		  $ttd=array($_SESSION["link_ttd1"],$_SESSION["link_ttd2"],$_SESSION["link_ttd3"],$_SESSION["link_ttd4"]);
		  $str_img=array();
		  for($l=0;$l<=3;$l++){
			  if($ttd[$l]==null){ 
				$str_img[$l]='';  
			  }
			  else{
				$str_img[$l]='<img src="'.$ttd[$l].'" height="40" width="100" top="35px" align="middle" /> </br><font align="center"></font>';  
			  }
			  
			  
		  }
		 $halaman = $this->PageNo() + $base;

		if ($str_img[3] != ''){
			$this->MultiCell(62, 5, ''.$txt, 0, 'C', 0, 1, '', '', true);
			$this->MultiCell(125, 15, $n.'PEMERINTAH KABUPATEN JEPARA TA. 2019 '.$txt, 'T', 'L', 0, 0, '', '', true);
			$this->MultiCell(35, 10, $n.'TIM PENELITI :'.$txt, 1, 'L', 0, 0, '', '', true);
			//$this->writeHTMLCell(40, 5, 130, 195, 'TIM PENELITI :', 0, 'L', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 170, 195,  $str_img[0], 1, 'C', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 200, 195, $str_img[1], 1, 'C', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 230, 195, $str_img[2], 1, 'C', 0, 0, '', '', true);
			
			$this->writeHTMLCell(30, 10, 260, 195, $str_img[3], 1, 'C', 0, 0, '', '', true);
			
			//$this->MultiCell(62, 15, '<img src="/files/btnred.png">', 0, 'L', 0, 0, '', '', true, null, true);
			$this->MultiCell(33, 15, $halaman, 'T', 'R', 0, 0, '', '', true);

		}else{
			$this->MultiCell(62, 5, ''.$txt, 0, 'C', 0, 1, '', '', true);
			$this->MultiCell(125, 15, $n.'PEMERINTAH KABUPATEN JEPARA TA. 2019 '.$txt, 'T', 'L', 0, 0, '', '', true);
			
			$this->MultiCell(35, 10, $n.'TIM PENELITI :'.$txt, 1, 'L', 0, 0, '', '', true);
			//$this->writeHTMLCell(40, 5, 130, 195, 'TIM PENELITI :', 0, 'L', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 170, 195,  $str_img[0], 1, 'C', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 200, 195, $str_img[1], 1, 'C', 0, 0, '', '', true);
			$this->writeHTMLCell(30, 10, 230, 195, $str_img[2], 1, 'C', 0, 0, '', '', true);
			
			$this->MultiCell(63, 15, $halaman, 'T', 'R', 0, 0, '', '', true);
			
			
		
		}
		  //$this->Cell(0,0,'PEMERINTAH KABUPATEN JEPARA TA. 2019','T',0,'L');
		  //$this->Cell(0,0,$this->PageNo(),'T',0,'R');
		  
			//$this->Cell(20, 10, 'Creator', 0, false, 'C', 0, '', 0, false, 'T', 'M'); 
			//$this->Cell(20, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', //0, false, 'T', 'M');
					}    
	}  
	
    //$pdf = new TCPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
	$pdf = new MYPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
    set_time_limit(0);
	ini_set('memory_limit', '6000M');
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('simAnggaran Online');
    $pdf->SetTitle('simAnggaran');
    $pdf->SetSubject('PDF Gen'); 
    $pdf->SetKeywords('APBD');
    $pdf->setPrintHeader(false);
    $pdf->setFooterFont(array('helvetica','', 10));
	//$pdf->setFooterFont(array('times','', 12));
    
	$pdf->setRightMargin(1);
    //$pdf->setFooterMargin(PDF_MARGIN_FOOTER);	
	
	$pdf->setHeaderMargin($topmargin);
	
	$pdf->setFooterMargin(20);
	$pdf->SetAutoPageBreak(true, 17);
	
	//$pdf->setFooterMargin($footermargin);
	//$pdf->SetAutoPageBreak(true, $footermargin);
	
	//$pdf->SetMargins(5,20);
	$pdf->SetMargins(10,$topmargin);
	
    //$pdf->SetAutoPageBreak(true, 11);
    $pdf->setLanguageArray($l);
    $pdf->SetFont('helvetica','', 10);
    $pdf->AddPage();
	
	$pdf->Image('files/logo_kecil.png', 16, 20+$topmargin-10, 20, 18, 'PNG', '', '', 
				true, 150, '', false, false, 0, false, false, false);
	
	$header = GenReportFormHeader($kodekeg, $tipedok, $revisi);
    $pdf->writeHTML($header, true, 0, true, 0);
	
	$ypos = $pdf->GetY()-13;
	//$pdf->Write(5, 'some text' .$ypos, '');
	$pdf->SetY($ypos, true, false);
	
	$is_first = true;
	$batas = 30;
	
	$total = 0;
	$totalpen = 0;


	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
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
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
		
			
			$persen = apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp);
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datajenis->uraian,  'width' => '230x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahxp;
			$totalpen += $datajenis->jumlahx;

			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {

					$persen = apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp);
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 );		
								 
					//REKENING
					$sql = 'select kodero,uraian,jumlah,jumlahp,anggaran from {anggperkegperubahan'.$revisi.'} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						
						//BINTANG
						$alldetilbintang = false;
						if ($bintangkegiatan) {
							$statusrek = '<font color="Red">*</font>';
							$alldetilbintang = true;
								
						} else {
							if (($data->anggaran==0) and ($data->jumlahp>0)) {
								
								$statusrek = '<font color="Red">*</font>';
								$alldetilbintang = true;

							} else {
								
								$statusrek = '';

							}
						}							
 
												
						$penrekening = $data->jumlah;
						$persen = apbd_hitungpersen($penrekening, $data->jumlahp);
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => $data->uraian . $statusrek,  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($penrekening),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => apbd_fn($data->jumlahp - $penrekening),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 
										);
										
							//DETIL
							$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan,anggaran from {anggperkegdetilperubahan'.$revisi.'} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////////drupal_set_message($fsql);

							$resultdetil = db_query($fsql);
							
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									$statusdetil_pen = '';
									if ($penrekening > 0) {
										$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan, anggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' and iddetil=\'%s\'';
										$fsql = sprintf($sql, $kodekeg, $data->kodero, db_escape_string($datadetil->iddetil));
										
										//////drupal_set_message($fsql);
										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										$resultdetilpen = db_query($fsql);
										if ($datadetilpen = db_fetch_object($resultdetilpen)) {
											$datadetilpenuraian = $datadetilpen->uraian;
											$datadetilpentotal = $datadetilpen->total;								
											
										}

										
									} else {
										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';

										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										
									}	
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';

										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '-';
										
										if ($penrekening > 0) {
										$unitjumlahpen = $datadetilpen->unitjumlah . ' ' . $datadetilpen->unitsatuan;
										$volumjumlahpen = $datadetilpen->volumjumlah . ' ' . $datadetilpen->volumsatuan;
										$hargasatuanpen = apbd_fn($datadetilpen->harga);
										//$bullet = '•';
										}
										
									}
									
									//BINTANG
									$allsubbintang = false;
									if ($alldetilbintang) {
										$statusdetil = '<font color="Red">*</font>';
										$allsubbintang = true;
											
									} else {
										if (($datadetil->anggaran==0) and ($datadetil->total>0)) {
											
											$statusdetil = '<font color="Red">*</font>';
											$allsubbintang = true;

										} else {
											
											$statusdetil = '';

									}									}
									
									$persen = apbd_hitungpersen($datadetilpentotal, $datadetil->total);
									if ($datadetil->uraian == $datadetilpenuraian) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
			 												 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' => $datadetil->uraian . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
															 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

															 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 
															 array('data' => apbd_fn($datadetil->total - $datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 );
									} else {
										
										//***
										if (($datadetilpenuraian) !='' and ($datadetilpentotal>0)) {
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetilpenuraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn(-$datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
											//####
											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select distinct idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,anggaran from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												////////drupal_set_message($fsql);
												
												//$no = 0;
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
													}
												}
											}
											//###
											
										}
										
										if (($datadetil->uraian) !='' and ($datadetil->total>0)) {
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetil->uraian  . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn($datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
										}
															 
									}
									
									if (count($rowsrek) >= $batas){
										$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
										
										if ($is_first == false)	$pdf->AddPage();
										$pdf->writeHTML($output, true, 0, true, 0);
										$is_first = false;
										
										$rowsrek = array();
									}
									
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select distinct idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,anggaran from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										//////drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;

												if ($allsubbintang) {
													$statussub = '<font color="Red">*</font>';
												} else {
													if (($datasub->anggaran==0) and ($datasub->total>0))
														$statussub = '<font color="Red">*</font>';
													else
														$statussub = '';
												}
												
												
												$datasuburaian_pen = '';
												$datasubunitjumlah_pen = '';
												$datasubvolumjumlah_pen = '';
												$datasubharga_pen = '';
												$datasubtotal_pen = 0;

												if ($penrekening > 0) {
													$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsub} where iddetil=\'%s\' and idsub=\'%s\'';
													$fsql = sprintf($sql, db_escape_string($datadetil->iddetil), db_escape_string($datasub->idsub));
													$resultsubpen = db_query($fsql);
													if ($resultsubpen) {
														if ($datasubpen = db_fetch_object($resultsubpen)) {
															$datasuburaian_pen = $datasubpen->uraian;
															$datasubunitjumlah_pen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
															$datasubvolumjumlah_pen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
															$datasubharga_pen = $datasubpen->harga;
															$datasubtotal_pen = $datasubpen->total;
														}
													}
												}
												
												if (strtolower(trim($datasuburaian_pen)) == strtolower(trim($datasub->uraian))) {
													$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
													
													//////drupal_set_message($datadetil->iddetil);
													//////drupal_set_message('foo');
												
													$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
															 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' =>  '- ' . $datasub->uraian . $statussub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 );	
															 
															 
												} else {
													if (($datasuburaian_pen) !='' and ($datasubtotal_pen>0)) {

														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasuburaian_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );	
													}

													$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
															 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' =>  '- ' . $datasub->uraian . $statussub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															);	
															 
												}
												//$$$
											}
										}
										
										//###
									}
									
									if (count($rowsrek) >= $batas){
										$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
										
										if ($is_first == false)	$pdf->AddPage();
										$pdf->writeHTML($output, true, 0, true, 0);

										$is_first = false;										
										$rowsrek = array();
									}
									
								}
							}												
						///////					 
						}
					}								 
										 
				////////
				}
			}			

		}
	}
	
	$persen = apbd_hitungpersen($totalpen, $total);
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '290px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn($totalpen),  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total),  'width'=> '240px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total-$totalpen),  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 );
						 
	

	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	if ($is_first == false)	$pdf->AddPage();
	$pdf->writeHTML($output, true, 0, true, 0);
	
	$footer = GenReportFormFooter($kodekeg, $tipedok,$revisi);
	$pdf->writeHTML($footer, true, 0, true, 0);
	
    $pdf->Output('DPPA_' . $kodekeg . '.PDF', 'I');
	
}

function GenReportFormContent($kodekeg, $revisi) {

	if ($revisi==variable_get('apbdrevisi', 1)) $revisi = '';
	if ($revisi=='9') $revisi = '';

	$bintangkegiatan = false;
	$sql = 'select anggaran from {kegiatanskpd} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$bintangkegiatan = ($data->anggaran==0);
		}
	}
	
	
	set_time_limit(0);
	ini_set('memory_limit', '2048M');
	
	$total = 0;
	$totalpen = 0;


	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
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
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
		
			
			$persen = apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp);
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datajenis->uraian,  'width' => '230x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahxp;
			$totalpen += $datajenis->jumlahx;

			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {

					$persen = apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp);
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 );		
								 
					//REKENING
					$sql = 'select kodero,uraian,jumlah,jumlahp,anggaran from {anggperkegperubahan'.$revisi.'} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						
						//BINTANG
						$alldetilbintang = false;
						if ($bintangkegiatan) {
							$statusrek = '<font color="Red">*</font>';
							$alldetilbintang = true;
								
						} else {
							if (($data->anggaran==0) and ($data->jumlahp>0)) {
								
								$statusrek = '<font color="Red">*</font>';
								$alldetilbintang = true;

							} else {
								
								$statusrek = '';

							}
						}							
 
												
						$penrekening = $data->jumlah;
						$persen = apbd_hitungpersen($penrekening, $data->jumlahp);
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => $data->uraian . $statusrek,  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($penrekening),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => apbd_fn($data->jumlahp - $penrekening),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 
										);
										
							//DETIL
							$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan,anggaran from {anggperkegdetilperubahan'.$revisi.'} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////////drupal_set_message($fsql);

							$resultdetil = db_query($fsql);
							
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									$statusdetil_pen = '';
									if ($penrekening > 0) {
										$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan, anggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' and iddetil=\'%s\'';
										$fsql = sprintf($sql, $kodekeg, $data->kodero, db_escape_string($datadetil->iddetil));
										
										//////drupal_set_message($fsql);
										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										$resultdetilpen = db_query($fsql);
										if ($datadetilpen = db_fetch_object($resultdetilpen)) {
											$datadetilpenuraian = $datadetilpen->uraian;
											$datadetilpentotal = $datadetilpen->total;								
											
										}

										
									} else {
										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';

										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										
									}	
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';

										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '-';
										
										if ($penrekening > 0) {
										$unitjumlahpen = $datadetilpen->unitjumlah . ' ' . $datadetilpen->unitsatuan;
										$volumjumlahpen = $datadetilpen->volumjumlah . ' ' . $datadetilpen->volumsatuan;
										$hargasatuanpen = apbd_fn($datadetilpen->harga);
										//$bullet = '•';
										}
										
									}
									
									//BINTANG
									$allsubbintang = false;
									if ($alldetilbintang) {
										$statusdetil = '<font color="Red">*</font>';
										$allsubbintang = true;
											
									} else {
										if (($datadetil->anggaran==0) and ($datadetil->total>0)) {
											
											$statusdetil = '<font color="Red">*</font>';
											$allsubbintang = true;

										} else {
											
											$statusdetil = '';

									}									}
									
									$persen = apbd_hitungpersen($datadetilpentotal, $datadetil->total);
									if ($datadetil->uraian == $datadetilpenuraian) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
			 												 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' => $datadetil->uraian . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
															 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

															 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 
															 array('data' => apbd_fn($datadetil->total - $datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 );
									} else {
										
										//***
										if (($datadetilpenuraian) !='' and ($datadetilpentotal>0)) {
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetilpenuraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn(-$datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
											//####
											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select distinct idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,anggaran from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												////////drupal_set_message($fsql);
												
												//$no = 0;
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
													}
												}
											}
											//###
											
										}
										
										if (($datadetil->uraian) !='' and ($datadetil->total>0)) {
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetil->uraian  . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn($datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
										}
															 
									}
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select distinct idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,anggaran from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										//////drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;

												if ($allsubbintang) {
													$statussub = '<font color="Red">*</font>';
												} else {
													if (($datasub->anggaran==0) and ($datasub->total>0))
														$statussub = '<font color="Red">*</font>';
													else
														$statussub = '';
												}
												
												
												$datasuburaian_pen = '';
												$datasubunitjumlah_pen = '';
												$datasubvolumjumlah_pen = '';
												$datasubharga_pen = '';
												$datasubtotal_pen = 0;

												if ($penrekening > 0) {
													$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsub} where iddetil=\'%s\' and idsub=\'%s\'';
													$fsql = sprintf($sql, db_escape_string($datadetil->iddetil), db_escape_string($datasub->idsub));
													$resultsubpen = db_query($fsql);
													if ($resultsubpen) {
														if ($datasubpen = db_fetch_object($resultsubpen)) {
															$datasuburaian_pen = $datasubpen->uraian;
															$datasubunitjumlah_pen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
															$datasubvolumjumlah_pen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
															$datasubharga_pen = $datasubpen->harga;
															$datasubtotal_pen = $datasubpen->total;
														}
													}
												}
												
												if (strtolower(trim($datasuburaian_pen)) == strtolower(trim($datasub->uraian))) {
													$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
													
													//////drupal_set_message($datadetil->iddetil);
													//////drupal_set_message('foo');
												
													$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
															 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' =>  '- ' . $datasub->uraian . $statussub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 );	
															 
												} else {
													if (($datasuburaian_pen) !='' and ($datasubtotal_pen>0)) {

														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasuburaian_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );	
													}

													$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
															 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' =>  '- ' . $datasub->uraian . $statussub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

															 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
															);	
															 
												}
												//$$$
											}
										}
										
										//###
									}
								}
							}												
						///////					 
						}
					}								 
										 
				////////
				}
			}			

		}
	}
	
	$persen = apbd_hitungpersen($totalpen, $total);
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '290px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn($totalpen),  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total),  'width'=> '240px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total-$totalpen),  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}


function GenReportFormContentDPA($kodekeg, $revisi) {

	if ($revisi==variable_get('apbdrevisi', 1)) $revisi = '';
	if ($revisi=='9') $revisi = '';

	$bintangkegiatan = false;
	$sql = 'select bintang from {kegiatanperubahan} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$bintangkegiatan = $data->bintang;
		}
	}
	
	
	set_time_limit(0);
	ini_set('memory_limit', '2048M');
	
	$total = 0;
	$totalpen = 0;


	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
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
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
		
			
			$persen = apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp);
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datajenis->uraian,  'width' => '230x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahxp;
			$totalpen += $datajenis->jumlahx;

			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {

					$persen = apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp);
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 );		
								 
					//REKENING
					$sql = 'select kodero,uraian,jumlah,jumlahp,bintang from {anggperkegperubahan'.$revisi.'} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						
						//BINTANG
						$alldetilbintang = false;
						if ($bintangkegiatan) {
							$statusrek = '<font color="Red">*</font>';
							$alldetilbintang = true;
								
						} else {
							if ($data->bintang==1) {
								
								$statusrek = '<font color="Red">*</font>';
								$alldetilbintang = true;

							} else {
								
								$statusrek = '';

							}
						}							
 
												
						$penrekening = $data->jumlah;
						$persen = apbd_hitungpersen($penrekening, $data->jumlahp);
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => $data->uraian . $statusrek,  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($penrekening),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => apbd_fn($data->jumlahp - $penrekening),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
											 
										);
										
							//DETIL
							$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan, bintang from {anggperkegdetilperubahan'.$revisi.'} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							
							//if  (($data->kodero=='52206001') and ($kodekeg=='201828434003')) {
							//	drupal_set_message($fsql);
							//}
							
							$resultdetil = db_query($fsql);
							
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									$statusdetil = '';
									
									if ($penrekening > 0) {
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan,bintang from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' and iddetil=\'%s\'';
										$fsql = sprintf($sql, $kodekeg, $data->kodero, db_escape_string($datadetil->iddetil));
										
										//drupal_set_message($fsql);
										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										$datadetilpenanggaran = 0;
										$resultdetilpen = db_query($fsql);
										if ($datadetilpen = db_fetch_object($resultdetilpen)) {
											$datadetilpenuraian = $datadetilpen->uraian;
											$datadetilpentotal = $datadetilpen->total;
											$datadetilbintang = $datadetilpen->bintang;
										}
										
										//bintang penetapan
										if ($datadetilbintang) {
											$statusdetil_pen = '<font color="Red">*</font>';

										} else {
											$statusdetil_pen = '';
											
										}
										
										
									} else {
										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';

										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										
									}	
									
									//PERBINTANGAN
									if ($alldetilbintang) {
										$statusdetil = '<font color="Red">*</font>';
										$allsubbintang = true;
										
									} else {
										if ($datadetil->bintang or $datadetilbintang) {
											$statusdetil = '<font color="Red">*</font>';
											$allsubbintang = true;

										} else {
											$statusdetil = '';
											$allsubbintang = false;
											
										}
									}										
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';

										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '-';
										
										if ($penrekening <> 0) {
										$unitjumlahpen = $datadetilpen->unitjumlah . ' ' . $datadetilpen->unitsatuan;
										$volumjumlahpen = $datadetilpen->volumjumlah . ' ' . $datadetilpen->volumsatuan;
										$hargasatuanpen = apbd_fn($datadetilpen->harga);
										//$bullet = '•';
										}
										
									}
									 
									
									$persen = apbd_hitungpersen($datadetilpentotal, $datadetil->total);
									if ($datadetil->uraian == $datadetilpenuraian) {
										//drupal_set_message('x');
										
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
			 												 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' => $datadetil->uraian . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
															 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

															 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 
															 array('data' => apbd_fn($datadetil->total - $datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 );
															 
										if ($datadetil->pengelompokan) {
											//SUB DETIL
											$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
											$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
											//////////drupal_set_message($fsql);
											
											//$no = 0;
											$resultsub = db_query($fsql);
											if ($resultsub) {
												while ($datasub = db_fetch_object($resultsub)) {
													//$no += 1;

													$datasuburaian_pen = '';
													$datasubunitjumlah_pen = '';
													$datasubvolumjumlah_pen = '';
													$datasubharga_pen = '';
													$datasubtotal_pen = 0; 

													$uraian_rev = str_replace("*","",$datasub->uraian);

													if (($penrekening > 0) and ($datadetil->uraian == $datadetilpenuraian)) {
														
												
														$statusdetilsub_pen = '';
														$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsub} where iddetil=\'%s\' and idsub=\'%s\'';
														$fsql = sprintf($sql, db_escape_string($datadetil->iddetil), db_escape_string($datasub->idsub));
														$resultsubpen = db_query($fsql);
														if ($resultsubpen) {
															if ($datasubpen = db_fetch_object($resultsubpen)) {
																$datasuburaian_pen = $datasubpen->uraian;
																$datasubunitjumlah_pen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
																$datasubvolumjumlah_pen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
																$datasubharga_pen = $datasubpen->harga;
																$datasubtotal_pen = $datasubpen->total;

															if ($datasubpen->bintang) 
																$statusdetilsub_pen = '<font color="Red">*</font>';
															else
																$statusdetilsub_pen = '';
																
															
															} 
															  
														}  
														     
														  
														//recek
														if (($datasuburaian_pen=='') and ($datasubtotal_pen == $datasub->total)) $datasuburaian_pen = $uraian_rev;
														
													} 
													
													//PERBINTANGAN
													if ($datasub->bintang) 
														$statusdetilsub = '<font color="Red">*</font>';
													else
														$statusdetilsub = '';
													
													if ($statusdetil != '') $statusdetilsub = $statusdetil;
														
													if ($datasuburaian_pen == $uraian_rev) {
														$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
													
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );	
													
													} else if (trim($datasuburaian_pen) == trim($datasub->uraian)) {
														$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
													
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );															
														
													} else {
														
														if ($datasuburaian_pen == $datasub->uraian) {
															$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
														
															$rowsrek[] = array (
																	 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																	 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																	 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );																
															
														} else {
															
															if (trim($datasuburaian_pen) == trim($datasub->uraian)) {
																$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
																
																	$rowsrek[] = array (
																			 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																			 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																			 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 													 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 );																		
															} else { 
																
																if (($datasuburaian_pen !='') and ($datasubtotal_pen>0)) {

																	$rowsrek[] = array (
																			 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																			 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																			 array('data' =>  '- ' . $datasuburaian_pen . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => apbd_fn(-$datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 );	
																}  
																
																if ($datasub->bintang) $statusdetilsub = '<font color="Red">*</font>';
														
																$rowsrek[] = array (
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																		 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																		 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		);	
																
															}
														}		 
													}
													
													//$$$
												}
											}
											
											//###
										}
										
									
									} else {
										
										//***
										if (($datadetilpenuraian) !='' and ($datadetilpentotal<>0)) {
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetilpenuraian . $statusdetil_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn(-$datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
											//####
											if ($datadetil->pengelompokan) {

											
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												
													
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														$statusdetilsub_pen = '';
														//if ($datasub->total==$datasub->anggaran)
														//	$statusdetilsub_pen = '';
														//else
														//	$statusdetilsub_pen = '<font color="Red">*</font>';
														
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
													}
												}
											}
											//###
											
										} 
										
										if (($datadetil->uraian) !='' and ($datadetil->total<>0)) {
											if ($datadetil->bintang)
												$statusdetilsub = '<font color="Red">*</font>';
											else
												$statusdetilsub = '';
											
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetil->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn($datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
											//####
											if ($datadetil->pengelompokan) {

											
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												
													
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														if ($datasub->bintang>0) 
															$statusdetilsub_pen = '<font color="Red">*</font>';
														else
															$statusdetilsub_pen = '';
														
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' =>  apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
													}
												}
											}															
															
										}
															 
									}

								}
							}												
						
						///////					 
						}
					}								 
										 
				////////
				}
			}			

		}
	}
	
	$persen = apbd_hitungpersen($totalpen, $total);
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '290px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn($totalpen),  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total),  'width'=> '240px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 array('data' => apbd_fn($total-$totalpen),  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}


function GenReportFormFooter($kodekeg, $tipedok,$revisi) {
	
	if ($revisi=='9')
	{
		$revisi ='';$str_table='';
	}
	else
	{
		$revisi = $revisi;
		$str_table = $revisi;
	}
	
	if ($tipedok=='dpa') {
		
		$str_dpa = '';
		if (!isSuperuser()) $str_dpa = '<p style="color:red"><em>Untuk keperluan review SKPD</em></p>';
			
		
		$pquery = sprintf("select tw1p, tw2p, tw3p, tw4p from {kegiatanperubahan} where kodekeg='%s'", db_escape_string($kodekeg)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$tw1 = $data->tw1p;
			$tw2 = $data->tw2p;
			$tw3 = $data->tw3p;
			$tw4 = $data->tw4p;
			
		}

		$pquery = sprintf("select dpatgl".$revisi." dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		//drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}
		
		//SUB REVISI
		$pquery = sprintf("select kodekeg, tanggal from {dpanomorkeg1} where kodekeg='%s'" . $where, db_escape_string($kodekeg));
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$dpatgl = $data->tanggal;
			
		} 		
		
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );

		$rowsfooter[] = array (
							 array('data' => 'RENCANA BELANJA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => 'Jumlah (Rp)',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => 'Keterangan',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black;  border-right: 1px solid black;text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'text-align:center;'),
							 ); 
		$rowsfooter[] = array (
							 array('data' => 'I',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right'),
							 array('data' => $strreview,  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH',  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'II',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right'),
							 array('data' => $str_dpa,  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => $str_dpa,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'III',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black;text-align:right'),
							 array('data' => $str_dpa,  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => $str_dpa,  'width' => '300px', 'style' => 'text-align:center;'),
							 );	
		$rowsfooter[] = array (
							 array('data' => 'IV',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		 $rowsfooter[] = array (
			 array('data' => '',  'width'=> '100px', 'style' => 'text-align:center'),
			 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
			 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
			 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
			 array('data' => $budnama,  'width' => '300px', 'style' => 'text-align:center; text-decoration: underline;'),
		 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='';
		$pquery = sprintf("select u.kodedinas, u.namauk, u.pimpinannama, u.pimpinannip, u.pimpinanjabatan, k.plafon, k.total from {unitkerja} u left join {kegiatanperubahan".$revisi."} k 
				  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
			$plafon = $data->plafon;
			$total = $data->total;
			
		}
		//if ($total > $plafon)	
		//	$strplafon = '!!!ANGGARAN MELEBIHI PLAFON, HARAP DIPERBAIKI!!!';

		$pquery = sprintf("select count(kodero) jmlrek from {anggperkegperubahan".$revisi."} where (jumlah mod 1000)>0 and  kodekeg='%s'", db_escape_string($kodekeg));
		$pres = db_query($pquery);
		////////drupal_set_message($pquery); 
		if ($data = db_fetch_object($pres)) {
			if ($data->jmlrek > 0)	$str1000 = '!!!ADA SEJUMLAH REKENING YANG TIDAK BULAT PER 1000, HARAP DIPERBAIKI!!!';
		}
		

		
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $str1000,  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $strplafon,  'width'=> '675px',  'colspan'=>'12',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
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

function kegiatanskpd_printdpap_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Dokumen Revisi dan Setting Printer',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,        
	);
	
	$kodekeg = arg(3);
	$topmargin = arg(4);
	$tipedok = arg(5);

	$revisi = arg(6);
	//$sampul = arg(7);
	//$exportpdf = arg(8);
	
	
	if (!isset($topmargin)) $topmargin=10;
	if (!isset($revisi)) $revisi='9';

	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodekeg, 
	);
	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipedok, 
	); 
	$form['formdata']['revisi']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $revisi, 
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
	
	if ($tipedok=='dpa') {
		$form['formdata']['submitpreview'] = array (
			'#type' => 'submit',		
			'#value' => 'Preview',
		);		
		$form['formdata']['submitdpa'] = array (
			'#type' => 'submit',		
			'#value' => 'DPPA-SKPD',
		);		
		$form['formdata']['submitdpasampul'] = array (
			'#type' => 'submit',
			'#value' => 'Sampul DPPA'
		);		
	} else {
		$form['formdata']['submitdpa'] = array (
			'#type' => 'submit',		
			'#value' => 'RPKA-SKPD',
		);		
		$form['formdata']['submitdpasampul'] = array (
			'#type' => 'submit',
			'#value' => 'Sampul RPKA'
		);		
	}
	
	//form multi
	$form['formmulti'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Untuk mencetak lembat DPPA yang banyak',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,        
	);
	

	$form['formmulti']['offset']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Offset', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled,  
		'#disabled'     => false, 
		'#default_value'=> '0', 
	); 
	
	$form['formmulti']['submitprepare'] = array (
		'#type' => 'submit',		
		'#value' => 'Prepare',
	);		
	$form['formmulti']['submitdpa1'] = array (
		'#type' => 'submit',		
		'#value' => 'Bagian #1',
	);		
	$form['formmulti']['submitdpa2'] = array (
		'#type' => 'submit',
		'#value' => 'Bagian #2'
	);		

	return $form;
}

 
function kegiatanskpd_printdpap_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tipedok = $form_state['values']['tipedok'];
	$kodekeg = $form_state['values']['kodekeg'];
	$topmargin = $form_state['values']['topmargin'];
	$revisi = $form_state['values']['revisi'];
	$offset = $form_state['values']['offset'];

	//$revisi = arg(6);
	//$sampul = arg(7);
	//$exportpdf = arg(8);


	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['submitdpasampul']) 
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/10/dpa/'.$revisi.'/pdf/sampuldppa';
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitpreview']) 
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/10/dpa/'.$revisi.'/pdf/preview';
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitprepare']) 
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/10/dpa/'.$revisi.'/resume/' . $offset;
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitdpa1']) 
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/10/dpa/'.$revisi.'/pdf1';
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitdpa2']) 
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/10/dpa/'.$revisi.'/pdf2';
	else
		$uri = 'apbd/kegiatanskpd/printperubahan/' . $kodekeg . '/'. $topmargin . '/' . $tipedok . '/' . $revisi . '/pdf' ;

	drupal_goto($uri);
	
}

function GenReportFormSampulBelanja($kodekeg, $revisi) {
	//$revisi='2';

	$kodekeg_id = $kodekeg;
	
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.lokasi, k.jenis, k.total,k.totalp, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, 
				uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip, d.dpano, d.dpatgl 
				from {kegiatanperubahan} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on (p.kodeu=u.kodeu) left join {unitkerja} uk on (k.kodeuk=uk.kodeuk) left join {kegiatandpa} d on (k.kodekeg=d.kodekeg)' . $where, db_escape_string($kodekeg));
	////////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodeuk = $data->kodeuk;
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3) . ' - ' .  $data->kegiatan;
		
		$kodekeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$total = $data->total;
		$totalp = $data->totalp;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;

		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
		
		$jenis = $data->jenis;
		if ($data->jenis==1) $strjenis = '  -  T I D A K';
	}
	

	
	if ($jenis==1)
		$pquery = sprintf('select btlno dpano from {dpanomor'.$revisi.'} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	else
		$pquery = sprintf('select blno dpano from {dpanomor'.$revisi.'} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	////////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	
	
	if ($dpano !='') {
		/* $tahun = variable_get('apbdtahun', 0);
		//$tahun=2017;
		if ($jenis==1)
			$pquery = sprintf('select dpabtlformat'.$revisi.' dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
		else
			$pquery = sprintf('select dpablformat'.$revisi.' dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
			
		////////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($pres) {
			if ($data = db_fetch_object($pres)) {
				$dpaformat = $data->dpaformat;
			}
		}
		
		$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
		$dpanolengkap = str_replace('NOKEG',$kodekeg,$dpanolengkap); */
	$kodekeg = arg(3);
	$where = ' where kodekeg=\'%s\'';
	$pquery = sprintf('select dpano from {kegiatandpa}' . $where, db_escape_string($kodekeg));
	////////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpanolengkap = $data->dpano;
	}
		
		/* $kodekeg = arg(3);
		$res = db_query("select dpano from kegiatandpa where kodekeg = :kodekeg", array(':kodekeg' => $kodekeg));
		if ($data = db_fetch_object($res)){
			$dpanolengkap = 'test'.$data->dpano;
		} */
		
	} else 
		$dpanolengkap = '........................';
		

	
	$rows[] = array (array ('data'=>'', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'KABUPATEN JEPARA', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPPA-SKPD)', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'B E L A N J A' . $strjenis . '  -  L A N G S U N G', 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'NO. DPPA-SKPD : ' . $dpanolengkap, 'width'=>'755px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array ( 
				array('data' => '',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'URUSAN PEMERINTAHAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $urusan, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'ORGANISASI',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $organisasi, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'PROGRAM',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($program), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'KEGIATAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($kegiatan), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	if ($jenis==2)
		$rows[]= array (
					array('data' => 'LOKASI',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
					array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
					array('data' => strtoupper($lokasi), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
				);
	$rows[]= array (
				array('data' => 'SUMBER DANA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $sumberdana1, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
			);
	
	/*
	$rows[]= array (
				array('data' => 'ANGGARAN PENETAPAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- Jumlah',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ', 'width' => '45px', 'style' => 'border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => apbd_fn($total) . ',00', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '540px', 'colspan'=>'3',  'style' => 'border:none;  font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- Terbilang',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($total)), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	
	
	$rows[]= array (
				array('data' => 'ANGGARAN PERUBAHAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- Jumlah',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ', 'width' => '45px', 'style' => 'border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => apbd_fn($totalp) . ',00', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '540px', 'colspan'=>'3',  'style' => 'border:none;  font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- Terbilang',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($totalp)), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	*/
			
	$rows[]= array (
				array('data' => 'JUMLAH ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ' . apbd_fn($totalp) . ',00', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'TERBILANG',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($totalp)), 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);			
	$rows[]= array (
				array('data' => 'PENGGUNA ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
				array('data' => '', 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NAMA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannama, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NIP',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannip, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- JABATAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinanjabatan, 'width' => '585px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}

function GenReportFormContentDPAResume($kodekeg, $bagian) {


	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$total = 0;
	$totalpen = 0;


	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
						 
						 array('data' => 'URAIAN',  'width' => '230x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;font-size:small;'),
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
						 
	 //JENIS
	$sql = 'select tingkat, kodekeg, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp, pengelompokan from kegiatandpaprint where kodekeg=\'%s\' and bagian=\'%s\' order by id';
	$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($bagian));	
	//drupal_set_message($fsql);
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
		
			
			$persen = apbd_hitungpersen($data->jumlah, $data->jumlahp);
			if ($data->tingkat=='3') {
				$rowsrek[] = array (
							 array('data' => $data->kode,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => $data->uraian,  'width' => '230x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
							 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
							 );
				$total += $data->jumlahp;
				$totalpen += $data->jumlah;
			
			} elseif ($data->tingkat=='4') {
				$rowsrek[] = array (
							 array('data' => apbd_format_rek_obyek($data->kode),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => strtoupper($data->uraian),  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
							 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

							 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),
							 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

					 );	
						
			} elseif ($data->tingkat=='5') {				 
				$rowsrek[] = array (
						 array('data' => apbd_format_rek_rincianobyek($data->kode),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
						 array('data' => $data->uraian,  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
						 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),

						 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black;  text-align:right;font-size:small;font-weight:bold;'),
						 
					);
					
			} elseif ($data->tingkat=='6') {	
					$bullet = ($data->pengelompokan=='0'? '-':'#');
					$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
						 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
						 array('data' => $data->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
						 array('data' => $data->satuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
						 array('data' => $data->volume, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
						 array('data' => $data->harga,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
						 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

						 array('data' => $data->satuanp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
						 array('data' => $data->volumep, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
						 array('data' => $data->hargap,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
						 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
						 
						 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
						 );
						 
			} elseif ($data->tingkat=='7') {	
					$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
						 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
						 array('data' =>  '- ' . $data->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => $data->satuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => $data->volume, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => apbd_fn($data->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

						 array('data' => $data->satuanp, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => $data->volumep, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => apbd_fn($data->hargap),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => apbd_fn($data->jumlahp),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

						 array('data' => apbd_fn($data->hargap - $data->harga),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
						 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
						 );	
			}									
		}
	}
	
	if ($bagian=='1') {
					$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;border-bottom: 1px solid black;'),
						 array('data' =>  '',  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' =>'', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),

						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),

						 array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 array('data' => '',  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;border-bottom: 1px solid black;'),
						 );	
	} else {

		$sql = 'select sum(jumlah) penetapan, sum(jumlahp) perubahan from kegiatandpaprint where kodekeg=\'%s\' and tingkat=\'%s\'';
		$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string('3'));	
		//drupal_set_message($fsql);
		$result = db_query($fsql);
		if ($result) {
			while ($data = db_fetch_object($result)) {
				$totalpen = $data->penetapan;
				$total = $data->perubahan;
			}
		}

		
		$persen = apbd_hitungpersen($totalpen, $total);
		$rowsrek[] = array (
							 array('data' => 'JUMLAH BELANJA',  'width'=> '290px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
							 array('data' => apbd_fn($totalpen),  'width' => '240px', 'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

							 array('data' => apbd_fn($total),  'width'=> '240px',  'colspan'=>'4',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

							 array('data' => apbd_fn($total-$totalpen),  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),
							 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-size:small; font-weight:bold;'),

							 );	}		
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}


function resume_dpa($kodekeg, $revisi, $offset) {

	if ($revisi==variable_get('apbdrevisi', 1)) $revisi = '';
	if ($revisi=='9') $revisi = '';

	$bintangkegiatan = false;
	$sql = 'select bintang from {kegiatanperubahan} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$bintangkegiatan = $data->bintang;
		}
	}
	 
	
	set_time_limit(0);
	ini_set('memory_limit', '512M');
	
	$batas = 170+$offset;
	$bagian = 1; $row = 0;

	//drupal_set_message('batas : ' . $batas);
	
	
	$sql = sprintf('delete from kegiatandpaprint where kodekeg=\'%s\'', db_escape_string($kodekeg));
	//drupal_set_message($sql);
	$res = db_query($sql);

	
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
		
			$row++;
			if (  $row>$batas) $bagian = 2;
			
			$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			'3', $kodekeg, $bagian, $datajenis->kodej, $datajenis->uraian, NULL, NULL, NULL, $datajenis->jumlahx, NULL, NULL, NULL, $datajenis->jumlahxp);
			$res = db_query($sql);
		
		
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan'.$revisi.'} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$row++;
					if ($row>$batas) $bagian = 2;

					$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					'4', $kodekeg, $bagian, $dataobyek->kodeo, $dataobyek->uraian, NULL, NULL, NULL, $dataobyek->jumlahx, NULL, NULL, NULL, $dataobyek->jumlahxp);
					$res = db_query($sql);
				
								 
					//REKENING
					$sql = 'select kodero,uraian,jumlah,jumlahp,bintang from {anggperkegperubahan'.$revisi.'} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						
						//BINTANG
						$alldetilbintang = false;
						if ($bintangkegiatan) {
							$statusrek = '<font color="Red">*</font>';
							$alldetilbintang = true;
								
						} else {
							if ($data->bintang==1) {
								
								$statusrek = '<font color="Red">*</font>';
								$alldetilbintang = true;

							} else {
								
								$statusrek = '';

							}
						}							
 
						$penrekening = $data->jumlah;						

						$row++;
						if ($row>$batas) $bagian = 2;

						$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						'5', $kodekeg, $bagian, $data->kodero, $data->uraian, NULL, NULL, NULL, $data->jumlah, NULL, NULL, NULL, $data->jumlahp);
						$res = db_query($sql);
						
										
							//DETIL
							$sql = 'select distinct iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan, bintang from {anggperkegdetilperubahan'.$revisi.'} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));							
							$resultdetil = db_query($fsql);
							
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									$statusdetil = '';
									
									if ($penrekening > 0) {
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan,bintang from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' and iddetil=\'%s\'';
										$fsql = sprintf($sql, $kodekeg, $data->kodero, db_escape_string($datadetil->iddetil));
										
										//drupal_set_message($fsql);
										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										$datadetilpenanggaran = 0;
										$resultdetilpen = db_query($fsql);
										if ($datadetilpen = db_fetch_object($resultdetilpen)) {
											$datadetilpenuraian = $datadetilpen->uraian;
											$datadetilpentotal = $datadetilpen->total;
											$datadetilbintang = $datadetilpen->bintang;
										}
										
										//bintang penetapan
										if ($datadetilbintang) {
											$statusdetil_pen = '<font color="Red">*</font>';

										} else {
											$statusdetil_pen = '';
											
										}
										
										
									} else {
										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';

										$datadetilpenuraian = '';
										$datadetilpentotal = 0;
										
									}	
									
									//PERBINTANGAN
									if ($alldetilbintang) {
										$statusdetil = '<font color="Red">*</font>';
										$allsubbintang = true;
										
									} else {
										if ($datadetil->bintang or $datadetilbintang) {
											$statusdetil = '<font color="Red">*</font>';
											$allsubbintang = true;

										} else {
											$statusdetil = '';
											$allsubbintang = false;
											
										}
									}										
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';

										$unitjumlahpen = '';
										$volumjumlahpen = '';
										$hargasatuanpen = '';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '-';
										
										if ($penrekening <> 0) {
										$unitjumlahpen = $datadetilpen->unitjumlah . ' ' . $datadetilpen->unitsatuan;
										$volumjumlahpen = $datadetilpen->volumjumlah . ' ' . $datadetilpen->volumsatuan;
										$hargasatuanpen = apbd_fn($datadetilpen->harga);
										//$bullet = '•';
										}
										
									}
									 
									
									if ($datadetil->uraian == $datadetilpenuraian) {
										
										$row++;
										if ($row>$batas) $bagian = 2;

										$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp, pengelompokan) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
										'6', $kodekeg, $bagian, $datadetil->iddetil, $datadetil->uraian . $statusdetil, $unitjumlahpen, $volumjumlahpen, $hargasatuanpen, $datadetilpentotal, $unitjumlah, $volumjumlah, $hargasatuan, $datadetil->total, $datadetil->pengelompokan);
										$res = db_query($sql);
										
										/*
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
			 												 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' => $datadetil->uraian . $statusdetil,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
															 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

															 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
															 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 
															 array('data' => apbd_fn($datadetil->total - $datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
															 );
											*/
															 
										if ($datadetil->pengelompokan) {
											//SUB DETIL
											$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
											$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
											
											//$no = 0;
											$resultsub = db_query($fsql);
											if ($resultsub) {
												while ($datasub = db_fetch_object($resultsub)) {
													//$no += 1;

													$datasuburaian_pen = '';
													$datasubunitjumlah_pen = '';
													$datasubvolumjumlah_pen = '';
													$datasubharga_pen = '';
													$datasubtotal_pen = 0; 

													$uraian_rev = str_replace("*","",$datasub->uraian);

													if (($penrekening > 0) and ($datadetil->uraian == $datadetilpenuraian)) {
														
												
														$statusdetilsub_pen = '';
														$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsub} where iddetil=\'%s\' and idsub=\'%s\'';
														$fsql = sprintf($sql, db_escape_string($datadetil->iddetil), db_escape_string($datasub->idsub));
														$resultsubpen = db_query($fsql);
														if ($resultsubpen) {
															if ($datasubpen = db_fetch_object($resultsubpen)) {
																$datasuburaian_pen = $datasubpen->uraian;
																$datasubunitjumlah_pen = $datasubpen->unitjumlah . ' ' . $datasubpen->unitsatuan;
																$datasubvolumjumlah_pen = $datasubpen->volumjumlah . ' ' . $datasubpen->volumsatuan;
																$datasubharga_pen = $datasubpen->harga;
																$datasubtotal_pen = $datasubpen->total;

															if ($datasubpen->bintang) 
																$statusdetilsub_pen = '<font color="Red">*</font>';
															else
																$statusdetilsub_pen = '';
																
															
															} 
															  
														}  
														     
														  
														//recek
														if (($datasuburaian_pen=='') and ($datasubtotal_pen == $datasub->total)) $datasuburaian_pen = $uraian_rev;
														
													} 
													
													//PERBINTANGAN
													if ($datasub->bintang) 
														$statusdetilsub = '<font color="Red">*</font>';
													else
														$statusdetilsub = '';
													
													if ($statusdetil != '') $statusdetilsub = $statusdetil;
														
													if ($datasuburaian_pen == $uraian_rev) {
													
														$row++;
														if ($row>$batas) $bagian = 2;

														$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
														'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, $datasubunitjumlah_pen, $datasubvolumjumlah_pen, $datasubharga_pen, $datasubtotal_pen, $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
														$res = db_query($sql);
														
														/*
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );	
														*/
													} else if (trim($datasuburaian_pen) == trim($datasub->uraian)) {


														$row++;
														if ($row>$batas) $bagian = 2;
													
														$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
														'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, $datasubunitjumlah_pen, $datasubvolumjumlah_pen, $datasubharga_pen, $datasubtotal_pen, $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
														$res = db_query($sql);
														
														/*
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 );															
														*/
														
													} else {
														
														if ($datasuburaian_pen == $datasub->uraian) {

															$row++;
															if ($row>$batas) $bagian = 2;
														
															$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
															'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, $datasubunitjumlah_pen, $datasubvolumjumlah_pen, $datasubharga_pen, $datasubtotal_pen, $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
															$res = db_query($sql);

															/*
															$rowsrek[] = array (
																	 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																	 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																	 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																	 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																	 );	
															*/					
															
														} else {
															
															if (trim($datasuburaian_pen) == trim($datasub->uraian)) {

																$row++;
																if ($row>$batas) $bagian = 2;

															
																$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
																'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, $datasubunitjumlah_pen, $datasubvolumjumlah_pen, $datasubharga_pen, $datasubtotal_pen, $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
																$res = db_query($sql);
																	/*
																	$rowsrek[] = array (
																			 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																			 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																			 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
						 													 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => apbd_fn($datasub->total - $datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 );	
																*/
															} else { 
																
																if (($datasuburaian_pen !='') and ($datasubtotal_pen>0)) {

																	$row++;
																	if ($row>$batas) $bagian = 2;

																	$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
																	'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, $datasubunitjumlah_pen, $datasubvolumjumlah_pen, $datasubharga_pen, $datasubtotal_pen, '0', '0', '0', '0');
																	$res = db_query($sql);
																	
																	/*
																	$rowsrek[] = array (
																			 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																			 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																			 array('data' =>  '- ' . $datasuburaian_pen . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubunitjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => $datasubvolumjumlah_pen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubharga_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn($datasubtotal_pen),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																			 array('data' => apbd_fn(-$datasubtotal_pen),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																			 );	
																	*/
																}  
																
																if ($datasub->bintang) $statusdetilsub = '<font color="Red">*</font>';

																	$row++;
																	if ($row>$batas) $bagian = 2;

																
																	$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
																	'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub, '0', '0', '0', '0', $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
																	$res = db_query($sql);
																	/*		
																	$rowsrek[] = array (
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																		 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																		 array('data' =>  '- ' . $datasub->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																		 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																		);	
																	*/
																	
															}
														}		 
													}
													
													//$$$
												}
											}
											
											//###
										}
										
									
									} else {
										
										//***
										if (($datadetilpenuraian) !='' and ($datadetilpentotal<>0)) {

											$row++;
											if ($row>$batas) $bagian = 2;

										
											$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp, pengelompokan) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
											'6', $kodekeg, $bagian, $datadetil->iddetil, $datadetilpenuraian . $statusdetil_pen, $unitjumlahpen, $volumjumlahpen, $hargasatuanpen, $datadetilpentotal, '0', '0', '0', '0', $datadetil->pengelompokan);
											$res = db_query($sql);						
											
											/*
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetilpenuraian . $statusdetil_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => $unitjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlahpen, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuanpen,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetilpentotal),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn(-$datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
												*/
											//####
											if ($datadetil->pengelompokan) {

											
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												
													
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														$statusdetilsub_pen = '';

														$row++;
														if ($row>$batas) $bagian = 2;

														
														$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
														'7', $kodekeg, $bagian, $datasub->idsub, $datasub->uraian . $statusdetilsub_pen, $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total, '0', '0', '0', '0');
														$res = db_query($sql);
														/*
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn(-$datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
														*/
													}
												}
											}
											//###
											
										} 
										
										if (($datadetil->uraian) !='' and ($datadetil->total<>0)) {
											if ($datadetil->bintang)
												$statusdetilsub = '<font color="Red">*</font>';
											else
												$statusdetilsub = '';

											$row++;
											if ($row>$batas) $bagian = 2;

											$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp, pengelompokan) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
											'6', $kodekeg, $bagian, $datadetil->iddetil, $datadetil->uraian . $statusdetilsub, '0', '0', '0', '0', $unitjumlah, $volumjumlah, $hargasatuan, $datadetil->total, $datadetil->pengelompokan);
											$res = db_query($sql);
											
											/*											
											$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => $datadetil->uraian . $statusdetilsub,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),

																 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;'),
																 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn($datadetil->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
												*/
											//####
											if ($datadetil->pengelompokan) {

											
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,bintang from {anggperkegdetilsubperubahan'.$revisi.'} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												
													
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														
														if ($datasub->bintang>0) 
															$statusdetilsub_pen = '<font color="Red">*</font>';
														else
															$statusdetilsub_pen = '';
														
														$row++;
														if ($row>$batas) $bagian = 2;

														$sql = sprintf("insert into {kegiatandpaprint} (tingkat, kodekeg, bagian, kode, uraian, satuan, volume, harga, jumlah, satuanp, volumep, hargap, jumlahp) values('%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
														'7', $kodekeg, $bagian, $datadetil->iddetil, $datasub->uraian . $statusdetilsub_pen, '0', '0', '0', '0', $datasub->unitjumlah . ' ' . $datasub->unitsatuan, $datasub->volumjumlah . ' ' . $datasub->volumsatuan, $datasub->harga, $datasub->total);
														$res = db_query($sql);
														
														/*
														$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
																 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
																 array('data' => '- ' . $datasub->uraian . $statusdetilsub_pen,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => '0',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' =>  apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),

																 array('data' => apbd_fn($datasub->total),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn1(100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;font-style: italic;'),
																);	
														*/
													}
												}
											}															
															
										}
															 
									}

								}
							}												
						
						///////					 
						}
					}					 			 
										 
				////////
				}
			}			

		}
	}
	
	

	
	return '0';
	
}
 



?>