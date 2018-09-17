<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\UpgradeScript\Setup;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

 		if (version_compare($context->getVersion(), '1.0.1', '<')) {
		    
		    /* create cms block: Rodalink - Customer Support */    
            $cmsBlockContent = <<<EOD
<p>Customer Support</p>
EOD;
			$cmsBlock = $this->createBlock()->load('customer-support', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Customer Support',
			        'identifier' => 'customer-support',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Customer Support */    
		}
		/* end of 1.0.1 */

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
		    
		    /* create cms block: Rodalink - Megamenu */    
            $cmsBlockContent = <<<EOD
<ul>
<li><a href="#">MTB</a></li>
<li><a href="#">Road</a></li>
<li><a href="#">City &amp; Touring</a></li>
<li><a href="#">BMX</a></li>
<li><a href="#">Kids</a></li>
<li><a href="#">Brands</a></li>
<li class="yellow"><a href="#">Sale</a></li>
<li><a href="#">Lokasi Toko</a></li>
</ul>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-megamenu', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Megamenu',
			        'identifier' => 'rodalink-megamenu',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Megamenu */    
		}
		/* end of 1.0.2 */

		if (version_compare($context->getVersion(), '1.0.3', '<')) {
		    
		    /* create cms block: Rodalink - Banner */    
            $cmsBlockContent = <<<EOD
<div class="main-banner-wrapper">
<div class="main-banner">
<div class="section-banner"><img src="{{view url=''}}/Magento_Theme/images/main-banner.png" alt="Main Banner" />
<div class="text">
<h1>EQUIP YOURSELF,</h1>
<h1>RIDE WITH CONFIDENCE</h1>
<a href="#">Shop New Arrivals</a></div>
</div>
<div class="section-banner"><img src="{{view url=''}}/Magento_Theme/images/main-banner.png" alt="Main Banner" />
<div class="text">
<h1>EQUIP YOURSELF,</h1>
<h1>RIDE WITH CONFIDENCE</h1>
<a href="#">Shop New Arrivals</a></div>
</div>
<div class="section-banner"><img src="{{view url=''}}/Magento_Theme/images/main-banner.png" alt="Main Banner" />
<div class="text">
<h1>EQUIP YOURSELF,</h1>
<h1>RIDE WITH CONFIDENCE</h1>
<a href="#">Shop New Arrivals</a></div>
</div>
</div>
</div>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-banner', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Banner',
			        'identifier' => 'rodalink-banner',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Banner */

		    /* create cms block: Rodalink - Top Highlight */    
            $cmsBlockContent = <<<EOD
<div class="top-highlight-wrapper">
<div class="top-highlight">
<div class="left-side">
<div class="left-top"><img src="{{view url=''}}/Magento_Theme/images/road-bike.png" alt="Road Bike" />
<div class="caption">
<h4>Road Bike</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
<div class="left-bottom">
<div class="child-bottom left"><img src="{{view url=''}}/Magento_Theme/images/helmet.png" alt="Helmet" />
<div class="caption">
<h4>Helmet</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
<div class="child-bottom right"><img src="{{view url=''}}/Magento_Theme/images/tires-wheel.png" alt="Tires &amp; Wheels" />
<div class="caption">
<h4>Tires &amp; Wheels</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
</div>
</div>
<div class="right-side">
<div class="right-top">
<div class="child-top left"><img src="{{view url=''}}/Magento_Theme/images/exclusive-parts.png" alt="Exclusive Parts" />
<div class="caption">
<h4>Exclusive Parts</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
<div class="child-top right"><img src="{{view url=''}}/Magento_Theme/images/apparels.png" alt="Apparels" />
<div class="caption">
<h4>Apparels</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
</div>
<div class="right-bottom"><img src="{{view url=''}}/Magento_Theme/images/mountain-bike.png" alt="Mountain Bike" />
<div class="caption">
<h4>Mountain Bike</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
<a href="#">Shop Now</a></div>
</div>
</div>
</div>
</div>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-tophighlight', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Top Highlight',
			        'identifier' => 'rodalink-tophighlight',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Top Highlight */

		    /* create cms block: Rodalink - Why Choose Us */    
            $cmsBlockContent = <<<EOD
<div class="why-choose-us">
<div class="top-content">
<div class="background-red"></div>
<h3>Why Choose Us?</h3>
</div>
<div class="bottom-content">
<div class="option-content">
<div class="img-wrap"><img src="{{view url=''}}/Magento_Theme/images/ship.png" alt="free-shipping" /></div>
<div class="words">
<h4>Free Shipping</h4>
<p>Gratis pengiriman ke seluruh Indonesia</p>
</div>
</div>
<div class="option-content">
<div class="img-wrap"><img src="{{view url=''}}/Magento_Theme/images/warranty.png" alt="30days-guarantee" /></div>
<div class="words">
<h4>30 Days Guarantee</h4>
<p>Garansi 30 hari pengembalian produk</p>
</div>
</div>
<div class="option-content">
<div class="img-wrap"><img src="{{view url=''}}/Magento_Theme/images/pickup.png" alt="store-pickup" /></div>
<div class="words">
<h4>Store Pickup</h4>
<p>Pengambilan produk di dealer terdekat</p>
</div>
</div>
<div class="option-content">
<div class="img-wrap"><img src="{{view url=''}}/Magento_Theme/images/secure.png" alt="secure-checkout" /></div>
<div class="words">
<h4>Secure Checkout</h4>
<p>Keamanan transaksi terjamin</p>
</div>
</div>
</div>
</div>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-whychooseus', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Why Choose Us',
			        'identifier' => 'rodalink-whychooseus',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Why Choose Us */

		    /* create cms block: Rodalink - Blog Highlight */    
            $cmsBlockContent = <<<EOD
<div class="blog-highlight-wrapper">
<div class="blog-highlight">
<div class="blog-left"><img src="{{view url=''}}/Magento_Theme/images/5-hal.png" alt="5 Hal" />
<div class="background-blog"></div>
<div class="tagline">
<div class="title">
<h4>5 Hal yang perlu diperhatikan saat menggunakan helm sebelum bersepeda</h4>
</div>
<div class="short-desc">
<p>Ternyata jika saat bersepeda posisi helm yang kita gunakan salah dapat mempengaruhi kesehatan, kenyamanan, kecepatan kita bersepeda lhoo Riders.</p>
</div>
<div class="action"><a href="#">Baca Selengkapnya</a></div>
</div>
</div>
<div class="blog-right">
<div class="blog-top"><img src="{{view url=''}}/Magento_Theme/images/sarung-tangan.png" alt="Sarung Tangan" />
<div class="background-blog"></div>
<div class="tagline">
<div class="title">
<h4>Mengapa Perlu Menggunakan Sarung Tangan Saat Bersepeda?</h4>
</div>
<div class="action"><a href="#">Baca Selengkapnya</a></div>
</div>
</div>
<div class="blog-bottom"><img src="{{view url=''}}/Magento_Theme/images/visit.png" alt="Visit Our Blog" />
<div class="background-blog"></div
<div class="tagline">
<div class="visit">
<h4>Visit Our Blog</h4>
</div>
</div>
</div>
</div>
</div>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-blog', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Blog Highlight',
			        'identifier' => 'rodalink-blog',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Blog Highlight */

		    /* create cms block: Rodalink - Brand */    
            $cmsBlockContent = <<<EOD
<div class="brand-wrapper">
<div class="brand">
<ul>
<li><img src="{{view url=''}}/Magento_Theme/images/polygon.png" alt="Polygon" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/shimano.png" alt="Shimano" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fulcrum.png" alt="Fulcrum" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fizik.png" alt="Fizik" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/michelin.png" alt="Michelin" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fox.png" alt="Fox" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/polygon.png" alt="Polygon" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/shimano.png" alt="Shimano" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fulcrum.png" alt="Fulcrum" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fizik.png" alt="Fizik" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/michelin.png" alt="Michelin" /></li>
<li><img src="{{view url=''}}/Magento_Theme/images/fox.png" alt="Fox" /></li>
</ul>
</div>
</div>
EOD;
			$cmsBlock = $this->createBlock()->load('rodalink-brand', 'identifier');
		
			if (!$cmsBlock->getId()) {
			    $cmsBlock = [
			        'title' => 'Rodalink - Brand',
			        'identifier' => 'rodalink-brand',
			        'content' => $cmsBlockContent,
			        'is_active' => 1,
			        'stores' => 0,
			    ];
			    $this->createBlock()->setData($cmsBlock)->save();
			} else {
			    $cmsBlock->setContent($cmsBlockContent)->save();
			}
		    /* end create cms block: Rodalink - Brand */

		    /* create cms page: Home Page */    
            $pageContent = <<<EOD
{{block class="Magento\Cms\Block\Block" block_id="rodalink-banner"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-whychooseus"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-tophighlight"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-blog"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-brand"}}
EOD;

            $cmsPage = $this->createPage()->load('home', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Home Page',
                    'page_layout' => '1column',
                    'identifier' => 'home',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => 0,
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: Home Page */

		}
		/* end of 1.0.3 */

		if (version_compare($context->getVersion(), '1.0.4', '<')) {
		    
		    /* update cms page: Home Page */    
            $pageContent = <<<EOD
{{block class="Magento\Cms\Block\Block" block_id="rodalink-banner"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-whychooseus"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-tophighlight"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-topwidget"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-blog"}}
{{block class="Magento\Cms\Block\Block" block_id="rodalink-brand"}}
EOD;

            $cmsPage = $this->createPage()->load('home', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Home Page',
                    'page_layout' => '1column',
                    'identifier' => 'home',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => 0,
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end update cms page: Home Page */

            /* create cms block: Rodalink - Top Widget Product */    
            $cmsBlockContent = <<<EOD
<div class="top-widget-wrapper">
<div class="top-widget">
<div class="left-widget">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" title="New Arrivals" products_count="8" template="product/widget/content/grid.phtml" conditions_encoded="a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`3`;]]"}}</div>
<div class="right-widget">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" title="Category" products_count="8" template="product/widget/content/grid.phtml" conditions_encoded="a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`3`;]]"}}</div>
</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('rodalink-topwidget', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Top Widget Product',
                    'identifier' => 'rodalink-topwidget',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - Top Widget Product */

		}
		/* end of 1.0.4 */

		if (version_compare($context->getVersion(), '1.0.5', '<')) {
		    
            /* create cms block: Rodalink - All Category Widget */    
            $cmsBlockContent = <<<EOD
{{widget type="Ves\Megamenu\Block\Widget\Menu" title="All Category" alias="side-menu"}}
EOD;
            $cmsBlock = $this->createBlock()->load('rodalink-allcategory', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - All Category Widget',
                    'identifier' => 'rodalink-allcategory',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - All Category Widget */

		}
		/* end of 1.0.5 */

		if (version_compare($context->getVersion(), '1.0.6', '<')) {
		    
            /* create cms block: Rodalink - Top Widget Product */    
            $cmsBlockContent = <<<EOD
<div class="top-widget-wrapper">
<div class="top-widget">
<div class="left-widget">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" title="New Arrivals" products_count="8" template="product/widget/content/grid.phtml" conditions_encoded="a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`3`;]]"}}</div>
<div class="right-widget">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" title="Category" products_count="8" template="product/widget/content/grid.phtml" conditions_encoded="a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`3`;]]"}}</div>
</div>
<div class="button_wrapper">
<div class="bestseller_button"><span class="action primary"><a href="{{store url="#"}}">View All Best Seller</a></span></div>
</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('rodalink-topwidget', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Top Widget Product',
                    'identifier' => 'rodalink-topwidget',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - Top Widget Product */

		}
		/* end of 1.0.6 */

		/* 1.0.7 */
		if (version_compare($context->getVersion(), '1.0.7', '<')) {
		    
            /* create cms block: Rodalink -  Success Bank Transfer Info */    
            $cmsBlockContent = <<<EOD
<ul class="list_bank">
	<li>
		<span class="image">
			<img src="{{media url="wysiwyg/success/mandiri.png"}}" alt="" />
		</span>
		<div class="content">
			<p>A/C No : 101-0006729527</p>
			<p>An. PT Monica HijauLestari</p>
			<p>Click <a href="#">here</a> for instruction how to transfer.</p>
		</div>
	</li>
	<li>
		<span class="image">
			<img src="{{media url="wysiwyg/success/bca.png"}}" alt="" />
		</span>
		<div class="content">
			<p>A/C No : 101-0006729527</p>
			<p>An. PT Monica HijauLestari</p>
			<p>Click <a href="#">here</a> for instruction how to transfer.</p>
		</div>
	</li>
	<li>
		<span class="image">
			<img src="{{media url="wysiwyg/success/bri.png"}}" alt="" />
		</span>
		<div class="content">
			<p>A/C No : 101-0006729527</p>
			<p>An. PT Monica HijauLestari</p>
			<p>Click <a href="#">here</a> for instruction how to transfer.</p>
		</div>
	</li>
	<li>
		<span class="image">
			<img src="{{media url="wysiwyg/success/bni.png"}}" alt="" />
		</span>
		<div class="content">
			<p>A/C No : 101-0006729527</p>
			<p>An. PT Monica HijauLestari</p>
			<p>Click <a href="#">here</a> for instruction how to transfer.</p>
		</div>
	</li>
</ul>
EOD;
            $cmsBlock = $this->createBlock()->load('bank_transfer_info', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Success Bank Transfer Info',
                    'identifier' => 'bank_transfer_info',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink -  Success Bank Transfer Info */

            /* create cms block: Rodalink - Success Page Info */    
            $cmsBlockContent = <<<EOD
<span class="label">Need to discuss your order?</span>
<span class="phone">Call 1500 827 or +6221 748 666 88</span>
<span class="whatsapp">WhatsApp +62-811-1785-115</span>
EOD;
            $cmsBlock = $this->createBlock()->load('checkout_info', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Success Page Info',
                    'identifier' => 'checkout_info',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink -  Rodalink - Success Page Info */

		}
		/* end of 1.0.7 */

		if (version_compare($context->getVersion(), '1.0.8', '<')) {
		    
		    /* update cms page: 404 */    
            $pageContent = <<<EOD
<div class="no-route-content">
	<div class="left-side">
		<img src="{{view url="images/404.png"}}" alt="" />
		<span>WHOOPS!</span>
		<p>We couldn't find the page</p>
		<p>you are looking for</p>
		<a href="{{store url=''}}">
			<span>Go Back</span>
		</a>
	</div>
	<div class="right-side">
		<img src="{{view url="images/bicycle_404.png"}}" alt="" />
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('no-route', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => '404 Not Found',
                    'page_layout' => '1column',
                    'identifier' => 'no-route',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0]
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end update cms page: 404 */

            /* create cms page: Contact Us */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">Contact Us</h1>
		<p>We love hearing from you, our Rodalink customers. Please contact us about anything at all.
		Reach us however you like.</p>
	</div>
	<div class="content">
		<div class="our-contact">
			<h1 class="title">Our Contact</h1>
			<span>PT. Rodalink</span>
			<div class="st-address">Jl. Mayjend H. R. Muhammad 121, Surabaya</div>
			<div class="cs">
				<span>Customer Support rodalink.co.id</span>
				<span>Hotline  : (031) 7343220</span>
			</div>
			<div class="email">
				<a href="mailto:support@rodalinkonline.freshdesk.com">support@rodalinkonline.freshdesk.com</a>
			</div>
			<div class="opr-time">
				<span>Jam Operasional Customer Service :</span>
				<span>Senin - Jumat [08:00 - 17:00]</span>
				<span>Tanggal Merah Libur</span>
			</div>
		</div>
		<div class="form-contact">
			<h1 class="title">Send Us Message</h1>
			{{block class="Magento\Contact\Block\ContactForm" name="contactForm" template="Magento_Contact::form.phtml"}}
		</div>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('contact-us', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Contact Us',
                    'page_layout' => '1column',
                    'identifier' => 'contact-us',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: Contact Us */

            /* create cms page: About Us */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">About Us</h1>
	</div>
	<div class="content">
		<div class="left">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
		</div>
		<div class="right">
			<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
		</div>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('about-us', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'About Us',
                    'page_layout' => '1column',
                    'identifier' => 'about-us',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: About Us */
        
        }
        /* end of 1.0.8 */
		

		if (version_compare($context->getVersion(), '1.0.9', '<')) {
		    
		    /* update cms page: 404 */    
            $pageContent = <<<EOD
<div class="no-route-content">
	<div class="right-side">
		<img src="{{view url="images/bicycle_404.png"}}" alt="" />
	</div>
	<div class="left-side">
		<img src="{{view url="images/404.png"}}" alt="" />
		<span>WHOOPS!</span>
		<p>We couldn't find the page</p>
		<p>you are looking for</p>
		<a href="{{store url=''}}">
			<span>Go Back</span>
		</a>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('no-route', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => '404 Not Found',
                    'page_layout' => '1column',
                    'identifier' => 'no-route',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0]
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end update cms page: 404 */

            /* create cms page: Contact Us */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">Contact Us</h1>
		<p>We love hearing from you, our Rodalink customers. Please contact us about anything at all.
		Reach us however you like.</p>
	</div>
	<div class="content">
		<div class="our-contact">
			<h1 class="title">Our Contact</h1>
			<span>PT. Rodalink</span>
			<div class="st-address">
				<img src="{{view url="images/maps.png"}}" alt="" />
				<span>Jl. Mayjend H. R. Muhammad 121, Surabaya</span>
			</div>
			<div class="cs">
				<img src="{{view url="images/phone.png"}}" alt="" />
				<div>
					<span>Customer Support rodalink.co.id</span>
					<span>Hotline  : (031) 7343220</span>
				</div>
			</div>
			<div class="email">
				<img src="{{view url="images/mail.png"}}" alt="" />
				<a href="mailto:support@rodalinkonline.freshdesk.com">support@rodalinkonline.freshdesk.com</a>
			</div>
			<div class="opr-time">
				<img src="{{view url="images/clock.png"}}" alt="" />
				<div>
					<span>Jam Operasional Customer Service :</span>
					<span>Senin - Jumat [08:00 - 17:00]</span>
					<span>Tanggal Merah Libur</span>
				</div>
			</div>
		</div>
		<div class="form-contact">
			<h1 class="title">Send Us Message</h1>
			{{block class="Magento\Contact\Block\ContactForm" name="contactForm" template="Magento_Contact::form.phtml"}}
		</div>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('contact-us', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Contact Us',
                    'page_layout' => '1column',
                    'identifier' => 'contact-us',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: Contact Us */
        
        }
        /* end of 1.0.9 */

		/* 1.0.10 */
		if (version_compare($context->getVersion(), '1.0.10', '<')) {
		    
            /* create cms block: Rodalink - Footer */    
            $cmsBlockContent = <<<EOD
<div class="footer-wrapper" data-mage-init='{"accordion":{"openedState": "active", "collapsible": true, "active": [0], "multipleCollapsible": false}}'>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Rodalink</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Tentang Rodalink</a></li>
			<li><a href="#">Outlet Rodalink Indonesia</a></li>
			<li><a href="#">Brand Kami</a></li>
			<li><a href="#">Keuntungan Member Rodalink</a></li>
			<li><a href="#">Voucher Belanja</a></li>
			<li><a href="#">Biaya Servis Sepeda</a></li>
			<li><a href="#">Blog</a></li>
			<li><a href="#">Karir</a></li>
			<li><a href="#">Sitemap</a></li>
		</ul>
	</div>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Customer Service</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Rodalink di BBM dan Line</a></li>
			<li><a href="#">Garansi Harga Termurah</a></li>
			<li><a href="#">Garansi 30 Hari Pengembalian</a></li>
			<li><a href="#">Kebijakan Pengiriman</a></li>
			<li><a href="#">Kebijakan Retur</a></li>
			<li><a href="#">Garansi Sepeda Polygon</a></li>
			<li><a href="#">Syarat dan Kondisi</a></li>
			<li><a href="#">Konfirmasi Pembayaran</a></li>
		</ul>
	</div>
	<div class="footer-content how-to">
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Panduan Belanja</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Cara Order</a></li>
			<li><a href="#">Konfirmasi Pembayaran</a></li>
			<li><a href="#">Panduan Memilih Sepeda</a></li>
			<li><a href="#">Hubungi Kami</a></li>
		</ul>
		<img src="{{view url=''}}/Magento_Theme/images/ssl.png" alt="ssl" /></div>
		<div class="footer-content social">
			<h3>Follow Us</h3>
			<ul>
				<li><a href="#"><img src="{{view url=''}}/Magento_Theme/images/fb.png" alt="facebook" /></a></li>
				<li><a href="#"><img src="{{view url=''}}/Magento_Theme/images/ig.png" alt="instagram" /></a></li>
				<li><a href="#"><img src="{{view url=''}}/Magento_Theme/images/bbm.png" alt="bbm" /></a></li>
				<li><a href="#"><img src="{{view url=''}}/Magento_Theme/images/line.png" alt="line" /></a></li>
			</ul>
		</div>
	</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('footer-rodalink', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Footer',
                    'identifier' => 'footer-rodalink',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - Footer */
		}
		/* end of 1.0.10 */

		/* 1.0.11 */ 
		if (version_compare($context->getVersion(), '1.0.11', '<')) {

			/* create cms page: FAQ */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">Rodalink FAQ</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
	</div>
	<div class="content">
		<div class="item-content">
			<h3>Pertanyaan 1</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Pertanyaan 2</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Pertanyaan 3</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Pertanyaan 4</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Pertanyaan 5</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('faq', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Rodalink Faq',
                    'page_layout' => '1column',
                    'identifier' => 'faq',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: FAQ */

            /* create cms page: terms and condition */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">Rodalink Terms and Condition</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
	</div>
	<div class="content">
		<div class="item-content">
			<h3>Term 1</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
				<ul>
					<li>Lorem ipsum dolor sit amet</li>
					<li>Lorem ipsum dolor sit amet</li>
					<li>Lorem ipsum dolor sit amet</li>
					<li>Lorem ipsum dolor sit amet</li>
				</ul>
			</div>
		</div>
		<div class="item-content">
			<h3>Term 2</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Term 3</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Term 4</h3>
			<div class="desc">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
		<div class="item-content">
			<h3>Term 5</h3>
			<div class="desc">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
			</div>
		</div>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('terms-condition', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Terms and Condition',
                    'page_layout' => '1column',
                    'identifier' => 'terms-condition',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: terms condition */

            /* create cms page: privacy and policy */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="head">
		<h1 class="title">Privacy Policy</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
	</div>
	<div class="content">
		<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
		<ul>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
		</ul>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('privacy-policy', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Privacy Policy',
                    'page_layout' => '1column',
                    'identifier' => 'privacy-policy',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: terms condition */
		}
		/* end of 1.0.11 */

		/* 1.0.12 */ 
		if (version_compare($context->getVersion(), '1.0.12', '<')) {

			/* create cms page: Tentang Rodalink */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>tentang rodalink</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('tentang-rodalink', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Tentang Rodalink',
                    'page_layout' => '1column',
                    'identifier' => 'tentang-rodalink',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: Tentang Rodalink */

			/* create cms page: outlet rodalink */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>Outlet Rodalink</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('outlet-rodalink', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Outlet Rodalink',
                    'page_layout' => '1column',
                    'identifier' => 'outlet-rodalink',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: outlet rodalink */

			/* create cms page: keuntungan */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>keuntungan member</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('keuntungan', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Keuntungan Member',
                    'page_layout' => '1column',
                    'identifier' => 'keuntungan',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: keuntungan */

			/* create cms page: cara order */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>cara order</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('cara-order', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Cara Order',
                    'page_layout' => '1column',
                    'identifier' => 'cara-order',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: cara order */

			/* create cms page: biaya servis */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>biaya servis sepeda</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('biaya-servis', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Biaya Servis',
                    'page_layout' => '1column',
                    'identifier' => 'biaya-servis',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: biaya servis */

			/* create cms page: panduan sepeda */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>panduan memilih sepeda</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('panduan', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Panduan Memilih Sepeda',
                    'page_layout' => '1column',
                    'identifier' => 'panduan',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: panduan sepeda */

			/* create cms page: kebijakan pengiriman */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>kebijakan pengiriman</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('kebijakan-pengiriman', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Kebijakan Pengiriman',
                    'page_layout' => '1column',
                    'identifier' => 'kebijakan-pengiriman',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: kebijakan pengiriman */

			/* create cms page: kebijakan retur */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>kebijakan retur</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('kebijakan-retur', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Kebijakan Retur',
                    'page_layout' => '1column',
                    'identifier' => 'kebijakan-retur',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: kebijakan retur */

			/* create cms page: faq */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>faq</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('faq', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'FAQ',
                    'page_layout' => '1column',
                    'identifier' => 'faq',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: faq */

			/* create cms page: term condition */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>Term and Condition</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('term-condition', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Terms and Condition',
                    'page_layout' => '1column',
                    'identifier' => 'term-condition',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: term condition */

			/* create cms page: privacy policy */    
            $pageContent = <<<EOD
<div class="cms-content">
	<div class="col-12--2 with-right-border">
		<aside class="sidebar--external">
			<nav>
				<h3 class="sidebar__title">
					<strong id="head-shading">Rodalink</strong>
				</h3>
				<div class="sidebar__content-wrapper">
					<ul>
						<li>
							<a href="{{store url=''}}tentang-rodalink">Tentang Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}outlet-rodalink">Outlet Rodalink</a>
						</li>
						<li>
							<a href="{{store url=''}}keuntungan">keuntungan member</a>
						</li>
						<li>
							<a href="{{store url=''}}cara-order">cara order</a>
						</li>
						<li>
							<a href="{{store url=''}}biaya-servis">biaya servis sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}panduan">panduan memilih sepeda</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-pengiriman">kebijakan pengiriman</a>
						</li>
						<li>
							<a href="{{store url=''}}kebijakan-retur">kebijakan retur</a>
						</li>
						<li>
							<a href="{{store url=''}}faq">FAQ</a>
						</li>
						<li>
							<a href="{{store url=''}}term-condition">terms condition</a>
						</li>
						<li>
							<a href="{{store url=''}}privacy-policy">privacy policy</a>
						</li>
					</ul>
				</div>
			</nav>
		</aside>
	</div>
	<div class="col-12--10 no-gutter with-left-border">
		<section class="content--external">
			<div class="page__title">
				<h1>privacy policy</h1>
			</div>
			<article>
				<div class="title">
					<div id="desc">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
						<ul>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
							<li>Lorem ipsum dolor sit amet</li>
						</ul>
					</div>
					<br>
				</div>
				<div id="edit-terms-section-container"></div>
			</article>
		</section>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('privacy-policy', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Privacy Policy',
                    'page_layout' => '1column',
                    'identifier' => 'privacy-policy',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0],
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end create cms page: privacy policy */
        }
        if (version_compare($context->getVersion(), '1.0.13', '<')) {
		    
		    /* update cms page: 404 */    
            $pageContent = <<<EOD
<div class="no-route-content">
	<div class="right-side">
		<img src="{{view url="images/bicycle_404.png"}}" alt="" />
	</div>
	<div class="left-side">
		<img src="{{view url="images/404.png"}}" alt="" />
		<span>WHOOPS!</span>
		<p>We couldn't find the page</p>
		<p>you are looking for</p>
		<a href="javascript:history.back()">
			<span>Go Back</span>
		</a>
	</div>
</div>
EOD;

            $cmsPage = $this->createPage()->load('no-route', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => '404 Not Found',
                    'page_layout' => '1column',
                    'identifier' => 'no-route',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => [0]
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }
            /* end update cms page: 404 */
        }

        /* 1.0.14 */
		if (version_compare($context->getVersion(), '1.0.14', '<')) {
		    
            /* create cms block: Rodalink - Footer */    
            $cmsBlockContent = <<<EOD
<div class="footer-wrapper" data-mage-init='{"accordion":{"openedState": "active", "collapsible": true, "active": [0], "multipleCollapsible": false}}'>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Rodalink</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Tentang Rodalink</a></li>
			<li><a href="#">Outlet Rodalink Indonesia</a></li>
			<li><a href="#">Brand Kami</a></li>
			<li><a href="#">Keuntungan Member Rodalink</a></li>
			<li><a href="#">Voucher Belanja</a></li>
			<li><a href="#">Biaya Servis Sepeda</a></li>
			<li><a href="#">Blog</a></li>
			<li><a href="#">Karir</a></li>
			<li><a href="#">Sitemap</a></li>
		</ul>
	</div>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Customer Service</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Rodalink di BBM dan Line</a></li>
			<li><a href="#">Garansi Harga Termurah</a></li>
			<li><a href="#">Garansi 30 Hari Pengembalian</a></li>
			<li><a href="#">Kebijakan Pengiriman</a></li>
			<li><a href="#">Kebijakan Retur</a></li>
			<li><a href="#">Garansi Sepeda Polygon</a></li>
			<li><a href="#">Syarat dan Kondisi</a></li>
			<li><a href="#">Konfirmasi Pembayaran</a></li>
		</ul>
	</div>
	<div class="footer-content how-to">
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Panduan Belanja</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="#">Cara Order</a></li>
			<li><a href="#">Konfirmasi Pembayaran</a></li>
			<li><a href="#">Panduan Memilih Sepeda</a></li>
			<li><a href="#">Hubungi Kami</a></li>
		</ul>
		<img src="{{view url=''}}/Magento_Theme/images/ssl.png" alt="ssl" /></div>
		<div class="footer-content social">
			<h3>Follow Us</h3>
			<ul>
				<li><a href="https://www.facebook.com/rodalink.id"><img src="{{view url=''}}/Magento_Theme/images/fb.png" alt="facebook" /></a></li>
				<li><a href="https://www.instagram.com/rodalinkindonesia/"><img src="{{view url=''}}/Magento_Theme/images/ig.png" alt="instagram" /></a></li>
				<li><a href="https://www.rodalink.com/id/layanan-pelanggan/rodalink-hadir-di-bbm-dan-line/"><img src="{{view url=''}}/Magento_Theme/images/bbm.png" alt="bbm" /></a></li>
				<li><a href="http://line.me/ti/p/%40ddg3909b"><img src="{{view url=''}}/Magento_Theme/images/line.png" alt="line" /></a></li>
			</ul>
		</div>
	</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('footer-rodalink', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Footer',
                    'identifier' => 'footer-rodalink',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - Footer */
		}
		/* end of 1.0.14 */

		/* 1.0.15 */
		if (version_compare($context->getVersion(), '1.0.15', '<')) {
		    
            /* create cms block: Rodalink - Footer */    
            $cmsBlockContent = <<<EOD
<div class="footer-wrapper" data-mage-init='{"accordion":{"openedState": "active", "collapsible": true, "active": [0], "multipleCollapsible": false}}'>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Rodalink</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="tentang-rodalink"}}">Tentang Rodalink</a></li>
			<li><a href="{{store url="outlet-rodalink"}}">Outlet Rodalink Indonesia</a></li>
			<li><a href="{{store url="brands"}}">Brand Kami</a></li>
			<li><a href="{{store url="keuntungan"}}">Keuntungan Member Rodalink</a></li>
			<li><a href="{{store url="#"}}">Voucher Belanja</a></li>
			<li><a href="{{store url="biaya-servis"}}">Biaya Servis Sepeda</a></li>
			<li><a href="{{store url="blog"}}">Blog</a></li>
			<li><a href="{{store url="#"}}">Karir</a></li>
			<li><a href="{{store url="#"}}">Sitemap</a></li>
		</ul>
	</div>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Customer Service</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="#"}}">Rodalink di BBM dan Line</a></li>
			<li><a href="{{store url="#"}}">Garansi Harga Termurah</a></li>
			<li><a href="{{store url="#"}}">Garansi 30 Hari Pengembalian</a></li>
			<li><a href="{{store url="kebijakan-pengiriman"}}">Kebijakan Pengiriman</a></li>
			<li><a href="{{store url="kebijakan-retur"}}">Kebijakan Retur</a></li>
			<li><a href="{{store url="#"}}">Garansi Sepeda Polygon</a></li>
			<li><a href="{{store url="term-condition"}}">Syarat dan Kondisi</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
		</ul>
	</div>
	<div class="footer-content how-to">
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Panduan Belanja</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="cara-order"}}">Cara Order</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
			<li><a href="{{store url="panduan"}}">Panduan Memilih Sepeda</a></li>
			<li><a href="{{store url="contact-us"}}">Hubungi Kami</a></li>
		</ul>
		<img src="{{view url=''}}/Magento_Theme/images/ssl.png" alt="ssl" /></div>
		<div class="footer-content social">
			<h3>Follow Us</h3>
			<ul>
				<li><a href="https://www.facebook.com/rodalink.id"><img src="{{view url=''}}/Magento_Theme/images/fb.png" alt="facebook" /></a></li>
				<li><a href="https://www.instagram.com/rodalinkindonesia/"><img src="{{view url=''}}/Magento_Theme/images/ig.png" alt="instagram" /></a></li>
				<li><a href="https://www.rodalink.com/id/layanan-pelanggan/rodalink-hadir-di-bbm-dan-line/"><img src="{{view url=''}}/Magento_Theme/images/bbm.png" alt="bbm" /></a></li>
				<li><a href="http://line.me/ti/p/%40ddg3909b"><img src="{{view url=''}}/Magento_Theme/images/line.png" alt="line" /></a></li>
			</ul>
		</div>
	</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('footer-rodalink', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Footer',
                    'identifier' => 'footer-rodalink',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end create cms block: Rodalink - Footer */
		}
		/* end of 1.0.15 */
		
		/* 1.0.16 */
		if (version_compare($context->getVersion(), '1.0.16', '<')) {
		    
            /* update cms block: Rodalink - Footer */    
            $cmsBlockContent = <<<EOD
<div class="footer-wrapper" data-mage-init='{"accordion":{"openedState": "active", "collapsible": true, "active": [0], "multipleCollapsible": false}}'>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Rodalink</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="tentang-rodalink"}}">Tentang Rodalink</a></li>
			<li><a href="{{store url="storepickup"}}">Outlet Rodalink Indonesia</a></li>
			<li><a href="{{store url="brands"}}">Brand Kami</a></li>
			<li><a href="{{store url="keuntungan"}}">Keuntungan Member Rodalink</a></li>
			<li><a href="{{store url="#"}}">Voucher Belanja</a></li>
			<li><a href="{{store url="biaya-servis"}}">Biaya Servis Sepeda</a></li>
			<li><a href="{{store url="blog"}}">Blog</a></li>
			<li><a href="{{store url="#"}}">Karir</a></li>
			<li><a href="{{store url="#"}}">Sitemap</a></li>
		</ul>
	</div>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Customer Service</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="#"}}">Rodalink di BBM dan Line</a></li>
			<li><a href="{{store url="#"}}">Garansi Harga Termurah</a></li>
			<li><a href="{{store url="#"}}">Garansi 30 Hari Pengembalian</a></li>
			<li><a href="{{store url="kebijakan-pengiriman"}}">Kebijakan Pengiriman</a></li>
			<li><a href="{{store url="kebijakan-retur"}}">Kebijakan Retur</a></li>
			<li><a href="{{store url="#"}}">Garansi Sepeda Polygon</a></li>
			<li><a href="{{store url="term-condition"}}">Syarat dan Kondisi</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
		</ul>
	</div>
	<div class="footer-content how-to">
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Panduan Belanja</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="cara-order"}}">Cara Order</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
			<li><a href="{{store url="panduan"}}">Panduan Memilih Sepeda</a></li>
			<li><a href="{{store url="contact-us"}}">Hubungi Kami</a></li>
		</ul>
		<img src="{{view url=''}}/Magento_Theme/images/ssl.png" alt="ssl" /></div>
		<div class="footer-content social">
			<h3>Follow Us</h3>
			<ul>
				<li><a href="https://www.facebook.com/rodalink.id"><img src="{{view url=''}}/Magento_Theme/images/fb.png" alt="facebook" /></a></li>
				<li><a href="https://www.instagram.com/rodalinkindonesia/"><img src="{{view url=''}}/Magento_Theme/images/ig.png" alt="instagram" /></a></li>
				<li><a href="https://www.rodalink.com/id/layanan-pelanggan/rodalink-hadir-di-bbm-dan-line/"><img src="{{view url=''}}/Magento_Theme/images/bbm.png" alt="bbm" /></a></li>
				<li><a href="http://line.me/ti/p/%40ddg3909b"><img src="{{view url=''}}/Magento_Theme/images/line.png" alt="line" /></a></li>
			</ul>
		</div>
	</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('footer-rodalink', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Footer',
                    'identifier' => 'footer-rodalink',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end update cms block: Rodalink - Footer */
		}
		/* end of 1.0.16 */

		/* 1.0.17 */
		if (version_compare($context->getVersion(), '1.0.17', '<')) {
		    
            /* update cms block: PDP - Info Cicilan */    
            $cmsBlockContent = <<<EOD
<div class="installment_info">
<div class="button"><button><span> <!--?php /* @escapeNotVerified */ echo __('kalkulator cicilan') ?--> kalkulator cicilan </span> </button></div>
<div class="info_bank">
<ul>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/visa.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/master_card.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/klik_bca.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/bca_klikpay.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/mandiri.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/american_express.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/cimb_klik.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/jcb.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/citibank.png"}}" alt="" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/cicilan/mandiri_klikpay.png"}}" alt="" /></a></li>
</ul>
</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('info_cicilan', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'PDP - Info Cicilan',
                    'identifier' => 'info_cicilan',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end update cms block: Rodalink - Footer */
		}
		/* end of 1.0.17 */

		/* 1.0.18 */
		if (version_compare($context->getVersion(), '1.0.18', '<')) {
		    
            /* update cms block: Rodalink - Footer */    
            $cmsBlockContent = <<<EOD
<div class="footer-wrapper" data-mage-init='{"accordion":{"openedState": "active", "collapsible": true, "active": [0], "multipleCollapsible": false}}'>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Rodalink</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="tentang-rodalink"}}">Tentang Rodalink</a></li>
			<li><a href="{{store url="storepickup"}}">Outlet Rodalink Indonesia</a></li>
			<li><a href="{{store url="brands"}}">Brand Kami</a></li>
			<li><a href="{{store url="keuntungan"}}">Keuntungan Member Rodalink</a></li>
			<li><a href="{{store url="#"}}">Voucher Belanja</a></li>
			<li><a href="{{store url="biaya-servis"}}">Biaya Servis Sepeda</a></li>
			<li><a href="{{store url="blog"}}">Blog</a></li>
			<li><a href="{{store url="#"}}">Karir</a></li>
			<li><a href="{{store url="#"}}">Sitemap</a></li>
		</ul>
	</div>
	<div class="footer-content" >
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Customer Service</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="#"}}">Rodalink di BBM dan Line</a></li>
			<li><a href="{{store url="#"}}">Garansi Harga Termurah</a></li>
			<li><a href="{{store url="#"}}">Garansi 30 Hari Pengembalian</a></li>
			<li><a href="{{store url="kebijakan-pengiriman"}}">Kebijakan Pengiriman</a></li>
			<li><a href="{{store url="kebijakan-retur"}}">Kebijakan Retur</a></li>
			<li><a href="{{store url="#"}}">Garansi Sepeda Polygon</a></li>
			<li><a href="{{store url="term-condition"}}">Syarat dan Kondisi</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
		</ul>
	</div>
	<div class="footer-content how-to">
		<div data-role="collapsible">
			<div data-role="trigger" class="title">
				<h3>Panduan Belanja</h3>
			</div>
		</div>
		<ul data-role="content">
			<li><a href="{{store url="cara-order"}}">Cara Order</a></li>
			<li><a href="{{store url="confirmpayment"}}">Konfirmasi Pembayaran</a></li>
			<li><a href="{{store url="panduan"}}">Panduan Memilih Sepeda</a></li>
			<li><a href="{{store url="contact"}}">Hubungi Kami</a></li>
		</ul>
		<img src="{{view url=''}}/Magento_Theme/images/ssl.png" alt="ssl" /></div>
		<div class="footer-content social">
			<h3>Follow Us</h3>
			<ul>
				<li><a href="https://www.facebook.com/rodalink.id"><img src="{{view url=''}}/Magento_Theme/images/fb.png" alt="facebook" /></a></li>
				<li><a href="https://www.instagram.com/rodalinkindonesia/"><img src="{{view url=''}}/Magento_Theme/images/ig.png" alt="instagram" /></a></li>
				<li><a href="https://www.rodalink.com/id/layanan-pelanggan/rodalink-hadir-di-bbm-dan-line/"><img src="{{view url=''}}/Magento_Theme/images/bbm.png" alt="bbm" /></a></li>
				<li><a href="http://line.me/ti/p/%40ddg3909b"><img src="{{view url=''}}/Magento_Theme/images/line.png" alt="line" /></a></li>
			</ul>
		</div>
	</div>
</div>
EOD;
            $cmsBlock = $this->createBlock()->load('footer-rodalink', 'identifier');
        
            if (!$cmsBlock->getId()) {
                $cmsBlock = [
                    'title' => 'Rodalink - Footer',
                    'identifier' => 'footer-rodalink',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 0,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
            /* end update cms block: Rodalink - Footer */
		}
		/* end of 1.0.18 */
	}
    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }

    /**
     * Create block
     *
     * @return Page
     */
    public function createBlock()
    {
        return $this->blockFactory->create();
    }
}