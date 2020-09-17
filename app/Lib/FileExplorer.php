<?php

namespace App\Lib;

/**
 * @deprecated - The class is depreciated and will need to be heavily refactored
 * This class will be used for file management
 * i.e. Create, delete, or modify file and directories
 * it will also provide information about a particular file or directory
 * It has only shared functions
 *
 */
class FileExplorer
{
    /*
    * Shared functions
    */
    public $rootFolder = '';

// Private variable
    private $g_DirStack = array();

// Default constructor
    function __construct()
    {
        $this->rootFolder = getcwd();
    }

// Gets the current directory
    function getRootPath()
    {
        list($scriptPath) = get_included_files();

        return $scriptPath;
    }

// Function to get the file handler
    function getFileHandler($name_or_path)
    {
        return fopen($name_or_path, "w + r") or die("Failed to open file");
    }

// Will be used for folder browsing

    function deleteFolder($name_or_path)
    {
        return $this->deleteDirectory($name_or_path);
    }

    function deleteDirectory($dirname)
    {
// Check if the path supplied is the current directory
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);

        if (!$dir_handle)
            return false;

        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    delete_directory($dirname . '/' . $file);
            }
        }

        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

// Delete a folder

    function createFolder($name_or_path)
    {
        return $this->createDirectory($name_or_path);
    }

// Alias to deleteDirectory

    function createDirectory($name_or_path)
    {
        $temp = explode("/", $name_or_path);

        if (is_array($temp))
            return $this->createDirectoryRecursively($name_or_path);

        return mkdir($name_or_path);
    }

// Create a folder

    private function createDirectoryRecursively($name_or_path)
    {
        $relativePath = '';

        $name_or_path = explode("/", $name_or_path);

        foreach ($name_or_path as $key => $path) {
            if ($path == '..')
                $relativePath .= "../";
            elseif ($path == '')
                break;
            else {
                $relativePath .= $path . "/";

                mkdir($relativePath);
            }
        }

        return opendir($relativePath);
    }

    /*
    * Used to create a directory recursively
    */

    function createFile($name_or_path)
    {
        $myfile = fopen($name_or_path, "w");

        if (!$myfile) {
            return false;
        }

        fclose($myfile);

        return true;
    }

// Alias to creating directory recursively

    function appendFile($name_or_path, $data)
    {
// Open the file for writing
        $file = fopen($name_or_path, "a");

// Failed to open file for append
        if (!$file)
            return false;

// Write to file, if empty then new file will be created
        fwrite($file, $data);

// Close connection to file
        fclose($file);

        return true;
    }

// Create a file

    function writeFile($name_or_path, $data)
    {
// Open the file for writing
        if(!$this->fileExists($name_or_path)){
            $this->createFile($name_or_path);
        }

        $file = fopen($name_or_path, "w");

        if (!$file)
            return false;

// Write to file, if empty then new file will be created
        fwrite($file, $data);

// Close connection to file
        fclose($file);
    }


    function writeToFileLBL($name_or_path, $data){
        if(!is_array($data)){
            return $this->writeFile($name_or_path, $data);
        }

        // Clear previous content
        $this->writeFile($name_or_path, '');

        foreach ($data as $line) {
            $result = $this->appendFile($name_or_path, $line);

            if(!$result){
                return $result;
            }
        }

        return true;
    }

    function appendToFileLBL($name_or_path, $data) {
        if(!is_array($data)){
            return $this->appendFile($name_or_path, $data);
        }

        foreach ($data as $line) {
            $result = $this->appendFile($name_or_path, $line);

            if(!$result){
                return $result;
            }
        }

        return true;
    }

// Append into a file

    function readFileLBL($name_or_path)
    {
        $myArray = array();

        if (!$this->fileExists($name_or_path))
            return $myArray;

        $i = 0;

        $fh = fopen($name_or_path, 'r');

        while ($line = fgets($fh)) {
            $myArray[$i] = $line;

            $i++;
        }

        fclose($fh);

        return $myArray;
    }

// Write a file

    function readFile($name_or_path)
    {
        $file = '';

        if (!$this->fileExists($name_or_path))
            return false;

        // Open the file for writing
        $files = fopen($name_or_path, "r");

        // Write to file, if empty then new file will be created
        $file = file_get_contents($name_or_path);

        // Close connection to file
        if (isset($file))
            fclose($files);

        return $file;
    }

// Reads the file line by line and stores the lines in an array
// then returns it
    function deleteFile($name_or_path)
    {
        if (!is_file($name_or_path))
            return false;

        unlink($name_or_path);
    }

// Read the contents of the file

    function moveFolder($current_location, $new_location)
    {
        return $this->moveDirectory($current_location, $new_location);
    }

// Deletes a file

    function moveDirectory($current_location, $new_location)
    {
        if ($current_location == $new_location)
            return true;

        if (is_file($current_location))
            return false;

        return rename($current_location, $new_location);
    }

// Moves a directory to a new location

    function moveFile($current_location, $new_location)
    {
        if ($current_location == $new_location)
            return true;

        if (!is_file($current_location))
            return false;

// Get the file name of the file
        $fn = $this->getDetails($current_location, 'basename');

        if (is_dir($new_location))
            $new_location .= "/" . $fn;


        return rename($current_location, $new_location);
    }

    function getDetails($name_or_path, $extraParam = '')
    {
    // Convert extraparam to lowercase
        $extraparam = strtolower($extraParam);

        if (!is_dir($name_or_path) and !is_file($name_or_path))
            die("Not a valid file or folder");

        $info = pathinfo($name_or_path);

        $myArray = array();

        $myArray[0] = $info['dirname'];
        $myArray[1] = $info['basename'];

        if (!isset($info['extension']))
            $myArray[2] = '';
        else
            $myArray[2] = $info['extension'];

        $myArray[3] = $info['filename'];
        $myArray[4] = getcwd();
        $myArray[5] = $this->getFileSize($name_or_path);
        $myArray[6] = filectime($name_or_path);
        $myArray[7] = filemtime($name_or_path);
        $myArray[8] = $this->getFileType($name_or_path);

// Return a specific detail
        if ($extraParam == 'dirname')
            return $myArray[0];
        elseif ($extraParam == 'basename')
            return $myArray[1];
        elseif ($extraParam == 'extension')
            return $myArray[2];
        elseif ($extraParam == 'filename')
            return $myArray[3];
        elseif ($extraParam == 'fullpath')
            return $myArray[4];
        elseif ($extraParam == 'size')
            return $myArray[5];
        elseif ($extraParam == 'datecreated')
            return $myArray[6];
        elseif ($extraParam == 'datemodified')
            return $myArray[7];
        elseif ($extraParam == 'type')
            return $myArray[8];
        else
            return $myArray;
    }

// Get file MIME_FILE_TYPE

    function getFileSize($name_or_path)
    {
        return filesize($name_or_path);
    }

// Gets the file details and returns it as an array, if the second argument
// is specified, then it retuns a specific detail about the file

    function getFileType($name_or_path)
    {
        $ext = strtolower(pathinfo($name_or_path)['extension']);

        switch ($ext) {
            case 'css':
                return 'text/css';
                break;

            case 'js':
                return 'application/javascript';
                break;

            case 'json':
                return 'application/json';
                break;

            case 'bat':
                return 'application/bat';
                break;

            default:
                return mime_content_type($name_or_path);
                break;
        }
    }

// Moves a file from one location to another

    function getBaseName($name_or_path)
    {
        return basename($name_or_path);
    }

// Gets the base basename of a folder

    function renameFolder($current_name, $new_name)
    {
        return $this->renameDirectory($current_name, $new_name);
    }


// Renames a folder/direcory

    function renameDirectory($current_name, $new_name)
    {
        if ($current_name == $new_name)
            return true;

        if (!is_dir($current_name)) {
            return false;
        }

        return rename($current_name, $new_name);
    }

// Alias to renameDirectory

    function renameFile($current_name, $new_name)
    {
        if ($current_name == $new_name)
            return true;

        if (!is_file($current_name)) {
            return false;
        }

        return rename($current_name, $new_name);
    }

// Renames a files

    function readDirectoryContents($name_or_path, $ascending = false)
    {
        if ($ascending)
            $files = scandir($name_or_path);
        else
            $files = scandir($name_or_path, 0);

        return $files;
    }

// Reads all items in the file and returns them

    function folderExists($name_or_path)
    {
        return $this->directoryExists($name_or_path);
    }

// Checks is if a file exists

    function directoryExists($name_or_path)
    {
        return is_dir($name_or_path);
    }

// Check if a folder exists

    function copyFile($current_location, $new_location)
    {
        if ($current_location == $new_location) {
            if ($this->fileExists($current_location))
                $new_location .= 'copy';

            $new_location .= ' copy';
        }

        if (!is_file($current_location))
            return false;

        return copy($current_location, $new_location);
    }

    function fileExists($name_or_path)
    {
        return is_file($name_or_path);
    }

// Gets the size of the file in bytes

    function copyFolder($current_location, $new_location)
    {
        if (!is_dir($current_location)) {
            return false;
        }

        return $this->copyDirectory($current_location, $new_location);
    }

// Copy files

    function copyDirectory($current_location, $new_location)
    {
        if ($current_location == $new_location) {
            if ($this->fileExists())
                $new_location .= 'copy';

            $new_location .= 'copy';
        }

        if (!is_dir($current_location))
            return false;

        return copy($current_location, $new_location);
    }

    // Copy folder
    function human_filesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor] . 'B';
    }

    function human_filesize_2($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    private function pushd($dir)
    {
        global $g_DirStack;
        array_push($g_DirStack, getcwd());
        chdir($dir);
    }

// Get a human readable file size from bytes

    private function popd()
    {
        global $g_DirStack;
        $dir = array_pop($g_DirStack);

        assert($dir != null);

        chdir($dir);
    }
}
