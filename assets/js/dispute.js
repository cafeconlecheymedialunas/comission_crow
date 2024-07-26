jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#message-form").on("submit", function (e) {
    e.preventDefault();

    var data = {
      action: "save_message",
      security: $("#message_nonce").val(),
      post_id: $("#post_id").val(),
      from: $("#from").val(),
      to: $("#to").val(),
      message: $("#message").val(),
    };

    $.post(ajax_object.ajax_url, data, function (response) {
      if (response.success) {
        $("#chat-box").empty();
        response.data.messages.forEach(function (msg) {
          var fromUser =
            msg.from === parseInt($("#from").val()) ? "You" : "Other User";
          $("#chat-box").append(
            '<div class="message"><strong>' +
              fromUser +
              ":</strong><p>" +
              msg.message +
              "</p></div>",
          );
        });
      } else {
        alert("Error saving message.");
      }
    });
  });
});
