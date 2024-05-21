<?php
class AddressPopulator {
    public function populateAddress($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "address WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."address WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "address (
                    `id_address`, 
                    `id_country`,
                    `id_state`,
                    `id_customer`,
                    `id_manufacturer`,
                    `id_supplier`,
                    `id_warehouse`,
                    `alias`,
                    `lastname`,
                    `firstname`,
                    `address1`,
                    `address2`,
                    `postcode`,
                    `city`,
                    `other`,
                    `phone`,
                    `phone_mobile`,
                    `vat_number`,
                    `dni`,
                    `date_add`,
                    `date_upd`,
                    `active`,
                    `deleted`,
                    `company`
                ) 
                VALUES (
                    '" . pSQL($value['id_address']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_state']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['alias']) . "',
                    '" . pSQL($value['lastname']) . "',
                    '" . pSQL($value['firstname']) . "',
                    '" . pSQL($value['address1']) . "',
                    '" . pSQL($value['address2']) . "',
                    '" . pSQL($value['postcode']) . "',
                    '" . pSQL($value['city']) . "',
                    '" . pSQL($value['other']) . "',
                    '" . pSQL($value['phone']) . "',
                    '" . pSQL($value['phone_mobile']) . "',
                    '" . pSQL($value['vat_number']) . "',
                    '" . pSQL($value['dni']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    '" . pSQL($value['company']) . "'
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

    public function populateAddressFormat($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "address_format WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."address_format WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "address_format (
                    `id_country`,
                    `format`
                ) 
                VALUES (
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['format']) . "'
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

    
    

    public function populateAllAddresses($conn, $prefix) {
        $this->populateAddress($conn,$prefix);
        $this->populateAddressFormat($conn,$prefix);
        
    }
}