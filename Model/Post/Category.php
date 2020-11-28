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

use Common\Blog\Api\Data\CategoryInterface;
use Common\Blog\Model\AbstractStoreModel;
use Common\Blog\Model\ResourceModel\Post\Category as Resource;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Category extends AbstractStoreModel implements CategoryInterface
{
    public const FIELD_TITLE = 'name';
    public const FIELD_IS_ACTIVE = 'is_active';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_PARENT_ID = 'parent_id';
    public const FIELD_STORE_IDS = 'store_ids';
    public const FIELD_SORT_ORDER = 'sort_order';
    public const FIELD_URL_KEY = 'url_key';
    public const FIELD_META_TITLE = 'meta_title';
    public const FIELD_META_DESCRIPTION = 'meta_description';
    public const FIELD_META_KEYWORDS = 'meta_keywords';

    public const MEDIA_FOLDER = 'blog/category';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::FIELD_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::FIELD_DESCRIPTION);
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
    public function getUrlKey()
    {
        return $this->getData(self::FIELD_URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        return $this->getData(self::FIELD_META_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::FIELD_META_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->getData(self::FIELD_META_DESCRIPTION);
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
    public function getSortOrder()
    {
        return $this->getData(self::FIELD_SORT_ORDER);
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(Resource::class);
    }
}
