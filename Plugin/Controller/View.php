<?php

namespace Swissup\SeoUrlsCodazonAjaxLayeredNavPro\Plugin\Controller;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\App\RequestInterface;
use Swissup\SeoUrls\Helper\Data as Helper;
use Swissup\SeoUrls\Model\Url\Filter as UrlBuilder;

abstract class View
{
    protected Helper $helper;
    protected Serializer $serializer;
    protected UrlBuilder $urlBuilder;

    public function __construct(
        Helper $helper,
        Serializer $serializer,
        UrlBuilder $urlBuilder
    ) {
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->urlBuilder = $urlBuilder;
    }

    public function changeUpdatedUrl(
        $resultJson,
        RequestInterface $request
    ) {
        if ($resultJson instanceof Json) {
            $refProperty = new \ReflectionProperty($resultJson, 'json');
            $refProperty->setAccessible(true);

            try {
                $json = $this->serializer->unserialize(
                    $refProperty->getValue($resultJson)
                );
            } catch (\InvalidArgumentException $e) {
                return $resultJson;
            }

            $json['updated_url'] = $this->urlBuilder->getUrl(
                '*/*/*',
                [
                    '_current' => true,
                    '_use_rewrite' => true,
                    '_query' => $request->getQueryValue()
                ]
            );

            $resultJson->setData($json);
        }
    }
}
