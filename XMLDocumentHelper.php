<?php

class WorldData{
    function __construct($numberOfIterations, $numberOfSpecies, $numberOfCells, $organisms)
    {
        $this->numberOfIterations = $numberOfIterations;
        $this->numberOfSpecies = $numberOfSpecies;
        $this->numberOfCells = $numberOfCells;
        $this->organisms = $organisms;
    }

    public $numberOfIterations;
    public $numberOfSpecies;
    public $numberOfCells;
    public $organisms;
}

class Organism{
    public $coordinate_x;
    public $coordinate_y;
    public $type;

    function __construct($coordinate_x, $coordinate_y, $type)
    {
        $this->coordinate_x = $coordinate_x;
        $this->coordinate_y = $coordinate_y;
        $this->type = $type;
    }
}

class SimpleXmlWorldParser
{
//TODO: Validate for input types howeever possible
    public static function readXMLDocument($file_name){
        if (file_exists($file_name)) {
            $xml = simplexml_load_file($file_name);

            foreach($xml->organisms->organism as $organism)
                $initial_organisms[] =new Organism(
                    current($organism->x_pos),
                    current($organism->y_pos),
                    current($organism->species)
                );

            $initial_world_object = new WorldData(
                current($xml->world->iterations),
                current($xml->world->species),
                current($xml->world-> cells),
                $initial_organisms
            );

            return $initial_world_object;
        }
        else
        {
            exit('Failed to open file');
        }
    }

    public static function createXMLDocument( $data){

        $xml = new DOMDocument('1.0', 'utf-8');

        $xml->formatOutput = true;

        $xml_life = $xml->createElement('life');
        $xml ->appendChild($xml_life);
            $xml_world = $xml->createElement('world');
            $xml_life -> appendChild( $xml_world );
                $xml_cells = $xml->createElement('cells', $data->numberOfCells);
                $xml_cells -> appendChild($xml->createTextNode(''));
                $xml_world -> appendChild($xml_cells );
                $xml_species = $xml->createElement('species', $data->numberOfSpecies);
                $xml_species -> appendChild($xml->createTextNode(''));
                $xml_world -> appendChild($xml_species );
                $xml_iterations = $xml->createElement('iterations', $data->numberOfIterations);
                $xml_iterations -> appendChild($xml->createTextNode(''));
                $xml_world      -> appendChild($xml_iterations );
            $xml_organisms      = $xml->createElement('organisms');
           foreach($data->organisms as $organism) {
               $xml_life->appendChild($xml_organisms);
               $xml_x_pos = $xml->createElement('x_pos', $organism->coordinate_x);
               $xml_x_pos->appendChild($xml->createTextNode(''));
               $xml_organisms->appendChild($xml_x_pos);
               $xml_y_pos = $xml->createElement('y_pos', $organism->coordinate_y);
               $xml_y_pos->appendChild($xml->createTextNode(''));
               $xml_organisms->appendChild($xml_y_pos);
               $xml_spec = $xml->createElement('species', $organism->type);
               $xml_spec->appendChild($xml->createTextNode(''));
               $xml_organisms->appendChild($xml_spec);
           }

    return $xml;
    }
}

$testArray = array("key1" => '42', "key2" => "array2");

var_dump(array_filter($testArray, function ($row) {return $row !== 42;}));

