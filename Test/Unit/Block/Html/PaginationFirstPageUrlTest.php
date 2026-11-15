<?php

declare(strict_types=1);

namespace MageSuite\Pagination\Test\Unit\Block\Html;

class PaginationFirstPageUrlTest extends \PHPUnit\Framework\TestCase
{
    protected \Magento\Framework\App\Request\Http $request;
    protected \MageSuite\Pagination\Block\Html\Pagination $block;

    protected function setUp(): void
    {
        $this->request = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->request->method('getFullActionName')->willReturn('catalog_category_view');

        $urlBuilder = $this->createStub(\Magento\Framework\UrlInterface::class);
        $urlBuilder->method('getUrl')->willReturn('http://localhost/women.html?color=red&product_list_order=price&p=1');

        $context = $this->createStub(\Magento\Framework\View\Element\Template\Context::class);
        $context->method('getUrlBuilder')->willReturn($urlBuilder);
        $context->method('getRequest')->willReturn($this->request);

        $this->block = new \MageSuite\Pagination\Block\Html\Pagination(
            $context,
            $this->request,
            $this->createStub(\MageSuite\Pagination\Helper\Configuration::class),
            []
        );
    }

    public function testFirstPageUrlKeepsOtherQueryParams(): void
    {
        $this->request->method('getQuery')->willReturn(new \Laminas\Stdlib\Parameters([
            'color' => 'red',
            'product_list_order' => 'price',
            'p' => '3'
        ]));

        $this->assertSame(
            'http://localhost/women.html?color=red&product_list_order=price',
            $this->block->getPagerUrl(['p' => 1])
        );
    }

    public function testFirstPageUrlWithoutOtherQueryParamsHasNoQueryString(): void
    {
        $this->request->method('getQuery')->willReturn(new \Laminas\Stdlib\Parameters(['p' => '3']));

        $this->assertSame('http://localhost/women.html', $this->block->getPagerUrl(['p' => 1]));
    }
}
