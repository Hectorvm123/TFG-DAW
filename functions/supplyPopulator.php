<?php
class SupplyPopulator {
    private function populateSupplyOrder($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order (
                    `id_supply_order`, 
                    `id_supplier`,
                    `supplier_name`,
                    `id_lang`,
                    `id_warehouse`,
                    `id_supply_order_state`,
                    `id_currency`,
                    `id_ref_currency`,
                    `reference`,
                    `date_add`,
                    `date_upd`,
                    `date_delivery_expect`,
                    `total_te`,
                    `total_with_discount_te`,
                    `total_tax`,
                    `total_ti`,
                    `discount_rate`,
                    `discount_value_te`,
                    `is_template`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order']) . "', 
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['supplier_name']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['id_supply_order_state']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_ref_currency']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['date_delivery_expect']) . "',
                    '" . pSQL($value['total_te']) . "',
                    '" . pSQL($value['total_with_discount_te']) . "',
                    '" . pSQL($value['total_tax']) . "',
                    '" . pSQL($value['total_ti']) . "',
                    '" . pSQL($value['discount_rate']) . "',
                    '" . pSQL($value['discount_value_te']) . "',
                    '" . pSQL($value['is_template']) . "'
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

    private function populateSupplyOrderHistory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_history WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_history WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_history (
                    `id_supply_order_history`, 
                    `id_supply_order`,
                    `id_employee`,
                    `employee_lastname`,
                    `employee_firstname`,
                    `id_state`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_history']) . "', 
                    '" . pSQL($value['id_supply_order']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['employee_lastname']) . "',
                    '" . pSQL($value['employee_firstname']) . "',
                    '" . pSQL($value['id_state']) . "',
                    '" . pSQL($value['date_add']) . "'
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

    private function populateSupplyOrderDetail($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_detail WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_detail WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_detail (
                    `id_supply_order_detail`, 
                    `id_supply_order`,
                    `id_currency`,
                    `id_product`,
                    `id_product_attribute`,
                    `reference`,
                    `supplier_reference`,
                    `name`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `npm`,
                    `exchange_rate`,
                    `unit_price_te`,
                    `quantity_expected`,
                    `quantity_received`,
                    `price_te`,
                    `discount_rate`,
                    
                    `discount_value_te`,
                    `price_with_discount_te`,
                    `tax_rate`,
                    `tax_value`,
                    `total_ti`,
                    `tax_value_with_order_discount`,
                    `price_value_with_order_discount_te`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_detail']) . "', 
                    '" . pSQL($value['id_supply_order']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '" . pSQL($value['isbn']) . "',
                    '" . pSQL($value['upc']) . "',
                    '" . pSQL($value['npm']) . "',
                    '" . pSQL($value['exchange_rate']) . "',
                    '" . pSQL($value['unit_price_te']) . "',
                    '" . pSQL($value['quantity_expected']) . "',
                    '" . pSQL($value['quantity_received']) . "',
                    '" . pSQL($value['price_te']) . "',
                    '" . pSQL($value['discount_rate']) . "',

                    '" . pSQL($value['discount_value_te']) . "',
                    '" . pSQL($value['price_with_discount_te']) . "',
                    '" . pSQL($value['tax_rate']) . "',
                    '" . pSQL($value['tax_value']) . "',
                    '" . pSQL($value['total_ti']) . "',
                    '" . pSQL($value['tax_value_with_order_discount']) . "',
                    '" . pSQL($value['price_value_with_order_discount_te']) . "'
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

    private function populateSupplyOrderReceiptHistory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_receipt_history WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_receipt_history WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_receipt_history (
                    `id_supply_order_receipt_history`, 
                    `id_sypply_order_detail`,
                    `id_employee`,
                    `employee_lastname`,
                    `employee_firstname`,
                    `id_supply_order_state`,
                    `quantity`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_receipt_history']) . "', 
                    '" . pSQL($value['id_sypply_order_detail']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['employee_lastname']) . "',
                    '" . pSQL($value['employee_firstname']) . "',
                    '" . pSQL($value['id_supply_order_state']) . "',
                    '" . pSQL($value['quantity']) . "',
                    '" . pSQL($value['date_add']) . "'
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

    private function populateSupplyOrderState($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_state WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_state WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_state (
                    `id_supply_order_state`, 
                    `delivery_note`,
                    `editable`,
                    `receipt_state`,
                    `pending_receipt`,
                    `enclosed`,
                    `color`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_state']) . "', 
                    '" . pSQL($value['delivery_note']) . "',
                    '" . pSQL($value['editable']) . "',
                    '" . pSQL($value['receipt_state']) . "',
                    '" . pSQL($value['pending_receipt']) . "',
                    '" . pSQL($value['enclosed']) . "',
                    '" . pSQL($value['color']) . "'
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

    private function populateSupplyOrderStateLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_state_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_state_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_state_lang (
                    `id_supply_order_state`, 
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_state']) . "', 
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
    
    

    public function populateAllSupplies($conn, $prefix) {
        $this->populateSupplyOrder($conn,$prefix);
        $this->populateSupplyOrderHistory($conn,$prefix);
        $this->populateSupplyOrderDetail($conn,$prefix);
        $this->populateSupplyOrderReceiptHistory($conn,$prefix);
        $this->populateSupplyOrderState($conn,$prefix);
        $this->populateSupplyOrderStateLang($conn,$prefix);
        
    }
}