<?php
namespace EZAMA

/**
*
* @Name : Shortcut
* @Programmer : Akpé Aurelle Emmanuel Moïse Zinsou
* @Date : 2019-04-01
* @Released under : https://github.com/manuwhat/Shortcut/blob/master/LICENSE
* @Repository : https://github.com/manuwhat/Shortcut
*
**/
{

    
    class shortcutQueryBuilder
    {
        protected static function getSignature(\ReflectionMethod $method, &$signature, &$parameters, &$paramsNum, &$count)
        {
            $params=$method->getParameters();
            $paramsNum=count($params);
            $signature='';
            $parameters=array();
            $count=0;
            foreach ($params as $k=>$param) {
                self::getParameterDeclaration($param, $tmp, $count, $method);
                $signature.=$tmp;
                $parameters[]='$'.$param->getName();
                $tmp='';
                if ($k<$paramsNum-1) {
                    $signature.=',';
                }
            }
        }
        
        protected static function getParameterDeclaration(\ReflectionParameter $param, &$tmp, &$count, $method)
        {
            $tmp=$param->isPassedByReference()?'&$'.$param->getName():'$'.$param->getName();
            if ($param->isOptional()) {
                $count++;
                if ($method->isInternal()) {
                    $tmp.='="acce91966cd8eee995ee1ac30c98c3d89d8f9235"';
                } else {
                    self::handleOptionalParameter($param, $tmp);
                }
            }
        }
        
        protected static function handleOptionalParameter(\reflectionParameter $param, &$tmp)
        {
            if ($param->isDefaultValueConstant()) {
                $tmp.='='.$param->getDefaultValueConstantName();
            } elseif ($param->isDefaultValueAvailable()) {
                $tmp.='='.var_export($param->getDefaultValue(), true);
            } elseif ($param->allowsNull()) {
                $tmp.='=null';
            }
        }
        
        protected static function BuildTheSwitch(&$hasInternal, $count, $paramsNum, $parameters, $classname)
        {
            $hasInternal.='switch($count){';
            while ($count>0) {
                $hasInternal.="case $count:return new $classname(".join(',', array_slice($parameters, 0, $paramsNum-$count))."); break;";
                $count--;
            }
            $hasInternal.='default:return new '.$classname.'('.join(',', $parameters).');break;}';
        }
        
        
        protected static function _init($classname)
        {
            return [
                'reflectionClass'=>$tmp=new \ReflectionClass($classname),
                'classname'=>$tmp->getName(),
                'fullQualifiedClassname'=>str_replace('\\', '_', $classname),
            ];
        }
        
        protected static function forwardInit($name, $Dir, \ReflectionClass $reflectionClass)
        {
            self::createDir($Dir);
            return [
                'private_scope'=>false,
                'name'=>trim($name),
                'reflectionMethod'=>$reflectionClass->getConstructor(),
                'notInstantiable'=>false,
            ];
        }
        
        private static function createDir($Dir)
        {
            if (!file_exists($Dir)) {
                mkdir($Dir);
            }
        }
        
        
        private function __construct()
        {
        }
    }
    
}
