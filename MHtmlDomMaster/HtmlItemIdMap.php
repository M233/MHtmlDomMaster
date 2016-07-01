<?php
/**
 *   根据html元素的ID 建立起来的映射表
 */
class HtmlItemIdMap
{
    public $mArrItems;
    public function __construct()
    {
        $this->mArrItems=array();
    }
    /**
     *  添加一个item
     * @param type $id
     * @param HtmlItem $obj
     */
    public function fn_add_item($id,$obj)
    {
        $id=  strtolower($id);
        if($obj instanceof HtmlItem)
        {
            if(!isset($this->mArrItems[$id]))
            {
                //如果没有设置则 添加
                $this->mArrItems[$id]=$obj;
            }
        }
    }
    /**
     *  获取一个Item 通过html元素的id
     * @param type $id
     * @return null 存在返回元素  不存在返回null
     */
    public function fn_get_item($id)
    {
        $id=  strtolower($id);
        if(isset($this->mArrItems[$id]))
        {
            return $this->mArrItems[$id];
        }
        return null;
    }
    /**
     *  清空映射表
     */
    public function fn_clean()
    {
        $this->mArrItems=array();
    }
}

?>
