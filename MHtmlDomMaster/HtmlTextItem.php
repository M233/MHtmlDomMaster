<?php
    /**
     *  文本节点
     */
    class HtmlTextItem 
    {
        public $mObjParent;
        public $mStrInnerHtml;
        private function __construct()
        {
        }
        
        public static function  builder()
        {
            return new HtmlTextItem();
        }
        public function setInnerHtml($innerHtml)
        {
            $this->mStrInnerHtml=$innerHtml;
            return $this;
        }
        
    }
?>
