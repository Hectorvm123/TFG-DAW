<?php
class CartPopulator{
    private function populateCart($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart (
                    `id_cart`, 
                    `id_shop_group`,
                    `id_shop`,
                    `id_carrier`,
                    `delivery_option`,
                    `id_lang`,
                    `id_address_delivery`,
                    `id_address_invoice`,
                    `id_currency`,
                    `id_customer`,
                    `id_guest`,
                    `secure_key`,
                    `recyclable`,
                    `gift`,
                    `gift_message`,
                    `mobile_theme`,
                    `allow_seperated_package`,
                    `date_add`,
                    `date_upd`,
                    `checkout_session_data`                 
                ) 
                VALUES (
                    '" . pSQL($value['id_cart']) . "', 
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['delivery_option']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_address_delivery']) . "',
                    '" . pSQL($value['id_address_invoice']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_guest']) . "',
                    '" . pSQL($value['secure_key']) . "',
                    '" . pSQL($value['recyclable']) . "',
                    '" . pSQL($value['gift']) . "',
                    '" . pSQL($value['gift_message']) . "',
                    '" . pSQL($value['mobile_theme']) . "',
                    '" . pSQL($value['allow_seperated_package']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
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


    private function populateCartCartRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_cart_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_cart_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_cart_rule (
                    `id_cart`, 
                    `id_cart_rule`
                    ) VALUES (
                        '" . pSQL($value['id_cart']) . "', 
                        '" . pSQL($value['id_cart_rule']) . "'
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


    private function populateCartProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_product (
                    `id_cart`, 
                    `id_product`,
                    `id_address_delivery`,
                    `id_shop`,
                    `id_product_attribute`,
                    `quantity`,
                    `date_add`,
                    `id_customization`
                ) 
                VALUES (
                    '" . pSQL($value['id_cart']) . "', 
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_address_delivery']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['quantity']) . "',
                    '" . pSQL($value['date_add']) . "',
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

    private function populateCartRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_rule (
                    `id_cart_rule`,
                    `id_customer`, 
                    `date_from`,
                    `date_to`,
                    `description`,
                    `quantity_per_user`,
                    `priority`,
                    `partial_use`,
                    `code`,
                    `minimum_amount`,
                    `minimum_amount_tax`,
                    `minimum_amount_currency`,
                    `minimum_amount_shipping`,
                    `country_restriction`, 
                    `carrier_restriction`,
                    `cart_rule_restriction`,
                    `shop_restriction`,
                    `free_shipping`,
                    `reduction_percent`,
                    `reduction_amount`,
                    `reduction_tax`,
                    `reduction_currency`,
                    `reduction_product`,
                    `gift_product`,
                    `highlight`,
                    `active`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_cart_rule']) . "',
                    '" . pSQL($value['id_customer']) . "', 
                    '" . pSQL($value['date_from']) . "',
                    '" . pSQL($value['date_to']) . "',
                    '" . pSQL($value['description']) . "',
                    '" . pSQL($value['quantity_per_user']) . "',
                    '" . pSQL($value['priority']) . "',
                    '" . pSQL($value['partial_use']) . "',
                    '" . pSQL($value['code']) . "',
                    '" . pSQL($value['minimum_amount']) . "',
                    '" . pSQL($value['minimum_amount_tax']) . "',
                    '" . pSQL($value['minimum_amount_currency']) . "',
                    '" . pSQL($value['minimum_amount_shipping']) . "',
                    '" . pSQL($value['country_restriction']) . "', 
                    '" . pSQL($value['carrier_restriction']) . "',
                    '" . pSQL($value['cart_rule_restriction']) . "',
                    '" . pSQL($value['shop_restriction']) . "',
                    '" . pSQL($value['free_shipping']) . "',
                    '" . pSQL($value['reduction_percent']) . "',
                    '" . pSQL($value['reduction_amount']) . "',
                    '" . pSQL($value['reduction_tax']) . "',
                    '" . pSQL($value['reduction_currency']) . "',
                    '" . pSQL($value['reduction_product']) . "',
                    '" . pSQL($value['gift_product']) . "',
                    '" . pSQL($value['highlight']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "'

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

    private function populateCartRuleCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_carrier (
                    `id_cart_rule`, 
                    `id_carrier`
                    
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule']) . "', 
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

    private function populateCartRuleCombination($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_combination WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_combination WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_combination (
                    `id_cart_rule_1`, 
                    `id_cart_rule_2`
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule_1']) . "', 
                        '" . pSQL($value['id_cart_rule_2']) . "'
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

    private function populateCartRuleCountry($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_country WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_country WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_country (
                    `id_cart_rule`, 
                    `id_country`
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule']) . "', 
                        '" . pSQL($value['id_country']) . "'
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
    

    private function populateCartRuleGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_group (
                    `id_cart_group`, 
                    `id_group`
                    ) VALUES (
                        '" . pSQL($value['id_cart_group']) . "', 
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
    private function populateCartRuleLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_lang (
                    `id_cart_rule`, 
                    `id_lang`,
                    `name`
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule']) . "', 
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

    private function populateCartRuleProductRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_product_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_product_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_rule_product_rule (
                    `id_product_rule`, 
                    `id_product_rule_group`, 
                    `type`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_rule']) . "', 
                    '" . pSQL($value['id_product_rule_group']) . "', 
                    '" . pSQL($value['type']) . "'
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

    private function populateCartRuleProductRuleGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_product_rule_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_product_rule_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_product_rule_group (
                    `id_product_rule_group`, 
                    `id_cart_rule`,
                    `quantity`
                    ) VALUES (
                        '" . pSQL($value['id_product_rule_group']) . "', 
                        '" . pSQL($value['id_cart_rule']) . "',
                        '" . pSQL($value['quantity']) . "'
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
    
    private function populateCartRuleProductRuleValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_product_rule_value WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_product_rule_value WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_rule_product_rule_value (
                    `id_product_rule`, 
                    `id_item`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_rule']) . "', 
                    '" . pSQL($value['id_item']) . "'
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

    private function populateCartRuleShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_rule_shop (
                    `id_cart_rule`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_cart_rule']) . "', 
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

    private function populateOrderCartRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_cart_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_cart_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_cart_rule (
                    `id_order_cart_rule`, 
                    `id_order`, 
                    `id_cart_rule`,
                    `id_order_invoice`,
                    `name`,
                    `value`,
                    `value_tax_excl`,
                    `free_shipping`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_cart_rule']) . "', 
                    '" . pSQL($value['id_order']) . "', 
                    '" . pSQL($value['id_cart_rule']) . "',
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['value']) . "',
                    '" . pSQL($value['value_tax_excl']) . "',
                    '" . pSQL($value['free_shipping']) . "',
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


    public function populateAllCarts($conn, $prefix) {
        $this->populateCart($conn,$prefix);
        $this->populateCartCartRule($conn,$prefix);
        $this->populateCartProduct($conn,$prefix);
        $this->populateCartRule($conn,$prefix);
        $this->populateCartRuleCarrier($conn,$prefix);
        $this->populateCartRuleCombination($conn,$prefix);
        $this->populateCartRuleCountry($conn,$prefix);
        $this->populateCartRuleGroup($conn,$prefix);
        $this->populateCartRuleLang($conn,$prefix);
        $this->populateCartRuleProductRule($conn,$prefix);
        $this->populateCartRuleProductRuleGroup($conn,$prefix);
        $this->populateCartRuleProductRuleValue($conn,$prefix);
        $this->populateCartRuleShop($conn,$prefix);
        $this->populateOrderCartRule($conn,$prefix);
    }
}