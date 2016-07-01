<?php
/**
 * 根据html元素的Class 建立起来的引用表
 */
class HtmlItemClassMap
{
    public $mArrItems;
    public function __construct()
    {
        $this->mArrItems=array();
    }
    
    public function fn_add_item($class,$obj)
    {
        if($obj instanceof HtmlItem)
        {
            $class=  strtolower($class);
            if(isset($this->mArrItems[$class]))
            {
                $this->mArrItems[$class][]=$obj;
            }
            else
            {
                $this->mArrItems[$class]=array($obj);
            }
        }
    }
    
    /**
     *      根据HtmlItem的class 获取符合条件的HtmlItem
     * @param type $class
     * @return type     失败返回空数组
     */
    public function fn_get_item($class)
    {
        $class=  strtolower($class);
        if(isset($this->mArrItems[$class]))
        {
            return $this->mArrItems[$class];
        }
        return array();
    }
}
