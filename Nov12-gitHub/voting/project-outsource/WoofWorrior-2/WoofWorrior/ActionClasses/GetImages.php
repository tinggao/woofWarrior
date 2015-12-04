<?php
/**
 * Created by PhpStorm.
 * User: Haziq
 * Date: 10/21/2015
 * Time: 12:39 PM
 */

error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'].'/WoofWorrior/ControllerClasses/ImageController.php');

    $Object = new ImageController();
    $Data   = $Object->getAllImages();
    if($Data[0]['Status'] == 'ok')
    {
         $Object = new ValidatingPermissions();
         if($Object->revive($Data))
         {
             echo json_encode($Object->getData());
         }
        else
        {
            echo json_encode($Object->getStatus());
        }
    }
    else
    {
          echo json_encode($Data);
    }

/**
 * Class ValidatingPermissions
 * Resolve the error which conflicted upon by the windows during uploading files
 * one solution is to copy these files to the new directory with low permission access
 * that's  what the class is responsible of
 * @author Haziq
 * Version: 1.0
 * Date: 10/23/2015
 * Time: 6:20 PM
 */
class ValidatingPermissions
{
    private $directory;
    private $data;
    private $status;


    public function __construct()
    {
        $this->directory = $_SERVER['DOCUMENT_ROOT']. '/WoofWorrior/Uploaded';
        $this->data = array();
        $this->status = array();
    }

    /**
     * Gets the images location, Description, Votes
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the cause of error or accomplishment
     * @return mixed
     */
    public  function  getStatus()
    {
        return $this->status;
    }

    /**
     * This function copy the response from the entity class to a new array
     * function is also responsible for moving the files from the strict
     * permission folder to the low security measures areas for purpose of viewing only
     *
     * @param $array
     * @return bool
     */
    public function revive($array)
    {
       array_push($this->data, ["Status"=>"ok"]);
       if(!is_dir($this->directory))
       {
           mkdir($this->directory, 0744);
       }
        //$this->directory = '/WoofWorrior/Uploaded';
       for($i=1; $i < count($array); $i++)
       {
           $id          = $array[$i]['ID'];
           $description = $array[$i]['Description'];
           $path        = $array[$i]['Path'];
           $vote        = $array[$i]['Vote'];
           $filename    = $this->getFileName($path);
           //echo 'FileName: '.$filename . '<br/>';
           $path        = substr($path, 28);
           $dest = $this->directory . '/';
           $path = $_SERVER['DOCUMENT_ROOT'] . '/WoofWorrior/' . $path;
           //echo "Path: " .$path . ' Destination: '. $dest;
           $dest = $dest . $filename;
           if (!copy($path, $dest))
           {
               array_push($this->status, ["Status" => "error", "Message" => "Error occurred while copying files"]);
               return false;
           }
           else
           {
               //file has been copied and now save them in an array
               array_push($this->data, [ "ID"=>$id, "Description"=>$description, "Path"=>$dest, "Vote"=>$vote  ]);
           }
      }
        $this->arrangeData();
        return true;
    }

    /**
     * @param $filePath
     * @return string
     */
    private function getFileName($filePath)
    {
        $filename = "";
        $path = substr($filePath,36);
        //echo 'Path: ' . $path . '<br/>';
        $index = strpos($path, "/");
        //echo 'Index: ' . $index . '<br/>';
        for($i=$index+1; $i<strlen($path); $i++)
        {
           $filename .= $path[$i];
        }
        return $filename;
    }

     public function responseToScript($array)
     {
         if($array[0]['Status'] == 'ok')
         {
             return $this->revive($array);
         }
         return false;
     }

    /**
     * Get all the image stored in the lowered permission directory
     * @return array
     */
     private function iterateDirectories()
     {
        $dir = $_SERVER['DOCUMENT_ROOT']. '/WoofWorrior/Uploaded' ;
        $files = array();
         if ($handle = opendir($dir))
         {
             while (false !== ($file = readdir($handle)))
             {
                 if ('.' === $file) continue;
                 if ('..' === $file) continue;

                 // do something with the file
                 $files[] = $file;
             }
             closedir($handle);
         }
         return $files;
     }


    /**
     * The primary role is to alter the data contain in the main array
     * also addd the new attribute namely "ALT" for the original filename inside the low permission
     * folder for safe keeping and to reduce the error which is caused due to sandbox..
     */
    private function arrangeData()
    {
       $tempArray = array();
       $array =   $this->iterateDirectories();
       array_push($tempArray, ["Status"=>"ok"]);
       $indexer = 0;
        for($i=1; $i<count($this->data); $i++)
        {
            $id          = $this->data[$i]['ID'];
            $description = $this->data[$i]['Description'];
            $path        = $this->data[$i]['Path'];
            $vote        = $this->data[$i]['Vote'];
            $file        = $array[$indexer];
            $indexer += 1;
            array_push($tempArray, [ "ID"=>$id, "Description"=>$description, "Path"=>$path, "Vote"=>$vote, "Alt"=>$file  ]);
        }
        unset($this->data);
        $this->data = $tempArray;
    }


}
