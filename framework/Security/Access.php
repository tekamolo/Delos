<?php
declare(strict_types=1);

namespace Delos\Security;

use Delos\Exception\Exception;

final class Access
{
    const USER = "USER";
    const ADMIN = "ADMIN";

    public array $userTypeArray = array(
        self::USER,
        self::ADMIN,
    );
    private string $currentUser;
    private string $adminFolder;

    /**
     * AccessChecker constructor.
     */
    public function __construct()
    {
    }

    public function control(string $accessString): void
    {
        $accessArray = explode("|", $accessString);
        $accessGranted = false;

        foreach ($accessArray as $a) {
            if (defined("self::" . $a)) {
                $accessGranted = true;
                $this->currentUser = $a;
                break;
            }
        }
        if (!$accessGranted) {
            throw new Exception("The user types " . $accessString . " cannot access this resources");
        }
    }

    /**
     * Set access can be used in twig templates for filtering accesses between departments. Note: kind of looks like the previous method except
     * accessGranted flag was removed replaced by hasAccess var and also the Exception was totally removed
     * @param string $accessString ex: "MANAGEMENT|STAFF_SUPPORT"
     * @return bool
     */
    public function setAccess(string $accessString): bool
    {
        $accessArray = explode("|", $accessString);
        $hasAccess = false;

        foreach ($accessArray as $a) {
            if (defined("self::" . $a) && constant("self::" . $a) == $this->adminFolder) {
                $hasAccess = true;
                break;
            }
        }
        return $hasAccess;
    }

    /**
     * Set access by contants can be used in the code.
     * @param array $accessArray ex: "array(Access::MANAGEMENT)"
     * @return bool
     */
    public function hasAccessCaseSet(array $accessArray): bool
    {
        $hasAccess = false;
        if (in_array($this->adminFolder, $accessArray)) {
            $hasAccess = true;
        }
        return $hasAccess;
    }


}