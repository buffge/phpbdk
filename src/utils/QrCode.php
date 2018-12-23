<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-8-23, 10:34:00
 * QQ:1515888956
 */
namespace bdk\utils;

use think\facade\App;
use bdk\traits\Register;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode as EQrCode;

/**
 * @regName QrCode
 */
class QrCode
{
    use Register;
    public function png(string $url)
    {
        $qrCode = new EQrCode($url);
        $qrCode->setSize(300);
        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLabel('亨中信科', 16, __DIR__ . '/assets/fonts/simsun.ttc', LabelAlignment::CENTER);
        $qrCode->setLogoPath(__DIR__ . '/assets/images/symfony.png');
        $qrCode->setLogoSize(50, 50);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        // Directly output the QR code
//        Response::header('Content-Type: ' . $qrCode->getContentType());
        header('Content-Type: ' . $qrCode->getContentType());
        echo $qrCode->writeString();
        die;
        // Save it to a file
//        $qrCode->writeFile(__DIR__ . '/qrcode.png');
// Create a response object
//        $response = new QrCodeResponse($qrCode);
//        return $response;
    }

    public function createInvoteCode($url)
    {
        $qrCode = new EQrCode($url);
        $qrCode->setSize(300);
        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
//        $qrCode->setLabel('亨中信科', 16, __DIR__ . '/assets/fonts/simsun.ttc', LabelAlignment::CENTER);
        $qrCode->setLogoPath(App::getRootPath() . '/public/static/images/logo_no_word.jpg');
        $qrCode->setLogoSize(50, 50);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        // Directly output the QR code
//        Response::header('Content-Type: ' . $qrCode->getContentType());
        header('Content-Type: ' . $qrCode->getContentType());

        echo $qrCode->writeString();
        die;
    }
}
