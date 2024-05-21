<?php
class SupplierPopulator {

    private function populateSupplier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier (
                    `id_supplier`,
                    `name`, 
                    `date_add`,
                    `date_upd`,
                    `active`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['name']) . "', 
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['active']) . "'
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


    private function populateSupplierLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier_lang (
                    `id_supplier`,
                    `id_lang`, 
                    `description`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['description']) . "',
                    '" . pSQL($value['meta_title']) . "',
                    '" . pSQL($value['meta_keywords']) . "',
                    '" . pSQL($value['meta_description']) . "'

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

    private function populateSupplierShop($conn, $prefix){
        try {   
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier_shop (
                    `id_supplier`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
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
    
    

    public function populateAllSuppliers($conn, $prefix) {
        $this->populateSupplier($conn,$prefix);
        $this->populateSupplierLang($conn,$prefix);
        $this->populateSupplierShop($conn,$prefix);
        
    }
}