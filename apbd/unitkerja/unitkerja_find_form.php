<?php
// $Id$

function unitkerja_find_form() {
    drupal_add_css('files/css/kegiatancam.css');	        
    drupal_set_title('Data SKPD');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['namauk'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Nama SKPD',
        '#description' => ' Nama SKPD yang akan dicari',
        '#autocomplete_path' => 'apbd/unitkerja/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/unitkerja' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function unitkerja_find_form_validate($form, &$form_state) {

}
function unitkerja_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['namauk'];
    drupal_goto('apbd/unitkerja/show/' . $v);
}
