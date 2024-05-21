<?php
class AttachmentPopulator {
    public function populateAttachment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attachment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attachment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attachment (
                    `id_attachment`, 
                    `file`,
                    `file_name`,
                    `file_size`,
                    `mime`
                ) 
                VALUES (
                    '" . pSQL($value['id_attachment']) . "', 
                    '" . pSQL($value['file']) . "',
                    '" . pSQL($value['file_name']) . "',
                    '" . pSQL($value['file_size']) . "',
                    '" . pSQL($value['mime']) . "'
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

    public function populateAttachmentLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attachment_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attachment_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attachment_lang (
                    `id_attachment`, 
                    `name`,
                    `description`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_attachment']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['description']) . "',
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
   
    

    public function populateAllAttachments($conn, $prefix) {
        $this->populateAttachment($conn,$prefix);
        $this->populateAttachmentLang($conn,$prefix);
        
    }
}