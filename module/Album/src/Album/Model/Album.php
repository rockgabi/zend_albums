<?php

namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Album implements InputFilterAwareInterface {
	public $id;
	public $artist;
	public $title;
	protected $inputFilter;

	// Implmementa funcion necesaria para trabajar con Zend\Db’s TableGateway
	public function exchangeArray($data) {
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->artist = (isset($data['artist'])) ? $data['artist'] : null;
		$this->title = (isset($data['title'])) ? $data['title'] : null;
	}

    // Metodo necesario para bindear este modelo a un formulario
    public function getArrayCopy() {
        return get_object_vars($this);
    }

	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception('Not used');
	}

	// Filtros relacionados con este modelo
	public function getInputFilter() {
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();

			$inputFilter->add($factory->createInput(array(
				'name' => 'id',
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name' => 'artist',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 1,
							'max' => 100,
						),
					),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name' => 'title',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 1,
							'max' => 100,
						),
					),
				),
			)));

			$this->inputFilter =  $inputFilter;
		}

		return $this->inputFilter;
	}

}