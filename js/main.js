jQuery(function($) {

// Whole-script strict mode syntax

// Begin Form Validate
$("#formpdf").submit(function() {
	$.ajax({
		type : "POST",
		url : "sendform.php",
		dataType : "html",
		data : $(this).serialize(),
		beforeSend : function() {
			$("#loading").show();
		},
		success : function(response) {
			$("#response").html(response);
			$("#formpdf").hide();
			$("#loading").hide();
		}
	})
	return false;
});
// End Form Validate

});