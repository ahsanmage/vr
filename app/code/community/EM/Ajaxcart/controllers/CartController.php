<?php
require_once Mage::getBaseDir('code').DS.'core/Mage/Checkout/controllers/CartController.php';
class EM_Ajaxcart_CartController extends Mage_Checkout_CartController
{   
	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

	/**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

	protected function initTheme(){
		Mage::getSingleton('core/design_package')
			->setPackageName($this->getRequest()->getParam('ajax_package_name'))
			->setTheme('layout',$this->getRequest()->getParam('ajax_layout'))
			->setTheme('template',$this->getRequest()->getParam('ajax_template'))
			->setTheme('skin',$this->getRequest()->getParam('ajax_skin'));
	}

	/**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

	/**
     * Add product to shopping cart action
     */
    public function addAction()
    {
		$this->initTheme();
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

			$product = $this->_initProduct();
			$related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);
			
            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
					$img = "<img src='".Mage::helper('catalog/image')->init($product, 'image')->resize(60,null)."' />";
					$tmp_product = '<div id="message_ajax"><div class="ajaxcart_image">'.$img.'</div><div class="ajaxcart_message">'.$message.'</div></div>';
					$check = 'success';
                }
            }
        } catch (Mage_Core_Exception $e) {
			
			if ($this->_getSession()->getUseNotice(true)) {
				//$tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
				Mage::helper('ajaxcart')->getOptionsHtml($this,$product->getId());
			} else {
				$tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
			}
        } catch (Exception $e) {
			$check = 'failed';
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
        }

		$url = $this->getRequest()->getParam('return_url');
		if ($url) {
			header("Location:".$url);exit;
		}

		$arr['options'] = 	2;
		$arr['check'] 	= 	$check;
		$arr['msg']		= 	$tmp_product;
		if($check == 'success'){
			$this->loadLayout();
			$layout = $this->getLayout();
			$block_sidebar	=	$layout->getBlock('cart_sidebar');
			$block_sidebar_mobile	=	$layout->getBlock('cart_sidebar_mobile');
			$cartBlockLink = $layout->createBlock('checkout/links');
			$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
			$cartBlockLink->addCartLink();
			$links = $topLinkBlock->getLinks();
			$link = array_shift($links);

			$arr['total']	= 	strip_tags(Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal()));
			$arr['qty']		= 	$cart->getItemsQty();
			$arr['toplink']	=	$link->getLabel();
			$arr['sidebar']	=	$block_sidebar->toHtml();
			$arr['check_crosssell']	=	0;

			if(Mage::helper("ajaxcart")->getGeneral_Crosssell() == 1 && Mage::helper("ajaxcart")->checkMobilePhp() != 1){
				$crosssellBlock = $layout->createBlock('ajaxcart/cart_crosssell')
				->setTemplate('em_ajaxcart/cart/crosssell.phtml');
				$arr['check_crosssell']	=	1;
				$arr['crosssell']	=	$crosssellBlock->toHtml();
			}
			$arr['sidebar_mobile']	=	$block_sidebar_mobile->toHtml();
		}
		if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']))
		{	// if IE
			echo json_encode($arr, JSON_HEX_TAG);exit;
		}else{	// other browser
			echo json_encode($arr);exit;
		}
    }

	public function addgroupAction(){ 
        $orderItemIds = $this->getRequest()->getParam('order_items', array());
        if (is_array($orderItemIds)) {
            $itemsCollection = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addIdFilter($orderItemIds)
                ->load();
            /* @var $itemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */
            $cart = $this->_getCart();
			
            foreach ($itemsCollection as $item) {
                try {
                    $cart->addOrderItem($item, 1);
                } catch (Mage_Core_Exception $e) {
					$check = 'failed';
                    if ($this->_getSession()->getUseNotice(true)) {
						$tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
                    } else {
						 $tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
                    }
                } catch (Exception $e) {
                    $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
                    Mage::logException($e);
                    $this->_goBack();
                }
            }
            if($cart->save()){
				$this->_getSession()->setCartWasUpdated(true);
				
				$message = $this->__('Total of %d product(s) were successfully added to your shopping cart.', count($itemsCollection));
				$check = 'success';
				$tmp_product	=	'<div id="message_ajax">'.$message.'</div>';
			}
			
			$url = $this->getRequest()->getParam('return_url');
			if ($url) {
				header("Location:".$url);exit;
			}
			
			$this->loadLayout();
			$layout = $this->getLayout();
			$block_sidebar	=	$layout->getBlock('cart_sidebar');
			$block_sidebar_mobile	=	$layout->getBlock('cart_sidebar_mobile');
			$cartBlockLink = $layout->createBlock('checkout/links');
			$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
			$cartBlockLink->addCartLink();
			$links = $topLinkBlock->getLinks();
			$link = array_shift($links);
			$arr['check'] 	= 	$check;
			$arr['msg']		= 	$tmp_product;
			if($check == 'success'){
				$arr['total']	= 	strip_tags(Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal()));
				$arr['qty']		= 	$cart->getItemsQty();
				$arr['toplink']	=	$link->getLabel();
				$arr['sidebar']	=	$block_sidebar->toHtml();
				$arr['check_crosssell']	=	0;

				if(Mage::helper("ajaxcart")->getGeneral_Crosssell() == 1 && Mage::helper("ajaxcart")->checkMobilePhp() != 1){
					$crosssellBlock = $layout->createBlock('ajaxcart/cart_crosssell')
					->setTemplate('em_ajaxcart/cart/crosssell.phtml');
					$arr['check_crosssell']	=	1;
					$arr['crosssell']	=	$crosssellBlock->toHtml();
				}
				$arr['sidebar_mobile']	=	$block_sidebar_mobile->toHtml();
			}
			if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']))
			{	// if IE
				echo json_encode($arr, JSON_HEX_TAG);exit;
			}else{	// other browser
				echo json_encode($arr);exit;
			}
        }
        $this->_goBack();
    }

	public function addtocartAction(){
        $this->indexAction();
    }
	
    public function preDispatch(){
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction(){
		$this->initTheme();
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
					$product = $item->getProduct();
                    $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());					
					$img = "<img src='".Mage::helper('catalog/image')->init($product, 'image')->resize(60,null)."' />";
					$tmp_product = '<div id="message_ajax"><div class="ajaxcart_image">'.$img.'</div><div class="ajaxcart_message">'.$message.'</div></div>';
					$check = 'success';	
                }
            }
        } catch (Mage_Core_Exception $e) {	
			$check = 'failed';
			if ($this->_getSession()->getUseNotice(true)) {
				$tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
            } else {
                $tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';	
            }
		} catch (Exception $e) {
			Mage::register('ajaxcart_message',$this->__('Cannot update the item.'));
        }
		
		$url = $this->getRequest()->getParam('return_url');
		if ($url) {
			header("Location:".$url);exit;
		}
		
		$this->loadLayout();
		$layout = $this->getLayout();
		$block_sidebar	=	$layout->getBlock('cart_sidebar');
		$block_sidebar_mobile	=	$layout->getBlock('cart_sidebar_mobile');
		$cartBlockLink = $layout->createBlock('checkout/links');
		$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
		$cartBlockLink->addCartLink();
		$links = $topLinkBlock->getLinks();
		$link = array_shift($links);

		$arr['check'] 	= 	$check;
		$arr['msg']		= 	$tmp_product;
		if($check == 'success'){
			$arr['total']	= 	strip_tags(Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal()));
			$arr['qty']		= 	$cart->getItemsQty();
			$arr['toplink']	=	$link->getLabel();
			$arr['sidebar']	=	$block_sidebar->toHtml();
			$arr['check_crosssell']	=	0;

			if(Mage::helper("ajaxcart")->getGeneral_Crosssell() == 1 && Mage::helper("ajaxcart")->checkMobilePhp() != 1){
				$crosssellBlock = $layout->createBlock('ajaxcart/cart_crosssell')
				->setTemplate('em_ajaxcart/cart/crosssell.phtml');
				$arr['check_crosssell']	=	1;
				$arr['crosssell']	=	$crosssellBlock->toHtml();
			}
			$arr['sidebar_mobile']	=	$block_sidebar_mobile->toHtml();
		}
		if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']))
		{	// if IE
			echo json_encode($arr, JSON_HEX_TAG);exit;
		}else{	// other browser
			echo json_encode($arr);exit;
		}
    }
	
	/**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack(){
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            /*if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }*/

            //$this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }

}