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

namespace Common\Blog\Block;

use Common\Blog\Api\Data\PostInterface;
use Common\Blog\Api\PostRepositoryInterface;
use Common\Blog\Helper\Url as UrlHelper;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
class Post extends AbstractBlock
{
    protected $mediaFolder = \Common\Blog\Model\Post::MEDIA_FOLDER;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @param FilterProvider          $filterProvider
     * @param PostRepositoryInterface $postRepository
     * @param UrlHelper               $urlHelper
     * @param Template\Context        $context
     * @param array                   $data
     */
    public function __construct(
        FilterProvider $filterProvider,
        PostRepositoryInterface $postRepository,
        UrlHelper $urlHelper,
        Template\Context $context,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->postRepository = $postRepository;
        parent::__construct($urlHelper, $context, $data);
    }

    /**
     * @param string $content
     * @return string
     * @throws \Exception
     */
    public function filterContent($content)
    {
        return $this->filterProvider->getPageFilter()->filter($content);
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->postRepository->get($id);
    }
}
