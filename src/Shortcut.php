<?php
namespace EZAMA

/**
*
* @Name : Shortcut
* @Version : 1.0.0
* @Programmer : Akpé Aurelle Emmanuel Moïse Zinsou
* @Date : 2019-04-01
* @Released under : https://github.com/EZAMA/Shortcut/blob/master/LICENSE
* @Repository : https://github.com/EZAMA/Shortcut
*
**/
{

    class Shortcut
    {
        const VALID_PHP_FUNCTION_NAME_PATTERN = '#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#';
        const CAN_NEVER_EVER_CHOOSE_THIS_AS_FUNCTION_NAME= "new";
        const PLACEHOLDER_FOR_INTERNALS_CLASSES_OPTIONALS_PARAMETERS ="This is internal and thus sucks we must do something ClassShortcutDesigner";
        private static $DIR=null;
        
        public static function create($classname, $name=self::CAN_NEVER_EVER_CHOOSE_THIS_AS_FUNCTION_NAME)
        {
            if (is_string($classname)&&class_exists($classname, true)) {
                $reflectionClass=new \reflectionClass($classname);
                $classname=$reflectionClass->getName();
                $fullQualifiedClassname=str_replace('\\', '_', $classname);
                if ($Dir=self::$DIR) {
                    $file=self::$DIR.DIRECTORY_SEPARATOR.$fullQualifiedClassname.".Shortcut.php";
                } else {
                    $Dir=dirname(__DIR__).DIRECTORY_SEPARATOR.'ClassShortcuts';
                    $file=$Dir.DIRECTORY_SEPARATOR.'ClassShortcuts'.DIRECTORY_SEPARATOR.$fullQualifiedClassname.".Shortcut.php";
                }
                $fileExists=file_exists($file);
                if (!function_exists($classname)&&!function_exists($name)) {
                    if (!$fileExists) {
                        $name=trim($name);
                        if (!file_exists($Dir)) {
                            mkdir($Dir);
                        }
                        $reflectionMethod=$reflectionClass->getConstructor();
                        $notInstantiable=false;
                        if (is_null($reflectionMethod)||$notInstantiable=!$reflectionClass->isInstantiable()) {
                            if ($notInstantiable) {
                                //return;
                                throw new \InvalidArgumentException('Not Instantiable class '.$fullQualifiedClassname.' passed as Argument');
                            } else {
                                if (strtolower($name)!=='new'&&preg_match(self::VALID_PHP_FUNCTION_NAME_PATTERN, $name)) {
                                    $Shortcut="<?php
												function $name(){";
                                } else {
                                    $Shortcut="<?php
												function $fullQualifiedClassname(){";
                                }
                                $Shortcut.="return new $classname();
                                        }";
                                file_put_contents($file, str_replace("\t", '    ', $Shortcut));
                                file_put_contents($file, php_strip_whitespace($file));//just for cleanliness of the generated code
                                return include_once($file);
                            }
                        }
                        self::getSignature($reflectionMethod, $signature, $parameters, $paramsNum, $count);
                        
                        $hasInternal='';
                        if ($count) {
                            $hasInternal.='switch($count){';
                            while ($count>0) {
                                $hasInternal.="case $count:return new $classname(".join(',', array_slice($parameters, 0, $paramsNum-$count))."); break;";
                                $count--;
                            }
                            $hasInternal.='default:return new '. $classname.'('.join(',', $parameters).');break;}';
                        }
                        if (strtolower($name)!=='new'&&preg_match(self::VALID_PHP_FUNCTION_NAME_PATTERN, $name)) {
                            $Shortcut="<?php
                                        function $name($signature){";
                        } else {
                            $Shortcut="<?php
                                        function $fullQualifiedClassname($signature){";
                        }
                        if (!strpos($signature, "This is internal and thus sucks we must do something ClassShortcutDesigner")) {
                            $Shortcut.="return new $classname(".join(',', $parameters).");
                                        }";
                        } else {
                            $Shortcut.='
                                        $count=count(array_keys(get_defined_vars(),"'.self::PLACEHOLDER_FOR_INTERNALS_CLASSES_OPTIONALS_PARAMETERS.'"));
                                        '.$hasInternal.'
                                        }';
                        }
                            
                        file_put_contents($file, str_replace("\t", '    ', $Shortcut));
                        file_put_contents($file, php_strip_whitespace($file));//just for cleanliness of the generated code
                        return include_once($file);
                    } else {
                        return include_once($file);
                    }
                } else {
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
                        $tmp.='="This is internal and thus sucks we must do something ClassShortcutDesigner"';
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
}
