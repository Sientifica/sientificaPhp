<?php

class UploadFile {

    public $field_name = "";
    public $file_ext = "txt";
    public $save_path = ".";
    public $save_filename = "";
    public $file = "";
    public $has_file = false;
    public $is_save = false;
    public $class;
    public $file_tmp = null;
    public $index = '';

    public function UploadFile($model, $field_name, $file_name = "") {


        if (is_object($model)) {
            $this->class = get_class($model);


            $pattern = '/(.*)\[+([0-9]*)\]/';
            $replacement = '${1},${2}';
            $getvalues = preg_replace($pattern, $replacement, $field_name);
            @list($this->field_name, $this->index) = explode(",", $getvalues);


            if ($this->hasUploadFileModel()) {
                $this->has_file = true;
                if (isset($this->index)) {
                    $filestr = @$_FILES[$this->class]["name"][$this->index][$this->field_name];
                    $this->file_tmp = @$_FILES[$this->class]["tmp_name"][$this->index][$this->field_name];
                } else {
                    $filestr = @$_FILES[$this->class]["name"][$this->field_name];
                    $this->file_tmp = @$_FILES[$this->class]["tmp_name"][$this->field_name];
                }
                $this->file = $filestr;
                $this->file_ext = $this->getFileExtend($filestr);
                $this->save_filename = $file_name;
                $this->randFileName();
            }
        } else {
            $this->class = false;

            $this->field_name = $field_name;
            if ($this->hasUploadFile()) {
                $this->has_file = true;
                $filestr = @$_FILES[$this->field_name]["name"];
                $this->file_tmp = @$_FILES[$this->field_name]["tmp_name"];
                $this->file = $filestr;
                $this->file_ext = $this->getFileExtend($filestr);
                $this->randFileName();
            }
        }
    }

    public function hasUploadFileModel() {
        if (isset($_FILES[$this->class]['name'])) {
            if (isset($this->index)) {
                if (is_array(@$_FILES[$this->class]['name'][$this->index])) {
                    if (!array_key_exists($this->field_name, @$_FILES[$this->class]['name'][$this->index])) {
                        return false;
                    }
                }
            } else {
                if (!array_key_exists($this->field_name, @$_FILES[$this->class]['name'])) {
                    return false;
                }
            }
            return @$_FILES[$this->class]["error"][$this->index][$this->field_name] == 0;
        }else
            return false;
    }

    public function hasUploadFile() {
        if (isset($_FILES)) {
            if (!array_key_exists($this->field_name, @$_FILES)) {
                return false;
            }
            return @$_FILES[$this->field_name]["error"] == 0;
        }else
            return false;
    }

    public function randFileName() {
        $result = '';
         $result .= microtime();
          $result .= rand(10000, 99999);
          $result = preg_replace('/[\. ]/', '', $result);
         
        $extend = explode(".", $this->file);
       // $result .= SiFunctions::formatUrl($extend[0]) . "_" . date("YmdHis");
        if ($this->save_filename == "")
            $this->save_filename = $result . "." . $this->file_ext;
        else
            $this->save_filename = $this->save_filename . "." . $this->file_ext;
    }

    public function getFileExtend($filestr) {
        $extend = explode(".", $filestr);
        $va = count($extend) - 1;
        return $extend[$va];
    }

    public function save() {
        if (!$this->class) {
            $src_filename = @$_FILES[$this->field_name]["tmp_name"];
        } else {
            if (isset($this->index)) {
                $src_filename = @$_FILES[$this->class]["tmp_name"][$this->index][$this->field_name];
            } else {
                $src_filename = @$_FILES[$this->class]["tmp_name"][$this->field_name];
            }
        }
        $dest_filename = $this->save_path . "/" . $this->save_filename;
        return move_uploaded_file($src_filename, $dest_filename);
    }

    function inType($extlist) {
        return in_array($this->file_ext, $extlist);
    }

    public function isUploaded(){

     return  is_uploaded_file($this->file_tmp);

    }

}
