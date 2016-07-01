<?php
    /**
     *      html的子元素
     */
    class HtmlItem
    {
        //元素的标签名
        public $mStrTagName;
        //元素 里面的内容
        public $mStrInnerHtml;
        //属性
        public $mArrAttribute;
        //子标签
        public $mArrChild;
        //父元素
        public $mObjParent;
        
        public function __construct($tagName,$innerHtml,$attribute,$child,$parent)
        {
            $this->mStrTagName=$tagName;
            $this->mStrInnerHtml=$innerHtml;
            $this->mArrAttribute=$attribute;
            $this->mArrChild=$child;
            $this->mObjParent=$parent;
        }
        
        /**
         *  获取指定的属性
         * @param type $name    属性名
         * @return string  存在则返回对应的属性 失败则返回空字符串
         */
        public function fn_get_attr($name)
        {
            $name=trim($name);
            if(isset($this->mArrAttribute[$name]))
            {
                return $this->mArrAttribute[$name];
            }
            return "";
        }
    }
?>