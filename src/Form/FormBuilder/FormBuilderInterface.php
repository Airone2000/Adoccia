<?php

namespace App\Form\FormBuilder;

interface FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options);
    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options);
}