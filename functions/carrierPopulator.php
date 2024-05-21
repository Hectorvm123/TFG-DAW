<?php
class CarrierPopulator {

    private function populateCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier (
                    `id_carrier`,
                    `id_reference`,
                    `name`, 
                    `url`,
                    `active`,
                    `deleted`,
                    `shipping_handling`,
                    `range_behavior`,
                    `is_module`,
                    `is_free`,
                    `shipping_external`,
                    `need_range`,
                    `external_module_name`,
                    `shipping_method`,
                    `position`,
                    `max_width`,
                    `max_height`,
                    `max_depth`,
                    `grade`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_reference']) . "',
                    '" . pSQL($value['name']) . "', 
                    '" . pSQL($value['url']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    '" . pSQL($value['shipping_handling']) . "',
                    '" . pSQL($value['range_behavior']) . "',
                    '" . pSQL($value['is_module']) . "',
                    '" . pSQL($value['is_free']) . "',
                    '" . pSQL($value['shipping_external']) . "',
                    '" . pSQL($value['need_range']) . "',
                    '" . pSQL($value['external_module_name']) . "',
                    '" . pSQL($value['shipping_method']) . "',
                    '" . pSQL($value['position']) . "',
                    '" . pSQL($value['max_width']) . "',
                    '" . pSQL($value['max_height']) . "',
                    '" . pSQL($value['max_depth']) . "',
                    '" . pSQL($value['grade']) . "'

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


    private function populateCarrierShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_shop (
                    `id_carrier`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
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

    private function populateCarrierLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_lang (
                    `id_carrier`,
                    `id_shop`,
                    `id_lang`, 
                    `delay`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['delay']) . "'

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

    private function populateCarrierZone($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_zone WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_zone WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_zone (
                    `id_carrier`,
                    `id_zone`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_zone']) . "'

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

    private function populateOderCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_carrier (
                    `id_order_carrier`,
                    `id_order`,
                    `id_order_invoice`, 
                    `weight`,
                    `shipping_cost_tax_excl`,
                    `shipping_cost_tax_incl`,
                    `tracking_number`,
                    `date_add`,
                    `id_carrier`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_carrier']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['id_order_invoice']) . "', 
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['shipping_cost_tax_excl']) . "',
                    '" . pSQL($value['shipping_cost_tax_incl']) . "',
                    '" . pSQL($value['tracking_number']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['id_carrier']) . "'

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

    private function populateCarrierGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_group (
                    `id_carrier`, 
                    `id_group`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "', 
                    '" . pSQL($value['id_group']) . "'
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
    

    public function populateAllCarriers($conn, $prefix) {
        $this->populateCarrier($conn,$prefix);
        $this->populateCarrierShop($conn,$prefix);
        $this->populateCarrierLang($conn,$prefix);
        $this->populateCarrierZone($conn,$prefix);
        $this->populateOderCarrier($conn,$prefix);
        $this->populateCarrierGroup($conn,$prefix);
        
    }
}