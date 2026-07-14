<?php

declare(strict_types=1);

namespace MageSuite\Pagination\Test\Unit\Block\Html;

class PaginationTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\MageSuite\Pagination\Block\Html\Pagination $block;

    /**
     * @var \Magento\Framework\App\RequestInterface|
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $requestDouble;

    protected function setUp(): void
    {
        $this->requestDouble = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->block = new \MageSuite\Pagination\Block\Html\Pagination(
            $this->objectManager->get(\Magento\Framework\View\Element\Template\Context::class),
            $this->requestDouble,
            $this->objectManager->get(\MageSuite\Pagination\Helper\Configuration::class),
            []
        );
    }

    public function testItReturnsCorrectPattern(): void
    {
        $expect = 'http://localhost/index.php/?p=[page]';
        $result = $this->block->getUrlPattern();

        $this->assertEquals($expect, $result);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getActions')]
    public function testItReturnsCorrectSwitcher(string $fullActionName, bool $expectedResult): void
    {
        $this->requestDouble->method('getFullActionName')->willReturn($fullActionName);
        $this->assertEquals($expectedResult, $this->block->hasInputSwitcher());
    }

    public function testIsShowPerPage(): void
    {
        $this->requestDouble->method('getFullActionName')->willReturn('review_product_listAjax');
        $this->assertFalse($this->block->isShowPerPage());
    }

    public static function getActions(): array
    {
        return [
            ['catalog_category_view', true],
            ['catalogsearch_advanced_result', true],
            ['catalogsearch_result_index', true],
            ['frontend_cache_warmup', true],
            ['brands_index_index', true],
            ['review_product_list', false],
            ['sales_order_item_pager', false]
        ];
    }

}
