<?php
    
function pengumuman_edit_form(){
    $nomor = arg(3);
    drupal_add_css('files/css/kegiatancam.css');		
	drupal_set_title('Pengumuman');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($nomor))
    {
        if (!user_access('urusan edit'))
            drupal_access_denied();
			
        $sql = 'select nomor,pengumuman from {pengumuman} where nomor=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($nomor));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$nomor = $data->nomor;
				$pengumuman = $data->pengumuman;
				
                $disabled =TRUE;
			} else {
				$nomor = '';
			}
        } else {
			$nomor = '';
		}
    } else {
		if (!user_access('urusan tambah'))
			drupal_access_denied();
		$nomor = '';
	}
    
	$form['nomor']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Kode', 
		//'#description'  => 'nomor', 
		'#default_value'=> $nomor, 
	); 
	$form['pengumuman']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Isi Pengumuman', 
		//'#rows' => 5,
		//'#cols' => 5, 
		'#maxlength'    => 255, 
		'#size'         => 100, 
		'#description'  => 'Isikan pengumuman disini, pengumuman ini akan muncul dalam running text di bagian atas halaman web', 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pengumuman, 
	);
	
    $form['e_nomor']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomor, 
    ); 
	
    $form['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/node' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function pengumuman_edit_form_validate($form, &$form_state) {

}

function pengumuman_edit_form_submit($form, &$form_state) {
    $e_nomor = $form_state['values']['e_nomor'];
    
	$nomor = $form_state['values']['nomor'];
	$pengumuman = $form_state['values']['pengumuman'];
    
    if ($e_nomor=='')
    {
		$nomor = getnomor();
        $sql = 'insert into {pengumuman} (nomor,pengumuman,) values(\'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($nomor, $pengumuman));
    } else {
        $sql = 'update {pengumuman} set pengumuman=\'%s\' where nomor=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($pengumuman, $e_nomor));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('<front>');    
}

function getnomor() {
    
	$query = "select max(nomor) maxno from pengumuman";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		return $iv=1;
	}	 
	return $iv;
}
?>