<?php
    
function programsasaran_edit_form(){
	$kodepro = arg(3);
	$tahun = arg(4);
	$nomor = arg(5);
    drupal_add_css('files/css/kegiatancam.css');	
	drupal_set_title('Sasaran/Target Tahun ' . $tahun );
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');

	$title = 'Target';
	$sql = 'select program from {program} where kodepro=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodepro));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) $title = $data->program;
	} 


    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> $title,
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );

	$tipe = 0;
	$pengukuran = 0;
		
	$sql = 'select ktarget,satuan,rtarget,tipe,pengukuran,sasaran from {programsasaran} where kodepro=\'%s\' and tahun=\'%s\' and nomor=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodepro, $tahun, $nomor));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			
			$sasaran = $data->sasaran;
			$ktarget = $data->ktarget;
			$satuan = $data->satuan;
			$rtarget = $data->rtarget;
			$tipe = $data->tipe;
			$pengukuran = $data->pengukuran;
		} else {
			$nomor = '';
		}
	} 
    
	
	$form['formdata']['kodepro']= array(
		'#type'         => 'hidden', 
		//'#description'  => 'kodebid', 
		'#maxlength'    => 7, 
		'#default_value'=> $kodepro, 
	); 
	$form['formdata']['tahun']= array(
		'#type'         => 'hidden', 
		//'#description'  => 'kodebid', 
		'#maxlength'    => 4, 
		'#default_value'=> $tahun, 
	); 

	$form['formdata']['sasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sasaran' , 
		'#description'  => 'Sasaran yang direncanakan. misalnya Terwujudnya jalan kabupaten beraspal Hotmix', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		'#required'     => TRUE, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $sasaran, 
		'#weight' => 1,
	); 
	$form['formdata']['ktarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target' , 
		'#description'  => 'Target yang direncanakan untuk dicapai dari sasaran yang ditetapkan. Untuk jalan, misalnya 10', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		'#required'     => TRUE, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ktarget, 
		'#weight' => 2,
	); 
	$form['formdata']['satuan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Satuan' , 
		'#description'  => 'Satuan dari target yang dicapai. Untuk jalan, misalnya KM', 
		'#size'         => 30, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $satuan, 
		'#weight' => 3,
	); 

	$optionsb = array('0' => t('Kuantitatif'), '1' => t('Kualitatif'));
	$form['formdata']['tipe']= array(
		'#type'         => 'radios', 
		'#title'        => 'Tipe Data',
		'#options'		=> $optionsb,
		'#description'  => 'Apakah volume target yang ingin dicapai merupakan data kuantitatif atau kualitatif', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tipe, 
		'#weight' => 4,
	); 

	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);

	$optionukur = array('0' => t('Meningkat'), '1' => t('Menurun'));
	$form['formdata']['pengukuran']= array(
		'#type'         => 'radios', 
		'#title'        => 'Pengukuran',
		'#options'		=> $optionukur,
		'#description'  => 'Bila merupakan data kuantitatif, apakah pengukurannya Meningkat atau Menurun. Lebih jelasnya bisa dibaca di petunjuk pengisian', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pengukuran, 
		'#weight' => 6,
	); 

	$form['formdata']['ss2'] = array ( 
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
	);

	$form['formdata']['rtarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Plafon',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah anggaran yang dibutuhkan untuk memenuhi target yang direncanakan', 
		'#required'     => TRUE, 
		'#size'         => 30, 
		'#default_value'=> $rtarget, 
		'#weight' => 8,
	); 
	$form['formdata']['e_nomor']= array(
		'#type'         => 'hidden', 
		//'#description'  => 'kodebid', 
		'#maxlength'    => 2, 
		'#default_value'=> $nomor, 
		'#weight' => 9,
	); 
	    	
	$pagelink = '/apbd/programsasaran/' . $kodepro . $tahun;
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/programsasaran/'" . $kodepro . $tahun . " class='btn_blue' style='color: white'>Batal</a>",
		'#suffix' => "&nbsp;<a href='" . $pagelink . "' class='btn_blue' style='color: white'>Batal</a>" . "&nbsp;",
        '#value' => 'Simpan',
        '#weight' => 10
    );
    
    return $form;
}
function programsasaran_edit_form_validate($form, &$form_state) {
	$tipe = $form_state['values']['tipe'];
	$ktarget = $form_state['values']['ktarget'];
	$rtarget = $form_state['values']['rtarget'];

	$rtarget 	= is_numeric($rtarget) ? $rtarget : 0;

	if ($tipe == 0 ) {			//Kuantitatif
		$ktarget 	= is_numeric($ktarget) ? $ktarget : -1;		//Harus angka
		if ($ktarget == -1 ) {
			form_set_error('', 'Target kuantitatif harus diisi dengan angka');
		}
    }


}
function programsasaran_edit_form_submit($form, &$form_state) {
    
    $e_nomor = $form_state['values']['e_nomor'];
    
	$kodepro = $form_state['values']['kodepro'];
	$tahun = $form_state['values']['tahun'];
	$sasaran = $form_state['values']['sasaran'];
	$ktarget = $form_state['values']['ktarget'];
	$satuan = $form_state['values']['satuan'];
	$rtarget = $form_state['values']['rtarget'];
	$tipe = $form_state['values']['tipe'];
	$pengukuran = $form_state['values']['pengukuran'];

	$rtarget 	= is_numeric($rtarget) ? $rtarget : 0;
    
    if ($e_nomor=='')
    {
		//Get nomor
		$nomor = 0;
		$sql = 'select nomor from {programsasaran} where kodepro=\'%s\' and tahun=\'%s\' order by nomor desc limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($kodepro, $tahun));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) 	
				$nomor = $data->nomor;
			else 
				$nomor = 0;
		} 
		$nomor += 1;
		
        $sql = 'insert into {programsasaran} (kodepro,tahun,nomor,ktarget,satuan,rtarget,tipe,sasaran,pengukuran) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($kodepro, $tahun, $nomor,$ktarget,$satuan,$rtarget,$tipe,$sasaran, $pengukuran));
    } else {
        $sql = 'update {programsasaran} set ktarget=\'%s\',satuan=\'%s\',rtarget=\'%s\',tipe=\'%s\',sasaran=\'%s\',pengukuran=\'%s\' where kodepro=\'%s\' and tahun=\'%s\' and nomor=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($ktarget, $satuan, $rtarget, $tipe, $sasaran, $pengukuran, $kodepro, $tahun, $e_nomor));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');

    $sasaranprogram = '';
    $targetprogram = '';
    $sasarantarget = '';
    $sql = 'select nomor,sasaran,ktarget,satuan from {programsasaran} where kodepro=\'%s\' and tahun=\'%s\' order by nomor';
    $res = db_query(db_rewrite_sql($sql), array ($kodepro, $tahun));
    if ($res) {
      while ($data = db_fetch_object($res)) {

        $sasaranprogram .= $data->sasaran . '; ';
        $targetprogram .= $data->ktarget . ' ' . $data->satuan . '; ';

        $sasarantarget .= $data->sasaran . ' (' . $data->ktarget . ' ' . $data->satuan . '); '; 

        }
    } 
    //$sql = "update {program} set s" . $tahun . "=\'%s\', t" . $tahun . "=\'%s\', st" . $tahun . "=\'%s\'  where kodepro=\'%s\' ";
    $sql = 'update {program} set s' . $tahun . '=\'%s\', t' . $tahun . '=\'%s\', st' . $tahun . '=\'%s\'  where kodepro=\'%s\' ';

    //drupal_set_message($sasaranprogram);
    //drupal_set_message($targetprogram);
    //drupal_set_message($sql);

    $res = db_query(db_rewrite_sql($sql), array($sasaranprogram, $targetprogram, $sasarantarget, $kodepro));
    
    if ($res)
        drupal_set_message('Penyimpanan sasaran/target program berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan sasaran/target program tidak berhasil dilakukan');


    drupal_goto('apbd/programsasaran/' . $kodepro . $tahun );    
}
?>
