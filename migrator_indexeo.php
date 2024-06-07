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
    require 'functions/addressPopulator.php';
    require 'functions/attachmentPopulator.php';
    require 'functions/attributePopulator.php';
    require 'functions/carrierPopulator.php';
    require 'functions/cartPopulator.php';
    require 'functions/categoryPopulator.php';
    require 'functions/CMSPopulator.php';
    require 'functions/customerPopulator.php';
    require 'functions/deliveryPopulator.php';
    require 'functions/employeePopulator.php';
    require 'functions/groupPopulator.php';
    require 'functions/imagePopulator.php';
    require 'functions/langPopulator.php';
    require 'functions/manufacturerPopulator.php';
    require 'functions/orderPopulator.php';
    require 'functions/productPopulator.php';
    require 'functions/stockPopulator.php';
    require 'functions/supplierPopulator.php';
    require 'functions/supplyPopulator.php';
    require 'functions/taxPopulator.php';
    require 'functions/warehousePopulator.php';
    require 'functions/featurePopulator.php';


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
            return [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Configurar conexión'),
                        'icon' => 'icon-cogs',
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'OLD_DB_HOST',
                            'label' => $this->l('Host antiguo:'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'OLD_DB_USERNAME',
                            'label' => $this->l('Usuario antiguo:'),
                        ],
                        [
                            'type' => 'password',
                            'name' => 'OLD_DB_PASSWORD',
                            'label' => $this->l('Contraseña antigua:'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'OLD_DB',
                            'label' => $this->l('Base de datos antigua:'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'OLD_DB_PREFIX',
                            'label' => $this->l('Prefijo antiguo de las tablas:'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'OLD_COOKIE_KEY',
                            'label' => $this->l('Cookie key antigua:'),
                            'hint' => $this->l('Solo necesario para Empleados y Clientes. Puedes encontrar esta clave en el archivo "settings.inc.php" ubicado en el directorio "config" de tu antigua instalación de PrestaShop.'),
                        ],
                        // Checkboxes múltiples aquí
                        [
                            'type' => 'checkbox',
                            'name' => 'MY_CHECKBOXES',
                            'values' => [
                                'query' => [
                                    ['id' => 'address', 'name' => 'Direecciones', 'val' => '1'],
                                    ['id' => 'attachment', 'name' => 'Adjuntos', 'val' => '2'],
                                    ['id' => 'attribute', 'name' => 'Atributos', 'val' => '3'],
                                    ['id' => 'carrier', 'name' => 'Transportistas', 'val' => '4'],
                                    ['id' => 'cart', 'name' => 'Carritos', 'val' => '5'],
                                    ['id' => 'category', 'name' => 'Categorias', 'val' => '6'],
                                    ['id' => 'cms', 'name' => 'CMS', 'val' => '7'],
                                    ['id' => 'delivery', 'name' => 'Envios', 'val' => '9'],
                                    ['id' => 'employee', 'name' => 'Empleados Y Clientes (necesita cookie key)', 'val' => '10'],
                                    ['id' => 'group', 'name' => 'Grupos', 'val' => '11'],
                                    ['id' => 'image', 'name' => 'Imagenes', 'val' => '12'],
                                    ['id' => 'lang', 'name' => 'Idiomas', 'val' => '13'],
                                    ['id' => 'manufacturer', 'name' => 'Fabricantes', 'val' => '14'],
                                    ['id' => 'order', 'name' => 'Pedidos', 'val' => '15'],
                                    ['id' => 'product', 'name' => 'Productos', 'val' => '16', 'hint' => $this->l('También se importara el stock')],
                                    ['id' => 'stock', 'name' => 'Stock', 'val' => '17'],
                                    ['id' => 'supplier', 'name' => 'Proveedores', 'val' => '18'],
                                    ['id' => 'supply', 'name' => 'Suministros', 'val' => '19'],
                                    ['id' => 'tax', 'name' => 'Impuestos', 'val' => '20'],
                                    ['id' => 'warehouse', 'name' => 'Almacenes', 'val' => '21'],
                                    ['id' => 'feature', 'name' => 'Caracteristicas', 'val' => '22'],
                                ],
                                'id' => 'id',
                                'name' => 'name'
                            ],
                        ],
                    ],
                    'submit' => [
                        'title' => $this->l('Migrar'),
                    ],
                ],
            ];
        }
        
        protected function getConfigFormValues()
        {
            $values = [
                'OLD_DB_HOST' => Configuration::get('OLD_DB_HOST'),
                'OLD_DB_USERNAME' => Configuration::get('OLD_DB_USERNAME'),
                'OLD_DB_PASSWORD' => Configuration::get('OLD_DB_PASSWORD'),
                'OLD_DB' => Configuration::get('OLD_DB'),
                'OLD_DB_PREFIX' => Configuration::get('OLD_DB_PREFIX'),
                'OLD_COOKIE_KEY' => Configuration::get('OLD_COOKIE_KEY'),
            ];

            // Valores de checkboxes
            $checkboxes = array('address', 'attachment', 'attribute', 'carrier', 'cart', 'category', 'cms', 'delivery', 'employee', 'group', 'image', 'lang', 'manufacturer', 'order', 'product', 'stock', 'supplier', 'supply', 'tax', 'warehouse', 'feature');
            foreach ($checkboxes as $checkbox) {
                $values['MY_CHECKBOXES_'.$checkbox] = Configuration::get('MY_CHECKBOXES_'.$checkbox);
            }
            return $values;
        }

        private function cambiarCookieKey() {
            $archivo = _PS_ROOT_DIR_.'/app/config/parameters.php';
            $nueva_linea = "    'cookie_key' => '". $this->form_values['OLD_COOKIE_KEY'] ."'," . PHP_EOL;
        
            $contenido = file($archivo);
            $linea_modificada = false;
        
            foreach ($contenido as $indice => $linea) {
                if (strpos($linea, 'cookie_key') !== false) {
                    $contenido[$indice] = $nueva_linea;
                    $linea_modificada = true;
                    break;
                }
            }

            if ($linea_modificada) {
                file_put_contents($archivo, implode('', $contenido));
                echo "La línea que contiene 'cookie_key' ha sido modificada.";
            } else {
                echo "No se encontró la línea que contiene 'cookie_key'.";
            }

        }

        protected function postProcess()
        {
            $this->form_values = $this->getConfigFormValues();

            foreach (array_keys($this->form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }

            $this->form_values = $this->getConfigFormValues();

            
            if($this->testConnection()) {
                $host = $this->form_values['OLD_DB_HOST'];
                $username = $this->form_values['OLD_DB_USERNAME'];
                $password = $this->form_values['OLD_DB_PASSWORD'];
                $dbname = $this->form_values['OLD_DB'];
                $prefix = $this->form_values['OLD_DB_PREFIX'];

                try {

                    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                    //  EMPLOYEES & CUSTOMER----------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_employee']){
                        $this->cambiarCookieKey();
                        $employeePopulator = new EmployeePopulator();
                        $employeePopulator->populateAllEmployees($conn, $prefix);
                        $customerPopulator = new CustomerPopulator();
                        $customerPopulator->populateAllCustomers($conn, $prefix);
                    }
                    


                    //  CATEGORIES-----------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_category']){
                        $categoryPopulator = new CategoryPopulator();
                        $categoryPopulator->populateAllCategories($conn, $prefix);
                    }


                    // PRODUCTS--------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_product']){
                        $productPopulator = new ProductPopulator();
                        $productPopulator->populateAllProducts($conn, $prefix);
                        $stockPopulator = new StockPopulator();
                        $stockPopulator->populateAllStocks($conn, $prefix);
                    }

                    //  GROUP---------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_group']){
                        $groupsPopulator = new GroupPopulator();
                        $groupsPopulator->populateAllGroups($conn, $prefix);
                    }


                    //  ATTRIBUTE---------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_attribute']){
                        $attributePopulator = new AttributePopulator();
                        $attributePopulator->populateAllAttributes($conn, $prefix);
                    }


                    //  CARRIER---------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_carrier']){
                        $carrierPopulator = new CarrierPopulator();
                        $carrierPopulator->populateAllCarriers($conn, $prefix);
                    }


                    // TAX----------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_tax']){
                        $taxPopulator = new TaxPopulator();
                        $taxPopulator->populateAllTaxes($conn, $prefix);
                    }


                    //  MANUFACTURER---------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_manufacturer']){
                        $manufacturerPopulator = new ManufacturerPopulator();
                        $manufacturerPopulator->populateAllManufacturers($conn, $prefix);
                    }


                    //  SUPPLIER-------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_supplier']){
                        $supplierPopulator = new SupplierPopulator();
                        $supplierPopulator->populateAllSuppliers($conn, $prefix);
                    }


                    //  SUPPLY--------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_supply']){
                        $supplyPopulator = new SupplyPopulator();
                        $supplyPopulator->populateAllSupplies($conn, $prefix);
                    }



                    //  ATTACHMENT------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_attachment']){
                        $attachmentPopulator = new AttachmentPopulator();
                        $attachmentPopulator->populateAllAttachments($conn, $prefix);
                    }


                    // ADDRESS-----------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_address']){
                        $addressPopulator = new AddressPopulator();
                        $addressPopulator->populateAllAddresses($conn, $prefix);
                    }


                    //  DELIVERY---------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_delivery']){
                        $deliveryPopulator = new DeliveryPopulator();
                        $deliveryPopulator->populateAllDeliveries($conn, $prefix);
                    }


                    // WAREHOUSE---------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_warehouse']){
                        $warehousePopulator = new WarehousePopulator();
                        $warehousePopulator->populateAllWarehouses($conn, $prefix);
                    }


                    // CMS---------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_cms']){
                        $CMSPopulator = new CMSPopulator();
                        $CMSPopulator->populateAllCMS($conn, $prefix);
                    }


                    //  IMAGE------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_image']){
                        $imagePopulator = new ImagePopulator();
                        $imagePopulator->populateAllImages($conn, $prefix);
                    }

                    // ORDER--------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_order']){
                        $orderPopulator = new OrderPopulator();
                        $orderPopulator->populateAllOrders($conn, $prefix);
                    }


                    //  STOCK-------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_stock']){
                        $stockPopulator = new StockPopulator();
                        $stockPopulator->populateAllStocks($conn, $prefix);
                    }
                    

                    //  LANG-------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_lang']){
                        $langPopulator = new LangPopulator();
                        $langPopulator->populateAllLangs($conn, $prefix);
                    }

                    //  Feature-------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_feature']){
                        $featurePopulator = new FeaturePopulator();
                        $featurePopulator->populateAllFeatures($conn, $prefix);
                    }

                    
                    //  CART--------------------------------------------------------------------------
                    if($this->form_values['MY_CHECKBOXES_cart']){
                        $cartPopulator = new CartPopulator();
                        $cartPopulator->populateAllCarts($conn, $prefix);
                    }
                    

                }
                catch(PDOException $exception) {
                    echo '<div style="background-color: red;">'.$exception->getMessage().'</div>';
                    return false;
                }
                // Cerrar conexion
                $conn = null;
                echo '<div style="background-color: green;">Migracion exitosa</div>';

            }

            return true;
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
        

        private function testConnection(){
            $host = $this->form_values['OLD_DB_HOST'];
            $username = $this->form_values['OLD_DB_USERNAME'];
            $password = $this->form_values['OLD_DB_PASSWORD'];
            $dbname = $this->form_values['OLD_DB'];
        
            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                if($conn){
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $conn = null; 
                }
            } catch (PDOException $e) {
                if ($password === ''){
                    echo '<div style="background-color: red;">'.$e->getMessage().'</div>';
                    return false;
                }
                
            }
            return true;
        }

    }
