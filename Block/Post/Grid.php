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

use Common\Blog\Block\AbstractBlock;
use Common\Blog\Helper\Url as UrlHelper;
use Common\Blog\Model\Post;
use Common\Blog\Model\Post\Comment;
use Common\Blog\Model\ResourceModel\Post\Collection as PostCollection;
use Common\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Common\Blog\Model\ResourceModel\Post\Comment\Collection as CommentCollection;
use Common\Blog\Model\ResourceModel\Post\Comment\CollectionFactory as CommentCollectionFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Block\Html\Pager;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Grid extends AbstractBlock
{
    protected $mediaFolder = Post::MEDIA_FOLDER;

    /**
     * @var CommentCollectionFactory
     */
    protected $commentCollectionFactory;

    /**
     * @var array
     */
    protected $commentsTotal;
    /**
     * @var PostCollectionFactory
     */
    protected $postCollectionFactory;
    /**
     * @var Collection
     */
    protected $postCollection;
    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param PostCollectionFactory    $postCollectionFactory
     * @param UrlHelper                $urlHelper
     * @param Context                  $context
     * @param array                    $data
     */
    public function __construct(
        CommentCollectionFactory $commentCollectionFactory,
        PostCollectionFactory $postCollectionFactory,
        UrlHelper $urlHelper,
        Context $context,
        array $data = []
    ) {
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($urlHelper, $context, $data);
    }

    /**
     * @param int $postId
     * @return CommentCollection
     * @throws NoSuchEntityException
     */
    public function getComments($postId)
    {
        if ($this->commentsTotal === null) {
            $postIds = [];
            foreach ($this->getPostCollection() as $post) {
                $postIds[] = $post->getId();
            }

            /* @var CommentCollection $collection */
            $collection = $this->commentCollectionFactory->create();
            $collection->addFieldToFilter(Comment::FIELD_POST_ID, ['in' => $postIds])
                ->addFieldToFilter(Comment::FIELD_IS_ACTIVE, ['eq' => true]);
            $conn = $collection->getConnection();
            $select = $collection->getSelect()->reset(Select::COLUMNS)
                ->columns([Comment::FIELD_POST_ID, 'COUNT(*)']);
            $this->commentsTotal = $conn->fetchPairs($select);
        }
        return $this->commentsTotal[$postId] ?? 0;
    }

    /**
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostCollection()
    {
        if ($this->postCollection === null) {
            /* @var PostCollection $collection */
            $this->postCollection = $this->postCollectionFactory->create();
            $this->postCollection->addStoreFilter($this->_storeManager->getStore())
                ->addFieldToFilter(Post::FIELD_IS_ACTIVE, ['eq' => true])
                ->addOrder(Post::FIELD_CREATED_AT, PostCollection::SORT_ORDER_DESC);
        }
        return $this->postCollection;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /* @var Pager $pagerBlock */
        if (($pagerBlock = $this->getChildBlock('blog_post_pager'))) {
            $this->getPostCollection();
            $this->pageSize = $this->_scopeConfig->getValue(
                'blog/general/page_size',
                ScopeInterface::SCOPE_WEBSITE,
                $this->_storeManager->getWebsite()
            );
            $pagerBlock->setLimit($this->pageSize)
                ->setAvailableLimit([$this->pageSize])
                ->setCollection($this->postCollection)
                ->setShowAmounts($this->postCollection->getSize() > $this->pageSize);
        }
        return parent::_prepareLayout();
    }
}
