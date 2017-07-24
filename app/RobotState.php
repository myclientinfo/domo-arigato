<?php
namespace Roboto;


class RobotState
{
    const MAX_X = 5;
    const MAX_Y = 5;

    private $current_x = null;
    private $current_y = null;
    private $new_x = null;
    private $new_y = null;

    private $current_degrees = null;
    private $new_degrees = null;

    private $placed = false;

    const DEGREES_FACING = [
        0 => 'NORTH',
        90 => 'EAST',
        180 => 'SOUTH',
        270 => 'WEST'
    ];

    public function __construct($state = null){
        if($state){
            $state = json_decode($state);
            $this->current_x = $state->x_position;
            $this->current_y = $state->y_position;
            $this->current_degrees = $state->degrees;
            $this->placed = true;
        }
    }

    /**
     * @return integer
     */
    public function getCurrentX()
    {
        return $this->current_x;
    }

    /**
     * @param integer $set_x
     */
    public function setXPosition($set_x)
    {
        if ($set_x > self::MAX_X || $set_x  < 1) {
            throw new InvalidMovementException;
        }
        $this->new_x = $set_x;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCurrentY()
    {
        return $this->current_y;
    }

    /**
     * @param integer $set_y
     */
    public function setYPosition($set_y)
    {
        if ($set_y > self::MAX_Y || $set_y  < 1) {
            throw new InvalidMovementException;
        }
        $this->new_y = $set_y;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCurrentDegrees()
    {
        return $this->current_degrees;
    }

    /**
     * @param integer $degrees
     */
    public function setDegrees($degrees)
    {
        $this->new_degrees = $degrees;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentFacing()
    {
        if($this->current_degrees === null){
            return null;
        }
        return self::DEGREES_FACING[$this->current_degrees];
    }

    /**
     * @param string $current_degrees
     */
    public function setFacing($facing)
    {
        $facing_degrees = array_flip(self::DEGREES_FACING);
        if(!isset($facing_degrees[$facing])) {
            throw new InvalidMovementException;
        }

        $this->new_degrees = $facing_degrees[$facing];
        return $this;
    }

    public function reset(){
        $this->new_x = null;
        $this->new_y = null;
        $this->new_degrees = null;
        return $this;
    }

    public function commit(){

        if($this->new_x){
            $this->current_x = $this->new_x;
        }
        if($this->new_y){
            $this->current_y = $this->new_y;
        }
        if($this->new_degrees !== null) {
            $this->current_degrees = $this->new_degrees;
        }

        $_SESSION['robot_state'] = (string)$this;
        //print_r($_SESSION);
        $this->reset();
    }

    public function isDirty(){
        return $this->new_y || $this->new_x || $this->new_degrees;
    }

    public function setPlaced(){
        $this->placed = true;
        return $this;
    }

    public function getPlaced(){
        return $this->placed;
    }

    public function __toString()
    {
        $state = [
            'x_position' => $this->current_x,
            'y_position' => $this->current_y,
            'degrees' => $this->current_degrees,
            'facing' => $this->getCurrentFacing()
        ];

        return json_encode($state);
    }
}