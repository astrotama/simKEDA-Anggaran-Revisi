<?php
function desa_edit_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data Kegiatan Renstra SKPD',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    $kodekeg = arg(3);
	$kodeuk = apbd_getuseruk();
	if (isSuperuser())
		$kodeuk = $_SESSION['kodeuk'];
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);

	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
	drupal_set_title('Rentra SKPD');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodekeg))
    {
        if (!user_access('desa edit'))
            drupal_access_denied();
		
		$customwhere = '';

		//if (isUserKecamatan()) {
		//	$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
		//}	
			
        $sql = 'select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kegiatan, k.sasaran, k.target, k.total,  p.program from {kegiatanrenstra} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' . $customwhere;
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodepro = $data->kodepro;
				$kodeuk = $data->kodeuk;
				$kegiatan = $data->kegiatan ;
				$sasaran = $data->sasaran;
				$target = $data->target;
				$total = $data->total;
				$disabled =TRUE;
			} else {
				$kodekeg = '';
			}
        } else {
			$kodekeg = '';
		}
    } else {
		$form['formdata']['#title'] = 'Tambah Data Kegiatan Renstra SKPD';
		$kodekeg = '';
	
		if (!user_access('desa tambah'))
			drupal_access_denied();
    }
	$form['formdata']['isikegiatanpolicy']= array(
		'#type'         => 'hidden', 
		//'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> variable_get("apbdkegiatan",0), 
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
	$tipenomorkeg='hidden';
	if ($disabled)
		$tipenomorkeg="textfield";
	if (variable_get("apbdkegiatan",0)==1)			
		$tipenomorkeg='hidden';	
	
	$form['formdata']['nomorkeg']= array(
		'#type'         => $tipenomorkeg, 
		'#title'        => 'Nomor', 
		//'#description'  => 'kodekeg', 
		'#maxlength'    => 3, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nomorkeg, 
	); 
	
	if ($e_kodekeg=='') 
		$selecttahun='select';
	else
		$selecttahun='hidden';

	$opttahun = array();     
	$opttahun['2014'] = '2014';
	$opttahun['2015'] = '2015';
	$opttahun['2016'] = '2016';
	$opttahun['2017'] = '2017';
	$opttahun['2018'] = '2018';
	
	$form['formdata']['tahun']= array(
		'#type'         => $selecttahun, 
		'#title'        => 'Tahun', 
		'#options'	=> $opttahun,
		//'#description'  => 'tahun', 
		'#width'         => 20, 
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
	
	$form['formdata']['kegiatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatan', 
		//'#maxlength'    => 60, 
		'#size'         => 59, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);
	$form['formdata']['program'] = array (
		'#type'		=> 'textarea',
		'#title'	=> 'Program',
		'#cols'		=> '40',
		'#rows'		=> '3',
		'#disabled'     => true, 
		'#default_value' => $program,
	);
	$form['formdata']['program-val']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $program, 
	);

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
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	
	$tipesuk = 'hidden';
	if (apbd_getuseruk() =='03')
		$tipesuk = 'select';

	$pquery = "select kodesuk, namasuk from {subunitkerja} order by namasuk" ;
	$pres = db_query($pquery);
	$subskpd = array();
	$subskpd[''] = '----Pilih Sub SKPD----';
	while ($data = db_fetch_object($pres)) {
		$subskpd[$data->kodesuk] = $data->namasuk;
	}
	
	$form['formdata']['kodesuk']= array(
		'#type'         => $tipesuk, 
		'#title'        => 'Sub SKPD',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
	); 
	
	$form['formdata']['sasaran']= array(
		'#type'         => 'textarea', 
		'#title'        => 'Sasaran', 
		'#cols'		=> '40',
		'#rows'		=> '3',
		//'#disabled'     => true, 
		//'#description'  => 'sasaran', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $sasaran, 
	); 
	$form['formdata']['target']= array(
		'#type'         => 'textarea', 
		'#title'        => 'Target/Volume', 
		'#cols'		=> '40',
		'#rows'		=> '3',
		//'#disabled'     => true, 
		//'#description'  => 'target', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $target, 
	); 

	$form['formdata']['total']= array(
		'#type'         => $textfield, 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $total, 
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

    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/desa' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );

    return $form;
	
}
function desa_edit_form_validate($form, &$form_state) {

	if ($form_state['values']['kegiatan'] == '') {
		form_set_error('', 'Nama kegiatan tidak boleh kosong');
	}
	
    if ($form_state['values']['kodeuk']=='') {
		form_set_error('', 'User Id anda tidak terhubung dengan Unit Kerja/SKPD, hubungilah admin untuk mengisi kode SKPD anda.');
    }
	if ($form_state['values']['isikegiatanpolicy']==0) {
		if ($form_state['values']['kodepro']=='')
			form_set_error('', 'Program belum di isi, silahkan diisi terlebih dahulu');
	} else {
		if ($form_state['values']['kodepro']=='')
			form_set_error('kegiatan', 'Kegiatan harus dipilih dari pilihan (tekanlah tombol cari), tidak boleh diisi sendiri');
		//cek di database tahun, kodeu, kodepro, kodeuk, nomorkeg tidak boleh sama
		$sql = sprintf("select count(*) as jml from {kegiatanrenstra} where tahun=%s and kodepro='%s' and kodeuk='%s' and nomorkeg='%s'",
					   $form_state['values']['tahun'],
					   $form_state['values']['kodepro'],
					   $form_state['values']['kodeuk'],
					   $form_state['values']['nomorkeg']
					   );
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$old = $form_state['values']['e_kodeuk'] . $form_state['values']['e_kodepro'] . $form_state['values']['e_nomorkeg'];
			$new = $form_state['values']['kodeuk'] . $form_state['values']['kodepro'] . $form_state['values']['nomorkeg'];

			$isModify = $old != $new;
			
			$num = $data->jml;
			if ($isModify) {
				if ($num>0) {
					form_set_error('kegiatan', 'Kegiatan ini sudah dibuat untuk SKPD tersebut');
				}
			}
		}
	}
    
	
}

function desa_edit_form_submit($form, &$form_state) {
    $e_kodekeg = $form_state['values']['e_kodekeg'];
    $e_kodeuk = $form_state['values']['e_kodeuk'];
    $e_kodepro = $form_state['values']['e_kodepro'];
	$isikegiatanpolicy = $form_state['values']['isikegiatanpolicy'];
    
	$kodekeg = $form_state['values']['kodekeg'];
	$nomorkeg = $form_state['values']['nomorkeg'];
	$tahun = $form_state['values']['tahun'];
	$kodepro = $form_state['values']['kodepro'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kegiatan = $form_state['values']['kegiatan'];
	$sasaran = $form_state['values']['sasaran'];
	$target = $form_state['values']['target'];

	//$total = $form_state['values']['total'];
	//$totalpenetapan = $form_state['values']['totalpenetapan'];
	//if ($totalpenetapan == 0)
	//	$totalpenetapan = $total;
	
	$total = $form_state['values']['total'];

    if ($e_kodekeg=='')
    {
		//$kodeuk = apbd_getuseruk();		
		//$tahun = '2012';
		$kodekeg = $tahun . $kodeuk . $kodepro ;
		
		if ($isikegiatanpolicy==0)
			$nomorkeg =apbd_getcounterskpd($kodekeg);
			
		$kodekeg .= apbd_getcounterkegiatan($kodekeg);
		$sql = sprintf("insert into {kegiatanrenstra} (kodekeg, nomorkeg, tahun, kodepro, kodeuk, kegiatan, sasaran, target, total) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						$kodekeg, $nomorkeg, $tahun, $kodepro, $kodeuk, 
						db_escape_string($kegiatan),
						db_escape_string($sasaran),
						db_escape_string($target),
						db_escape_string($total)
					  );
		//drupal_set_message($sql);
		$res = db_query($sql);
    } else {
		
		//$tahun='2012';
		if (($e_kodepro == $kodepro) && ($e_kodeuk == $kodeuk)) {
			$kodekeg = $e_kodekeg;
		} else {
			$kodekeg = $tahun . $kodeuk . $kodepro;			
			$kodekeg .= apbd_getcounterkegiatan($kodekeg);
		}
		$sql = sprintf("update {kegiatanrenstra} set kodekeg='%s', nomorkeg='%s', kodepro='%s', kodeuk='%s', kegiatan='%s', sasaran='%s', target='%s', total='%s' where kodekeg='%s'",
						$kodekeg,
						db_escape_string($nomorkeg),
						$kodepro,
						$kodeuk,
						db_escape_string($kegiatan),
						db_escape_string($sasaran),
						db_escape_string($target),			db_escape_string($total),
						$e_kodekeg);		
		$res = db_query($sql);
    }
    if ($res) {
        drupal_set_message('Penyimpanan data berhasil dilakukan');		
    }
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');

	$_SESSION['kodeuk'] = $kodeuk;
    drupal_goto('apbd/desa');    
}
?>