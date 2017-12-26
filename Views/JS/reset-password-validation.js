$(document).ready(function () {
   	
	$('#reset-password-form').validate({
        rules: {
            password: {
                required: true,
				minlength: 5,
				maxlength: 8
            },
			confirmPassword: {
                required: true,
				minlength: 5,
				maxlength: 8,
				equalTo: "#password"
            }
			
        },
        highlight: function (element) {
            $(element).closest('.control-group').removeClass('success').addClass('error');
        },
        success: function (element) {
            element.addClass('valid')
                .closest('.control-group').removeClass('error').addClass('success');
        }
    });
	
	
});