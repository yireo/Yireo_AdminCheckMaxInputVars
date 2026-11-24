<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Test\Unit\Plugin;

use Magento\Backend\App\Request\BackendValidator;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Yireo\AdminCheckMaxInputVars\Plugin\BackendValidatorPlugin;
use Yireo\AdminCheckMaxInputVars\Validator\RequestValidator;

class BackendValidatorPluginTest extends TestCase
{
    public function testAfterValidateDelegatesToRequestValidator(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $action = $this->createMock(ActionInterface::class);

        $requestValidator = $this->createMock(RequestValidator::class);
        $requestValidator->expects($this->once())->method('validate')->with($request, $action);

        (new BackendValidatorPlugin($requestValidator))
            ->afterValidate($this->createMock(BackendValidator::class), null, $request, $action);
    }
}
