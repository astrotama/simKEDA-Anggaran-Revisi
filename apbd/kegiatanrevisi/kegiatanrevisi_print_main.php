<?php
function kegiatanrevisi_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = '20';
	$id = arg(3);
	$exportpdf = arg(6);
	$sampul = arg(7);
	$tipedok = 'rka'; //arg(5);
	$hal1 = '9999';

	if (isset($topmargin)) $topmargin = arg(4);

	////drupal_set_message($kodekeg);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		if (isset($sampul))  {

			if ($sampul=='sp') {
				$pdfFile = 'surat-permohonan-revisi-' . $id . '.pdf';
				$htmlContent = PrintSuratPermohonan($id);
				
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, true, 'P');
				
			} else if ($sampul=='mp') {

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
				//PrintMatriksSPTJM
			} else if ($sampul=='sptjm') {
				$pdfFile = 'sptjm-permohonan-revisi-' . $id . '.pdf';
				$htmlContent = PrintMatriksSPTJM($id);
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, true, 'P');

			} else if ($sampul=='sampuldppa') {
				$pdfFile = 'sampul-dppa-' . $id . '.pdf';
				$htmlContent = GenReportFormSampulBelanja($id);
				apbd_ExportPDF3P_Surat($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1, true, 'P');
				
			} else {
				//$pdfFile = 'dpa-skpd-sampul.pdf';
				//$htmlContent = GenReportFormSampulDepan($kodekeg);
				//apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
			}
			
		} else {
			//require_once('test.php');
			//myt();

			$sql = 'select kodekeg from {kegiatanrevisiperubahan} where id=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array ($id));
			$isL =false;
			if ($data = db_fetch_object($res)) {
				$kodekeg = $data->kodekeg;	
			}
			
			$pdfFile = 'rka-skpd-revisi-' . $kodekeg . '.pdf';
 
			//$htmlContent = GenReportForm(1);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok);
			$htmlContent = GenReportFormContent($kodekeg);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok);
			
			//$output = drupal_get_form('kegiatanrevisi_print_form');
			//$output .= $htmlContent;
			//return $output;
			
			apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
		}
		
	} else {
		 
		$output = drupal_get_form('kegiatanrevisi_print_form');
		$output .= getDescription($id);
		return $output;
	}
	
}

function getDescription($id){
	$sql = 'select kr.kegiatan from {kegiatanrevisiperubahan} kp inner join {kegiatanrevisi} kr on kp.kodekeg=kr.kodekeg where kp.id=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($id));
	if ($data = db_fetch_object($res)) {
		$kegiatan = strtoupper($data->kegiatan);	
	}
	
	$rows[]= array (
				array('data' => '+ Kegiatan : ' . $kegiatan, 'style' => 'border-none; text-align:left;'),
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
		$masukantarget_pen = $data->masukantarget;
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
		$masukantarget = $data->masukantarget;
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
	if ($jenis==1) $strjenis = 'T I D A K  -  ';
	if ($tipedok=='dpa') {
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARAN', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'9', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
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
		ini_set('memory_limit', '1024M');
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

		$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
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
							 array('data' => 'Anggaran (Rp)',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
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
		//if ($total > $plafon)	
		//	$strplafon = '!!!ANGGARAN MELEBIHI PLAFON, HARAP DIPERBAIKI!!!';

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
	if ($jenisrevisi=='1') {
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
	
	$pquery = sprintf('select lokasi, tw1, tw2, tw3, tw4, kegiatan, sumberdana1, kelompoksasaran, p.kodepro, p.program, k.total, k.nomorkeg  from {kegiatanrevisi} k inner join {program} p on k.kodepro=p.kodepro where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
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

function kegiatanrevisi_print_form () {
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
	$tipedok =  arg(5);

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
	$form['formdata']['sampul']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $sampul, 
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
	$form['formdata']['submitsptjm'] = array (
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => 'SPTJM'
	);		
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
				array('data' => 'Usulan revisi anggaran kegiatan ' . $kegiatanuraian . ' telah disusun sesuai dengan ketentuan sebagaimana diatur dengan Peraturan Bupati Nomor 6 Tahun 2014 tentang Tata Cara Revisi Anggaran', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
				);
	$rows[] = array (
				array('data' => '2.',  'width'=> '20px', 'style' => 'text-align:left;'),
				array('data' => 'Dokumen-dokumen yang dipersyaratkan dalam rangka revisi anggaran telah disusun dengan lengkap dan benar, telah kami simpan (arsipkan) dan siap diaudit sewaktu-waktu.', 'colspan'=>'4', 'width' => '515px', 'style' => 'text-align:left;'),
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

function kegiatanrevisi_print_form_submit($form, &$form_state) {
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

	$uri = 'apbd/kegiatanskpd/printusulan/' . $id . '/'. $topmargin . '/rka/pdf';
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsp']) $uri .= '/sp';
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitmp']) $uri .= '/mp';
	else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsptjm']) $uri .= '/sptjm';
	
	
	drupal_goto($uri);
	
}

function GenReportFormSampulBelanja($kodekeg) {
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.lokasi, k.jenis, k.total, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, 
				uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip 
				from {kegiatanrevisi} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
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
		$pquery = sprintf('select btlno dpano from {dpanomor1} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	else
		$pquery = sprintf('select blno dpano from {dpanomor1} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	if ($dpano !='') {
		$tahun = variable_get('apbdtahun', 0);
		
		if ($jenis==1)
			$pquery = sprintf('select dpabtlformat1 dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
		else
			$pquery = sprintf('select dpablformat1 dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
			
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
	$rows[] = array (array ('data'=>'DOKUMEN PERUBAHAN PELAKSANAAN ANGGARAN', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPA-SKPD)', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array ('data'=>'TAHUN ANGGARAN 2017', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
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