<?php
    include_once ("HtmlItemIdMap.php");
    include_once ("HtmlItemClassMap.php");
  
    class HtmlDomMaster
    {
        //根据HtmlItem的Id建立起来的映射表
        public $mObjIdMap;
        //根据HtmlItem的Class建立起来的映射表
        public $mObjClassMap;
        public function __construct()
        {
            $this->mObjIdMap=new HtmlItemIdMap();
            $this->mObjClassMap=new HtmlItemClassMap();
        }

        //添加一个item 到 $mObjIdMap 中去
        public function fn_add_id_item($id,$obj)
        {
            $this->mObjIdMap->fn_add_item($id, $obj);
        }

        //添加一个item 到 $mObjClassMap中
        public function fn_add_class_item($class,$obj)
        {
            $this->mObjClassMap->fn_add_item($class, $obj);
        }
        
        
    }
?>
