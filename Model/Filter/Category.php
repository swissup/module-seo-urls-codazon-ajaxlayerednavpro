<?php

namespace Swissup\SeoUrlsCodazonAjaxLayeredNavPro\Model\Filter;

use Swissup\SeoUrls\Model\Filter\Category as OriginalCategory;

class Category extends OriginalCategory {
    public function getOptions()
    {
        $category = $this->getCategory();
        if ((null !== $category)
            && !$this->hasData("options_{$category->getId()}")
        ) {
            $children = $category->getChildrenCategories();
            $siblings = $category->getParentCategory()->getChildrenCategories();
            $this->categoryView->preloadData(
                array_merge(
                    $this->helper->getIdsFromCategories($children),
                    $this->helper->getIdsFromCategories($siblings)
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

                    // option prefix is path to current category filter from current category
                    $options[$child->getId()] = $this->getOptionPrefix() . $label;
                }

            $this->setData("options_{$category->getId()}", $options);
        }

        return $this->getData("options_{$category->getId()}");
    }

    protected function getOptionPrefix()
    {
        $category = $this->getCategory();
        if (!$this->hasData("option_prefix_{$category->getId()}")) {
            $this->setData(
                "option_prefix_{$category->getId()}",
                ''
            );
        }

        return $this->getData("option_prefix_{$category->getId()}");
    }
}
