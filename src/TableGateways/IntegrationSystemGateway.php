<?php
namespace Src\TableGateways;

// Include the Simple HTML DOM Parser library file
include_once __DIR__ . '/../../simple_html_dom.php';

class IntegrationSystemGateway {
    public function __construct() {}

    public function getAllContentByUrl($url) {
        // Initialize Simple HTML DOM Parser
        $html = new \simple_html_dom();
        
        $url = !empty($url) ? $url : 'https://vnexpress.net/hom-nay-hoang-xuan-vinh-ve-nuoc-3452035.html';
        // Use the parse_url function to get the HTML content from the URL
        $html->load_file($url);
        
        // Check if HTML content was successfully loaded
        if ($html) {
            // Return HTML content instead of echoing it
            echo $html;
            return $html;
        } else {
            return "Failed to load HTML content from URL: $url";
        }
    }
}
