<?php
//    include_once 'bootstrap.php';
require_once '/usr/share/php/PHPUnit/Autoload.php';
/* подключаем phpUnit и файл с тестируемым классом */
require_once('Document.php');

class DriverPluginDocumentTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        //        $_FILES = array(
//            'file_valid' => array(
//                'uploadPicture' => 'foo.jpg',
//                'name'     => 'foo.txt',
//                'tmp_name' => '/tmp/php42up23',
//                'type'     => 'text/plain',
//                'size'     => 42,
//                'error'    => 0
//            )
//        );
        parent::setUp();
    }

    public function testCreatingClass()
    {
        $driverPluginDocument = new Driver_Plugin_Document();
        $this->assertNotNull($driverPluginDocument);
    }

    public function testClassHasAttribute()
    {
        $this->assertClassHasAttribute('_scanPdfDir', 'Driver_Plugin_Document');
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Nothing to upload
     */
    public function testUploadDocumentMethodNothingToUploadException()
    {
        $driverPluginDocument = new Driver_Plugin_Document();
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage This file type is not accepted! Please upload .jpg, .tif, .tiff, .pdf or .png document. foo.jpg
     */
    public function testUploadDocumentThrowNotCorrectImageFormatException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'text/plain',
                'size'     => 42,
                'error'    => 0
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document();
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage The uploaded file exceeds the upload_max_filesize directive in php.ini
     */
    public function testUploadDocumentThrowMethodMaxFileSizeException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_INI_SIZE
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document();
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
     */
    public function testUploadDocumentThrowMethodMaxFileSizeFormException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_FORM_SIZE
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document();
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }


}

