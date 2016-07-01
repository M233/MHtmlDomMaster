<?php
    include_once("HtmlAttrParser.php");
    include_once("HtmlItem.php");
    include_once("HtmlTextItem.php");
    include_once("HtmlNoFindEndTagException.php");
    include_once("HtmlDomMaster.php");
   
    class HtmlParser
    {
        //把标签转为正则 html标签的开始部分  标志位  如 <div id="123" >
        const  PREG_TO_START=0;
        //把标签转为正则 html标签的结束部分  标志位  如 </div>
        const PREG_TO_END=1;
        //单例模式
        private static $mInstance;
        //属性解析器
        private $mObjAttrParser;
        //自封闭标签
        private $mArrShutTags;
        //标签的正则
        private $mStrRegTag;
        //不需要解析的innerHtml的标签
        private $mArrTagNoContinueParseInerrHtml;
        private function __construct()
        {
            // 单例模式
            $this->mObjAttrParser=new HtmlAttrParser();
            $this->mArrShutTags=array("br","hr","col","img",
                                      "area","base","link","meta",
                                      "frame","input","param","isindex",
                                      "basefont");
            $this->mStrRegTag='/(?P<start><\s*)'                                         // < 开始位置
                            . '(?P<tag>[a-zA-Z][a-zA-Z0-9]*)'                       //标签名
                            . '(?P<attr>\s+\w+\s*=\s*(?P<yh>["\']).*?\k<yh>)*'         //属性
                            . '\s*(?P<end_item_identify>\/?)'                       //结束标签标志
                            . '\s*(?P<end>>)/';         
            $this->mArrTagNoContinueParseInerrHtml=array("script","style","code");
        }
        //获取单例
        public static function fn_get_instance()
        {
            if(!(self::$mInstance instanceof HtmlParse))
            {
                //初始化
                self::$mInstance=new HtmlParser();
            }
            return self::$mInstance;
        }
        public  function fn_parse_html($html,$htmlDomMaster=null)
        {
            $arrItems=array();
            while(1)
            {
                $n=preg_match($this->mStrRegTag, $html,$arrRegResult,PREG_OFFSET_CAPTURE,0);
                if($n==0)
                {
                    //  $html里面没有新的html标签  把所有节点都变为文本节点
                    $arrItems[]=HtmlTextItem::builder()->setInnerHtml($html);
                    break;
                }
                else
                { 
                    /**
                     * 存在新的标签
                     */
                    if($arrRegResult[0][1]!=0)
                    {
                        //在标签前面 存在文本
                        $strTemp=trim(substr($html, 0,$arrRegResult[0][1]));
                        if($strTemp!="")
                        {
                            //不等于空字符串的时候 添加为文本节点
                            $arrItems[]=  HtmlTextItem::builder()->setInnerHtml($strTemp);
                        }
                    }
                    //标签名  标签小写
                    $strTagName=  strtolower($arrRegResult['tag'][0]);
                    //是否存在结束标签
                    $isHaveEndIdentify=false;
                    /*
                     *  获取属性
                     */
                    //属性开始的索引
                    $nAttrStartPosition=$arrRegResult['tag'][1]+ strlen($strTagName);
                    if(empty($arrRegResult['end_item_identify'][0]))
                    {
                        //没有结束标签
                        //属性结束的索引
                        $nAttrEndPosition=$arrRegResult['end'][1];
                    }
                    else
                    {
                        //有结束标签
                        $nAttrEndPosition=$arrRegResult['end_item_identify'][1];
                        $isHaveEndIdentify=true;
                    }
                    //属性的字符串
                    $strAttr=  substr($html,$nAttrStartPosition ,max(array($nAttrEndPosition-$nAttrStartPosition,0)));
                    //属性数组
                    $arrAttr=  $this->mObjAttrParser->fn_parse_attribute($strAttr);
                    if($isHaveEndIdentify)
                    {
                        //如果有结束标签
                        $nTagEndPos=$arrRegResult['end'][1]+1;
                        $strInnerHtml="";
                    }
                    else
                    {
                        //如果没有结束标签
                        /*
                        *  获取innerHtml
                        */
                       try
                       {
                            $strTempHtml=substr($html,$arrRegResult['end'][1]+1);
                            $arr= $this-> fn_get_tag_inner_html_and_end_pos($strTagName, $strTempHtml);
                            $nTagEndPos=$arr['tagEndPos']+$arrRegResult['end'][1]+1;
                            $strInnerHtml=trim($arr['innerHtml']);
                            $stra3111=  substr($strInnerHtml, strlen($strInnerHtml)-100,100);
                       } catch (Exception $ex) 
                       {
                            if($ex instanceof HtmlNoFindEndtagException)
                            {
                                //没有找到结束标签
                                if(!in_array(strtolower($ex->mTagName),$this->mArrShutTags))
                                {
                                    //如果不是自封闭标签  则报错
                                    var_dump("tag ".$ex->mTagName."  ".$ex->getMessage()."  \n   error code ".$ex->getCode());
                                }
                            }
                            else
                            {
                                var_dump($ex->getMessage()."  \n   error code ".$ex->getCode());
                            }
                            //解析错误
                            $nTagEndPos=$arrRegResult['end'][1]+1;
                            $strInnerHtml="";
                       }
                    }
                    $htmlItem=new HtmlItem($strTagName, $strInnerHtml, $arrAttr,array(), null);
                    //把HtmlItem添加到映射表中
                    $this->fn_add_to_map($htmlItem, $arrAttr, $htmlDomMaster);
                    
                    if($strInnerHtml!="" && $this->fn_is_continue_parse_inner_html($strTagName))
                    {
                        //继续递归解析 解析innerHtml
                        $arrChilditems=$this->fn_parse_html($strInnerHtml,$htmlDomMaster);
                        $size=  count($arrChilditems);
                        for($i=0;$i<$size;++$i)
                        {
                            $childHtmlItem=$arrChilditems[$i];
                            $childHtmlItem->mObjParent=$htmlItem;
                        }
                        $htmlItem->mArrChild=$arrChilditems;
                    }
                    //把解析好的元素添加到item数组中
                    $arrItems[]=$htmlItem;
                    //继续解析
                    $html=  substr($html, $nTagEndPos);
                    if($html==false || trim($html)=="")
                    {
                        //截取字符串错误 或者剩余的html为空字符串 则跳出循环
                        break;
                    }
                }
            }
             return $arrItems;
        }
        /**
        *  获取标签里面的内容 和标签的结束位置  
        * @param type $strTagName  标签名
        * @param type $strHtml     html代码
        * @return Array           返回结果 结果是一个数组  结构是这样的 
         *                         array( "innerHtml"=>"html代码","tagEndPos"=>n)  n为该标签结束位置
        * @throws Exception        抛出 tag转换reg的错误  和 没有找到结束标签的错误（html不是标准的html代码）  
        */
       function fn_get_tag_inner_html_and_end_pos($strTagName,$strHtml)
       {
           //标签的结束部分
           $strRegItemEndTag=  $this->fn_convert_tag_to_reg($strTagName, self::PREG_TO_END);
           if($strRegItemEndTag=="")
           {
               //转换标签到正则错误 
               throw new Exception("convert tag to reg error ".$strTagName, 2);
           }
           $n=  preg_match($strRegItemEndTag, $strHtml,$arrResultReg,PREG_OFFSET_CAPTURE);
           if($n==0)
           {
               //没有找到 标签结束部分
               throw new HtmlNoFindEndtagException("no find end tag  ".$strHtml, 3,$strTagName);
           }
           //第一个结束标签的位置
           $nTagEndPos=$arrResultReg[0][1];
           //第一个结束标签的长度
           $nTagEndLength=  strlen($arrResultReg[0][0]);
           /*
            * 查询在第一个结束标签前 存在多少个 相同的标签头
            */

           // 获取在第一个结束标签前的字符串  
           $str1=  substr($strHtml,0,$nTagEndPos);
           
           //多少个相同的标签头
           $nCountSampleTag= $this-> fn_count_tag_start_num($strTagName, $str1);
        
           if($nCountSampleTag==0)
           {
               return array("innerHtml"=>substr($strHtml, 0,$nTagEndPos),"tagEndPos"=>($nTagEndPos+$nTagEndLength));
           }
           //下面查找结束便签的 开始位置
            $nOffset=$nTagEndPos+$nTagEndLength;
            $nLastPosition=$nOffset;
            $nStart=$nOffset;
            do
            {
                 for($i=0;$i<$nCountSampleTag;++$i)
                 {
                      $n=  preg_match($strRegItemEndTag, $strHtml,$arrResultReg,PREG_OFFSET_CAPTURE,$nOffset);
                      if($n==0)
                      {
                            throw new HtmlNoFindEndtagException("no find end tag2  ".$strHtml, 3,$strTagName);
                      }
                     
                     $nOffset=$arrResultReg[0][1]+strlen($arrResultReg[0][0]);
                     $nLastPosition=$arrResultReg[0][1];
                 }
                 //再判断 刚刚搜索的字符串中有没有标签头  
                $strTempHtml=substr($strHtml, $nStart,$nLastPosition-$nStart);
                $nCountSampleTag=$this->fn_count_tag_start_num($strTagName,$strTempHtml);
                $nStart=$nOffset;
               
                
                 
            }while($nCountSampleTag!=0);
           
            return array("innerHtml"=>substr($strHtml, 0,$nLastPosition),"tagEndPos"=>($nOffset));
       }
       
        /**
        *  判断 在第一个结束标签前 存在多少个标签头
        * @param type $strTagName  标签名
        * @param type $strHtml     html代码
        * @return type
        */
       function fn_count_tag_start_num($strTagName,$strHtml)
       {
           $ss='检波器的电压灵敏度';
           if(strpos($strHtml,$ss)!==false)
           {
               $kkkk=1;
           }
           $regItemStartTag=  $this->fn_convert_tag_to_reg($strTagName, self::PREG_TO_START,'(?P<end_tag_identify>\/)?');
           if($regItemStartTag=="")
           {
               //转换标签到正则错误 
               throw new Exception("convert tag to reg error ".$strTagName, 1);
           }
           //搜索开始的位置
           $nStart=0;
           //多少个相对标签头
           $nCount=0;
           $nLength= strlen($strHtml);
           do
           {
               $n=preg_match($regItemStartTag,$strHtml,$arrResultReg,PREG_OFFSET_CAPTURE,$nStart);
               if($n==0)
               {   
                   //没有找到标签
                   break;
               }
               if(isset($arrResultReg['end_tag_identify']) && !empty($arrResultReg['end_tag_identify']))
               {
                   //存在结束标签 例如 闭合元素( <div/> )存在 跳过 
               }
               else
               {
                   $nCount++;
               }
               $nStart=$arrResultReg[0][1]+strlen($arrResultReg[0][0]);
           }while($nStart!=$nLength);
           return $nCount;
       }
       /**
         * 
         * @param type $strTagName
         * @param type $nModel
         * @param type $strExtra    额外添加的部分
         * @return string
         */
        private function fn_convert_tag_to_reg($strTagName,$nModel,$strExtra="")
        {
            $strTagName=  strtolower($strTagName);
            //标签的处理
            $strRegTag='';
            //拼接正则表达式的标签名部分 比如 div 拼接成 [Dd][Ii][Vv]的
            //for($i=0;$i<  MUtil::abslength($strTagName);++$i)
            for($i=0;$i<  strlen($strTagName);++$i)
            {
                $char=$strTagName[$i];
                if($char!=  strtoupper($char))
                {
                    $strRegTag .=('['.$char.strtoupper($char).']');
                }
                else
                {
                    $strRegTag .=$char;
                }
            }
            if($nModel==  self::PREG_TO_START)
            {
                //转换为开始标签
                $strRegTag='/<\s*'
                                  .$strRegTag                       //标签
                                  . '.*?'                           //属性
                                  .$strExtra                        //额外的匹配规则    
                                  //. '\s*>/';
                                  . '>/';
            }
            else if($nModel==  self::PREG_TO_END)
            {
                //转换为结束标签
               $strRegTag='/<\s*\/\s*'
                        .$strRegTag
                        .'.*?>/';
            }
            return $strRegTag;
        }
        
        /**
         *  是否继续解析 innerHtml
         * @param type $strTagName
         * @return boolean
         */
        function fn_is_continue_parse_inner_html($strTagName)
        {
            $strTagName=  strtolower($strTagName);
            if(in_array($strTagName, $this->mArrTagNoContinueParseInerrHtml))
            {
                return false;
            }
            return true;
        }
        
        /**
         *  把HtmlItem添加到HtmlDomMaster的映射表中
         * @param type $htmlItem
         * @param type $arrAttr
         * @param type $htmlDomMaster
         * @return type
         */
        function fn_add_to_map($htmlItem,$arrAttr,$htmlDomMaster)
        {
            if(!($htmlDomMaster instanceof HtmlDomMaster))
            {
                return;
            }
            if(isset($arrAttr['id']))
            {
                //添加id 的映射表中
                $htmlDomMaster->fn_add_id_item($arrAttr['id'],$htmlItem);
            }
            if(isset($arrAttr['class']))
            {
                //添加到class 映射表中
                $arrClass=preg_split('/\s+/',$arrAttr['class']);
                foreach($arrClass as $class)
                {
                    if($class!='')
                    {
                        $htmlDomMaster->fn_add_class_item($class,$htmlItem);
                    }
                }
            }
        }
    }
?>


