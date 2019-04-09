<?php
// $Id$

function kegiatancam_find_form() {
	drupal_add_css('files/css/kegiatancam.css');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data Kegiatan Kecamatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan Data Kegiatan Kecamatan',
        '#description' => 'kegiatan yang akan dicari',
        '#autocomplete_path' => 'apbd/kegiatancam/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#suffix' => "&nbsp;<a href='/apbd/kegiatancam' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function kegiatancam_find_form_validate($form, &$form_state) {

}
function kegiatancam_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['kegiatan'];
    drupal_goto('apbd/kegiatancam/show/' . $v);
}
