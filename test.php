<?php
//    include_once 'bootstrap.php';
    require_once 'PHPUnit.php';
    /* подключаем phpUnit и файл с тестируемым классом */
//    require_once('Document.php');

class ExampleTest extends PHPUnit_TestCase {
    public function testOne()
    {
        $this->assertTrue(FALSE);
    }
}
# run the test
$suite = new PHPUnit_Framework_TestSuite('ExampleTest');
PHPUnit_TextUI_TestRunner::run($suite);

