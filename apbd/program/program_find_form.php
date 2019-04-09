<?php
// $Id$

function program_find_form() {
	drupal_set_title('Data Program');
    drupal_add_css('files/css/kegiatancam.css');		

    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['program'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Nama Program',
        '#description' => 'program yang akan dicari',
        '#autocomplete_path' => 'apbd/program/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/program' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function program_find_form_validate($form, &$form_state) {

}
function program_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['program'];
    drupal_goto('apbd/program/show/' . $v);
}
