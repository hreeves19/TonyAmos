<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 10/10/2018
 * Time: 4:49 PM
 */
$originalFileLocation = "../../TonyAmos/Forms/TonyAmosCollection.zip";

if(file_exists($originalFileLocation) == false)
{
    // Get real path for our folder
    $rootPath = realpath("../../TonyAmos/CSV/BCHobs2");
    $archive_file_name = "TonyAmosCollection.zip";

// Initialize archive object
    $zip = new ZipArchive();
    $zip->open($archive_file_name, ZipArchive::CREATE);

// Create recursive directory iterator

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

// Zip archive will be created only after closing object
    $zip->close();

    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$archive_file_name");
    header("Content-length: " . filesize($archive_file_name));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile("$archive_file_name");

    /*echo $archive_file_name;*/
}

/*echo $originalFileLocation;*/
echo "<a href=\"$originalFileLocation\" download=\"TonyAmosCollection\"><button class=\"downloadButton\"><i class=\"fa fa-download\"></i> Download Collection</button></a>";