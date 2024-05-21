<?php
class DeliveryPopulator {
    private function populateDelivery($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "delivery WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."delivery WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "delivery (
                    `id_delivery`, 
                    `id_shop`,
                    `id_shop_group`,
                    `id_carrier`,
                    `id_range_price`,
                    `id_range_weight`,
                    `id_zone`,
                    `price`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_range_price']) . "',
                    '" . pSQL($value['id_range_weight']) . "',
                    '" . pSQL($value['id_zone']) . "',
                    '" . pSQL($value['price']) . "'
                )";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }
    
    

    public function populateAllDeliveries($conn, $prefix) {
        $this->populateDelivery($conn,$prefix);

        
    }
}