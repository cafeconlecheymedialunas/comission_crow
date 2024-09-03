jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#registration-agent-form,#registration-company-form").on("submit", function (e) {
      e.preventDefault();
      var form = $(this);
      $customSpinner.addClass("d-flex");
      $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: form.serialize() + "&action=register_user",
          cache: false,
          success: function (response) {
              $customSpinner.removeClass("d-flex").hide();
              console.log(response)
              if (response.success) {
                  Swal.fire({
                      title: "You have successfully registered!",
                      text: "You will be redirected in a few seconds to the login page.",
                      icon: "success",
                      showConfirmButton: false,
                      timer: 2000, // 2 segundos
                  }).then(function () {
                      window.location.href = response.data.redirect_url;
                  });
              } else {
                  displayFormErrors(form, response.data);
              }
          },
      });
  });

  $("#login_form").on("submit", function (e) {
      e.preventDefault();

      $customSpinner.addClass("d-flex");

      var form = this;
      var formData = new FormData(form);

      formData.append("action", "login_user");

      $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: formData,
          cache: false,
          processData: false,
          contentType: false,
          success: function (response) {
              $customSpinner.removeClass("d-flex").hide();
            console.log(response)
              if (response.success) {
                  Swal.fire({
                      title: "You are successfully logged in!",
                      text: "You will be redirected to the dashboard",
                      icon: "success",
                      showConfirmButton: false,
                      timer: 2000, // 2 segundos
                  }).then(function () {
                      window.location.href = response.data.redirect_url;
                  });
              } else {
                  displayFormErrors(form, response.data);
              }
          },
      });
  });

  $("#reset_form").on("submit", function (e) {
      e.preventDefault();
      var form = $(this);
      $customSpinner.addClass("d-flex");
      $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: form.serialize() + "&action=reset_password",
          cache: false,
          success: function (response) {
              $customSpinner.removeClass("d-flex").hide();
              if (response.success) {
                  Swal.fire({
                      title: "Application received",
                      text: "A link to reset your password was emailed to you.",
                      icon: "success",
                      showConfirmButton: true,
                  });
              } else {
                  displayFormErrors(form, response.data);
              }
          },
      });
  });

  function initTabs() {
    var params = new URLSearchParams(window.location.search);
    var action = params.get('action');
    var role = params.get('role');

    if (action === 'login') {
        $('#login-tab').addClass('active');
        $('#login').addClass('show active');
        $('#register-agent-tab').removeClass('active');
        $('#register-company-tab').removeClass('active');
        $('#register').removeClass('show active');
    } else if (action === 'register') {
        $('#register').addClass('show active');
        $('#login-tab').removeClass('active');
        $('#login').removeClass('show active');
        
        if (role === 'company') {
            $('#register-company-tab').addClass('active');
            $('#company_name_container').show();
            $('#registration-title').text('Register as a Company');
        } else if (role === 'commercial_agent') {
            $('#register-agent-tab').addClass('active');
            $('#company_name_container').hide();
            $('#registration-title').text('Register as an Agent');
        } else {
            $('#register-agent-tab').removeClass('active');
            $('#register-company-tab').removeClass('active');
            $('#company_name_container').hide();
            $('#registration-title').text('Register');
        }
    } else {
        $('#login-tab').addClass('active');
        $('#login').addClass('show active');
        $('#register-agent-tab').removeClass('active');
        $('#register-company-tab').removeClass('active');
        $('#register').removeClass('show active');
    }
}

initTabs();

$('.nav-tabs a').on('click', function (e) {
    e.preventDefault();
    var target = $(this).attr('href');
    var role = $(this).data('role');

    $('.nav-tabs a').removeClass('active');
    $(this).addClass('active');
    $('.tab-pane').removeClass('show active');
    $(target).addClass('show active');

    if (role === 'company') {
        $('#company_name_container').show();
    } else {
        $('#company_name_container').hide();
    }
});

});
