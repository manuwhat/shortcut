<?php
namespace{
require dirname(__DIR__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Shortcut.php';
use PHPUnit\Framework\TestCase;

class unreal
{
    public function __construct()
    {
    }
}

class unreal2
{
}

class cannotBeInstantiated
{
    private function __construct()
    {
    }
}

class ShortcutTest extends TestCase
{
    public function testCreate()
    {
        Ezama\Shortcut::setDir('./');
        // Ezama\Shortcut::setShortcutForAll(true);
        create_Shortcut('ArrayObject');
        $this->assertInstanceof('ArrayObject', arrayObject());
        create_Shortcut('SplFileObject');
        $this->assertInstanceof('SplFileObject', SplFileObject(__FILE__));
        create_Shortcut('ArrayIterator');
        $this->assertInstanceof('ArrayIterator', ArrayIterator([1,2,3]));
        create_Shortcut('unreal');
        $this->assertInstanceof('unreal', unreal());
        create_Shortcut('unreal2');
        $this->assertInstanceof('unreal2', unreal2());
        create_Shortcut('DateTime', 'DT');
        $this->assertInstanceof('Datetime', dt());
        create_Shortcut('ForTestOnly\data');
        $this->assertInstanceof('ForTestOnly\data', ForTestOnly_data());
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Not Instantiable class cannotBeInstantiated passed as Argument');
        create_Shortcut('cannotBeInstantiated');
        $this->assertFalse(function_exists('cannotBeInstantiated'));
        cannotBeInstantiated();
    }
}
}

namespace ForTestOnly{
    class data
    {
    }
    
}
