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

namespace Common\Blog\Controller\Adminhtml\Tag;

use Common\Base\Controller\Adminhtml\AbstractAjaxAction;
use Common\Blog\Model\Post\Tag;
use Common\Blog\Model\ResourceModel\Post\Tag\Collection;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Search extends AbstractAjaxAction
{
    public const ADMIN_RESOURCE = 'Common_Blog::blog_tag';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $key = $this->getRequest()->getParam('searchKey');
        $page = $this->getRequest()->getParam('page');
        $pageSize = $this->getRequest()->getParam('limit');

        /* @var Collection $collection */
        $collection = $this->_objectManager->create(Collection::class);
        $collection->addFieldToFilter(Tag::FIELD_NAME, ['like' => $key . '%'])
            ->setPageSize($pageSize)
            ->setCurPage($page)
            ->addOrder(Tag::FIELD_NAME, Collection::SORT_ORDER_ASC);

        $options = [];
        foreach ($collection as $tag) {
            $options[] = [
                'label' => $tag->getData(Tag::FIELD_NAME),
                'value' => $tag->getId(),
                'path'  => $tag->getId(),
                'level' => 1
            ];
        }
        return $this->processResult(['options' => $options, 'total' => $collection->getSize()]);
    }
}
