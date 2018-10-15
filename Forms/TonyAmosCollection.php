<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 7/30/2018
 * Time: 2:13 PM
 */
include '../../TonyAmos/Classes/DBHelper.php';
include '../../TonyAmos/Classes/TonyDBHelper.php';

$dbName = "tonyamosdb";
$queryDB = new TonyDBHelper();
?>

<!doctype html>
<!--suppress ALL -->
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Tony Amos Collection</title>

    <!-- Importing JS files -->
    <script type="text/javascript" src="../../TonyAmos/Scripts/tonyAmosManager.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="../../TonyAmos/Scripts/frontendManager.js"></script>

    <!-- Importing CSS files -->
    <link rel="stylesheet" type="text/css" href="../../TonyAmos/Master/CSS/master.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">

    <!-- Add icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Jquery Calandar -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>
        body, html
        {
            height: 100%;
        }

        #lgSearech
        {
            display: block;
            padding-left: 2px;
            padding-right: 2px;
            border: none;
        }

        .flex-container
        {
            display: flex;
            flex-direction: column;
            margin-bottom: 50px;
            overflow-y:auto;
            overflow-x:hidden;
            height: 100%;
        }

        .flex-container > div
        {
            margin: 10px;
        }

        #divsearch
        {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #dtable
        {

        }

        .txtSearch
        {
            size: 30;
        }

        #tblAdvancedSearch
        {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        #tblAdvancedSearch td, tr
        {
            border: 1px solid #ddd;
            padding: 8px;
        }

        select
        {
            min-width: 100px;
            width: auto;
        }

        .button
        {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 7px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /*scrollbar style */
        ::-webkit-scrollbar {
            width: 5px;
        }
        .flex-container::-webkit-scrollbar {
            width: 15px;
        }
        ::-webkit-scrollbar-track {
            background-color: #eaeaea;
            border-left: 1px solid #ccc;
        }
        .flex-container::-webkit-scrollbar-thumb {

            background-color: #0067C5;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: transparent;
        }
        ::-webkit-scrollbar-thumb {

            background-color: transparent;
        }
        .flex-container::-webkit-scrollbar-thumb:hover {
            background-color: #4CAF50;
        }

        .downloadButton {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 7px 16px;
            cursor: pointer;
            font-size: 16px;
        }

    </style>
</head>
<!-- END HEADER -->
<!-- HTML BODY -->
<body>
<h2 id="page_title">Tony Amos Collection</h2>
<div class="flex-container">
    <div id="divFieldset">
        <fieldset id="flexFieldset">
            <legend id="lgSearech">Advanced Search</legend>
            <!-- ADVANCED SEARCH TABLE -->
            <table id="tblAdvancedSearch">
                <!-- FIRST ROW -->
                <tr>
                    <td>
                        <label for="from">Date From: </label>
                        <input type="text" id="from" name="from" style="size: 15px;">
                    </td>
                    <td>
                        <label for="to">Date To: </label>
                        <input type="text" id="to" name="to" style="size: 15px;">
                    </td>
                    <td>
                        Observed amount <select id="ddlOPObservedAmount" style="min-width: 10px; size: 10px;">
                            <option value="=">=</option>
                            <option value=">">></option>
                            <option value="<"><</option>
                            <option value=">=">>=</option>
                            <option value="<="><=</option>
                        </select>
                        <input type="text" class="txtSearch" id="txtObservedAmount"><br>
                    </td>
                </tr>
                <!-- SECOND ROW -->
                <tr>
                    <td>Birdcode: <?php $queryDB->GET_DDL_BIRD_CODES($dbName); ?></td>
                    <td>Mile Markers: <?php $queryDB->GET_DDL_MILEMARKERS($dbName); ?></td>
                    <td>
                        Search for Duplicate Mile Markers
                        <br>Yes <input type="radio" name="radMile" value="true" id="radMileYes">
                        <br>No &nbsp<input type="radio" name="radMile" value="false" id="radMileNo">
                    </td>
                </tr>
                <!-- THIRD ROW -->
                <tr>
                    <td>Distance From: <input type="text" id="txtDistanceFrom"><br></td>
                    <td>Distance to: <input type="text" id="txtDistanceTo"></td>
                    <td>
                        Search for Deceased Birds
                        <br>Yes <input type="radio" name="radBird" value="true" id="radDeadYes">
                        <br>No &nbsp<input type="radio" name="radBird" value="false" id="radDeadNo">
                    </td>
                </tr>
                <!-- FOURTH ROW -->
                <tr>
                    <td><div id="divDay"></div></td>
                    <td>
                    </td>
                    <td>
                        Search for Injured Birds
                        <br>Yes <input type="radio" name="radInj" value="true" id="radInjYes">
                        <br>No &nbsp<input type="radio" name="radInj" value="false" id="radInjNo">
                    </td>
                </tr>
            </table>

            <!-- SEARCH AND DOWNLOAD BUTTONS -->
            <div id="divsearch" style="padding-top: 10px;">
                <button type="button" onclick="processSearch()" class="button">Search</button>
            </div>
            <div id="divDownload" style="display: flex; align-items: center; justify-content: center;">
            </div>
        </fieldset>
    </div>

    <!-- DATA TABLES -->
    <div id="divTable">
        <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" data-page-length='20' style="width: 100%;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Bird Code</th>
                <th>Amount Observed</th>
                <th>Distance</th>
                <th>Date</th>
                <th>Julian Day</th>
                <th>Mile Marker</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th></th>
                <th>birdcode</th>
                <th>bc_observedamount</th>
                <th>bc_distance</th>
                <th>bc_date</th>
                <th>bc_julianday</th>
                <th>milemarker</th>
            </tr>
            </tfoot>
        </table>
    </div>
        <br>
        <br>
        <br>
        <br>
</div>
<?php /*include '../../TonyAmos/Master/footer.php'; */?>
</body>
<script>
    $( function() {
        var startdate = new Date(1984, 11, 1, 1, 1, 1, 1);
        var enddate = new Date(1984, 11, 8, 1, 1, 1, 1);
        var mindate = new Date(1984, 11, 1, 1, 1, 1, 1);
        var maxdate = new Date(2010, 11, 31, 1, 1, 1, 1);

        var dateFormat = "mm/dd/yy",
            from = $( "#from" ).datepicker ({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "1984:2010",
                    defaultDate: startdate,
                    minDate: mindate,
                    maxDate: maxdate
                    /*numberOfMonths: 3*/
                })
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#to" ).datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: "1984:2010",
                defaultDate: enddate,
                minDate: mindate,
                maxDate: maxdate
                /*numberOfMonths: 3*/
            })
                .on( "change", function() {
                    from.datepicker( "option", "maxDate", getDate( this ) );
                });

        function getDate( element ) {
            var date;
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                console.log("error in calandar");
                date = null;
            }

            return date;
        }
    } );
</script>
</html>
