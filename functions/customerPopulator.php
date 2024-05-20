<?php
class CustomerPopulator{
    
    private function populateCustomer($conn, $prefix){
        $query = $conn->prepare("SELECT * FROM " .$prefix. "customer WHERE 1");
        $query->execute();
    
        // set the resulting array to associative
        foreach($query->fetchAll() as $key=>$value) {
            $sql = "INSERT INTO ". _DB_PREFIX_ ."customer (`id_customer`, `id_shop_group`,  `id_shop`, `id_gender`, `id_default_group`, `id_lang`, `id_risk` , `company`, `siret`, 
                `ape`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, `birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`,
                `website`, `outstanding_allow_amount`, `show_public_prices`, `max_payment_days`, `secure_key`, `note`, `active`, `is_guest`, `deleted`, `date_add`, 
                `date_upd`, `reset_password_token`, `reset_password_validity`) 
                VALUES ('"
                . pSQL($value['id_customer']) . "', '" 
                . pSQL($value['id_shop_group']) . "', '" 
                . pSQL($value['id_shop']) . "', '" 
                . pSQL($value['id_gender']) . "', '" 
                . pSQL($value['id_default_group']) . "', '" 
                . pSQL($value['id_lang']) . "', '" 
                . pSQL($value['id_risk']) . "', '" 
                . pSQL($value['company']) . "', '" 
                . pSQL($value['siret']) . "', '" 
                . pSQL($value['ape']) . "', '" 
                . pSQL($value['firstname']) . "', '" 
                . pSQL($value['lastname']) . "', '" 
                . pSQL($value['email']) . "', '" 
                . pSQL($value['passwd']) . "', '" 
                . pSQL($value['last_passwd_gen']) . "', '" 
                . pSQL($value['birthday']) . "', '" 
                . pSQL($value['newsletter']) . "', '" 
                . pSQL($value['ip_registration_newsletter']) . "', '" 
                . pSQL($value['newsletter_date_add']) . "', '" 
                . pSQL($value['optin']) . "', '" 
                . pSQL($value['website']) . "', '" 
                . pSQL($value['outstanding_allow_amount']) . "', '" 
                . pSQL($value['show_public_prices']) . "', '" 
                . pSQL($value['max_payment_days']) . "', '" 
                . pSQL($value['secure_key']) . "', '" 
                . pSQL($value['note']) . "', '" 
                . pSQL($value['active']) . "', '" 
                . pSQL($value['is_guest']) . "', '" 
                . pSQL($value['deleted']) . "', '" 
                . pSQL($value['date_add']) . "', '" 
                . pSQL($value['date_upd']) . "', NULL, NULL)  
                ON DUPLICATE KEY UPDATE 
                id_shop = VALUES(id_shop), 
                id_gender = VALUES(id_gender), 
                id_default_group = VALUES(id_default_group), 
                id_lang = VALUES(id_lang), 
                id_risk = VALUES(id_risk), 
                company = VALUES(company), 
                siret = VALUES(siret), 
                ape = VALUES(ape), 
                firstname = VALUES(firstname), 
                lastname = VALUES(lastname), 
                email = VALUES(email), 
                passwd = VALUES(passwd), 
                last_passwd_gen = VALUES(last_passwd_gen), 
                birthday = VALUES(birthday), 
                newsletter = VALUES(newsletter), 
                ip_registration_newsletter = VALUES(ip_registration_newsletter), 
                newsletter_date_add = VALUES(newsletter_date_add), 
                optin = VALUES(optin), 
                website = VALUES(website), 
                outstanding_allow_amount = VALUES(outstanding_allow_amount), 
                show_public_prices = VALUES(show_public_prices), 
                max_payment_days = VALUES(max_payment_days), 
                secure_key = VALUES(secure_key), 
                note = VALUES(note), 
                active = VALUES(active), 
                is_guest = VALUES(is_guest), 
                deleted = VALUES(deleted), 
                date_add = VALUES(date_add), 
                date_upd = VALUES(date_upd), 
                reset_password_token = NULL,
                reset_password_validity = NULL";
    
            Db::getInstance()->execute($sql);
        }
    }
    
    private function populateCustomerGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "customer_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."customer_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."customer_group (`id_customer`, `id_group`) VALUES ('" . pSQL($value['id_customer']) . "', '" . pSQL($value['id_group']) . "')";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateCustomerMessage($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "customer_message WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."customer_message WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "customer_message (
                    `id_customer_message`, 
                    `id_customer_thread`,
                    `id_employee`,
                    `message`,
                    `file_name`,
                    `ip_address`,
                    `user_agent`,
                    `date_add`,
                    `date_upd`,
                    `private`,
                    `read`
                ) 
                VALUES (
                    '" . pSQL($value['id_customer_message']) . "', 
                    '" . pSQL($value['id_customer_thread']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['message']) . "',
                    '" . pSQL($value['file_name']) . "',
                    '" . pSQL($value['ip_address']) . "',
                    '" . pSQL($value['user_agent']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['private']) . "',
                    '" . pSQL($value['read']) . "'
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

    private function populateCustomerMessageSyncImap($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "customer_message_sync_imap WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."customer_message_sync_imap WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "customer_message_sync_imap (`md5_header`) VALUES ('" . pSQL($value['md5_header']) . "',  )"; 
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateCustomerThread($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "customer_thread WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."customer_thread WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "customer_thread (
                    `id_customer_thread`,
                    `id_shop`,
                    `id_lang`,
                    `id_contact`,
                    `id_customer`,
                    `id_order`,
                    `id_product`,
                    `status`,
                    `email`,
                    `token`,
                    `date_add`,
                    `date_upd`

                ) 
                VALUES (
                    '" . pSQL($value['id_customer_thread']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_contact']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['status']) . "',
                    '" . pSQL($value['email']) . "',
                    '" . pSQL($value['token']) . "',
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

    
    private function populateMailAlertCustomerOOS($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "mailalert_customer_oos WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."mailalert_customer_oos WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "mailalert_customer_oos (
                    `id_customer`,
                    `customer_mail`,
                    `id_product`,
                    `id_product_attribute`,
                    `id_shop`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['customer_mail']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_lang']) . "'
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
    


    public function populateAllCustomers($conn, $prefix) {
        $this->populateCustomer($conn,$prefix);
        $this->populateCustomerGroup($conn,$prefix);
        $this->populateCustomerMessage($conn,$prefix);
        $this->populateCustomerMessageSyncImap($conn,$prefix);
        $this->populateCustomerThread($conn,$prefix);
        $this->populateMailAlertCustomerOOS($conn,$prefix);
    }
}