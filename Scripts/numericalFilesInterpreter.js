var birdcodes;
var birdids;

function btnUploadNumericalFiles()
{
    // Initializing local variables and getting the Choose File button
    var btnUpload = document.getElementById("btnUploadNumericalFile");
    var txt = "";

    // If there are files
    if ('files' in btnUpload) {

        // If the lenght is zero, then select a different file
        if (btnUpload.files.length == 0) {
            txt = "Select one or more files.";
        }

        // Otherwise
        else {

            // Looping through the files
            for (var i = 0; i < btnUpload.files.length; i++) {

                // Counting the number of files
                txt += "<br><strong>" + (i+1) + ". file</strong><br>";

                var file = btnUpload.files[i];

                // Show file name
                if ('name' in file) {
                    txt += "name: " + file.name + "<br>";
                }

                // Show the size of th efile
                if ('size' in file) {
                    txt += "size: " + file.size + " bytes <br>";
                }

                // Process file
                processNumericalFiles(file);
            }
        }
    }

    // Otherwise
    else {
        if (btnUpload.value == "") {
            txt += "Select one or more files.";
        } else {
            txt += "The files property is not supported by your browser!";
            txt  += "<br>The path of the selected file: " + x.value; // If the browser does not support the files property, it will return the path of the selected file instead.
        }
    }
    document.getElementById("result").innerHTML = txt;
}

function processNumericalFiles(file)
{
    console.log(file.name);
    reader = new FileReader();
    var regexFindMileMarkers = /(240\d\d)/gm;
    var originalNumMileMarkers;

    reader.onload = function(e)
    {
        var originalFileText = reader.result;
        var mileMarkers = originalFileText.match(regexFindMileMarkers);
        var mileMarkersWithText = [];

        // Checking to see if there are mile markers
        if(mileMarkers != null && mileMarkers.length > 0)
        {
            originalNumMileMarkers = mileMarkers.length;

            // Separate mile marker text
            mileMarkersWithText = getMileMarkerText(originalFileText, mileMarkers);
            console.log(birdids);
        }

        else
        {
            console.log("No mile markers found.");
        }
    };

    // Read file as text
    reader.readAsText(file);
}

function getMileMarkerText(text, mileMarkers)
{
    var result = [];

    for(var i = 0; i < mileMarkers.length; i++)
    {
        if(i + 1 < mileMarkers.length)
        {
            result.push(text.slice(text.search(mileMarkers[i]), text.search(mileMarkers[i + 1])));
        }

        else
        {
            result.push(text.slice(text.search(mileMarkers[mileMarkers.length - 1]), text.search(/(239\d\d)/)));
        }
    }

    return result;
}

function getBirdCodesDB()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            birdcodes = JSON.parse(this.responseText);
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getBirdCodes=" + 2);
}

function getBirdIds()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            birdids = JSON.parse(this.responseText);
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getBirdIds=" + 1);
}

$(document).ready(function ()
{
    getBirdCodesDB();
    getBirdIds();
});
