<?php
namespace de\interaapps\uppm\helper;

trait JSONModel {
    public function json(){
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    public static function fromJson($json){
        $instance = new static();
        $decoded = json_decode($json);

        foreach (get_class_vars(get_class($instance)) as $field => $ignored) {
            if (isset($decoded->{$field})) {
                $instance->{$field} = $decoded->{$field};
            }
        }

        return $instance;
    }
}