$(document).ready(function() {

   var table = $('.dataTable').DataTable();

   $('#testbutton').click(function() {
      table.clear();
      table.rows.add(
          [
            ["test", "test", "test", "test", "test", "test"]
          ]
      ).draw();
   });

   $('#ajaxTest').click(function() {
       $.ajax({
           url: "http://localhost:8000/api/document",
           done: function (result) {
               $('#result').html(result.responseText);
               console.log(result)
            },
           error: function(result, var2, var3) {
                $('#result').html(result.responseText);
                console.log(result)
           },
           always: function(result, var2, var3) {
           $('#result').html(result.responseText);
           console.log(var2)
       },
       });
   });
});