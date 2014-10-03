<?php

error_reporting(E_ALL);

require_once('world.php');
require_once('XMLDocumentHelper.php');

$initial_data = SimpleXmlWorldParser::readXMLDocument('in.xml');
$world = new World($initial_data->numberOfCells, $initial_data->organisms);

function render($world, $initial_data) {
    $rendering = '';
    for ($y = 0; $y <= $initial_data->numberOfCells -1; $y++) {
        $rendering .= $y;
        for ($x = 0; $x <= $initial_data->numberOfCells -1; $x++) {
            $cells = $world->getState();
            $rendering .= '|' . (isset($cells[$x][$y]) ? $cells[$x][$y]->to_char() : '|');
        }
        $rendering .= "\n";
    }
    return $rendering;
}



$initial_data2 = SimpleXmlWorldParser::createXMLDocument($initial_data);


for($iteration =0; $iteration < $initial_data->numberOfIterations; $iteration++ ) {
    $output = "{$world->getTickCount()}";
    $output .= "\n".render($world, $initial_data);
    echo $output;
    $world->tick();
    sleep(1);
//    system('clear');

}