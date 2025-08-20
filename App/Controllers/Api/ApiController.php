<?php
class ApiController {
    public function data() {
        $db = Database::get();
        $data = $db->select('sample_table', '*');
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
    