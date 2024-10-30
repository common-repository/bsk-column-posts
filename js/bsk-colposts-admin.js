
jQuery(document).ready( function($) {
	$("#bsk_colposts_truncate_content_eanble_chk_ID").click(function(){
		if( $(this).is(":checked") ){
			$("#bsk_colposts_truncate_limited_length_container_ID").css("display", "block");
		}else{
			$("#bsk_colposts_truncate_limited_length_container_ID").css("display", "none");
		}
	});
	
	$("#bsk_colposts_save_settings_ID").click(function(){
		var is_enabled = $("#bsk_colposts_truncate_content_eanble_chk_ID").is(":checked");
		if( is_enabled ){
			var limited_length = $("#bsk_colposts_truncate_content_limited_length_ID").val();
			
			if( limited_length < 1 ){
				alert( 'Please set value' );
				$("#bsk_colposts_truncate_content_limited_length_ID").focus();
				
				return false;
			}
		}
		$("#bsk_colposts_settings_form_id").submit();
	});
});