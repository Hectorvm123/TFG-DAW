<?php
class WarehousePopulator {
    private function populateWarehouse($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse (
                    `id_warehouse`, 
                    `id_country`,
                    `id_address`,
                    `id_employee`,
                    `reference`,
                    `name`,
                    `management_type`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_address']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['management_type']) . "',
                    '" . pSQL($value['deleted']) . "'
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

    private function populateWarehouseShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse_shop (
                    `id_warehouse`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
                    '" . pSQL($value['id_shop']) . "'
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
    

    public function populateAllWarehouses($conn, $prefix) {
        $this->populateWarehouse($conn,$prefix);
        $this->populateWarehouseShop($conn,$prefix);
        
    }
}