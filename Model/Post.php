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

namespace Common\Blog\Model;

use Common\Blog\Api\Data\PostInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Post extends AbstractStoreModel implements PostInterface
{
    public const FIELD_TITLE = 'title';
    public const FIELD_IS_ACTIVE = 'is_active';
    public const FIELD_SUMMARY = 'summary';
    public const FIELD_CONTENT = 'content';
    public const FIELD_CATEGORY_ID = 'category_id';
    public const FIELD_STORE_IDS = 'store_ids';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'updated_at';
    public const FIELD_URL_KEY = 'url_key';
    public const FIELD_META_TITLE = 'meta_title';
    public const FIELD_META_DESCRIPTION = 'meta_description';
    public const FIELD_META_KEYWORDS = 'meta_keywords';

    public const MEDIA_FOLDER = 'blog/post';

    public const KEY_TAG_IDS = 'tag_ids';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::FIELD_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getSummary()
    {
        return $this->getData(self::FIELD_SUMMARY);
    }

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
    public function getCategoryId()
    {
        return $this->getData(self::FIELD_CATEGORY_ID);
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
    public function afterSave()
    {
        parent::afterSave();

        if ($this->hasData(self::KEY_TAG_IDS) && is_array($this->getData(self::KEY_TAG_IDS))) {
            $conn = $this->getResource()->getConnection();
            $tbl = $conn->getTableName('blog_post_tag');
            $conn->delete($tbl, ['post_id = ?' => $this->getId()]);
            $postId = $this->getId();
            $conn->insertArray(
                $tbl,
                ['post_id', 'tag_id'],
                array_map(
                    function ($tagId) use ($postId) {
                        return [$postId, $tagId];
                    },
                    $this->getData(self::KEY_TAG_IDS)
                )
            );
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function getTagIds()
    {
        if (!$this->hasData(self::KEY_TAG_IDS)) {
            $conn = $this->getResource()->getConnection();
            $select = $conn->select()
                ->from($conn->getTableName('blog_post_tag'), ['tag_id'])
                ->where('post_id = ?', $this->getId());
            $this->setData(self::KEY_TAG_IDS, $conn->fetchCol($select));
        }
        return $this->getData(self::KEY_TAG_IDS);
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Post::class);
    }
}
