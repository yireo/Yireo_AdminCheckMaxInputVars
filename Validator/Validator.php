<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Validator;

use Magento\Framework\App\Request\Http as HttpRequest;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Yireo\AdminCheckMaxInputVars\Exception\ValidationException;

class Validator
{
    /**
     * Throw an exception when the POST data reaches the PHP max_input_vars limit
     *
     * @param HttpRequest $request
     * @return void
     * @throws ValidationException
     */
    public function handleMaxInputVars(HttpRequest $request): void
    {
        $currentInputVars = $this->countTerminals($request->getPost()->toArray());
        $maxInputVars = $this->getMaxInputVars();
        if ($maxInputVars > 0 && $currentInputVars >= $maxInputVars) {
            $msg = 'Increase the PHP value "max_input_vars". Current value %1 is too low.';
            throw new ValidationException(__($msg, $maxInputVars));
        }
    }

    /**
     * Throw an exception when the POST data reaches the PHP max_input_nesting_level limit
     *
     * @param HttpRequest $request
     * @return void
     * @throws ValidationException
     */
    public function handleInputNestingLevel(HttpRequest $request): void
    {
        $maxInputNestingLevel = $this->getMaxInputNestingLevel();
        $currentInputNestingLevel = $this->getArrayDepth($request->getPost()->toArray());
        if ($maxInputNestingLevel > 0 && $currentInputNestingLevel >= $maxInputNestingLevel) {
            $msg = 'Increase the PHP value "max_input_nesting_level". Current value %1 is too low.';
            throw new ValidationException(__($msg, $maxInputNestingLevel));
        }
    }

    /**
     * Get the PHP max_input_vars value
     *
     * @return int
     */
    protected function getMaxInputVars(): int
    {
        return (int)ini_get('max_input_vars');
    }

    /**
     * Get the PHP max_input_nesting_level value
     *
     * @return int
     */
    protected function getMaxInputNestingLevel(): int
    {
        return (int)ini_get('max_input_nesting_level');
    }

    /**
     * Count all non-array values
     *
     * @param mixed $value
     * @return int
     */
    private function countTerminals(mixed $value): int
    {
        if (!is_array($value)) {
            return 1;
        }

        $count = 0;
        foreach ($value as $item) {
            $count += $this->countTerminals($item);
        }

        return $count;
    }

    /**
     * Get the maximum nesting depth of an array
     *
     * @param array $array
     * @return int
     */
    private function getArrayDepth(array $array): int
    {
        $depth = 0;
        $iteratorIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($iteratorIterator as $ignored) {
            $depth = max($depth, (int)$iteratorIterator->getDepth());
        }

        return $depth;
    }
}
