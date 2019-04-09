<?php
function kegiatanrevisi_edit1_form() {
	//apbd/kegiatanrevisi/edit1
	drupal_add_css('files/css/kegiatancam.css');

	
	drupal_set_title('Usulan Revisi - Langkah #1, Pilih Jenis Revisi');
	
	$id = arg(3);
	$jenisrevisi = arg(4);
	if (isset($jenisrevisi)==false) 
		$jenisrevisi = '1';
	
	if (isset($id)) {
        $sql = 'select id,jenisrevisi from {kegiatanrevisiperubahan} where id=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$id = $data->id;
				$jenisrevisi = $data->jenisrevisi;
			}
		} else
			$id = 0;
	} else 
		$id = 0;
	

	$form['id']= array(
		'#type' => 'value', 
		'#value' => $id, 
	);

	$form['jenisrevisi']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis Revisi'), 
		//'#description'  => 'Jenissd belanja',
		'#default_value' => $jenisrevisi, // changed
		'#options' => array(	
			 '1' => t('[1] Perubahan/Pergeseran Anggaran Tetap'), 	
			 '2' => t('[2] Kesalahan Administrasi'),	
			 '3' => t('[3] Penambahan/Pengurangan pada Pagu Anggaran Tetap dari Dana Transfer (DAK/Banprov)'), 	
			 //'4' => t('[4] Mendesak/Darurat'), 	
		   ), 
	);
	
	$form['next'] = array(
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Batal</a>",
		'#value' => 'Lanjut >>'
	);
  return $form;
	
}
function kegiatanrevisi_edit1_form_validate($form, &$form_state) {

  $jenisrevisi = $form_state['values']['jenisrevisi'];
  if (!$jenisrevisi) {
    form_set_error('jenisrevisi', t('Jenis revisi harus dipilih salah satu.'));
  }

}
 
function kegiatanrevisi_edit1_form_submit($form, &$form_state) {
	$id = $form_state['values']['id'];
	$jenisrevisi = $form_state['values']['jenisrevisi'];

	//$form_state['redirect'] = 'kegiatanrevisi2/' . $jenisrevisi ;
	$form_state['redirect'] = 'apbd/kegiatanrevisi/edit2/' . $id . '/' . $jenisrevisi ;

}
?>