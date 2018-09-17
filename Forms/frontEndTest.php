<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 7/30/2018
 * Time: 2:13 PM
 */
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

    <!-- Importing CSS files -->
    <link rel="stylesheet" type="text/css" href="../../TonyAmos/Master/CSS/master.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">


    <style>
        body, html
        {
            height: 100%;
        }

        #divLoad
        {
            display: flex;
            justify-content: center;
        }

        #divLoad > #figLoad
        {
            border: thin silver solid;
            background-color: white;
            text-align: center;
            border-radius: 25px;
        }

        .flex-container
        {
            display: flex;
            flex-direction: column;
        }

        .flex-container > div
        {
            margin: 10px;
        }

        #dtable
        {

        }
    </style>
</head>
<!-- END HEADER -->
<!-- HTML BODY -->
<body>
<h2 id="page_title">Tony Amos Collection</h2>
<div class="flex-container">
    <div style="display:none;" id="divCollection">
        Collection
        <select id="ddlCollection">
            <option value="-1">Unselected</option>
            <option value="BCHobs1">BCHobs1</option>
            <option value="BCHobs2">BCHobs2</option>
            <option value="GARBGOBS">GARBGOBS</option>
            <option value="SANJOobs">SANJOobs</option>
        </select>
    </div>
    <div>
        Year <select id="ddlYears" onchange="ddlYearChanges()">
            <option value="-1">Unselected</option>
            <option value="1984">1984</option>
            <option value="1985">1985</option>
            <option value="1986">1986</option>
            <option value="1987">1987</option>
            <option value="1988">1988</option>
            <option value="1989">1989</option>
            <option value="1990">1990</option>
            <option value="1991">1991</option>
            <option value="1992">1992</option>
            <option value="1993">1993</option>
            <option value="1994">1994</option>
            <option value="1995">1995</option>
            <option value="1996">1996</option>
            <option value="1997">1997</option>
            <option value="1998">1998</option>
            <option value="1999">1999</option>
            <option value="2000">2000</option>
            <option value="2001">2001</option>
            <option value="2002">2002</option>
            <option value="2003">2003</option>
            <option value="2004">2004</option>
            <option value="2005">2005</option>
            <option value="2006">2006</option>
            <option value="2007">2007</option>
            <option value="2008">2008</option>
            <option value="2009">2009</option>
            <option value="2010">2010</option>
        </select>
    </div>
    <div id="divDay">
    </div>
    <div id="divTable">
        <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="80%" data-page-length='20'>
            <thead>
            <tr>
                <th>bc_ID</th>
                <th>birdcode</th>
                <th>bc_observedamount</th>
                <th>bc_distance</th>
                <th>bc_yearday</th>
                <th>bc_julianday</th>
                <th>milemarker</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>bc_ID</th>
                <th>birdcode</th>
                <th>bc_observedamount</th>
                <th>bc_distance</th>
                <th>bc_yearday</th>
                <th>bc_julianday</th>
                <th>milemarker</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
    <?php include '../../TonyAmos/Master/footer.php'; ?>
</body>
<script>
    function loadDatatableDay()
    {
        document.getElementById("divTable").style.display = "block";

        // Getting the primary key for the day
        var ddlDay = document.getElementById("ddlDays");
        var primaryKey = ddlDay.options[ddlDay.selectedIndex].value;
        primaryKey = parseInt(primaryKey);
        console.log(primaryKey);

        // Getting the year
        var selectTag = document.getElementById("ddlYears");
        var year = selectTag.options[selectTag.selectedIndex].value;
        year = parseInt(year);

        var table = $('#dtable').DataTable ({
            "processing": true,
            "serverside": true,
            "lengthMenu": [20, 40, 60, 80, 100],
            "destroy": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf'
            ],

            "language": {
                  "processing": "<div id='divLoad'><figure id='figLoad'>" +
                  "<img src='../../TonyAmos/Master/load.gif'><figcaption>Loading</figcaption></figure><div>"
            },

            "initComplete": function()
            {

            },

            ajax:
            {
                url: "./TonyQuery.php?getDay=" + primaryKey + "&year=" + year
            },

            columns: [
                {data: 'bc_ID'},
                {data: 'birdcode'},
                {data: 'bc_observedamount'},
                {data: 'bc_distance'},
                {data: 'bc_yearday'},
                {data: 'bc_julianday'},
                {data: 'milemarker'},
            ],
        });

        $('#dtable tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        } );

        // Apply the search
        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    }

    function ddlYearChanges()
    {
        // Getting the select tag and its value
        var selectTag = document.getElementById("ddlYears");
        var year = selectTag.options[selectTag.selectedIndex].value;
        year = parseInt(year);

        callGetDDLYear(year);
    }

    function callGetDDLYear()
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
    }

    $(document).ready(function ()
     {
        document.getElementById("divTable").style.display = "none";
     });
</script>
</html>
