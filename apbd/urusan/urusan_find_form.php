<?php
// $Id$

function urusan_find_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['urusansingkat'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Nama Urusan',
        '#description' => 'Urusan yang akan dicari',
        '#autocomplete_path' => 'apbd/urusan/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/urusan' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function urusan_find_form_validate($form, &$form_state) {

}
function urusan_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['urusansingkat'];
    drupal_goto('apbd/urusan/show/' . $v);
}
