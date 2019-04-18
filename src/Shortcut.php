<?php
namespace EZAMA

/**
*
* @Name : Shortcut
* @Version : 1.0.0
* @Programmer : Akpé Aurelle Emmanuel Moïse Zinsou
* @Date : 2019-04-01
* @Released under : https://github.com/manuwhat/Shortcut/blob/master/LICENSE
* @Repository : https://github.com/manuwhat/Shortcut
*
**/
{

    class Shortcut
    {
        const VALID_PHP_FUNCTION_NAME_PATTERN='#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#';
        const CAN_NEVER_EVER_CHOOSE_THIS_AS_FUNCTION_NAME="new";
        const PLACEHOLDER_FOR_INTERNALS_CLASSES_OPTIONALS_PARAMETERS="acce91966cd8eee995ee1ac30c98c3d89d8f9235";
        private static $DIR=null;
        private static $SHORTCUT_FOR_ALL=false;
        
        public static function SetShortcutForAll($bool)
        {
            self::$SHORTCUT_FOR_ALL=(bool)$bool;
        }
        public static function create($classname, $name=self::CAN_NEVER_EVER_CHOOSE_THIS_AS_FUNCTION_NAME)
        {
            if (is_string($classname)&&class_exists($classname, true)) {
                $reflectionClass=new \reflectionClass($classname);
                $classname=$reflectionClass->getName();
                $fullQualifiedClassname=str_replace('\\', '_', $classname);
                self::getTheRightDir($file, $Dir, $fullQualifiedClassname);
                $fileExists=file_exists($file);
                if (!function_exists($classname)&&!function_exists($name)) {
                    if (!$fileExists) {
                        $private_scope=false;
                        $name=trim($name);
                        self::createDir($Dir);
                        $reflectionMethod=$reflectionClass->getConstructor();
                        $notInstantiable=false;
                        if (is_null($reflectionMethod)||$notInstantiable=!$reflectionClass->isInstantiable()) {
                            self::HandleNotInstantiableAndHasNoConstructor($Shortcut, $fullQualifiedClassname, $name, $notInstantiable, $classname);
                            if ($Shortcut) {
                                return self::pushAndShow($file, $Shortcut);
                            }
                            $private_scope=true;
                        }
                        
                        self::getSignature($reflectionMethod, $signature, $parameters, $paramsNum, $count);
                        
                        $hasInternal='';
                        if ($count) {
                            self::BuildTheSwitch($hasInternal, $count, $paramsNum, $parameters, $classname);
                        }
                        self::useTheRightNameAndScope($Shortcut, $name, $fullQualifiedClassname, $signature, $private_scope, $classname);
                        
                        self::handleInternals($Shortcut, $hasInternal, $parameters, $signature, $classname);
                            
                        return self::pushAndShow($file, $Shortcut);
                    } else {
                        return include_once($file);
                    }
                } else {
                    self::GetTheRightExceptionMessage($fileExists, $name, $fullQualifiedClassname);
                }
            }
        }

        private static function getSignature(\ReflectionMethod $method, &$signature, &$parameters, &$paramsNum, &$count)
        {
            $params=$method->getParameters();
            $paramsNum=count($params);
            $signature='';
            $parameters=array();
            $count=0;
            foreach ($params as $k=>$param) {
                if ($param->isPassedByReference()) {
                    $tmp='&$'.$param->getName();
                } else {
                    $tmp='$'.$param->getName();
                }

                if ($param->isOptional()) {
                    $count++;
                    if ($method->isInternal()) {
                        $tmp.='="acce91966cd8eee995ee1ac30c98c3d89d8f9235"';
                    } elseif ($param->isDefaultValueConstant()) {
                        $tmp.='='.$param->getDefaultValueConstantName();
                    } elseif ($param->isDefaultValueAvailable()) {
                        $tmp.='='.var_export($param->getDefaultValue(), true);
                    } elseif ($param->allowsNull()) {
                        $tmp.='=null';
                    }
                }
                
                $signature.=$tmp;
                $parameters[]='$'.$param->getName();
                $tmp='';
                if ($k<$paramsNum-1) {
                    $signature.=',';
                }
            }
        }
        private static function BuildTheSwitch(&$hasInternal, $count, $paramsNum, $parameters, $classname)
        {
            $hasInternal.='switch($count){';
            while ($count>0) {
                $hasInternal.="case $count:return new $classname(".join(',', array_slice($parameters, 0, $paramsNum-$count))."); break;";
                $count--;
            }
            $hasInternal.='default:return new '.$classname.'('.join(',', $parameters).');break;}';
        }
        
        private static function useTheRightNameAndScope(&$Shortcut, $name, $fullQualifiedClassname, $signature, $scope, $classname)
        {
            if (strtolower($name)!=='new'&&preg_match(self::VALID_PHP_FUNCTION_NAME_PATTERN, $name)) {
                $Shortcut="<?php
							function $name($signature){";
                if ($scope) {
                    $Shortcut.="if(".'@get_class()'."!==$classname){
									throw new scopeException(\"Shortcut function $name can only be called in class $classname scope\");
								}";
                }
            } else {
                $Shortcut="<?php
							function $fullQualifiedClassname($signature){";
                if ($scope) {
                    $Shortcut.="if(@get_class()!==\"$classname\"){
							throw new scopeException(\"Shortcut function $fullQualifiedClassname can only be called in class $classname scope\");
						}";
                }
            }
        }
        
        private static function handleInternals(&$Shortcut, $hasInternal, $parameters, $signature, $classname)
        {
            if (!strpos($signature, "acce91966cd8eee995ee1ac30c98c3d89d8f9235")) {
                $Shortcut.="return new $classname(".join(',', $parameters).");
							}";
            } else {
                $Shortcut.='
							$count=count(array_keys(get_defined_vars(),"'.self::PLACEHOLDER_FOR_INTERNALS_CLASSES_OPTIONALS_PARAMETERS.'"));
							'.$hasInternal.'
							}';
            }
        }
        
        private static function pushAndShow($file, $Shortcut)
        {
            file_put_contents($file, str_replace("\t", '    ', $Shortcut));
            file_put_contents($file, php_strip_whitespace($file)); //just for cleanliness of the generated code
            return include_once($file);
        }
        
        private static function GetTheRightExceptionMessage($fileExists, $name, $fullQualifiedClassname)
        {
            if (!$fileExists) {
                if (strtolower($name)!=='new'&&preg_match(self::VALID_PHP_FUNCTION_NAME_PATTERN, $name)) {
                    throw new \InvalidArgumentException('function '.$name.' passed as second Argument already exists.
					Can\'t create a shortcut with the same name');
                } else {
                    throw new \InvalidArgumentException('function '.$fullQualifiedClassname.' already exists and An alias has not been provided as Argument 2.
					Can\'t create a shortcut function with this name');
                }
            }
        }
        
        private static function HandleNotInstantiableAndHasNoConstructor(&$Shortcut, $fullQualifiedClassname, $name, $notInstantiable, $classname)
        {
            if ($notInstantiable) {
                if (!self::$SHORTCUT_FOR_ALL) {
                    throw new \InvalidArgumentException('Not Instantiable class '.$fullQualifiedClassname.' passed as Argument');
                }
            } else {
                self::useTheRightNameAndScope($Shortcut, $name, $fullQualifiedClassname, '', false, $classname);
                $Shortcut.="return new $classname();
						}";
                // return self::pushAndShow($file, $Shortcut);
            }
        }
        
        private static function getTheRightDir(&$file, &$Dir, $fullQualifiedClassname)
        {
            if ($Dir=self::$DIR) {
                $file=self::$DIR.DIRECTORY_SEPARATOR.$fullQualifiedClassname.".Shortcut.php";
            } else {
                $Dir=dirname(__DIR__).DIRECTORY_SEPARATOR.'ClassShortcuts';
                $file=$Dir.DIRECTORY_SEPARATOR.$fullQualifiedClassname.".Shortcut.php";
            }
        }
        
        private static function createDir($Dir)
        {
            if (!file_exists($Dir)) {
                mkdir($Dir);
            }
        }
        
        
        
        public static function setDir($dirname)
        {
            if (is_dir($dirname)&&is_writable($dirname)&&!self::$DIR) {
                self::$DIR=$dirname;
            }
        }
        
        
        private function __construct()
        {
        }
    }
    
    
}

namespace{
    function create_Shortcut($classname, $name='new')
    {
        return EZAMA\Shortcut::create($classname, $name);
    }
    class scopeException extends \Exception
    {
    }
}
