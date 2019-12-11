<?php

use Delos\Security\Access;

class AccessTest extends PHPUnit_Framework_TestCase
{
    public function AccessProvider()
    {
        return [
          'Correct test'=> [
              "adminFolder" => Access::MANAGEMENT,
              "department" => 'MANAGEMENT|STAFF_SUPPORT',
              "expectsError" => false
            ],
          'Error the admin folder is not in the access checker class' => [
              "adminFolder" => '!_admin9',
              "department" => 'MANAGEMENT|STAFF_SUPPORT',
              "expectsError" => true
          ],
          'Error the resource access is not grant for compliance' => [
              "adminFolder" => Access::COMPLIANCE,
              "department" => 'MANAGEMENT|STAFF_SUPPORT',
              "expectsError" => true
          ],
          'Error the access are not provided' => [
              "adminFolder" => Access::COMPLIANCE,
              "department" => '',
              "expectsError" => true
          ],
        ];
    }

    /**
     * @dataProvider AccessProvider
     * @param $adminFolder
     * @param $department
     * @param $expectsError
     * @throws \Delos\Exception\Exception
     */
    public function testAccessChecker($adminFolder,$department,$expectsError)
    {
        if($expectsError){
            $this->expectException(Exception::class);
        }
        $accessChecker = new Access($adminFolder);
        $accessChecker->control($department);

    }
}