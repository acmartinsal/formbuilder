<?php

namespace Smartinsalmeida;

/**
 * Description of Form
 *
 * @author S
 */
class Form extends \Micro\Form {

    protected function render_field() {
        $html = "\n";
        $html .= '<div class="form-group">';
        
        if (!$this->attributes) {
            $this->attributes = array();
        }

        // Configure the attributes
        if ($this->type != 'checkbox') {
            $this->attributes = $this->attributes + array('class' => 'form-control');
        }

        // Get the current value
        if ($this->value !== NULL) {
            $value = $this->value;
        } else {
            $value = $this->validation->value($this->field);
        }

        if ($this->label && $this->type != 'checkbox') {
            //$html .= '<div class="col-sm-6">';
            $html .= '<label class="col-sm-3 control-label" for="' . $this->field . '">' . $this->label . ' ' .(isset($this->attributes['required']) ? '*': '').'</label><div class="col-sm-9">';
        }else{
            $html .= '<div class="col-sm-offset-3 col-sm-9"><div class="checkbox"><label>';
        }
        
        if ($this->type == 'select') {
            $html .= \Micro\HTML::select($this->field, $this->options, $value, $this->attributes);
        } elseif ($this->type == 'textarea') {
            $html .= \Micro\HTML::tag('textarea', $value, $this->attributes);
        } else {
            
            // Input field
            $this->attributes = $this->attributes  + array('type' => $this->type, 'value' => $value);

            $html .= \Micro\HTML::tag('input', FALSE, $this->attributes);
            if ($this->type == 'checkbox') {
                $html .= " ".$this->label;
                $html .= '</label></div>';
            }
        }
        $html .= '</div>';
        $html .= '</div>';
        
        // If there was a validation error
        if ($error = $this->validation->error($this->field)) {
            if (isset($this->attributes['class'])) {
                $this->attributes['class'] .= ' error';
            } else {
                $this->attributes['class'] = $this->field . ' ' . $this->type . ' error';
            }

            $html .= "\n<div class=\"error_message\">$error</div>";
        }

        if ($this->tag) {
            $html = \Micro\HTML::tag($this->tag, $html . "\n") . "\n";
        }

        return $html;
    }
    
    public function required($required)
    {
        if(false !== $required)
        {
            $this->attributes(array('required' => 'required'));
        }
    }
}

