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

namespace Common\Blog\Model\Post;

use Common\Blog\Api\Data\CommentInterface;
use Common\Blog\Model\ResourceModel\Post\Comment as Resource;
use Magento\Framework\Model\AbstractModel;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Comment extends AbstractModel implements CommentInterface
{
    public const FIELD_IS_ACTIVE = 'is_active';
    public const FIELD_CONTENT = 'content';
    public const FIELD_CUSTOMER_ID = 'customer_id';
    public const FIELD_AUTHOR = 'author';
    public const FIELD_EMAIL = 'email';
    public const FIELD_POST_ID = 'post_id';
    public const FIELD_PARENT_ID = 'parent_id';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'updated_at';

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::FIELD_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::FIELD_IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function getPostId()
    {
        return $this->getData(self::FIELD_POST_ID);
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->getData(self::FIELD_PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::FIELD_CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getAuthor()
    {
        return $this->getData(self::FIELD_AUTHOR);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::FIELD_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::FIELD_UPDATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(Resource::class);
    }
}
