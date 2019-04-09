<?php

function program_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodepro as fld1, program as fld2 from {program} where lower(program) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodepro as fld2, program as fld1 from {program} where lower(kodepro) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}

function program_utilitas($arg1='normal', $arg2='') {
    switch($arg1) {
        case 'listprogram_asli':
            $pquery = sprintf("select kodepro, program from program where kodeu ='%s'", db_escape_string($arg2));
			
            $pres = db_query($pquery);
            $out ="";
            while ($data = db_fetch_object($pres)) {
                $out .= "'" . $data->kodepro . "':'" . $data->program . "',";
                $matches[$data->kodepro] = $data->program;
            }
            //if (strlen($out)>0) {
                //$out = "{" . substr($out, 0, strlen($out)-1 ) . "}";
            //}
            //echo $out;
            drupal_json($matches);
            break;

        case 'listprogram':
            $pquery = sprintf("select kodeo kodepro, uraian program from obyek where kodej ='%s'", db_escape_string($arg2));
			
			
            $pres = db_query($pquery);
            $out ="";
			$matches['00000'] = '00000 - Rekening Tahun Sebelumnya';
            while ($data = db_fetch_object($pres)) {
                //$out .= "'" . $data->kodepro . "':'" . $data->program . "',";
                $matches[$data->kodepro] = $data->kodepro . ' - ' . $data->program;
            }
            //if (strlen($out)>0) {
                //$out = "{" . substr($out, 0, strlen($out)-1 ) . "}";
            //}
            //echo $out;
            drupal_json($matches);
            break;
		
		case 'show':
        case 'browse':
        case 'kodeu' :
            DisplayProgram($arg1, $arg2);
            break;
        default:
            echo 'test';
            break;
    }
}

function DisplayProgram($arg='normal', $nama='') {
	switch($arg){
		case 'show':
           $qlike = " and lower(p.program) like lower('%%%s%%')";    
			break;
		case 'kodeu':
			$qlike = " and p.kodeu='%s'";
			break;
	}
    
    $header = array (
        array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kode', 'field'=> 'kodepro', 'valign'=>'top'),
		//array('data' => 'kodeu', 'field'=> 'kodeu', 'valign'=>'top'),
		//array('data' => 'np', 'field'=> 'np', 'valign'=>'top'),
		//array('data' => ucwords(strtolower('tahun')), 'field'=> 'tahun', 'valign'=>'top'),
		array('data' => 'Program', 'field'=> 'program', 'valign'=>'top'),
		//array('data' => ucwords(strtolower('sifat')), 'field'=> 'sifat', 'valign'=>'top'),
		//array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
		//array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),

		array('data' => '', 'width' => '110px'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by p.kodepro';
    }

    $customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;
	//$where = ' where true' ;

    $sql = 'select p.kodepro, p.kodeu, p.tahun, p.program, p.sifat, p.sasaran, p.target, p.np from {program} p left join {urusan} u on (p.kodeu=u.kodeu) ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    //echo $fsql;
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {program} p " . $where;
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
			$kodebid = $data->kodebid;
			if ($kodebid=='')
				$kodebid="00";
			
			$attrlink = "' program='" . $data->program .
						"' kodepro='" . $data->kodepro .
						"' tahun='" . $data->tahun .
						"' sifat='" . $data->sifat .
						"' sasaran='" . $data->sasaran .
						"' target='" . $data->target . "'";
			$editlink = "<a href='#' class='btn_blue' " . $attrlink . " style='color:white;'>Pilih</a>";            
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodepro, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->kodeu, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->tahun, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->np, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->program, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->sifat, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
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
	$pquery = "select kodeu, urusansingkat from urusan order by urusansingkat";
	$pres = db_query($pquery);
	$option = "<option value=''>---Pilih Urusan---</option>";
	while ($prow = db_fetch_object($pres)) {
		$option .= "<option value='" . $prow->kodeu . "'>" . $prow->urusansingkat . "</option>";
	}
	
	$rr[] = array (
		array('data' => 'Urusan', 'width' => '150px'),
		array('data' => "<select id='prur' style='width: 200px;'>" . $option. "</select>", 'width' => '200px'),
		array('data' => 'Nama Program', 'width' => '150px', 'align'=>'right'),
		array('data' => "<input type='text' id='i_pr' value='' style='width: 150px;'/>", 'width'=>'150px', 'align' =>'left'),
		//array('data' => "<a href='#sikg' class='btn_blue'>Cari</a>")
		array('data' => "<a href='#batal' class='btn_blue' style='color: #ffffff;'>Tutup</a>", 'align' => 'right'),
	);
    //echo $arg;
	if (!(($arg == 'kodeu')||($arg=='show'))) {
		$output ="<div id='pvtabpr'>" . $output . "</tab>";
        echo theme_box('', theme_table(array(), $rr));
	}
    
    echo $output;
        
}