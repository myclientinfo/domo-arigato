<?php
namespace Roboto;
use PHPUnit\Framework\TestCase;

/**
 * covers RobotState
 */
class RobotStateTest extends TestCase
{
    /**
     * @var RobotState
     */
    protected $state;

    const TEST_X = 4;
    const TEST_Y = 3;
    const TEST_FACING = 'SOUTH';

    protected function setUp()
    {

        $this->state = new RobotState();
    }

    /**
     * @dataProvider stateProvider
     */
    public function testSetters($x_coord, $y_coord, $x_expected, $y_expected): void {
        $this->state->setXPosition($x_coord);
        $this->state->setYPosition($y_coord);
        $this->state->commit();

        $this->assertEquals($x_expected, $this->state->getCurrentX());
        $this->assertEquals($y_expected, $this->state->getCurrentY());
    }

    public function testCoordinateXSetterToInvalidValue(){
        $this->expectException(InvalidMovementException::class);
        $this->state->setXPosition((RobotState::MAX_X + 1));
    }

    public function testCoordinateYSetterToInvalidValue(){
        $this->expectException(InvalidMovementException::class);
        $this->state->setYPosition((RobotState::MAX_Y + 1));
    }

    public function testUncommittedCodeDoesNotUpdate(){
        $this->state->setXPosition(self::TEST_X);
        $new_position = $this->state->getCurrentX();

        $this->assertNotEquals($new_position, self::TEST_X, 'Settings should not be saved prior to commit');
    }

    public function testSetFacingValidValue(){
        $this->assertEquals(null, $this->state->getCurrentFacing());

        $this->state->setFacing(self::TEST_FACING)->commit();

        $this->assertEquals(self::TEST_FACING, $this->state->getCurrentFacing());

    }

    public function testFluentInterface() {
        $this->state->setXPosition(self::TEST_X)
            ->setYPosition(self::TEST_Y)
            ->setFacing(self::TEST_FACING)
            ->commit();

        $this->assertEquals(self::TEST_X, $this->state->getCurrentX());
        $this->assertEquals(self::TEST_Y, $this->state->getCurrentY());
        $this->assertEquals(180, $this->state->getCurrentDegrees());
    }

    public function testIsDirty() {
        $this->assertFalse($this->state->isDirty());
        $this->state->setXPosition(self::TEST_X);
        $this->assertTrue($this->state->isDirty());
    }

    public function testIsNotDirtyAfterCommit() {
        $this->state->setXPosition(self::TEST_X);
        $this->assertTrue($this->state->isDirty());
        $this->state->commit();
        $this->assertFalse($this->state->isDirty());
    }

    public function testIsNotDirtyAfterRest() {
        $this->state->setYPosition(self::TEST_Y);
        $this->assertTrue($this->state->isDirty());
        $this->state->reset();
        $this->assertFalse($this->state->isDirty());
    }

    public function testIsNotDirtyAfterInvalidMovement() {
        $this->assertFalse($this->state->isDirty());
        $this->expectException(InvalidMovementException::class);
        $this->state->setXPosition(RobotState::MAX_X+1);
        $this->assertFalse($this->state->isDirty());
    }

    public function stateProvider(){
        return [
            'set as one'  => [1, 1, 1, 1],
            'set as one and five' => [5, 1, 5, 1],
            'set as five' => [5, 5, 5, 5],
        ];
    }
}
