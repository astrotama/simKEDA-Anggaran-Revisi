<?php
    
function pendapatan_edit_form(){
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanpad.js');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Rekening Pendapatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	$kodeuk = arg(3);
	$kodero = arg(4);
	//drupal_set_message($kodeuk);
	
	if (!isSuperuser())
		$kodeuk = apbd_getuseruk();
		
	//drupal_set_message($kodeuk);
	
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	//drupal_set_message($tahun);

	$title = 'Rekening Pendapatan';

	
	$allowedit = (batastgl() || (isSuperuser()));	

	if ($allowedit==false) {
		//dispensasippas
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasippas from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasippas;
			}
		}
	}
	
	//$title =l($title, 'apbd/kegiatanskpd/rekening/' . $kodeuk, array('html'=>true));
	drupal_set_title($title);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;

	$jumlah = 0;
	$jumlahsebelum = 0;
	$jumlahsesudah = 0;
	//$uraian = 'Isikan rekening dengan mengklik tombol Pilih disamping';
	
    if (isset($kodero))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
			
        $sql = 'select tahun,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah,ketrekening from {anggperuk} 
			   where tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodero));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$tahun = $data->tahun;
				$kodero = $data->kodero;
				$uraian = $data->uraian;
				$jumlah = $data->jumlah;
				$jumlahsebelum = $data->jumlahsebelum;
				$jumlahsesudah = $data->jumlahsesudah;
				$ketrekening = $data->ketrekening;
                $disabled =TRUE;
			} else {
				$kodero = '';
			}
        } else {
			$kodero = '';
		}
    } else {
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Rekening Pendapatan';
		$kodero = '';
	}
    
	$form['formdata']['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		'#default_value'=> $tahun, 
	); 	
	$form['formdata']['e_kodero']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 

	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$skpd = array();
	$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$skpd[$data->kodeuk] = $data->namasingkat;
	}
	$skpdtype = 'hidden';
	if (isSuperuser() || isAdministrator())
		$skpdtype='select';

	$form['formdata']['kodeuk']= array(
		'#type'         => $skpdtype, 
		'#title'        => 'SKPD',
		'#options'		=> $skpd,
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
		
	$form['formdata']['kegiatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rekening', 
		//'#description'  => 'uraian', 
		'#maxlength'    => 255, 
		'#size'         => 70, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianpendapatan',
		'#default_value'=> $uraian, 
	); 
	$form['formdata']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi rekening dengan memilih menggunakan tombol Pilih</font></span>",
	);

	$form['formdata']['nk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 
	$form['formdata']['ketrekening']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Dasar Hukum', 
		//'#description'  => 'Rekening belanja', 
		'#maxlength'    => 255, 
		'#size'         => 70, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ketrekening, 
	); 
	
	$form['formdata']['jumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#disabled'     => true, 
		'#description'  => 'Jumlah anggaran pendapatan, akan terisi saat detil rekening diisikan', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlah, 
	); 
	$form['formdata']['jumlahsebelum']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Tahun Lalu',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah anggaran pendapatan tahun lalu, seandainya ada', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsebelum, 
	); 
	$form['formdata']['jumlahsesudah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Depan',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah perkiraan anggaran pendapatan tahun depan, seandainya ada', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsesudah, 
	); 
	
	//drupal_set_message($kodero); 
	if ($kodero == '') {
		$form['formdata']['detil1'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #1',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil1']['uraian1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			//'#description'  => 'Uraian detil pendapatan', 
			'#size'         => 69, 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil1']['keterangan1'] = array (
			'#type' => 'markup',
			'#value' => "<span><font size='1'>Uraian detil pendapatan. Uraian HARUS DIISI bila memang ada, bila tidak diisi maka semua isian unit, volume dan harga akan diabaikan. Bila merupakan PENGELOMPOKAN maka uraian tersebut bisa mempunyai sub detil pendapatan.</font></span>",
		);			
		
		$form['formdata']['detil1']['pengelompokan1']= array(
			'#type'         => 'hidden', 
			'#default_value'=> false, 
		); 		
		$form['formdata']['detil1']['unitjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#description'  => 'Jumlah detil pendapatan',
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil1']['unitsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#description'  => 'Satuan detil pendapatan',
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil1']['volumjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#description'  => 'Volume detil pendapatan',
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil1']['volumsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#description'  => 'Satuan volume detil pendapatan',
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil1']['harga1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#description'  => 'Harga per satuan detil pendapatan, sedangkan harga totalnya adalah [Unit Jumlah] x [Volume Jumlah] x [Harga]',

			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 

		$form['formdata']['detil2'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #2',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil2']['uraian2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 69, 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil2']['pengelompokan2']= array(
			'#type'         => 'hidden', 
			'#default_value'=> false, 
		); 
		
		$form['formdata']['detil2']['unitjumlah2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil2']['unitsatuan2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil2']['volumjumlah2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil2']['volumsatuan2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil2']['harga2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		

		$form['formdata']['detil3'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #3',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil3']['uraian3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 69, 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil3']['pengelompokan3']= array(
			'#type'         => 'hidden', 
			'#default_value'=> false, 
		); 
		
		$form['formdata']['detil3']['unitjumlah3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil3']['unitsatuan3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil3']['volumjumlah3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil3']['volumsatuan3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil3']['harga3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 	
		
		$form['formdata']['detil4'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #4',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil4']['uraian4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 69, 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil4']['pengelompokan4']= array(
			'#type'         => 'hidden', 
			'#default_value'=> false, 
		); 
		
		$form['formdata']['detil4']['unitjumlah4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil4']['unitsatuan4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil4']['volumjumlah4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil4']['volumsatuan4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil4']['harga4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		

		$form['formdata']['detil5'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #5',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil5']['uraian5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 69, 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil5']['pengelompokan5']= array(
			'#type'         => 'hidden', 
			'#default_value'=> false, 
		); 
		
		$form['formdata']['detil5']['unitjumlah5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil5']['unitsatuan5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil5']['volumjumlah5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil5']['volumsatuan5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil5']['harga5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		
	
	//Edit lama, dikasih Next dan Prev
	} else {

		//PREV
		$sql = 'select kodero from {anggperuk} where tahun=\'%s\' and kodeuk=\'%s\' and kodero<\'%s\' order by kodero desc limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$prevkode=$data->kodero;

				$form['formdata']['prevkode']= array(
					'#type'         => 'hidden', 
					'#default_value'=> $prevkode, 
				);				
				
				
				$form['formdata']['submitprev'] = array (
					'#type' => 'submit',
					'#value' => '<<',
				); 
				
			}
		}	
		
		//NEXT
		$sql = 'select kodero from {anggperuk} where tahun=\'%s\' and kodeuk=\'%s\' and kodero>\'%s\' order by kodero limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$nextkode=$data->kodero;

				$form['formdata']['nextkode']= array(
					'#type'         => 'hidden', 
					'#default_value'=> $nextkode, 
				);				

				$form['formdata']['submitnext'] = array (
					'#type' => 'submit',
					'#value' => '>>',
				);
				
			}
		}
		
		//Detil RAB
		$form['formdata']['submitrab'] = array (
			'#type' => 'submit',
			'#value' => 'Detil',
		);		
	
	}
	
	if ($allowedit) {
		$form['formdata']['submitnewdetil'] = array (
			'#type' => 'submit',
			'#value' => 'Tambah Detil',
		);
		
		$form['formdata']['submitnew'] = array (
			'#type' => 'submit',
			'#value' => 'Tambah Rekening',
		);
		
		
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/pendapatan/' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
	}
    return $form;
}
function pendapatan_edit_form_validate($form, &$form_state) {
	$uraian = $form_state['values']['kegiatan'];
	$kodero = $form_state['values']['nk'];
	
	$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];
	$e_kodero = $form_state['values']['e_kodero'];

	if ($kodero=='') {		
		if ($uraian =='') {
			form_set_error('', 'Rekening belum diisi');
			
		} else {			//Rekening diisi dari mengetik
			$sql = 'select kodero from {rincianobyek} where uraian=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array ($uraian));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {
					$kodero = $data->kodero;
				} else {
					form_set_error('', 'Rekening tidak diisi/dipilih dengan benar');
				}
			} 
		}
	}
	
	if ($e_kodero != $kodero) {		//Rekening baru
		$sql = 'select kodero from {anggperuk} where tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {		
				form_set_error('', 'Rekening sudah digunakan dalam SKPD ini');
			}
		}
	} else {
		
	}

	//CEK PER 1000
	$seribu = 1000;

	//Hitung detilnya dulu
	$uraian1 = $form_state['values']['uraian1'];
	if ($uraian1 != '') {
		$unitjumlah1 = $form_state['values']['unitjumlah1'];
		$volumjumlah1 = $form_state['values']['volumjumlah1'];
		$harga1 = $form_state['values']['harga1'];
		
		$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
	}
	//CEK PER 1000
	if (($total1 % $seribu)>0) form_set_error('', 'Isian detil rekening #1 tidak bulat per seribu');
	
	$uraian2 = $form_state['values']['uraian2'];
	if ($uraian2 != '') {
		$unitjumlah2 = $form_state['values']['unitjumlah2'];
		$volumjumlah2 = $form_state['values']['volumjumlah2'];
		$harga2 = $form_state['values']['harga2'];
		
		$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
	}
	//CEK PER 1000
	if (($total2 % $seribu)>0) form_set_error('', 'Isian detil rekening #2 tidak bulat per seribu');

	$uraian3 = $form_state['values']['uraian3'];
	if ($uraian3 != '') {
		$unitjumlah3 = $form_state['values']['unitjumlah3'];
		$volumjumlah3 = $form_state['values']['volumjumlah3'];
		$harga3 = $form_state['values']['harga3'];
		
		$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
	}
	//CEK PER 1000
	if (($total3 % $seribu)>0) form_set_error('', 'Isian detil rekening #3 tidak bulat per seribu');

	$uraian4 = $form_state['values']['uraian4'];
	if ($uraian4 != '') {
		$unitjumlah4 = $form_state['values']['unitjumlah4'];
		$volumjumlah4 = $form_state['values']['volumjumlah4'];
		$harga4 = $form_state['values']['harga4'];
		
		$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
	}
	//CEK PER 1000
	if (($total4 % $seribu)>0) form_set_error('', 'Isian detil rekening #4 tidak bulat per seribu');

	$uraian5 = $form_state['values']['uraian5'];
	if ($uraian5 != '') {
		$unitjumlah5 = $form_state['values']['unitjumlah5'];
		$volumjumlah5 = $form_state['values']['volumjumlah5'];
		$harga5 = $form_state['values']['harga5'];
		
		$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
	}
	//CEK PER 1000
	if (($total5 % $seribu)>0) form_set_error('', 'Isian detil rekening #5 tidak bulat per seribu');	
}

function pendapatan_edit_form_submit($form, &$form_state) {
	
	$kodeuk = $form_state['values']['kodeuk'];
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/pendapatan/edit/' . $kodeuk . '/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/pendapatan/edit/' . $kodeuk . '/' . $prevkode ;
		//drupal_set_message('Next');

    } else if($form_state['clicked_button']['#value'] == $form_state['values']['submitrab']) {
		$kodeuk = $form_state['values']['kodeuk'];
		$kodero = $form_state['values']['nk'];
        $form_state['redirect'] = 'apbd/pendapatan/detil/' . $kodeuk . '/' . $kodero;
		//drupal_set_message('Next');

	} else {
		
		$e_kodero = $form_state['values']['e_kodero'];			

		$kodeuk = $form_state['values']['kodeuk'];
		$tahun = $form_state['values']['tahun'];
		
		$uraian = $form_state['values']['kegiatan'];
		$ketrekening = $form_state['values']['ketrekening'];
		$kodero = $form_state['values']['nk'];
		
		$jumlah = $form_state['values']['jumlah'];
		$jumlahsebelum = $form_state['values']['jumlahsebelum'];
		$jumlahsesudah = $form_state['values']['jumlahsesudah'];
		
		if (($kodero=='') and ($uraian !='')) {		//Rekening diisi dari mengetik
			$sql = 'select kodero from {rincianobyek} where uraian=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array ($uraian));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {
					
					$kodero = $data->kodero;
				} else {
					$kodero = '';
				}
			} else {
				$kodero = '';
			}
		}
		
		if ($e_kodero=='')
		{
			
			//Hitung detilnya dulu
			$uraian1 = $form_state['values']['uraian1'];
			if ($uraian1 != '') {
				$pengelompokan1 = $form_state['values']['pengelompokan1'];
				$unitjumlah1 = $form_state['values']['unitjumlah1'];
				$unitsatuan1 = $form_state['values']['unitsatuan1'];
				$volumjumlah1 = $form_state['values']['volumjumlah1'];
				$volumsatuan1 = $form_state['values']['volumsatuan1'];
				$harga1 = $form_state['values']['harga1'];
				
				$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
				$totalrekening = $total1;
			}
			$uraian2 = $form_state['values']['uraian2'];
			if ($uraian2 != '') {
				$pengelompokan2 = $form_state['values']['pengelompokan2'];
				$unitjumlah2 = $form_state['values']['unitjumlah2'];
				$unitsatuan2 = $form_state['values']['unitsatuan2'];
				$volumjumlah2 = $form_state['values']['volumjumlah2'];
				$volumsatuan2 = $form_state['values']['volumsatuan2'];
				$harga2 = $form_state['values']['harga2'];
				
				$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
				$totalrekening += $total2;
			}
			$uraian3 = $form_state['values']['uraian3'];
			if ($uraian3 != '') {
				$pengelompokan3 = $form_state['values']['pengelompokan3'];
				$unitjumlah3 = $form_state['values']['unitjumlah3'];
				$unitsatuan3 = $form_state['values']['unitsatuan3'];
				$volumjumlah3 = $form_state['values']['volumjumlah3'];
				$volumsatuan3 = $form_state['values']['volumsatuan3'];
				$harga3 = $form_state['values']['harga3'];
				
				$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
				$totalrekening += $total3;
			}
			$uraian4 = $form_state['values']['uraian4'];
			if ($uraian4 != '') {
				$pengelompokan4 = $form_state['values']['pengelompokan4'];
				$unitjumlah4 = $form_state['values']['unitjumlah4'];
				$unitsatuan4 = $form_state['values']['unitsatuan4'];
				$volumjumlah4 = $form_state['values']['volumjumlah4'];
				$volumsatuan4 = $form_state['values']['volumsatuan4'];
				$harga4 = $form_state['values']['harga4'];
				
				$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
				$totalrekening += $total4;
			}
			$uraian5 = $form_state['values']['uraian5'];
			if ($uraian5 != '') {
				$pengelompokan5 = $form_state['values']['pengelompokan5'];
				$unitjumlah5 = $form_state['values']['unitjumlah5'];
				$unitsatuan5 = $form_state['values']['unitsatuan5'];
				$volumjumlah5 = $form_state['values']['volumjumlah5'];
				$volumsatuan5 = $form_state['values']['volumsatuan5'];
				$harga5 = $form_state['values']['harga5'];
				
				$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
				$totalrekening += $total5;
			}

			$sql = 'insert into {anggperuk} (tahun,kodeuk,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah,ketrekening) 
				   values (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($tahun, $kodeuk, $kodero,$uraian, $totalrekening, $jumlahsebelum, $jumlahsesudah, $ketrekening));
			
			
			//Simpan detilnya
			$sql = 'insert into {anggperukdetil} (tahun, kodero, kodeuk, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, pengelompokan) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			if ($res) 
				if ($uraian1 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $kodeuk, $uraian1, $unitjumlah1, $unitsatuan1, $volumjumlah1, $volumsatuan1, $harga1, 
							$unitjumlah1 * $volumjumlah1 * $harga1, $pengelompokan1));	
				}
			
			if ($res) 
				if ($uraian2 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $kodeuk, $uraian2, $unitjumlah2, $unitsatuan2, $volumjumlah2, $volumsatuan2, $harga2, 
							$unitjumlah2 * $volumjumlah2 * $harga2, $pengelompokan2));	
				}

			if ($res) 
				if ($uraian3 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $kodeuk, $uraian3, $unitjumlah3, $unitsatuan3, $volumjumlah3, $volumsatuan3, $harga3, 
							$unitjumlah3 * $volumjumlah3 * $harga3, $pengelompokan3));	
				}

			if ($res) 	
				if ($uraian4 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $kodeuk, $uraian4, $unitjumlah4, $unitsatuan4, $volumjumlah4, $volumsatuan4, $harga4, 
							$unitjumlah4 * $volumjumlah4 * $harga4, $pengelompokan4));	
				}
			
			if ($res) 
				if ($uraian5 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $kodeuk, $uraian5, $unitjumlah5, $unitsatuan5, $volumjumlah5, $volumsatuan5, $harga5, 
							$unitjumlah5 * $volumjumlah5 * $harga5, $pengelompokan5));	
				}	
			
			
		} else {
			$sql = 'update {anggperuk} set uraian=\'%s\', kodero=\'%s\', jumlahsebelum=\'%s\', jumlahsesudah=\'%s\', ketrekening=\'%s\' where tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($uraian, $kodero, $jumlahsebelum, $jumlahsesudah, $ketrekening, $tahun, $kodeuk, $e_kodero));
		}
		
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
			$nextkode = $form_state['values']['nextkode'];
			$form_state['redirect'] = 'apbd/pendapatan/edit/' . $kodeuk ;

		} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitnewdetil']) {
			$form_state['redirect'] = 'apbd/pendapatan/detil/edit/' . $kodeuk . '/' . $kodero  ;
			
		} else {
			
			if ($res)
				drupal_set_message('Penyimpanan data berhasil dilakukan');
			else
				drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
			drupal_goto('apbd/pendapatan/');    
		}
	}
}
?>