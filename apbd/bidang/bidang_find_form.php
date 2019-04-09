<?php
// $Id$

function bidang_find_form() {
    drupal_add_css('files/css/kegiatancam.css');
    drupal_set_title('Data Bidang');
    
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['bidang'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan bidang',
        '#description' => 'bidang yang akan dicari',
        '#autocomplete_path' => 'apbd/bidang/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/bidang' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function bidang_find_form_validate($form, &$form_state) {

}
function bidang_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['bidang'];
    drupal_goto('apbd/bidang/show/' . $v);
}
