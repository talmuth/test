<?php
include_once('bootstrap.php');

define('DRIVER_DOCUMENT_TMP_SCAN_PDF_PATH', APPLICATION_PATH . "/../" . PUBLIC_HTML_FOLDER . "/documents/_tmp-scan-pdf/");
class Driver_Plugin_Document
{
    private $_scanPdfDir;

    const tmpScanPdfDir = DRIVER_DOCUMENT_TMP_SCAN_PDF_PATH;

    public function __construct()
    {
        $this->_scanPdfDir = APPLICATION_PATH . "/../" . PUBLIC_HTML_FOLDER . "/documents/_tmp-scan-pdf";

        if (!is_dir($this->_scanPdfDir)) {
            mkdir($this->_scanPdfDir, 0777, true);
        }
    }

    public function uploadDocumentPage($driverId, $documentId, &$fileName, $filesArray = null)
    {
        if (empty($filesArray) && !count($filesArray)) {
            $filesArray = $_FILES['uploadPicture'];
        }

        $documentModel = new Documents_Model_CustomDocumentDriverFormNscCheck();
        $documentRow = $documentModel->getRow($documentId);

        $path = Documents_Model_CustomDocumentDriverFormNscCheck::uploadPath;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if ((in_array($filesArray["type"], array(
            "image/png",
            "image/jpeg",
            "image/pjpeg",
            'application/octet-stream',
            "image/tif",
            "image/x-tif",
            "image/tiff",
            "image/x-tiff",
            "application/tif",
            "application/x-tif",
            "application/tiff",
            "application/x-tiff",
            'application/acrobat',
            'application/x-pdf',
            'application/pdf',
            'applications/vnd.pdf',
            'text/pdf',
            'text/x-pdf'
        )))
        ) {
            if ($filesArray["error"] > 0) {
                $errMessage = '';
                switch ($filesArray["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errMessage = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errMessage = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errMessage = 'The uploaded file was only partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errMessage = 'No file was uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errMessage = 'Missing a temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errMessage = 'Failed to write file to disk';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $errMessage = 'File upload stopped by extension';
                        break;
                    default:
                        $errMessage = 'Unknown upload error';
                }

                throw new Exception($errMessage);
            } else {
                $extension = end(explode(".", $filesArray['name']));

                $withoutExt = $storeName = "dqf-ID" . $driverId . "_form-name-ID" . $documentRow->cddfnc_form_id . "__datetime" .
                    date("Y_m_d_H_i_s") . "__rand" . rand(1000, 9999) . "__filesize" . $filesArray["size"];

                if (!empty($extension)) {
                    $storeName .= ".{$extension}";
                }

                if (file_exists($path . $storeName)) {
                    throw new Exception('File already exists. Please try again later.');
                } else {
                    $result = @move_uploaded_file($filesArray["tmp_name"], $path . $storeName);
                    if ($result) {
                        $multipageArray = null;

                        if (in_array($extension, array('tif', 'tiff', 'pdf'))) {
                            $convertedFilename = $storeName;

                            $image = new Imagick($path . $storeName);
                            $image->setresourcelimit(Imagick::RESOURCETYPE_MEMORY, 512);
                            $image->setresourcelimit(Imagick::RESOURCETYPE_MAP, 1024);
                            $image->setresourcelimit(6, 1);

                            if ($image->getNumberImages() > 1 || 'pdf' == $extension) {
                                $attr = $image->identifyimage(true);

                                $cmd = 'convert -limit memory 128MiB -limit map 256Mib -limit thread 1 -density 150  -define registry:temporary-path="' . APPLICATION_PATH .  '/../data/tmp" "' . $path . $storeName . '" "' . $path . $withoutExt . '.jpg"';

                                if (system($cmd) === false) {
                                    throw new Exception('Unable to run convert script');
                                }

                                if ($image->getNumberImages() > 1) {
                                    try {
                                        foreach ($image as $index => $imagePage) {
                                            if (!file_exists($path . $withoutExt . "-{$index}.jpg")) {
                                                throw new Exception($withoutExt . "-{$index}.jpg" . ' - File does not exist');
                                            }

                                            $multipageArray[] = $withoutExt . "-{$index}.jpg";
                                        }
                                    } catch (Exception $e) {
                                        if (is_array($multipageArray) && count($multipageArray)) {
                                            foreach ($multipageArray as $page) {
                                                @unlink($path . $page);
                                            }
                                        }

                                        throw new Exception($e->getMessage());
                                    }
                                } else {
                                    if (!file_exists($path . $withoutExt . ".jpg")) {
                                        throw new Exception($withoutExt . ".jpg" . ' - File does not exist');
                                    }

                                    $storeName = $withoutExt . '.jpg';
                                }

                                @unlink($path . $convertedFilename);
                            }
                        }

                        if (empty($multipageArray)) {
                            $fileName = $storeName;
                        } else {
                            $fileName = $multipageArray;
                        }

                    } else {
                        throw new Exception('Error is occurred during file upload. Please try again later.');
                    }
                }
            }
        } else {
            throw new Exception('This file type is not accepted! Please upload .jpg, .tif, .tiff, .pdf or .png document. ' . $filesArray['name']);
        }
    }

    public function getDocument(Zend_Db_Table_Row_Abstract $documentRow, $contentDisposition = 'attachment')
    {
        $documentPageModel = new Documents_Model_CustomDocument();
        $documentPageList = $documentPageModel->getListByNscCheck($documentRow->cddfnc_id);

        if (count($documentPageList)) {
            require_once(APPLICATION_PATH . '/../library/tcpdf/config/lang/eng.php');
            require_once(APPLICATION_PATH . '/../library/tcpdf/tcpdf.php');

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetMargins(0, 0, 0,  true);
            $pdf->SetAutoPageBreak(FALSE,  0);

            foreach ($documentPageList as $pageRow) {
                $filePath = Documents_Model_CustomDocument::uploadPath . $pageRow->cd_Scan;
                if (file_exists($filePath)) {


                    $dpi = array(72, 72);
                    list($imgWidth, $imgHeight) = px2mm($filePath, $dpi);

                    $resolution= array($imgWidth,  $imgHeight);

                    if ($imgWidth > $imgHeight) {
                        $pdf->AddPage('L', $resolution, $keepmargins = true);
                    } else {
                        $pdf->AddPage('P', $resolution, $keepmargins = true);
                    }

                    $pdf->Image($filePath, '', '', (int)$imgWidth, (int)$imgHeight, '', '', '', true);
                }
            }

            $pdfData = $pdf->Output("{$documentRow->cdfn_name}.pdf", 'S');

            $response = new Zend_Controller_Response_Http();
            $response->setHeader('Content-Disposition', $contentDisposition . '; filename="' . $documentRow->cdfn_name . '.pdf"');
            $response->setHeader('Content-type', 'application/pdf');
            $response->setHeader('Content-length', strlen($pdfData));
            $response->setHeader('Cache-Control', 'private', true);
            $response->setHeader('Expires', '0', true);
            $response->setHeader('Pragma', 'private', true);
            $response->setBody($pdfData);
            $response->sendResponse();
        }
    }
}

function px2mm($filePath, $dpi) {
    list($imgWidth, $imgHeight) = getimagesize($filePath);

    $h = $imgWidth * 25.4 / $dpi[0];
    $l = $imgHeight * 25.4 / $dpi[1];

    $px2mm[] = $h;
    $px2mm[] = $l;

    return $px2mm;
}
