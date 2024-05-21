<?php
class EmployeePopulator{
    

    private function populateEmployee($conn, $prefix){            
        $query = $conn->prepare("SELECT * FROM " .$prefix. "employee WHERE 1");
        $query->execute();

        // set the resulting array to associative
        foreach($query->fetchAll() as $key=>$value) {
            //por cada value hacer insert en ps_employee y ps_employee_shop
            $sql = "INSERT INTO ". _DB_PREFIX_ ."employee (`id_employee`, `id_profile`, `id_lang`, `lastname`, `firstname`, `email`, `passwd`, `last_passwd_gen`, 
                `stats_date_from`, `stats_date_to`, `stats_compare_from`, `stats_compare_to`, `stats_compare_option`, `preselect_date_range`, `bo_color`, `bo_theme`, `bo_css`, `default_tab`, `bo_width`, `bo_menu`, `active`, `optin`, `id_last_order`, `id_last_customer_message`, `id_last_customer`) 
                VALUES ('" . pSQL($value['id_employee']) . "', '" . pSQL($value['id_profile']) . "', '" . pSQL($value['id_lang']) . "', '" . pSQL($value['lastname']) . "', '" . pSQL($value['firstname']) . "', '" . pSQL($value['email']) . "', '" . pSQL($value['passwd']) . "', '" . pSQL($value['last_passwd_gen']) . "', '" . pSQL($value['stats_date_from']) . "', '" . pSQL($value['stats_date_to']) . "', '" . pSQL($value['stats_compare_from']) . "', '" . pSQL($value['stats_compare_to']) . "', '" . pSQL($value['stats_compare_option']) . "', '" . pSQL($value['preselect_date_range']) . "', '" . pSQL($value['bo_color']) . "', '" . pSQL($value['bo_theme']) . "', '" . pSQL($value['bo_css']) . "', '" . pSQL($value['default_tab']) . "', '" . pSQL($value['bo_width']) . "', '" . pSQL($value['bo_menu']) . "', '" . pSQL($value['active']) . "', '" . pSQL($value['optin']) . "', '" . pSQL($value['id_last_order']) . "', '" . pSQL($value['id_last_customer_message']) . "', '" . pSQL($value['id_last_customer']) . "') 
                ON DUPLICATE KEY UPDATE 
                id_profile = VALUES(id_profile), 
                id_lang = VALUES(id_lang), 
                lastname = VALUES(lastname), 
                firstname = VALUES(firstname), 
                email = VALUES(email), 
                passwd = VALUES(passwd), 
                last_passwd_gen = VALUES(last_passwd_gen), 
                stats_date_from = VALUES(stats_date_from), 
                stats_date_to = VALUES(stats_date_to), 
                stats_compare_from = VALUES(stats_compare_from), 
                stats_compare_to = VALUES(stats_compare_to), 
                stats_compare_option = VALUES(stats_compare_option), 
                preselect_date_range = VALUES(preselect_date_range), 
                bo_color = VALUES(bo_color), 
                bo_theme = VALUES(bo_theme), 
                bo_css = VALUES(bo_css), 
                default_tab = VALUES(default_tab), 
                bo_width = VALUES(bo_width), 
                bo_menu = VALUES(bo_menu), 
                active = VALUES(active), 
                optin = VALUES(optin), 
                id_last_order = VALUES(id_last_order), 
                id_last_customer_message = VALUES(id_last_customer_message), 
                id_last_customer = VALUES(id_last_customer)";

            Db::getInstance()->execute($sql);
        }
    }

    private function populateEmployeeShop($conn, $prefix){
        

        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "employee_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."employee_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."employee_shop (`id_employee`, `id_shop`) VALUES ('" . pSQL($value['id_employee']) . "', '" . pSQL($value['id_shop']) . "')";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }


    public function populateAllEmployees($conn, $prefix) {
        $this->populateEmployee($conn, $prefix);
        $this->populateEmployeeShop($conn, $prefix);
    }
}