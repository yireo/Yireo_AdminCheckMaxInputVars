<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Validator;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Request\ValidatorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Phrase;
use Yireo\AdminCheckMaxInputVars\Exception\ValidationException;

class RequestValidator implements ValidatorInterface
{
    /**
     * @param Validator $validator
     * @param JsonFactory $resultJsonFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param Auth $auth
     */
    public function __construct(
        private readonly Validator $validator,
        private readonly JsonFactory $resultJsonFactory,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly RedirectInterface $redirect,
        private readonly Auth $auth
    ) {
    }

    /**
     * Block admin POST requests that reach the PHP input limits
     *
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @return void
     * @throws InvalidRequestException
     */
    public function validate(RequestInterface $request, ActionInterface $action): void
    {
        if (!$request instanceof HttpRequest || !$request->isPost()) {
            return;
        }

        if (!$this->auth->isLoggedIn()) {
            return;
        }

        $messages = $this->getMessages($request);
        if (empty($messages)) {
            return;
        }

        if ($request->isAjax()) {
            $data = ['error' => true, 'message' => implode(', ', array_map('strval', $messages))];
            throw new InvalidRequestException($this->resultJsonFactory->create()->setData($data), $messages);
        }

        $result = $this->resultRedirectFactory->create()->setUrl($this->redirect->getRefererUrl());
        throw new InvalidRequestException($result, $messages);
    }

    /**
     * Collect the messages of all failing checks
     *
     * @param HttpRequest $request
     * @return Phrase[]
     */
    private function getMessages(HttpRequest $request): array
    {
        $messages = [];

        try {
            $this->validator->handleMaxInputVars($request);
        } catch (ValidationException $validationException) {
            $messages[] = __('%1', $validationException->getMessage());
        }

        try {
            $this->validator->handleInputNestingLevel($request);
        } catch (ValidationException $validationException) {
            $messages[] = __('%1', $validationException->getMessage());
        }

        return $messages;
    }
}
