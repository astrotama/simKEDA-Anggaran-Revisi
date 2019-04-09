<?php
// $Id$

function kegiatanppa_find_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data Kegiatan PPA',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Data Kegiatan PPA',
        '#description' => 'kegiatan yang akan dicari',
        '#autocomplete_path' => 'apbd/kegiatanppa/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Cari',
    );
    return $form;
}
function kegiatanppa_find_form_validate($form, &$form_state) {

}
function kegiatanppa_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['kegiatan'];
    drupal_goto('apbd/kegiatanppa/show/' . $v);
}
