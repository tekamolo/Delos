<?php
declare(strict_types=1);

namespace Delos\Service;

trait JsonResponseArrayTrait
{
    private array $jsonResponseArray = array(
        'success' => false,
        'error' => array(),
    );

    public function addError(string $error): void
    {
        $this->jsonResponseArray['success'] = false;
        $this->jsonResponseArray['error'][] = $error;
    }

    public function getJsonResponseArray(): array
    {
        if ($this->isErrorEmpty()) $this->setSuccessToTrue();
        return $this->jsonResponseArray;
    }

    public function isErrorEmpty(): bool
    {
        return empty($this->jsonResponseArray['error']);
    }

    private function setSuccessToTrue(): void
    {
        $this->jsonResponseArray['success'] = true;
    }
}