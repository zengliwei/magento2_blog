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

use Common\Base\Model\AbstractDataProvider;
use Common\Blog\Model\Post;
use Common\Blog\Model\ResourceModel\Post\Collection;
use Common\Blog\Model\ResourceModel\Post\Tag\Collection as TagCollection;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class DataProvider extends AbstractDataProvider
{
    protected $persistKey = 'blog_post';

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        $this->meta = ['general' => ['children' => []]];
        $this->customizeTagOptions();
        return parent::getMeta();
    }

    private function customizeTagOptions()
    {
        $tagIds = [];
        foreach ($this->collection->getItems() as $model) {
            $tagIds = array_merge($tagIds, $model->getTagIds());
        }
        $tagIds = array_unique($tagIds);

        /* @var TagCollection $tagCollection */
        $tagCollection = $this->objectManager->create(TagCollection::class);
        $tagCollection->addFieldToFilter('id', ['in' => $tagIds]);

        $options = [];
        foreach ($tagCollection as $tag) {
            $options[] = ['label' => $tag->getData(Tag::FIELD_NAME), 'value' => $tag->getId()];
        }
        $this->meta['general']['children']['tag_ids'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => $options
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        parent::getData();
        foreach (array_keys($this->loadedData) as $id) {
            $data = &$this->loadedData[$id]['data'];
            if (!empty($data['image']) && !is_array($data['image'])) {
                $data['image'] = $this->prepareFileData($data, 'image', Post::MEDIA_FOLDER);
            }
        }
        return $this->loadedData;
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->initCollection(Collection::class);
    }
}
