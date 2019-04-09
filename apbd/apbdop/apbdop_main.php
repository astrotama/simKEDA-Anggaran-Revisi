<?php
function apbdop_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg) {

        switch($arg) {
            case 'show':
                $qlike = " and lower(u1.username) like lower('%%%s%%')";    
                break;
            case 'filter':
                $jenisuser = arg(3);
                
                if ($kodeuk !='00') {
                    $qlike .= sprintf(' and jenisuser=\'%s\' ', $jenisuser);
                } 

            default:
                drupal_access_denied();
                break;
        }
    }

	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px'),
			array('data' => 'Username', 'field'=> 'username', 'width'=>'110'),
			array('data' => 'Nama', 'field'=> 'nama'),
			array('data' => 'Unit Kerja', 'field'=> 'namasingkat', 'width' => '350px'),
			array('data' => '', 'width' => '110px'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px'),
			array('data' => 'Username', 'field'=> 'username', 'width'=>'110'),
			array('data' => 'Nama', 'field'=> 'nama'),
			array('data' => 'Bidang', 'field'=> 'namasingkat', 'width' => '350px'),
			array('data' => '', 'width' => '110px'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by u1.username';
    }


	if (isSuperuser()) {

	    //$customwhere = ' ';
		//if (!isSuperuser()) $customwhere = sprintf(' and u1.kodeuk=\'%s\' ', apbd_getuseruk());
		$where = ' where true' . $qlike ;

		$sql = 'select u0.uid, u1.username, u1.nama, u1.kodeuk, u2.namasingkat skpd from {apbdop} u1 inner join {users} u0 on u1.username=u0.name left join {unitkerja} u2 on (u1.kodeuk=u2.kodeuk) ' . $where;
		$fsql = sprintf($sql, addslashes($nama));
		
	} else {
		
		$customwhere = " and u1.kodesuk<>'' ";
		$customwhere .= sprintf(' and u1.kodeuk=\'%s\' ', apbd_getuseruk());
		$where = ' where true' . $customwhere . $qlike ;

		$sql = 'select -1 uid, u1.username, u1.nama, u1.kodesuk, u2.namasuk skpd from {apbdop} u1 inner join {subunitkerja} u2 on (u1.kodesuk=u2.kodesuk) ' . $where;
		$fsql = sprintf($sql, addslashes($nama));
	}
	
    $limit = 50;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {apbdop}" . $where;
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
			if (user_access('apbdop edit'))
				$username = l($data->username, 'apbd/manageuser/edit/' . $data->username, array('html'=>TRUE));
            else
                $username = $data->username;
			if (user_access('apbdop penghapusan'))
                //$editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/manageuser/delete/' . $data->username, array('html'=>TRUE));
                $editlink =l('Hapus', 'apbd/manageuser/delete/' . $data->username, array('html'=>TRUE));
            $no++;
			
			if (isSuperuser()) {
				
				//userskpd
				if (isVerifikatorByID($data->uid))
				{
					$hapus =l('Hapus', 'apbd/manageuser/delete/' . $data->username, array('html'=>TRUE));
					$editlink =l('TTD', 'apbd/manageuser/upload/' . $data->username, array('html'=>TRUE)). '&nbsp;'. '&nbsp;';
					$editlink .=  l('Penugasan', 'userskpd/' . $data->username, array('html'=>TRUE)) . '&nbsp;' . $hapus;
				}
				else
					$editlink = 'Penugasan&nbsp;' . $editlink;
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right'),                
					array('data' => $username, 'align' => 'left'),
					array('data' => $data->nama, 'align' => 'left'),
					array('data' => $data->skpd, 'align' => 'left'),
					array('data' => $editlink, 'align' => 'right'),
				);
				
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right'),                
					array('data' => $username, 'align' => 'left'),
					array('data' => $data->nama, 'align' => 'left'),
					array('data' => $data->skpd, 'align' => 'left'),
					array('data' => $editlink, 'align' => 'right'),
				);
			}
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('apbdop tambah')) {
		$btn .= l('Baru', 'apbd/manageuser/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	//if (user_access('apbdop pencarian'))	{
	//	$btn .= l('Cari', 'apbd/manageuser/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	//}
	if (isSuperuser())	{
		$btn .= l('Aktifitas', 'aktifitas', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}

	//if (isSuperuser()) $output .= drupal_get_form('apbdop_main_form');

    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
//	if (user_access('apbdop tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/manageuser/edit/' , array('html'=>TRUE)) ;
//	if (user_access('apbdop pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/manageuser/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function apbdop_main_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pilihan Data',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,        
    );
    $filter = arg(2);
    if (isset($filter) && ($filter=='filter')) {
        $jenisuser = arg(3);
    }
    
    //drupal_set_message($filter);

    $form['formdata']['jenisuser']= array(
        '#type' => 'radios', 
        '#title' => t('Jenis Operator'), 
        '#default_value' => $jenisuser,
        '#options' => array(    
             '' => t('Semua'),  
             '1' => t('SKPD Teknis'),   
             '2' => t('Kecamatan'), 
           ),
        '#weight' => 5,     
    );  

    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Tampilkan',
        '#weight' => 7,
    );
    return $form;
}

function apbdopmain_form_submit($form, &$form_state) {
    $jenisuser= $form_state['values']['jenisuser'];
    
    
    $uri = 'apbd/apbdop/filter/' . $jenisuser;
    drupal_goto($uri);  
}

?>