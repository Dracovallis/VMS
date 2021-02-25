<?php

class ProductController extends ControllerBase
{
    public function indexAction($args = null)
    {
        $limit = 10;
        $page = !empty((int)$args['page']) ? (int)$args['page'] :  1;
        $offset = ($page - 1) * $limit;

        $params =  [
            'limit' => $limit,
            'offset' => $offset
        ];

        $productsModel = new Product($this->_db);
        $products = $productsModel->find($params, true);

        if (!empty($products)) {
            $productIds = array_keys($products);
            $conditionsArray = [];
            $bind = [];
            foreach ($productIds as $key => $id) {
                $conditionsArray[] = ':param' . $key;
                $bind['param' . $key] = $id;
            }
            $params = [
                'conditions' => 'product_id IN ( ' . implode(',', $conditionsArray) .  ') AND language_id = 1',
                'bind' => $bind
            ];
            $productDescriptionModel = new ProductDescription($this->_db);
            $productDescriptions = $productDescriptionModel->find($params, true);
        }


        $this->setVars([
            'products' => $products,
            'productDescriptions' => $productDescriptions ?? [],
            'page' => $page,
            'limit' => $limit
        ]);

        $this->render();
    }

    public function detailsAction($args = null)
    {
        if ($args['id']) {
            $productId = $args['id'];

            $productModel = new Product($this->_db);
            $product = $productModel->findFirst((int)$productId);

            if (!empty( $product)) {
                $productDescriptionModel = new ProductDescription($this->_db);
                $productDescription = $productDescriptionModel->findFirst($productId);
    
                $product['description'] = $productDescription;
    
                $this->setVars([
                    'product' => $product
                ]);
            }
        }

        $this->render();
    }
}
