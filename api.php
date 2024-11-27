<?php

include "../../server/authcontrol.php";
include "login.php";

ini_set("display_errors", 0);
error_reporting(0);

header('Content-Type: application/json');

// Kullanıcı adı ve soyadı kontrolü
if (empty($_POST["ad"])) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Ad eksik!'
    ));
    exit;
} else if (empty($_POST["soyad"])) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Soyad eksik!'
    ));
    exit;
}

$ad = $_POST["ad"];
$soyad = $_POST["soyad"];

// Soğuma süresi kontrolü
$checkCooldown = checkCooldown($kid);
if ($checkCooldown["success"] == "false") {
    die(json_encode($checkCooldown));
} else {
    addCooldown($kid);
}

// Etkinlik detaylarını almak için cURL fonksiyonu
function getEventDetails($cookies)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://example.com/your_endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXY, $GLOBALS["proxy"]);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS["proxyauth"]);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    $doc = new DOMDocument();
    @$doc->loadHTML($result);
    $xpath = new DOMXPath($doc);
    $viewstateelement = $xpath->query("//input[@id='__VIEWSTATE']");
    if ($viewstateelement->length > 0) {
        $viewstate = $viewstateelement->item(0)->getAttribute("value");
    } else {
        return false;
    }

    return array(
        "viewstate" => $viewstate
    );
}

// Kişi detaylarını sorgulama fonksiyonu
function getPersonDetail($cookies, $eventParams, $ad, $soyad)
{
    $queryarray = array();
    $queryarray["__EVENTTARGET"] = 'ctl00$smCoolite';
    $queryarray["__EVENTARGUMENT"] = "cphCFB_customerSearch_btnSearchCustomer|event|Click";
    $queryarray["__VIEWSTATE"] = $eventParams["viewstate"];
    $queryarray["submitAjaxEventConfig"] = json_encode(array(
        "config" => [
            "extraParams" => [
                "txtCustomerName" => $ad,
                "txtCustomerSurname" => $soyad
            ]
        ]
    ));
    $query = http_build_query($queryarray);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://example.com/your_endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXY, $GLOBALS["proxy"]);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS["proxyauth"]);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: " . strlen($query)
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    $result = curl_exec($ch);
    curl_close($ch);

    if (strpos($result, "Kriterlere uygun kayıt bulunamadı.") !== false) {
        return array();
    } else {
        // Burada alınan JSON verisi işlenir
        $data = json_decode($result, true);
        return $data;
    }
}

// Cookie dosyasını al
$fileCookies = file_get_contents("cookie.txt");
$eventdetails = getEventDetails($fileCookies);

if ($eventdetails == false) {
    die(json_encode(array("success" => "false", "message" => "Giriş hatası")));
}

$result = getPersonDetail($fileCookies, $eventdetails, $ad, $soyad);
$number = count($result);

if ($number < 1) {
    die(json_encode(array("success" => "false", "message" => "Kayıt bulunamadı")));
} else {
    $data = array();
    foreach ($result as $key => $value) {
        array_push($data, array(
            "tc" => $value["TCK_VKN"],
            "ad" => $value["AD_SOYAD_UNVAN"],
            "dogumtarihi" => $value["DOGUM_YILI"],
            "babaadi" => $value["BABA_ADI"]
        ));
    }
    die(json_encode(array("success" => "true", "number" => $number, "data" => $data)));
}
