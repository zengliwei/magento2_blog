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

namespace Common\Blog\Controller;

use Common\Blog\Helper\Url as UrlHelper;
use Common\Blog\Model\Post;
use Common\Blog\Model\Post\CategoryFactory;
use Common\Blog\Model\Post\TagFactory;
use Common\Blog\Model\PostFactory;
use Common\Blog\Model\ResourceModel\Post as ResourcePost;
use Common\Blog\Model\ResourceModel\Post\Category as ResourceCategory;
use Common\Blog\Model\ResourceModel\Post\Tag as ResourceTag;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var ResourcePost
     */
    private $resourcePost;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceTag
     */
    private $resourceTag;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ResourceCategory
     */
    private $resourceCategory;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param ActionFactory         $actionFactory
     * @param PostFactory           $postFactory
     * @param ResourcePost          $resourcePost
     * @param CategoryFactory       $categoryFactory
     * @param ResourceCategory      $resourceCategory
     * @param TagFactory            $tagFactory
     * @param ResourceTag           $resourceTag
     * @param StoreManagerInterface $storeManager
     * @param UrlHelper             $urlHelper
     */
    public function __construct(
        ActionFactory $actionFactory,
        PostFactory $postFactory,
        ResourcePost $resourcePost,
        CategoryFactory $categoryFactory,
        ResourceCategory $resourceCategory,
        TagFactory $tagFactory,
        ResourceTag $resourceTag,
        StoreManagerInterface $storeManager,
        UrlHelper $urlHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->postFactory = $postFactory;
        $this->resourcePost = $resourcePost;
        $this->tagFactory = $tagFactory;
        $this->resourceTag = $resourceTag;
        $this->categoryFactory = $categoryFactory;
        $this->resourceCategory = $resourceCategory;
        $this->storeManager = $storeManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @inheritDoc
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $path = explode('/', $identifier);
        $parts = count($path);
        if ($parts < 2 || $path[0] != $this->urlHelper->getRouteName($this->storeManager->getWebsite())) {
            return;
        }

        // match post
        if (preg_match('/\\.html$/', $identifier)) {
            /* @var Post $post */
            $post = $this->postFactory->create();
            $this->resourcePost->load(
                $post,
                substr($path[$parts - 1], 0, strrpos($path[$parts - 1], '.html')),
                Post::FIELD_URL_KEY
            );
            if ($post->getId() && $post->isInStore()) {
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                    ->setModuleName('blog')
                    ->setControllerName('post')
                    ->setActionName('view')
                    ->setParam('id', $post->getId());
                return $this->actionFactory->create(Forward::class);
            }
            return;
        }

        // match tag
        if ($parts == 3 & $path[1] == $this->urlHelper->getTagRouteName($this->storeManager->getWebsite())) {
            $tag = $this->tagFactory->create();
            $this->resourceTag->load($tag, $path[2], Post\Tag::FIELD_URL_KEY);
            if ($tag->getId() && $tag->getData(Post\Tag::FIELD_IS_ACTIVE)) {
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                    ->setModuleName('blog')
                    ->setControllerName('tag')
                    ->setActionName('view')
                    ->setParam('id', $tag->getId());
                return $this->actionFactory->create(Forward::class);
            }
            return;
        }

        // match category
        $category = $this->categoryFactory->create();
        $this->resourceCategory->load($category, $path[$parts - 1], Post\Category::FIELD_URL_KEY);
        if ($category->getId() && $category->getData(Post\Category::FIELD_IS_ACTIVE) && $category->isInStore()) {
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                ->setModuleName('blog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $category->getId());
            return $this->actionFactory->create(Forward::class);
        }
    }
}
