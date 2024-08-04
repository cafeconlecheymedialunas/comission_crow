<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
	<script>
	
	function displayFormErrors(form, data) {
   // Clear previous error messages
   jQuery(form).find(".error-message").empty();
   jQuery(form).find(".general-errors").hide().empty();

   var firstErrorElement = null;

   // Display field-specific errors
   jQuery.each(data.fields, function (fieldName, errorMessages) {
      var field = jQuery(form).find("#" + fieldName);
      if (field.length) {
         // Display the first error message for the field
         if (errorMessages.length > 0) {
            field.next(".error-message").text(errorMessages[0]);

            // Set the first error element for scrolling
            if (firstErrorElement === null) {
               firstErrorElement = field;
            }
         }
      }
   });

   // Display the first general error
   if (data.general && data.general.length > 0) {
      var generalErrorsElement = jQuery(form).find(".general-errors");
      if (generalErrorsElement.length) {
         // Append the first general error message
         var errorElement = jQuery('<p class="text-danger"></p>').text(data.general[0]);
         generalErrorsElement.append(errorElement);
         // Show the general errors element
         generalErrorsElement.show();

         // Set the general errors element as the first error for scrolling if no field-specific errors
         if (firstErrorElement === null) {
            firstErrorElement = generalErrorsElement;
         }
      }
   }

   // Scroll to the first error element if it exists
   if (firstErrorElement) {
      jQuery('html, body').animate({
         scrollTop: firstErrorElement.offset().top - 100 // Adjust offset as needed
      }, 500);
   }
}

</script>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>


