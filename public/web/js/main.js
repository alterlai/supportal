$(document).ready(function() {

    /** DataTable object **/
   var table = $('.dataTable').DataTable();

    /**
     * Fetch document records using an AJAX request.
     */
   $('#applyFilter').click(function()
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
           error: function(result, var2, var3) {
                alert("Er is iets fout gegaan. Neem contact op met een administrator.")
            },
       });
   });

    /**
     * Get the applied filter options.
     */
   function getFilterOptions()
   {
        var selectedFilters = $('input:checked');
        var previousName = null;
        var output = {};
        console.log(selectedFilters);

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

