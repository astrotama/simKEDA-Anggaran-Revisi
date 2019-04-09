<?php
function kegiatanrevisi_edit_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    $id = arg(3);
	$plafonbaru =0;
	$status = 0;
	
	//drupal_set_message($id);
	
	//$kodeuk = apbd_getuseruk();
	//if (isSuperuser())
	//	$kodeuk = $_SESSION['kodeuk'];
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatanrev.js');
	drupal_add_css('files/css/kegiatancam.css');
    $disabled = FALSE;
    if (isset($id))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
		

			 
        $sql = 'select k.id,k.tahun,k.kodeuk,k.kodekeg,k.kegiatanbaru,k.kegiatanlama,k.kodeprobaru,k.kodeprolama,
				k.plafonbaru,k.plafonlama,k.status,p.program from {kegiatanrevisi} k left join {program} p on k.kodeprolama=p.kodepro where k.id=\'%s\'' ;
				
		//drupal_set_message($sql . $kodekeg);
        $res = db_query(db_rewrite_sql($sql), array ($id));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$id = $data->id;
				$tahun = $data->tahun;
				$kodeuk = $data->kodeuk;
				$kodekeg = $data->kodekeg;
				
				$kegiatanbaru = $data->kegiatanbaru;
				$kegiatanlama = $data->kegiatanlama;
				
				$kodeprobaru = $data->kodeprobaru;
				$kodeprolama = $data->kodeprolama;
				$programlama = $data->program;
				
				$plafonbaru = $data->plafonbaru;
				$plafonlama = $data->plafonlama;
				
				$status = $data->status;
				
				if ($kodeprobaru!='') {
					$sql = 'select program from {program} where kodepro=\'%s\'' ;
					$res = db_query(db_rewrite_sql($sql), array ($kodeprobaru));		
					//drupal_set_message($sql . $kodekeg);
					if ($res) {
						$data = db_fetch_object($res);
						if ($data) {    
							$programbaru = $data->program;
						}
					}
				}
				
				$disabled =TRUE;
				
			} else {
				$id = '';
				$kegiatanbaru = '';
			}
        } else {
			$id = '';
			$kegiatanbaru = '';
		}
    } else {
		//$form['formdata']['#title'] = 'Tambah Data Kegiatan Renja SKPD';
		$id = '';
		$kodekeg = '';
		$kegiatanbaru = '';
	
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
    }

	drupal_set_title($kegiatanbaru);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	$batas = mktime(20, 0, 0, 3, 16, 2015) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = true;		// (($selisih>0) || (isSuperuser()));
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	$form['formdata']['id']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $id, 
	);	 
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

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
	
	$form['formdata']['kegiatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		'#description'  => '', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => 'true', 
		'#default_value'=> $kegiatanlama, 
	);
	$form['formdata']['kegiatanket'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1.5'>Nama kegiatan yang akan direvisi, anda bisa memilihnya lewat tombol Pilih. Revisi terhadap nama kegiatan jangan dituliskan disini, tapi pada menu Revisi Nama Kegiatan pada bagian bawah</font></span>",
	);		
	$form['formdata']['kegiatanlama']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatanx', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => 'true', 
		'#default_value'=> $kegiatanlama, 
	);
	$form['formdata']['programlama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Program', 
		'#description'  => 'Program dari kegiatan yang akan direvisi. Revisi terhadap program jangan dituliskan disini, tapi pada menu Revisi Program dengan cara memilih Program yang baru', 
		'#maxlength'    => 255, 
		'#size'         => 90,  
		//'#disabled'     => 'false', 
		'#default_value'=> $programlama, 
	);
	$form['formdata']['kodeprolama']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodeprolama', 
		//'#description'  => 'kegiatanx', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => 'true',  
		'#default_value'=> $kodeprolama, 
	);
	$form['formdata']['plafonlama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Plafon Anggaran',
		'#description'        => 'Plafon anggaran yang akan direvisi. Revisi terhadap plafon jangan dituliskan disini, tapi pada menu Revisi Plafon pada bagian bawah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		//'#access'		=> false,		
		'#default_value'=> $plafonlama, 
	); 
	$form['formdata']['plafon']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $plafonlama, 
	); 

	//Revisi Kegiatan
	$collapsed = ($kegiatanbaru == '');
	$form['formdata']['revkegiatan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Revisi Nama Kegiatan',
		'#collapsible' => true,
		'#collapsed' => $collapsed,     
	);	
	$form['formdata']['revkegiatan']['kegiatanbaru']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Kegiatan Baru', 
		'#description'  => 'Isi dengan nama kegiatan yang baru bila ingin mengubah, kosongkan bila tidak ingin mengubah nama kegiatan', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatanbaru, 
	);	
	
	$collapsed = ($kodeprobaru == '');
	$form['formdata']['revprogram'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Revisi Program',
		'#collapsible' => true,
		'#collapsed' => $collapsed,     
	);		
	$form['formdata']['revprogram']['kodepro'] = array (
		'#type'		=> 'hidden',
		'#title'	=> 'Program Baru',
		'#default_value' => $kodeprobaru,
	);
	$form['formdata']['revprogram']['program'] = array (
		'#type'		=> 'textfield',
		'#title'	=> 'Program',
		//'#description'  => 'Isi dengan program yang baru bila ingin mengubah, kosongkan bila tidak ingin mengubah program', 
		//'#cols'		=> '120',
		//'#rows'		=> '2',
		'#maxlength'    => 255, 
		'#size'         => 90, 
		'#disabled'     => false, 
		'#default_value' => $programbaru,
	);
	$form['formdata']['revprogram']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi dengan program yang baru bila ingin mengubah, kosongkan bila tidak ingin mengubah program</font></span>",
	);
	$form['formdata']['revprogram']['program-val']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $programbaru, 
	);	

	$collapsed = ($plafonbaru == 0);
	$form['formdata']['revplafon'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Revisi Plafon',
		'#collapsible' => true,
		'#collapsed' => $collapsed,     
	);	
	$form['formdata']['revplafon']['plafonbaru']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Plafon Anggaran Baru',
		'#description'  => 'Isi dengan jumlah plafon yang baru bila ingin mengubah, kosongkan bila tidak ingin mengubah plafon', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $plafonbaru, 
	); 
	
	/*
	$tipestatus = 'hidden';
	if (isSuperuser()) $tipestatus = 'radios';
	$form['formdata']['status']= array(
		'#type'         => $tipestatus, 
		'#title'        => 'Status',
		'#default_value' => $status,
		'#options' => array(	
			 '0' => t('Usulan'), 	
			 '1' => t('Disetujui'), 	
			 '9' => t('Ditolak'),	
		   ),
	); 
	*/
	
	if ($allowedit) 
		
		if (isSuperuser()) {
			if ($status==0) {
				$form['formdata']['setujui'] = array (
					'#type' => 'submit',
					'#value' => 'Setujui',
				); 			
				$form['formdata']['tolak'] = array (
					'#type' => 'submit',
					'#value' => 'Tolak',
				); 	
				
			} else {
				$form['formdata']['reset'] = array (
					'#type' => 'submit',
					'#value' => 'Reset',
				); 			
			}
		}
			
		if ($status==0) {
			$form['formdata']['submit'] = array (
				'#type' => 'submit',
				'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisiperubahan' class='btn_green' style='color: white'>Batal</a>",
				'#value' => 'Simpan'
			);
		}
		
    return $form;
	
}
function kegiatanrevisi_edit_form_validate($form, &$form_state) {

	$kodekeg = $form_state['values']['kodekeg'];
	if ($kodekeg == '')
		form_set_error('kegiatanrevisi', 'Kegiatan yang akan direvisi belum dipilih.' );

	$kodeuk = $form_state['values']['kodeuk'];
	if ($kodeuk == '')
		form_set_error('kegiatanrevisi', 'Kegiatan yang akan direvisi belum dipilih dengan benar' );

	$kodeprobaru = $form_state['values']['kodepro'];
	if ($kodeprobaru==$kodeprolama) $kodeprobaru = '';
	
	$kegiatanlama = $form_state['values']['kegiatanlama'];
	$kegiatanbaru = $form_state['values']['kegiatanbaru'];
	if ($kegiatanbaru==$kegiatanlama) $kegiatanbaru = '';

	$plafonbaru = $form_state['values']['plafonbaru'];
	if ($plafonbaru==$plafonlama) $plafonbaru =0;

	if (($kegiatanbaru=='') and ($kodeprobaru=='') and ($plafonbaru==0))
		form_set_error('kegiatanrevisi', 'Anda belum memasukkan revisi. Revisi bisa dilakukan terhadap nama kegiatan, program, dan/atau plafon anggaran' );

	/*
	$kegiatanlama = $form_state['values']['kegiatanlama'];
	$kegiatanbaru = $form_state['values']['kegiatanbaru'];
	if (strtolower($kegiatanbaru) == strtolower($kegiatanlama))
		form_set_error('kegiatanrevisi', 'Untuk merevisi nama kegiatan, harus menggunakan nama kegiatan yg berbeda' );
	*/
	
	//$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
	
	$id = $form_state['values']['id'];
	if ($id == '') {
		$sql = sprintf('select kodekeg,kegiatanlama from {kegiatanrevisi} where tipe=0 and status=0 and kodekeg=\'%s\'', $kodekeg) ;
		
		//$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
		//drupal_set_message($sql);
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);	
			if ($data) { 
				form_set_error('kegiatanrevisi', 'Kegiatan ' . $data->kegiatanlama . ' sudah masuk dalam daftar revisi.' );
			}
		} else
			form_set_error('kegiatanrevisi', 'Kesalahan validasi' );
	}
}
 
function kegiatanrevisi_edit_form_submit($form, &$form_state) {
	$tipe = 0;
	
	$id = $form_state['values']['id'];
	$kodekeg = $form_state['values']['kodekeg'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];

	$kodeprolama = $form_state['values']['kodeprolama'];
	$kodeprobaru = $form_state['values']['kodepro'];
	if ($kodeprobaru==$kodeprolama) $kodeprobaru = '';
	
	$kegiatanlama = $form_state['values']['kegiatanlama'];
	$kegiatanbaru = $form_state['values']['kegiatanbaru'];
	if ($kegiatanbaru==$kegiatanlama) $kegiatanbaru = '';

	$plafonlama = $form_state['values']['plafon'];
	$plafonbaru = $form_state['values']['plafonbaru'];
	if ($plafonbaru==$plafonlama) $plafonbaru =0;
	
	//drupal_set_message($plafonlama);	
	if ($id=='')
	{
        $sql =  sprintf("insert into {kegiatanrevisi} (tipe, tahun, kodeuk, kodekeg, kegiatanlama, kegiatanbaru, kodeprolama, kodeprobaru, plafonlama, plafonbaru) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $tipe, $tahun, $kodeuk, $kodekeg, $kegiatanlama, db_escape_string($kegiatanbaru), db_escape_string($kodeprolama), db_escape_string($kodeprobaru), db_escape_string($plafonlama), db_escape_string($plafonbaru) ); 
		
		//c
		$res = db_query($sql);
		
	} else {
		
		
		if (!isSuperuser()) { 
			//SKPD
			$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s' where id='%s'",
							db_escape_string($kegiatanbaru),
							db_escape_string($kodeprobaru),
							db_escape_string($plafonbaru),
							$id);		
			$res = db_query($sql);
			
		} else {
			
			//drupal_set_message('to setujui');
			if ($form_state['clicked_button']['#value'] == $form_state['values']['setujui']) {
				//drupal_set_message('setujui');
				
				$status = 1;
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', 
								status='%s' where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								$id);		
				$res = db_query($sql);
				
				//UPDATE
				//1. Nama kegiatan
				if ($res) {
					if ($kegiatanbaru !='') {
						$sql = sprintf("update {kegiatanskpd} set kegiatan='%s'
										where kodekeg='%s'",
										db_escape_string($kegiatanbaru),
										$kodekeg);		
						$res = db_query($sql);
					}
				}
				//2. Plafon
				if ($res) {
					if ($plafonbaru != 0) {
						$sql = sprintf("update {kegiatanskpd} set plafon='%s'
										where kodekeg='%s'",
										db_escape_string($plafonbaru),
										$kodekeg);		
						$res = db_query($sql);
					}
				}
				//3. Program
				if ($res) {
					if ($kodeprobaru != '') {

						$kodekegnew = $tahun . $kodeuk . $kodeprobaru ;
						$nomorkeg = apbd_getcounterskpd($kodekegnew);
						$kodekegnew .= apbd_getcounterkegiatan($kodekegnew);							
						
						//drupal_set_message($kodekeg);
						//drupal_set_message($kodekegnew);
						
						$sql = sprintf("update {kegiatanskpd} set kodepro='%s', kodekeg='%s' 
										where kodekeg='%s'",
										db_escape_string($kodeprobaru),
										$kodekegnew, 
										$kodekeg);		
						$res = db_query($sql);
					}
				}
				
			} else if ($form_state['clicked_button']['#value'] == $form_state['values']['tolak']) {
				$status = 9;
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', 
								status='%s' where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								$id);	
				$res = db_query($sql);							

			} else if ($form_state['clicked_button']['#value'] == $form_state['values']['reset']) {
				$status = 0;
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', 
								status='%s' where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								$id);		
				$res = db_query($sql);
												
			} else {
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s' where 
								id='%s'", db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$id);		
				$res = db_query($sql);
			}
		}
		
	}
	if ($res) {
		
		drupal_set_message('Penyimpanan data berhasil dilakukan');		
	}
	else {
		drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	}
	drupal_goto('apbd/kegiatanrevisi');    

}
?>