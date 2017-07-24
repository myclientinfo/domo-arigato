<?php
namespace Roboto;
use PHPUnit\Framework\TestCase;

/**
 * Class RobotTest
 * @package Roboto
 */

class RobotTest extends TestCase
{
    /**
     * @var Robot
     */
    protected $robot;

    const TEST_X = 4;
    const TEST_Y = 3;
    const TEST_FACING = 'SOUTH';
    const TEST_DEGREES = 180;
    const TEST_REPORT_FORMAT = '%s,%s,%s';

    protected function setUp()
    {
        $this->robot = new Robot(new RobotState());
    }

    public function testPlace() {
        $this->robot->place(self::TEST_X, self::TEST_Y, self::TEST_FACING);

        $this->assertEquals(self::TEST_X, $this->robot->state->getCurrentX());
        $this->assertEquals(self::TEST_Y, $this->robot->state->getCurrentY());
        $this->assertEquals(self::TEST_FACING, $this->robot->state->getCurrentFacing());
        $this->assertEquals(self::TEST_DEGREES, $this->robot->state->getCurrentDegrees());
    }

    public function testRobotRejectsInvalidPlacement(){
        $this->expectException(InvalidMovementException::class);
        $this->robot->place(RobotState::MAX_X+1, RobotState::MAX_Y+1, self::TEST_FACING);
    }

    public function testRobotCannotMoveBeforeBeingPlaced(){
        $this->expectException(InvalidMovementException::class);
        $this->robot->move();
    }

    public function testRobotRejectsInvalidMove(){
        // place robot at north east corner facing east
        $this->robot->place(RobotState::MAX_X, RobotState::MAX_Y, 'EAST');
        $this->expectException(InvalidMovementException::class);
        $this->robot->move();
    }

    public function testReport(){
        $this->robot->place(self::TEST_X, self::TEST_Y, self::TEST_FACING);
        $report = $this->robot->report();
        $this->assertInstanceOf(RobotState::class, $report);
    }

    public function testRobotCannotReportBeforeBeingPlaced(){
        $this->expectException(InvalidMovementException::class);
        $this->robot->report();
    }

    public function testCommanderRejectsInvalidCommand(){
        $this->expectException(InvalidCommandException::class);
        $this->robot->commander('INVALID');
    }

    /**
     * @dataProvider dataFileProvider
     */
    public function testDataFile($commands, $result){
        foreach($commands as $command) {
            $this->robot->commander($command);
        }
        $report = json_decode((string)$this->robot->report());
        $report_string = sprintf(self::TEST_REPORT_FORMAT, $report->x_position, $report->y_position, $report->facing);
        $this->assertEquals($result, $report_string);
    }

    public function dataFileProvider(){
        $file = file('input.txt');
        $data_array = [];
        foreach($file as $row){
            list($commands, $result) = explode('=', $row);
            $commands = explode('->', $commands);
            $data_array[] = ['commands' => $commands, 'result' => trim($result)];
        }
        return $data_array;
    }
}
