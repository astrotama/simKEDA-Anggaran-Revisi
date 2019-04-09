<?php
// $Id$

function kegiatanlt_find_form() {
    drupal_add_css('files/css/kegiatancam.css');
    drupal_set_title('Master Data Kegiatan');

    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $form['formdata']['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Isikan kegiatan',
        '#description' => 'kegiatan yang akan dicari',
        '#autocomplete_path' => 'apbd/kegiatanlt/utils_auto/uraian',
    );
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanlt' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function kegiatanlt_find_form_validate($form, &$form_state) {

}
function kegiatanlt_find_form_submit($form, &$form_state) {
    $v=$form_state['values']['kegiatan'];
    drupal_goto('apbd/kegiatanlt/show/' . $v);
}
