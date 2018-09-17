<?php
/**
 * Copyright © 2015 iCube. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\UpgradeScript\Setup;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
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
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
               
        /**
         * cms block Rodalin - Footer
         */

        $cmsBlockContent = <<<EOD
<div class="footer-wrapper">
<div class="footer-content">
<h3>Rodalink</h3>
<ul>
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
<div class="footer-content">
<h3>Customer Service</h3>
<ul>
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
<h3>Panduan Belanja</h3>
<ul>
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
             
        $cmsBlock = [
            'title' => 'Rodalink - Footer',
            'identifier' => 'footer-rodalink',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 0,
        ];

        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        $block->setData($cmsBlock)->save();
       
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
}