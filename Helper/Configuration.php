<?php

namespace MageSuite\Pagination\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_ACTION_PATHS = 'pagination/configuration/action_paths';

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function getActionPaths($storeId = null): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_ACTION_PATHS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (is_string($value)) {
            $value = $this->serializer->unserialize($value);
        }

        return $value;
    }
}
