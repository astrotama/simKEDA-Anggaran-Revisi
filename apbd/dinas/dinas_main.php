<?php
function dinas_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanlt.js');
    
	switch($arg) {
		case 'show':
			$qlike = " and lower(nama) like lower('%%%s%%')";
			$_SESSION['kodeu'] = '';
			break;
		case 'filter':
			$kodeu = arg(3);
			$_SESSION['kodeu'] = '';
			if (strlen($kodeu)>0) {
				$_SESSION['kodeu'] = $kodeu;
				$qlike .= sprintf(" and kodeu='%s' ", db_escape_string($kodeu));
			}
			break;
		default:
			$_SESSION['kodeu'] = '';
			break;
		
	}
			
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        		//array('data' => ucwords(strtolower('id')), 'field'=> 'id', 'valign'=>'top'),
		array('data' => ucwords(strtolower('kodeuk')), 'field'=> 'kodeuk', 'valign'=>'top'),
		array('data' => ucwords(strtolower('kodeu')), 'field'=> 'kodeu', 'valign'=>'top'),
		array('data' => ucwords(strtolower('nourut')), 'field'=> 'nourut', 'valign'=>'top'),
		array('data' => ucwords(strtolower('nama')), 'field'=> 'nama', 'valign'=>'top'),

		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by id';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select id,kodeuk,kodeu,nourut,nama from {ukurusan}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {ukurusan}" . $where;
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
			$editlink = '';
			if (user_access('dinas edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/dinas/edit/' . $data->id, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('dinas penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/dinas/delete/' . $data->id, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodeuk, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodeu, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->nourut, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->nama, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
    $output .= theme_box('', theme_table($header, $rows));
	if (user_access('dinas tambah'))
		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/dinas/edit/' , array('html'=>TRUE)) ;
	if (user_access('dinas pencarian'))		
        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/dinas/find/' , array('html'=>TRUE)) ;

	$output .= $btn . "<div id='fl_filter'>" . drupal_get_form ('dinas_filter_form') . "</div>" . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function dinas_filter_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Filter Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	$u = arg(3);
	$pquery = "select kodeu, urusansingkat from urusan order by urusansingkat";
	$pres = db_query($pquery);
	$urusan = array();
	$urusan[''] = '---SEMUA URUSAN---';
	
	while ($prow = db_fetch_object($pres)) {
		$urusan[$prow->kodeu] = $prow->urusansingkat ;
	}
	
    $form['formdata']['flurusan'] = array (
        '#type' => 'select',
        '#title' => 'Urusan',
		'#options' => $urusan,
		'#default_value' => $u,
    );
	
	$form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Filter',
    );
	
	return $form;
}
function dinas_filter_form_submit($form, &$form_state) {
	$urusan = $form_state['values']['flurusan'];
	
	drupal_goto("apbd/dinas/filter/" . $urusan );
}
?>