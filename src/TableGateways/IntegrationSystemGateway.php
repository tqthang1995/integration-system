<?php
namespace Src\TableGateways;

class IntegrationSystemGateway {
    public function __construct() {}

    public function getAllContentByUrl($url) {
        include('C:\Study\php\IntegrationSystem\src\TableGateways\parse\simple_html_dom.php');
        $url = 'https://vnexpress.net/hom-nay-hoang-xuan-vinh-ve-nuoc-3452035.html';
        $html = file_get_html($url);
        echo $html;
    }
}