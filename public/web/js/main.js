$(document).ready(function() {

   var table = $('.dataTable').DataTable();

   $('#testbutton').click(function()
   {
      table.clear();
      table.rows.add(
          [
            ["test", "test", "test", "test", "test", "test"]
          ]
      ).draw();
   });

   $('#ajaxTest').click(function()
   {
       $.ajax({
           url: "/ajax/document",
           type: "GET",
           dataType: 'json',
           async: true,
           success: function (result, status) {
               console.log(result);
               updateDocumentTable(result)
            },
           error: function(result, var2, var3) {
                alert("Er is iets fout gegaan. Neem contact op met een administrator.")
            },
       });
   });

    function updateDocumentTable(data)
    {
        // Prepare data for table insertion
        tableData = [];
        for (var i = 0; i < data.length; i++) {
            tableData.push([
                data[i].naam,
                data[i].discipline,
                data[i].omschrijving,
                data[i].gebouw,
                data[i].verdieping,
                "<a href='/document/"+data[i].documentId+"'>Bekijk</a>"
            ]);
        }

        // Clear table and insert new data.
        table.clear();
        table.rows.add(tableData).draw();
    }
});

