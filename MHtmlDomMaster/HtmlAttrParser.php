<?php
    class HtmlAttrParser
    {
        //正则解析的字符串
        public $mAttrReg="123";
        public function __construct()
        {
            $this->mAttrReg='/'
                            . '((?:(?P<yh>["\'])).*?(?:\k<yh>))'           //匹配引号开始的 字符串 " 123" 或者 '123' 
                            . '|='                                         //匹配等号
                            . '|((?<=\b)[\w-:]+(?=\b))'                        //匹配一般字符串
                            . '/';
        }
        /**
         *  解析 html dom元素的 属性字符串
         *  解析模版  ' aA1 = "a6a2  87aa" Bb3 data="地狱"  cC=Cc  d5="D\'7" 2="qu" data="天堂 "   "aa '
         * @param type $str     属性字符串
         * @return Array        返回属性数组 已经解析好的
         */
        function fn_parse_attribute($str)
        {
            
            preg_match_all($this->mAttrReg, $str,$arrResult);

            $arrTemp=$arrResult[0];
            //存储属性的数组
            $arrAttrs=array();
            //缓存字符串的 栈
            $arrStack=array();
            while(count($arrTemp))
            {
                //获取队列的第一个item 去除数据前后的空格
                $str=trim(array_shift($arrTemp));
                if($str=="=")
                {
                    //如果 栈中没有数据 或者临时数组中已经没有数据了
                    if(count($arrStack)==0 || count($arrTemp)==0)
                    {
                        //抛弃这个 = 
                        continue;
                    }
                    //属性全部小写
                    $key=  strtolower(array_pop($arrStack));
                    $value=  array_shift($arrTemp);
                    //去除 属性左右两边的引号
                    $value=trim( preg_replace('/(^\s*["\'])|(["\']\s*$)/', "",$value));
                    if(!isset($arrAttrs[$key]))
                    {
                        //如果不存在 这个属性则设置 
                        //如果已经存在这个属性 则抛弃
                        $arrAttrs[$key]=$value;
                    }
                }
                else
                {
                    //把字符串添加到栈当中
                    $arrStack[]=$str;
                }
            }
            //把栈多余的字符串添加到属性中,属性值为空字符串  例如 'aa'=''
            while(count($arrStack))
            {
                //属性全部小写
                $key=strtolower(array_pop($arrStack));
                if(!isset($arrAttrs[$key]))
                {
                    $arrAttrs[$key]="";
                }
            }
            //处理完成  返回属性数组
            return $arrAttrs;
        }
    }
?>
