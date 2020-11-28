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

namespace Common\Blog\Model\Post\Comment;

use Common\Base\Model\AbstractDataProvider;
use Common\Blog\Api\PostRepositoryInterface;
use Common\Blog\Model\Post;
use Common\Blog\Model\Post\Comment;
use Common\Blog\Model\ResourceModel\Post\Comment as ResourceComment;
use Common\Blog\Model\ResourceModel\Post\Comment\Collection;
use Magento\Framework\UrlInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class DataProvider extends AbstractDataProvider
{
    protected $persistKey = 'blog_comment';

    /**
     * @inheritDoc
     */
    public function getData()
    {
        parent::getData();
        foreach (array_keys($this->loadedData) as $id) {
            $data = &$this->loadedData[$id]['data'];

            $urlBuilder = $this->objectManager->get(UrlInterface::class);

            $postRepository = $this->objectManager->get(PostRepositoryInterface::class);
            $post = $postRepository->get($data[Comment::FIELD_POST_ID]);

            $comment = $this->objectManager->create(Comment::class);
            $resourceComment = $this->objectManager->create(ResourceComment::class);
            $resourceComment->load($comment, $data[Comment::FIELD_PARENT_ID]);

            $data['post'] = sprintf(
                '<a target="_blank" href="%s">%s</a>',
                $urlBuilder->getUrl('blog/post/edit', ['id' => $data[Comment::FIELD_POST_ID]]),
                $post->getData(Post::FIELD_TITLE)
            );

            $data['parent'] = sprintf(
                '<div>%s</div><div><a target="_blank" href="%s">%s</a></div>',
                $comment->getData(Comment::FIELD_CONTENT),
                $urlBuilder->getUrl('blog/comment/edit', ['id' => $data[Comment::FIELD_PARENT_ID]]),
                __('Edit')
            );
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
