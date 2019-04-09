<?php

function kegiatanskpd_autocomplete($tag='', $arg='') {
    if (($tag=='uraian') || ($tag=='kode')) {    
        $matches = array();
        if ($arg) {
            switch($tag) {
                case 'uraian' :
                    $qsql = 'select kodekeg as fld1, kegiatan as fld2, plafon from {kegiatanskpd} where lower(kegiatan) like lower(\'%%%s%%\')';
                    break;
                case 'kode' :
                    $qsql = 'select kodekeg as fld2, kegiatan as fld1, plafon from {kegiatanskpd} where lower(kodekeg) like lower(\'%%%s%%\')';
                    break;
            }
			
			if (!isSuperuser()) {
				$qsql .= sprintf(' and kodeuk=\'%s\' ', apbd_getuseruk());
				if (isUserKecamatan())
					$qsql .= sprintf(' and kodesuk=\'%s\' ', apbd_getusersuk());
			}
            $result = db_query_range($qsql, array($arg), 0,10 );
            while ($data = db_fetch_object($result)) {
                $matches[$data->fld2] = $data->fld2. ' (' . apbd_fn($data->plafon) . ')';
            }
        }
        drupal_json($matches);
    } else {
        switch($tag) {
            case 'showdata':
                ShowData();
                break;
            case 'transferdata':
                TransferData();
                break;
        }
    }
}

function TransferData() {
    $kodekeg=$_POST['kodekeg'];
    //delete first
    $query=sprintf('delete from {kegiatanrkpdsub} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);
    $query=sprintf('delete from {kegiatanrkpd} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanrkpd} select * from {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanrkpdsub} select * from {kegiatanskpdsub} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);
    
}
function ShowData() {
    $kodeuk = arg(4);
    $kodeuktujuan = arg(5);
    $tipe = arg(6);
    
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
    //$tahun = 2012;
	$qlike='';
	$limit = 65000;
    $header = array (
        array('data' => 'T?', 'width' => '10px', 'valign'=>'top'),
		
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Kegiatan')),  'valign'=>'top'),
		array('data' => ucwords(strtolower('Sasaran')), 'valign'=>'top'),
		array('data' => ucwords(strtolower('Target')), 'valign'=>'top'),		
		array('data' => ucwords(strtolower('Jumlah')),  'valign'=>'top', 'width'=>'120px'),
		array('data' => ucwords(strtolower('SKPD')), 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by uk.namasingkat, k.kegiatan';
    }
    $customwhere='';
    $customwhere=sprintf(' and k.totalpenetapan > 0 and k.dekon=0 and k.tahun=\'%s\' ', $tahun);;
    if ($kodeuk !='00')
        $customwhere .= sprintf(' and (k.kodeuk=\'%s\') ', $kodeuk);


    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select k.kodekeg, k.nomorkeg, k.tahun,k.kegiatan, k.sasaran, k.target, k.totalpenetapan, p.kodekeg as p_kodekeg, uk.namasingkat as asaluk from {kegiatanskpd} k left join {kegiatanrkpd} p on (p.kodekeg=k.kodekeg) left join {unitkerja} uk on (k.kodeuk=uk.kodeuk)' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanskpd} k" . $where;
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
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
                $check = "<input type='checkbox' name='kodekeg' value='" . $data->kodekeg . "' checked='true' title='" . $rtitle . "'/>";
                $rows[] = array (
                    array('data' => $check , 'align' => 'right', 'valign'=>'top'),
                    array('data' => $no, 'align' => 'right', 'valign'=>'top'),             
                    array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
                    array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
                    array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
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
        $btn = "<a href='#transferskpd' class='btn_blue' style='color: white;'>Transfer ke RKPD</a>";
    }
    $output .="<div id='dv_btntransfer'>" . $btn . "</div><div id='mindicator'></div><div id='transfermsg'></div>";
    echo $output;
    //echo arg(3);
    //echo 'kodeuk: ' . arg(4) . '<br/>';
    //echo 'kodeuktujuan: ' . arg(5) . '<br/>';
    //echo 'tipe: ' . arg(6) . '<br/>';
}
?>