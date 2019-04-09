<?php

function desa_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodedesa as fld1, namadesa as fld2 from {desa} where lower(namadesa) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodedesa as fld2, namadesa as fld1 from {desa} where lower(kodedesa) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function desa_utilitas ($arg1='normal', $arg2='') {
    switch($arg1) {
        case 'listdesa':
            $ks = split(",", $arg2);
            $iks = "";
            if (count($ks)>0) {
                $iks = join("','", $ks);
            }
            $iks = "('" . $iks . "')";
            $pquery = sprintf("select kodedesa, namadesa from desa where kodedesa in %s", $iks);
            
            $pres = db_query($pquery);
            $out ="";
            while ($data = db_fetch_object($pres)) {
                //$out .= "'" . $data->kodepro . "':'" . $data->program . "',";
                $matches[$data->kodedesa] = $data->namadesa;
            }
            drupal_json($matches);
            break;
        case 'show':
        case 'kodeuk':
        case 'browse':
            DisplayDesa($arg1, $arg2);
            break;
        default:
            echo 'test';
            break;
    }    
}

function DisplayDesa($arg='normal', $nama='') {
	switch($arg){
		case 'show':
           $qlike = " and lower(d.namadesa) like lower('%%%s%%')";    
			break;
        case 'kodeuk':
            $qlike = " and lower(d.kodeuk) = '%s'";
            break;
	}

    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kode')), 'field'=> 'kodedesa', 'valign'=>'top', 'width'=>'8px'),
		array('data' => 'Kecamatan', 'field'=> 'namadesa', 'valign'=>'top'),
		array('data' => '', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by nomor';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true';		/// . $customwhere . $qlike ;

    $sql = 'select nomor,kecamatan from {kecamatanlt} ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
	//drupal_set_message($fsql);
    $limit = 20;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kecamatanlt} d " . $where;
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
			$attrlink = "' kodedesa='" . $data->nomor .
						"' namadesa='" . $data->kecamatan . "'";
			$editlink = "<a href='#' class='btn_blue' " . $attrlink . " style='color:white;'>Pi l ih</a>";            
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->nomor, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kecamatan, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
    $output .= theme_box('', theme_table($header, $rows));
    $output .= theme ('pager', NULL, $limit, 0);
	
	/*
	$pquery = "select kodeuk, namasingkat from unitkerja where iskecamatan=1 order by namasingkat";
	$pres = db_query($pquery);
	$option = "<option value=''>---Pilih Kecamatan---</option>";
	while ($prow = db_fetch_object($pres)) {
		$option .= "<option value='" . $prow->kodeuk . "'>" . $prow->namasingkat. "</option>";
	}
	*/
	$rr[] = array (
		array('data' => '&nbsp;', 'width' => '150px'),
		array('data' => "&nbsp;", 'width' => '200px'),
		array('data' => "<a href='#batal' class='btn_blue' style='color:#ffffff'>Tutup</a>", 'align' => 'right'),
	);

	$rr[] = array (
		array('data' => 'Isi Lokasi (langsung)', 'width' => '150px'),
		array('data' => "<input type='text' name='dlokasi' id='dlokasi' value='' size='30'/>", 'width' => '200px'),
		array('data' => "<a href='#pilihdlokasi' class='btn_blue' style='color:#ffffff'>Pi l ih</a>", 'align' => 'right'),
	);
	
	/*
	$rr[] = array (
		array('data' => 'Kecamatan', 'width' => '150px'),
		array('data' => "<select id='dskec' style='width: 200px;'>" . $option. "</select>", 'width' => '200px'),
		array('data' => "<a href='#batal' class='btn_blue' style='color:#ffffff'>Tutup</a>", 'align' => 'right'),
	);
	*/
    
	if (!(($arg=='kodeuk')||($arg=='show'))) {
		$output ="<div id='pvtabds'>" . $output . "</tab>";
        echo theme_box('', theme_table(array(), $rr));
	}
    
    echo $output;
}