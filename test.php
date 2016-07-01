<?php
    //加载php文件
    include_once("MHtmlDomMaster/MHtmlDomMaster.php");
    $strHtml=file_get_contents("test.html");
    /*
       $strHtml的值等于下面的Html代码，这段Html代码只是用来讲解用
        <html>
            <head>
            </head>
            <body>
                    <div id="div1">
                            <div class="class1">
                                    <span> span1 </span>
                                    <span> span2 </span>
                            </div>
                            <div class="class2">
                                    div class2
                            </div>
                    </div>
                    <div id="div2">
                    </div>
            </body>
        </html>
     */
    /*
     * 生成Dom树  
     */
    $objDomMaster=new MHtmlDomMaster($strHtml);
    /*
     *  通过ID查找Dom节点  
     *  返回的是包含符合条件的Dom节点的HtmlItemIterator类  
     *  如果没找到 则HtmlItemIterator->fn_size()==0
     */
    $objIteratorDiv=$objDomMaster->fn_find_item_by_id('div1');
    /**
     *  获取HtmlItemIterator包含的HtmlItem中的第0个
     *  如果Iterator没有包含HtmlItem，则返回NULL
     */
    $objHtmlItemDiv=$objIteratorDiv->fn_get_item();
    /**
     * HtmlItem 里面的innerHtml
     */
    $strInnerHtml=$objHtmlItemDiv->mStrInnerHtml;
    /**
     *   下面输出的结果是
        <div class="class1">
            <span> span1 </span>
            <span> span2 </span>
        </div>
        <div class="class2">
                div class2
        </div>
     */
    echo $strInnerHtml;
    /**
     * 通过class查找Dom节点
     */
    $objIteratorDiv=$objDomMaster->fn_find_item_by_class("class1");
    /*
       下面输出结果是
        <span> span1 </span>
        <span> span2 </span>
     */
    echo $objIteratorDiv->fn_get_item()->mStrInnerHtml;
    /**
     *  通过标签名查找Dom节点 
     *  标签名不区分大小写
     */
    $objIteratorDiv=$objDomMaster->fn_find_item_by_tag("div");
    /**
     *  支持链式查询
     *  fn_find_item_by_tag("div")的查询是从上一个fn_find_item_by_class("class1")的查询结果所返回的HtmlItemIterator所包含的HtmlItem中查询
     */
    $objIteratorSpan=$objDomMaster->fn_find_item_by_class("class1")->fn_find_item_by_tag("span");
    /*
     *  获取HtmlItemIterator中所包含的HtmlItem的第1个(从0开始算)
     */
    $objItemSpan=$objIteratorSpan->fn_get_item(1);
    /*
      下面的输出结果是
      span2 
     */
    echo $objItemSpan->mStrInnerHtml;
?>