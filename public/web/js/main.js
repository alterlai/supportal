$(document).ready(function() {

    /** DataTable object **/
   var table = $('.dataTable').DataTable();

    /**
     * Fetch document records using an AJAX request.
     */
   $('.filterOption input').change(function()
   {
       updateTable();
   });

    /**
     * Remove filters button action and update the table
     */
    $("#removeFilters").click(function () {
        $(".filterOption input:checked").prop("checked", false);
        updateTable();
    });

    $(".parentfilter").click(function () {
        $(this).
    });


    /**
     * Fire an AJAX request and update the datatable with new values
     */
   function updateTable()
   {
       parameters = getFilterOptions();
       $.ajax({
           url: "/ajax/documents/"+parameters['buildingId'],
           type: "GET",
           dataType: 'json',
           async: true,
           data: getFilterOptions(),
           success: function (result, status) {
               updateDocumentTable(result)
           },
           error: function(result, message) {
               alert("Er is iets fout gegaan. Neem contact op met een administrator.");
               console.log(result);
           },
       });
   }

    /**
     * Get the applied filter options.
     */
   function getFilterOptions()
   {
        var selectedFilters = $('input:checked');
        var previousName = null;
        var output = {};

        // Loop over each selected filter and add the names and values to a new array.
        for (var i = 0; i < selectedFilters.length; i++)
        {
            if (selectedFilters[i].name !== previousName)
            {
                output[selectedFilters[i].name] = [];
                output[selectedFilters[i].name].push(selectedFilters[i].value)
            }
            else
            {
                output[selectedFilters[i].name].push(selectedFilters[i].value);
            }
            previousName = selectedFilters[i].name;
        }
        output['floor'] = $('#floorInput').val();
        output['buildingId'] = $('#buildingId').val();

        console.log(output);
        return output;
   }

    /**
     * Update the home dataTable with new data.
     * @param data
     */
    function updateDocumentTable(data)
    {
        // Prepare data for table insertion
        tableData = [];
        for (var i = 0; i < data.length; i++) {
            tableData.push([
                data[i].naam,
                data[i].documentType,
                data[i].discipline,
                data[i].omschrijving,
                data[i].gebouw,
                data[i].verdieping,
                "<a href='/document/?documentId="+data[i].documentId+"'>Bekijk</a>"
            ]);
        }

        // Clear table and insert new data.
        table.clear();
        table.rows.add(tableData).draw();
    }


});

