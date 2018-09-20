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
    var regexFindMileMarkers = /(240[4-6]\d)/gm;
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
            console.log(mileMarkersWithText);
            recreateFile(mileMarkersWithText, mileMarkers, originalFileText, file);
        }

        else
        {
            console.log("No mile markers found.");
        }
    };

    // Read file as text
    reader.readAsText(file);
}

function recreateFile(numericalFile, numericalMileMarkers, originalFileText, file)
{
    var newfile = "";
    var bigRegex = /(\d{3}[4,5]\d\s*[4,5]\d\s)|(\d{3}[4,5]\d\s)|(\d{3})|(\d{2}\s[4,5]\d\s*)|(\d{2})/gm;
    var regexCodeDoubleObserved = /(\d{3}[4,5]\d\s*[4,5]\d\s)/gm; // Regex code when there is a three digit code with 10 or more observed amount
    var regexCodeObserved = /(\d{3}[4,5]\d\s)/gm; // Regex code when there is a three digit code with an observed amount between 0 and 9
    var regexCodeTripleDigit = /(\d{3})/gm; // Most standard, just a normal 3 digit code with no observed amount
    var regexCodeTwoDigitObserved = /(\d{2}\s[4,5]\d\s*)/gm; // Regex code when there is a two digit code with an observed amount from 0 to 9
    var regexCodeTwoDigit = /(\d{2})/gm; // Standard for 2 digit code with no observations

    // converting birdids to ints instead of strings
    for(var key in birdids)
    {
        birdids[key] = parseInt(birdids[key]);
    }


    if(numericalFile != null && numericalFile.length > 0)
    {
        for(var i = 0; i < numericalFile.length; i++)
        {
            // Replacing original text with whats extracted
            originalFileText = originalFileText.replace(numericalFile[i], "");

            if(numericalFile[i] === "")
            {
                // Do nothing
            }

            else
            {
                // Replacing new lines and mile markers in numericalText and getting mile marker into Tony's original format * #
                var numericalText = numericalFile[i];
                var stringMileMarker = parseInt(numericalText) - 24049;
                stringMileMarker = "* " + stringMileMarker;
                numericalText = numericalText.replace(numericalMileMarkers[i], "");
                numericalText = numericalText.replace(/\r?\n|\r/g, "");

                // Getting array of codes
                var arrayOfIds = numericalText.match(bigRegex);
                newfile += stringMileMarker;
                newfile += convertNumericalToString(arrayOfIds, regexCodeDoubleObserved, regexCodeObserved, regexCodeTripleDigit,
                    regexCodeTwoDigitObserved, regexCodeTwoDigit);
            }
        }
    }

    if(originalFileText !== "")
    {
        console.log("What's left: \n" + originalFileText);
    }

    console.log("New file: \n" + newfile);
    createNewFile(newfile, file.name);
}

function convertNumericalToString(arrayOfIds, regexCodeDoubleObserved, regexCodeObserved, regexCodeTripleDigit, regexCodeTwoDigitObserved, regexCodeTwoDigit)
{
    var newCodesAsStrings = "";
    var OBSERVED_BASE = 48;

    for(var i = 0; i < arrayOfIds.length; i++)
    {
        var numericalCode = arrayOfIds[i];

        if(numericalCode.match(regexCodeDoubleObserved))
        {
            var observedBirdCode = numericalCode.match(/\d{3}/); // Getting 3 digit code from numerical code
            numericalCode = numericalCode.replace(observedBirdCode, "");

            // Getting double digit numbers
            var twoDigitNumber = numericalCode.match(/\d{2}/gm);
            twoDigitNumber = twoDigitNumber.map(Number);
            twoDigitNumber[0] = parseInt(twoDigitNumber[0]) - OBSERVED_BASE;
            twoDigitNumber[1] = parseInt(twoDigitNumber[1]) - OBSERVED_BASE;
            twoDigitNumber = twoDigitNumber.join();
            twoDigitNumber = twoDigitNumber.replace(",", "");

            // Finding the birdcode
            var position = birdids.indexOf(parseInt(observedBirdCode));
            position = parseInt(position);
            observedBirdCode = birdcodes[position];

            // Adding spaces to the bird codes that have less than 4 spaces
            observedBirdCode = formatCodeWithSpaces(observedBirdCode);

            var fullCodeWithObs = "";
            fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
            newCodesAsStrings += fullCodeWithObs;
        }

        else if(numericalCode.match(regexCodeObserved))
        {
            newCodesAsStrings += translateThreeDigitCodeObs(numericalCode, OBSERVED_BASE);
        }

        else if(numericalCode.match(regexCodeTripleDigit))
        {
            newCodesAsStrings += translateThreeDigitCodeNoObs(numericalCode, OBSERVED_BASE);
        }

        else if(numericalCode.match(regexCodeTwoDigitObserved))
        {
            newCodesAsStrings += translateTwoDigitCodes(numericalCode, OBSERVED_BASE, true);
        }

        else if(numericalCode.match(regexCodeTwoDigit))
        {
            newCodesAsStrings += translateTwoDigitCodes(numericalCode, OBSERVED_BASE, false);
        }
    }

    return newCodesAsStrings;
}

// obs is a boolean flag, true means there is an observed amount, false mean there isn't one
function translateTwoDigitCodes(numericalCode, OBSERVED_BASE, obs)
{
    var resultString = "";
    var observedBirdCode;
    var amountObserved;
    var position;

    // Checking to see if there is an observation
    if(obs)
    {
        // Pulling the first two digit number
        observedBirdCode = numericalCode.match(/\d{2}/);

        // Getting the second two digit number
        numericalCode = numericalCode.replace(observedBirdCode, "");
        amountObserved = numericalCode.match(/\d{2}/);

        // Getting birdcode
        position = birdids.indexOf(parseInt(observedBirdCode));
        observedBirdCode = birdcodes[position];
        amountObserved = parseInt(amountObserved) - OBSERVED_BASE;
        observedBirdCode = formatCodeWithSpaces(observedBirdCode);

        resultString = observedBirdCode + amountObserved;
    }

    else
    {
        // Pulling the first two digit number
        observedBirdCode = numericalCode.match(/\d{2}/);
        position = birdids.indexOf(parseInt(observedBirdCode));
        observedBirdCode = birdcodes[position];
        observedBirdCode = formatCodeWithSpaces(observedBirdCode);
        resultString = observedBirdCode;
    }

    return resultString;
}

function translateThreeDigitCodeNoObs(numericalCode, OBSERVED_BASE)
{
    var resultString = "";

    var observedBirdCode = numericalCode.match(/\d{3}/);

    // Finding the birdcode
    var position = birdids.indexOf(parseInt(observedBirdCode));
    position = parseInt(position);
    observedBirdCode = birdcodes[position];

    observedBirdCode = formatCodeWithSpaces(observedBirdCode);

    resultString = observedBirdCode;

    console.log("resultstringnoobs: \n" + resultString);

    return resultString;
}

function translateThreeDigitCodeObs(numericalCode, OBSERVED_BASE)
{
    var resultString = "";

    // Getting 3 digit code and replacing original text with nothing
    var observedBirdCode = numericalCode.match(/\d{3}/);
    numericalCode = numericalCode.replace(observedBirdCode, "");

    // Finding the birdcode
    var position = birdids.indexOf(parseInt(observedBirdCode));
    position = parseInt(position);
    observedBirdCode = birdcodes[position];

    // Getting number observed
    var observedAmount = numericalCode.match(/\d{2}/);
    observedAmount = observedAmount.map(Number);
    observedAmount = parseInt(observedAmount) - OBSERVED_BASE;

    // This is probably a code, not an observation amount
    if(observedAmount < 0)
    {
        var additionalBirdCode = numericalCode.match(/\d{2}/);
        position = birdids.indexOf(parseInt(additionalBirdCode));
        position = parseInt(position);
        additionalBirdCode = birdcodes[position];
        additionalBirdCode = formatCodeWithSpaces(additionalBirdCode);
        observedBirdCode = formatCodeWithSpaces(observedBirdCode);
        resultString = observedBirdCode + additionalBirdCode;

        console.log("resultstringwithcorrection: \n" + resultString);
    }

    else
    {
        observedBirdCode = formatCodeWithSpaces(observedBirdCode);
        resultString = observedBirdCode + observedAmount;

        console.log("resultstring: \n" + resultString);
    }

    return resultString;
}

function formatCodeWithSpaces(observedBirdCode)
{
    // Adding spaces to the bird codes that have less than 4 spaces
    if(observedBirdCode.length === 2)
    {
        observedBirdCode = observedBirdCode + "0";
        observedBirdCode = observedBirdCode.replace("0", '  ');
    }

    else if(observedBirdCode.length === 3)
    {
        observedBirdCode = observedBirdCode + "0";
        observedBirdCode = observedBirdCode.replace("0", ' ');
    }

    return observedBirdCode;
}

function getMileMarkerText(text, mileMarkers)
{
    console.log("milemarkers\n" + mileMarkers);
    var result = [];

    for(var i = 0; i < mileMarkers.length; i++)
    {
        if(i + 1 < mileMarkers.length)
        {
            result.push(text.slice(text.search(mileMarkers[i]), text.search(mileMarkers[i + 1])));
        }

        else
        {
            result.push(text.slice(text.search(mileMarkers[mileMarkers.length - 1]), text.search(/(239\d\d)|(239\s*32)|(24032)/)));
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

function createNewFile(text, filename)
{
    var xhttp = new XMLHttpRequest();
    var dir = "C:/xampp/htdocs/TonyAmos/Data/OriginalData/BCHobs2/BIRDRAW/BDZ84";

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            if(this.responseText)
            {
                console.log("File has been created.");
            }

            else
            {
                console.log("File has failed to be created.");
            }
        }
    };

    xhttp.open("POST", "./WriteToFile.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("text=" + text + "&filetype=" + "txt" + "&path=" + dir + "&filename=" + filename);
}

$(document).ready(function ()
{
    getBirdCodesDB();
    getBirdIds();
});
