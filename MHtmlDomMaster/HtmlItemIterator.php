<?php

class HtmlItemIterator
{
    //搜索的items
    public $mArrItems;
    public function __construct($arr)
    {
        $this->mArrItems=$arr;
    }
    
    /**
     *  获取$index的指定 HtmlItem 
     * @param type $index
     * @return null 成功则返回item 失败 返回 NUll
     */
    public function fn_get_item($index=0)
    {
        //边界检查
        if($index<0 || $index>= count($this->mArrItems))
        {
            //超过数组边界
            return null;
        }
        return $this->mArrItems[$index];
    }
    
    /**
     *  查询item 通过id
     * @param type $id
     * @return \HtmlItemIterator  成功返回包含指定HtmlItem的iterator 否则返回包含空item的iterator
     */
    public function fn_find_item_by_id($id)
    {
        //ID 全部小写
        $id=  strtolower($str);
        //需要查找的HtmlItem
        $arrFindItems=$this->mArrItems;
        //查找符合结果的Items
        $arrResultItems=array();
        //是否继续查找
        $isContinueFind=true;
        while(count($arrItems) && $isContinueFind)
        {
             //同层的子节点
            $arrComtemporaryChild=array();
            foreach($arrFindItems as $item)
            {
                if(!($item instanceof HtmlItem))
                {
                    //不是Html普通节点则忽略
                    continue;
                }
                if($item->fn_get_attr("id")==$id)
                {
                    //如果ID相等的话返回 并结束循环
                    //return new HtmlItemIterator(array($item));;
                    $arrResultItems[]=$item;
                    //结束循环查找
                    $isContinueFind=false;
                    break;
                }
                if(count($item->mArrChild))
                {
                    //如果子节点存在 则添加到同层子节点中
                    $arrComtemporaryChild=  array_merge($arrComtemporaryChild,$item->mArrChild);
                }
            }
            //该层次没有符合条件的节点 开始查询下一个层次的
            $arrFindItems=$arrComtemporaryChild;
        }
        //所有节点搜索完毕 没有符合条件的 返回空HtmlIterator
        return new HtmlItemIterator($arrResultItems);
    }
    
    /**
     *  通过class 查询符合条件的item
     * @param type $class       类名
     * @return \HtmlItemIterator    成功返回包含指定HtmlItem的iterator 否则返回包含空item的iterator
     */
    public function fn_find_item_by_class($class)
    {
        //class 全部小写
        $class=  strtolower($class);
        $arrItems=$this->mArrItems;
        $arrClassItem=array();
        while(count($arrItems))
        {
            //同层的子节点
            $arrComtemporaryChild=array();
            foreach($arrItems as $item)
            {
                if(!($item instanceof HtmlItem))
                {
                    //不是Html普通节点则忽略
                    continue;
                }
                //分割className 获得字符数组
                $arrClass=preg_split('/\s+/',$item->fn_get_attr("class"));
                if(in_array($class, $arrClass))
                {
                    //添加数组当中
                    $arrClassItem[]=$item;
                }
                if(count($item->mArrChild))
                {
                    //如果子节点存在 则添加到同层子节点中
                    $arrComtemporaryChild=  array_merge($arrComtemporaryChild,$item->mArrChild);
                }
            }
            //该层次已经查询完毕 开始查询下一个层次的
            $arrItems=$arrComtemporaryChild;
        }
        return new HtmlItemIterator($arrClassItem);
    }
    
    /**
     *  通过tagName  查询符合条件的item
     * @param type $tagName  标签名 忽略大小写
     * @return \HtmlItemIterator
     */
    public function fn_find_item_by_tag($tagName)
    {
        //class 全部小写
        $tagName=  strtolower($tagName);
        $arrItems=$this->mArrItems;
        $arrTagItems=array();
        while(count($arrItems))
        {
            //同层的子节点
            $arrComtemporaryChild=array();
            foreach($arrItems as $item)
            {
                if(!($item instanceof HtmlItem))
                {
                    //不是Html普通节点则忽略
                    continue;
                }
                if($item->mStrTagName==$tagName)
                {
                    //添加数组当中
                    $arrTagItems[]=$item;
                }
                if(count($item->mArrChild))
                {
                    //如果子节点存在 则添加到同层子节点中
                    $arrComtemporaryChild=  array_merge($arrComtemporaryChild,$item->mArrChild);
                }
            }
            //该层次已经查询完毕 开始查询下一个层次的
            $arrItems=$arrComtemporaryChild;
        }
        return new HtmlItemIterator($arrTagItems);
    }
    
    /**
     *  通过索引返回包含对应的item的iterator
     * @param type $index
     */
    public function fn_find_item_by_index($index)
    {
        if($index<0 || $index >= count($this->mArrItems))
        {
            return new HtmlItemIterator(array());
        }
        return new HtmlItemIterator(array($this->mArrItems[$index]));
    }
    
    /**
     *  获取$index对应的item里面的 innerHtml
     * @param type $index
     * @return string
     */
    public function fn_get_inner_html($index=0)
    {
        if($index<0 ||  $index>=count($this->mArrItems))
        {
            return "";
        }
        return $this->mArrItems[$index]->mStrInnerHtml;
    }
    
    /**
     *  iterator 包含的item数
     * @return type
     */
    public function fn_size()
    {
        return count($mArrItems);
    }
}

?>
