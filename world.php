<?php

error_reporting(E_ALL);

class LocationOccupied extends Exception { }

class World {

    private $tick;

    /**
     * @var Cell[][] $current_cells
     */
    private $current_cells;
    private $new_cells;
    private $directions;

    /**
     * @param int $noOfCells
     * @param Organism[] $organisms
     */
    public function __construct($noOfCells, $organisms)
    {
        $this->tick = 0;
        $this->initializeCells($noOfCells);
        $this->initializeOrganisms($organisms);

        $this->directions = array(
            array(-1, 1),  array(0, 1),  array(1, 1),
            array(-1, 0),                array(1, 0),
            array(-1, -1), array(0, -1), array(1, -1)
        );
    }

    /**
     * @param int $noOfCells
     */
    private function initializeCells($noOfCells)
    {
        echo $noOfCells;
        $this->current_cells = array();
        for($x=0; $x < $noOfCells; $x++)
        {
             $this->current_cells[$x] = array();
            for($y=0; $y < $noOfCells; $y++)
            {
                $this->current_cells[$x][$y] = new Cell($x, $y);
            }
        }
    }

    /**
     * @param Organism[] $organisms
     */
    private function initializeOrganisms($organisms)
    {
        foreach($organisms as $organism)
        {
            $cell = $this->current_cells[$organism->coordinate_x][$organism->coordinate_y];
            $cell->setType($organism->type);
        }
    }

    function neighbours_around($cell) {
        if (!isset($this->neighbours[$cell->key])) {
            $this->neighbours[$cell->key] = array();
            foreach ($this->directions as $set) {
                $neighbour = $this->cell_at(($cell->x + $set[0]), ($cell->y + $set[1]));
                if ($neighbour) { $this->neighbours[$cell->key][] = $neighbour; }
            }
        }
        return $this->neighbours[$cell->key];
    }

    function alive_neighbours_around($cell) {
        $alive_neighbours = 0;
        foreach ($this->neighbours_around($cell) as $cell) {
            if (!$cell->dead) {
                $alive_neighbours++;
            }
        }
        return $alive_neighbours;
    }

    /**
     * @param Cell $cell
     * @return null|string
     */
    private function calculate_next_cell_state($cell)
    {
        $typesCount = array();

        foreach ($this->directions as $direction)
        {
            $neighbour_x = $cell->getX() + $direction[0];
            $neighbour_y = $cell->getY() + $direction[1];
            if(array_key_exists($neighbour_x, $this->current_cells) && array_key_exists($neighbour_y, $this->current_cells))
            {
                $neighbourType = $this->current_cells[$neighbour_x][$neighbour_y]->getType();

                if(!empty($neighbourType))
                {
                    $typesCount[$neighbourType] = isset($typesCount[$neighbourType]) ? $typesCount[$neighbourType] + 1 : 1;
                }
            }
         }

        $typesCountGreaterThanThree = array_filter($typesCount, function($count){return $count >= 3; });

        if(empty($cell->getType()) && !empty($typesCountGreaterThanThree))
        {
           return array_rand($typesCountGreaterThanThree, 1);
        }
        elseif(!empty($cell->getType()) && (empty($typesCount[$cell->getType()]) || $typesCount[$cell->getType()] > 3 || $typesCount[$cell->getType()] < 2))
        {
            return NULL;
        }
        else
        {
            return $cell->getType();
        }
    }

    function tick()
    {
        $this->new_cells = $this->current_cells;

        foreach($this->new_cells as $newCellRow)
        {
            foreach($newCellRow as $newCell)
            {
                $type = $this->calculate_next_cell_state($newCell);
                $newCell->setType($type);
            }
        }

        unset($this->current_cells);

        $this->current_cells = $this->new_cells;

        $this->tick += 1;
    }

    public function getTickCount()
    {
        return $this->tick;
    }

    function getState()
    {
        return $this->current_cells;
    }
}


class Cell
{
    private $x;
    private $y;
    private $type;
    private $occupied;

    /**
     * @param int $x
     * @param int $y
     * @param bool $occupied
     * @param null|string $type
     */
    function __construct($x, $y, $occupied = false, $type = NULL)
    {
        $this->x = $x;
        $this->y = $y;
        $this->occupied = $occupied;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->occupied = !empty($type);
    }

    /**
     * @return boolean
     */
    public function isOccupied()
    {
        return $this->occupied;
    }

    /**
     * @return string
     */
    function to_char() {
        return (empty($this->type) ? ' ' : "{$this->type}");
    }

}