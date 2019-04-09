<?php

function kegiatanrkpd_autocomplete($tag='', $arg='') {
    if (($tag=='uraian') || ($tag=='uraianpendapatan') || ($tag=='uraianpembiayaan') || ($tag=='satuan')  || ($tag=='uraianbelanja')  || ($tag=='kode')) {        
        $matches = array();
        if ($arg) {
            switch($tag) {
                case 'uraian' :
                    //$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where lower(uraian) like lower(\'%%%s%%\')';
					$kode= '5';
					$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where left(kodero,1)=\'%s\' and lower(uraian) like lower(\'%%%s%%\')';

					$result = db_query_range($qsql, array($kode, $arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
					}
					
                    break;

				case 'uraianpendapatan' :
                    //$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where lower(uraian) like lower(\'%%%s%%\')';
					$kode= '4';
					$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where left(kodero,1)=\'%s\' and lower(uraian) like lower(\'%%%s%%\')';

					$result = db_query_range($qsql, array($kode, $arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
					}

                    break;

				case 'uraianpembiayaan' :
                    //$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where lower(uraian) like lower(\'%%%s%%\')';
					$kode= '6';
					$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where left(kodero,1)=\'%s\' and lower(uraian) like lower(\'%%%s%%\')';

					$result = db_query_range($qsql, array($kode, $arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
					}

                    break;
					
				case 'uraianbelanja' :
					$qsql = 'select harga, uraian from {kamusbelanja} where lower(uraian) like lower(\'%s%%\')';

					$result = db_query_range($qsql, array($arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->uraian] = $data->uraian. ' (' . apbd_fn($data->harga). ')';
					}

                    break;

				case 'satuan' :
                    //$qsql = 'select kodero as fld1, uraian as fld2 from {rincianobyek} where lower(uraian) like lower(\'%%%s%%\')';
					$qsql = 'select nomor as fld1, satuan as fld2 from {satuanlt} where lower(satuan) like lower(\'%s%%\') order by satuan';

					$result = db_query_range($qsql, array($arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->fld2] = $data->fld2;
					}
                    break;
					
				case 'kode' :
                    //$qsql = 'select kodero as fld2, uraian as fld1 from {rincianobyek} where lower(kodero) like lower(\'%%%s%%\')';
					$qsql = 'select kodero as fld2, uraian as fld1 from {rincianobyek} where left(kodero,1)=\'%s\' and lower(kodero) like lower(\'%%%s%%\')';

					$result = db_query_range($qsql, array($kode, $arg), 0,10 );
					while ($data = db_fetch_object($result)) {
						$matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
					}

                    break;
            } 
            //$result = db_query_range($qsql, array($kode, $arg), 0,10 );
            //while ($data = db_fetch_object($result)) {
            //    $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
            //}
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
    $query=sprintf('delete from {kegiatanppasub} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);
    $query=sprintf('delete from {kegiatanppa} where kodekeg=\'%s\'', db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanppa} select * from {kegiatanrkpd} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);

    $query = sprintf("insert into {kegiatanppasub} select * from {kegiatanrkpdsub} where kodekeg='%s'", db_escape_string($kodekeg));
    db_query($query);
    
}
function ShowData() {
    $kodeuk = arg(4);
    $kodeuktujuan = arg(5);
    $tipe = arg(6);
    
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	$qlike='';
	$limit = 65000;
    $header = array (
        array('data' => 'T?', 'width' => '10px', 'valign'=>'top'),
		
        array('data' => 'N', 'width' => '10px', 'valign'=>'top'),
        array('data' => ucwords(strtolower('Kode')),  'valign'=>'top', 'width'=>'90px'),
        array('data' => ucwords(strtolower('No Urut')),  'valign'=>'top', 'width'=>'30px'),
		array('data' => ucwords(strtolower('kegiatan')),  'valign'=>'top'),
		array('data' => ucwords(strtolower('Jumlah')),  'valign'=>'top', 'width'=>'120px'),
		array('data' => ucwords(strtolower('SKPD')),  'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodekeg';
    }
    $customwhere='';
    $customwhere=sprintf(' and k.totalpenetapan > 0 and k.dekon=0 and k.tahun=\'%s\' ', $tahun);;
    if ($kodeuk !='00')
        $customwhere .= sprintf(' and (k.kodeuk=\'%s\') ', $kodeuk);


    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select k.kodekeg, k.nomorkeg, k.tahun,k.kegiatan, k.totalpenetapan, p.kodekeg as p_kodekeg, uk.namasingkat as asaluk from {kegiatanrkpd} k left join {kegiatanppa} p on (p.kodekeg=k.kodekeg) left join {unitkerja} uk on (k.kodeuk=uk.kodeuk)' . $where;
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
        $btn = "<a href='#transferrkpd' class='btn_blue' style='color: white;'>Transfer Data</a>";
    }
    $output .="<div id='dv_btntransfer'>" . $btn . "</div><div id='mindicator'></div><div id='transfermsg'></div>";
    echo $output;
    //echo arg(3);
    //echo 'kodeuk: ' . arg(4) . '<br/>';
    //echo 'kodeuktujuan: ' . arg(5) . '<br/>';
    //echo 'tipe: ' . arg(6) . '<br/>';
}
?>