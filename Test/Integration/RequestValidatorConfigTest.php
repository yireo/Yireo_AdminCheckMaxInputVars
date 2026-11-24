<?php

declare(strict_types=1);

namespace Yireo\AdminCheckMaxInputVars\Test\Integration;

use Magento\Framework\App\Request\CompositeValidator;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\AdminCheckMaxInputVars\Validator\RequestValidator;

/**
 * @magentoAppArea adminhtml
 */
class RequestValidatorConfigTest extends TestCase
{
    private const ITEM_NAME = 'yireo_admin_check_max_input_vars';

    public function testModuleIsEnabled(): void
    {
        $moduleList = Bootstrap::getObjectManager()->get(ModuleListInterface::class);
        $this->assertTrue($moduleList->has('Yireo_AdminCheckMaxInputVars'));
    }

    public function testValidatorRunsAfterCsrfValidator(): void
    {
        $validators = $this->getValidators('RequestValidator');
        $this->assertArrayHasKey(self::ITEM_NAME, $validators);
        $this->assertSame(RequestValidator::class, $validators[self::ITEM_NAME]['instance']);

        $names = array_keys($validators);
        $this->assertContains('csrf_validator', $names);
        $this->assertGreaterThan(
            array_search('csrf_validator', $names, true),
            array_search(self::ITEM_NAME, $names, true)
        );
        $this->assertSame(self::ITEM_NAME, end($names));
    }

    public function testValidatorIsNotAddedToEveryCompositeValidator(): void
    {
        $this->assertArrayNotHasKey(self::ITEM_NAME, $this->getValidators(CompositeValidator::class));
    }

    private function getValidators(string $type): array
    {
        $config = Bootstrap::getObjectManager()->get(ConfigInterface::class);
        $arguments = $config->getArguments($type);

        return (array)($arguments['validators'] ?? []);
    }
}
