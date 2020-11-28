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

use Common\Base\Block\MediaBlock;
use Common\Blog\Helper\Url as UrlHelper;
use Common\Blog\Model\Post\Category;
use Common\Blog\Model\Post\Tag;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * @package Common\Blog
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_blog
 */
abstract class AbstractBlock extends MediaBlock
{
    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @param UrlHelper        $urlHelper
     * @param Template\Context $context
     * @param array            $data
     */
    public function __construct(
        UrlHelper $urlHelper,
        Template\Context $context,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param string      $dateTime
     * @param string|null $format
     * @return string
     * @throws LocalizedException
     */
    public function formatDateTime($dateTime, $format = null)
    {
        $format = $format ?: $this->_scopeConfig->getValue(
            'blog/general/date_time_format',
            ScopeInterface::SCOPE_WEBSITE,
            $this->_storeManager->getWebsite()
        );
        return $this->_localeDate->date($dateTime)->format($format);
    }

    /**
     * @param Category $category
     * @return string [base_url]/[blog_route]/[category_url_key]/
     * @throws NoSuchEntityException
     */
    public function getCategoryUrl($category)
    {
        return $this->urlHelper->getCategoryUrl($category);
    }

    /**
     * @param \Common\Blog\Model\Post $post
     * @return string [base_url]/[blog_route]/[post_url_key].html
     * @throws NoSuchEntityException
     */
    public function getPostUrl($post)
    {
        return $this->urlHelper->getPostUrl($post);
    }

    /**
     * @param Tag $tag
     * @return string [base_url]/[blog_route]/[tag_url_key]/
     * @throws NoSuchEntityException
     */
    public function getTagUrl($tag)
    {
        return $this->urlHelper->getTagUrl($tag);
    }
}
