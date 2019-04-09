<?php
// $Id$

function kegiatanrkpd_find_form() {
    drupal_set_title('Kegiatan RKPD');
	drupal_add_css('files/css/kegiatancam.css');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data Kegiatan RKPD',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Data Kegiatan RKPD',
        '#description' => 'kegiatan yang akan dicari',
        '#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanrkpd' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function kegiatanrkpd_find_form_validate($form, &$form_state) {

}
function kegiatanrkpd_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['kegiatan'];
    drupal_goto('apbd/kegiatanrkpd/show/' . $v);
}
