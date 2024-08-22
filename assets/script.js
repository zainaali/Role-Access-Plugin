function load() {
    if (arr.pages.show_pages) {
        let pages = arr.pages.show_pages

        let pp = pages.map(c => `page-item-${c}`)
        document.querySelectorAll('.page_item').forEach(function (e) {
            let classs = e.classList.value
            let o = classs.match(/page\-item(.*\s)/g)
            let selector = o == null ? classs.match(/page\-item(.*)/g)[0] : o[0].replace(/\s/g, '');
            if (!pp.includes(selector)) e.style.display = 'none'
        })
    }

}

function settingToggle(source) {
    
    checkboxes = document.getElementsByName('pages[]');
    
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}

window.load = load()


jQuery( document ).ready(function($) {
    $(".rest_pss_b").click(function(){
       $(".login_f").toggle();
       $(".reset_f").toggle();
        
    });
});


jQuery(document).ready(function($) {
    $('.f_login').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission.

        // Get form data.
        var obj_payload         = {};
          obj_payload.username  = $('#username').val();
          obj_payload.password = $('#password').val();
          obj_payload.action      = 'lmic_login';
          obj_payload.security    = $('#security').val();
         
          $.ajax({
            url: lmic_ajax_obj.ajax_url,
            type: 'POST',
            cache: false,
            data: obj_payload,
           success: function(response) {
                if (response.success) {
                    // Login was successful, display a success message
                    $('#login-message').html('<div class="success">Login successful</div>');
                    window.location.href = lmic_ajax_obj.site_lmic_url;


                } else {
                    // Login failed, display an error message
                    $('#login-message').html('<div class="error">Login failed: ' + response.message + '</div>');
                }
            },
            error: function(response) {
                // Handle errors
                 $('#login-message').html('<div class="error">Login failed: ' + response.message + '</div>');
            }
          });   
       
    });
});

jQuery(document).ready(function($) {
    $('#password-reset-form').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission.

        // Get reCAPTCHA response.
        var recaptchaResponse = grecaptcha.getResponse();

        // Check if reCAPTCHA is verified.
        if (recaptchaResponse.length === 0) {
             $('#rest_message').html('<div class="error"> Please complete the reCAPTCHA.</div>');
            return;
        }

        // Collect form data.
        var formData = new FormData(this);

        // Convert FormData to a plain object.
        var formObject = {};
        formData.forEach(function(value, key) {
            formObject[key] = value;
        });

        // Add reCAPTCHA response to the form data.
        formObject["g-recaptcha-response"] = recaptchaResponse;

        // Send an AJAX request.
        $.ajax({
            url: lmic_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: "lmic_reset_password",
                security_nonce: $('#security_nonce').val(),
                formData: formObject
            },
            success: function(response) {
               
                if (response.success) {
                    // Password reset request was successful.
                    alert('Password reset request successful. Check your email for further instructions.');
					 jQuery(".search-popup").toggleClass("active");
                } else {
                    // Password reset request failed.
                     $('#rest_message').html('<div class="error"> ' + response.message + '</div>');
                }
            },
            error: function() {
                // Handle errors.
                alert('AJAX error occurred.');
            }
        });
    });
	
	
	
	  $('#new_pass_form').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission.

        // Convert FormData to a plain object.
        
        // Send an AJAX request.
        $.ajax({
            url: lmic_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: "handle_ajax_reset_password_confirmation",
                security_nonce_new_pass: $('#security_nonce_new_pass').val(),
              	new_password: $('#new_password').val(),
				u_mail: $('#u_mail').val(),
            },
            success: function(response) {
               
                if (response.success) {
                    // Password reset request was successful.
					$('.new_pass_msg').html('<div class="success_ms">' + response.message + '</div>');
                } else {
                    // Password reset request failed.
                     $('.new_pass_msg').html('<div class="error"> ' + response.message + '</div>');
                }
            },
            error: function() {
                // Handle errors.
                alert('AJAX error occurred.');
            }
        });
    });
});