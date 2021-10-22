$(document).ready(function () {
   // when the user clicks on like
   $(".like").on("click", function () {
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "../sn/backend/publication_handle.php",
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
            $post.siblings().removeClass("hide");
         }
      });
   });

   // when the user clicks on unlike
   $(".dislike").on("click", function () {
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "../sn/backend/publication_handle.php",
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
            $post.siblings().removeClass("hide");
         }
      });
   });

   $(".delete").on("click", function () {
      let confirmation = confirm("Are you sure you want to delete that post?");
      if (!confirmation) return;
      let post_id = $(this).data("id");
      $post = $(this);

      $.ajax({
         url: "../sn/backend/publication_handle.php",
         type: "post",
         data: {
            delete: 1,
            post_id: post_id
         },
         success: function () {
            post = document.getElementById(post_id);
            post.parentNode.removeChild(post);
         }
      });
   });

   $(window).scroll(function () {
      var position = $(window).scrollTop();
      var bottom = $(document).height() - $(window).height();

      if (position == bottom) {
         var row = Number($("#row").val());
         var rowperpage = 5;
         row = row + rowperpage;
         // PROBLEM HERE
         $("#row").val(row);

         $.ajax({
            url: "../sn/backend/function_caller.php",
            type: "post",
            data: { row: row, function: "get_and_display_publications" },
            success: function (response) {
               // alert(response);
               $("div.publications")
                  .after("<div class='content publications'>" + response + "</div>")
                  .show()
                  .fadeIn("slow");
               $("div.publications:first").remove();
            }
         });
      }
   });
});
