<?php

function kegiatanppa_autocomplete($tag='', $arg='') {
    if (($tag=='uraian') || ($tag=='kode')) {            
        $matches = array();
        if ($arg) {
            switch($tag) {
                case 'uraian' :
                    $qsql = 'select kodekeg as fld1, kegiatan as fld2 from {kegiatanppa} where lower(kegiatan) like lower(\'%%%s%%\')';
                    break;
                case 'kode' :
                    $qsql = 'select kodekeg as fld2, kegiatan as fld1 from {kegiatanppa} where lower(kodekeg) like lower(\'%%%s%%\')';
                    break;
            }
            $result = db_query_range($qsql, array($arg), 0,10 );
            while ($data = db_fetch_object($result)) {
                $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
            }
        }
        drupal_json($matches);
    } else {
        switch($tag) {
            case 'showdata':
                ShowData();
                break;
            case 'bktransferdata':
                bkTransferData();
                break;
        }
    }
}
function bkTransferData() {
    $kodekeg=$_POST['kodekeg'];
    //delete first
    $query=sprintf('delete from {kegiatanrkpdsub} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);
    $query=sprintf('delete from {kegiatanrkpd} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanrkpd} select * from {kegiatanppa} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanrkpdsub} select * from {kegiatanppasub} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);
    
}
function ShowData() {
    $kodeuk = arg(4);
    //$kodeuktujuan = arg(5);
    $tipe = arg(6);
    //$tipe=1;
    
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
    //$tahun = 2012;
	$qlike='';
	$limit = 65000;
    $header = array (
        array('data' => 'T?', 'width' => '10px', 'valign'=>'top'),
		
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => ucwords(strtolower('Kode')), 'valign'=>'top', 'width'=>'90px'),
        array('data' => ucwords(strtolower('No.Urut')), 'valign'=>'top', 'width'=>'30px'),
		array('data' => ucwords(strtolower('kegiatan')), 'valign'=>'top'),
		array('data' => ucwords(strtolower('Jumlah')),  'valign'=>'top', 'width'=>'120px'),
		array('data' => ucwords(strtolower('SKPD')), 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodekeg';
    }
    $customwhere='';
    $customwhere=sprintf(' and k.jenis=2 and k.tahun=\'%s\' ', $tahun);;
    if ($kodeuk !='00')
        $customwhere .= sprintf(' and (k.kodeuk=\'%s\') ', $kodeuk);


    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select k.kodekeg, k.nomorkeg, k.tahun,k.kegiatan, k.totalpenetapan, p.kodekeg as p_kodekeg, uk.namasingkat as asaluk from {kegiatanppa} k left join {kegiatanrkpd} p on (p.kodekeg=k.kodekeg) left join {unitkerja} uk on (k.kodeuk=uk.kodeuk)' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanrkpd} k" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
            $kodekeg = $data->kodekeg;
            $p_kodekeg = $data->p_kodekeg;
            $muat = true;
            $rtitle = 'Belum ditransfer';
            if ($tipe==0) {
                if ($kodekeg==$p_kodekeg)
                    $muat=false;
            } elseif($tipe==1) {
                if ($kodekeg==$p_kodekeg)
                    $rtitle='Sudah pernah ditransfer';
            }
            if ($muat) {
                $no++;
                $check = "<input type='checkbox' name='kodekeg' value='" . $data->kodekeg . "' checked='true' title='" . $rtitle . "'/>";
                $rows[] = array (
                    array('data' => $check , 'align' => 'right', 'valign'=>'top'),
                    array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                    
                    array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
                    array('data' => $data->nomorkeg, 'align' => 'left', 'valign'=>'top'),
                    array('data' => $data->kegiatan, 'align' => 'left', 'valign'=>'top'),
                    array('data' => apbd_fn($data->totalpenetapan), 'align' => 'right', 'valign'=>'top'),
                    array('data' => $data->asaluk, 'align' => 'left', 'valign'=>'top'),
                );
            }
        }
    } else {
        //$rows[] = array (
        //    array('data' => 'data kosong', 'colspan'=>'6')
        //);
    }
    $output .= theme_box('', theme_table($header, $rows));
    if (count($rows) > 0) {
        $btn = "<a href='#bktransferppa' class='btn_blue' style='color: white;'>Transfer Data</a>";
    }
    $output .="<div id='dv_btntransfer'>" . $btn . "</div><div id='mindicator'></div><div id='transfermsg'></div>";
    echo $output;
    //echo arg(3);
    //echo 'kodeuk: ' . arg(4) . '<br/>';
    //echo 'kodeuktujuan: ' . arg(5) . '<br/>';
    //echo 'tipe: ' . arg(6) . '<br/>';
}
?>