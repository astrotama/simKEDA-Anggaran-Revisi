<?php
// $Id$

function desa_find_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_set_title('Data Desa');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian ',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['namadesa'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Nama Desa',
        '#description' => 'Desa yang akan dicari',
        '#autocomplete_path' => 'apbd/desa/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/desa' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function desa_find_form_validate($form, &$form_state) {

}
function desa_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['namadesa'];
    drupal_goto('apbd/desa/show/' . $v);
}
