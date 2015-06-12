<?php

namespace Smartinsalmeida;

/**
 * Description of FormBuilder
 *
 * @author Sylvain
 */
use Micro\Validation;

class FormBuilder
{

    private $formfields = array();

    public function __construct($oData)
    {
        foreach ($oData->children as $key => $val) {
            // Cet élément de formulaire a t il des enfants ?
            if (count($oData->children->$key->children) == 0) {
                // Non
                $this->addFormField($key, $oData->children->$key);
            } else {
                // Oui
                self::__construct($oData->children->$key);
            }
        }
    }

    public function addFormField($field_name, $field_details)
    {
        $this->formfields[$field_name] = $field_details;
    }

    public function build()
    {
        $form = new Form(new Validation(null));
        foreach ($this->formfields as $form_field) {
            if ("form" === $form_field->vars->block_prefixes[0]) {
                $titre = (!empty($form_field->vars->label) ? $form_field->vars->label : "");
                if ($form_field->vars->block_prefixes[1] == "text") { // Champs texte
                    if ($form_field->vars->block_prefixes[2] == "textarea") { // textarea
                        $form->{$form_field->vars->name}->textarea()->label($titre)->value($form_field->vars->value)->attributes((array) $form_field->vars->attr);
                    } else if ($form_field->vars->block_prefixes[2] == "captcha") {						
                    } else if ($form_field->vars->block_prefixes[2] == "password") {
                         $form->{$form_field->vars->name}->input($form_field->vars->block_prefixes[2])->label($titre)->value($form_field->vars->value)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);
                    } else {
                        if (!isset($form_field->vars->attr)) {
                            $form_field->vars->attr = array();
                        }
                        $form->{$form_field->vars->name}->input($form_field->vars->block_prefixes[1])->label($titre)->value($form_field->vars->value)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);
                    }
				}elseif($form_field->vars->block_prefixes[1] == "number") { // Nombre
					$form->{$form_field->vars->name}->input("text")->label($titre)->value($form_field->vars->value)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);
                } elseif ($form_field->vars->block_prefixes[1] == "time") { // Heure
                    $heure = date('H:i');
                    $form->{$form_field->vars->name}->input('text')->label($titre)->value($heure)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);  
                } elseif ($form_field->vars->block_prefixes[1] == "date") { // Date
                    //$date = date('d/m/Y');
                    $form->{$form_field->vars->name}->input('text')->label($titre)->value($form_field->vars->value)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);  
                
                } elseif ($form_field->vars->block_prefixes[1] == "file") { // File
                    $form->{$form_field->vars->name}->input($form_field->vars->block_prefixes[1])->value('')->label($titre)->wrap('p')->required($form_field->vars->required);
                } elseif ($form_field->vars->block_prefixes[1] == "checkbox") { // Checkbox
                    if(true == $form_field->vars->checked){
                        $form_field->vars->attr = array_merge(array('checked' => 'checked'),$form_field->vars->attr); 
                    }
                    $form->{$form_field->vars->name}->input($form_field->vars->block_prefixes[1])->value('1')->label($titre)->attributes((array) $form_field->vars->attr)->required($form_field->vars->required);
                } elseif ($form_field->vars->block_prefixes[1] == "choice") { // Selects or Radios
                    $choices = array();
                    foreach ($form_field->vars->choices as $choice) {
                        if (true === is_object($choice->data)) { // Entity
                            $choices[$choice->value] = $choice->label;
                        } else {
                            $choices[$choice->value] = $choice->label;
                        }
                    }
                    $form->{$form_field->vars->name}->select($choices)->label($titre)->value($form_field->vars->value)->required($form_field->vars->required);
                } else {
                    foreach ($form_field->vars as $var) {
                        //var_dump($var);
                    }
                }
            }elseif("repeated" == $form_field->vars->block_prefixes[0]){
                foreach($form_field->children as $children){
                    $titre = (!empty($children->vars->label) ? $children->vars->label : "");
                    $form->{$children->vars->name}->input('password')->label($titre)->wrap('p')->required($form_field->vars->required);
                }
            }elseif("collection" == $form_field->vars->block_prefixes[1]){
                foreach($form_field->vars->prototype->children as $children){
                    $titre = (!empty($children->vars->label) ? $children->vars->label : "");
                    $form->{$children->vars->name}->input('text')->label($titre)->wrap('p')->required($form_field->vars->required);
                }
            }else{  
                foreach($form_field->children as $children){
                    $titre = (!empty($children->vars->label) ? $children->vars->label : "");
                    $form->{$children->vars->name}->input('text')->label($titre)->wrap('p')->required($form_field->vars->required);
                }
            }
        }
        return $form;
    }

}

// End2
