<?php
function kegiatanskpd_editadmin_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    $kodekeg = arg(3);
	$jenis = 2;
	$kodeuk = apbd_getuseruk();
	if (isSuperuser())
		$kodeuk = $_SESSION['kodeuk'];
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);

	$total = 0;
	$plafon = 0;
	
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
    $disabled = FALSE;
    if (isset($kodekeg))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
		

		//PENETAPAN
        $sql = 'select total, plafon from {kegiatanskpd} where kodekeg=\'%s\'' ;
		$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$total_pen = $data->total;
				$plafon_pen = $data->plafon;				
			}
		}
		
        $sql = 'select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, k.kegiatan, k.total, k.plafon, k.jenis, k.inaktif, k.isppkd,
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.dispensasi, p.program 
				from {kegiatanperubahan} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' ;
				
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodepro = $data->kodepro;
				$program = $data->program;
				$kodeuk = $data->kodeuk;
				$kegiatan = $data->kegiatan ;
				
				$jenis = $data->jenis; 
				$isppkd = $data->isppkd;
				
				$total = $data->total;
				$plafon = $data->plafon;
				
				$sumberdana1 = $data->sumberdana1;
				//$sumberdana2 = $data->sumberdana2;
				$sumberdana1rp = $data->sumberdana1rp;
				//$sumberdana2rp = $data->sumberdana2rp;
				
				$dispensasi = $data->dispensasi;
				
				$inaktif = $data->inaktif;

				
				$disabled =TRUE;
			} else {
				$kodekeg = '';
				$kegiatan = 'Kegiatan Baru';
			}
        } else {
			$kodekeg = '';
			$kegiatan = 'Kegiatan Baru';
		}
    } else {
		//$form['formdata']['#title'] = 'Tambah Data Kegiatan Renja SKPD';
		$kodekeg = '';
		$kegiatan = 'Kegiatan Baru';
	
		//if (!user_access('kegiatanskpd tambah'))
		//	drupal_access_denied();
    }

	//drupal_set_title($kegiatan);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	$batas = mktime(20, 0, 0, 3, 16, 2015) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = true;		// (($selisih>0) || (isSuperuser()));
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	
	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodekeg, 
	);

	$form['formdata']['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		//'#description'  => 'tahun', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	); 
	$form['formdata']['kodepro']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodepro', 
		//'#description'  => 'kodepro', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	);
	
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		'#description'  => 'Nama kegiatan', 
		'#maxlength'    => 255, 
		'#size'         => 100, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);

	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $jenis,
		'#options' => array(	
			 '1' => t('Tidak Langsung'), 	
			 '2' => t('Langsung'),	
		   ), 
	);		
	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	$form['formdata']['kegiatanjenis'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1.5'>Jenis belanja</font></span>",
	);		
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	

	$form['formdata']['isppkd']= array(
		'#type' => 'radios', 
		'#title' => t('PPKD'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $isppkd,
		'#options' => array(	
			 '0' => t('Bukan PPKD'), 	
			 '1' => t('PPKD'),	
		   ), 
	);		
	$form['formdata']['ssa'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	
	/*
	$form['formdata']['nk']= array(
		'#type'         => $tipenomorkeg, 
		'#title'        => 'nomorkeg', 
		//'#description'  => 'kodekeg', 
		'#maxlength'    => 3, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nomorkeg, 
	);
	*/
	
	if (isSuperuser()) {
		/*
		$form['formdata']['program'] = array (
			'#type'		=> 'textarea',
			'#title'	=> 'Program',
			'#cols'		=> '40',
			'#rows'		=> '2',
			//'#maxlength'    => 255, 
			//'#size'         => 60, 
			'#disabled'     => true, 
			'#default_value' => $program,
		);
		*/
		$form['formdata']['program'] = array (
			'#type'		=> 'textfield',
			'#title'	=> 'Program',
			'#maxlength'    => 255, 
			'#size'         => 100, 
			'#default_value' => $program,
		);
		
		$form['formdata']['program-val']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $program, 
		);
		$form['formdata']['kegiatanpro'] = array (
			'#type' => 'markup',
			'#value' => "<span><font size='1.5'>Program kegiatan, isikan dengan proggram yang sesuai melalui tombol Program disamping</font></span>",
		);		

		$form['formdata']['inaktif']= array(
			'#type' => 'radios', 
			'#title' => t('Status'), 
			//'#description'  => 'Jenis belanja',
			'#default_value' => $inaktif,
			'#options' => array(	
				 '0' => t('Aktif'), 	
				 '1' => t('Tidak Aktif'),	
			   ), 
		);		
		$form['formdata']['ss0'] = array (
			'#type' => 'item',
			'#value' => "<div style='clear:both;'></div>",
		);	
		
	} else {
		$form['formdata']['programx']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Program', 
			'#description'  => 'Program kegiatan', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			'#disabled'     => true, 
			'#default_value'=> $program, 
		);
		$form['formdata']['inaktif']= array(
			'#type'         => 'hidden', 
			'#title'        => 'Inaktif', 
			'#description'  => 'Kegiatan tidak diaktifkan, tidak bisa diakses oleh SKPD', 
			//'#maxlength'    => 255, 
			//'#size'         => 60, 
			//'#required'     => !$disabled, 
			'#disabled'     => true, 
			'#default_value'=> $inaktif, 
		);
		
	}
		
	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$skpd = array();
	//$dinas[''] = '--- pilih dinas teknis---';
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
		'#description'  => 'SKPD pelaksana kegiatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	
	$form['formdata']['anggaran'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Isian Anggaran',
		'#collapsible' => true,
		'#collapsed' => false,        
	);
	
	$form['formdata']['anggaran']['totalx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah', 
		'#description'  => 'Jumlah anggaran perubahan, anggaran penetapannya adalah ' . apbd_fn($total_pen), 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => true, 
		'#default_value'=> $total, 
	); 
	$form['formdata']['anggaran']['total']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $total, 
	); 	
	$form['formdata']['anggaran']['plafon']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Alokasi',
		'#description'  => 'Alokasi plafon anggara perubahan, plafon penetapannya adalah ' . apbd_fn($plafon_pen), 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $plafon, 
	); 

	$form['formdata']['sumberdana'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Sumber Dana',
		'#collapsible' => true,
		'#collapsed' => false,        
	);

	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdana = array();
	$sumberdana[''] = '-Pilih Sumber Dana-';
	while ($data = db_fetch_object($pres)) {
		$sumberdana[$data->sumberdana] = $data->sumberdana;
	}
	
	$form['formdata']['sumberdana']['sumberdana1']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdana,
		'#width'         => 30, 
		'#default_value'=> $sumberdana1, 
	); 
	$form['formdata']['sumberdana']['sumberdana1rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $sumberdana1rp, 
	); 
	/*
	$form['formdata']['sumberdana']['sumberdana2']= array(
		'#type'         => 'select', 
		'#options'		=> $sumberdana,
		'#title'        => 'Sumber Dana #2', 
		'#width'         => 30, 
		'#default_value'=> $sumberdana2, 
	); 
	$form['formdata']['sumberdana']['sumberdana2rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sumber Dana #2 Rp',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $sumberdana2rp, 
	); 
	*/

	$form['formdata']['dispensasi']= array(
		'#type' => 'radios', 
		'#title' => t('Perpanjangan RKA'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $dispensasi,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Perpanjang'),	
		   ), 
	);		

	$form['formdata']['ssdisp'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	
    $form['formdata']['e_kodekeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodekeg, 
    ); 
	
    $form['formdata']['e_kodepro']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodepro, 
    ); 

    $form['formdata']['e_kodeuk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeuk, 
    ); 
    $form['formdata']['e_nomorkeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomorkeg, 
    ); 

	//PREV
	$sql = 'select kodekeg from {kegiatanperubahan} where tahun=\'%s\' and kodeuk=\'%s\' and kodekeg<\'%s\' order by kodekeg desc limit 1';
	$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodekeg));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$prevkode=$data->kodekeg;

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
	$sql = 'select kodekeg from {kegiatanperubahan} where tahun=\'%s\' and kodeuk=\'%s\' and kodekeg>\'%s\' order by kodekeg limit 1';
	$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodeuk, $kodekeg));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$nextkode=$data->kodekeg;

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

	$form['formdata']['submitnew'] = array (
		'#type' => 'submit',
		'#value' => 'Baru',
	); 
	
	if ($allowedit) 
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpdperubahan' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
	
    return $form;
	
}
function kegiatanskpd_editadmin_form_validate($form, &$form_state) {

//  
	
}

function kegiatanskpd_editadmin_form_submit($form, &$form_state) {
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/editadmin/' . $nextkode ;
		//drupal_set_message('Next');
		
    } elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/editadmin/' . $prevkode ;
		//drupal_set_message('Next');
		
    } elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
        $form_state['redirect'] = 'apbd/kegiatanskpd/editadmin/';
		//drupal_set_message('Next');
		
	} else {
		$e_kodekeg = $form_state['values']['e_kodekeg'];
		$e_kodeuk = $form_state['values']['e_kodeuk'];
		$e_kodepro = $form_state['values']['e_kodepro'];

		$kodeuk = $form_state['values']['kodeuk'];
		$kodepro = $form_state['values']['kodepro'];
		
		$kodekeg = $form_state['values']['kodekeg'];

		$kegiatan = $form_state['values']['kegiatanx'];
		$jenis = $form_state['values']['jenis'];
		$isppkd = $form_state['values']['isppkd'];
		$plafon = $form_state['values']['plafon'];
		$inaktif = $form_state['values']['inaktif'];
		$dispensasi = $form_state['values']['dispensasi'];
		
		if ($jenis==2) $isppkd =0;
		
		$sumberdana1 = $form_state['values']['sumberdana1'];
		$sumberdana2 = '';		//$form_state['values']['sumberdana2'];
		$sumberdana1rp = $form_state['values']['sumberdana1rp'];
		$sumberdana2rp = 0;		//$form_state['values']['sumberdana2rp'];
		
		$total = $form_state['values']['total'];
		if (($sumberdana1 . $sumberdana2) == '') $sumberdana1 = 'DAU';
		if (($sumberdana1rp + $sumberdana2rp) == '') $sumberdana1rp = $total;
		
		if ($e_kodekeg=='')
		{					
			$tahun = variable_get('apbdtahun', 0);
			$kodekeg = $tahun . $kodeuk . $kodepro ;
			$nomorkeg =apbd_getcounterskpd($kodekeg);
			$kodekeg .= apbd_getcounterkegiatan($kodekeg);
			
			
			$sql = sprintf("insert into {kegiatanperubahan} (kodekeg, nomorkeg, tahun, kodepro, kodeuk, kegiatan, jenis, plafon, sumberdana1, sumberdana1rp, 
					sumberdana2, sumberdana2rp, inaktif, isppkd, dispensasi) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
							$kodekeg, $nomorkeg, $tahun, $kodepro, $kodeuk,
							db_escape_string($kegiatan),
							db_escape_string($jenis),
							db_escape_string($plafon),
							db_escape_string($sumberdana1),					  
							db_escape_string($sumberdana1rp),
							db_escape_string($sumberdana2),					  
							db_escape_string($sumberdana2rp),
							db_escape_string($inaktif),
							$isppkd,
							$dispensasi
						  );
			//drupal_set_message($sql);

			$res = db_query($sql);
		} else {

			$kodeberubah=false;
			if (($e_kodepro == $kodepro) && ($e_kodeuk == $kodeuk)) {
				$kodekeg = $e_kodekeg;
				
			} else {
				$tahun = variable_get('apbdtahun', 0);
				$kodekeg = $tahun . $kodeuk . $kodepro;			
				$kodekeg .= apbd_getcounterkegiatan($kodekeg);
				
				$kodeberubah=true;
			}		
			
			
			$sql = sprintf("update {kegiatanperubahan} set kegiatan='%s', plafon='%s', sumberdana1='%s', sumberdana1rp='%s', sumberdana2='%s', 
					sumberdana2rp='%s', kodeuk='%s', kodepro='%s', kodekeg='%s', jenis='%s', inaktif='%s',  isppkd='%s',
					dispensasi='%s' where kodekeg='%s'",
							db_escape_string($kegiatan),					  
							db_escape_string($plafon),					  
							db_escape_string($sumberdana1),					  
							db_escape_string($sumberdana1rp),
							db_escape_string($sumberdana2),					  
							db_escape_string($sumberdana2rp),
							db_escape_string($kodeuk),
							db_escape_string($kodepro),
							db_escape_string($kodekeg),
							db_escape_string($jenis),
							db_escape_string($inaktif),
							db_escape_string($isppkd),
							db_escape_string($dispensasi),
							$e_kodekeg);		
			$res = db_query($sql);
		}
		if ($res) {
			
			if ($kodeberubah) {
				//Update anggperkeg
				$sql = sprintf("update {anggperkeg} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);

				//Update anggperkegdetil
				$sql = sprintf("update {anggperkegdetil} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);
				
			}
			
			drupal_set_message('Penyimpanan data berhasil dilakukan');		
		}
		else {
			drupal_set_message($kodekeg);		
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
		}
		//drupal_goto('apbd/kegiatanskpd');    
	}
}
?>