<?php
declare(strict_types=1);

namespace Tests\Unit\Security;

use Delos\Exception\Exception;
use Delos\Security\Access;
use PHPUnit\Framework\TestCase;

final class AccessCheckerTest extends TestCase
{
    public function AccessProvider()
    {
        return [
            'Correct test' => [
                "department" => 'ADMIN|USER',
                "expectsError" => false
            ],
            'Error the admin folder is not in the access checker class' => [
                "department" => 'COMPLIANCE',
              "expectsError" => true
          ],
          'Error the access are not provided' => [
              "department" => '',
              "expectsError" => true
          ],
        ];
    }

    /**
     * @dataProvider AccessProvider
     * @throws Exception
     */
    public function testAccessChecker(string $department, bool $expectsError)
    {
        if ($expectsError) {
            $this->expectException(Exception::class);
        } else {
            $this->expectNotToPerformAssertions();
        }
        $accessChecker = new Access();
        $accessChecker->control($department);
    }
}