<?php

error_reporting(E_ALL);

require_once('world.php');
require_once('XMLDocumentHelper.php');

// Initial data import from XML and initial World created
$initial_data = SimpleXmlWorldParser::readXMLDocument('in.xml');
$world = new World($initial_data->numberOfCells, $initial_data->organisms);


for($iteration =0; $iteration < $initial_data->numberOfIterations; $iteration++ ) {
    $output = "Iteration: {$world->getTickCount()}";
    $output .= "\n".render($world, $initial_data);
    $world->tick();
    system('clear');
    echo $output;
    sleep(1);
}

$newWorldData = worldToWorldData($world, $initial_data);

$outputXmlDocument = SimpleXmlWorldParser::createXMLDocument($newWorldData);
$outputXmlDocument->saveXML();


function worldToWorldData($world, $initial_data)
{
    $cellsArray = array();


    foreach($world->getState() as $currentCellRow)
    {
        foreach($currentCellRow as $currentCell)
        {
           if(!empty($currentCell->getType())){
               $cellsArray[] = new Organism($currentCell->getX(), $currentCell->getY(), $currentCell->getType());
           }
        }
    }

    $worldData = new WorldData($initial_data->numberOfIterations, $initial_data->numberOfSpecies, $initial_data->numberOfCells, $cellsArray);

    return $worldData;
}

// Simple console world render function...
function render($world, $initial_data) {
    $rendering = '';
    for ($y = 0; $y <= $initial_data->numberOfCells -1; $y++) {
        $rendering .= $y;
        if($y <= 9){ $rendering .= " ";}
        for ($x = 0; $x <= $initial_data->numberOfCells -1; $x++) {
            $cells = $world->getState();
            $rendering .= '|' . (isset($cells[$x][$y]) ? $cells[$x][$y]->to_char() : '|');
        }
        $rendering .= "\n";
    }
    return $rendering;
}