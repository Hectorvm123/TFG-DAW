<?php
class TaxPopulator {
    private function populateTaxRulesGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rules_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rules_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rules_group (
                    `id_tax_rules_group`, 
                    `name`,
                    `active`,
                    `deleted`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rules_group']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['active']) . "',
                    0,
                    '" . pSQL(date("Y-m-d H:i:s")) . "',
                    '" . pSQL(date("Y-m-d H:i:s")) . "'
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

    private function populateTaxRulesGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rules_group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rules_group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rules_group_shop (
                    `id_tax_rules_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rules_group']) . "', 
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

    private function populateTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax (
                    `id_tax`,
                    `rate`,
                    `active`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['rate']) . "',
                    '" . pSQL($value['active']) . "',
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

    private function populateTaxLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_lang (
                    `id_tax`,
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax']) . "',
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

    private function populateTaxRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rule (
                    `id_tax_rule`,
                    `id_tax_rules_group`, 
                    `id_country`,
                    `id_state`,
                    `zipcode_from`,
                    `zipcode_to`,
                    `id_tax`,
                    `behavior`,
                    `description`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rule']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_state']) . "',
                    '" . pSQL($value['zipcode_from']) . "',
                    '" . pSQL($value['zipcode_to']) . "',
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['behavior']) . "',
                    '" . pSQL($value['description']) . "'

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

    private function populateOrderDetailTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_detail_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_detail_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_detail_tax (
                    `id_order_detail`,
                    `id_tax`, 
                    `unit_amount`,
                    `total_amount`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_detail']) . "',
                    '" . pSQL($value['id_tax']) . "', 
                    '" . pSQL($value['unit_amount']) . "',
                    '" . pSQL($value['total_amount']) . "'
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

    private function populateOrderInvoiceTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_invoice_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_invoice_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_invoice_tax (
                    `id_order_invoice`,
                    `type`,
                    `id_tax`,
                    `amount`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['type']) . "',
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['amount']) . "'

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

    
    

    public function populateAllTaxes($conn, $prefix) {
        $this->populateTax($conn,$prefix);
        $this->populateTaxLang($conn,$prefix);
        $this->populateTaxRule($conn,$prefix);
        $this->populateOrderDetailTax($conn,$prefix);
        $this->populateOrderInvoiceTax($conn,$prefix);
        $this->populateTaxRulesGroup($conn,$prefix);
        $this->populateTaxRulesGroupShop($conn,$prefix);
        
    }
}