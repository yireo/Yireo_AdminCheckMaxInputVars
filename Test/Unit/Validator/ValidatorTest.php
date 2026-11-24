<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Test\Unit\Validator;

use Laminas\Stdlib\Parameters;
use Magento\Framework\App\Request\Http as HttpRequest;
use PHPUnit\Framework\TestCase;
use Yireo\AdminCheckMaxInputVars\Exception\ValidationException;
use Yireo\AdminCheckMaxInputVars\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function testMaxInputVarsBelowLimit(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getValidator(5, 10)->handleMaxInputVars($this->getRequest(['a' => 1, 'b' => ['c' => 2, 'd' => 3]]));
    }

    public function testMaxInputVarsAtLimit(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Current value 3 is too low');
        $this->getValidator(3, 10)->handleMaxInputVars($this->getRequest(['a' => 1, 'b' => ['c' => 2, 'd' => 3]]));
    }

    public function testMaxInputVarsIgnoredWithoutLimit(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getValidator(0, 0)->handleMaxInputVars($this->getRequest(['a' => 1]));
    }

    public function testInputNestingLevelBelowLimit(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getValidator(100, 3)->handleInputNestingLevel($this->getRequest(['a' => ['b' => 1]]));
    }

    public function testInputNestingLevelAtLimit(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('"max_input_nesting_level". Current value 2 is too low');
        $this->getValidator(100, 2)->handleInputNestingLevel($this->getRequest(['a' => ['b' => ['c' => 1]]]));
    }

    private function getValidator(int $maxInputVars, int $maxInputNestingLevel): Validator
    {
        return new class($maxInputVars, $maxInputNestingLevel) extends Validator {
            public function __construct(
                private readonly int $maxInputVars,
                private readonly int $maxInputNestingLevel
            ) {
            }

            protected function getMaxInputVars(): int
            {
                return $this->maxInputVars;
            }

            protected function getMaxInputNestingLevel(): int
            {
                return $this->maxInputNestingLevel;
            }
        };
    }

    private function getRequest(array $post): HttpRequest
    {
        $request = $this->createMock(HttpRequest::class);
        $request->method('getPost')->willReturn(new Parameters($post));

        return $request;
    }
}
