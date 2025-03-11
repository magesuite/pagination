<?php

declare(strict_types=1);

namespace MageSuite\Pagination\Block\Html;

class Pagination extends \Magento\Theme\Block\Html\Pager
{
    protected const AJAX_REVIEW_ACTION_PATH = 'review_product_listAjax';

    protected const FULL_CATEGORY_ACTION_NAME = 'catalog_category_view';

    protected const FLAG_USE_AJAX_REVIEW_URL = 'use_ajax_review_url';

    protected \Magento\Framework\App\RequestInterface $request;

    protected \MageSuite\Pagination\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\Pagination\Helper\Configuration $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->configuration = $configuration;
    }

    public function getPagerUrl($params = []): string
    {
        if ($this->getData(self::FLAG_USE_AJAX_REVIEW_URL)) {
            return $this->getUrl('review/product/listAjax', [
                'id' => (int) $this->getRequest()->getParam('id'),
                '_query' => $params
            ]);
        }

        if ($this->request->getFullActionName() != self::FULL_CATEGORY_ACTION_NAME) {
            return parent::getPagerUrl($params);
        }

        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;
        $url = $this->getUrl($this->getPath(), $urlParams);

        $paginationParam = $this->getPageVarName();
        if (isset($params[$paginationParam]) && $params[$paginationParam] == 1) {
            $url = $this->removePaginationParamForFirstPageUrl($url);
        }

        return $url;
    }

    public function getUrlPattern(): string
    {
        $pattern = $this->getPagerUrl([$this->getPageVarName() => 'page']);
        $pattern = str_replace('p=page', 'p=[page]', $pattern);

        return $this->escapeHtml($pattern);
    }

    public function hasInputSwitcher(): bool
    {
        $actions = $this->getActionsWithInputSwitcher();
        $requestName = $this->request->getFullActionName();

        return in_array($requestName, $actions);
    }

    public function isShowPerPage(): bool
    {
        if ($this->isAjaxReviewAction()) {
            return false;
        }

        return parent::isShowPerPage();
    }

    private function isAjaxReviewAction(): bool
    {
        return $this->request->getFullActionName() === self::AJAX_REVIEW_ACTION_PATH || $this->getData(self::FLAG_USE_AJAX_REVIEW_URL);
    }

    private function getActionsWithInputSwitcher(): array
    {
        $actions = [];
        $configArray = $this->configuration->getActionPaths();

        foreach ($configArray as $item) {
            $actions[] = $item['path'];
        }

        return $actions;
    }

    private function removePaginationParamForFirstPageUrl(string $url): string
    {
        $query = get_object_vars($this->request->getQuery());
        $url = strtok($url, '?');
        $paginationParam = $this->getPageVarName();
        unset($query[$paginationParam]);
        $query = http_build_query($query);

        if (empty($query)) {
            return $url;
        }

        return $url . '?' . $query;
    }
}
