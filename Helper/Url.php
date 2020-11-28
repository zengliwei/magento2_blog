<?php

namespace Common\Blog\Helper;

use Common\Blog\Model\Post;
use Common\Blog\Model\Post\Category;
use Common\Blog\Model\Post\Tag;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Url
{
    public const XML_PATH_ROUTE_NAME = 'blog/general/route_name';
    public const XML_PATH_TAG_ROUTE_NAME = 'blog/general/tag_route_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Category $category
     * @param null     $store
     * @return string [base_url]/[blog_route]/[category_url_key]/
     * @throws NoSuchEntityException
     */
    public function getCategoryUrl($category, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        return $store->getBaseUrl()
            . $this->getRouteName($store->getWebsiteId()) . '/'
            . $category->getUrlKey() . '/';
    }

    /**
     * @param null|int|string $websiteId
     * @return mixed
     */
    public function getRouteName($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ROUTE_NAME,
            $websiteId ? ScopeInterface::SCOPE_WEBSITE : null,
            $websiteId
        );
    }

    /**
     * @param Post $post
     * @param null $store
     * @return string [base_url]/[blog_route]/[post_url_key].html
     * @throws NoSuchEntityException
     */
    public function getPostUrl($post, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        return $store->getBaseUrl()
            . $this->getRouteName($store->getWebsiteId()) . '/'
            . $post->getUrlKey() . '.html';
    }

    /**
     * @param Tag  $tag
     * @param null $store
     * @return string [base_url]/[blog_route]/[tag_route]/[tag_url_key]/
     * @throws NoSuchEntityException
     */
    public function getTagUrl($tag, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        return $store->getBaseUrl()
            . $this->getRouteName($store->getWebsiteId()) . '/'
            . $this->getTagRouteName($store->getWebsiteId()) . '/'
            . $tag->getUrlKey() . '/';
    }

    /**
     * @param null|int|string $websiteId
     * @return mixed
     */
    public function getTagRouteName($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TAG_ROUTE_NAME,
            $websiteId ? ScopeInterface::SCOPE_WEBSITE : null,
            $websiteId
        );
    }
}
