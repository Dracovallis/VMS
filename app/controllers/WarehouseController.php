<?php

class WarehouseController extends ControllerBase
{
    public function indexAction()
    {
        if (!empty($_POST) && !empty($_POST['submit'])) {
            $warehouseHeightMeters = floatval($_POST['warehouseHeight']) ?? 3.6;

            $smallestDimensionMeters = $warehouseHeightMeters;

            $setWarehouseLength = !empty($_POST['setLength']);
            if ($setWarehouseLength && !empty($_POST['warehouseLength'])) {
                $warehouseLengthMeters = floatval($_POST['warehouseLength']);

                $smallestDimensionMeters = min($warehouseLengthMeters,   $warehouseHeightMeters);
            }

            $desiredProductQty =  (int)$_POST['desiredProductQty'] ?? 5;

            $query = "SELECT 
            length / 1000 as length, width / 1000 as width, height / 1000 as height, length_class_id
            FROM product WHERE status = 1";
            $stmt = $this->_db->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $laff = new \Cloudstek\PhpLaff\Packer();

            $maxDimension = 0;
            $packedProducts = 0;
            $packedBoxes = 0;
            $unpacked = 0;
            $boxes = [];
            // $lengthClassesModel = new LengthClass($this->_db);
            // $lengthClasses = $lengthClassesModel->find([], true);
            foreach ($products as $product) {
                // $this->mapProductLengthUnits($lengthClasses, $product);
                if (
                    $product['width'] <= $smallestDimensionMeters &&
                    $product['height'] <= $smallestDimensionMeters &&
                    $product['length'] <= $smallestDimensionMeters
                ) {
                    // first we pack the same products on top of one another because the library just can't handle it xD
                    $stack = [];
                    for ($i = 0; $i < $desiredProductQty; $i++) {
                        $stack[] = $product;
                    }

                    $laff->pack($stack);
                    $box = $laff->get_container_dimensions();
                    $boxes[] = $box;

                    $maxDimension = max($maxDimension, $box['width'], $box['length'], $box['height']);

                    $packedProducts += $desiredProductQty;
                    $packedBoxes++;
                } else {
                    $unpacked += $desiredProductQty;
                }
            }

            if (!empty($warehouseLengthMeters)) {
                $maxDimension = $warehouseLengthMeters;
            }

            if (!empty($packedProducts)) {
      
                // the function does not allow to pass height so we swap it with width
                $laff->pack($boxes, ['width' => $warehouseHeightMeters, 'length' => $maxDimension]);
                $warehouseDimensions = $laff->get_container_dimensions();
                $warehouseVolume = $laff->get_container_volume();
            }



            $this->setVars([
                'warehouseWidth' => $warehouseDimensions['height'] ?? 0,
                'warehouseHeight' => $warehouseDimensions['width'] ?? 0,
                'warehouseLength' => $warehouseDimensions['length'] ?? 0,
                'warehouseVolume' => $warehouseVolume ?? 0,
                'unpacked' => $unpacked,
                'packedBoxes' => $packedBoxes,
                'packedProducts' => $packedProducts,
                'postHeight' =>  $warehouseHeightMeters,
                'postLength' =>  $warehouseLengthMeters ?? false,
                'postProductQty' =>  $desiredProductQty,
                'setWarehouseLength' => $setWarehouseLength
            ]);
        }




        $this->render();
    }

    private function mapProductLengthUnits($lengthClasses, &$product)
    {
        // 1=cm=1.00000000, 2=mm=10.00000000, 3=inch=0.39370000
        $product['width'] = ($product['width'] * $lengthClasses[$product['length_class_id']]['value']) / 100;
        $product['length'] = ($product['length'] * $lengthClasses[$product['length_class_id']]['value']) / 100;
        $product['height'] = ($product['height'] * $lengthClasses[$product['length_class_id']]['value']) / 100;
    }
}
