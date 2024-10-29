jQuery(document).ready(function($) {
    $("select[name=_establishments_type]").change(function() {
	    var obj = $('select[name=_establishments_type]').val();
	    
        $.post(aumenu_ajax_object.ajax_url, {
	        action: "change",
            obj
        }, function(data) {
	        if (data) {
		        var json_data = JSON.parse(data);
		        
		        var field = "";
		        $.each(json_data, function(key, value) {
			        field += "<option value=\""+value.id+"\">"+value.name+"</option>";
		        });
		        
		        $("select[name=_establishments_id]").html(field);
			}
        });
    });
});