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

    <link rel="stylesheet" type="text/css" href="../../TonyAmos/Master/CSS/master.css">
    <script type="text/javascript" src="../../TonyAmos/Scripts/tonyAmosManager.js"></script>
    <script type="text/javascript" src="../../TonyAmos/Library/jQuery-2.2.3/jquery-2.2.3.js"></script>
    <script type="text/javascript" src="../../TonyAmos/Library/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../TonyAmos/Scripts/numericalFilesInterpreter.js"></script>
</head>
<!-- END HEADER -->
<!-- HTML BODY -->
<body>
<div id="wrap">
    <div id="main" class="main">
        <!-- Right div, this one has a scroll bar if the screen gets to high -->
        <h2 id="page_title">Tony Amos Collection</h2>
        <div id="content" style="margin: 10px;">
            <!-- Upload file Button -->
            <div>
                <p>Use the choose files to sort the files that you want. Pick a collection to name the directories and files properly:</p>
                <input type="file" id="btnUploadFile" multiple size="50" onchange="btnUploadFile()">
                Collection:
                <select id="ddlCollection">
                    <option value="-1">Unselected</option>
                    <option value="BCHobs1">BCHobs1</option>
                    <option value="BCHobs2">BCHobs2</option>
                    <option value="GARBGOBS">GARBGOBS</option>
                    <option value="SANJOobs">SANJOobs</option>
                </select>
            </div>

            <div>
                <p id="result"></p>
            </div>

            <div>
                <select id="ddlYears">
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
                <button type="button" onclick="getYearFileNames('1984')">Get File</button>
            </div>

            <div>
                <p>To populate days drop down list for deletion, select a year in the drop down list below.
                If there are no records for that year, nothing will show:</p>
                <select id="ddlYearDelete" onchange="getDDLDay()">
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

            <div id="divResponse">
            </div>

            <div>
                <p>To insert a single (sorted) file to the database, choose the file with the 'Choose Files' button below:</p>
                <select id="ddlYearsForInsertion" onchange="populateDDLForInsertion()">
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
                <!--<input type="file" id="btnUploadFileToInsert" multiple size="50" onchange="btnUploadFileToInsert()">-->
            </div>

            <div id="divDDLDaysInsert">
            </div>

            <div>
                <p>To check to see if any days are missing in the database, click this button below:</p>
                <button type="button" onclick="checkDBForMissedRecords()">Check Database</button>
            </div>

            <div>
                <p>Use this section for CSV generation:</p>
                Year: <select id="ddlYearCSV" onchange="getDDLDay()">
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
                <div id="divDDLDay">
                </div>
            </div>

            <div>
                <p id="response"></p>
            </div>

            <div>
                <p id="databaseResponse"></p>
            </div>

            <div>
                <p>Use this section for interpreting the numerical files:</p>
                <input type="file" id="btnUploadNumericalFile" multiple size="50" onchange="btnUploadNumericalFiles()">
            </div>
            <div>
                <p>Use this section to update the bc_date_object:</p>
                <button type="button" onclick="callToUpdateDateObject()">Update Table</button>
            </div>
        </div>
    </div>
<?php include '../../TonyAmos/Master/footer.php'; ?>
</body>
</html>
