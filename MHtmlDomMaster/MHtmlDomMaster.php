<?php
    include_once("HtmlParser.php");
    include_once("HtmlDomMaster.php");
    include_once("HtmlAttrParser.php");
    include_once("HtmlItem.php");
    include_once("HtmlTextItem.php");
    include_once("HtmlItemIterator.php");
       
    
    class  MHtmlDomMaster extends HtmlDomMaster
    {
         //生成的Dom树
        public $mArrItems;
        //Html解析器
        public $mObjHtmlParser;
        public function __construct($html)
        {
            parent::__construct();
            $this->mObjHtmlParser=  HtmlParser::fn_get_instance();
            //解析 生成树
            $this->mArrItems=$this->mObjHtmlParser->fn_parse_html($html,$this);
        }
        
        
        /**
         * 获取包含符合条件的HtmlIterator 
		 * 如果没找到，则HtmlDomIte 
         * @param type $id
         * @return type
         */
        public function fn_find_item_by_id($id)
        {
            $id=trim($id);
            $arrItems=array();
            $item=$this->mObjIdMap->fn_get_item($id);
            if($item!=null)
            {
                $arrItems[]=$item;
            }
            return new HtmlItemIterator($arrItems);
        }
        
     
        /**
         *  获取包含符合条件的HtmlIterator
         * @param type $class
         * @return type
         */
        public function fn_find_item_by_class($class)
        {
            $class=trim($class);
            $arrItems=$this->mObjClassMap->fn_get_item($class);
            return new HtmlItemIterator($arrItems);
        }
        
        /**
         *  获取 包含指定tagName的所有HtmlItem 的Iterator
         * @param type $tagName 标签名
         * @return type
         */
        public function fn_find_item_by_tag($tagName)
        {
            $objIterator=new HtmlItemIterator($this->mArrItems);
            return $objIterator->fn_find_item_by_tag($tagName);
        }
    }
    
?> 
    

