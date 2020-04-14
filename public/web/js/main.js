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
            addPill(input);
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
     * Add a filter pill below the search bar
     */
    function addPill(input)
    {
        $('#pillContainer').append("<div class='tagPill'>" + input + " </div>")
    }
});

