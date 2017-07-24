<?php
namespace Roboto;


class Robot
{

    public $state;

    public function __construct(RobotState $state)
    {
        $this->state = $state;
    }

    /**
     * Handles input command strings, breaking them up and executing action methods. These can have any source.
     *
     * @param string $input
     * @throws InvalidCommandException
     * @return void
     */
    public function commander(string $input): void {
        $command_array = explode(' ', $input);
        $command = $command_array[0];

        if(count($command_array) > 1){
            $option = $command_array[1];
        }

        if($command === 'PLACE') {

            if(!isset($option)){
                throw new InvalidCommandException('A place command requires co-ordinates and facing');
            }

            list($coord_x, $coord_y, $facing) = explode(',', $option);

            // all of these must be set by input string
            if(!$coord_x || !$coord_y || !$facing){
                throw new InvalidCommandException('A place command requires co-ordinates and facing');
            }

            $this->place($coord_x, $coord_y, $facing);
        }
        else if($command === 'MOVE') {
            $this->move();
        }
        else if($command === 'LEFT') {
            $this->turnLeft();
        }
        else if($command === 'RIGHT') {
            $this->turnRight();
        }
        else if($command === 'REPORT') {
            // null action - report is run after all successful actions
        }
        else {
            throw new InvalidCommandException(sprintf('The %s command is not supported', $command));
        }
    }

    public function move(){
        if(!$this->state->getPlaced()){
            throw new InvalidMovementException('Robot must be placed before moving');
        }

        $facing = $this->state->getCurrentFacing();
        $coord_x = $this->state->getCurrentX();
        $coord_y = $this->state->getCurrentY();

        try {
            if($facing === 'NORTH'){
                $this->state->setYPosition($coord_y + 1);
            }
            else if($facing === 'SOUTH'){
                $this->state->setYPosition($coord_y - 1);
            }
            else if($facing === 'EAST'){
                $this->state->setXPosition($coord_x + 1);
            }
            else if($facing === 'WEST'){
                $this->state->setXPosition($coord_x - 1);
            }

        } catch(InvalidMovementException $e) {
            $this->state->reset();
            throw new InvalidMovementException('This movement would hurt the robot');
        }

        $this->state->commit();

    }

    public function turnLeft(){

        if(!$this->state->getPlaced()){
            throw new InvalidMovementException('The robot must be placed before turning');
        }

        $new_degrees = $this->state->getCurrentDegrees() - 90;

        if($new_degrees === -90){
           $new_degrees = 270;
        }
        $this->state->setDegrees($new_degrees)->commit();


    }

    public function turnRight(){

        if(!$this->state->getPlaced()){
            throw new InvalidMovementException('The robot must be placed before turning');
        }

        $new_degrees = $this->state->getCurrentDegrees() + 90;

        if($new_degrees === 360){
            $new_degrees = 0;
        }

        $this->state->setDegrees($new_degrees)->commit();
    }


    public function place(int $x_coord, int $y_coord, string $facing = 'NORTH'){
        try {
            $this->state->setXPosition($x_coord)->setYPosition($y_coord)->setFacing($facing);
        } catch(InvalidMovementException $e) {
            $this->state->reset();
            throw $e;
        }

        $this->state->setPlaced()->commit();
    }

    public function report() : RobotState {
        if(!$this->state->getPlaced()){
            throw new InvalidMovementException('The robot must be placed before it can be reported');
        }
        return $this->state;
    }


}