<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;


class AlbumController extends AbstractActionController {

	protected $albumTable;

	public function indexAction() {
		return new ViewModel(array(
			'albums' => $this->getAlbumTable()->fetchAll(),
		));
	}

	public function addAction() {
		$form = new AlbumForm();
		$form->get('submit')->setValue('Add');

		$request = $this->getRequest();
		// Procesar post request
		if ($request->isPost()) {
			$album = new Album();
			$form->setInputFilter($album->getInputFilter());
			$form->setData($request->getPost());

			if ($form->isValid()) {
				// Le paso los datos a la instancia del album
				$album->exchangeArray($form->getData());
				// Connexion con la bd para guardar el Album
				$this->getAlbumTable()->saveAlbum($album);

				return $this->redirect()->toRoute('album');
			}
		}
		// Enviar formulario
		return array('form' => $form);
	}

	public function editAction() {
        // Leer parámetro en el placeholder id dentro de la ruta definida para este controlador
		$id = (int) $this->params()->fromRoute('id',0);
		if (!$id) {
            // Si no viene id, redirigir a la acción agregar album
			return $this->redirect()->toRoute('album', array(
				'action' => 'add'
			));
		}

        // Intento obtener el album el cual se quiere editar
		try {
			$album = $this->getAlbumTable()->getAlbum($id);
		} catch(\Exception $ex) {
            // Si no existe se redirige
			return $this->redirect()->toRoute('album', array(
				'action' => 'index'
			));
		}

        // Preparo el nuevo formulario para mostrar en la edición
        $form = new AlbumForm();
            // The form’s bind() method attaches the model to the form. This is used in two ways:
            //  • When displaying the form, the initial values for each element are extracted from the model.
            //  • After successful validation in isValid(), the data from the form is put back into the model.
            $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        // En caso de que el formulario ya se envió
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($form->getData());

                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );

	}

	public function deleteAction() {
        // Capturamos el id de la transacción, viene como parámetro
        $id = (int) $this->params()->fromRoute('id', 0);
        // Si no existe redirigimos a la ruta album
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        // Caso de que el formulario ya se envío por Post
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Capturo la confirmación de borrado
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                // Borrar
                $this->getAlbumTable()->deleteAlbum($id);
            }
            // Redirigir a ruta album
            return $this->redirect()->toRoute('album');
        }

        // Envio parámetros a la vista para generar el formulario
        return array(
            'id' => $id,
            'album' => $this->getAlbumTable()->getAlbum($id),
        );

	}

	public function getAlbumTable()
	{
		if (!$this->albumTable) {
			$sm = $this->getServiceLocator();
			$this->albumTable = $sm->get('Album\Model\AlbumTable');
		}
		return $this->albumTable;
	}


}