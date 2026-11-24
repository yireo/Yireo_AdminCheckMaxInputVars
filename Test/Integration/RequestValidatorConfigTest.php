<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Test\Integration;

use Magento\Backend\App\Request\BackendValidator;
use Magento\Framework\App\Request\CompositeValidator;
use Magento\Framework\App\Request\ValidatorInterface;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yireo\AdminCheckMaxInputVars\Plugin\BackendValidatorPlugin;
use Yireo\AdminCheckMaxInputVars\Validator\RequestValidator;

/**
 * @magentoAppArea adminhtml
 */
class RequestValidatorConfigTest extends TestCase
{
    public function testModuleIsEnabled(): void
    {
        $moduleList = Bootstrap::getObjectManager()->get(ModuleListInterface::class);
        $this->assertTrue($moduleList->has('Yireo_AdminCheckMaxInputVars'));
    }

    public function testCoreRequestValidatorsAreKept(): void
    {
        $validator = Bootstrap::getObjectManager()->create(ValidatorInterface::class);
        $this->assertInstanceOf(CompositeValidator::class, $validator);

        $validators = (new ReflectionProperty($validator, 'validators'))->getValue($validator);
        $this->assertArrayHasKey('csrf_validator', $validators);
        $this->assertArrayHasKey('http_method_validator', $validators);
        $this->assertInstanceOf(BackendValidator::class, $validators['csrf_validator']);

        foreach ($validators as $item) {
            $this->assertNotInstanceOf(RequestValidator::class, $item);
        }
    }

    public function testPluginIsRegisteredOnBackendValidator(): void
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginCode = 'Yireo_AdminCheckMaxInputVars::validateInputLimits';
        $next = $pluginList->getNext(BackendValidator::class, 'validate');
        $this->assertContains($pluginCode, (array)($next[DefinitionInterface::LISTENER_AFTER] ?? []));
        $plugin = $pluginList->getPlugin(BackendValidator::class, $pluginCode);
        $this->assertInstanceOf(BackendValidatorPlugin::class, $plugin);
    }
}
