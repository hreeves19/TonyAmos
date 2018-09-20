/**
 * Created by hreeves on 7/30/2018.
 */
// First file to test: C:\Users\hreeves\Desktop\Tony\Original Data Dump\BCHobs2\BIRDRAW\BDZ00
    // Directory i need to be in: C:\xampp2\htdocs\BandoCat\tonyamos\OriginalData\BCHobs2\BIRDRAW
var total = 0;
var totalFail = 0;
var objMileMarkers;
var objBirdCodes;
var totalCodesNotFound = 0;
var arrayOfObjects = [];
var start = 1985; // This is the incrementer for the automation loop
var end = 2001; // Flag to stop the loop
var automated = false;

//https://www.epochconverter.com/days/1993

function btnUploadFile()
{
    // Initializing local variables and getting the Choose File button
    var btnUpload = document.getElementById("btnUploadFile");
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
                processFile(file);
                /*processPopulationTextFile(file);*/
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
 * Function Name: Process File(s)
 *
 * Description:
 * This functions purpose is to extract the text from the files, and
 * separate the mile markers and their texts appropriately. There are 3
 * arrays that are generated.
 *
 * textByMileMarker     - string array - Contains the text with the appropriate
 *                                       mile marker
 * mileMarkerInt        - int array    - Contains the mile markers without the text
 *                                       example.) 1, 2, 3, 4....
 * arrayOfErrorMessage  - string array - Contains the error messages that will be added
 *                                       to ErrorLog.txt
 *
 * Parameters:
 * file - file object - Contains the file that is uploaded, if there are
 *                      more than one given, then it is one at a time
 *
 * Return:
 * Doesn't return anything, but goes to writeToFilePost function.
 ********************************************************************/
function processFile(file)
{
    // Creating reader object
    var reader = new FileReader();
    var mileMarkerText = "";
    var failed = false;
    var textByMileMarker = []; // String
    var mileMarkerInt = []; // Int
    var arrayOfErrorMessages = [];
    var originalNumMileMarkers = 0;
    var fileWithoutMarkers = false; // No mile markers but has key codes
    var duplicateMileMarkers = []; // An array of duplicate milemarkers


    // Regular expression to find the mile markers which are: * 0, * 1, * 2, ....
    var regex = /(\*\s*[1-9]\d)|(\*\s*\d)/gm;
    var regexNegativeMileMarkers = /(\*\s*-\d\d)|(\*\s*-\d)|(\*)/gm;

    reader.onload = function(e)
    {
        // Getting text and replacing new lines with nothing
        var originalText = reader.result;
        originalText = originalText.replace(/\r?\n|\r/g, '');

        var mileMarker = originalText.match(regex); // Example on return: * 0, * 1, * 2....
        /*var negativeMileMarkers = originalText.match(regexNegativeMileMarkers);*/

        // Checking to see if there are mile markers
        if(mileMarker != null && mileMarker.length > 0)
        {
            originalNumMileMarkers = mileMarker.length;
        }

        else
        {
            // Checking if there are any key codes, key codes can be 4, 3, or 2 characters long
            if(originalText.search(/[A-Z]{2,4}/) != -1)
            {
                // Pushing error message to log file, adding mile marker 0, and creating the mile marker array
                originalText = "* 0" + originalText;
                mileMarker = originalText.match(regex);
                fileWithoutMarkers = true;
            }

            else
            {
                arrayOfErrorMessages.push("File " + file.name + " has no key codes in it. This file will not be added.");
                console.log("File failed, contains no recognizable key codes or mile markers: " + file.name);
                totalFail++;
                return;
            }
        }

        // Checking for no mile marker at start
        originalText = checkForNoMileMarkerAtStart(mileMarker, originalText);

        // Delete all text before mile marker 0
        if(originalText.includes("* 0"))
        {
            originalText = originalText.slice(originalText.search(/\* 0/));
        }

        // Have to reinitialize mile markers because one may have been added
        mileMarker = originalText.match(regex);

        // Checking to see if there are mile markers found
        if(mileMarker != null && mileMarker.length > 0)
        {
            //  Getting text between mile markers, Tony did not always do all mile markers
            for(var i = 0; i < mileMarker.length; i++)
            {
                // Skipping undefined elements of the array
                if(typeof mileMarker[i] === 'undefined')
                {
                    console.log("Skip " + i);
                }

                else
                {
                    // Cutting current mile marker out of original text
                    originalText = originalText.replace(mileMarker[i], "");

                    // For mile markers that are not the last one
                    if(i + 1 < mileMarker.length)
                    {
                        // Getting text to next mile marker
                        mileMarkerText = originalText.slice(0, originalText.search("\\" + mileMarker[i + 1]));
                    }

                    else
                    {
                        mileMarkerText = originalText;
                    }

                    // Cutting out text retrieved from originalText
                    originalText = originalText.replace(mileMarkerText, "");

                    // Adding mile marker to text extracted
                    mileMarkerText = mileMarker[i] + mileMarkerText;

                    textByMileMarker.push(mileMarkerText);

                    // Getting the mile markers as integers
                    mileMarkerInt.push(parseInt(mileMarker[i].replace(/\D/g, '')));
                }
            }

            // Checking if the file has failed from the mile marker ordering
            if(failed != true)
            {
                total++; // Incrementing the total number of files that succeed
            }
        }

        // Checking to see if there are duplicate values
        // Replace the duplicate mile markers with a unique integer
        arrayOfErrorMessages = checkForDuplicateMileMarkers(mileMarkerInt, file, duplicateMileMarkers);

        // File has no mile markers, but has key codes
        if(fileWithoutMarkers === true)
        {
            arrayOfErrorMessages.push("File " + file.name + " does not contain any mile markers, but has potential key codes. Adding mile marker 0 to it.");
        }

        // Checking if mile marker was added at beginning
        if(originalNumMileMarkers < mileMarker.length && fileWithoutMarkers === false)
        {
            arrayOfErrorMessages.push("Mile marker " + mileMarker[0] + " was added to the original text.");
        }

        callWriteToFilePost(textByMileMarker, file.name, mileMarkerInt, arrayOfErrorMessages);
        /*console.log(textByMileMarker);*/
    };

    // Read file as text
    reader.readAsText(file);
}

/********************************************************************
 * Function Name: Call Write to File
 *
 * Description:
 * This function is used to call the php file WriteToFile. This is how
 * the text files are being added to the servers. Uses POST because
 * GET cannot hold all the information being passed to WriteToFile.php.
 *
 * Parameters:
 * arrayOfText - array of strings, this is the text of each mile marker separated
 *               as elements in the array
 * arrayOfMileMarkers - array of integers, this is the mile markers
 * filename - string, this is the name of the file
 * error - string, contains an error message (if there is one)
 *
 * Return:
 * Prints an error message to the console on the website.
 ********************************************************************/
function callWriteToFilePost(arrayOfText, filename, arrayOfMileMarkers, arrayOfErrorMessages)
{
    var xhttp = new XMLHttpRequest();

    // arrayOfText (array of strings containing the full text of each mile marker) needs to be in this format for the server
    arrayOfText = JSON.stringify(arrayOfText);
    arrayOfMileMarkers = JSON.stringify(arrayOfMileMarkers);
    arrayOfErrorMessages = JSON.stringify(arrayOfErrorMessages);

    if(parseInt(document.getElementById("ddlCollection").value) === -1)
    {
        console.log("Must select a collection");
    }

    else
    {
        xhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                console.log(this.response);
            }
        };

        // Have to use encodeURIComponent because it will delete plus signs in the text without it
        xhttp.open("POST", "./WriteToFile.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("arrayOfText=" + encodeURIComponent(arrayOfText) + "&filename=" + filename + "&arrayOfMileMarkers="
            + arrayOfMileMarkers + "&error=" + arrayOfErrorMessages + "&collection=" + document.getElementById("ddlCollection").value);
    }
}

/********************************************************************
 * Function Name: Check For Duplicate Mile Markers
 *
 * Description:
 * This function is used to detect duplicated mile markers.
 *
 * Parameters:
 * arrayOfMileMarkers - array of integers, this is the mile markers
 * file - file object, this is the current file
 * duplicateMileMarkers - array of duplicateMileMarkers, integers
 *
 * Return:
 * Prints an error message to the console on the website.
 ********************************************************************/
function checkForDuplicateMileMarkers(arrayOfMileMarkers, file, duplicateMileMarkers)
{
    // An array that contains the error messages for duplicate mile markers
    var result = [];

    // We need to check each element of the array to see if there is a duplicate value
    for(var i = 0; i < arrayOfMileMarkers.length; i++)
    {
        var check = arrayOfMileMarkers[i]; // Getting a value to check
        var pass = 0; // Our condition variable

        for(var index = 0; index < arrayOfMileMarkers.length; index++)
        {
            // Checking each element of the array with the number that is being checked
            if(check == arrayOfMileMarkers[index])
            {
                pass++; // Each element's pass will be at least 1, anything higher means the mile marker is duplicated

                // This catches the duplicate value
                if(pass > 1)
                {
                    // Checking to see if mile marker is already in the array
                    if(result.indexOf("File " + file.name + " has a duplicate mile marker. Mile marker " + arrayOfMileMarkers[index] + ".") == -1)
                    {
                        // If it isn't in the array, add it
                        result.push("File " + file.name + " has a duplicate mile marker. Mile marker " + arrayOfMileMarkers[index] + ".");
                        duplicateMileMarkers.push(arrayOfMileMarkers[i]); // Adding duplicate mile marker to array
                    }
                }
            }
        }
    }

    return result;
}

/********************************************************************
 * Function Name: Is Letter
 *
 * Description:
 * This function is used to check if the string passed to it has
 * a letter.
 *
 * Parameters:
 * str - a string, it is the text before the first mile marker,
 * not mile marker 0
 *
 * Return:
 * Prints an error message to the console on the website.
 ********************************************************************/
function isLetter(str)
{
    return str.match(/[A-Z]/g);
}

/********************************************************************
 * Function Name: Check for No Mile Marker at Start
 *
 * Description:
 * This function is used to check if there is a mile marker at the start and
 * checks if there is text before the first mile marker.
 *
 * Parameters:
 * mileMarker - An array of mile markers, string * 0, * 1, * 2, ...
 * originalText - a string containing the original text
 *
 * Return:
 * originalText - the change would be a mile marker added at the beginning
 * or cutting the text after mile marker 0
 ********************************************************************/
function checkForNoMileMarkerAtStart(mileMarker, originalText)
{
    // Checking to see if there were mile markers found and if the array is empty or null
    if(mileMarker != null && mileMarker.length > 0)
    {
        // Parsing the first mile marker as an integer
        var firstMileMarker = parseInt(mileMarker[0].replace(/\D/g, ''));

        // Finding the 0 mile marker
        switch(firstMileMarker)
        {
            // Mile marker 0
            case 0:
            {
                // Cut out all the text before the 0 mile marker
                originalText = originalText.slice(originalText.search(/\* 0/));
                break;
            }

            default:
            {
                var arrayOfLetters = isLetter(originalText.slice(0, originalText.search("\\" + mileMarker[0])));

                // Checking to see if there are letters before the first mile marker (which wouldn't be mile marker zero)
                if(arrayOfLetters != null && arrayOfLetters.length > 0)
                {
                    // Returning original text with added mile marker in the beginning
                    return "* " + (firstMileMarker - 1) + originalText;
                }
                break;
            }
        }
    }

    // Nothing changes from the original text
    return originalText;
}

/********************************************************************
 * Function Name: Process Bird Codes text file
 *
 * Description:
 * This function is used get the bird code, description, and number code
 * for each birdcode. It will then make a request to TonyQuery.php. This
 * populates the birdcode table.
 *
 * Parameters:
 * file - file object - The file is populateBirdCodes.txt, but the user
 * must upload this file
 *
 * Return:
 * callPopulateBirdCodesPost()
 ********************************************************************/
function processPopulationTextFile(file)
{
    // Creating reader object
    var reader = new FileReader();

    reader.onload = function(e)
    {
        // Getting the files text
        var originalText = reader.result;

        // Creating three arrays
        // ids - string array - bird code integer keys 001, 002, 003, ....
        // birdCode - string array - bird code characters SAND, LGUL, ....
        // birdCodeDescription - string array - bird code descriptions that Tony gives
        var ids = [];
        var birdCode = [];
        var birdCodeDescription = [];

        // Loop through each row and get the text before the most recent comma
        for(var i = 0; i < 367; i++)
        {
            // spliting by commas
            var temp = originalText.split(",")[i];
            temp = temp.replace("\n", ""); // deleting new line
            ids.push(temp.match(/[0-9]{3}/).toString()); // Get 3 digit code
            birdCode.push(temp.match(/[A-Z]{2,4}/).toString()); // Getting 4 character code

            // Replacing ids and birdCode from temp string, so all that is left are the descriptions
            temp = temp.replace(ids[i], "");
            temp = temp.replace(birdCode[i], "");
            temp = temp.slice(temp.search(/\S/)); // Getting rid of all whitespaces before the first non-whitespace character
            birdCodeDescription.push(temp); // Getting birdCodeDescription
        }

        // Calling ajax function
        callPopulateBirdCodesPost(ids, birdCode, birdCodeDescription);
    };

    // Read file as text
    reader.readAsText(file);
}

/********************************************************************
 * Function Name: Call Populate Bird Codes
 *
 * Description:
 * This function is used to call TonyQuery.php, which will populate the
 * bird codes table.
 *
 * Parameters:
 * ids                  - string array - bird code integer keys 001, 002, 003, ....
 * birdCode             - string array - bird code characters SAND, LGUL, ....
 * birdCodeDescription  - string array - bird code descriptions that Tony gives
 *
 * Return:
 * No return, but this will populate the birdcodes table with 367 records
 ********************************************************************/
function callPopulateBirdCodesPost(ids, birdCode, birdCodeDescription)
{
    var xhttp = new XMLHttpRequest();

    // arrayOfText (array of strings containing the full text of each mile marker) needs to be in this format for the server
    ids = JSON.stringify(ids);
    birdCode = JSON.stringify(birdCode);
    birdCodeDescription = JSON.stringify(birdCodeDescription);

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            console.log(this.response);
        }
    };

    // Have to use encodeURIComponent because it will delete plus signs in the text without it
    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("ids=" + encodeURIComponent(ids) + "&birdCode=" + encodeURIComponent(birdCode) + "&birdCodeDescription=" + encodeURIComponent(birdCodeDescription));
}

/********************************************************************
 * Function Name: Get Year Files
 *
 * Description:
 * This function is used to call TonyQuery.php, which will return all files
 * in whatever year is specified.
 *
 * Parameters:
 * tempYear - int - The year that is being chosen
 *
 * Return:
 * After returning, it will then call getBirdCodes.
 ********************************************************************/
function getYearFileNames(tempYear)
{
    var selectTag = document.getElementById("ddlYears");
    var year = selectTag.options[selectTag.selectedIndex].value;
    year = parseInt(year);
    /*var year = parseInt(tempYear);
    console.log("Year: " + year);*/


    if(year === -1)
    {
        return;
    }

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            objMileMarkers = JSON.parse(this.responseText);
            getBirdCodes();
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("year=" + year);
}

/********************************************************************
 * Function Name: Get Bird Codes
 *
 * Description:
 * This function is used to call TonyQuery.php, which will return all
 * the bird codes into an array.
 *
 * Parameters:
 * none
 *
 * Return:
 * Will fill the objBirdCodes array, then will call slidingWindow()
 ********************************************************************/
function getBirdCodes()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            objBirdCodes = JSON.parse(this.responseText);
            slidingWindow();

        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getBirdCodes=" + 1);
}

/********************************************************************
 * Function Name: Sliding Window
 *
 * Description:
 * This function is responsible for creating a list of objects that
 * hold the individual bird code, the amount seen, the filename, and
 * its distance. After finding this information, it calls LOOP().
 *
 * Parameters:
 * None
 *
 * Functions Called:
 * countBirdCodes() - called when the local array holding the regex for
 *                    either codes with distance or not is built. It will
 *                    then process this local array.
 *
 * LOOP()           - called after everything is completed. LOOP splits the
 *                    array in chunks of 1000 elements, then calls the server
 *                    to insert each code into the tables.
 * Return:
 * Does not return anything.
 *
 * Note:
 * slidingWindow() only uses regex.
 ********************************************************************/
function slidingWindow()
{
    // These are the regex expressions that are used
    var regexGetCodesNoDistance = /[A-Z]{2,4}\s{0,2}\d*/g;
    var regexGetCodeWithDistance = /\+\s*\d*([A-Z]{2,4}\s{0,2}\d*)*/g;
    var regexGetDistance = /\+\s*\d*/;
    var regexDoubleDigitMileMarker = /\d_M[0-9][0-9]\.txt/;
    var regexCutMileMarkers = /(\*\s*\d\d)|(\*\s*\d)/;

    // Local variables
    var totalNumberCodes = 0;
    var total = new Array(objBirdCodes.length);
    var arrayOfCodes; // These codes do not have a distance, example: MILKLGULRBG 2HGULHGULHGUL2, WILLDCC 3, ....
    var arrayOfDistances; // This array contains the distances and the codes that follow, example: +  440LGULRBG RBG 2, +  501HGUL, ...
    var withDistance = false; // Boolean flag

    // Creating an array that holds the total number of codes for each code
    for(var x = 0; x < total.length; x++)
    {
        total[x] = 0;
    }

    // Looping through mile marker object
    for(var index in objMileMarkers)
    {
        if(objMileMarkers.hasOwnProperty(index))
        {
            var mileMarkerText = objMileMarkers[index]; // Getting the mile marker text
            var filename = index; // Getting the filename
            var checkFileName = filename.match(regexDoubleDigitMileMarker); // Trying to find double digit mile markers to check

            // Checking to see if checkFileName has been defined
            if(checkFileName != null)
            {
                // Replacing everything, except digits, with a number
                console.log("Check file name : " + filename);
            }

            // Cutting mile marker out of the files text
            mileMarkerText = mileMarkerText.replace(regexCutMileMarkers, "");

            // Check if its a negative mile marker
            if(mileMarkerText.search(/\*\s*-\d*/) !== -1)
            {
                // Cut out negative mile markers and their text
                mileMarkerText = mileMarkerText.slice(0, mileMarkerText.search(/\*\s*-\d*/));
            }

            // Getting the codes as an array, replacing original text with spaces
            // If anything is left, something was missed
            if(mileMarkerText.search(regexGetCodeWithDistance) !== -1)
            {
                // Getting distances and their codes
                arrayOfDistances = mileMarkerText.match(regexGetCodeWithDistance);
                mileMarkerText = mileMarkerText.replace(regexGetCodeWithDistance, "");

                // For any codes that didn't have a distance, add them here
                arrayOfCodes = mileMarkerText.match(regexGetCodesNoDistance);
                mileMarkerText = mileMarkerText.replace(regexGetCodesNoDistance, "");
                withDistance = true; // Setting flag to true
            }

            // Checking if there are any codes found in the mileMarkerText
            // However, this does not check if these codes are bird codes, just grabs the
            // text and creates an array of codes that may be valid
            else if(mileMarkerText.search(regexGetCodesNoDistance) !== -1)
            {
                arrayOfCodes = mileMarkerText.match(regexGetCodesNoDistance);
                mileMarkerText = mileMarkerText.replace(regexGetCodesNoDistance, "");
                withDistance = false;// Setting flag to false
            }

            else
            {
                console.log("File " + filename + " has no bird codes.");
                continue;
            }

            // If there is something left, there was an issue with the file
            // Usually the issues are unrecognizable characters or Tony made a
            // mistake in terms of extra spaces or missing a space (remember some
            // codes have spaces in them, if he forgets the space then this causes
            // misreads)
            if(mileMarkerText !== "")
            {
                console.log(filename + "\n" + arrayOfCodes);
                console.log("What's left: " + mileMarkerText + "\t" + filename);
            }

            // Checking to see if the text has distances in them
            if(withDistance)
            {
                // Checking to see if the array is defined and has elements within it
                if(arrayOfCodes != null && arrayOfCodes.length > 0)
                {
                    totalNumberCodes += countBirdCodes(arrayOfCodes, total, filename, index, -1);
                }

                // Checking to see if the array is defined and has elements within it
                if(arrayOfDistances != null && arrayOfDistances.length > 0)
                {
                    // Looping through distances elements
                    for(var q = 0; q < arrayOfDistances.length; q++)
                    {
                        // See if there are codes within the distance
                        if(arrayOfDistances[q].search(regexGetCodesNoDistance) !== -1)
                        {
                            var arrayOfCodesWithDistance = arrayOfDistances[q].match(regexGetCodesNoDistance);
                            var distance = arrayOfDistances[q].match(regexGetDistance);
                            distance = distance.toString();
                            totalNumberCodes += countBirdCodes(arrayOfCodesWithDistance, total, filename, index, distance);

                        }
                    }
                }

                else
                {
                    console.log("File " + filename + " is empty.");
                }
            }

            else
            {
                // Checking to see if the array is null or empty
                if(arrayOfCodes != null && arrayOfCodes.length > 0)
                {
                    totalNumberCodes += countBirdCodes(arrayOfCodes, total, filename, index, -1);
                }

                else
                {
                    console.log("File " + filename + " is empty.");
                }
            }
        }
    }

    // Printing messages to the console
    console.log("Total number of observations: " + totalNumberCodes);
    console.log("Total codes found but were not valid: " + totalCodesNotFound);

    LOOP(); // Starting the loop
}

/********************************************************************
 * Function Name: Count Bird Codes
 *
 * Description:
 * This function is used to count the number of bird codes that are found.
 * It also pushes the code (along with the distance, amount observed, and
 * the file name) into a global array of objects. It only pushes codes that
 * are actual bird codes, if it isn't a bird code it is counted and skipped.
 *
 * Parameters:
 * arrayOfCodes - string array - Contains potential bird codes, example:
 * SAND5, LGUL, RBG 2, ....
 *
 * Return:
 * Returns the total number of codes to slidingWindow().
 * Total number of codes will count the number following the code,
 * example: SAND5 would be counted as 5.
 ********************************************************************/
function countBirdCodes(arrayOfCodes, total, filename, index, distance)
{
    var totalNumberCodes = 0;

    for(var i = 0; i < arrayOfCodes.length; i++)
    {
        var code = arrayOfCodes[i];
        var numberFound = code.replace(/\D*/, "");
        numberFound = parseInt(numberFound);
        code = code.replace(/\d*\s*/g, "");

        // Replacing numbers and spaces with nothing, so that it's just the code
        var position = objBirdCodes.indexOf(arrayOfCodes[i].replace(/\d*\s*/g, ""));

        // If the code was found in the bird codes array
        if(position > -1)
        {
            // Checking if number is real
            if (!isNaN(numberFound))
            {
                total[position] += numberFound;
            }

            // Code didn't have a number, meaning that there was only one found
            else
            {
                total[position]++;
                numberFound = 1;
            }

            // Pushing information into an object
            arrayOfObjects.push({birdcode: code, amount: numberFound, filename: filename, distance: distance});
        }

        else
        {
            /*console.log("Can't find: " + arrayOfCodes[i].replace(/\d*\s*!/g, ""));
            console.log("File: " + filename + "\n" + arrayOfCodes + "\n" + objMileMarkers[index]);*/
            totalCodesNotFound++;
            continue;
        }

        // Checking if number is real
        if(!isNaN(numberFound))
        {
            totalNumberCodes += numberFound;
        }
        else
        {
            totalNumberCodes++;
        }
    }

    return totalNumberCodes;
}

/********************************************************************
 * Function Name: Insert Bird Codes
 *
 * Description:
 * This function is used to call TonyQuery.php, which will be sent
 * an array of 1000 objects (the last one sent will be less than this,
 * if there are 13456 amount of total objects, the last array will be
 * 456 elements). It will then insert the elements into tables bc_observations
 * and bc_date. It will return any errors or that a new record has been
 * created.
 *
 * Parameters:
 * birdObject - an array, object of bird codes - If there were 76,000
 *              arrayOfObjects, then there would be 76 elements. However, doing
 *              birdObject[0] would show 1000 objects, which comes from arrayOfObjects.
 *
 * index      - int - Keeps track of the index in the array of bird objects
 *
 * firstTime  - boolean flag - This is for the birdObject[0], this flag
 *            is checked in TonyQuery.php.
 *
 * Return:
 * Returns once the server has given it a response, than it will call itself
 * until all of the birdObject elements have been sent to TonyQuery.php. After
 * finishing for the last time, it will call getFileNames() to get the next year.
 * Will end if there are no more years.
 ********************************************************************/
function insertBirdCodes(birdObject, index, firstTime)
{
    if(index < birdObject.length)
    {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                console.log(this.responseText);
                console.log("Index: " + index);
                console.log(birdObject[index]);
                index++;
                insertBirdCodes(birdObject, index, false);
            }
        };

        xhttp.open("POST", "./TonyQuery.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("insertObserved=" + JSON.stringify(birdObject[index]) + "&firstTime=" + firstTime);
    }

    else
    {
        if(automated)
        {
            start++;

            if(start != end)
            {
                arrayOfObjects.length = 0;
                getYearFileNames(start);
            }

            else
            {
                console.log("Done.");
            }
        }
    }
}

/********************************************************************
 * Function Name: Start Loop
 *
 * Description:
 * This function is used to start the loop to insert each element of
 * arrayOfObjects. Also, it breaks arrayOfObjects into chunks, then stores
 * it into result.
 *
 * Parameters:
 * none
 *
 * Return:
 * Does not return
 ********************************************************************/
function LOOP()
{
    var array = [];
    console.log("Length of entire object: " + arrayOfObjects.length);

    // Copying arrayOfObjects into array
    for(var i = 0; i < arrayOfObjects.length; i++)
    {
        array.push(arrayOfObjects[i]);
    }

    var result = chunkArray(array, 1000);

    insertBirdCodes(result, 0, true);
}

/********************************************************************
 * Function Name: Chunk Array
 *
 * Description:
 * This function is used to separate the array in chunks of 1000 elements.
 * For example, if there were 76,000 elements, the new array would have
 * 76 elements. Doing tempArray[index] would show you a 1000 objects.
 *
 * Parameters:
 * none
 *
 * Return:
 * Returns an array that has been separated in chunks.
 ********************************************************************/
function chunkArray(myArray, chunk_size)
{
    var index = 0;
    var arrayLength = myArray.length;
    var tempArray = [];

    for (index = 0; index < arrayLength; index += chunk_size)
    {
        myChunk = myArray.slice(index, index+chunk_size);

        tempArray.push(myChunk);
    }

    return tempArray;
}

// Calling server to populate a drop down list that contains the day of the year selected
function getDDLDay()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            if(parseInt(document.getElementById("ddlYearCSV").value) !== -1)
            {
                document.getElementById("divDDLDay").innerHTML = this.responseText;
            }

            else
            {
                document.getElementById("divResponse").innerHTML = this.responseText;
            }
        }
    };

    if(parseInt(document.getElementById("ddlYearCSV").value) !== -1)
    {
        xhttp.open("POST", "./TonyQuery.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("ddlYearValue=" + document.getElementById("ddlYearCSV").value + "&notdelete=" + 1);
    }

    else
    {
        xhttp.open("POST", "./TonyQuery.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("ddlYearValue=" + document.getElementById("ddlYearDelete").value);
    }
}

// Calling TonyQuery.php to delete day from bc_observations and bc_date
function deleteFromDB()
{
    document.getElementById("databaseResponse").innerHTML = "";
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            document.getElementById("databaseResponse").innerHTML = this.responseText;
            getDDLDay();
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("deleteRecordDay=" + document.getElementById("ddlDays").value);
}

function populateDDLForInsertion()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            document.getElementById("divDDLDaysInsert").innerHTML = this.responseText;
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("getFilesFromYear=" + document.getElementById("ddlYearsForInsertion").value);
}

// Calling TonyQuery.php to insert day to bc_observations and bc_date
function insertOneDayToDB()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            /*document.getElementById("databaseResponse").innerHTML = this.responseText;*/
            objMileMarkers = JSON.parse(this.responseText);
            getBirdCodes();
            automated = false;
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("insertRecordDay=" + document.getElementById("ddlDaysToInsert").value + "&year=" + document.getElementById("ddlYearsForInsertion").value);
}

// Calling server to check if any days are missing
function checkDBForMissedRecords()
{
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            console.log(this.responseText);
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("checkDBForMissingRecords=1");
}

// Call to server for CSV generation, this is going to WriteToFile.php
function generateCSV()
{
    getDataForCSVGeneration();
}

function getDataForCSVGeneration()
{
    console.log("Generating CSV for year " + start + "....");
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var arrayOfPk = JSON.parse(this.responseText);
            var chunksOfPk = chunkArray(arrayOfPk, 25); // Splitting the array of primary keys into chunks

            callCSVGeneration(chunksOfPk, 0, true);
        }
    };

    xhttp.open("POST", "./TonyQuery.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /*xhttp.send("GET_PRIMARYKEYS_DAYS=" + true + "&yearCSV=" + document.getElementById("ddlYearCSV").value);*/
    xhttp.send("GET_PRIMARYKEYS_DAYS=" + true + "&yearCSV=" + start);
}

function callCSVGeneration(arrayOfPrimaryKeys, index, firstTime)
{
    console.log("Sending request " + index + "....");
    if(index < arrayOfPrimaryKeys.length)
    {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                console.log("Sent-\n" + arrayOfPrimaryKeys[index]);
                console.log("Response-\n" + this.responseText);
                index++;
                callCSVGeneration(arrayOfPrimaryKeys, index, false);
            }
        };

        xhttp.open("POST", "./WriteToFile.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        /*xhttp.send("arrayOfPrimaryKeys=" + JSON.stringify(arrayOfPrimaryKeys[index]) + "&year=" + document.getElementById("ddlYearCSV").value);*/
        xhttp.send("arrayOfPrimaryKeys=" + JSON.stringify(arrayOfPrimaryKeys[index]) + "&year=" + start);
    }

    else
    {
        // Still more years to go
        // This part is for automation from start to end (variables are at the top)
        if(start < end)
        {
            start++;
            getDataForCSVGeneration();
        }

        else
        {
            console.log("Completed all years.");
        }
    }
}