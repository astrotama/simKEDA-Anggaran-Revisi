<?php

function kegiatanrkpd_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodekeg = arg(3);
    if (isset($kodekeg)) {
        $customwhere = '';
        if (!isSuperuser()) {
            $customwhere .= sprintf(' and kodeuk=\'%s\' ', apbd_getuseruk());	
        }	
        
        $sql = 'select kodekeg,tahun,kodepro,kodeuk,kodeuktujuan,sifat,kegiatan,lokasi,sasaran,target,totalsebelum,total,targetsesudah,nilai,lolos,asal,kodekec,apbdkab,apbdprov,apbdnas,kodebid,dekon,apbp,apbn,kodesuk,totalsebelum2,totalsebelum3,totalpenetapan,sumberdana,pnpm from {kegiatanrkpd} where kodekeg=\'%s\'' . $customwhere;
        $res = db_query(db_rewrite_sql($sql), array($kodekeg));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Data Kegiatan RKPD',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanrkpd',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function kegiatanrkpd_delete_form_validate($form, &$form_state) {
}
function kegiatanrkpd_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodekeg = $form_state['values']['kodekeg'];
        $sql = 'DELETE FROM {kegiatanrkpd} WHERE kodekeg=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodekeg));
        if ($res) {
            $query = sprintf("DELETE FROM {kegiatanrkpd} where kodekeg='%s'", db_escape_string($kodekeg));
            db_query($query);
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanrkpd');
        }
        
    }
}
?>