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
use Common\Blog\Model\Post\Comment;
use Common\Blog\Model\Post\CommentFactory;
use Common\Blog\Model\ResourceModel\Post\Comment as ResourceComment;
use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class PostComment extends Action implements HttpPostActionInterface
{
    /**
     * @var CommentFactory
     */
    protected $commentFactory;
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;
    /**
     * @var ResourceComment
     */
    protected $resourceComment;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param CommentFactory          $commentFactory
     * @param CustomerSession         $customerSession
     * @param PostRepositoryInterface $postRepository
     * @param ResourceComment         $resourceComment
     * @param ScopeConfigInterface    $scopeConfig
     * @param StoreManagerInterface   $storeManager
     * @param Context                 $context
     */
    public function __construct(
        CommentFactory $commentFactory,
        CustomerSession $customerSession,
        PostRepositoryInterface $postRepository,
        ResourceComment $resourceComment,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->commentFactory = $commentFactory;
        $this->customerSession = $customerSession;
        $this->postRepository = $postRepository;
        $this->resourceComment = $resourceComment;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        if (empty($postData[Comment::FIELD_POST_ID])) {
            return $this->_redirect('blog');
        }
        $postModel = $this->postRepository->get($postData[Comment::FIELD_POST_ID]);
        if (!$postModel->getId()) {
            return $this->_redirect('blog');
        }

        try {
            if ($this->customerSession->isLoggedIn()) {
                $postData[Comment::FIELD_CUSTOMER_ID] = $this->customerSession->getCustomerId();
                $postData[Comment::FIELD_AUTHOR] = $this->customerSession->getCustomer()->getName();
                $postData[Comment::FIELD_EMAIL] = $this->customerSession->getCustomer()->getEmail();
            }
            if ($this->scopeConfig->getValue(
                'blog/general/auto_approve_reply',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getWebsite()
            )) {
                $postData[Comment::FIELD_IS_ACTIVE] = true;
            }

            /* @var Comment $comment */
            $comment = $this->commentFactory->create();
            $this->resourceComment->save($comment->setData($postData));

            $this->messageManager->addSuccessMessage(__('Reply submitted.'));
            $result = ['success' => true];
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to reply.'));
            $result = ['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }

        /* @var Json $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultRedirect->setData($result);
    }
}
