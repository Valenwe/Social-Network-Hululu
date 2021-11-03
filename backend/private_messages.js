$(document).ready(function () {
   $("body").delegate(".show_conversation", "click", function () {
      target_id = $(this).parent().attr("id");

      $(this).parent().find(".hide_conversation").removeClass("hide");
      $(this).parent().find(".conversation").removeClass("hide");
      $(this).addClass("hide");
   });

   $("body").delegate(".hide_conversation", "click", function () {
      target_id = $(this).parent().attr("id");

      $(this).parent().find(".show_conversation").removeClass("hide");
      $(this).parent().find(".conversation").addClass("hide");
      $(this).addClass("hide");
   });

   $("body").delegate(".send_message", "click", function () {
      conversation = $(this).parent();
      input = conversation.find(".new_message_content");
      content = input.val();

      $.ajax({
         url: "../sn/backend/private_message_handle.php",
         type: "post",
         data: { send_message: 1, target_id: conversation.parent().attr("id"), content: content },
         success: function (response) {
            input.val("");
            input.before(response);
         }
      });
   });

   // affiche de nouveaux messages si click
   $("body").delegate(".show_more_messages", "click", function () {
      var content = $(this).parent().parent();
      var counter = content.find(".message_counter");
      var target_id = content.attr("id");

      var row = Number(counter.val());
      var rowperpage = 5;

      if (row % rowperpage == 0) {
         row = row + rowperpage;
         counter.val(row);

         $.ajax({
            url: "../sn/backend/function_caller.php",
            type: "post",
            data: { row: row, target_id: target_id, function: "get_and_display_messages" },
            success: function (response) {
               content.after(response);
               content.remove();
            }
         });
      } else {
         $(this).text("No more messages available");
         $(this).removeClass("interactable");
      }
   });
});
