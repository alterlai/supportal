$(document).ready(function() {

    /** DataTable object **/
   var table = $('.dataTable').DataTable();

   /** Filters to be applied to the search **/
   var filters = [];


   /** Event handler for add tag button **/
    $('#addTagButton').click( function()
    {
        var input = $("#searchContent").val();
        console.log("function activated");
        if (input !== "")
        {
            filters.push(input);
            updateContent();
        }

    });

    /**
     * Update main content with documents
     */
    function updateContent()
    {
        $.ajax({
            url: "/ajax/document",
            type: "GET",
            dataType: 'json',
            async: true,
            data: {'filters': filters},
            success: function (result, status) {
                console.log(result);
            },
            error: function(result, message) {
                alert("Er is iets fout gegaan. Neem contact op met een administrator.");
                console.log(message);
                console.log(result);
            }
        });
    }


    /**
     * Fire an AJAX request and update the datatable with new values
     */
   function updateTable()
   {
       parameters = getFilterOptions();
       $.ajax({
           url: "/ajax/document",
           type: "GET",
           dataType: 'json',
           async: true,
           data: getFilterOptions(),
           success: function (result, status) {
               updateDocumentTable(result)
           },
           error: function(result, message) {
               alert("Er is iets fout gegaan. Neem contact op met een administrator.");
               console.log(message);
               console.log(result);
           },
       });
   }
});

