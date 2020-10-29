<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Validator;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Request\ValidatorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\ManagerInterface;
use Yireo\AdminCheckMaxInputVars\Exception\ValidationException;

class RequestValidator implements ValidatorInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * RequestValidator constructor.
     * @param Validator $validator
     * @param JsonFactory $resultJsonFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Validator $validator,
        JsonFactory $resultJsonFactory,
        ManagerInterface $messageManager
    ) {
        $this->validator = $validator;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @throws ValidationException
     */
    public function validate(RequestInterface $request, ActionInterface $action): void
    {
        $messages = [];

        try {
            $this->validator->handleMaxInputVars($request);
        } catch (ValidationException $validationException) {
            $messages[] = $validationException->getMessage();
        }

        try {
            $this->validator->handleInputNestingLevel($request);
        } catch (ValidationException $validationException) {
            $messages[] = $validationException->getMessage();
        }

        if (empty($messages)) {
            return;
        }

        if ($this->handleAjaxCalls($request, $action, $messages)) {
            return;
        }

        if ($this->handleMessages($request, $action, $messages)) {
            return;
        }

        $this->handleOtherCalls($messages);
    }

    /**
     * @param RequestInterface $request
     * @param ActionInterface $action
     * @param array $messages
     * @return false
     * @throws InvalidRequestException
     */
    private function handleAjaxCalls(RequestInterface $request, ActionInterface $action, array $messages)
    {
        if (!$request->isAjax()) {
            return false;
        }

        $data = ['error' => true, 'message' => implode(', ', $messages)];
        $result = $this->resultJsonFactory->create()->setData($data);
        throw new InvalidRequestException($result, $messages);
    }

    /**
     * @param array $messages
     * @return bool
     */
    private function handleMessages(RequestInterface $request, ActionInterface $action, array $messages)
    {
        $allowedActions = ['save'];
        if (!in_array($request->getActionName(), $allowedActions)) {
            return false;
        }

        foreach ($messages as $message) {
            $this->messageManager->addWarningMessage($message);
        }

        return true;
    }

    /**
     * @param array $messages
     */
    private function handleOtherCalls(array $messages)
    {
    }
}
