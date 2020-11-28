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

namespace Common\Blog\Controller\Post;

use Common\Blog\Api\PostRepositoryInterface;
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
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @param PostRepositoryInterface $postRepository
     * @param Context                 $context
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        Context $context
    ) {
        $this->postRepository = $postRepository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->postRepository->get($id);

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set($model->getTitle());
        $this->_view->getPage()->getConfig()->setMetaTitle($model->getMetaTitle());
        $this->_view->getPage()->getConfig()->setKeywords($model->getMetaKeywords());
        $this->_view->getPage()->getConfig()->setDescription($model->getMetaDescription());
        $this->_view->renderLayout();
    }
}
