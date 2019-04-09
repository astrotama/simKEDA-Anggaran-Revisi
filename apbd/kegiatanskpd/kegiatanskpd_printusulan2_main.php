<?php
function kegiatanskpd_printusulan2_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;
	$topmargin = '20';
	$id = arg(3);
	$exportpdf = arg(6);
	$sampul = arg(7);
	$tipedok = arg(5);  //'dpa' / 'rka'
	$hal1 = '9999';

	if (isset($topmargin)) $topmargin = arg(4);

	////drupal_set_message($kodekeg);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		if (isset($sampul))  {

			if ($sampul=='sp') {
				if ($tipedok=='rpka') {
					$pdfFile = 'surat-permohonan-perubahan-' . $id . '.pdf';
					$htmlContent = PrintSuratPermohonanPerubahan($id);
				} else {
					$pdfFile = 'surat-permohonan-revisi-' . $id . '.pdf';
					$htmlContent = PrintSuratPermohonan($id);
				}	
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, true, 'P');
				
			} else if ($sampul=='mp') {
				
				if ($tipedok=='rpka') {
					$pdfFile = 'matriks-permohonan-perubahan-' . $id . '.pdf';
					$htmlContent = PrintMatriksPermohonanPerubahan($id);
					apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, false, 'P');
					
				} else {
					$sql = 'select id,detiluraian,rab,jenisrevisi from {kegiatanrevisiperubahan} where id=\'%s\'';
					$res = db_query(db_rewrite_sql($sql), array ($id));
					$isL =false;
					if ($data = db_fetch_object($res)) {
						if (($data->detiluraian =='1') or ($data->rab=='1'))
							$isL = true;	
					}
				
					$pdfFile = 'matriks-permohonan-revisi-' . $id . '.pdf';
					if ($isL) {
						$htmlContent = PrintMatriksPermohonanL($id);
						apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, false, 'L');
					} else {
						$htmlContent = PrintMatriksPermohonan($id);
						apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, false, 'P');
					}
				}
				
				//PrintMatriksSPTJM
			} else if ($sampul=='sptjm') {
				$pdfFile = 'sptjm-permohonan-revisi-' . $id . '.pdf';
				$htmlContent = PrintMatriksSPTJM($id);
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, true, 'P');
 
			} else if ($sampul=='apv') {
				$pdfFile = 'persetujuan-revisi-' . $id . '.pdf';
				$htmlContent = PrintPersetujuan($id);
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, '', $htmlContent, '', $pdfFile, $hal1, true, 'P');
				//return $htmlContent;

			} else if ($sampul=='sampuldppa') {
				$pdfFile = 'sampul-dppa-' . $id . '.pdf';
				$htmlContent = GenReportFormSampulBelanja($id, $revisi);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
				//return $htmlContent;
				
			} else if ($sampul=='sampuld') {
				$pdfFile = 'dppa-skpd-sampul.pdf';
				$htmlContent = GenReportFormSampulDepan($id);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
				//return 'Hello';
			}
			
		} else {
			//require_once('test.php');
			//myt();
			
			if ($tipedok=='rka') {
				$sql = 'select kodekeg from {kegiatanrevisiperubahan} where id=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array ($id));
				$isL =false;
				if ($data = db_fetch_object($res)) {
					$kodekeg = $data->kodekeg;	
				}
				$pdfFile = 'rka-skpd-revisi-' . $kodekeg . '.pdf';

			} else if ($tipedok=='rpka') {
				$kodekeg = $id;	
				$pdfFile = 'rpka-skpd-perubahan-' . $kodekeg . '.pdf';
				
			} else {
				$kodekeg = $id;	
				$pdfFile = 'dppa-skpd-' . $kodekeg . '.pdf';
			}
			
 
			//$htmlContent = GenReportForm(1);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok);
			$htmlContent = GenReportFormContent($kodekeg);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok);
			
			//$output = drupal_get_form('kegiatanskpd_printusulan2_form');
			//$output .= $htmlContent;
			//return $output;
			$sql = 'SELECT * FROM {kegiatanverifikasi} where kodekeg=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$link=array();$ind=0;
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$username	=$dataobyek->username;
					$valid		=$dataobyek->persetujuan;
					if($valid=1){
						$sql = 'SELECT * FROM {apbdop} where username=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($username));
						
						$result = db_query($fsql);
						if ($result) {
							while ($data = db_fetch_object($result)) {
								
								$link[$ind]=$data->ttd;
								$ind++;
							}
						}
					}
				}
			}
			$_SESSION["link_ttd1"] = $link[0];
			$_SESSION["link_ttd2"] = $link[1];
			$_SESSION["link_ttd3"] = $link[2];
			$_SESSION["link_ttd4"] = $link[3];
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'cekl');
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
		}
		
	} else {
		
		$output = drupal_get_form('kegiatanskpd_printusulan2_form');
		$output .= getDescription($id, $tipedok);
		return $output;
	}
	
}

function GenReportFormSampulDepan($kodeuk) {
	$where = ' where kodeuk=\'%s\'';
	$pquery = sprintf('select kodedinas, namauk from {unitkerja} ' . $where, db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
	}
	$rows[] = array (array ('data'=>'', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPPA-SKPD)', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=> $organisasi, 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array (
				array('data' => '',  'width'=> '190px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '650px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'KODE',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'NAMA FORMULIR', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Ringkasan Dokumen Pelaksanaan Anggaran Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Pendapatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.2',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rekapitulasi Belanja Langsung menurut Program dan Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'DPPA - SKPD 2.2.1',  'width'=> '190px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Langsung Program dan Per Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);


	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}

function getDescription($id, $tipedok){
	if ($tipedok=='rka') {
		
		drupal_set_title('Cetak Usulan Revisi RKA-SKPD');
		
		$sql = 'select kr.kegiatan from {kegiatanrevisiperubahan} kp inner join {kegiatanrevisi} kr on kp.kodekeg=kr.kodekeg where kp.id=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($data = db_fetch_object($res)) {
			$kegiatan = strtoupper($data->kegiatan);	
		}
		
		$rows[]= array (
					array('data' => '- Kegiatan : ' . $kegiatan, 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak RKA Revisi, klik tombol RKA Revisi', 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak Surat Permohonan Revisi, klik tombol Permohonan', 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak Matrik Revisi, klik tombol Matrik', 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak SPTJM, klik tombol SPTJM', 'style' => 'border-none; text-align:left;'),
				 );							 

		 if (isSuperuser()) {
			$kodeuk = '81';	
		} else
			$kodeuk = apbd_getuseruk();
		
		$linkskpd .= l('Kop SKPD', 'apbd/unitkerja/edit/' . $kodeuk );
		$rows[]= array (
					array('data' => '- Untuk mengganti alamat kantor pada kop surat, klik link ' . $linkskpd,    'style' => 'border-none; text-align:left;'),
				 );							 
	} else {
		
		drupal_set_title('Cetak Usulan Perubahan RKA-SKPD');
		
		$sql = 'select kegiatan from {kegiatanrevisi} where kodekeg=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($data = db_fetch_object($res)) {
			$kegiatan = strtoupper($data->kegiatan);	
		}
		
		$rows[]= array (
					array('data' => '- Kegiatan : ' . $kegiatan, 'style' => 'border-none; text-align:left;'),
				 );							 
		$rows[]= array (
					array('data' => '- Untuk mencetak RPKA-SKPD, klik tombol RPKA-SKPD', 'style' => 'border-none; text-align:left;'),
				 );							 
	}
	$rows[]= array (
				array('data' => '- Bila hasil cetakan tidak sesuai, misalnya tanda tangan terpotong, tambahkan Margin Atas untuk menggesernya',    'style' => 'border-none; text-align:left;'),
			 );							 

	
	$headerkosong = array();
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttbl));
	
	return $output;
}

function GenReportForm($kodekeg, $tipedok) {
	
	
	/*
	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetilrevisi} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetilrevisi} d
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
	
	////drupal_set_message($dpa);

	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan from {unitkerja} u left join {kegiatanrevisi} k 
			  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$budnama = $data->budnama;
		$budnip = $data->budnip;
		$budjabatan = $data->budjabatan;
		$dpatgl = $data->dpatgl;
	}

	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanperubahan} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;		
	}

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanrevisi} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodeu . '.' . $data->kodepro . ' - ' . $data->program;
		//$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		
		$jenis = $data->jenis;
		$tahun = $data->tahun;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		//$masukantarget = $data->masukantarget;
		$masukantarget = 'Rp ' . apbd_fn($data->total);
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$plafon = $data->plafon;
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
	
	if ($tipedok=='dpa')
		$rowsjudul[] = array (array ('data'=>'DPA-SKPD - BELANJA ' . $strbelanja . 'LANGSUNG', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	
	else
		$rowsjudul[] = array (array ('data'=>'RKA-SKPD - BELANJA ' . $strbelanja . 'LANGSUNG', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	

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
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Lokasi',  'width'=> '150px', 'style' => ' text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $lokasi,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($total),  'width' => '160', 'colspan'=>'2',  'style' => 'text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
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
						 //array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '240x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//JENIS
	$total = 0;
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkegrevisi} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '240x', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkegrevisi} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '240x', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '240x', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );
							//DETIL
							$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,pengelompokan from {anggperkegdetilrevisi} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////drupal_set_message($fsql);
							
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
														 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '- ' . $datadetil->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $unitjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $volumjumlah, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $hargasatuan,  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '. ' . $datasub->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
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
						 array('data' => apbd_fn($total),  'width' => '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );


	if ($tipedok=='dpa') {						 
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );

		$rowsfooter[] = array (
							 array('data' => 'RENCANA BELANJA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
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
	} else {
		$pquery = sprintf("select count(kodero) jmlrek from {anggperkegrevisi} where (jumlah mod 1000)>0 and  kodekeg='%s'", db_escape_string($kodekeg));
		////drupal_set_message($pquery); 
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			if ($data->jmlrek > 0)	$str1000 = '!!!ADA SEJUMLAH REKENING YANG TIDAK BULAT PER 1000, HARAP DIPERBAIKI!!!';
		}
								 
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $str1000,  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
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

function GenReportFormHeader($kodekeg, $tipedok) {
	

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

	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanperubahan} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;		
	}
	
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, k.kegiatan, k.lokasi, k.jenis, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran, k.keluarantarget, 
				k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, uk.namauk from {kegiatanrevisi} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
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
		//$masukantarget = $data->masukantarget;
		$masukantarget = 'Rp ' . apbd_fn($data->total);
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$plafon = $data->plafon;
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
	if ($jenis==2)  
		$strjenis = 'B E L A N J A  -  L A N G S U N G';
	else  
		$strjenis = 'BELANJA TIDAK LANGSUNG';
	
	if ($tipedok=='dpa') {
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width' => '360px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '250px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
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
							 array('data' => 'RENCANA PERUBAHAN KERJA DAN ANGGARAN', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => $strjenis, 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RPKA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => $strjenis, 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RPKA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
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
							 array('data' => 'Rincian Perubahan Rencana Kerja dan Anggaran Belanja Langsung per Program dan Kegiatan Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'13',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
							 );							 
	else
		$rowskegiatan[]= array (
							 array('data' => 'Rincian Perubahan Rencana Kerja dan Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'13',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
							 );							 
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodekeg) {


	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetilrevisi} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetilrevisi} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	//if ($jumrincian > 350) {
		set_time_limit(0);
		ini_set('memory_limit', '640M');
	//}
	
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
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkegrevisi} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
		
			$sql = 'select sum(jumlah) jumlahx from {anggperkeg} where kodekeg=\'%s\' and mid(kodero,1,3)=\'%s\'';
			$fsqlpen = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$resultjenispen = db_query($fsqlpen);
			
			//drupal_set_message($fsqlpen);
			
			$penjenis = 0;
			if ($resultjenispen) {
				if ($datajenispen = db_fetch_object($resultjenispen)) {
					$penjenis = $datajenispen->jumlahx;
				}
			}
			
			
			$persen = apbd_hitungpersen($penjenis, $datajenis->jumlahx);
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => $datajenis->uraian,  'width' => '230x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($penjenis),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 array('data' => apbd_fn($datajenis->jumlahx - $penjenis),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			$totalpen += $penjenis;

			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkegrevisi} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {

					//OBYEK
					$sql = 'select sum(jumlah) jumlahx from {anggperkeg} where kodekeg=\'%s\' and mid(kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					$resultobyekpen = db_query($fsql);
					
					$penobyek = 0;
					if ($resultobyekpen) {
						if ($dataobyekpen = db_fetch_object($resultobyekpen)) {
							$penobyek = $dataobyekpen->jumlahx;
						}
					}
				
					$persen = apbd_hitungpersen($penobyek, $dataobyek->jumlahx);
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($penobyek),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

										 array('data' => apbd_fn($dataobyek->jumlahx-$penobyek),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
										 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

								 );		
								 
					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						
						
						$sql = 'select kodero,uraian,jumlah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
						$resultpen = db_query($fsql);
						
						$penrekening = 0;
						if ($resultpen) {
							if ($datapen = db_fetch_object($resultpen)) {
								$penrekening = $datapen->jumlah;
							}
						}
						
						$persen = apbd_hitungpersen($penrekening, $data->jumlah);
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => $data->uraian,  'width' => '230x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($datapen->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '', 'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => '',  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:center;font-size:small;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '60px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),

											 array('data' => apbd_fn($data->jumlah - $penrekening),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
											 array('data' => apbd_fn1($persen),  'width' => '35px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-size:small;font-weight:bold;'),
											 
										);
										
							//DETIL
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperkegdetilrevisi} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////drupal_set_message($fsql);

							$resultdetil = db_query($fsql);
							
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									if ($penrekening > 0) {
									$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' and iddetil=\'%s\'';
									$fsql = sprintf($sql, $kodekeg, $data->kodero, db_escape_string($datadetil->iddetil));
									
									//drupal_set_message($fsql);
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
									
									
									$persen = apbd_hitungpersen($datadetilpentotal, $datadetil->total);
									if ($datadetil->uraian == $datadetilpenuraian) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
			 												 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' => $datadetil->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
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
																 array('data' => apbd_fn1(0),  'width' => '60px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 
																 array('data' => apbd_fn(-$datadetilpentotal),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 array('data' => apbd_fn1(-100),  'width' => '35px', 'style' => ' border-right: 1px solid black; text-align:right;font-size:small;font-weight:lighter;'),
																 );
											//####
											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												////drupal_set_message($fsql);
												
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
															 array('data' => $datadetil->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;'),
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
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsubrevisi} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
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
												
												if ($datasuburaian_pen == $datasub->uraian) {
													$persen = apbd_hitungpersen($datasubtotal_pen, $datasub->total);
												
													$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:small;'),
															 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;font-size:small;'),
															 array('data' =>  '- ' . $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
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
															 array('data' =>  '- ' . $datasub->uraian,  'width' => '215px', 'style' => ' border-right: 1px solid black; text-align:left;font-size:small;font-weight:lighter;font-style: italic;'),
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

function GenReportFormFooter($kodekeg, $tipedok) {
	
	if ($tipedok=='dpa') {
		$pquery = sprintf("select tw1, tw2, tw3, tw4 from {kegiatanrevisi} where kodekeg='%s'", db_escape_string($kodekeg)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$tw1 = $data->tw1;
			$tw2 = $data->tw2;
			$tw3 = $data->tw3;
			$tw4 = $data->tw4;
			
		}

		$pquery = sprintf("select dpatgl1 dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
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
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => $budjabatan,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'II',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'III',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'7',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;text-decoration: underline'),
							 );	
		$rowsfooter[] = array (
							 array('data' => 'IV',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:right'),
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
		$pquery = sprintf("select u.kodedinas, u.namauk, u.pimpinannama, u.pimpinannip, u.pimpinanjabatan, k.plafon, k.total from {unitkerja} u left join {kegiatanrevisi} k 
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
		//$strplafon = apbd_fn($total) . ' - ' . apbd_fn($plafon);
		if ($total>$plafon) {	
			//$strplafon .=  ' WARNING!';
			$strplafon = '!!!USULAN ANGGARAN (' . apbd_fn($total) . ') MELEBIHI PLAFON (' . apbd_fn($plafon) . '), HARAP DIPERBAIKI!!!';
		}
		

		$pquery = sprintf("select count(kodero) jmlrek from {anggperkegrevisi} where (jumlah mod 1000)>0 and  kodekeg='%s'", db_escape_string($kodekeg));
		$pres = db_query($pquery);
		////drupal_set_message($pquery); 
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

function PrintSuratPermohonan($id) {
	
	$sql = 'select id,jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$kodekeg = $data->kodekeg; 
		$tahun = $data->tahun; 
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}
	
	$tpenetapan = 1;
	if ($jenisrevisi == '1') {
		
		//cek admin yg baru
		$tpenetapan = 0;
		$sql = 'select total from {kegiatanskpd} where kodekeg=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
		if ($data = db_fetch_object($res)) {
			$tpenetapan = $data->total;
		}
			
	
		$jenisrevisi_str = 'Perubahan/Pergeseran Anggaran Tetap.';
		
		$geserblokir_str = '[ - ]';
		$geserrincian_str = '[ - ]';
		$geserobyek_str = '[ - ]';
		if ($geserblokir =='1') $geserblokir_str = '[ x ]';
		if ($geserrincian =='1') $geserrincian_str = '[ x ]';
		if ($geserobyek =='1') $geserobyek_str = '[ x ]';

	} else if ($jenisrevisi =='2') {
		$jenisrevisi_str = 'Perubahan/ralat karena kesalahan administrasi.';
		
		$lokasi_str = '[ - ]'; 
		$sumberdana_str = '[ - ]';
		$kinerja_str = '[ - ]';
		$sasaran_str = '[ - ]';
		$detiluraian_str = '[ - ]';
		$rab_str = '[ - ]'; 
		$triwulan_str = '[ - ]';
		$lainnya_str = '[ - ]';
		
		if ($lokasi =='1') $lokasi_str = '[ x ]'; 
		if ($sumberdana =='1') $sumberdana_str = '[ x ]';
		if ($kinerja =='1') $kinerja_str = '[ x ]';
		if ($sasaran =='1') $sasaran_str = '[ x ]';
		if ($detiluraian =='1') $detiluraian_str = '[ x ]';
		if ($rab =='1') $rab_str = '[ x ]'; 
		if ($triwulan =='1') $triwulan_str = '[ x ]';
		if ($lainnya =='1') $lainnya_str = '[ x ]';

		
	} else if ($jenisrevisi =='3')
		$jenisrevisi_str = 'Jenis : Penambahan/Pengurangan pada Pagu Anggaran Tetap dari Dana Transfer (DAK/Banprov).';

	else if ($jenisrevisi =='9')
		$jenisrevisi_str = 'Jenis : Perubahan APBD.';
		
	else
		$jenisrevisi_str = 'Mendesak/Darurat.';


	
	$sql = sprintf("select pimpinannama,pimpinanpangkat,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> 'PEMERINTAH KABUPATEN JEPARA', 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;'),
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->namauk, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->header1, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border-bottom: 1px solid black; text-align:center;'),
						array ('data'=> $data->header2, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border-bottom: 1px solid black; text-align:center;')
						);
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
	}
	
	$sql = sprintf('select k.nomorkeg, k.kodepro, k.kegiatan, 
				uk.kodedinas from {kegiatanrevisi} k left join {unitkerja} uk 
				on k.kodeuk=uk.kodeuk where k.kodekeg=\'%s\'' . $where, db_escape_string($kodekeg));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
		$namakegiatan = strtoupper($data->kegiatan);
	}

	$sql = sprintf('select blno dpano,bltgl dpatgl from {dpanomor} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$dpano = $data->dpano;
		$dpatgl = $data->dpatgl;
	}
	$tahun = variable_get('apbdtahun', 0);	
	$sql = sprintf('select dpablformat dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));	
	$res = db_query($sql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$dpaformat = $data->dpaformat;
		}
	}	
	$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
	$dpanolengkap = str_replace('NOKEG',$nomorkeg,$dpanolengkap);	
	
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '335px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '200px', 'style' => 'text-align:left;'),
				);
	

	$rows[] = array (
				array('data' => 'Nomor',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $nosurat, 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Sifat',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Segera', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Lampiran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Satu Berkas', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Perihal',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Usulan Revisi Anggaran', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);

	
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kepada',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Yth. Sekretaris Daerah Kabupaten Jepara',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Di',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'J E P A R A',  'width'=> 'Jeparapx', 'style' => 'text-align:left;text-decoration:underline;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);

			
	//ISI SURAT		
	//1
	$rows[] = array (
				array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dasar Hukum :', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Peraturan Bupati Nomor 6 Tahun 2014 tentang Tata Cara Revisi Anggaran', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	
	if (($jenisrevisi=='3') and ($subjenisrevisi=='2')) {
		
	} else {
		if ($tpenetapan>0) {
			$rows[] = array (
						array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'DPA-SKPD Nomor : ' . $dpanolengkap . ', Tanggal : ' . $dpatgl . ', Nama Kegiatan : ' . $namakegiatan, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
						);
		}
	}		
	
	//2
	$rows[] = array (
				array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Alasan/pertimbangan perlunya Revisi Anggaran:', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => $alasan1, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	if ($alasan2 !='')
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => $alasan2, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
	if ($alasan3 !='')
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => $alasan3, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
	
	//3
	$rows[] = array (
				array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Bersama ini diusulkan Revisi Anggaran :', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	if (($jenisrevisi=='1') or ($jenisrevisi=='9')) {
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Kategori : ' . $jenisrevisi_str, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Jenis : ', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'i.', 'width' => '25px', 'style' => 'text-align:left;'),
					array('data' => 'Perubahan/penghapusan blokir dalam jenis belanja berkenaan ' . $geserblokir_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'ii.', 'width' => '25px', 'style' => 'text-align:left;'),
					array('data' => 'Pergeseran antar rincian objek belanja dalam objek belanja berkenaan ' . $geserrincian_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iii.', 'width' => '25px', 'style' => 'text-align:left;'),
					array('data' => 'Pergeseran antar objek belanja dalam jenis belanja berkenaan ' . $geserobyek_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
	
	//ADMIN`	
	} else if ($jenisrevisi=='2') {
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Kategori : ' . $jenisrevisi_str, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Jenis : ', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'i.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan lokasi ' . $lokasi_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'ii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan sumber dana ' . $sumberdana_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan tolok ukur dan/atau target kinerja ' . $kinerja_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iv.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan kelompok sasaran kegiatan ' . $sasaran_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);	
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'v.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan uraian dalam rincian objek belanja ' . $detiluraian_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'vi.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan perhitungan satuan, volume danharga satuan dalam rincian objek belanja ' . $rab_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'vii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan pagu anggaran triwulan ' . $triwulan_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'viii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan administrasilainnya yang tidak bertentangan dengan ketentuan peraturan perundang-undangan ' . $lainnya_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
	
	} else if ($jenisrevisi=='3') {
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Kategori : Penambahan, pengurangan dan/atau pagu anggaran tetap yang dananya bersumber dari transfer Pemerintah Propinsi dan/atau Pemerintah', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
		if ($subjenisrevisi=='1') {
			$rows[] = array (
						array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Adanya petunjuk teknis kegiatan yang didanai atau bersumber transfer dari Pemerintah/Pemerintah Provinsi yang mengakibatkan perubahan/pergeseran anggaran yang telah ditetapkan dalam Peraturan Daerah tentang APBD atau Perubahan APBD', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
						);
		} else {
			$rows[] = array (
						array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Jenis : Adanya transfer dari Pemerintah atau Pemerintah Provinsi setelah ditetapkan Peraturan Daerah tentang APBD atau Perubahan APBD', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'c.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Kegiatan : ' . $namakegiatan, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
						);
		}	
	}
	//3
	$rows[] = array (
				array('data' => '4.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Sebagai bahan pertimbangan, dengan ini dilampirkan data dukung berupa', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Matriks Perubahan (semula-menjadi) sebagaimana daftar terlampir;', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Surat pernyataan Tanggung Jawab Mutlak (SPTJM);', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'c.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'ADK dan Manual RKA-SKPD DPA Revisi;', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	if ($dokumen!='')
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'd.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => $dokumen, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Demikian kami sampaikan, atas kerjasamanya diucapkan terima kasih', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function PrintSuratPermohonanPerubahan($id) {
	
	$sql = 'select id,jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, geserjenis, geserbaru, geserplafon, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$kodekeg = $data->kodekeg; 
		$tahun = $data->tahun; 
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		
		$geserbaru = $data->geserbaru; 
		$geserplafon = $data->geserplafon; 
		$geserjenis = $data->geserjenis; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}
	
	if (($geserjenis==1) or ($geserplafon==1) or ($geserbaru==1))
		$str_kepada = 'BUPATI JEPARA';
	else
		$str_kepada = 'Sekretaris Daerah Kabupaten Jepara';
	
	$tpenetapan = 1;
		
	//cek admin yg baru
	$tpenetapan = 0;
	$sql = 'select total from {kegiatanskpd} where kodekeg=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
	if ($data = db_fetch_object($res)) {
		$tpenetapan = $data->total;
	}
		

	$jenisrevisi_str = 'Perubahan Anggaran.';
	
	$geserblokir_str = '[ - ]';
	$geserrincian_str = '[ - ]';
	$geserobyek_str = '[ - ]';
	$geserjenis_str = '[ - ]';
	$geserbaru_str = '[ - ]';
	$geserplafon_str = '[ - ]';
	
	if ($geserblokir =='1') $geserblokir_str = '[ x ]';
	if ($geserrincian =='1') $geserrincian_str = '[ x ]';
	if ($geserobyek =='1') $geserobyek_str = '[ x ]';
	if ($geserjenis =='1') $geserjenis_str = '[ x ]';
	if ($geserbaru =='1') $geserbaru_str = '[ x ]';
	if ($geserplafon =='1') $geserplafon_str = '[ x ]';

	$lokasi_str = '[ - ]'; 
	$sumberdana_str = '[ - ]';
	$kinerja_str = '[ - ]';
	$sasaran_str = '[ - ]';
	$detiluraian_str = '[ - ]';
	$triwulan_str = '[ - ]';
	
	if ($lokasi =='1') $lokasi_str = '[ x ]'; 
	if ($sumberdana =='1') $sumberdana_str = '[ x ]';
	if ($kinerja =='1') $kinerja_str = '[ x ]';
	if ($sasaran =='1') $sasaran_str = '[ x ]';
	if ($detiluraian =='1') $detiluraian_str = '[ x ]';
	if ($triwulan =='1') $triwulan_str = '[ x ]';

		
	$sql = sprintf("select pimpinannama,pimpinanpangkat,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> 'PEMERINTAH KABUPATEN JEPARA', 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;'),
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->namauk, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->header1, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border-bottom: 1px solid black; text-align:center;'),
						array ('data'=> $data->header2, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border-bottom: 1px solid black; text-align:center;')
						);
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
	}
	
	$sql = sprintf('select k.nomorkeg, k.kodepro, k.kegiatan, 
				uk.kodedinas from {kegiatanrevisi} k left join {unitkerja} uk 
				on k.kodeuk=uk.kodeuk where k.kodekeg=\'%s\'' . $where, db_escape_string($kodekeg));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
		$namakegiatan = strtoupper($data->kegiatan);
	}

	$sql = sprintf('select blno dpano,bltgl dpatgl from {dpanomor} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$dpano = $data->dpano;
		$dpatgl = $data->dpatgl;
	}
	$tahun = variable_get('apbdtahun', 0);	
	$sql = sprintf('select dpablformat dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));	
	$res = db_query($sql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$dpaformat = $data->dpaformat;
		}
	}	
	$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
	$dpanolengkap = str_replace('NOKEG',$nomorkeg,$dpanolengkap);	
	
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '335px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '200px', 'style' => 'text-align:left;'),
				);
	

	$rows[] = array (
				array('data' => 'Nomor',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $nosurat, 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Sifat',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Segera', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Lampiran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Satu Berkas', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Perihal',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Usulan Perubahan Anggaran', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);

	
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kepada',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Yth. ' . $str_kepada,  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Di',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'J E P A R A',  'width'=> 'Jeparapx', 'style' => 'text-align:left;text-decoration:underline;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);

			
	//ISI SURAT		
	//1
	$rows[] = array (
				array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dasar Hukum :', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'KUA dan PPAS Perubahan Tahun Anggaran 2016', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	
	if ($tpenetapan>0) {
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'DPA-SKPD Nomor : ' . $dpanolengkap . ', Tanggal : ' . $dpatgl . ', Nama Kegiatan : ' . $namakegiatan, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
	}		
	
	//2
	$rows[] = array (
				array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Alasan/pertimbangan perlunya Revisi Anggaran:', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => $alasan1, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	
	//3
	$rows[] = array (
				array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Bersama ini diusulkan Perubahan Anggaran :', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Perubahan atau pergeseran rekening:', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'i.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Pergeseran antar Rincian Objek Belanja dalam objek belanja Kegiatan berkenaan ' . $geserrincian_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'ii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Pergeseran antar Objek Belanja dalam objek belanja Kegiatan berkenaan ' . $geserrincian_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Pergeseran antar Jenis Belanja dalam kelompok belanja Kegiatan berkenaan ' . $geserjenis_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iv.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Penambahan/Pengurangan anggaran dalam Kegiatan berkenaan ' . $geserplafon_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'v.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Kegiatan baru ' . $geserbaru_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
	
	//ADMIN`	
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Perubahan atas kesalahan administrasi:', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'i.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan lokasi ' . $lokasi_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'ii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan sumber dana ' . $sumberdana_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iii.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan tolok ukur dan/atau target kinerja ' . $kinerja_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'iv.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan kelompok sasaran kegiatan ' . $sasaran_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);	
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'v.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan uraian dalam rincian objek belanja ' . $detiluraian_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
	
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'vi.', 'width' => '25px', 'style' => 'text-align:right;'),
					array('data' => 'Ralat/kesalahan pagu anggaran triwulan ' . $triwulan_str, 'colspan'=>'2', 'width' => '470px', 'style' => 'text-align:left;'),
					);		
	
	
	//3
	$rows[] = array (
				array('data' => '4.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Sebagai bahan pertimbangan, dengan ini dilampirkan data dukung berupa', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'a.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Matriks Perubahan (semula-menjadi) sebagaimana daftar terlampir;', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'b.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Surat pernyataan Tanggung Jawab Mutlak (SPTJM);', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'c.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'ADK dan Manual RKA-SKPD Perubahan;', 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
				);
	if ($dokumen!='')
		$rows[] = array (
					array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'd.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => $dokumen, 'colspan'=>'3', 'width' => '495px', 'style' => 'text-align:left;'),
					);
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Demikian kami sampaikan, atas kerjasamanya diucapkan terima kasih', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}


function PrintMatriksPermohonan($id) {
	
	$sql = 'select id,jenisrevisi, kodekeg, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$kodekeg = $data->kodekeg;
		$tahun = $data->tahun; 
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}

	$sql = sprintf("select pimpinannama,pimpinanpangkat,namauk,kodedinas,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		
		$kodedinas =  $data->kodedinas;
	} 
	
	$pquery = sprintf('select p.kodepro, nomorkeg, lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  from {kegiatanrevisi} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_per = str_replace('||',', ', $data->lokasi);
		$sumberdana1_per = $data->sumberdana1;
		$kelompoksasaran_per = $data->kelompoksasaran;

		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		$anggaran = apbd_fn($data->total);
		
		$tw1_per = $data->tw1; 
		$tw2_per = $data->tw2; 
		$tw3_per = $data->tw3; 
		$tw4_per = $data->tw4; 
		
	}
	
	$pquery = sprintf('select lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  
				from {kegiatanskpd} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_pen = str_replace('||',', ', $data->lokasi);
		$sumberdana1_pen = $data->sumberdana1;
		$kelompoksasaran_pen = $data->kelompoksasaran;

		$tw1_pen = $data->tw1; 
		$tw2_pen = $data->tw2; 
		$tw3_pen = $data->tw3; 
		$tw4_pen = $data->tw4; 
		
	}
	
	
	/*
	$sql = sprintf('select k.nomorkeg, k.kodepro, k.kegiatan, 
				uk.kodedinas, p.program from {kegiatanskpd} k left join {unitkerja} uk 
on k.kodeuk=uk.kodeuk left join {program} p on k.kodepro=p.kodepro where k.kodekeg=\'%s\'' . $where, db_escape_string($kodekeg));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' . $data->kegiatan;
	}
	*/
	
	
	$rows[] = array (array ('data'=> 'MATRIK PERUBAHAN (SEMULA-MENJADI)', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	if ($jenisrevisi=='1')
		$rows[] = array (array ('data'=> 'PERUBAHAN ATAU PERGESERAN RINCIAN ANGGARAN YANG DALAM HAL PAGU ANGGARAN TETAP', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else if ($jenisrevisi=='2')
		$rows[] = array (array ('data'=> 'PERUBAHAN/RALAT KARENA KESALAHAN ADMINISTRASI', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else if ($jenisrevisi=='3')
		$rows[] = array (array ('data'=> 'PENAMBAHAN, PENGURANGAN DAN/ATAU PAGU ANGGARAN TETAP YANG DANANYA BERSUMBER DARI TRANSFER PEMERINTAH PROVINSI DAN/ATAU PEMERINTAH PUSAT', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else if ($jenisrevisi=='9')
		$rows[] = array (array ('data'=> 'PERUBAHAN APBD', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else 
		$rows[] = array (array ('data'=> 'PERGESERAN UNTUK KEPERLUAN/KEBUTUHAN MENDESAK', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));

	$rows[] = array (
				array('data' => 'SKPD',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $skpd, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Program',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $program, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kegiatan',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $kegiatan, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Anggaran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $anggaran, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	
	//ISI SURAT		
	//1
	if ($jenisrevisi=='2') {
		$rows[] = array (
					array('data' => 'No.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'URAIAN',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'SEMULA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'MENJADI',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'KETERANGAN', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);		
		if ($lokasi=='1')
			$rows[] = array (
						array('data' => '1.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan lokasi',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		if ($sumberdana=='1')
		$rows[] = array (
					array('data' => '2.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan sumber dana',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => $sumberdana1_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => $sumberdana1_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($kinerja=='1')
		$rows[] = array (
					array('data' => '3.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahantolok ukur dan/atau target kinerja',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($sasaran=='1')
		$rows[] = array (
					array('data' => '4.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan kelompok sasaran kegiatan',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => $kelompoksasaran_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => $kelompoksasaran_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($detiluraian=='1')
		$rows[] = array (
					array('data' => '5.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan uraian dalam rincian objek belanja',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($rab=='1')
		$rows[] = array (
					array('data' => '6.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan perhitungan satuan, volume danharga satuan dalam rincian objek belanja',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($triwulan=='1') {
			$rows[] = array (
						array('data' => '7.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan pagu anggaran triwulan',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #1',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw1_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw1_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #2',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw2_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw2_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #3',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw3_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw3_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'), 
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #4',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw4_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw4_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
						
		}
		if ($lainnya=='1')
		$rows[] = array (
					array('data' => '8.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan administrasi lainnya yang tidak bertentangan dengan ketentuan peraturan perundang-undangan',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
					
	//
	} else if (($jenisrevisi=='1') or ($jenisrevisi=='3')) {

		$total_pen=0;
		$total_per=0;
		$rows[] = array (
					array('data' => 'Kode',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Uraian',  'width'=> '180px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Semula',  'width' => '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Menjadi',  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Bertambah/ Berkurang',  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Ket.', 'width' => '35px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:center;'),
					);		

		//$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi}  where kodekeg=\'%s\' order by kodero';

		//$sql = 'select r.kodero,r.uraian,r.jumlah from {anggperkegrevisi}  r left join {anggperkeg} p on r.kodekeg=p.kodekeg and r.kodero=p.kodero where r.kodekeg=\'%s\' and r.jumlah<>p.jumlah order by r.kodero';
		//$fsql = sprintf($sql, db_escape_string($kodekeg));

		$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi}  where kodekeg=\'%s\' order by kodero';
		$fsql = sprintf($sql, db_escape_string($kodekeg));

		$res = db_query($fsql);
		
		$kosong = true;
		if ($res) {
			while ($data = db_fetch_object($res)) {

				$jumlah_pen = 0;
				$sqlpen = 'select kodero,uraian,jumlah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
				$fsql = sprintf($sqlpen, db_escape_string($kodekeg), db_escape_string($data->kodero));
				$respen = db_query($fsql);
				if ($datapen = db_fetch_object($respen)) {
					$jumlah_pen = $datapen->jumlah;
				}
				
				if ($jumlah_pen != $data->jumlah) {
					$total_pen += $jumlah_pen;
					$total_per += $data->jumlah;
					
					$rows[] = array (
								array('data' => $data->kodero,  'width'=> '50px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => $data->uraian,  'width'=> '180px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => apbd_fn($jumlah_pen),  'width' => '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;'),
								array('data' => apbd_fn($data->jumlah),  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
								array('data' => apbd_fn($data->jumlah - $jumlah_pen),  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
								array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
								);
				}			
			}
			
			//JIKA KOSONG
			if ($total_per == 0) {
				$rows[] = array (
							array('data' => '',  'width'=> '50px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
							array('data' => 'Tidak ada perubahan rekening belanja',  'width'=> '180px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
							array('data' => '0',  'width' => '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;'),
							array('data' => '0',  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
							array('data' => '0',  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
							array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
							);					
			}
			
			$rows[] = array (
						array('data' => '',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Total',  'width'=> '180px', 'style' => 'border-top: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($total_pen),  'width' => '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:rigth;'),
						array('data' => apbd_fn($total_per),  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($total_per - $total_pen),  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);		
			
			
		}							
	}	
	
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '535px', 'style' => 'border-top: 1px solid black;text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function PrintMatriksPermohonanPerubahan($id) {
	
	$sql = 'select id,jenisrevisi, kodekeg, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, geserbaru, geserplafon, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$kodekeg = $data->kodekeg;
		$tahun = $data->tahun; 
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		$geserbaru = $data->geserbaru; 
		$geserplafon = $data->geserplafon; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}

	$sql = sprintf("select pimpinannama,pimpinanpangkat,namauk,kodedinas,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		
		$kodedinas =  $data->kodedinas;
	} 
	
	$pquery = sprintf('select p.kodepro, nomorkeg, lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  from {kegiatanrevisi} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_per = str_replace('||',', ', $data->lokasi);
		$sumberdana1_per = $data->sumberdana1;
		$kelompoksasaran_per = $data->kelompoksasaran;

		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		$anggaran = apbd_fn($data->total);
		
		$tw1_per = $data->tw1; 
		$tw2_per = $data->tw2; 
		$tw3_per = $data->tw3; 
		$tw4_per = $data->tw4; 
		
	}
	
	$pquery = sprintf('select lokasi, tw1p, tw2p, tw3p, tw4p, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  
				from {kegiatanperubahan} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_pen = str_replace('||',', ', $data->lokasi);
		$sumberdana1_pen = $data->sumberdana1;
		$kelompoksasaran_pen = $data->kelompoksasaran;

		$tw1_pen = $data->tw1p; 
		$tw2_pen = $data->tw2p; 
		$tw3_pen = $data->tw3p; 
		$tw4_pen = $data->tw4p; 
		
	}
	
	
	/*
	$sql = sprintf('select k.nomorkeg, k.kodepro, k.kegiatan, 
				uk.kodedinas, p.program from {kegiatanskpd} k left join {unitkerja} uk 
on k.kodeuk=uk.kodeuk left join {program} p on k.kodepro=p.kodepro where k.kodekeg=\'%s\'' . $where, db_escape_string($kodekeg));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' . $data->kegiatan;
	}
	*/
	
	
	$rows[] = array (array ('data'=> 'MATRIK PERUBAHAN (SEMULA-MENJADI)', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));

	$rows[] = array (array ('data'=> 'PERUBAHAN APBD', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));

	$rows[] = array (
				array('data' => 'SKPD',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $skpd, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Program',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $program, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kegiatan',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $kegiatan, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Anggaran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $anggaran, 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '470px', 'style' => 'text-align:left;'),
				);
	
	//ISI SURAT		
	//1
		
	$no = 0;	
	if (($lokasi + $sumberdana + $kinerja + $sasaran + $detiluraian + $triwulan)>0) {
		$rows[] = array (
					array('data' => '- MATRIK PERUBAHAN ADMINISTRASI', 'colspan'=>'5', 'width' => '535px', 'style' => 'text-align:left;'),
					);

		$rows[] = array (
					array('data' => 'No.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'URAIAN',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'SEMULA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'MENJADI',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'KETERANGAN', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);		
		if ($lokasi=='1') {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan lokasi',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if ($sumberdana=='1') {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan sumber dana',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $sumberdana1_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $sumberdana1_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if ($kinerja=='1') {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahantolok ukur dan/atau target kinerja',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if ($sasaran=='1') {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan kelompok sasaran kegiatan',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $kelompoksasaran_pen,  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => $kelompoksasaran_per,  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if ($detiluraian=='1') {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan uraian dalam rincian objek belanja',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Terlampir di RKA',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Terlampir di RKA',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if ($triwulan=='1')  {
			$no++;
			$rows[] = array (
						array('data' => $no . '.',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan pagu anggaran triwulan',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => '',  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #1',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw1_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw1_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #2',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw2_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw2_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #3',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw3_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw3_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'), 
						);
			$rows[] = array (
						array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Triwulan #4',  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($tw4_pen),  'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($tw4_per),  'width'=> '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '125px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
						);
						
		}
		
		//CLOSING LINE
		$rows[] = array (
					array('data' => '',  'width'=> '535px', 'colspan'=>'5','style' => 'border-top: 1px solid black;'),
					);		
					
	//
	} 

		
	
	if (($geserjenis + $geserrincian + $geserobyek + $geserplafon + $geserbaru)>0) {

		$rows[] = array (
				array('data' => '- MATRIK PERUBAHAN REKENING', 'colspan'=>'5', 'width' => '535px', 'style' => 'text-align:left;'),
				);
					
		$total_pen=0;
		$total_per=0;
		$rows[] = array (
					array('data' => 'Kode',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Uraian',  'width'=> '180px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Semula',  'width' => '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Menjadi',  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Bertambah/ Berkurang',  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:center;'),
					array('data' => 'Ket.', 'width' => '35px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:center;'),
					);		

		//$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi}  where kodekeg=\'%s\' order by kodero';

		//$sql = 'select r.kodero,r.uraian,r.jumlah from {anggperkegrevisi}  r left join {anggperkeg} p on r.kodekeg=p.kodekeg and r.kodero=p.kodero where r.kodekeg=\'%s\' and r.jumlah<>p.jumlah order by r.kodero';
		//$fsql = sprintf($sql, db_escape_string($kodekeg));

		$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi}  where kodekeg=\'%s\' order by kodero';
		$fsql = sprintf($sql, db_escape_string($kodekeg));

		$res = db_query($fsql);
		
		$kosong = true;
		if ($res) {
			while ($data = db_fetch_object($res)) {

				$jumlah_pen = 0;
				$sqlpen = 'select kodero,uraian,jumlahp from {anggperkegperubahan} where kodekeg=\'%s\' and kodero=\'%s\'';
				$fsql = sprintf($sqlpen, db_escape_string($kodekeg), db_escape_string($data->kodero));
				$respen = db_query($fsql);
				if ($datapen = db_fetch_object($respen)) {
					$jumlah_pen = $datapen->jumlahp;
				}
				
				if ($jumlah_pen != $data->jumlah) {
					$total_pen += $jumlah_pen;
					$total_per += $data->jumlah;
					
					$rows[] = array (
								array('data' => $data->kodero,  'width'=> '50px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => $data->uraian,  'width'=> '180px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => apbd_fn($jumlah_pen),  'width' => '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;'),
								array('data' => apbd_fn($data->jumlah),  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
								array('data' => apbd_fn($data->jumlah - $jumlah_pen),  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
								array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
								);
				}			
			}
			
			//JIKA KOSONG
			if ($total_per == 0) {
				$rows[] = array (
							array('data' => '',  'width'=> '50px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
							array('data' => 'Tidak ada perubahan rekening belanja',  'width'=> '180px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
							array('data' => '0',  'width' => '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;'),
							array('data' => '0',  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
							array('data' => '0',  'width'=> '90px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
							array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
							);					
			}
			
			$rows[] = array (
						array('data' => '',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Total',  'width'=> '180px', 'style' => 'border-top: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($total_pen),  'width' => '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:rigth;'),
						array('data' => apbd_fn($total_per),  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($total_per - $total_pen),  'width'=> '90px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);		
			
			
		}							
	}	
	
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '535px', 'style' => 'border-top: 1px solid black;text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}


function kegiatanskpd_printusulan2_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Dokumen Revisi dan Setting Printer',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,        
	);
	
	$id = arg(3);
	$topmargin = arg(4);
	$tipedok = arg(5);

	$sampul = arg(7);
	//$tipedok =  arg(5);

	if (isset($topmargin)) $topmargin = arg(4);
	
	if (!isset($topmargin)) $topmargin=10;

	$form['formdata']['id']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $id, 
	);
	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipedok, 
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
	if ($tipedok=='rka') {
		$form['formdata']['sampul']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $sampul, 
		);
		$form['formdata']['submitrka'] = array (
			'#type' => 'submit',		
			'#value' => 'RKA Revisi',
		);		
		$form['formdata']['submitsp'] = array (
			'#type' => 'submit',
			'#value' => 'Permohonan'
		);		
		$form['formdata']['submitmp'] = array (
			'#type' => 'submit',
			'#value' => 'Matrik'
		);		

		$sql = 'select status from {kegiatanrevisiperubahan} where id=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($data = db_fetch_object($res)) {
			$status = $data->status;
		}
		
		if (isSuperuser() and ($status==1)) {
			$form['formdata']['submitsptjm'] = array (
				'#type' => 'submit',
				'#value' => 'SPTJM'
			);		
		
			$form['formdata']['submitapv'] = array (
				'#type' => 'submit',
				'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
				'#value' => 'Persetujuan'
			);
				
			
		} else {
			$form['formdata']['submitsptjm'] = array (
				'#type' => 'submit',
				'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
				'#value' => 'SPTJM'
			);		
		}
	
	}  else {
		$form['formdata']['submitrpka'] = array (
			'#type' => 'submit',	
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisiperubahan' class='btn_green' style='color: white'>Tutup</a>",
			'#value' => 'DPPA SKPD',
		);	
		
		
	}
	return $form;
}

function PrintMatriksSPTJM($id) {
	
	$sql = 'select id,jenisrevisi,kodekeg,subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$kodekeg = $data->kodekeg;
		$tahun = $data->tahun; 
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}

	$pquery = sprintf('select lokasi, kegiatan, sumberdana1, kelompoksasaran  
				from {kegiatanskpd} where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_pen = str_replace('||',', ', $data->lokasi);
		$sumberdana1_pen = $data->sumberdana1;
		$kelompoksasaran_pen = $data->kelompoksasaran;
	}
	$pquery = sprintf('select lokasi, kegiatan, sumberdana1, kelompoksasaran  
				from {kegiatanrevisi} where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_per = str_replace('||',', ', $data->lokasi);
		$sumberdana1_per = $data->sumberdana1;
		$kelompoksasaran_per = $data->kelompoksasaran;
		$kegiatanuraian = strtoupper($data->kegiatan);
		
	}
	
	if ($jenisrevisi=='9') {
		$str_revisi = 'perubahan';
		$str_hukum = 'peraturan perundang-undangan yang berlaku';
	} else {
		$str_revisi = 'revisi';
		$str_hukum = 'Peraturan Bupati Nomor 6 Tahun 2014 tentang Tata Cara Revisi Anggaran';
	}
	
	$sql = sprintf("select pimpinannama,pimpinanpangkat,namauk,kodedinas,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> 'PEMERINTAH KABUPATEN JEPARA', 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;'),
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->namauk, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->header1, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border-bottom: 1px solid black; text-align:center;'),
						array ('data'=> $data->header2, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border-bottom: 1px solid black; text-align:center;')
						);

		$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;

	}
	
	$sql = sprintf('select k.nomorkeg, k.kodepro, k.kegiatan, 
				uk.kodedinas, p.program from {kegiatanskpd} k left join {unitkerja} uk 
on k.kodeuk=uk.kodeuk left join {program} p on k.kodepro=p.kodepro where k.kodekeg=\'%s\'' . $where, db_escape_string($kodekeg));
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . '-' . $data->kegiatan;
	}


	$rows[] = array (array ('data'=> 'SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;text-decoration: underline;'));
	$rows[] = array (array ('data'=> 'NOMOR : ...............', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));


	$rows[] = array (array ('data'=> 'Yang bertanda tangan dibawah ini:', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:left;'));
	$rows[] = array (
				array('data' => '- Nama',  'width'=> '100px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $pimpinannama, 'colspan'=>'3', 'width' => '420px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '- NIP',  'width'=> '100px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $pimpinannip, 'colspan'=>'3', 'width' => '420px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '- Jabatan',  'width'=> '100px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan, 'colspan'=>'3', 'width' => '420px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '100px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '420px', 'style' => 'text-align:left;'),
				);
				
	//ISI			
	$rows[] = array (
				array('data' => '',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dengan ini menyatakan dan bertanggungjawab secara penuh atas hal-hal sebagai berikut:', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array ( 
				array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Usulan ' . $str_revisi . ' anggaran kegiatan ' . $kegiatanuraian . ' telah disusun sesuai dengan ketentuan sebagaimana diatur dengan ' . $str_hukum, 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dokumen-dokumen yang dipersyaratkan dalam rangka ' . $str_revisi . ' anggaran telah disusun dengan lengkap dan benar, telah kami simpan (arsipkan) dan siap diaudit sewaktu-waktu.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	if ($jenisrevisi=='1')
		$rows[] = array (
					array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Perhitungan matrik berupa perubahan atau pergeseran rincian anggaran yang dalam hal pagu anggaran tetap telah disusun sesuai dengan kebutuhan dan harga yang ekonomis.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
					);
	else if ($jenisrevisi=='2')
		$rows[] = array (
					array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Perhitungan matrik berupa perubahan/ralat karena kesalahan administrasi telah disusun sesuai dengan kebutuhan dan rincian uraian yang benar', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
					);
	else if ($jenisrevisi=='3')
		$rows[] = array (
					array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Perhitungan matrik berupa penambahan, pengurangan dan/atau pagu anggaran tetap yang dananya bersumber dari transfer pemerintah propinsi dan/atau pemerintah telah disusun sesuai dengan kebutuhan dan harga yang ekonomis.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
					);
	else if ($jenisrevisi=='9')
		$rows[] = array (
					array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Perhitungan matrik berupa penambahan, pengurangan dan/atau pergerean anggaran telah disusun sesuai dengan kebutuhan dan harga yang ekonomis.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
					);
	else
		$rows[] = array (
					array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
					array('data' => 'Perhitungan anggaran darurat telah disusun sesuai dengan kebutuhan dan rincian uraian yang benar', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
					);
		
	$rows[] = array (
				array('data' => '4.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Pengguna anggaran bertanggungjawab secara formal dan material atas usulan revisi anggaran ini', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '5.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Apabila dikemudian hari terbukti surat pernyataan ini tidak benar dan menimbulkan kerugian keuangan daerah, saya bersedia menyetorkan kerugian daerah tersebut ke Kas Daerah', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '6.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dalam hal terjadi permasalahan hukum diakibatkan oleh revisi anggaran ini menjadi tanggung jawab pengguna anggaran.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Demikian kami sampaikan, atas kerjasamanya diucapkan terima kasih', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'3','style' => 'text-align:left;'),
				array('data' => 'Met6Rb',  'width'=> '150px', 'style' => ' text-align:center;'),
				array('data' => '',  'width'=> '150px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function PrintMatriksPermohonanL($id) {

	/*
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
		ini_set('memory_limit', '720M');
	}	
	*/
	
	$sql = 'select id,kodekeg,jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen from {kegiatanrevisiperubahan} where id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		$tahun = $data->tahun; 
		$kodekeg = $data->kodekeg;
		$kodeuk = $data->kodeuk; 
		$kodekeg = $data->kodekeg; 
		$geserblokir = $data->geserblokir; 
		$geserrincian = $data->geserrincian; 
		$geserobyek = $data->geserobyek; 
		
		$lokasi = $data->lokasi; 
		$sumberdana = $data->sumberdana; 
		$kinerja = $data->kinerja; 
		$sasaran = $data->sasaran; 
		$detiluraian = $data->detiluraian; 
		$rab = $data->rab; 
		$triwulan = $data->triwulan; 
		$lainnya = $data->lainnya; 
		
		$alasan1 = $data->alasan1; 
		$alasan2 = $data->alasan2; 
		$alasan3 = $data->alasan3; 
		$nosurat = $data->nosurat; 
		$tglsurat = $data->tglsurat; 
		$dokumen = $data->dokumen;
	}

	$sql = sprintf("select pimpinannama,pimpinanpangkat,namauk,kodedinas,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinanjabatan = $data->pimpinanjabatan; 
		$pimpinannip = $data->pimpinannip;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		
		$kodedinas =  $data->kodedinas;
	}
	
	$pquery = sprintf('select lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  from {kegiatanrevisi} k left join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_per = str_replace('||',', ', $data->lokasi);
		$sumberdana1_per = $data->sumberdana1;
		$kelompoksasaran_per = $data->kelompoksasaran;

		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		$anggaran = apbd_fn($data->total);
		
		$tw1_per = $data->tw1; 
		$tw2_per = $data->tw2; 
		$tw3_per = $data->tw3; 
		$tw4_per = $data->tw4; 
		
	}
	
	$pquery = sprintf('select lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  from {kegiatanskpd} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$lokasi_pen = str_replace('||',', ', $data->lokasi);
		$sumberdana1_pen = $data->sumberdana1;
		$kelompoksasaran_pen = $data->kelompoksasaran;

		$tw1_pen = $data->tw1; 
		$tw2_pen = $data->tw2; 
		$tw3_pen = $data->tw3; 
		$tw4_pen = $data->tw4; 
		
	}

	$rows[] = array (array ('data'=> 'MATRIK PERUBAHAN (SEMULA-MENJADI)', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	if ($jenisrevisi=='2')
		$rows[] = array (array ('data'=> 'PERUBAHAN/RALAT KARENA KESALAHAN ADMINISTRASI', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else if ($jenisrevisi=='3')
		$rows[] = array (array ('data'=> 'PENAMBAHAN, PENGURANGAN DAN/ATAU PAGU ANGGARAN TETAP YANG DANANYA BERSUMBER DARI TRANSFER PEMERINTAH PROVINSI DAN/ATAU PEMERINTAH PUSAT', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	else if ($jenisrevisi=='4')
		$rows[] = array (array ('data'=> 'PERGESERAN UNTUK KEPERLUAN/KEBUTUHAN MENDESAK', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	
	$rows[] = array (array ('data'=> '', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
	$rows[] = array (array ('data'=> '', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));

	$rows[] = array (
				array('data' => 'SKPD',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $skpd, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Program',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $program, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kegiatan',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $kegiatan, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Anggaran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $anggaran, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	
	//ISI SURAT		
	//1
	if ($jenisrevisi>='2') {
		$rows[] = array (
					array('data' => 'No.',  'width'=> '35px',  'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'URAIAN',  'width'=> '125px', 'colspan'=>'2', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'SEMULA',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'MENJADI',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'KET.', 'width' => '35px', 'style' => 'border-top: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);	
		
		if ($lokasi=='1')
			$rows[] = array (
						array('data' => '1.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan lokasi', 'colspan'=>'2',  'width'=> '125px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_pen,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $lokasi_per,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		if ($sumberdana=='1')
		$rows[] = array (
					array('data' => '2.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan sumber dana', 'width'=> '125px', 'colspan'=>'2', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $sumberdana1_pen,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $sumberdana1_per,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($kinerja=='1')
		$rows[] = array (
					array('data' => '3.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahantolok ukur dan/atau target kinerja', 'colspan'=>'2',  'width'=> '125px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
		if ($sasaran=='1')
		$rows[] = array (
					array('data' => '4.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan penulisan kelompok sasaran kegiatan', 'colspan'=>'2',  'width'=> '125px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $kelompoksasaran_pen,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $kelompoksasaran_per,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);

		if ($detiluraian=='1') { 
			if ($rab=='1')
				$strdes = 'Sebagaimana terlampir dalam uraian no. 6) Ralat/kesalahan perhitungan satuan, volume dan harga satuan dalam rincian objek belanja';
			else
				$strdes = 'Sebagai berikut :';
			$rows[] = array (
						array('data' => '5.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
						array('data' => 'Ralat/kesalahan penulisan uraian dalam rincian objek belanja','colspan'=>'2',   'width'=> '125px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
		}
		if (($detiluraian=='1') or ($rab=='1') or ($jenisrevisi>='3')) {
			$strdes = 'Sebagai berikut :';
			if ($jenisrevisi>='3') 
				$strrab = 'Detil uraian anggaran';
			else
				$strrab = 'Ralat/kesalahan perhitungan satuan, volume dan harga satuan dalam rincian objek belanja';
			$rows[] = array (
						array('data' => '6.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
						array('data' => $strrab, 'colspan'=>'2', 'width'=> '125px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
						array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);
					
		
			
			$norek = 0;
			$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi} where kodekeg=\'%s\' order by kodero';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$res = db_query($fsql);
			if ($res) {
				while ($data = db_fetch_object($res)) {

					$sama = true;
					//CEK ADA DETIL PERUBAHAN
					$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total from {anggperkegdetilrevisi} where kodekeg=\'%s\' and kodero=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg),db_escape_string($data->kodero));
					$rescek = db_query($fsql);
					if ($rescek) {
						while (($datacek = db_fetch_object($rescek)) and ($sama)) {
							//cek penetapan 
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total from {anggperkegdetil} where iddetil=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($datacek->iddetil));
							$rescekpen = db_query($fsql);
							if ($rescekpen) {
								if ($datacekpen = db_fetch_object($rescekpen)) {
									$sama = ($datacek->uraian .  $datacek->unitsatuan . $datacek->volumsatuan  == $datacekpen->uraian .  $datacekpen->unitsatuan . $datacekpen->volumsatuan) and ($datacek->unitjumlah + $datacek->volumjumlah + $datacek->harga == $datacekpen->unitjumlah + $datacekpen->volumjumlah + $datacekpen->harga);
								} else {
									$sama = false;
								}
							}
						}
							
					}
					
					if ($sama==false) {
					$sqlpen = 'select kodero,uraian,jumlah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
					$fsql = sprintf($sqlpen, db_escape_string($kodekeg), db_escape_string($data->kodero));
					$respen = db_query($fsql);
					if ($datapen = db_fetch_object($respen)) {
						$jumlah_pen = $datapen->jumlah;
					}
					
					$norek++;
					$rows[] = array (
								array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => $norek . '.',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => $data->uraian, 'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
								array('data' => apbd_fn($jumlah_pen), 'colspan'=>'4',  'width'=> '340px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
								array('data' => apbd_fn($data->jumlah), 'colspan'=>'4',  'width' => '340px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;'),
								array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
								);		
					
					$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,pengelompokan from {anggperkegdetilrevisi} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							
					$resdetil = db_query($fsql);
					if ($resdetil) {
						while ($datadetil = db_fetch_object($resdetil)) {

							$uraian_p = ' (Baru)';
							$unit_p = '';
							$volum_p = '';
							$harga_p = '';
							$total_p = 0;
							
							$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,pengelompokan from {anggperkegdetil} where iddetil=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
							$resdetil_p = db_query($fsql);
							while ($datadetil_p = db_fetch_object($resdetil_p)) {
								$uraian_p = ' (' . $datadetil_p->uraian . ')';
								$unit_p = $datadetil_p->unitjumlah . ' ' . $datadetil_p->unitsatuan;
								$volum_p = $datadetil_p->volumjumlah . ' ' . $datadetil_p->volumsatuan;
								$harga_p = $datadetil_p->harga;
								$total_p = $datadetil_p->total;
							}
							
						
							$rows[] = array (
										array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
										array('data' => '-',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
										array('data' => $datadetil->uraian . $uraian_p,  'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;font-style: italic;'),
										


										array('data' => $unit_p,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => $volum_p,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => apbd_fn($harga_p) ,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => apbd_fn($total_p),  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),

										array('data' => $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => apbd_fn($datadetil->harga) ,  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),
										array('data' => apbd_fn($datadetil->total),  'width' => '85px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:rigth;font-style: italic;'),										
										array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;font-style: italic;'),
										);		
						}
					}
					}		
				}	//END LOOP

				if ($norek==0) {
					$rows[] = array (
								array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => '',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => '', 'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
								array('data' => 'Tidak ada perubahan', 'colspan'=>'4',  'width'=> '340px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => 'Tidak ada perubahan', 'colspan'=>'4',  'width' => '340px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
								array('data' => '', 'width' => '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
								);						
				}
		}
	}
	
	if (($triwulan=='1') or ($jenisrevisi>='3')) {
		$strdes = 'Sebagai berikut :';
		$rows[] = array (
					array('data' => '7.',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan pagu anggaran triwulan',  'width'=> '125px', 'colspan'=>'2', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => $strdes,  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '1.',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Triwulan I',  'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
					array('data' => apbd_fn($tw1_pen),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => apbd_fn($tw1_per),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '2.',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Triwulan II',  'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
					array('data' => apbd_fn($tw2_pen),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => apbd_fn($tw2_per),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
					);
		$rows[] = array (
					array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '3.',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Triwulan III',  'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
					array('data' => apbd_fn($tw3_pen),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => apbd_fn($tw3_per),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'), 
					);
		$rows[] = array (
					array('data' => '',  'width'=> '35px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '4.',  'width'=> '20px', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Triwulan IV',  'width'=> '105px', 'style' => 'border-top: 1px solid darkgray;text-align:left;'),
					array('data' => apbd_fn($tw4_pen),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => apbd_fn($tw4_per),  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;text-align:right;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-left: 1px solid black;border-right: 1px solid black;text-align:right;'),
					);
					
	}
	if ($lainnya=='1')
		$rows[] = array (
					array('data' => '8.',  'width'=> '35px', 'style' => 'border-darkgray: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;text-align:right;'),
					array('data' => 'Ralat/kesalahan administrasi lainnya yang tidak bertentangan dengan ketentuan peraturan perundang-undangan',  'width'=> '125px', 'colspan'=>'2', 'style' => 'border-top: 1px solid darkgray;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Terlampir di RKA',  'width'=> '340px', 'colspan'=>'4', 'style' => 'border-top: 1px solid darkgray;border-bottom: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => '', 'width'=> '35px',  'style' => 'border-top: 1px solid darkgray;border-bottom: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);
					
	//
	} else if ($jenisrevisi=='1') {

		$total_pen=0;
		$total_per=0;
		$rows[] = array (
					array('data' => 'Kode',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Uraian',  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Semula',  'width' => '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Menjadi',  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Bertambah/ Berkurang',  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
					array('data' => 'Keterangan', 'width' => '85px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
					);		

		$sql = 'select kodero,uraian,jumlah from {anggperkegrevisi} where kodekeg=\'%s\' order by kodero';
		$fsql = sprintf($sql, db_escape_string($kodekeg));
		$res = db_query($fsql);
		if ($res) {
			while ($data = db_fetch_object($res)) {
				
				$jumlah_pen = 0;
				$sqlpen = 'select kodero,uraian,jumlah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
				$fsql = sprintf($sqlpen, db_escape_string($kodekeg), db_escape_string($data->kodero));
				$respen = db_query($fsql);
				if ($datapen = db_fetch_object($respen)) {
					$jumlah_pen = $datapen->jumlah;
				}
			
				$total_pen += $jumlah_pen;
				$total_per += $data->jumlah;
				
				$rows[] = array (
							array('data' => $data->kodero,  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
							array('data' => $data->uraian,  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
							array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:rigth;'),
							array('data' => apbd_fn($jumlah_pen),  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
							array('data' => apbd_fn($data->jumlah - $jumlah_pen),  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
							array('data' => '', 'width' => '85px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
							);		
			}

			$rows[] = array (
						array('data' => '',  'width'=> '50px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => 'Total',  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:left;'),
						array('data' => apbd_fn($total_pen),  'width' => '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:rigth;'),
						array('data' => apbd_fn($total_per),  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => apbd_fn($total_per - $total_pen),  'width'=> '100px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;text-align:right;'),
						array('data' => '', 'width' => '85px', 'style' => 'border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black;text-align:left;'),
						);		
			
			
		}							
	}	
	
					
	$rows[] = array (
				array('data' => '', 'colspan'=>'12', 'width' => '875px', 'style' => 'border-top: 1px solid black;text-align:left;'),
				);

	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $tglsurat,  'width'=> '375px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => $pimpinanjabatan,  'width'=> '375px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '375px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '375px', 'style' => ' text-align:center;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => $pimpinannama,  'width'=> '375px', 'style' => ' text-align:center;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '500px', 'colspan'=>'11','style' => 'text-align:left;'),
				array('data' => 'NIP . ' . $pimpinannip,  'width'=> '375px', 'style' => ' text-align:center;'),
				);
	

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}
  
function kegiatanskpd_printusulan2_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tipedok = $form_state['values']['tipedok'];		
	$id = $form_state['values']['id'];
	$topmargin = $form_state['values']['topmargin'];
	//$sampul = $form_state['values']['sampul'];

	//$topmargin = '20';
	//$kodekeg = arg(3);
	//$exportpdf = arg(6);
	//$sampul = arg(7);
	//$tipedok =  'rka'; //arg(5);
	//$hal1 = '9999';

	if ($tipedok=='rpka') {
		$uri = 'apbd/kegiatanskpd/printusulan2/' . $id  . '/'. $topmargin . '/' . $tipedok . '/pdf';
		
		$sql = 'select id from {kegiatanrevisiperubahan} where kodekeg=\'%s\'';
		$fsql = sprintf($sql, db_escape_string($id));
		$res = db_query($fsql);
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$id_x = $data->id;
			}
		}		
		$uri_surat = 'apbd/kegiatanskpd/printusulan2/' . $id_x . '/'. $topmargin . '/' . $tipedok . '/pdf';

		if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsp']) $uri = $uri_surat . '/sp';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitmp']) $uri = $uri_surat . '/mp';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsptjm']) $uri = $uri_surat . '/sptjm';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitapv']) $uri = $uri_surat . '/apv';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitdpasampul']) $uri = $uri_surat . '/sampuldppa';
		
	} else {
		$uri = 'apbd/kegiatanskpd/printusulan2/' . $id . '/'. $topmargin . '/' . $tipedok . '/pdf';
		
		if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsp']) $uri .= '/sp';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitmp']) $uri .= '/mp';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsptjm']) $uri .= '/sptjm';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitapv']) $uri .= '/apv';
		else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitdpasampul']) $uri .= '/sampuldppa';
	}
	
	drupal_goto($uri);
	
}

function PrintPersetujuan($id) {
	 
	$sql = 'select kp.id,kp.jenisrevisi,kp.kodekeg,kp.kodeuk, kp.kodekeg, 
			kp.subjenisrevisi, kp.kodeuk, kp.kodekeg, kp.geserblokir, kp.geserrincian, kp.geserobyek, kp.lokasi, kp.sumberdana, kp.kinerja, kp.sasaran, kp.detiluraian, kp.rab, kp.triwulan, kp.lainnya, kp.nosurat, kp.tglsurat, kp.persetujuanno, kp.persetujuantgl, kr.kegiatan, uk.namauk, uk.namasingkat, uk.pimpinanjabatanlengkap		
			from {kegiatanrevisiperubahan} kp inner join {kegiatanrevisi} kr 
			on kp.kodekeg=kr.kodekeg inner join {unitkerja} uk on kp.kodeuk=uk.kodeuk where kp.id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		
		//$output = 'OK DB.';
		$jenisrevisi = $data->jenisrevisi; 

		if ($jenisrevisi==2) {
			$ralat = '';
			if ($data->lokasi) $ralat .= 'penulisan lokasi, '; 
			if ($data->sumberdana) $ralat .= 'penulisan sumber dana, '; 
			if ($data->kinerja) $ralat .= 'penulisan tolok ukur dan/atau target kinerja, ';  
			if ($data->sasaran) $ralat .= 'penulisan kelompok sasaran kegiatan, ';  
			if ($data->detiluraian) $ralat .= 'penulisan uraian dalam rincian obyek belanja, ';  
			if ($data->rab) $ralat .= 'penulisan satuan, volume dan harga satuan dalam rincian obyek belanja, ';  
			if ($data->triwulan) $ralat .= 'penulisan triwulan, ';  
			if ($data->lainnya) $ralat .= 'lainnya, '; 
		
		} else if ($jenisrevisi==1) {
			$ralat = '';
			$geserblokir = $data->geserblokir; 
			
			$geserrincian =  $data->geserrincian; 
			if ($geserrincian) $ralat .= 'pergeseran antar rincian obyek belanja dalam obyek belanja berkenaan, ';

			$geserobyek = $data->geserobyek;  
			if ($geserobyek) $ralat .= 'pergeseran antar obyek belanja dalam jenis belanja berkenaan, ';
			
		} else if ($jenisrevisi==3) {
		}
		
		$jenisrevisi = $data->jenisrevisi; 
		$subjenisrevisi = $data->subjenisrevisi; 
		
		$kodekeg = $data->kodekeg;
		$kegiatan = strtoupper($data->kegiatan);
		
		$kodeuk = $data->kodeuk; 
		$namauk = $data->namauk;
		$pimpinanjabatanskpd = $data->pimpinanjabatanlengkap;
		 
		$persetujuanno = '..............................................'; 
		$persetujuantgl = '..............................................';
	
		$nosurat = $data->nosurat;
		$tglsurat = $data->tglsurat;
	}

	if (($jenisrevisi==1) and ($geserrincian==1) and ($geserobyek==0)) {
		$kodeukttd='81';
		$pimpinanjabatan = 'PEJABAT PENGELOLA KEUANGAN DAERAH'; 
	} else {
		$kodeukttd='03';
		$pimpinanjabatan = 'SEKRETARIS DAERAH'; 
	}
	
	$sql = sprintf("select pimpinannama,pimpinanpangkat,namauk,kodedinas,pimpinanjabatan,pimpinannip,namauk,header1,header2 from {unitkerja} where kodeuk='%s'", db_escape_string($kodeukttd)) ;
	$res = db_query($sql);
	if ($data = db_fetch_object($res)) {
		//$output .= 'OK DB.';
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> 'PEMERINTAH KABUPATEN JEPARA', 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;'),
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->namauk, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border:none; text-align:center;'),
						array ('data'=> $data->header1, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border:none; text-align:center;')
						);
		$rows[] = array (
						array ('data'=> '', 'width'=>'35px', 'style' =>'border-bottom: 1px solid black; text-align:center;'),
						array ('data'=> $data->header2, 'width'=>'500px', 'colspan'=>'4', 'style' =>'border-bottom: 1px solid black; text-align:center;')
						);

		$rows[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'5', 'style' =>'border:none; text-align:center;'));
		
		$pimpinannama = $data->pimpinannama; 
		$pimpinanpangkat = $data->pimpinanpangkat; 
		$pimpinannip = $data->pimpinannip;

	}
	
	//HEADER
	/*
	$rows[] = array (
				array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => '',  'width'=> '300px', 'style' => 'text-align:left;'),
				);
	*/
	$rows[] = array (
				array('data' => '',  'width'=> '335px', 'colspan'=>'4','style' => 'text-align:left;'),
				array('data' => 'Jepara, ' . $persetujuantgl,  'width'=> '200px', 'style' => 'text-align:left;'),
				);
	

	$rows[] = array (
				array('data' => 'Nomor',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => $persetujuanno, 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Sifat',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Segera', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Lampiran',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '-', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Perihal',  'width'=> '50px', 'style' => 'text-align:left;'),
				array('data' => ':',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'Persetujuan Revisi Anggaran', 'colspan'=>'3', 'width' => '460px', 'style' => 'text-align:left;'),
				);

	
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Kepada',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Yth. ' . $pimpinanjabatanskpd ,  'width'=> '500px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '10px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => 'Di',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => 'J E P A R A',  'width'=> 'Jeparapx', 'style' => 'text-align:left;text-decoration:underline;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '200px', 'style' => 'text-align:left;'),
				array('data' => '',  'width'=> '15px', 'style' => 'text-align:left;'),
				array('data' => '', 'colspan'=>'3', 'width' => '310px', 'style' => 'text-align:left;'),
				);

	
	
	//ISI SURAT
	switch($jenisrevisi) {
		case '1':		//GESER
			
			//1
			$isian = 'Menunjuk Surat Saudara Nomor ' . $nosurat . ' Perihal Usulan Revisi Anggaran tanggal ' . $tglsurat . ' dan berdasarkan Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran, dapat kami sampaikan hal-hal sebagai berikut :';
			$rows[] = array (
						array('data' => $isian, 'colspan'=>'5', 'width' => '535px', 'style' => 'text-align:left;'),
						);
			
			$isian = 'Permohonan Saudara untuk melaksanakan perubahan atau pergeseran dalam hal pagu anggaran tetap untuk jenis ' . $ralat . ' kegiatan ' . $kegiatan . ' dapat kami setujui, hal ini sesuai dengan Pasal 3 Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran.';
			$rows[] = array (
						array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => $isian, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Pergeseran anggaran sebagaimana dimaksud pada angka 1 dilakukan dengan cara mengubah Peraturan Bupati tentang penjabaran APBD dan diformulasikan dalam Dokumen Pelaksanaan Perubahan Anggaran Satuan Kerja Perangkat Daerah sebagai dasar pelaksanaan untuk selanjutnya dianggarkan dalam rancangan peraturan daerah tentang perubahan APBD.', 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Mekanisme pelaksanaan kegiatan tetap berpedoman pada Peraturan dan Ketentuan yang berlaku.', 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			
							
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => 'Demikian untuk menjadikan perhatian dan atas kerjasama yang baik disampaikan terima kasih.', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);

			
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			if ($kodeukttd=='81') {
				$rows[] = array (
							array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
							array('data' => 'KEPALA DPPKAD KABUPATEN JEPARA',  'width'=> '300px', 'style' => ' text-align:center;'),
							);
				$rows[] = array (
							array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
							array('data' => 'Selaku',  'width'=> '300px', 'style' => ' text-align:center;'),
							);
			}
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
						
			break;			
		
		case '2':		//ADM
			//1
			$isian = 'Menunjuk Surat Saudara Nomor ' . $nosurat . ' Perihal Usulan Revisi Anggaran tanggal ' . $tglsurat . ' dan berdasarkan Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran, dapat kami sampaikan hal-hal sebagai berikut :';
			$rows[] = array (
						array('data' => $isian, 'colspan'=>'5', 'width' => '535px', 'style' => 'text-align:left;'),
						);
			
			
			$isian = 'Permohonan Saudara untuk melaksanakan Perubahan/ralat karena kesalahan administrasi untuk Ralat/Kesalahan ' . $ralat . ' Kegiatan ' . $kegiatan . ' dapat kami setujui, hal ini sesuai dengan Pasal 4 Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran';
			$rows[] = array (
						array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => $isian, 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			//$isian = 'Perubahan/ralat karena kesalahan administrasi sebagaimana dimaksud pada angka 1 diformulasikan dalam';
			$isian = 'Perubahan/ralat karena kesalahan administrasi sebagaimana dimaksud pada angka 1 diformulasikan dalam Dokumen Pelaksanaan Perubahan Anggaran Satuan Kerja Perangkat Daerah sebagai dasar pelaksanaan.';
			$rows[] = array (
						array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => $isian, 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			
			
			$rows[] = array (
						array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Mekanisme pelaksanaan kegiatan tetap berpedoman pada Peraturan dan Ketentuan yang berlaku.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			
							
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => 'Demikian untuk menjadikan perhatian dan atas kerjasama yang baik disampaikan terima kasih.', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);

			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			
			break;			
		
		case '3':		//DAK
			
			//1
			$isian = 'Menunjuk Surat Saudara Nomor ' . $nosurat . ' Perihal Usulan Revisi Anggaran tanggal ' . $tglsurat . ' dan berdasarkan Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran, dapat kami sampaikan hal-hal sebagai berikut :';
			$rows[] = array (
						array('data' => $isian, 'colspan'=>'5', 'width' => '535px', 'style' => 'text-align:left;'),
						);
			
			$isian = 'Permohonan Saudara untuk melaksanakan Penambahan, pengurangan dan/atau pagu angggaran tetap yang dananya bersumber dari transfer Pemerintah Provinsi dan/atau Pemerintah kegiatan ' . $kegiatan . ' dapat kami setujui, hal ini sesuai dengan Pasal 5 Peraturan Bupati Jepara Nomor 6 Tahun 2014 Tentang Tata Cara Revisi Anggaran.';
			$rows[] = array (
						array('data' => '1.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => $isian, 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Pergeseran anggaran sebagaimana dimaksud pada angka 1 dilakukan dengan cara mengubah Peraturan Bupati tentang penjabaran APBD dan diformulasikan dalam Dokumen Pelaksanaan Perubahan Anggaran Satuan Kerja Perangkat Daerah sebagai dasar pelaksanaan untuk selanjutnya dianggarkan dalam rancangan peraturan daerah tentang perubahan APBD.', 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '3.',  'width'=> '20px', 'style' => 'text-align:left;'),
						array('data' => 'Mekanisme pelaksanaan kegiatan tetap berpedoman pada Peraturan dan Ketentuan yang berlaku.', 'colspan'=>'3', 'width' => '515px', 'style' => 'text-align:left;'),
						);
			
							
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => 'Demikian untuk menjadikan perhatian dan atas kerjasama yang baik disampaikan terima kasih.', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);
			$rows[] = array (
						array('data' => '', 'colspan'=>'5', 'width' => '525px', 'style' => 'text-align:left;'),
						);

			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinanjabatan,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => '',  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => $pimpinannama,  'width'=> '300px', 'style' => ' text-align:center;text-decoration: underline;'),
						);
			$rows[] = array (
						array('data' => '',  'width'=> '235px', 'colspan'=>'4','style' => 'text-align:left;'),
						array('data' => 'NIP . ' . $pimpinannip,  'width'=> '300px', 'style' => ' text-align:center;'),
						);
			
			break;			
		
		case '4':
			
			break;			
	}
		
	
	$rows[] = array (
				array('data' => '',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => 'Tembusan :',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;text-decoration: underline;'),
				);
	$rows[] = array (
				array('data' => '1. Bupati Jepara (sebagai laporan)',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '2. Inspektorat',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '3. Arsip',  'width'=> '535px', 'colspan'=>'5','style' => 'text-align:left;'),
				);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	
	return $output;
	//return 'Halo';	
}
 
function GenReportFormSampulBelanja($kodekeg,$revisi) {
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.lokasi, k.jenis, k.totalp total, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, 
				uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip 
				from {kegiatanperubahan} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodeuk = $data->kodeuk;
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan; 
		
		$kodekeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$total = $data->total;
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
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	if ($dpano !='') {
		$tahun = variable_get('apbdtahun', 0);
		
		if ($jenis==1)
			$pquery = sprintf('select dpabtlformat'.$revisi.' dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
		else
			$pquery = sprintf('select dpablformat'.$revisi.' dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
			
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($pres) {
			if ($data = db_fetch_object($pres)) {
				$dpaformat = $data->dpaformat;
			}
		}
		
		$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
		$dpanolengkap = str_replace('NOKEG',$kodekeg,$dpanolengkap);
		
	} else 
		$dpanolengkap = '........................';
	
	$rows[] = array (array ('data'=>'', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'DOKUMEN PELAKSANAAN PERUBAHAN ANGGARAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPPA-SKPD)', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array ('data'=>'B E L A N J A' . $strjenis . '  -  L A N G S U N G', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'NO. DPPA-SKPD : ' . $dpanolengkap, 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array ( 
				array('data' => '',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'URUSAN PEMERINTAHAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $urusan, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'ORGANISASI',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $organisasi, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'PROGRAM',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($program), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'KEGIATAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($kegiatan), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	if ($jenis==2)
		$rows[]= array (
					array('data' => 'LOKASI',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
					array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
					array('data' => strtoupper($lokasi), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
				);
	$rows[]= array (
				array('data' => 'SUMBER DANA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $sumberdana1, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'JUMLAH ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ' . apbd_fn($total) . ',00', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => 'TERBILANG',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($total)), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$rows[]= array (
				array('data' => 'PENGGUNA ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
				array('data' => '', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NAMA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannama, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- NIP',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannip, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '- JABATAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinanjabatan, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}

?>