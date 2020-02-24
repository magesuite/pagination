<?php

namespace MageSuite\Pagination\Block\Html;

class Pagination extends \Magento\Theme\Block\Html\Pager
{

    const PAGER_CONFIGURATION_ACTIONS_PATH = 'pagination/configuration/action_paths';
    const AJAX_REVIEW_ACTION_PATH = 'review_product_listAjax';
    const FULL_CATEGORY_ACTION_NAME = 'catalog_category_view';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    public function getPagerUrl($params = [])
    {
        if ($this->request->getFullActionName() != self::FULL_CATEGORY_ACTION_NAME) {
            return parent::getPagerUrl($params);
        }

        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;

        $paginationParam = $this->getPageVarName();

        $url = $this->getUrl($this->getPath(), $urlParams);

        if (isset($params[$paginationParam]) && $params[$paginationParam] == 1) {
            $url = $this->removePaginationParamForFirstPageUrl($url);
        }

        return $url;
    }

    public function getUrlPattern()
    {
        $pattern = $this->getPagerUrl([$this->getPageVarName() => 'page']);
        $pattern = str_replace('p=page', 'p=[page]', $pattern);

        return $this->escapeHtml($pattern);
    }

    public function hasInputSwitcher()
    {
        $actions = $this->getActionsWithInputSwitcher();
        $requestName = $this->request->getFullActionName();

        return in_array($requestName, $actions);
    }

    public function isShowPerPage()
    {
        if ($this->isAjaxReviewAction()) {
            return false;
        }

        return parent::isShowPerPage();
    }

    private function isAjaxReviewAction()
    {
        return $this->request->getFullActionName() === self::AJAX_REVIEW_ACTION_PATH;
    }

    private function getActionsWithInputSwitcher()
    {
        $actions = [];

        $config = $this->scopeConfig->getValue(self::PAGER_CONFIGURATION_ACTIONS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $configArray = $this->serializer->unserialize($config);
        foreach ($configArray as $item) {
            $actions[] = $item['path'];
        }

        return $actions;
    }

    private function removePaginationParamForFirstPageUrl($url)
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
