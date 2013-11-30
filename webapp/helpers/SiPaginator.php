<?php

/* ---------------------------------------------------------------------------------------------------
  DESCRIPCIÓN : Paginador para active record frameworksientifica
  //@author : Ramiro Andrade
  --------------------------------------------------------------------------------------------------- */

class SiPaginator {

    private $model = null;
    private $query = null;
    public $criteria = array();
    public $jfunction = null;
    private $estructure = array();
    private $nlinksPages;
    private $nrows;
    private $quantity;
    private $page;
    private $pages;
    private $before;
    private $after;
    private $uri;
    private $data;
    private $sizes;

    public function getPages() {
        return $this->pages;
    }

    public function getPage() {
        return $this->page;
    }

    public function getNumRows() {
        return $this->nrows;
    }

    public function __construct($model, $quantity = 5, $nlinks = 5, $criteria = null) {

        $this->sizes = array($quantity, 10, 50, 100);

        if (is_array($model)) {
            $this->model = @$model['model'];
            $this->criteria = @$model['criteria'];
            $this->query = @$model['query'];
        } else if (is_object($model)) {
            $this->model = $model;
        }



        if (!is_null($criteria)) {
            $this->criteria = $criteria;
        }
        if (isset($_REQUEST['size'])) {

            $_SESSION['SiCookies']['size'] = $_REQUEST['size'];
            $this->quantity = $_REQUEST['size'];
        } elseif (isset($_SESSION['SiCookies']['size'])) {
            $this->quantity = $_SESSION['SiCookies']['size'];
        } else {
            $this->quantity = $this->sizes[0];
        }
        //======================================================================
        //Si se define parametro de búsqueda por sentencia sql
        if (!is_null($this->query)) {
            $this->estructure = $this->model->find_by_sql($model['query']);
        } else {
            if (sizeof($this->criteria) > 0 || is_object($this->criteria))
                $this->estructure = $this->model->find("all", $this->criteria);
            else
                $this->estructure = $this->model->find("all");
        }

        $this->nlinksPages = $nlinks;
        $this->page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 1;

        $this->nrows = sizeof($this->estructure);


        @$this->pages = (is_numeric($this->quantity)) ? ceil(($this->nrows) / $this->quantity) : 1;

        $data = array();
        foreach ($_GET as $key => $value) {
            if ($key != "module" && $key != 'PHPSESSID' && $key != "controller" && $key != "action" && $key != "size" && $key != "page" && $key != "token") {
                $data[$key] = $value;
            }
        }

        foreach ($_POST as $key => $value) {
            if ($key != "module" && $key != 'PHPSESSID' && $key != "controller" && $key != "action" && $key != "size" && $key != "page" && $key != "token") {
                $data[$key] = $value;
            }
        }

        $this->data = http_build_query($data);
        if (isset($_REQUEST['module']))
            $this->uri = Form::createUrl(@$_REQUEST['module'] . "/" . @$_REQUEST['controller'] . "/" . @$_REQUEST['action'] . "&" . $this->data);
        else
            $this->uri = Form::createUrl(@$_REQUEST['controller'] . "/" . @$_REQUEST['action'] . "&" . $this->data);

        $this->uri = urldecode($this->uri);
    }

    private function getOffSet() {
        $offset = ($this->quantity * $this->page) - $this->quantity;
        if ($this->page < 1 || $this->page > $this->nrows)
            return 0;
        else
            return $offset;
    }

    public function getData() {
        if (is_numeric($this->quantity))
            return array_slice($this->estructure, $this->getOffSet(), $this->quantity);

        return $this->estructure;
    }

    public function getPagination() {


        $this->after = $this->page + 1;
        $this->before = $this->page - 1;


        $back = '';
        $next = '';
        $first = '';
        $last = '';

        if (isset($this->page) && $this->page > 1) {
            $back = $this->getLink('< previous', $this->before, 'paginador_boton');
            $first = $this->getLink('<< First', 1, 'paginador_boton');
        }if (isset($this->page) && $this->page < $this->pages) {
            $next = $this->getLink('Next >>', $this->after, 'paginador_boton');
            $last = $this->getLink('Last >>', $this->pages, 'paginador_boton');
        }


        return Form::createTag('div', array('class' => 'barra_paginador'), $first . $back . $this->getLinksPages() . $next . $last, true);
    }

    private function getLinksPages() {
        $pages = '';
        $interval = ceil($this->nlinksPages / 2) - 1;
        $start = $this->page - $interval;
        $end = $this->page + $interval;

        if ($start < 1) {
            $end -= ( $start - 1);
            $start = 1;
        }
        if ($end > $this->pages) {
            $start -= ( $end - $this->pages);
            $end = $this->pages;
            if ($start < 1) {
                $start = 1;
            }
        }
        if (isset($this->pages) && $this->pages >= 1) {
            $pages = '';
            for ($k = $start; $k <= $end; $k++) {
                if ($k == $this->page) {
                    $pages .= Form::createTag('span', array('class' => 'pagina_actual'), $this->page, true);
                } else {
                    $pages .= $this->getLink($k, $k, 'paginador');
                }
            }
        }
        return $pages;
    }

    private function getLink($text, $page, $class) {

        if (is_null($this->jfunction)) {
            return Form::link($text, $this->uri . "&page=" . $page . "&size=" . $this->quantity, array('class' => $class));
        }
        return Form::link($text, 'javascript:void(0)', array('class' => $class, 'onclick' => $this->jfunction . "($page,$this->quantity,'$this->data')"));
    }

    public function getViewSizes() {
        $data = array();
        foreach ($this->sizes as $size) {
            $data[$size] = $size;
        }
        $data['all'] = "All";
        if (is_null($this->jfunction))
            return Form::dropDownList('sizes', $this->quantity, $data, array('onchange' => "document.location.href='$this->uri&page=$this->page&size='+this.value"));
        else
            return Form::dropDownList('sizes', $this->quantity, $data, array('onchange' => $this->jfunction . "(1,this.value)"));
    }

    public function setSizes(Array $sizes) {
        $this->sizes = $sizes;
    }

}
?>


