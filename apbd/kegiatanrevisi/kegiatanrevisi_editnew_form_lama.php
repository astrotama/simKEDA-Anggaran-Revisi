<?php
function kegiatanrevisi_editnew_form() {
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
	
	$kodeuk = apbd_getuseruk();
	if (isSuperuser())
		$kodeuk = $_SESSION['kodeuk'];
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
		

			 
        $sql = 'select k.id,k.tahun,k.kodeuk,k.kegiatanbaru,k.kodeprobaru,
				k.plafonbaru,p.program, k.status from {kegiatanrevisi} k left join {program} p on k.kodeprobaru=p.kodepro where k.id=\'%s\'' ;
				
        $res = db_query(db_rewrite_sql($sql), array ($id));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$id = $data->id;
				$tahun = $data->tahun;
				$kodeuk = $data->kodeuk;
				
				$kegiatanbaru = $data->kegiatanbaru;
				
				$kodeprobaru = $data->kodeprobaru;
				$program = $data->program;
				
				$plafonbaru = $data->plafonbaru;
				$status = $data->status;
				
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
	
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatanx', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => 'true',  
		'#default_value'=> $kegiatanbaru, 
	);
	$form['formdata']['kodepro'] = array (
		'#type'		=> 'hidden',
		'#title'	=> 'Program',
		'#default_value' => $kodeprobaru,
	);
	$form['formdata']['program'] = array (
		'#type'		=> 'textfield',
		'#title'	=> 'Program',
		//'#cols'		=> '120',
		//'#rows'		=> '2',
		'#maxlength'    => 255, 
		'#size'         => 90, 
		'#disabled'     => false, 
		'#default_value' => $program,
	);
	$form['formdata']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi dengan program kegiatan yang diusulkan</font></span>",
	);
	$form['formdata']['program-val']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $programbaru, 
	);	

	$form['formdata']['plafonbaru']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Plafon Anggaran',
		'#description'  => 'Isi dengan jumlah plafon anggaran yang diusulkan', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $plafonbaru, 
	); 
	
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
				'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_blue' style='color: white'>Batal</a>",
				'#value' => 'Simpan'
			);
		}
		
    return $form;
	
}
function kegiatanrevisi_editnew_form_validate($form, &$form_state) {

	
}
 
function kegiatanrevisi_editnew_form_submit($form, &$form_state) {
	$tipe = 1;
	
	$id = $form_state['values']['id'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tahun = $form_state['values']['tahun'];

	$kodeprobaru = $form_state['values']['kodepro'];
	
	$kegiatanbaru = $form_state['values']['kegiatanx'];

	$plafonbaru = $form_state['values']['plafonbaru'];
	
	//drupal_set_message($plafonlama);	
	if ($id=='')
	{ 

        $sql =  sprintf("insert into {kegiatanrevisi} (tipe, tahun, kodeuk, kegiatanbaru, kodeprobaru, plafonbaru) values('%s', '%s', '%s', '%s', '%s', '%s')", $tipe, $tahun, $kodeuk,  db_escape_string($kegiatanbaru), db_escape_string($kodeprobaru),  db_escape_string($plafonbaru) ); 
		
		//drupal_set_message($sql);	
		$res = db_query($sql);
		
	} else {
		
		if (!isSuperuser()) { 
			$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s' where id='%s'",
							db_escape_string($kegiatanbaru),
							db_escape_string($kodeprobaru),
							db_escape_string($plafonbaru),
							$id);		
			$res = db_query($sql);
			
		} else { 
			
			if ($form_state['clicked_button']['#value'] == $form_state['values']['setujui']) {
				
				$status = 1;
				$kodekeg = $tahun . $kodeuk . $kodeprobaru ;
				$nomorkeg = apbd_getcounterskpd($kodekeg);
				$kodekeg .= apbd_getcounterkegiatan($kodekeg);
				
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', status='%s',
								kodekeg='%s' where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								db_escape_string($kodekeg),
								$id);	
				//drupal_set_message($sql);	
				$res = db_query($sql);
				
				//ADD KEGIATAN
				if ($res) {
					$jenis = 2;
					$sql = sprintf("insert into {kegiatanskpd} (kodekeg, nomorkeg, tahun, kodepro, kodeuk, kegiatan, jenis,
									plafon) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
									$kodekeg, $nomorkeg, $tahun, $kodepro, $kodeuk,
									db_escape_string($kegiatanbaru),
									db_escape_string($jenis),
									db_escape_string($plafonbaru)
								  );
					//drupal_set_message($sql);
					$res = db_query($sql);
				}
				
			} else if ($form_state['clicked_button']['#value'] == $form_state['values']['tolak']) {
				$status = 9;
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', status='%s'
								where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								$id);	
				$res = db_query($sql);							

			} else if ($form_state['clicked_button']['#value'] == $form_state['values']['reset']) {
				$status = 0;
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s', status='%s'
								where id='%s'",
								db_escape_string($kegiatanbaru),
								db_escape_string($kodeprobaru),
								db_escape_string($plafonbaru),
								$status,
								$id);		
				$res = db_query($sql);
												
			} else {
				$sql = sprintf("update {kegiatanrevisi} set kegiatanbaru='%s', kodeprobaru='%s', plafonbaru='%s' where id='%s'",
								db_escape_string($kegiatanbaru),
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
	$_SESSION['kodeuk'] = $kodeuk;
	drupal_goto('apbd/kegiatanrevisi');    

}
?>