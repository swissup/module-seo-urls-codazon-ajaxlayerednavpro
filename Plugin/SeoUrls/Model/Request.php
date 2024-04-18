<?php

namespace Swissup\SeoUrlsCodazonAjaxLayeredNavPro\Plugin\SeoUrls\Model;

use Swissup\SeoUrls\Helper\Filter as HelperFilter;
use Swissup\SeoUrls\Model\Category as SeoCategory;
use Swissup\SeoUrls\Model\Request as Subject;
use Swissup\SeoUrls\Model\ResourceModel\Category\View as CategoryView;

class Request
{
    private HelperFilter $filterHelper;
    private CategoryView $categoryView;
    private SeoCategory $seoCategory;

    public function __construct(
        CategoryView $categoryView,
        HelperFilter $filterHelper,
        SeoCategory $seoCategory
    ) {
        $this->categoryView = $categoryView;
        $this->filterHelper = $filterHelper;
        $this->seoCategory = $seoCategory;
    }

    public function afterFindoutCategoryId(
        Subject $subject,
        $result,
        $categorySeoLabel,
        $categoryId
    ) {
        if (!$categorySeoLabel) {
            return $result;
        }

        $category = $categoryId === null ?
            $this->filterHelper->getRootCategory() :
            $this->filterHelper->getCategoryById($categoryId);

        $children = $category->getChildrenCategories();
        $siblings = $category->getParentCategory()->getChildrenCategories();
        $this->categoryView->preloadData(
                array_merge(
                    $this->filterHelper->getIdsFromCategories($children),
                    $this->filterHelper->getIdsFromCategories($siblings)
                )
            );

        $options = [];
        foreach ([$children, $siblings] as $ch)
            foreach ($ch as $child) {
                if (!$child->getIsActive()) {
                    continue;
                }

                $label = $this->seoCategory->getStoreLabel($child);
                if (in_array($label, $options)) {
                    // this should not occur - poor category naming
                    // concatenate value to duplicated label
                    $label .= '-' . $child->getId();
                }

                $options[$child->getId()] = $label;
            }

        uasort($options, function ($a,$b){
            return strlen($b)-strlen($a);
        });

        $i = 0;
        $value = '';
        do {
            foreach ($options as $key => $option) {
                if (strpos($categorySeoLabel, $option) === 0) {
                    $value .= $key . ',';
                    $categorySeoLabel = str_replace(
                        $option,
                        '',
                        $categorySeoLabel
                    );
                    $categorySeoLabel = trim($categorySeoLabel, '-');
                    break;
                }
            }
            $i++;
        } while ($i < 100 && $categorySeoLabel);

        return rtrim($value, ',');
    }
}
