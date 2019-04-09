if (Drupal.jsEnabled) {
   $(document).ready(function(){
        $("input:checkbox").change(function() { 
            if ($("#ok").is(":checked")) {  
					// checkbox is checked 
					alert('checked');
				} else {
					// checkbox is not checked 
					alert('not');
				}
        }); 
    });
  }; 
