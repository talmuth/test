<?php
//    include_once 'bootstrap.php';
require_once '/usr/share/php/PHPUnit/Autoload.php';
/* подключаем phpUnit и файл с тестируемым классом */
require_once('Document.php');

class DriverPluginDocumentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockDocumentModel;

    protected function setUp()
    {
        // Create a stub for the SomeClass class.
        $this->_mockDocumentModel = $this->getMockBuilder('Documents_Model_CustomDocumentDriverFormNscCheck')
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    public function testCreatingClass()
    {
        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
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
        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
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

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
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

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
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

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage The uploaded file was only partially uploaded
     */
    public function testUploadDocumentThrowUploadPartialException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_PARTIAL
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage No file was uploaded
     */
    public function testUploadDocumentThrowNoFileException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_NO_FILE
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Missing a temporary folder
     */
    public function testUploadDocumentThrowNoTmpDirException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_NO_TMP_DIR
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Failed to write file to disk
     */
    public function testUploadDocumentThrowCantWriteException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_CANT_WRITE
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage File upload stopped by extension
     */
    public function testUploadDocumentThrowExtensionException()
    {
        $_FILES = array(
            'uploadPicture' => array(
                'name'     => 'foo.jpg',
                'tmp_name' => '/tmp/php42up23',
                'type'     => 'image/jpeg',
                'size'     => 42,
                'error'    => UPLOAD_ERR_EXTENSION
            )
        );

        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
        $fileName = 'test';
        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
    }

//    /**
//     * @expectedException        Exception
//     * @expectedExceptionMessage Unknown document
//     */
//    public function testUploadDocumentThrowNonExistentDocumentException()
//    {
//        // Configure the stub.
//        $this->_mockDocumentModel->expects($this->any())
//            ->method('getRow')
//            ->will($this->returnValue('null'));
//
//        $driverPluginDocument = new Driver_Plugin_Document($this->_mockDocumentModel);
//
//        $fileName = 'test';
//
//        $_FILES = array(
//            'uploadPicture' => array(
//                'name'     => 'foo.jpg',
//                'tmp_name' => '/tmp/php42up23',
//                'type'     => 'image/jpeg',
//                'size'     => 42,
//                'error'    => 0
//            )
//        );
//
//        $driverPluginDocument->uploadDocumentPage(1, 1, $fileName);
//    }


}

