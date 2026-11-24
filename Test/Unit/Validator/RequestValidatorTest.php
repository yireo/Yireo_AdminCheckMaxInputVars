<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Test\Unit\Validator;

use Laminas\Stdlib\Parameters;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use PHPUnit\Framework\TestCase;
use Yireo\AdminCheckMaxInputVars\Validator\RequestValidator;
use Yireo\AdminCheckMaxInputVars\Validator\Validator;

class RequestValidatorTest extends TestCase
{
    private const TOO_MANY_VARS = ['a' => 1, 'b' => 2, 'c' => 3];

    public function testSkipsNonHttpRequest(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getRequestValidator()->validate(
            $this->createMock(RequestInterface::class),
            $this->createMock(ActionInterface::class)
        );
    }

    public function testSkipsGetRequest(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getRequestValidator()->validate(
            $this->getRequest(self::TOO_MANY_VARS, false),
            $this->createMock(ActionInterface::class)
        );
    }

    public function testSkipsWhenNotLoggedIn(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getRequestValidator(false)->validate(
            $this->getRequest(self::TOO_MANY_VARS),
            $this->createMock(ActionInterface::class)
        );
    }

    public function testPassesWithinLimits(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getRequestValidator()->validate(
            $this->getRequest(['a' => 1]),
            $this->createMock(ActionInterface::class)
        );
    }

    public function testAjaxRequestReturnsJsonError(): void
    {
        try {
            $this->getRequestValidator()->validate(
                $this->getRequest(self::TOO_MANY_VARS, true, true),
                $this->createMock(ActionInterface::class)
            );
        } catch (InvalidRequestException $exception) {
            $this->assertInstanceOf(Json::class, $exception->getReplaceResult());
            $this->assertCount(1, $exception->getMessages());
            return;
        }

        $this->fail('Expected InvalidRequestException');
    }

    /**
     * @dataProvider getActionNames
     */
    public function testRegularRequestIsBlockedWithRedirect(string $actionName): void
    {
        try {
            $this->getRequestValidator()->validate(
                $this->getRequest(self::TOO_MANY_VARS, true, false, $actionName),
                $this->createMock(ActionInterface::class)
            );
        } catch (InvalidRequestException $exception) {
            $this->assertInstanceOf(Redirect::class, $exception->getReplaceResult());
            $this->assertStringContainsString('max_input_vars', (string)$exception->getMessages()[0]);
            return;
        }

        $this->fail('Expected InvalidRequestException');
    }

    public static function getActionNames(): array
    {
        return [['save'], ['saverole'], ['massDelete']];
    }

    private function getRequestValidator(bool $loggedIn = true): RequestValidator
    {
        $validator = new class extends Validator {
            protected function getMaxInputVars(): int
            {
                return 3;
            }

            protected function getMaxInputNestingLevel(): int
            {
                return 64;
            }
        };

        $json = $this->createMock(Json::class);
        $json->method('setData')->willReturnSelf();
        $jsonFactory = $this->createMock(JsonFactory::class);
        $jsonFactory->method('create')->willReturn($json);

        $redirectResult = $this->createMock(Redirect::class);
        $redirectResult->method('setUrl')->willReturnSelf();
        $redirectFactory = $this->createMock(RedirectFactory::class);
        $redirectFactory->method('create')->willReturn($redirectResult);

        $redirect = $this->createMock(RedirectInterface::class);
        $redirect->method('getRefererUrl')->willReturn('https://example.test/admin/');

        $auth = $this->createMock(Auth::class);
        $auth->method('isLoggedIn')->willReturn($loggedIn);

        return new RequestValidator($validator, $jsonFactory, $redirectFactory, $redirect, $auth);
    }

    private function getRequest(
        array $post,
        bool $isPost = true,
        bool $isAjax = false,
        string $actionName = 'save'
    ): HttpRequest {
        $request = $this->createMock(HttpRequest::class);
        $request->method('getPost')->willReturn(new Parameters($post));
        $request->method('isPost')->willReturn($isPost);
        $request->method('isAjax')->willReturn($isAjax);
        $request->method('getActionName')->willReturn($actionName);

        return $request;
    }
}
