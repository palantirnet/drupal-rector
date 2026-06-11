<?php

// Not a *_requirements_alter implementation — left untouched.
function mymodule_form_alter(array &$form): void {
  $form['#title'] = t('Title');
}

// Wrong signature: the $requirements parameter must be passed by reference.
function mymodule_requirements_alter(array $requirements): void {
  // Not a real hook implementation.
}

// Wrong arity: a real implementation takes exactly one parameter.
function othermodule_requirements_alter(array &$requirements, $extra): void {
  $requirements['php']['title'] = t('PHP version');
}
