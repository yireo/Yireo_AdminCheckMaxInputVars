<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Validator;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Yireo\AdminCheckMaxInputVars\Exception\ValidationException;

class Validator
{
    /**
     * @param RequestInterface $request
     */
    public function handleMaxInputVars(RequestInterface $request)
    {
        $currentInputVars = $this->countTerminals((array)$request->getPost());
        $maxInputVars = $this->getMaxInputVars();
        if ($currentInputVars >= $maxInputVars) {
            $msg = 'Increase the PHP value "max_input_vars". Current value %1 is too low.';
            throw new ValidationException(__($msg, $maxInputVars));
        }
    }

    /**
     * @param RequestInterface $request
     */
    public function handleInputNestingLevel(RequestInterface $request)
    {
        $maxInputNestingLevel = $this->getMaxInputNestingLevel();
        $currentInputNestingLevel = $this->getArrayDepth((array)$request->getPost());
        if ($currentInputNestingLevel >= $maxInputNestingLevel) {
            $msg = 'Increase the PHP value "max_input_nesting_level". Current value %s is too low.';
            throw new ValidationException(__($msg, $currentInputNestingLevel));
        }
    }

    /**
     * @param $value
     * @return int
     */
    private function countTerminals($value): int
    {
        return is_array($value)
            ? array_reduce(
                $value,
                function ($carry, $item) {
                    return $carry + $this->countTerminals($item);
                },
                0
            )
            : 1;
    }

    /**
     * @param array $array
     * @return int
     */
    private function getArrayDepth(array $array): int
    {
        $depth = 0;
        $iteratorIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($iteratorIterator as $iterator) {
            $currentDepth = (int)$iteratorIterator->getDepth();
            $depth = $currentDepth > $depth ? $currentDepth : $depth;
        }

        return $depth;
    }

    /**
     * @return int
     */
    private function getMaxInputVars(): int
    {
        return (int)ini_get('max_input_vars');
    }

    /**
     * @return int
     */
    private function getMaxInputNestingLevel(): int
    {
        return (int)ini_get('max_input_nesting_level');
    }
}
