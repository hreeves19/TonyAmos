// Global arrays, these contain the bird codes and the ids
var birdcodes;
var birdids;

/********************************************************************
 * Function Name: Button Upload Numerical Files
 *
 * Description:
 * This function is used to upload only the numerical files that are found
 * in BDZ1984. These files were all numeric and they need to be translated
 * to Tony's file formatting.
 *
 * Parameters:
 * None
 *
 * Return:
 * None
 ********************************************************************/
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

/********************************************************************
 * Function Name: Process Numerical Files
 *
 * Description:
 * This function is used to process the files that are uploaded from
 * btnUploadNumericalFiles function. All this really does is read the
 * file then sends it to recreateFile function.
 *
 * Parameters:
 * file - file object - The file is one of the numerical file found
 * in BDZ1984, but the user must upload this file.
 *
 * Return:
 * None
 ********************************************************************/
function processNumericalFiles(file)
{
    // Creating local variables
    console.log(file.name);
    reader = new FileReader();
    var regexFindMileMarkers = /(240[4-6]\d)/gm; // Regex that gets all the mile markers
    var originalNumMileMarkers;

    // This allows us to read the contents of the folder uploaded once reader.readAsText(file) executes
    reader.onload = function(e)
    {
        // Storing the original file text into a variable
        var originalFileText = reader.result;
        var mileMarkers = originalFileText.match(regexFindMileMarkers); // Creating an array of milemarkers using match
        var mileMarkersWithText = [];

        // Checking to see if there are mile markers
        if(mileMarkers != null && mileMarkers.length > 0)
        {
            originalNumMileMarkers = mileMarkers.length;

            // Separate mile marker text
            mileMarkersWithText = getMileMarkerText(originalFileText, mileMarkers);
            recreateFile(mileMarkersWithText, mileMarkers, originalFileText, file); // Recreate the file
        }

        else
        {
            console.log("No mile markers found.");
        }
    };

    // Read file as text
    reader.readAsText(file);
}

/********************************************************************
 * Function Name: Process Bird Codes text file
 *
 * Description:
 * This function is used to recreate the file into Tony's original format.
 * Basically, it loops through each mile marker and calls a function
 * to convert the numerical string to Tony's formatting.
 *
 * Parameters:
 * numericalFile - array of strings - elements contain the mile markers with their respective codes
 *
 * numericalMileMarkers - array of strings - only contains the mile markers
 *
 * originalFileText - string - the original file text that was read from the file
 *
 * file - file object - The file is one of the numerical file found
 * in BDZ1984, but the user must upload this file.
 *
 * Return:
 * None
 ********************************************************************/
function recreateFile(numericalFile, numericalMileMarkers, originalFileText, file)
{
    // Big regex has all of the other ones but is combined into one
    // If you need to change the regex for any of them make sure you update
    // big regex or you will see some weird results
    var newfile = "";
    var bigRegex = /(\d{3}[4,5]\d\s*[4,5]\d\s)|(\d{3}[4,5]\d\s)|(\d{3})|(\d{2}\s+[4,5]\d\s+[4,5]\d\s)|(\d{2}\s[4,5]\d\s*)|(\d{2})|(\d{1}\s+[4,5]\d\s+[4,5]\d)|(\d{1}\s+[4,5]\d)|([1-9])/gm;
    //var bigRegex = /(\d{3}[4,5]\d\s*[4,5]\d\s)|(\d{3}[4,5]\d\s)|(\d{3})|(\d{2}\s[4,5]\d\s*)|(\d{2})/gm;
    //(\d{2}\s*[4,5]\d)
    /*var regexCodeDoubleObserved = /(\d{3}[4,5]\d\s*[4,5]\d\s)/gm;   */// Regex code when there is a three digit code with 10 or more observed amount
    /*var regexCodeObserved = /(\d{3}[4,5]\d\s)/gm;                   */// Regex code when there is a three digit code with an observed amount between 0 and 9
    /*var regexCodeTripleDigit = /(\d{3})/gm;                         */// Most standard, just a normal 3 digit code with no observed amount
    /*var regexCodeTwoDigitObserved = /(\d{2}\s[4,5]\d\s*)/gm;        */// Regex code when there is a two digit code with an observed amount from 0 to 9
    /*var regexCodeTwoDigit = /(\d{2})/gm;                            */// Standard for 2 digit code with no observations


    console.log(numericalFile);

    // converting birdids to ints instead of strings
    for(var key in birdids)
    {
        birdids[key] = parseInt(birdids[key]);
    }

    // Checking to see array exists and contains information
    if(numericalFile != null && numericalFile.length > 0)
    {
        for(var i = 0; i < numericalFile.length; i++)
        {
            // Replacing original text with whats extracted
            originalFileText = originalFileText.replace(numericalFile[i], "");

            // Empty
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

                // Getting array of codes and translating the string to Tony's format, then save it.
                // Eventually, newfile will contain all mile markers and bird codes in Tony's format. Its
                // what the original file should have looked like. This is as accurate as we are going to get.
                var arrayOfIds = numericalText.match(bigRegex);
                newfile += stringMileMarker;
                newfile += getTranslation(arrayOfIds, bigRegex);
                /*newfile += convertNumericalToString(arrayOfIds, regexCodeDoubleObserved, regexCodeObserved, regexCodeTripleDigit,
                    regexCodeTwoDigitObserved, regexCodeTwoDigit);*/
            }
        }
    }

    if(originalFileText !== "")
    {
        console.log("What's left: \n" + originalFileText);
    }

    console.log("New file: \n" + newfile);
    createNewFile(newfile, file.name); //  Go to create a new file and save it on the server
}

function getTranslation(arrayOfIds, bigRegex)
{
    var regexThreeDigitCode = /(\d{3}[4,5]\d\s*[4,5]\d\s)|(\d{3}[4,5]\d\s)|(\d{3})/gm;
    var regexTwoDigitCode = /(\d{2}\s+[4,5]\d\s+[4,5]\d\s)|(\d{2}\s[4,5]\d\s*)|(\d{2})/gm;
    var regexOneDigitCode = /(\d\s+[4,5]\d\s+[4,5]\d)|(\d\s+[4,5]\d)|(\d)/gm;
    var resultString = "";
    var OBSERVED_BASE = 48;

    for(var i = 0; i < arrayOfIds.length; i++)
    {
        var numericalCode = arrayOfIds[i]; // Storing element in different variable to save integrity

        // Three digit codes
        if(numericalCode.match(regexThreeDigitCode))
        {
            console.log("regexThreeDigitCode:\n" + numericalCode);
            resultString += translateThreeDigitCode(numericalCode, OBSERVED_BASE);
        }

        // Two digit codes
        else if(numericalCode.match(regexTwoDigitCode))
        {
            console.log("regexTwoDigitCode:\n" + numericalCode);
            resultString += translateTwoDigitCode(numericalCode, OBSERVED_BASE);
        }

        // One digit codes
        else if(numericalCode.match(regexOneDigitCode))
        {
            console.log("regexOneDigitCode:\n" + numericalCode);
            resultString += translateOneDigitCode(numericalCode, OBSERVED_BASE);
        }

        else
        {
            console.log("Found but not identified:\n" + numericalCode);
        }
    }

    return resultString;
}

function translateThreeDigitCode(numericalCode, OBSERVED_BASE)
{
    var newCodeAsString = "";
    var fullCodeWithObs = "";
    var twoDigitNumber;
    var position;
    var observedBirdCode;

    // Getting birdcode
    observedBirdCode = numericalCode.match(/\d{3}/);

    if(numericalCode.match(/(\d{3}[4,5]\d\s*[4,5]\d\s)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting double digit numbers
        twoDigitNumber = getObservedAmountTwoDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    else if(numericalCode.match(/(\d{3}[4,5]\d\s)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting the amount observed
        twoDigitNumber = getObservedAmountOneDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    else if(numericalCode.match(/(\d{3})/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        newCodeAsString += observedBirdCode;
    }

    else
    {
        console.log("Not sure what this is.");
    }

    return newCodeAsString;
}

function translateTwoDigitCode(numericalCode, OBSERVED_BASE)
{
    var newCodeAsString = "";
    var fullCodeWithObs = "";
    var twoDigitNumber;
    var position;
    var observedBirdCode;

    // Getting birdcode
    observedBirdCode = numericalCode.match(/\d{2}/);

    // A two digit code with observed amount greater than 9
    if(numericalCode.match(/(\d{2}\s+[4,5]\d\s+[4,5]\d\s)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting double digit numbers
        twoDigitNumber = getObservedAmountTwoDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    // A two digit code with observed amount from 0 - 9
    else if(numericalCode.match(/(\d{2}\s[4,5]\d\s*)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting the amount observed
        twoDigitNumber = getObservedAmountOneDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    // A two digit code with no observed amount
    else if(numericalCode.match(/(\d{2})/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        newCodeAsString += observedBirdCode;
    }

    else
    {
        console.log("Not really sure what this two digit code is.");
    }

    return newCodeAsString;
}

function translateOneDigitCode(numericalCode, OBSERVED_BASE)
{
    // (\d\s+[4,5]\d\s+[4,5]\d)|(\d\s+[4,5]\d)|(\d)
    var newCodeAsString = "";
    var fullCodeWithObs = "";
    var twoDigitNumber;
    var position;
    var observedBirdCode;

    // Getting birdcode
    observedBirdCode = numericalCode.match(/\d{1}/);

    // Single digit codes with an observed amount greater than 9
    if(numericalCode.match(/(\d\s+[4,5]\d\s+[4,5]\d)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting double digit numbers
        twoDigitNumber = getObservedAmountTwoDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    // Single digit codes with an observed amount from 0 - 9
    else if(numericalCode.match(/(\d\s+[4,5]\d)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        // Getting the amount observed
        twoDigitNumber = getObservedAmountOneDigit(OBSERVED_BASE, numericalCode);

        // Finding the birdcode
        fullCodeWithObs = observedBirdCode.toString() + twoDigitNumber.toString();
        newCodeAsString += fullCodeWithObs;
    }

    // Single digit code with no specified observed amount, assume Tony meant 1
    else if(numericalCode.match(/(\d)/))
    {
        // Getting birdcode
        numericalCode = numericalCode.replace(observedBirdCode, "");
        observedBirdCode = findBirdCode(observedBirdCode);

        newCodeAsString += observedBirdCode;
    }

    else
    {
        console.log("Not really sure what this is, its supposed to be a single digit code.");
    }

    console.log("Here it is: \n" + newCodeAsString);

    return newCodeAsString;
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

function validNumber(number)
{
    if(number < 0)
    {
        number = 0;
    }

    return number;
}

function findBirdCode(observedBirdCode)
{
    var position;

    // Finding the birdcode
    position = birdids.indexOf(parseInt(observedBirdCode));
    position = parseInt(position);
    observedBirdCode = birdcodes[position];

    // Adding spaces to the bird codes that have less than 4 spaces
    observedBirdCode = formatCodeWithSpaces(observedBirdCode);

    return observedBirdCode;
}

function getObservedAmountTwoDigit(OBSERVED_BASE, numericalCode)
{
    var twoDigitNumber;

    // Getting double digit numbers
    twoDigitNumber = numericalCode.match(/\d{2}/gm);
    twoDigitNumber = twoDigitNumber.map(Number);
    twoDigitNumber[0] = parseInt(twoDigitNumber[0]) - OBSERVED_BASE;
    twoDigitNumber[1] = parseInt(twoDigitNumber[1]) - OBSERVED_BASE;
    twoDigitNumber = twoDigitNumber.join();
    twoDigitNumber = twoDigitNumber.replace(",", "");

    // Making sure its a valid integer
    twoDigitNumber = parseInt(twoDigitNumber);
    twoDigitNumber = validNumber(twoDigitNumber);

    return twoDigitNumber;
}

function getObservedAmountOneDigit(OBSERVED_BASE, numericalCode)
{
    var twoDigitNumber;

    // Getting the amount observed
    twoDigitNumber = numericalCode.match(/\d{2}/);
    twoDigitNumber = parseInt(twoDigitNumber);
    twoDigitNumber = twoDigitNumber - OBSERVED_BASE;
    twoDigitNumber = validNumber(twoDigitNumber);

    return twoDigitNumber;
}

$(document).ready(function ()
{
    getBirdCodesDB();
    getBirdIds();
});
