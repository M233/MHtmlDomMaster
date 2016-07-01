<?php
    class DomTreeShow
    {
        public static $CLR="\n";
        public static function show($domTree)
        {
            $size=count($domTree);
            $str="";
            for($i=0;$i<$size;$i++)
            {
                $str .=self::fn_print_item($domTree[$i], 0);
            }
            echo $str;
        }
        public static function fn_print_item($obj,$n)
        {
            $strSpace=  self::fn_get_space($n);
            if($obj instanceof HtmlItem)
            {
                $str=$strSpace."<".$obj->mStrTagName.self::fn_print_attr($obj->mArrAttribute).">".self::$CLR;
                if(strpos($obj->mStrTagName, "script")!==false)
                {
                    $str .=$obj->mStrInnerHtml.self::$CLR;
                }
                else
                {
                    $size=count($obj->mArrChild);
                    for($i=0;$i<$size;++$i)
                    {
                        $childItem=$obj->mArrChild[$i];
                        $str .=self::fn_print_item($childItem, $n+1);
                    }
                }
                $str .=$strSpace."</".$obj->mStrTagName.">".self::$CLR;
                return $str;
            }
            else if($obj instanceof HtmlTextItem)
            {
                return self::fn_get_space($n)."HtmlTextItem ".$obj->mStrInnerHtml.self::$CLR;
            }
        }
        
        public static function fn_get_space($n)
        {
            $str="";
            for($i=0;$i<$n;$i++)
            {
                $str .="  ";
            }
            return $str;
        }
        public static function fn_print_attr($attr)
        {
            $str=" ";
            foreach( $attr as $key=>$value)
            {
                $str .=$key."= \"".$value."\"  ";
            }
            return $str;
        }
    }
?>
