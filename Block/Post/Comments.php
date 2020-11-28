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

namespace Common\Blog\Block\Post;

use Common\Blog\Api\PostRepositoryInterface;
use Common\Blog\Block\AbstractBlock;
use Common\Blog\Helper\Url as UrlHelper;
use Common\Blog\Model\Post;
use Common\Blog\Model\Post\Comment;
use Common\Blog\Model\ResourceModel\Post\Comment\Collection as CommentCollection;
use Common\Blog\Model\ResourceModel\Post\Comment\CollectionFactory as CommentCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Comments extends AbstractBlock
{
    /**
     * @var CommentCollectionFactory
     */
    protected $commentCollectionFactory;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param PostRepositoryInterface  $postRepository
     * @param ScopeConfigInterface     $scopeConfig
     * @param Session                  $session
     * @param UrlHelper                $urlHelper
     * @param Context                  $context
     * @param array                    $data
     */
    public function __construct(
        CommentCollectionFactory $commentCollectionFactory,
        PostRepositoryInterface $postRepository,
        ScopeConfigInterface $scopeConfig,
        Session $session,
        UrlHelper $urlHelper,
        Context $context,
        array $data = []
    ) {
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->postRepository = $postRepository;
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        parent::__construct($urlHelper, $context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $model = $this->getPost();
        if (!$model->getId()) {
            return;
        }

        /* @var CommentCollection $collection */
        $collection = $this->commentCollectionFactory->create();
        $collection->addFieldToFilter(Comment::FIELD_POST_ID, ['in' => $model->getId()])
            ->addFieldToFilter(Comment::FIELD_IS_ACTIVE, ['eq' => true])
            ->addOrder(Comment::FIELD_CREATED_AT, CommentCollection::SORT_ORDER_ASC);

        $commentGroup = [];
        foreach ($collection as $comment) {
            if (!isset($commentGroup[$comment->getData(Comment::FIELD_PARENT_ID)])) {
                $commentGroup[$comment->getData(Comment::FIELD_PARENT_ID)] = [];
            }
            $comment->setData(
                Comment::FIELD_CREATED_AT,
                $this->formatDateTime($comment->getData(Comment::FIELD_CREATED_AT))
            );
            $commentGroup[$comment->getData(Comment::FIELD_PARENT_ID)][] = $comment;
        }

        $isLoggedIn = $this->session->isLoggedIn();
        $this->jsLayout['components'] = [
            'blog-comments' => [
                'component' => 'Common_Blog/js/comments',
                'config'    => [
                    'postId'          => $this->getPost()->getId(),
                    'comments'        => $this->getCommentTree($commentGroup),
                    'postCommentUrl'  => $this->getUrl('blog/post/postComment'),
                    'defaultAuthor'   => $isLoggedIn ? $this->session->getCustomer()->getName() : null,
                    'defaultEmail'    => $isLoggedIn ? $this->session->getCustomer()->getEmail() : null,
                    'allowGuestReply' => !!$this->scopeConfig->getValue(
                        'blog/general/allow_guest_reply',
                        ScopeInterface::SCOPE_WEBSITE,
                        $this->_storeManager->getWebsite()
                    ),
                ]
            ]
        ];
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        $postId = $this->getRequest()->getParam('id');
        return $this->postRepository->get($postId);
    }

    /**
     * @param array
     * @param int $parentId
     * @return array
     * @throws LocalizedException
     */
    protected function getCommentTree($commentGroup, $parentId = 0)
    {
        $comments = [];
        if (isset($commentGroup[$parentId])) {
            foreach ($commentGroup[$parentId] as $comment) {
                $comment->setData('children', $this->getCommentTree($commentGroup, $comment->getId()));
                $comments[] = $comment->getData();
            }
        }
        return $comments;
    }
}
