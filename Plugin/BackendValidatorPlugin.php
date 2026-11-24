<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Plugin;

use Magento\Backend\App\Request\BackendValidator;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Yireo\AdminCheckMaxInputVars\Validator\RequestValidator;

/**
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
class BackendValidatorPlugin
{
    /**
     * @param RequestValidator $requestValidator
     */
    public function __construct(
        private readonly RequestValidator $requestValidator
    ) {
    }

    /**
     * Validate the PHP input limits once the admin form key and secret key are valid
     *
     * @param BackendValidator $subject
     * @param mixed $result
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @return void
     * @throws InvalidRequestException
     */
    public function afterValidate(
        BackendValidator $subject,
        mixed $result,
        RequestInterface $request,
        ActionInterface $action
    ): void {
        $this->requestValidator->validate($request, $action);
    }
}
