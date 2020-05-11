$(document).ready(function() {

    /** DataTable object **/
   var table = $('.dataTable').DataTable();

   /** Filters to be applied to the search **/
   var filters = [];


   /** Event handler for add tag button **/
    $('#addTagButton').click( function()
    {
        handleSubmit()
    });

    /** Event handler for add tag input field on enter press **/
    $("#searchContent").keyup(function(e)
    {
        if(e.keyCode === 13) // enter
        {
            handleSubmit()
        }
    });

    /** Event handler for document on click event **/
    $("body").on('click', '.documentCard', function ()
    {
        var selectedDocument = $(this).find('li');
        console.log(selectedDocument);

       $("#documentModal").modal();

       var modalFields = $("#documentModal").find('.documentInfo');

       modalFields.each(function(index) {
          $(this).html(selectedDocument[index].textContent);
       });
    });


    /** Event handler for closing pill button **/
    $(document).on('click', '.tagPill', function ()
    {
        value = $(this).find('span').text();

        // Delete the element
        $(this).remove();

        // Remove the value from the array
        index = filters.indexOf(value);
        if (index > -1) {
            filters.splice(index, 1);
        }

        // Update the table
        updateContent();
    });

    /**
     * Handle the sumbission of the search form. This is called by multiple events
     */
    function handleSubmit()
    {
        var input = $("#searchContent").val();
        if (input !== "")
        {
            filters.push(input);
            $("#searchContent").val('');
            addPill(input);
            updateContent();
        }
    }

    /**
     * Update main content with documents
     */
    function updateContent()
    {
        $(".loading").show();
        $.ajax({
            url: "/ajax/document",
            type: "GET",
            dataType: 'json',
            async: true,
            data: {'filters': filters},
            success: function (result, status) {
                updateDocumentCards(result);
            },
            error: function(result, message) {
                alert("Er is iets fout gegaan. Neem contact op met een administrator.");
                console.log(message);
                console.log(result);
            },
            complete: function () {
                $(".loading").hide();
            }
        });
    }

    /**
     * Add a filter pill below the search bar
     */
    function addPill(input)
    {
        $('#pillContainer').append("<div class='tagPill'><span>" + input + "</span><a class='closePill'> X</a></div>")
    }

    /**
     * Populate documents page with documents
     */
    function updateDocumentCards($documents) {
        // Clear tileContainer of existing documents
        $('.tileContainer').html('');
        console.log($documents);

        $documents.forEach(function($doc, index)
        {
            console.log($doc)
            $('.tileContainer').append(
                "<div class=\'documentCard\'>\n" +
                "                        <div class=\"pdfPreview\">\n" +
                "                            <svg class=\"bi bi-image-fill\" width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">\n" +
                "                                <path fill-rule=\"evenodd\" d=\"M.002 3a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2h-12a2 2 0 01-2-2V3zm1 9l2.646-2.354a.5.5 0 01.63-.062l2.66 1.773 3.71-3.71a.5.5 0 01.577-.094L15.002 9.5V13a1 1 0 01-1 1h-12a1 1 0 01-1-1v-1zm5-6.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z\" clip-rule=\"evenodd\"/>\n" +
                "                            </svg>\n" +
                "                        </div>\n" +
                "                        <div class=\"documentDetails\">\n" +
                "                            <ul>\n" +
                "                                <li>"+ $doc.naam + "</li>\n" +
                "                                <li>"+ $doc.locatie +"</li>\n" +
                "                                <li>"+  ($doc.gebouw ? $doc.gebouw : "") +"</li>\n" +
                "                                <li>"+  ($doc.area ? $doc.area : "") + "</li>\n" +
                "                                <li>Verdieping: " +   $doc.verdieping + "</li>\n" +
                "                                <li>"+  $doc.discipline + $doc.disciplineOmschrijving + "</li>\n" +
                "                                <li>"+  $doc.updatedAt + "</li>\n" +
                "                                <li>" + $doc.documentSoort + "</li>\n" +
                "                            </ul>\n" +
                "                        </div>\n" +
                "                    </div>");
        })
    }
});

