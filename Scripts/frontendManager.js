/********************************************************************
 * Function Name: Document Ready
 *
 * Description:
 * This function loads as soon as the TonyAmosCollection.php page loads.
 * It will hide the datatable and check the "no" radio buttons.
 *
 * Parameters:
 * none
 *
 * Return:
 * None
 ********************************************************************/
$( document ).ready(function()
{
    document.getElementById("divTable").style.display = "none";

    // Selecting the no's from the radio button groups
    $('input:radio[name=radBird]:nth(1)').attr('checked',true);
    $('input:radio[name=radInj]:nth(1)').attr('checked',true);
    $('input:radio[name=radMile]:nth(1)').attr('checked',true);

    $.ajax({
        url: './DownloadCSV.php',
        type: 'post',
        success: function(response)
        {
            document.getElementById("divDownload").innerHTML = response;
        }
    });

});

/********************************************************************
 * Function Name: Load Data Table
 *
 * Description:
 * This function is used to create the data table based on the search
 * results. It will load whatever WHERE clause you give it. Here is an
 * example of what is passed into "query": p.`bc_date_object`
 * BETWEEN "1984-12-01" AND "1985-01-31" AND NOT w.`bc_ID` = ANY
 * (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 51)
 * AND NOT w.`bc_ID` = ANY (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 46) LIMIT 49999
 *
 * There is a limit because if more than 50,000 records are returned,
 * the data table will crash.
 *
 * Parameters:
 * query        string      WHERE clause of a select statement
 *
 * Return:
 * Displays data table on the page
 ********************************************************************/
function loadDatatable(query)
{
    // Showing table
    document.getElementById("divTable").style.display = "block";
    var counter = 0;

    // Creating data table and defining its properties
    var table = $('#dtable').DataTable ({
        "processing": true,
        "serverside": true,
        "lengthMenu": [20, 40, 60, 80, 100],
        "destroy": true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf'
        ],

        // Displaying loading gif
        "language": {
            "processing": "<div id='divLoad'><figure id='figLoad'>" +
                "<img src='../../TonyAmos/Master/load.gif'><figcaption>Loading</figcaption></figure><div>"
        },

        "initComplete": function()
        {
            this.api().columns().every( function ()
            {
                // No drop down list for the ids
                if(counter !== 0)
                {
                    var column = this;
                    var select = $('<select style="width: 100%;"><option value="">Unselected</option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );

                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                }

                counter++; // Incrementing flag
            } );
        },

        // Getting select statement
        ajax:
            {
                url: "../../TonyAmos/Library/processQuery.php?query=" + query
            },

        columns: [
            {data: 'bc_ID'},
            {data: 'birdcode'},
            {data: 'bc_observedamount'},
            {data: 'bc_distance'},
            {data: 'bc_date'},
            {data: 'bc_julianday'},
            {data: 'milemarker'}
        ]
    });
}

/*function ddlYearChanges()
{
    // Getting the select tag and its value
    var selectTag = document.getElementById("ddlYears");
    var year = selectTag.options[selectTag.selectedIndex].value;
    year = parseInt(year);

    callGetDDLYear(year);
    /!*callGetDDLMonth(year);*!/
}*/

/*function callGetDDLYear()
{
    var selectTag = document.getElementById("ddlYears");
    var year = selectTag.options[selectTag.selectedIndex].value;
    year = parseInt(year);

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            document.getElementById("divDay").innerHTML = this.responseText;
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getDDL=" + year);
}*/

/*function callGetDDLMonth(year)
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            document.getElementById("divMonth").innerHTML = this.responseText;
        }
    };

    xhttp.open("POST", "../../TonyAmos/Library/processQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("year=" + year + "&ddlMonth=1");
}*/

/********************************************************************
 * Function Name: Process Search
 *
 * Description:
 * This function is used to process the search parameters by the user.
 *
 * Parameters:
 * None
 *
 * Return:
 * Displays data table on the page
 ********************************************************************/
function processSearch()
{
    // Conditions for submitting search
    // 1.) A date must be selected
    var daySelected = false;
    var monthSelected = false;
    var query = "";

    // Checking if date is selected
    if(document.getElementById("to").value !== "" || document.getElementById("from").value !== "")
    {
        // Getting sql select statement
        query = buildSQLQuery();
    }

    else
    {
        alert("Please specify either a date or a range of dates.");
    }

    console.log(query);

    // Populate datatable if there is a query
    if(query !== "")
    {
        loadDatatable(query);
    }
}

/********************************************************************
 * Function Name: Build SQL Query
 *
 * Description:
 * This function is used to build the where clause of the select statement
 * for the query. This defines the search parameters.
 *
 * Parameters:
 * None
 *
 * Return:
 * result   string  The where clause of the query
 ********************************************************************/
function buildSQLQuery()
{
    // Local variables and getting month
    var result = "";
    var checkDead = $('#radDeadYes').prop('checked');
    var checkInj = $('#radInjYes').prop('checked');
    var checkMile = $('#radMileYes').prop('checked');

    console.log(checkDead);

    // Getting birdcode
    var selectTagBirdCode = document.getElementById("ddlBirdCodes");
    var birdcodeNumber = selectTagBirdCode.options[selectTagBirdCode.selectedIndex].value;
    birdcodeNumber = parseInt(birdcodeNumber);

    // Getting milemarker
    var selectTagMileMarker = document.getElementById("ddlMilemarkers");
    var mile = selectTagMileMarker.options[selectTagMileMarker.selectedIndex].value;
    mile = parseInt(mile);

    // Getting values from the calenders
    var fromDate = document.getElementById("from").value;
    var toDate = document.getElementById("to").value;

    // User interacted with both calenders
    if(fromDate !== "" && toDate !== "" && fromDate !== toDate)
    {
        // Formatting string to date object
        fromDate = convertStringToDate(fromDate);
        toDate = convertStringToDate(toDate);
        result = "p.`bc_date_object` BETWEEN \"" + fromDate + "\" AND \"" + toDate + "\"";
    }

    // If calenders equal each other
    else if(fromDate !== "" && toDate !== "" && fromDate === toDate)
    {
        // Formatting string to date object
        fromDate = convertStringToDate(fromDate);
        result = "p.`bc_date_object` = '" + fromDate + "'";
    }

    // If just fromDate is selected
    else if(fromDate !== "" && toDate === "")
    {
        // Formatting string to date object
        fromDate = convertStringToDate(fromDate);
        result = "p.`bc_date_object` = '" + fromDate + "'";
    }

    // If just toDate is selected
    else if(fromDate === "" && toDate !== "")
    {
        // Formatting string to date object
        toDate = convertStringToDate(toDate);
        result = "p.`bc_date_object` = '" + toDate + "'";
    }

    else if(fromDate === "" && toDate === "")
    {
        alert("Please specify either a date or a range of dates.");
        result = "";
        return result;
    }

    // Bird code
    if(birdcodeNumber !== -1)
    {
        result += " AND w.`bc_birdcode_id` = " + birdcodeNumber;
    }

    // Mile marker
    if(mile !== -1)
    {
        result += " AND w.`bc_milemarker_id` = " + mile;
    }

    // Observed amount
    if(document.getElementById("txtObservedAmount").value !== "")
    {
        // Getting value
        var observed = document.getElementById("txtObservedAmount").value;

        // Checking to see if there are only digits
        if(validateSearchBox(observed))
        {
            observed = parseInt(observed);

            // Checking amount observed
            if(validateNumber(observed))
            {
                var ddlOperator = document.getElementById("ddlOPObservedAmount");
                var operator = ddlOperator.options[ddlOperator.selectedIndex].value;

                result += " AND w.`bc_observedamount` " + operator + " " + observed;
            }
        }
    }

    // Distances
    // Checking to see if both are filled out
    if(document.getElementById("txtDistanceFrom").value !== "" && validateSearchBox(document.getElementById("txtDistanceFrom").value)
    && document.getElementById("txtDistanceTo").value !== "" && validateSearchBox(document.getElementById("txtDistanceTo").value))
    {
        // Getting from
        var from = document.getElementById("txtDistanceFrom").value;
        from = parseInt(from);

        var to = document.getElementById("txtDistanceTo").value;
        to = parseInt(to);

        if(validateNumber(from) && validateNumber(to))
        {
            if(from < to)
            {
                result += " AND (w.`bc_distance` BETWEEN " + from + " AND " + to + " )";
            }

            else
            {
                alert("Invalid input for the distances. Distance from needs to be less than distance to.");
                result = "";
                return result;
            }
        }
    }

    // Dead bird rad checked yes
    if(checkDead === true)
    {
        result += " AND w.`bc_ID` = ANY (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 51)";
    }

    else
    {
        result += " AND NOT w.`bc_ID` = ANY (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 51)";
    }

    // Injured bird rad checked yes
    if(checkInj === true)
    {
        result += " AND w.`bc_ID` = ANY (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 46)";
    }

    else
    {
        result += " AND NOT w.`bc_ID` = ANY (SELECT `bc_ID` - 1 FROM `bc_observations` WHERE `bc_birdcode_id` = 46)";
    }

    // Duplicate mile markers
    if(checkMile === true)
    {
        result += " AND w.`bc_duplicate_milemarker` = 1";
    }

    else
    {
        result += " AND w.`bc_duplicate_milemarker` = 0";
    }

    result += " LIMIT 49999";

    return result;
}

/********************************************************************
 * Function Name: Validates Search Box
 *
 * Description:
 * This function tests a checkbox to make sure that there are only
 * digits in it.
 *
 * Parameters:
 * value    string  the input inside the text box
 *
 * Return:
 * true if it passes, false if not
 ********************************************************************/
function validateSearchBox(value)
{
    return /^\d+$/.test(value);
}

/********************************************************************
 * Function Name: Validate Number
 *
 * Description:
 * This function is used to validate a number.
 *
 * Parameters:
 * value        int      Should be the value in the text boxes (should
 *                       have been converted to an integer)
 *
 * Return:
 * true if it passes, false if not
 ********************************************************************/
function validateNumber(value)
{
    return value >= 0;
}

/********************************************************************
 * Function Name: Convert String to Date
 *
 * Description:
 * This function is used to convert the dates that are formatted from
 * the date picker plugin.
 *
 * Parameters:
 * date        string       The value shown in the date text boxes
 *                          (MM/DD/YYYY)
 *
 * Return:
 * The converted string in SQL date formatting (YYYY-MM-DD)
 ********************************************************************/
function convertStringToDate(date)
{
    var result = date.split("/");

    // Element 0 = month, Element 1 = day, Element 2 = year
    return result[2] + "-" + result[0] + "-" + result[1];
}
