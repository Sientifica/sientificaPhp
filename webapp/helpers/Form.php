<?php

class Form {

    const PREFIX = 'si';

    public static $errorCss = 'error';
    static $count = 0;
    public static $errorSummaryCss = 'msg error';
    public static $beforeRequiredLabel = '';
    public static $afterRequiredLabel = ' <span class="required">*</span>';

    public static function createTag($tag, $itemsOptions = array(), $content = false, $closeTag = true) {
        $html = '<' . $tag . self::renderAttrs($itemsOptions);
        if ($content === false)
            return $closeTag ? $html . ' />' : $html . '>';
        else
            return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    protected static function InputElement($type, $name, $value, $elements) {
        $elements['type'] = $type;
        $elements['value'] = $value;
        $elements['name'] = $name;
        if (!isset($elements['id']))
            $elements['id'] = self::getIdByName($name);
        else if ($elements['id'] === false)
            unset($elements['id']);
        return self::createTag('input', $elements);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function textField($name, $value = '', $itemsOptions = null) {

        return self::InputElement('text', $name, $value, $itemsOptions);
    }

    /**
     * Generates a hidden input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $itemsOptions additional HTML attributes (see {@link tag}).
     * @return string the generated input field
     * @see inputField
     */
    public static function hiddenField($name, $value = '', $itemsOptions = array()) {
        return self::InputElement('hidden', $name, $value, $itemsOptions);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function passwordField($name, $value = '', $itemsOptions = array()) {

        return self::InputElement('password', $name, $value, $itemsOptions);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function fileField($name, $value = '', $itemsOptions = array()) {
        return self::InputElement('file', $name, $value, $itemsOptions);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function radioButton($name, $checked = false, $itemsOptions = array()) {
        if ($checked)
            $itemsOptions['checked'] = 'checked';
        else
            unset($itemsOptions['checked']);
        $value = isset($itemsOptions['value']) ? $itemsOptions['value'] : 1;


        if (array_key_exists('uncheckValue', $itemsOptions)) {
            $uncheck = $itemsOptions['uncheckValue'];
            unset($itemsOptions['uncheckValue']);
        }
        else
            $uncheck = null;

        if ($uncheck !== null) {
            // add a hidden field so that if the radio button is not selected, it still submits a value
            if (isset($itemsOptions['id']) && $itemsOptions['id'] !== false)
                $uncheckOptions = array('id' => self::PREFIX . $itemsOptions['id']);
            else
                $uncheckOptions = array('id' => false);
            $hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
        }
        else
            $hidden = '';

        // add a hidden field so that if the radio button is not selected, it still submits a value
        return $hidden . self::InputElement('radio', $name, $value, $itemsOptions);
    }

    public static function radioButtonList($name, $select, $data, $htmlOptions = array()) {
        $template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
        $separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "<br/>\n";
        unset($htmlOptions['template'], $htmlOptions['separator']);

        if (substr($name, -2) !== '[]')
            $name.='[]';

        if (isset($htmlOptions['checkAll'])) {
            $checkAllLabel = $htmlOptions['checkAll'];
            $checkAllLast = isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
        }
        unset($htmlOptions['checkAll'], $htmlOptions['checkAllLast']);

        $labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
        unset($htmlOptions['labelOptions']);

        $items = array();
        $baseID = self::getIdByName($name);
        $id = 0;
        $checkAll = true;

        foreach ($data as $value => $label) {
            $checked = !is_array($select) && !strcmp($value, $select) || is_array($select) && in_array($value, $select);
            $checkAll = $checkAll && $checked;
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID . '_' . $id++;
            $option = self::radioButton($name, $checked, $htmlOptions);
            $label = self::label($label, $htmlOptions['id'], $labelOptions);
            $items[] = strtr($template, array('{input}' => $option, '{label}' => $label));
        }

        if (isset($checkAllLabel)) {
            $htmlOptions['value'] = 1;
            $htmlOptions['id'] = $id = $baseID . '_all';
            $option = self::radioButton($id, $checkAll, $htmlOptions);
            $label = self::label($checkAllLabel, $id, $labelOptions);
            $item = strtr($template, array('{input}' => $option, '{label}' => $label));
            if ($checkAllLast)
                $items[] = $item;
        }

        return implode($separator, $items);
    }

    public static function checkBox($name, $checked = false, $itemsOptions = array()) {
        if ($checked)
            $itemsOptions['checked'] = 'checked';
        else
            unset($itemsOptions['checked']);
        $value = isset($itemsOptions['value']) ? $itemsOptions['value'] : 1;


        if (array_key_exists('uncheckValue', $itemsOptions)) {
            $uncheck = $itemsOptions['uncheckValue'];
            unset($itemsOptions['uncheckValue']);
        }
        else
            $uncheck = null;

        if ($uncheck !== null) {
            // add a hidden field so that if the radio button is not selected, it still submits a value
            if (isset($itemsOptions['id']) && $itemsOptions['id'] !== false)
                $uncheckOptions = array('id' => self::PREFIX . $itemsOptions['id']);
            else
                $uncheckOptions = array('id' => false);
            $hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
        }
        else
            $hidden = '';

        // add a hidden field so that if the checkbox  is not selected, it still submits a value
        return $hidden . self::InputElement('checkbox', $name, $value, $itemsOptions);
    }

    public static function checkBoxList($name, $select, $data, $htmlOptions = array()) {
        $template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
        $separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "<br/>\n";
        unset($htmlOptions['template'], $htmlOptions['separator']);

        if (substr($name, -2) !== '[]')
            $name.='[]';

        if (isset($htmlOptions['checkAll'])) {
            $checkAllLabel = $htmlOptions['checkAll'];
            $checkAllLast = isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
        }
        unset($htmlOptions['checkAll'], $htmlOptions['checkAllLast']);

        $labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
        unset($htmlOptions['labelOptions']);

        $items = array();
        $baseID = self::getIdByName($name);
        $id = 0;
        $checkAll = true;

        foreach ($data as $value => $label) {
            $checked = !is_array($select) && !strcmp($value, $select) || is_array($select) && in_array($value, $select);
            $checkAll = $checkAll && $checked;
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID . '_' . $id++;
            $option = self::checkBox($name, $checked, $htmlOptions);
            $label = self::label($label, $htmlOptions['id'], $labelOptions);
            $items[] = strtr($template, array('{input}' => $option, '{label}' => $label));
        }

        if (isset($checkAllLabel)) {
            $htmlOptions['value'] = 1;
            $htmlOptions['id'] = $id = $baseID . '_all';
            $option = self::checkBox($id, $checkAll, $htmlOptions);
            $label = self::label($checkAllLabel, $id, $labelOptions);
            $item = strtr($template, array('{input}' => $option, '{label}' => $label));
            if ($checkAllLast)
                $items[] = $item;
        }

        return implode($separator, $items);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function textArea($name, $value = '', $itemsOptions = array()) {
        $itemsOptions['name'] = $name;
        if (!isset($itemsOptions['id']))
            $itemsOptions['id'] = self::getIdByName($name);
        else if ($itemsOptions['id'] === false)
            unset($itemsOptions['id']);

        return self::createTag('textarea', $itemsOptions, isset($itemsOptions['encode']) && !$itemsOptions['encode'] ? $value : self::encode($value));
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function getIdByName($name) {
        return str_replace(array('[]', '][', '[', ']'), array('', '_', '_', ''), $name);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    private static function renderAttrs($options) {
        $html = '';
        foreach ($options as $attrs => $values) {
            if (is_object($values) && get_class($values) == 'ActiveRecord\DateTime') {

                $html .= " {$attrs}=\"{$values->format("d/m/Y")}\" ";
            } else {

                if (is_array($values)) {
                    foreach ($values as $key => $val) {
                        $html .= " {$key}=\"{$val}\" ";
                    }
                } else {

                    $html .= " {$attrs}=\"{$values}\" ";
                }
            }
        }

        return $html;
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function encode($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function beginForm($action = "", $method = 'post', $itemsOptions = array()) {

        $hiddens = array();

        if ($action != "")
            $itemsOptions['action'] = self::createUrl($action);
        else
            $itemsOptions['action'] = "";

        $itemsOptions['method'] = $method;

        $form = self::createTag('form', $itemsOptions, false, false);

        if (strtolower($itemsOptions['method']) == 'post')
            $hiddens[] = self::hiddenField('token', $_SESSION['CSRF'], array('id' => false));

        if (strtolower($itemsOptions['method']) == 'get') {
            foreach ($_GET as $key => $value) {
                if ($key == "module" || $key == "controller" || $key == "action") {
                    $hiddens[] = self::hiddenField($key, $value, array('id' => false));
                }
            }
        }
        if ($hiddens !== array())
            $form.="\n" . self::createTag('div', array('style' => 'display:none'), implode("\n", $hiddens));
        return $form;
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function endForm() {
        return '</form>';
    }

    public static function createUrl($route, $params = array(), $ampersand = '&') {

        foreach ($params as &$param)
            if ($param === null)
                $param = '';
        if (isset($params['#'])) {
            $anchor = '#' . $params['#'];
            unset($params['#']);
        }
        else
            $anchor = '';
        $route = trim($route, '/');

        return self::createUrlDefault($route, $params, $ampersand) . $anchor;
    }

    protected static function createUrlDefault($route, $params = array(), $ampersand = '&') {

        //$url = Base::request()->getBaseUrl();
        $url = Base::request()->getUrlScript();

        $request = explode("/", $route);
        $n = sizeof($request);


        if ($n == 1) {
            if (!empty($_REQUEST['module']))
                $params['module'] = @$_REQUEST['module'];

            $params['controller'] = @$_REQUEST['controller'];
            $params['action'] = $route;
            return self::createPathInfo($params, '=', $ampersand);
        }
        if ($n == 3) {
            list($moduleID, $controllerID, $actionID) = @$request;
            $params['module'] = $moduleID;
            $params['controller'] = $controllerID;
            $params['action'] = $actionID;

            return self::createPathInfo($params, '=', $ampersand);
        } else {
            list($controllerID, $actionID) = @$request;
            $params['controller'] = $controllerID;
            $params['action'] = $actionID;

            return self::createPathInfo($params, '=', $ampersand);
        }




        return $url;
    }

    public static function parsePathInfo($pathInfo) {
        if ($pathInfo === '')
            return;
        $segs = explode('/', $pathInfo . '/');
        $n = count($segs);
        for ($i = 0; $i < $n - 1; $i+=2) {
            $key = $segs[$i];
            if ($key === '')
                continue;
            $value = $segs[$i + 1];
            if (($pos = strpos($key, '[')) !== false && ($pos2 = strpos($key, ']', $pos + 1)) !== false) {
                $name = substr($key, 0, $pos);
                if ($pos2 === $pos + 1)
                    $_REQUEST[$name][] = $_GET[$name][] = $value;
                else {
                    $key = substr($key, $pos + 1, $pos2 - $pos - 1);
                    $_REQUEST[$name][$key] = $_GET[$name][$key] = $value;
                }
            }
            else
                $_REQUEST[$key] = $_GET[$key] = $value;
        }
    }

    public static function createPathInfo($params, $equal, $ampersand, $key = null) {
        $pairs = array();
        foreach ($params as $k => $v) {
            if ($key !== null)
                $k = $key . '[' . $k . ']';

            if (is_array($v))
                $pairs[] = self::createPathInfo($v, $equal, $ampersand, $k);
            else
                $pairs[] = urlencode($k) . $equal . urlencode($v);
        }
        return "?" . implode($ampersand, $pairs);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function normalizeUrl($url) {

        if (is_array($url)) {
            if (sizeof($url) > 1) {
                $arrvars = array_splice($url, 1);
                //$arrvars['token'] = Base::$CSRF;
                $url = self::createUrl($url[0], $arrvars);
            }
            else
                $url = self::createUrlDefault($url[0]);
        }
        return $url;
    }

    public static function link($text, $url = '#', $itemsOptions = array()) {
        if ($url !== '')
            $itemsOptions['href'] = self::normalizeUrl($url);

        return self::createTag('a', $itemsOptions, $text);
    }

    public static function image($src, $alt = '', $itemsOptions = array()) {
        $itemsOptions['src'] = $src;
        $itemsOptions['alt'] = $alt;
        return self::createTag('img', $itemsOptions);
    }

    /**
     * Generates .
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function mailto($text, $email = '', $itemsOptions = array()) {
        if ($email === '')
            $email = $text;
        return self::link($text, 'mailto:' . $email, $itemsOptions);
    }

    public static function button($label = 'button', $itemsOptions = array()) {
        if (!isset($itemsOptions['name'])) {
            if (!array_key_exists('name', $itemsOptions))
                $itemsOptions['name'] = self::PREFIX . self::$count++;
        }
        if (!isset($itemsOptions['type']))
            $itemsOptions['type'] = 'button';
        if (!isset($itemsOptions['value']))
            $itemsOptions['value'] = $label;

        return self::createTag('input', $itemsOptions);
    }

    public static function htmlButton($label = 'button', $itemsOptions = array()) {
        if (!isset($itemsOptions['name']))
            $itemsOptions['name'] = self::PREFIX . self::$count++;
        if (!isset($itemsOptions['type']))
            $itemsOptions['type'] = 'button';

        return self::createTag('button', $itemsOptions, $label);
    }

    public static function submitButton($label = 'submit', $itemsOptions = array()) {
        $itemsOptions['type'] = 'submit';
        return self::button($label, $itemsOptions);
    }

    public static function resetButton($label = 'reset', $itemsOptions = array()) {
        $itemsOptions['type'] = 'reset';
        return self::button($label, $itemsOptions);
    }

    public static function imageButton($src, $itemsOptions = array()) {
        $itemsOptions['src'] = $src;
        $itemsOptions['type'] = 'image';
        return self::button('submit', $itemsOptions);
    }

    public static function linkButton($label = 'submit', $itemsOptions = array()) {
        if (!isset($itemsOptions['submit']))
            $itemsOptions['submit'] = isset($itemsOptions['href']) ? $itemsOptions['href'] : '';
        return self::link($label, '#', $itemsOptions);
    }

    public static function dropDownList($name, $select, $data, $itemsOptions = array()) {
        $itemsOptions['name'] = $name;
        if (!isset($itemsOptions['id']))
            $itemsOptions['id'] = self::getIdByName($name);
        else if ($itemsOptions['id'] === false)
            unset($itemsOptions['id']);

        $options = "\n" . self::listOptions($select, $data, $itemsOptions);
        return self::createTag('select', $itemsOptions, $options);
    }

    public static function listOptions($selection, $listData, &$itemsOptions) {
        $raw = isset($itemsOptions['encode']) && !$itemsOptions['encode'];
        $content = '';
        if (isset($itemsOptions['prompt'])) {
            $content.='<option value="">' . strtr($itemsOptions['prompt'], array('<' => '&lt;', '>' => '&gt;')) . "</option>\n";
            unset($itemsOptions['prompt']);
        }
        if (isset($itemsOptions['empty'])) {
            if (!is_array($itemsOptions['empty']))
                $itemsOptions['empty'] = array('' => $itemsOptions['empty']);
            foreach ($itemsOptions['empty'] as $value => $label)
                $content.='<option value="' . self::encode($value) . '">' . strtr($label, array('<' => '&lt;', '>' => '&gt;')) . "</option>\n";
            unset($itemsOptions['empty']);
        }

        if (isset($itemsOptions['options'])) {
            $options = $itemsOptions['options'];
            unset($itemsOptions['options']);
        }
        else
            $options = array();

        $key = isset($itemsOptions['key']) ? $itemsOptions['key'] : 'primaryKey';
        if (is_array($selection)) {
            foreach ($selection as $i => $item) {
                if (is_object($item))
                    $selection[$i] = $item->$key;
            }
        }
        else if (is_object($selection))
            $selection = $selection->$key;

        foreach ($listData as $key => $value) {
            if (is_array($value)) {
                $content.='<optgroup label="' . ($raw ? $key : self::encode($key)) . "\">\n";
                $dummy = array('options' => $options);
                if (isset($itemsOptions['encode']))
                    $dummy['encode'] = $itemsOptions['encode'];
                $content.=self::listOptions($selection, $value, $dummy);
                $content.='</optgroup>' . "\n";
            }
            else {
                $attributes = array('value' => (string) $key, 'encode' => !$raw);
                if (!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection))
                    $attributes['selected'] = 'selected';
                if (isset($options[$key]))
                    $attributes = array_merge($attributes, $options[$key]);
                $content.=self::createTag('option', $attributes, $raw ? (string) $value : self::encode((string) $value)) . "\n";
            }
        }

        unset($itemsOptions['key']);

        return $content;
    }

    public static function listData($models, $valueField, $textField, $groupField = '', $cat = " ") {
        $listData = array();
        if ($groupField === '') {
            foreach ($models as $model) {
                $value = self::value($model, $valueField);
                if (is_array($textField)) {
                    $arrVals = array();
                    foreach ($textField as $attr) {
                        $arrVals[] = self::value($model, $attr);
                    }
                    $text = implode($cat, $arrVals);
                } else {
                    $text = self::value($model, $textField);
                }
                $listData[$value] = $text;
            }
        } else {
            foreach ($models as $model) {
                $group = self::value($model, $groupField);
                $value = self::value($model, $valueField);
                if (is_array($textField)) {
                    $arrVals = array();
                    foreach ($textField as $attr) {
                        $arrVals[] = self::value($model, $attr);
                    }
                    $text = implode($cat, $arrVals);
                } else {
                    $text = self::value($model, $textField);
                }
                $listData[$group][$value] = $text;
            }
        }
        return $listData;
    }

    public static function listDataTypeEnum($model, $field) {
        $listData = array();
        $model = $model->find_by_sql('SHOW COLUMNS FROM  ' . $model->table()->table . " LIKE '" . $field . "'");
        if ($model) {
            $type = $model[0]->type;
            $arrVals = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/", "\\2", $type));

            foreach ($arrVals as $value) {
                if (!empty($value))
                    $listData[$value] = $value;
            }
        }

        return $listData;
    }

    public static function value($model, $attribute, $defaultValue = null) {
        foreach (explode('.', $attribute) as $name) {
            if (is_object($model))
                $model = $model->$name;
            else if (is_array($model) && isset($model[$name]))
                $model = $model[$name];
            else
                return $defaultValue;
        }
        return $model;
    }

    public static function activeTextField($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        return self::activeInputElement('text', $model, $attribute, $itemsOptions);
    }

    public static function activeHiddenField($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        return self::activeInputElement('hidden', $model, $attribute, $itemsOptions);
    }

    public static function activePasswordField($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        return self::activeInputElement('password', $model, $attribute, $itemsOptions);
    }

    public static function label($label, $for, $itemsOptions = array()) {
        if ($for === false)
            unset($itemsOptions['for']);
        else
            $itemsOptions['for'] = $for;
        if (isset($itemsOptions['required'])) {
            if ($itemsOptions['required']) {
                if (isset($itemsOptions['class']))
                    $itemsOptions['class'].=' ' . self::$requiredCss;
                else
                    $itemsOptions['class'] = self::$requiredCss;
                $label = self::$beforeRequiredLabel . $label . self::$afterRequiredLabel;
            }
            unset($itemsOptions['required']);
        }
        return self::createTag('label', $itemsOptions, $label);
    }

    public static function activeTextArea($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        $text = self::resolveValue($model, $attribute);
        if ($model->hasErrors($attribute))
            self::addErrorCss($itemsOptions);
        return self::createTag('textarea', $itemsOptions, isset($itemsOptions['encode']) && !$itemsOptions['encode'] ? $text : self::encode($text));
    }

    public static function activeLabel($model, $attribute, $itemsOptions = array()) {
        if (isset($itemsOptions['for'])) {
            $for = $itemsOptions['for'];
            unset($itemsOptions['for']);
        }
        else
            $for = self::getIdByName(self::resolveName($model, $attribute));
        if (isset($itemsOptions['label'])) {
            if (($label = $itemsOptions['label']) === false)
                return '';
            unset($itemsOptions['label']);
        }
        else
            $label = $model->getAttributeLabel($attribute);

        return self::label($label, $for, $itemsOptions);
    }

    protected static function activeInputElement($type, $model, $attribute, $itemsOptions) {
        $itemsOptions['type'] = $type;

        if ($type === 'file')
            unset($itemsOptions['value']);
        else if (!isset($itemsOptions['value']))
            if (is_string(self::resolveValue($model, $attribute)))
                $itemsOptions['value'] = htmlentities(utf8_decode(self::resolveValue($model, $attribute)));
            else
                $itemsOptions['value'] = self::resolveValue($model, $attribute);

        if ($model->hasErrors($attribute))
            self::addErrorCss($itemsOptions);
        return self::createTag('input', $itemsOptions);
    }

    public static function activeFileField($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        $hiddenOptions = isset($itemsOptions['id']) ? array('id' => self::PREFIX . $itemsOptions['id']) : array('id' => false);
        return self::hiddenField($itemsOptions['name'], '', $hiddenOptions)
                . self::activeInputElement('file', $model, $attribute, $itemsOptions);
    }

    public static function activeRadioButton($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        if (!isset($itemsOptions['value']))
            $itemsOptions['value'] = 1;
        if (!isset($itemsOptions['checked']) && self::resolveValue($model, $attribute) == $itemsOptions['value'])
            $itemsOptions['checked'] = 'checked';


        if (array_key_exists('uncheckValue', $itemsOptions)) {
            $uncheck = $itemsOptions['uncheckValue'];
            unset($itemsOptions['uncheckValue']);
        }
        else
            $uncheck = '0';

        $hiddenOptions = isset($itemsOptions['id']) ? array('id' => self::PREFIX . $itemsOptions['id']) : array('id' => false);
        $hidden = $uncheck !== null ? self::hiddenField($itemsOptions['name'], $uncheck, $hiddenOptions) : '';

        // add a hidden field so that if the radio button is not selected, it still submits a value
        return $hidden . self::activeInputElement('radio', $model, $attribute, $itemsOptions);
    }

    public static function activeRadioButtonList($model, $attribute, $data, $htmlOptions = array()) {
        self::resolveNameID($model, $attribute, $htmlOptions);
        $selection = self::resolveValue($model, $attribute);
        if ($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        $name = $htmlOptions['name'];
        unset($htmlOptions['name']);

        if (array_key_exists('uncheckValue', $htmlOptions)) {
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck = '';

        $hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::PREFIX . $htmlOptions['id']) : array('id' => false);
        $hidden = $uncheck !== null ? self::hiddenField($name, $uncheck, $hiddenOptions) : '';

        return $hidden . self::radioButtonList($name, $selection, $data, $htmlOptions);
    }

    public static function activeCheckBox($model, $attribute, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        if (!isset($itemsOptions['value']))
            $itemsOptions['value'] = 1;
        if (!isset($itemsOptions['checked']) && self::resolveValue($model, $attribute) == $itemsOptions['value'])
            $itemsOptions['checked'] = 'checked';


        if (array_key_exists('uncheckValue', $itemsOptions)) {
            $uncheck = $itemsOptions['uncheckValue'];
            unset($itemsOptions['uncheckValue']);
        }
        else
            $uncheck = '0';

        $hiddenOptions = isset($itemsOptions['id']) ? array('id' => self::PREFIX . $itemsOptions['id']) : array('id' => false);
        $hidden = $uncheck !== null ? self::hiddenField($itemsOptions['name'], $uncheck, $hiddenOptions) : '';

        return $hidden . self::activeInputElement('checkbox', $model, $attribute, $itemsOptions);
    }

    public static function activeCheckBoxList($model, $attribute, $data, $htmlOptions = array()) {
        self::resolveNameID($model, $attribute, $htmlOptions);
        $selection = self::resolveValue($model, $attribute);
        if ($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        $name = $htmlOptions['name'];
        unset($htmlOptions['name']);

        if (array_key_exists('uncheckValue', $htmlOptions)) {
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck = '';

        $hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::PREFIX . $htmlOptions['id']) : array('id' => false);
        $hidden = $uncheck !== null ? self::hiddenField($name, $uncheck, $hiddenOptions) : '';

        return $hidden . self::checkBoxList($name, $selection, $data, $htmlOptions);
    }

    public static function activeDropDownList($model, $attribute, $data, $itemsOptions = array()) {
        self::resolveNameID($model, $attribute, $itemsOptions);
        $selection = self::resolveValue($model, $attribute);
        $options = "\n" . self::listOptions($selection, $data, $itemsOptions);

        if (isset($itemsOptions['multiple'])) {
            if (substr($itemsOptions['name'], -2) !== '[]')
                $itemsOptions['name'].='[]';
        }
        if ($model->hasErrors($attribute))
            self::addErrorCss($itemsOptions);
        return self::createTag('select', $itemsOptions, $options);
    }

    public static function resolveValue($model, $attribute) {
        if (($pos = strpos($attribute, '[')) !== false) {
            if ($pos === 0) {  // [a]name[b][c], should ignore [a]
                if (preg_match('/\](\w+)/', $attribute, $matches))
                    $attribute = $matches[1];
                if (($pos = strpos($attribute, '[')) === false)
                    return $model->$attribute;
            }
            $name = substr($attribute, 0, $pos);
            $value = $model->$name;
            foreach (explode('][', rtrim(substr($attribute, $pos + 1), ']')) as $id) {
                if (is_array($value) && isset($value[$id]))
                    $value = $value[$id];
                else
                    return null;
            }
            return $value;
        }
        else
            return $model->$attribute;
    }

    public static function resolveName($model, &$attribute) {
        if (($pos = strpos($attribute, '[')) !== false) {
            if ($pos !== 0)  // e.g. name[a][b]
                return get_class($model) . '[' . substr($attribute, 0, $pos) . ']' . substr($attribute, $pos);
            if (($pos = strrpos($attribute, ']')) !== false && $pos !== strlen($attribute) - 1) {  // e.g. [a][b]name
                $sub = substr($attribute, 0, $pos + 1);
                $attribute = substr($attribute, $pos + 1);
                return get_class($model) . $sub . '[' . $attribute . ']';
            }
            if (preg_match('/\](\w+\[.*)$/', $attribute, $matches)) {
                $name = get_class($model) . '[' . str_replace(']', '][', trim(strtr($attribute, array('][' => ']', '[' => ']')), ']')) . ']';
                $attribute = $matches[1];
                return $name;
            }
        }
        else
            return get_class($model) . '[' . $attribute . ']';
    }

    public static function resolveNameID($model, &$attribute, &$itemsOptions) {
        if (!isset($itemsOptions['name']))
            $itemsOptions['name'] = self::resolveName($model, $attribute);
        if (!isset($itemsOptions['id']))
            $itemsOptions['id'] = self::getIdByName($itemsOptions['name']);
        else if ($itemsOptions['id'] === false)
            unset($itemsOptions['id']);
    }

    public static function errorSummary($models, $header = null, $footer = null, $itemsOptions = array()) {
        $content = '';
        if ($_POST) {
            if (!is_array($models)) {

                foreach ($models->getErrors() as $error) {
                    $content.="<li> $error</li>\n";
                }
                if ($content !== '') {
                    if ($header === null)
                        $header = '<p><strong> Please fix the following errors:</strong></p>';
                    if (!isset($itemsOptions['class']))
                        $itemsOptions['class'] = self::$errorSummaryCss;
                    return self::createTag('div', $itemsOptions, $header . "\n<ul>\n$content</ul>" . $footer);
                }
            }else {
                foreach ($models as $model) {
                    foreach ($model->getErrors() as $error) {
                        $content.="<li> $error</li>\n";
                    }
                }
                if ($content !== '') {
                    if ($header === null)
                        $header = '<p> Please fix the following errors:</p>';
                    if (!isset($itemsOptions['class']))
                        $itemsOptions['class'] = self::$errorSummaryCss;
                    return self::createTag('div', $itemsOptions, $header . "\n<ul>\n$content</ul>" . $footer);
                }
            }
        }
        else
            return '';
    }

    protected static function addErrorCss(&$itemsOptions) {
        if (isset($itemsOptions['class']))
            $itemsOptions['class'].=' ' . self::$errorCss;
        else
            $itemsOptions['class'] = self::$errorCss;
    }

}

?>
