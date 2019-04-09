<?php
function lapkegkecamatan_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = arg(4);
	$kodeuk = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	$exportpdf = arg(8);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	if ($revisi=='9') {
		$system_revisi =  variable_get('apbdrevisi', 1);
		$str_revisi = 'Terakhir (#' . $system_revisi . ')';		
		
		
	} else
		$str_revisi = '#' . $revisi;
	drupal_set_title('Kegiatan Kecamatan - Revisi ' . $str_revisi);
	
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-lapkegkecamatan-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodeuk);
		$htmlContent = GenReportFormContent($kodeuk,$revisi);
		$htmlFooter = '';
		
		apbd_ExportPDF3P_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile, $hal1);
		
	} else {
		$output = drupal_get_form('lapkegkecamatan_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($kodeuk,$revisi);
		return $output;
	}

}

function GenReportForm($kodeuk,$revisi) {
	
	$pquery = sprintf("select namauk from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kecamatan = strtoupper($data->namauk);
		$kecamatansql = strtolower($data->namauk);
		
		$kecamatansql = str_replace('kecamatan ', '', $kecamatansql);
	}
		
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'DAFTAR KEGIATAN DI ' . $kecamatan , 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN 2016', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	
	$headersrek[] = array (
						 
						 array('data' => 'No',  'width'=> '10px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Kegiatan',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Dinas',  'width' => '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	if ($kecamatansql=='batealit')
		$whereuk = " and lower(k.lokasi) like '%alit%'";
	else if ($kecamatansql=='nalumsari')
		$whereuk = " and lower(k.lokasi) like '%ums%'";
	else if ($kecamatansql=='bangsri')
		$whereuk = " and lower(k.lokasi) like '%gs%'";
	else if ($kecamatansql=='donorojo')
		$whereuk = " and lower(k.lokasi) like '%rojo%'";
	else
		$whereuk = sprintf(" and lower(k.lokasi) like lower('%%%s%%') ", $kecamatansql);
	
	$where = " and k.kodepro>'010' and k.kodekeg in (select kodekeg from anggperkeg where mid(kodero,1,2)='52' or mid(kodero,1,2)='53') ";
						 
	$sql = 'select k.kodekeg,k.kegiatan,k.lokasi,k.total,uk.namasingkat from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk where uk.kelompok<=1 and k.jenis=2 and k.total>0 ' . $whereuk . $where . ' order by k.total';
	 
	//drupal_set_message($sql);
	$no = 0;
	$total = 0;
	$result = db_query($sql);
	if ($result) {
		//drupal_set_message('ok');
		while ($data = db_fetch_object($result)) {
			$no++;
			//drupal_set_message($no);
			$total = $total + $data->total;
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '10px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $data->kegiatan,  'width' => '200px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->namasingkat,  'width' => '75px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => str_replace('||',', ', $data->lokasi),  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
			//drupal_set_message( $fsql);
		}	//while belanja

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '10px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => 'TOTAL',  'width' => '200px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => '',  'width' => '75px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
							 );

	}

	/*
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $no, 'width' => '370px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Jumlah Anggaran',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($total),  'width' => '370px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );
	
	*/
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	return $output;	
}

function GenReportFormHeader($kodeuk) {
	
	$pquery = sprintf("select namauk from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kecamatan = strtoupper($data->namauk);
		$kecamatansql = strtolower($data->namauk);
		
		$kecamatansql = str_replace('kecamatan ', '', $kecamatansql);
	}
		
	$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>$kecamatan , 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> 'DAFTAR KEGIATAN APBD TAHUN 2016', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> '', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; text-align:center;'));
	
	/*
	if ($kecamatansql=='batealit')
		$whereuk = " and lower(k.lokasi) like '%alit%'";
	else if ($kecamatansql=='nalumsari')
		$whereuk = " and lower(k.lokasi) like '%ums%'";
	else if ($kecamatansql=='bangsri')
		$whereuk = " and lower(k.lokasi) like '%gs%'";
	else
		$whereuk = sprintf(" and lower(k.lokasi) like lower('%%%s%%') ", $kecamatansql);
	
	$where = " and k.kodepro>'010' and k.kodekeg in (select kodekeg from anggperkeg where mid(kodero,1,2)='52' or mid(kodero,1,2)='53') ";
						 
	$sql = 'select count(k.kodekeg) jumlahkeg, sum(k.total) totalanggaran from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk where uk.kelompok<=1 and k.jenis=2 and k.total>0 ' . $whereuk . $where . ' order by k.total';
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$jumlahkeg = $data->jumlahkeg;
		$totalanggaran = $data->totalanggaran;
	}
	*/
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($kodeuk,$revisi) {
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;

	$pquery = sprintf("select namauk from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kecamatansql = strtolower($data->namauk);		
		$kecamatansql = str_replace('kecamatan ', '', $kecamatansql);
	}
		
	$headersrek[] = array (
						 
						 array('data' => 'NO',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'KEGIATAN',  'width' => '170px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'LOKASI',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'ANGGARAN',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	if ($kecamatansql=='batealit')
		$whereuk = " and lower(k.lokasi) like '%alit%'";
	else if ($kecamatansql=='nalumsari')
		$whereuk = " and lower(k.lokasi) like '%ums%'";
	else if ($kecamatansql=='bangsri')
		$whereuk = " and lower(k.lokasi) like '%gs%'";
	else if ($kecamatansql=='donorojo')
		$whereuk = " and lower(k.lokasi) like '%rojo%'";
	else
		$whereuk = sprintf(" and lower(k.lokasi) like lower('%%%s%%') ", $kecamatansql);
	
	$where = " and k.kodepro>'010' and k.kodekeg in (select kodekeg from {anggperkeg} where mid(kodero,1,2)='52' or mid(kodero,1,2)='53') ";
						 
	$sql = 'select k.kodekeg,k.kegiatan,k.lokasi,uk.namasingkat,k.total from {kegiatanskpd} k inner join {unitkerja} uk on k.kodeuk=uk.kodeuk where uk.kelompok<=1 and k.jenis=2 and k.total>0 ' . $whereuk . $where . ' order by k.total';
	 
	//drupal_set_message($sql);
	$no = 0;
	$total = 0;
	$result = db_query($sql);
	if ($result) {
		//drupal_set_message('ok');
		while ($data = db_fetch_object($result)) {
			$no++;
			//drupal_set_message($no);
			$total = $total + $data->total;
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $data->kegiatan,  'width' => '170px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->namasingkat,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => str_replace('||',', ', $data->lokasi),  'width' => '150px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
			//drupal_set_message( $fsql);
		}	//while belanja

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => 'TOTAL',  'width' => '170px', 'style' => 'border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;border-top: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($total),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
							 );

	}

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
		
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

function lapkegkecamatan_form () {
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
	$exportpdf = arg(8);
	 
	if ($topmargin=='') $topmargin=10;
	if ($hal1=='') $hal1=1;

	$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where iskecamatan=1 order by kodedinas" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] = '--PILIH KECAMATAN--';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'Kecamatan',
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
		//'#weight' => 5,
	);
	$form['formdata']['sst'] = array (
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

function lapkegkecamatan_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporan/apbd/lapkegkecamatan/' .$revisi.'/'. $kodeuk . '/' . $topmargin . '/' . $hal1 ;
	else	
		$uri = 'apbd/laporan/apbd/lapkegkecamatan/' .$revisi.'/'. $kodeuk . '/' . $topmargin . '/' . $hal1 . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>