<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Common\Blog\Block\Widget;

use Common\Blog\Block\AbstractBlock;
use Common\Blog\Helper\Url as UrlHelper;
use Common\Blog\Model\Post\Tag;
use Common\Blog\Model\ResourceModel\Post\Tag\Collection;
use Common\Blog\Model\ResourceModel\Post\Tag\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Tags extends AbstractBlock
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param UrlHelper         $urlHelper
     * @param Context           $context
     * @param array             $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        UrlHelper $urlHelper,
        Context $context,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($urlHelper, $context, $data);
    }

    /**
     * @return Collection
     */
    public function getTagCollection()
    {
        if ($this->collection === null) {
            /* @var Collection $collection */
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter(Tag::FIELD_IS_ACTIVE, ['eq' => true])
                ->addOrder(Tag::FIELD_NAME, Collection::SORT_ORDER_ASC);

            $this->collection->getSelect()
                ->join(
                    $this->collection->getConnection()->getTableName('blog_post_tag'),
                    'id = tag_id',
                    ['posts' => 'COUNT(post_id)']
                )
                ->group('tag_id');
        }
        return $this->collection;
    }
}
