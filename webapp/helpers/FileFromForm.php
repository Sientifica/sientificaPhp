<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileFromForm
 * 
 * Thi clase wraps the logic of a $_FILE data object that comes from a HTML form 
 * over a HTTP POST request.
 *
 * @author mauricio
 */
class FileFromForm {
    
     private $file;
     
     public function __construct($formFieldName) {
         
         
         $this->file = $_FILES[$formFieldName];
     }
     
     public function getFileAttribute ($attName)
     {
         if (isset ($this->file))
         {
             return $this->file[$attName];
         }   
         else
         {
             return false;
         }    
     }  
     
     public function copyFileToSystem ($finalFilePath)
     {
        if (! file_exists($this->file['tmp_name'])){
            return (false);
        }
        
        if (copy($this->file['tmp_name'],$finalFilePath))
        {
            return ($finalFilePath);
        }
        else
        {
            return (false);
        }
     }  
     
     public function getRawBinaryFile ()
     {
         $fp = fopen ($this->file['tmp_name'],"rb");
         $binFile = '';
         while(!feof($fp))
            $binFile .= fread($fp, filesize($this->file['tmp_name']));
         fclose($fp);
         print_r($binFile);
     }        
}

?>
