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

namespace Common\Blog\Controller\Tag;

use Common\Blog\Api\TagRepositoryInterface;
use Common\Blog\Block\Post\Grid;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class View extends Action implements HttpGetActionInterface
{
    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @param TagRepositoryInterface $tagRepository
     * @param Context                $context
     */
    public function __construct(
        TagRepositoryInterface $tagRepository,
        Context $context
    ) {
        $this->tagRepository = $tagRepository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->tagRepository->get($id);

        $this->_view->loadLayout();

        /* @var Grid $block */
        if (($block = $this->_view->getLayout()->getBlock('blog.post.list'))) {
            $collection = $block->getPostCollection();
            $collection->getSelect()
                ->join(
                    $collection->getConnection()->getTableName('blog_post_tag'),
                    'id = post_id',
                    ['tag_id']
                )
                ->where('tag_id = ?', $id);
        }

        $this->_view->getPage()->getConfig()->getTitle()->set($model->getName());
        $this->_view->getPage()->getConfig()->setMetaTitle($model->getName());
        $this->_view->getPage()->getConfig()->setKeywords($model->getName());
        $this->_view->getPage()->getConfig()->setDescription($model->getName());

        $this->_view->renderLayout();
    }
}
