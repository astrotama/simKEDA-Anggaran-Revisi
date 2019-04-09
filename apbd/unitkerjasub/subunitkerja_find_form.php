<?php
// $Id$

function subunitkerja_find_form() {
    drupal_add_css('files/css/kegiatancam.css');	    
    drupal_set_title('Data Sub SKPD');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['namasuk'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Nama Sub SKPD',
        '#description' => 'Sub SKPD yang akan dicari',
        '#autocomplete_path' => 'apbd/subunitkerja/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/subunitkerja' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function subunitkerja_find_form_validate($form, &$form_state) {

}
function subunitkerja_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['namasuk'];
    drupal_goto('apbd/subunitkerja/show/' . $v);
}
