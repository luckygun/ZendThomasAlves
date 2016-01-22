<?php
namespace Jeux\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Jeux\Model\Jeux;          // <-- Add this import
 use Jeux\Form\JeuxForm;

 class JeuxController extends AbstractActionController
 {
     protected $jeuxTable;
     public function indexAction()
     {
         return new ViewModel(array(
             'jeuxs' => $this->getJeuxTable()->fetchAll(),
         ));
     }

     public function addAction()
     {
                  $form = new JeuxForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $jeux = new Jeux();
             $form->setInputFilter($jeux->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $jeux->exchangeArray($form->getData());
                 $this->getJeuxTable()->saveJeux($jeux);

                 // Redirect to list of jeuxs
                 return $this->redirect()->toRoute('jeux');
             }
         }
         return array('form' => $form);

     }

     public function editAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('jeux', array(
                 'action' => 'add'
             ));
         }

         // Get the Jeux with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             $jeux = $this->getJeuxTable()->getJeux($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('jeux', array(
                 'action' => 'index'
             ));
         }

         $form  = new JeuxForm();
         $form->bind($jeux);
         $form->get('submit')->setAttribute('value', 'Edit');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($jeux->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getJeuxTable()->saveJeux($jeux);

                 // Redirect to list of jeuxs
                 return $this->redirect()->toRoute('jeux');
             }
         }

         return array(
             'id' => $id,
             'form' => $form,
         );
     }

     public function deleteAction()
     {  
         
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('jeux');
         }

         $request = $this->getRequest();
         if ($request->isPost()) {
             $del = $request->getPost('del', 'No');

             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $this->getJeuxTable()->deleteJeux($id);
             }

             // Redirect to list of jeuxs
             return $this->redirect()->toRoute('jeux');
         }

         return array(
             'id'    => $id,
             'jeux' => $this->getJeuxTable()->getJeux($id)
         );
     }
     
     public function getJeuxTable()
     {
         if (!$this->jeuxTable) {
             $sm = $this->getServiceLocator();
             $this->jeuxTable = $sm->get('Jeux\Model\JeuxTable');
         }
         return $this->jeuxTable;
     }
     public function surpriseAction(){
         
     }
 }
 ?>