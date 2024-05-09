<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Migrator_indexeo extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'migrator_indexeo';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Hector Valls';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Modulo de Migracion');
        $this->description = $this->l('Modulo para migrar prestashops con versiones antiguas, a las mas recientes');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitMigrator_indexeoModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        //$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMigrator_indexeoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configurar conexión'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_HOST',
                        'label' => $this->l('Host antiguo'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_USERNAME',
                        'label' => $this->l('Usuario antiguo'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'OLD_DB_PASSWORD',
                        'label' => $this->l('Contraseña antigua'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB',
                        'label' => $this->l('Base de datos antigua'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_PREFIX',
                        'label' => $this->l('Prefijo de las tablas'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_COOKIE_KEY',
                        'label' => $this->l('Cookie key antigua'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Guardar'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'OLD_DB_HOST' => Configuration::get('OLD_DB_HOST', 'localhost'),
            'OLD_DB_USERNAME' => Configuration::get('OLD_DB_USERNAME', 'admin'),
            'OLD_DB_PASSWORD' => Configuration::get('OLD_DB_PASSWORD', null),
            'OLD_DB' => Configuration::get('OLD_DB', null),
            'OLD_DB_PREFIX' => Configuration::get('OLD_DB_PREFIX', null),
            'OLD_COOKIE_KEY' => Configuration::get('OLD_COOKIE_KEY', ''),
        );
    }


    protected function postProcess()
    {
        $this->form_values = $this->getConfigFormValues();

        foreach (array_keys($this->form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->cambiarCookieKey();
        if($this->testConnection()) {
            $host = $this->form_values['OLD_DB_HOST'];
            $username = $this->form_values['OLD_DB_USERNAME'];
            $password = $this->form_values['OLD_DB_PASSWORD'];
            $dbname = $this->form_values['OLD_DB'];
            $prefix = $this->form_values['OLD_DB_PREFIX'];

            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //$this->populateEmployee($conn,$prefix);
                //$this->populateEmployeeShop($conn,$prefix);


                $this->populateCustomer($conn,$prefix);
                $this->populateCustomerGroup($conn,$prefix);
                $this->populateCustomerMessage($conn,$prefix);
                $this->populateCustomerMessageSyncImap($conn,$prefix);
                $this->populateCustomerThread($conn,$prefix);
                $this->populateMailAlertCustomerOOS($conn,$prefix);


                $this->populateCategory($conn,$prefix);
                $this->populateCategoryGroup($conn,$prefix);
                $this->populateCategoryLang($conn,$prefix);
                $this->populateCategoryShop($conn,$prefix);
                $this->populateCMSCategory($conn,$prefix);
                $this->populateCMSCategoryLang($conn,$prefix);
                $this->populateCMSCategoryShop($conn,$prefix);
                $this->popullateLayeredCategory($conn,$prefix);
                $this->populateCategoryProduct($conn,$prefix);


                $this->populateProduct($conn,$prefix);
                $this->populateProductLang($conn,$prefix);
                $this->populateProductShop($conn,$prefix);
                $this->populateFeatureProduct($conn,$prefix);
                $this->populateLayeredProductAttribute($conn,$prefix);
                $this->populateProductAttachment($conn,$prefix);
                $this->populateProductAttribute($conn,$prefix);
                $this->populateProductAttributeCombination($conn,$prefix);
                $this->populateProductAttributeImage($conn,$prefix);
                $this->populateProductAttributeShop($conn,$prefix);
                $this->populateProductCarrier($conn,$prefix);
                $this->populateProductCountryTax($conn,$prefix);
                $this->populateProductDownload($conn,$prefix);
                $this->populateProductSale($conn,$prefix);
                $this->populateProductSupplier($conn,$prefix);
                $this->populateProductTag($conn,$prefix);
                $this->populateWareHouseProductLocation($conn,$prefix);


                $this->populateGroup($conn,$prefix);
                $this->populateAttributeGroup($conn,$prefix);
                $this->populateAttributeGroupLang($conn,$prefix);
                $this->populateAttributeGroupShop($conn,$prefix);
                $this->populateCarrierGroup($conn,$prefix);
                $this->populateGroupLang($conn,$prefix);
                $this->populateGroupReduction($conn,$prefix);
                $this->populateGroupShop($conn,$prefix);
                $this->populateLayeredIndexableAttributeGroup($conn,$prefix);
                $this->populateLayeredIndexableAttributeGroupLangValue($conn,$prefix);




            }
            catch(PDOException $exception) {
                echo "Error: " . $exception->getMessage();
            }
            // Cerrar conexion
            $conn = null;
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }


    private function cambiarCookieKey(){
        $parametersPath = _PS_ROOT_DIR_.'/app/config/parameters.php';
        $content = file_get_contents($parametersPath);
        $newCookieKey = $this->form_values['OLD_COOKIE_KEY'];

        // Aquí estarías reemplazando la línea que contiene el cookie_key actual
        $newContent = preg_replace("/'cookie_key' => '[^']+'/", "'cookie_key' => '$newCookieKey'", $content);

        // Escribir los cambios de vuelta al archivo
        file_put_contents($parametersPath, $newContent);
    }
    public function populateEmployee($conn, $prefix){
        
        $prefix = $this->form_values['OLD_DB_PREFIX'];
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
    
    public function populateEmployeeShop($conn, $prefix){
        
    
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



    public function populateCustomer($conn, $prefix){
        
        $prefix = $this->form_values['OLD_DB_PREFIX'];
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
    
    public function populateCustomerGroup($conn, $prefix){
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

    public function populateCustomerMessage($conn, $prefix){
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

    public function populateCustomerMessageSyncImap($conn, $prefix){
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

    public function populateCustomerThread($conn, $prefix){
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

    
    public function populateMailAlertCustomerOOS($conn, $prefix){
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
    
    public function populateCategory($conn, $prefix){
        try {
                $query = $conn->prepare("SELECT * FROM " .$prefix. "category WHERE 1");
                $query->execute();

                Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."category WHERE 1;");
                foreach($query->fetchAll() as $key=>$value) {
                    if($value['id_category'] != 1 || $value['id_category'] != 2){

                    
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "category (
                        `id_category`,
                        `id_parent`,
                        `id_shop_default`,
                        `level_depth`,
                        `nleft`,
                        `nright`,
                        `active`,
                        `date_add`,
                        `date_upd`,
                        `position`,
                        `is_root_category`
                    ) 
                    VALUES (
                        '" . pSQL($value['id_category']) . "',
                        '" . pSQL($value['id_parent']) . "',
                        '" . pSQL($value['id_shop_default']) . "',
                        '" . pSQL($value['level_depth']) . "',
                        '" . pSQL($value['nleft']) . "',
                        '" . pSQL($value['nright']) . "',
                        '" . pSQL($value['active']) . "',
                        '" . pSQL($value['date_add']) . "',
                        '" . pSQL($value['date_upd']) . "',
                        '" . pSQL($value['position']) . "',
                        '" . pSQL($value['is_root_category']) . "'
                    )
                    ON DUPLICATE KEY UPDATE 
                    id_parent = VALUES(id_parent), 
                    id_shop_default = VALUES(id_shop_default), 
                    level_depth = VALUES(level_depth), 
                    nleft = VALUES(nleft), 
                    nright = VALUES(nright), 
                    active = VALUES(active), 
                    date_add = VALUES(date_add), 
                    date_upd = VALUES(date_upd), 
                    position = VALUES(position), 
                    is_root_category = VALUES(is_root_category);";
                    Db::getInstance()->execute($sql);
                }
            }
        }

        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateCategoryGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_group WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_group (`id_category`, `id_group`) VALUES ('" . pSQL($value['id_category']) . "', '" . pSQL($value['id_group']) . "')
                ON DUPLICATE KEY UPDATE 
                id_group = VALUES(id_group);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }
    
    public function populateCategoryLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_lang (
                    `id_category`, 
                    `id_shop`,
                    `id_lang`,
                    `name`,
                    `description`,
                    `link_rewrite`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['description']) . "',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_description']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_shop = VALUES(id_shop), 
                    id_lang = VALUES(id_lang), 
                    name = VALUES(name), 
                    description = VALUES(description), 
                    link_rewrite = VALUES(link_rewrite), 
                    meta_title = VALUES(meta_title), 
                    meta_keywords = VALUES(meta_keywords), 
                    meta_description = VALUES(meta_description);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateCategoryProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."category_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_product (
                    `id_category`, 
                    `id_product`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_product']) . "',
                        '" . pSQL($value['position']) . "'
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

    public function populateCategoryShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_shop WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_shop (
                    `id_category`, 
                    `id_shop`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['position']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                        id_category = VALUES(id_category), 
                        id_shop = VALUES(id_shop), 
                        position = VALUES(position);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateCMSCategory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category (
                    `id_cms_category`, 
                    `id_parent`,
                    `level_depth`,
                    `active`,
                    `date_add`,
                    `date_upd`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_parent']) . "',
                        '" . pSQL($value['level_depth']) . "',
                        '" . pSQL($value['active']) . "',
                        '" . pSQL($value['date_add']) . "',
                        '" . pSQL($value['date_upd']) . "',
                        '" . pSQL($value['position']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_cms_category = VALUES(id_cms_category), 
                    id_parent = VALUES(id_parent), 
                    level_depth = VALUES(level_depth), 
                    active = VALUES(active), 
                    date_add = VALUES(date_add), 
                    date_upd = VALUES(date_upd), 
                    position = VALUES(position);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateCMSCategoryLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category_lang (
                    `id_cms_category`, 
                    `id_lang`,
                    `id_shop`,
                    `name`,
                    `description`,
                    `link_rewrite`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['description']) . "',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_description']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_cms_category = VALUES(id_cms_category), 
                    id_lang = VALUES(id_lang), 
                    id_shop = VALUES(id_shop), 
                    name = VALUES(name), 
                    description = VALUES(description), 
                    link_rewrite = VALUES(link_rewrite), 
                    meta_title = VALUES(meta_title), 
                    meta_keywords = VALUES(meta_keywords), 
                    meta_description = VALUES(meta_description);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateCMSCategoryShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category_shop WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category_shop (
                    `id_cms_category`, 
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_shop']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                        id_cms_category = VALUES(id_cms_category), 
                        id_shop = VALUES(id_shop);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }


    public function popullateLayeredCategory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_category WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."layered_category (
                    `id_layered_category`, 
                    `id_shop`,
                    `id_category`,
                    `id_value`,
                    `type`,
                    `position`,
                    `filter_type`,
                    `filter_show_limit`,
                    `controller`
                    ) VALUES (
                        '" . pSQL($value['id_layered_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_category']) . "',
                        '" . pSQL($value['id_value']) . "',
                        '" . pSQL($value['type']) . "',
                        '" . pSQL($value['position']) . "',
                        '" . pSQL($value['filter_type']) . "',
                        '" . pSQL($value['filter_show_limit']) . "', 
                        'category'
                    )
                    ON DUPLICATE KEY UPDATE 
                    id_layered_category = VALUES(id_layered_category), 
                    id_shop = VALUES(id_shop), 
                    id_value = VALUES(id_value), 
                    type = VALUES(type), 
                    position = VALUES(position), 
                    filter_type = VALUES(filter_type), 
                    filter_show_limit = VALUES(filter_show_limit);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

   

    public function populateProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product (
                    `id_product`, 
                    `id_supplier`,
                    `id_manufacturer`,
                    `id_category_default`,
                    `id_shop_default`,
                    `id_tax_rules_group`,
                    `on_sale`,
                    `online_only`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `mpn`,
                    `ecotax`,
                    `quantity`,
                    `minimal_quantity`,
                    `low_stock_threshold`,
                    `low_stock_alert`,
                    `price`,
                    `wholesale_price`,
                    `unity`,
                    `unit_price`,
                    `unit_price_ratio`,
                    `additional_shipping_cost`,
                    `reference`,
                    `supplier_reference`,
                    `location`,
                    `width`,
                    `height`,
                    `depth`,
                    `weight`,
                    `out_of_stock`,
                    `additional_delivery_times`,
                    `quantity_discount`,
                    `customizable`,
                    `uploadable_files`,
                    `text_fields`,
                    `active`,
                    `redirect_type`,
                    `id_type_redirected`,
                    `available_for_order`,
                    `available_date`,
                    `show_condition`,
                    `condition`,
                    `show_price`,
                    `indexed`,
                    `visibility`,
                    `cache_is_pack`,
                    `cache_has_attachments`,
                    `is_virtual`,
                    `cache_default_attribute`,
                    `date_add`,
                    `date_upd`,
                    `advanced_stock_management`,
                    `pack_stock_type`,
                    `state`,
                    `product_type`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['id_category_default']) . "',
                    '" . pSQL($value['id_shop_default']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "',
                    '" . pSQL($value['on_sale']) . "',
                    '" . pSQL($value['online_only']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '',
                    '" . pSQL($value['upc']) . "',
                    '',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['quantity']) . "',
                    '" . pSQL($value['minimal_quantity']) . "',
                    0,
                    0,
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['unity']) . "',
                    0,
                    '" . pSQL($value['unit_price_ratio']) . "',
                    '" . pSQL($value['additional_shipping_cost']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['location']) . "',
                    '" . pSQL($value['width']) . "',
                    '" . pSQL($value['height']) . "',
                    '" . pSQL($value['depth']) . "',
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['out_of_stock']) . "',
                    0,
                    '" . pSQL($value['quantity_discount']) . "',
                    '" . pSQL($value['customizable']) . "',
                    '" . pSQL($value['uploadable_files']) . "',
                    '" . pSQL($value['text_fields']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['redirect_type']) . "',
                    '" . pSQL($value['id_product_redirected']) . "',
                    '" . pSQL($value['available_for_order']) . "',
                    '" . pSQL($value['available_date']) . "',
                    1,
                    '" . pSQL($value['condition']) . "',
                    '" . pSQL($value['show_price']) . "',
                    '" . pSQL($value['indexed']) . "',
                    '" . pSQL($value['visibility']) . "',
                    '" . pSQL($value['cache_is_pack']) . "',
                    '" . pSQL($value['cache_has_attachments']) . "',
                    '" . pSQL($value['is_virtual']) . "',
                    '" . pSQL($value['cache_default_attribute']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['advanced_stock_management']) . "',
                    0,
                    1,
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


    public function populateProductLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_lang (
                    `id_product`, 
                    `id_shop`,
                    `id_lang`,
                    `description`,
                    `description_short`,
                    `link_rewrite`,
                    `meta_description`,
                    `meta_keywords`,
                    `meta_title`,
                    `name`,
                    `available_now`,
                    `available_later`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['description']) . "',
                        '" . pSQL($value['description_short']) . "',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_description']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['available_now']) . "',
                        '" . pSQL($value['available_later']) . "'
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


    public function populateProductShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_shop (
                    `id_product`, 
                    `id_shop`,
                    `id_category_default`,
                    `id_tax_rules_group`,
                    `on_sale`,
                    `online_only`,
                    `ecotax`,
                    `minimal_quantity`,
                    `price`,
                    `wholesale_price`,
                    `unity`,
                    `unit_price_ratio`,
                    `additional_shipping_cost`,
                    `customizable`,
                    `uploadable_files`,
                    `text_fields`,
                    `active`,
                    `redirect_type`,
                    `id_type_redirected`,
                    `available_for_order`,
                    `available_date`,
                    `condition`,
                    `show_price`,
                    `indexed`,
                    `visibility`,
                    `cache_default_attribute`,
                    `advanced_stock_management`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_category_default']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "',
                    '" . pSQL($value['on_sale']) . "',
                    '" . pSQL($value['online_only']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['unity']) . "',
                    '" . pSQL($value['unit_price_ratio']) . "',
                    '" . pSQL($value['additional_shipping_cost']) . "',
                    '" . pSQL($value['customizable']) . "',
                    '" . pSQL($value['uploadable_files']) . "',
                    '" . pSQL($value['text_fields']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['redirect_type']) . "',
                    '" . pSQL($value['id_product_redirected']) . "',
                    '" . pSQL($value['available_for_order']) . "',
                    '" . pSQL($value['available_date']) . "',
                    '" . pSQL($value['condition']) . "',
                    '" . pSQL($value['show_price']) . "',
                    '" . pSQL($value['indexed']) . "',
                    '" . pSQL($value['visibility']) . "',
                    '" . pSQL($value['cache_default_attribute']) . "',
                    '" . pSQL($value['advanced_stock_management']) . "',
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

    public function populateFeatureProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."feature_product (
                    `id_product`, 
                    `id_feature`,
                    `id_feature_value`
                    
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_feature']) . "',
                        '" . pSQL($value['id_feature_value']) . "'
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

    public function populateLayeredProductAttribute($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_product_attribute WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_product_attribute WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."layered_product_attribute (
                    `id_product`, 
                    `id_attribute`,
                    `id_attribute_group`,
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_attribute']) . "',
                        '" . pSQL($value['id_attribute_group']) . "',
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

    public function populateProductAttachment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attachment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attachment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attachment (
                    `id_product`, 
                    `id_attachment`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_attachment']) . "'
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
    
    public function populateProductAttribute($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute (
                    `id_product_attribute`,
                    `id_product`, 
                    `reference`,
                    `supplier_reference`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `mpn`,
                    `wholesale_price`,
                    `price`,
                    `ecotax`,
                    `low_stock_threshold`,
                    `low_stock_alert`,
                    `weight`,
                    `unit_price_impact`,
                    `default_on`,
                    `minimal_quantity`,
                    `available_date`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '',
                    '" . pSQL($value['upc']) . "',
                    '',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    0,
                    0,
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['unit_price_impact']) . "',
                    NULL,
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['available_date']) . "'

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

    public function populateProductAttributeCombination($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_combination WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_combination WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attribute_combination (
                    `id_attribute`, 
                    `id_product_attribute`
                    ) VALUES (
                        '" . pSQL($value['id_attribute']) . "', 
                        '" . pSQL($value['id_product_attribute']) . "'
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
    public function populateProductAttributeImage($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_image WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_image WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attribute_image (
                    `id_image`, 
                    `id_product_attribute`
                    ) VALUES (
                        '" . pSQL($value['id_image']) . "', 
                        '" . pSQL($value['id_product_attribute']) . "'
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

    public function populateProductAttributeShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop (
                    `id_product`, 
                    `id_product_attribute`, 
                    `id_shop`,
                    `wholesale_price`,
                    `price`,
                    `ecotax`,
                    `weight`,
                    `unit_price_impact`,
                    `default_on`,
                    `minimal_quantity`,
                    `available_date`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['unit_price_impact']) . "',
                    NULL,
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['available_date']) . "'
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

    public function populateProductCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_carrier (
                    `id_product`, 
                    `id_carrier_reference`
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_carrier_reference']) . "'
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
    
    public function populateProductCountryTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_country_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_country_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_country_tax (
                    `id_product`, 
                    `id_country`, 
                    `id_tax`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_country']) . "', 
                    '" . pSQL($value['id_tax']) . "'
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

    public function populateProductDownload($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_download WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_download WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_download (
                    `id_product`, 
                    `id_product_download`, 
                    `display_name`,
                    `date_add`,
                    `date_expiration`,
                    `nb_days_accessible`,
                    `nb_downloadable`,
                    `active`,
                    `is_shareable`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_download']) . "', 
                    '" . pSQL($value['display_name']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_expiration']) . "',
                    '" . pSQL($value['nb_days_accessible']) . "',
                    '" . pSQL($value['nb_downloadable']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['is_shareable']) . "'
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

    public function populateProductSale($conn, $prefix){
            try {
                $query = $conn->prepare("SELECT * FROM " .$prefix. "product_sale WHERE 1");
                $query->execute();

                Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_sale WHERE 1;");
                foreach($query->fetchAll() as $key=>$value) {
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "product_sale (
                        `id_product`, 
                        `quantity`,
                        `sale_nbr`,
                        `date_upd`
                    ) 
                    VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['quantity']) . "',
                        '" . pSQL($value['sale_nbr']) . "',
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

    public function populateProductSupplier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_supplier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_supplier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_supplier (
                    `id_product`, 
                    `id_product_supplier`, 
                    `id_product_attribute`,
                    `id_supplier`,
                    `product_supplier_reference`,
                    `product_supplier_price_te`,
                    `id_currency`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_supplier']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['product_supplier_reference']) . "',
                    '" . pSQL($value['product_supplier_price_te']) . "',
                    '" . pSQL($value['id_currency']) . "'
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

    public function populateProductTag($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_tag WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_tag WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_tag (
                    `id_product`, 
                    `id_tag`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_tag']) . "',
                    1
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

    public function populateWareHouseProductLocation($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse_product_location WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse_product_location WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse_product_location (
                    `id_product`, 
                    `id_warehouse_product_location`, 
                    `id_product_attribute`,
                    `id_warehouse`,
                    `location`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_warehouse_product_location']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['location']) . "'
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

    public function populateAttributeGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group (
                    `id_attribute_group`, 
                    `is_color_group`, 
                    `group_type`,
                    `position`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['is_color_group']) . "', 
                    '" . pSQL($value['group_type']) . "',
                    '" . pSQL($value['position']) . "'
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

    public function populateAttributeGroupLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_lang (
                    `id_attribute_group`, 
                    `id_lang`, 
                    `name`,
                    `public_name`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['public_name']) . "'
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

    public function populateAttributeGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_shop (
                    `id_attribute_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
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

    public function populateCarrierGroup($conn, $prefix){
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
    
    public function populateGroup($conn, $prefix){
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

    public function populateGroupLang($conn, $prefix){
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

    public function populateGroupReduction($conn, $prefix){
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

    public function populateGroupShop($conn, $prefix){
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

    public function populateLayeredIndexableAttributeGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_attribute_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_indexable_attribute_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_attribute_group (
                    `id_attribute_group`, 
                    `indexable`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['indexable']) . "'
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

    public function populateLayeredIndexableAttributeGroupLangValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_attribute_group_lang_value WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_indexable_attribute_group_lang_value WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_attribute_group_lang_value (
                    `id_attribute_group`, 
                    `id_lang`,
                    `url_name`,
                    `meta_title`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['url_name']) . "',
                    '" . pSQL($value['meta_title']) . "'
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




    public function testConnection(){
        $host = $this->form_values['OLD_DB_HOST'];
        $username = $this->form_values['OLD_DB_USERNAME'];
        $password = $this->form_values['OLD_DB_PASSWORD'];
        $dbname = $this->form_values['OLD_DB'];
    
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexión exitosa"; 
            $conn = null; 
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}
