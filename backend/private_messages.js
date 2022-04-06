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
      target_id = conversation.parent().attr("id");
      content = input.val();

      $.ajax({
         url: "backend/private_message_handle",
         type: "post",
         data: { send_message: 1, target_id: target_id, content: content },
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
            url: "backend/function_caller",
            type: "post",
            data: { row: row, target_id: target_id, hidden: 0, function: "get_and_display_messages" },
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

   // check toutes les 3 secondes s'il y a de nouveaux messages
   window.setInterval(function () {
      $(".content:first")
         .find(".content")
         .each(function () {
            content = $(this);
            target_id = content.attr("id");
            counter = content.find(".message_counter");
            last_message_id = content.find(".last_message_id").val();
            hidden = Number(content.find(".conversation").hasClass("hide"));

            has_new_messages = false;
            $.ajax({
               url: "backend/function_caller",
               type: "post",
               data: { target_id: target_id, last_message_id: last_message_id, function: "has_new_messages" },
               success: function (response) {
                  has_new_messages = response;

                  // s'il y a un nouveau message
                  if (has_new_messages == 1) {
                     $.ajax({
                        url: "backend/function_caller",
                        type: "post",
                        data: { row: counter.val(), target_id: target_id, hidden: hidden, function: "get_and_display_messages" },
                        success: function (response) {
                           content.after(response);
                           content.remove();
                        }
                     });
                  }
               }
            });
         });
   }, 3000);
});
