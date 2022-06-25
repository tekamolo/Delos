<?php
declare(strict_types=1);

namespace Tests\Unit\Security;

use Delos\Security\Sanitizer;
use PHPUnit\Framework\TestCase;

final class SanitizerTest extends TestCase
{
    public function passDataProvider(): array
    {
        return array(
            ["standard" => ";SO'YW;&3`T3gn<yI2X;"],
            ["<a> tag" => ";SO'YW;&3`T3gn<yI2X<a>;"],
//            ["semicolon" => ";SO'YW;&3`T3gn<yI2X<a>;"],
            ["open and close tags" => ";SO'YW;&3`T<h3></h3>3gn<yI2X<a>;"],
            ["spaces" => ";SO'YW  ;&3`T<h3></h3>3gn<yI2X<a>;"],
            ["script tags" => ";SO'YW  ;&3`T<script src='https://www.google.com'></script>3gn<yI2X< a>;"],
//            ["semicolon" => ";SO'Y:W  ;&3`T<script src='https://www.google.com'></script>3gn<yI2X< a>;"],
        );
    }

    /**
     * @dataProvider passDataProvider
     */
    public function testSanitizePassword(string $password)
    {
        $expect = ";SO'YW;&3`T3gn<yI2X;";

        $result = Sanitizer::sanitizePassword($password);
        $this->assertEquals($expect, $result);
    }

    public function testSanitizeDots(){
        $password = "ñ~*zQ6$.pU*pG!";
        $result = Sanitizer::sanitizePassword($password);
        $this->assertEquals($password,$result);
    }
}