<?phptry{    // Include Ini & Utils Classes    require_once '../app/Mage.php';    require_once('lib/utils/CSVParser.php');    // Introduce Magento Objects    Mage::app();        function debug($arr){        echo "<pre>";        print_r($arr);        echo "</pre>";    }}catch(Exception $e){    echo $e->getMessage();}