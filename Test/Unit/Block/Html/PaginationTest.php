<?php

namespace MageSuite\Pagination\Test\Unit\Block\Html;

class PaginationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \MageSuite\Pagination\Block\Html\Pagination
     */
    private $block;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestDouble;

    public function setUp()
    {
        $this->requestDouble = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->block = new \MageSuite\Pagination\Block\Html\Pagination(
            $this->objectManager->get(\Magento\Framework\View\Element\Template\Context::class),
            $this->requestDouble,
            $this->objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class),
            $this->objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class),
            []
        );
    }

    public function testItReturnsCorrectPattern() {
        $expect = 'http://localhost/index.php/?p=[page]';
        $result = $this->block->getUrlPattern();

        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider getActions
     */
    public function testItReturnsCorrectSwitcher($fullActionName, $expectedResult){
        $this->requestDouble->method('getFullActionName')->willReturn($fullActionName);
        $this->assertEquals($expectedResult, $this->block->hasInputSwitcher());
    }

    public function testIsShowPerPage(){
        $this->requestDouble->method('getFullActionName')->willReturn('review_product_listAjax');
        $this->assertFalse($this->block->isShowPerPage());
    }

    public static function getActions()
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