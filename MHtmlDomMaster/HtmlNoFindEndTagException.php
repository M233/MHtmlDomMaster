<?php
    class HtmlNoFindEndtagException extends Exception
    {
        public $mTagName;
        public function __construct($message, $code,$tagName)
        {
            parent::__construct($message, $code);
            $this->mTagName=$tagName;
        }
    }
?>