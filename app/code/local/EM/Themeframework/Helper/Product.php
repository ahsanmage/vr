<?php
class EM_Themeframework_Helper_Product {
	function getPreviousNextProducts($product) {

        //condition to sort
        $order = Mage::getStoreConfig('catalog/frontend/default_sort_by');
        $direction = 'asc'; // asc or desc

        $storeId = Mage::app()->getStore()->getId();
        $cache = Mage::getSingleton('core/cache');
        $key = 'next-pre-product'.$storeId;		
        $categoryId = (int) Mage::app()->getRequest()->getParam('category');		
		if($categoryId)
        {
			$key .= 'cat_'. $categoryId;
		}
		
		$productKey = $key. 'pid_'.$product->getId();
		if($result = $cache->load($productKey)){		
			$result = unserialize($result);
			$prevId = $result['prev_id'];
			$nextId = $result['next_id'];
			$_prev_prod = Mage::getModel('catalog/product')->load( $prevId );			
			$_next_prod = Mage::getModel('catalog/product')->load( $nextId );
			if($_prev_prod && $_next_prod)
				return array($_prev_prod, $_next_prod);
		}
		
		$lifetime = Mage::getStoreConfig('core/cache/lifetime');
        if(! $data = $cache->load($key)){
            if($categoryId)
            {
                // having category id param
                $cat = Mage::getModel('catalog/category')->load( $categoryId ); // load category
                $products_collection = $cat->getProductCollection();
            }
            else{
                // get all products data
                $products_collection = Mage::getModel('catalog/product')->getCollection();
            }

            $products_collection->addAttributeToSort($order, $direction)
                ->addFieldToFilter('visibility',Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            $products_AllIds = $products_collection->getAllIds(); // get all products from the category
            $data = serialize($products_AllIds);
			
            $cache->save(urlencode($data), $key, array(Mage_Catalog_Model_Category::CACHE_TAG,Mage_Catalog_Model_Product::CACHE_TAG), $lifetime);
        }
        else{
            $products_AllIds = unserialize(urldecode($data));
        }

		$_product_id = $product->getId();
		$_pos = array_search($_product_id, $products_AllIds); // get position of current product

		$_next_pos = $_pos+1;
		$_prev_pos = $_pos-1;
		$_prevId = $_nextId = null;
		// get the next product url

		if( isset($products_AllIds[$_next_pos]) ) {
			$_nextId = $products_AllIds[$_next_pos];
			$_next_prod = Mage::getModel('catalog/product')->load( $_nextId );
		} 
		else {
			$_nextId = reset($products_AllIds);
			$_next_prod = Mage::getModel('catalog/product')->load( $_nextId );
		}

		// get the prev product url
		if( isset($products_AllIds[$_prev_pos]) ) {
			$_prevId = $products_AllIds[$_prev_pos];
			$_prev_prod = Mage::getModel('catalog/product')->load($_prevId);
			
		} 
		else {
			$_prevId = end($products_AllIds);
			$_prev_prod = Mage::getModel('catalog/product')->load( $_prevId );
		}
		$cache->save(serialize(array('prev_id' => $_prevId,'next_id' => $_nextId)), $productKey, array(Mage_Catalog_Model_Category::CACHE_TAG,Mage_Catalog_Model_Product::CACHE_TAG), $lifetime);
		return array($_prev_prod, $_next_prod);
	}
}