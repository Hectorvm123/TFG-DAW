<?php
class StockPopulator {
    public function populateStock($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "stock WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."stock WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock (
                    `id_stock`, 
                    `id_warehouse`,
                    `id_product`,
                    `id_product_attribute`,
                    `reference`,
                    `ean13`,
                    `upc`,
                    `physical_quantity`,
                    `usable_quantity`,
                    `price_te`,
                    `isbn`,
                    `mpn`
                ) 
                VALUES (
                    '" . pSQL($value['id_stock']) . "', 
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '" . pSQL($value['upc']) . "',
                    '" . pSQL($value['physical_quantity']) . "',
                    '" . pSQL($value['usable_quantity']) . "',
                    '" . pSQL($value['price_te']) . "',
                    0,
                    0
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


    public function populateStockAvailable($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "stock_available WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."stock_available WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_available (
                    `id_stock_available`, 
                    `id_shop`,
                    `id_product`,
                    `id_product_attribute`,
                    `id_shop_group`,
                    `quantity`,
                    `reserved_quantity`,
                    `physical_quantity`,
                    `depends_on_stock`,
                    `out_of_stock`,
                    `location`
                ) 
                VALUES (
                    '" . pSQL($value['id_stock_available']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['quantity']) . "',
                    0,
                    0,
                    '" . pSQL($value['depends_on_stock']) . "',
                    '" . pSQL($value['out_of_stock']) . "',
                    ''

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

    public function populateStockMVT($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "stock_mvt WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."stock_mvt WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_mvt (
                    `id_stock_mvt`, 
                    `id_stock`,
                    `id_order`,
                    `id_supply_order`,
                    `id_stock_mvt_reason`,
                    `id_employee`,
                    `employee_lastname`,
                    `employee_firstname`,
                    `physical_quantity`,
                    `date_add`,
                    `sign`,
                    `price_te`,
                    `last_wa`,
                    `current_wa`,
                    `referer`
                ) 
                VALUES (
                    '" . pSQL($value['id_stock_mvt']) . "', 
                    '" . pSQL($value['id_stock']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['id_supply_order']) . "',
                    '" . pSQL($value['id_stock_mvt_reason']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['employee_lastname']) . "',
                    '" . pSQL($value['employee_firstname']) . "',
                    '" . pSQL($value['physical_quantity']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['sign']) . "',
                    '" . pSQL($value['price_te']) . "',
                    '" . pSQL($value['last_wa']) . "',
                    '" . pSQL($value['current_wa']) . "',
                    '" . pSQL($value['referer']) . "'

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

    public function populateStockMVTReason($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "stock_mvt_reason WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."stock_mvt_reason WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_mvt_reason (
                    `id_stock_mvt_reason`,
                    `sign`,                    
                    `date_add`,
                    `date_upd`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_stock_mvt_reason']) . "',
                    '" . pSQL($value['sign']) . "',                    
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
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

    public function populateStockMVTReasonLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "stock_mvt_reason_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."stock_mvt_reason_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_mvt_reason_lang (
                    `id_stock_mvt_reason`,
                    `id_lang`,                    
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_stock_mvt_reason']) . "',
                    '" . pSQL($value['id_lang']) . "',                    
                    '" . pSQL($value['name']) . "'

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
    

    public function populateAllStocks($conn, $prefix) {
        $this->populateStock($conn,$prefix);
        $this->populateStockAvailable($conn,$prefix);
        $this->populateStockMVT($conn,$prefix);
        $this->populateStockMVTReason($conn,$prefix);
        $this->populateStockMVTReasonLang($conn,$prefix);
        
    }
}