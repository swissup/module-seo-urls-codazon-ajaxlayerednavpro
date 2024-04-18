<?php

namespace Swissup\SeoUrlsCodazonAjaxLayeredNavPro\Plugin\Category;

use Magento\Catalog\Controller\Category\View as Subject;
use Swissup\SeoUrlsCodazonAjaxLayeredNavPro\Plugin\Controller\View as AbstractView;

class View extends AbstractView
{

    public function afterExecute(
        Subject $subject,
        $result
    ) {
        if ($this->helper->isSeoUrlsEnabled()) {
            $this->changeUpdatedUrl($result, $subject->getRequest());
        }

        return $result;
    }
}
