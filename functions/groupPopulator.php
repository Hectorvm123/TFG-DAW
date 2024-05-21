<?php
class GroupPopulator{
    private function populateGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group (
                    `id_group`, 
                    `reduction`, 
                    `price_display_method`,
                    `show_prices`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
                    '" . pSQL($value['reduction']) . "', 
                    '" . pSQL($value['price_display_method']) . "',
                    '" . pSQL($value['show_prices']) . "',
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

    private function populateGroupLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_lang (
                    `id_group`, 
                    `id_lang`, 
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
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

    private function populateGroupReduction($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_reduction WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_reduction WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_reduction (
                    `id_group`, 
                    `id_group_reduction`, 
                    `id_category`,
                    `reduction`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
                    '" . pSQL($value['id_group_reduction']) . "', 
                    '" . pSQL($value['id_category']) . "',
                    '" . pSQL($value['reduction']) . "'

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

    private function populateGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_shop (
                    `id_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
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

    private function populateShopGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "shop_group WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "shop_group (
                    `id_shop_group`, 
                    `name`,
                    `share_customer`,
                    `share_stock`,
                    `active`,
                    `deleted`,
                    `color`
                ) 
                VALUES (
                    '" . pSQL($value['id_shop_group']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['share_customer']) . "',
                    '" . pSQL($value['share_stock']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    0
                )
                ON DUPLICATE KEY UPDATE 
                    id_shop_group = VALUES(id_shop_group), 
                    name = VALUES(name), 
                    share_customer = VALUES(share_customer), 
                    share_stock = VALUES(share_stock), 
                    active = VALUES(active), 
                    deleted = VALUES(deleted);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateSpecificPriceRuleConditionGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "specific_price_rule_condition_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."specific_price_rule_condition_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "specific_price_rule_condition_group (
                    `id_specific_price_rule_condition_group`, 
                    `id_specific_price_rule`
                ) 
                VALUES (
                    '" . pSQL($value['id_specific_price_rule_condition_group']) . "', 
                    '" . pSQL($value['id_specific_price_rule']) . "'
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
    

    
    public function populateAllGroups($conn, $prefix) {
        $this->populateGroup($conn,$prefix);           
        $this->populateGroupLang($conn,$prefix);
        $this->populateGroupReduction($conn,$prefix);
        $this->populateGroupShop($conn,$prefix);
        $this->populateShopGroup($conn,$prefix);
        $this->populateSpecificPriceRuleConditionGroup($conn,$prefix);
    }
}