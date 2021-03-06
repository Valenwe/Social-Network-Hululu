$(document).ready(function () {
   // when the user clicks on like
   $("body").delegate(".like", "click", function () {
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: {
            like: 1,

            post_id: post_id
         },
         success: function (response) {
            $post
               .parent()
               .find("span.likes_count")
               .text(response + " likes  ");
            $post.addClass("hide");
            $post.parent().find(".dislike").removeClass("hide");
         }
      });
   });

   // when the user clicks on unlike
   $("body").delegate(".dislike", "click", function () {
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: {
            dislike: 1,

            post_id: post_id
         },
         success: function (response) {
            $post
               .parent()
               .find("span.likes_count")
               .text(response + " likes  ");
            $post.addClass("hide");
            $post.parent().find(".like").removeClass("hide");
         }
      });
   });

   // user deletes a post
   $("body").delegate(".delete_post", "click", function () {
      let confirmation = confirm("Are you sure you want to delete that post?");
      if (!confirmation) return;
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: {
            delete_post: 1,
            post_id: post_id
         },
         success: function () {
            post = document.getElementById(post_id);
            // supprime l'élément
            post.parentNode.removeChild(post);
         }
      });
   });

   // active un edit d'un post
   $("body").delegate(".edit_post", "click", function () {
      let button = $(this);
      button.parent().addClass("hide");

      let post_id = button.data("id");
      let edit_form = button
         .parent()
         .parent()
         .find("form.edit_post_form#" + post_id);
      edit_form.removeClass("hide");
   });

   // cancel un edit
   $("body").delegate(".edit_post_cancel", "click", function () {
      let edit_form = $(this).parent().parent();
      let post_id = edit_form.attr("id");

      edit_form.addClass("hide");
      let post = edit_form.parent().find("div.post#" + post_id);

      post.removeClass("hide");
   });

   // sauvegarde un edit
   $("body").delegate(".edit_post_form", "submit", function () {
      let form = $(this);

      let post_id = form.attr("id");
      let title, content;

      let post = form.parent().find("div.post#" + post_id);

      form.find("input").each(function () {
         if ($(this).attr("name") == "edit_title") title = $(this).val();
      });

      form.find("textarea").each(function () {
         if ($(this).attr("name") == "edit_content") content = $(this).val();
      });

      // si aucun changement
      if (post.find(".post_title").text() == title && post.find(".post_content").text() == content) {
         form.addClass("hide");
         post.removeClass("hide");
         return false;
      }

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: { edit_post: 1, post_id: post_id, title: title, content: content },
         success: function (response) {
            form.addClass("hide");
            post.after(response);
            post.remove();
         }
      });

      // désactive le refresh de la page
      return false;
   });

   // affichage de nouvelles publications
   $(window).scroll(function () {
      var position = $(window).scrollTop();
      var bottom = $(document).height() - $(window).height();

      if (position == bottom) {
         var counter = $(".post_counter");
         var row = Number($(".post_counter").val());
         var rowperpage = 5;
         // si on a atteint la fin des publications
         if (row % rowperpage != 0) return;
         row = row + rowperpage;
         counter.val(row);

         $.ajax({
            url: "backend/function_caller",
            type: "post",
            data: { row: row, function: "get_and_display_publications" },
            success: function (response) {
               $("div.publications").after("<div class='content publications'>" + response + "</div>");
               $("div.publications:first").remove();
            }
         });
      }
   });

   // affiche les commentaires d'un post
   $("body").delegate(".show_comments", "click", function () {
      let comments = $(this).parent().find(".comments");
      comments.removeClass("hide");
      $(this).addClass("hide");
      $(this).parent().find(".hide_comments").removeClass("hide");
   });

   // cache les commentaires d'un post
   $("body").delegate(".hide_comments", "click", function () {
      let comments = $(this).parent().find(".comments");
      comments.addClass("hide");
      $(this).addClass("hide");
      $(this).parent().find(".show_comments").removeClass("hide");
   });

   // ajoute un commentaire
   $("body").delegate(".add_comment", "click", function () {
      let post = $(this).parent();
      let post_id = post.data("id");
      let content_element = post.find(".add_comment_content");
      let content = content_element.val();

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: { add_comment: 1, post_id: post_id, content: content },
         success: function (response) {
            if (post.next("p").text() == "No comments yet") post.next("p").remove();

            content_element.parent().after(response);
            content_element.val("");
         }
      });
   });

   // supprime un commentaire
   $("body").delegate(".delete_comment", "click", function () {
      let confirmation = confirm("Are you sure you want to delete that comment?");
      if (!confirmation) return;
      let comment_id = $(this).data("id");
      $comment = $(this);

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: {
            delete_comment: 1,
            comment_id: comment_id
         },
         success: function () {
            comment = document.getElementById(comment_id);
            // supprime l'élément
            comment.parentNode.removeChild(comment);
         }
      });
   });

   // active un edit d'un commentaire
   $("body").delegate(".edit_comment", "click", function () {
      let button = $(this);
      button.parent().addClass("hide");

      let comment_id = button.data("id");
      let edit_form = button
         .parent()
         .parent()
         .find("form.edit_comment_form#" + comment_id);
      edit_form.removeClass("hide");
   });

   // cancel un edit de commentaire
   $("body").delegate(".edit_comment_cancel", "click", function () {
      let edit_form = $(this).parent().parent();
      let comment_id = edit_form.attr("id");

      edit_form.addClass("hide");
      let comment = edit_form.parent().find("div.comment_section#" + comment_id);

      comment.removeClass("hide");
   });

   // sauvegarde un edit de commentaire
   $("body").delegate(".edit_comment_form", "submit", function () {
      let form = $(this);

      let comment_id = form.attr("id");
      let content;

      let comment = form.parent().find("div.comment_section#" + comment_id);

      form.find("textarea").each(function () {
         if ($(this).attr("name") == "edit_content") content = $(this).val();
      });

      // si aucun changement
      if (comment.find(".comment_content").text() == content) {
         form.addClass("hide");
         comment.removeClass("hide");
         return false;
      }

      $.ajax({
         url: "backend/publication_handle",
         type: "post",
         data: { edit_comment: 1, comment_id: comment_id, content: content },
         success: function (response) {
            form.addClass("hide");
            comment.after(response);
            comment.remove();
         }
      });

      // désactive le refresh de la page
      return false;
   });

   // affiche de nouveaux commentaires si click
   $("body").delegate(".show_more_comments", "click", function () {
      var counter = $(this).parent().find(".comment_counter");
      var post_id = $(this).parent().parent().attr("id");

      var comments = $(this).parent().parent().find(".comments");
      var show_span = $(this).parent().parent().find(".show_comments");
      var hide_span = $(this).parent().parent().find(".hide_comments");

      var row = Number(counter.val());
      var rowperpage = 5;

      if (row % rowperpage == 0) {
         row = row + rowperpage;
         counter.val(row);

         $.ajax({
            url: "backend/function_caller",
            type: "post",
            data: { row: row, post_id: post_id, function: "get_and_display_comments" },
            success: function (response) {
               show_span.remove();
               hide_span.remove();

               comments.after(response);
               comments.remove();
            }
         });
      } else {
         $(this).text("No more comments available");
         $(this).removeClass("interactable");
      }
   });
});
