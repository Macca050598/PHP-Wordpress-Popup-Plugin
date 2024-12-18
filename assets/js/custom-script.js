jQuery(document).ready(function($) {
    // Function to get the value of a cookie by its name
    function getCookie(name) {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf(name + '=') === 0) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

      // Function to set a cookie with a given name, value, expiration time, and SameSite attribute
      function setCookie(name, value, days, sameSite) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=" + sameSite;
    }

    

    // Check if the formClosed cookie is set to true
    if (getCookie('formClosed') === 'true') {
        console.log("Form has been previously closed.");
        // Calculate the time since the form was last closed
        var timeSinceClose = new Date().getTime() - parseInt(getCookie('formClosedTime'));
        console.log("Time since form closed:", timeSinceClose);
        // Check if at least an hour has passed since the form was last closed
        if (timeSinceClose < 3600000) {
            console.log("Less than an hour has passed since form was closed. Hiding form.");
            // Hide the form
            $(".formContainer").hide();
           
        }
        else  { // 3600000 milliseconds = 1 hour 
            console.log("At least an hour has passed since form was closed. Showing form again.");
            // Show the form again
            $(".formContainer").fadeIn();

       
        }
    } else if (getCookie('formClosed') === 'false') {
        console.log("Form has not been previously closed.");
        // The formClosed cookie is not set, so show the form after 5 seconds
        setTimeout(function() {
            console.log("Form will appear after 5 seconds.");
            $(".formContainer").fadeIn();
        }, 5000);
    }
     // Check if the close button is clicked
     $("#close-form-btn").click(function() {
        console.log("Close button clicked.");
        // Set the formClosed cookie to true with the current time
        // document.cookie = "formClosed=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/";
        setCookie("formClosed", true, 365, "None");

        // Set the formClosedTime cookie to the current time
        // document.cookie = "formClosedTime=" + new Date().getTime() + "; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/";
        setCookie("formClosedTime", new Date().getTime(), 365, "None");

        // Calculate the time since the form was last closed
        console.log("Time since form closed:", timeSinceClose);
        // Set the timeSinceClose cookie to the calculated time since close
        // document.cookie = "timeSinceClose=" + timeSinceClose + "; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/";
        setCookie("timeSinceClose", timeSinceClose, 365, "None");

        // Hide the form
        $(".formContainer").hide();
    });
   
    // Check if the cookie indicating form interaction exists
     // Check if the hasInteracted and formClosed cookies are set to false
     var hasInteracted = getCookie('hasInteracted') === 'true';
     var formClosed = getCookie('formClosed') === 'true';
 
     if (!hasInteracted && !formClosed) {
         console.log("User has not interacted before and form is not closed.");
         // Show the form after 5 seconds
         setTimeout(function() {
             console.log("Form will appear after 5 seconds.");
             $(".formContainer").fadeIn();
         }, 5000);
     }

    // Function to set a cookie with a given name, value, and expiration time
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Function to get the value of a cookie by its name
    function getCookie(name) {
        var nameEQ = name + "=";
        var cookies = document.cookie.split(';');
        for(var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];
            while (cookie.charAt(0) == ' ') {
                cookie = cookie.substring(1, cookie.length);
            }
            if (cookie.indexOf(nameEQ) == 0) {
                return cookie.substring(nameEQ.length, cookie.length);
            }
        }
        return null;
    }

    // Event listener for form submission
    $('#enquiry_form').submit(function(event) {
        // Set the cookie to indicate that the user has interacted with the form
        setCookie("hasInteracted", true, 365); // Cookie expires in 365 days
    });
});

jQuery(document).ready(function($) {
    // Event listener for the close button
    $("#close-form-btn").click(function() {
        // Hide the form when the button is clicked
        $(".formContainer").hide();
    });
});



// jQuery(document).ready(function($) {
//     setTimeout(function() {
//         $(".formContainer").fadeIn(); // Show the form after 5 seconds
//     }, 3000);
// });

// jQuery(document).ready(function($) {
//     // Event listener for the close button
//     $("#close-form-btn").click(function() {
//         // Hide the form when the button is clicked
//         $(".formContainer").hide();
//     });
// });


// jQuery(document).ready(function($) {
//     wp.data.select('core').getCurrentUser().then(user => {
//         if (!getCookie('firsttime') && user.capabilities.includes('Administrator')) {
//             // User is an admin and it's their first time visiting
//             setTimeout(function() {
//                 $('#enquiry_form').fadeIn(); // Show the form after 5 seconds
//             }, 5000);
//             // Set the cookie to true so the form won't show again
//             setCookie('firsttime', true);
//         }
//     });
// });

// function setCookie(c_name, value, exdays) {
//     var exdate = new Date();
//     exdate.setDate(exdate.getDate() + exdays);
//     var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
//     document.cookie = c_name + "=" + c_value;
// }

// function getCookie(c_name) {
//     var c_value = document.cookie;
//     var c_start = c_value.indexOf(" " + c_name + "=");
//     if (c_start == -1) {
//         c_start = c_value.indexOf(c_name + "=");
//     }
//     if (c_start == -1) {
//         c_value = null;
//     } else {
//         c_start = c_value.indexOf("=", c_start) + 1;
//         var c_end = c_value.indexOf(";", c_start);
//         if (c_end == -1) {
//             c_end = c_value.length;
//         }
//         c_value = unescape(c_value.substring(c_start, c_end));
//     }
//     return c_value;
// }

// function delCookie(name) {
//     document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
// }
