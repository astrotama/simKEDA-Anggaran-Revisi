<?php
// $Id$

function dinas_find_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian dinas',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['nama'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan nama',
        '#description' => 'nama yang akan dicari',
        '#autocomplete_path' => '',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Cari',
    );
    return $form;
}
function dinas_find_form_validate($form, &$form_state) {

}
function dinas_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['nama'];
    drupal_goto('apbd/dinas/show/' . $v);
}
