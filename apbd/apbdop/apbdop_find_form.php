<?php
// $Id$

function apbdop_find_form() {
    drupal_add_css('files/css/kegiatancam.css');	    
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['nama'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan nama',
        '#description' => 'nama yang akan dicari',
        '#autocomplete_path' => 'apbd/apbdop/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/manageuser' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function apbdop_find_form_validate($form, &$form_state) {

}
function apbdop_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['nama'];
    drupal_goto('apbd/manageuser/show/' . $v);
}
