<?php if (get_plugin_options('mailbaby_plugin_active')): ?>

    
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MailBaby Form</title>
</head>
<body>


<div class="formContainer">
    <button id="close-form-btn">&times;</button>
    <div class="container">
        <div class="image-column">
        <img src="/assets/images/colour.PNG" alt="Placeholder Image">
        </div>
        <div class="form-column">
            <h2><?php echo esc_html(get_plugin_options('mailbaby_plugin_heading')); ?></h2>
            <h4><?php echo esc_html(get_plugin_options('mailbaby_plugin_subheading')); ?></h4>
            <form id="enquiry_form" action="" method="post">
                <?php wp_nonce_field('wp_rest');?>
                <input type="text" name="name" placeholder="Your Name">
                <input type="email" name="email" placeholder="Your Email" required>
                <input type="submit" value="Submit">
                <div class="thank-you" id="thank-you-msg">Thank you for contacting us!</div>
            </form>
            <div id="form_success"></div>
            <div id="form_error"></div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $("#enquiry_form").submit(function(event) {
            event.preventDefault();
            $(".formContainer").hide();
            var form = $(this);
            $.ajax({
                type: "POST",
                url: "<?php echo get_rest_url(null, 'v1/mailbaby-form/submit');?>",
                data: form.serialize(),
                success: function(res) {
                    form.hide();
                    $("#form_success").html(res).fadeIn();
                },
                error: function() {
                    $("#form_error").html("There was an error submitting").fadeIn();
                }
            });
            // Set the hasInteracted cookie
            setCookie("hasInteracted", true, 365, "None"); // SameSite=None
        });
    });

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
</script>


</body>
</html>
<?php else: ?>

<p>This form is not active</p>

<?php endif; ?>
