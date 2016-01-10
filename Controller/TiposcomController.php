<?php
namespace Controller;
use \App\Controller;
use \App\Session;
use \Model\TipocomModel;
use \Clases\TipoCompra;
class TiposcomController extends Controller
{
    private $modelo;
    function __construct() {
        parent::__construct();
        $this->modelo= new TipocomModel();
    }
    public function index(){
        if($this->checkUser()){
            $this->redirect(array("index.php"),array(
                "tiposcom" => $this->modelo->obtenerTodos()
            )); 
        }    
    }
    public function add(){
        if($this->checkUser()){
            if (isset($_POST['btnaceptar'])) {
                if($this->checkDates()) { 
                    $tc= new TipoCompra(0, $_POST['txtnom']);
                    $id = $this->modelo->guardame($tc);
                    Session::set("msg",(isset($id)) ? "Tipo de Compra Creada" : Session::get('msg'));
                    header("Location:index.php?c=tiposcom&a=index");
                    exit();
                }
            }
            $this->redirect(array('add.php'));
        }
    }   
    public function edit(){        
        if($this->checkUser()){
            Session::set("id",$_GET['p']);
            if (Session::get('id')!=null && isset($_POST['btnaceptar'])){                             
                if($this->checkDates()) {     
                    $tc= new TipoCompra($_POST['hid'], $_POST['txtnom']);
                    $id = $this->modelo->modificame($tc);
                    Session::set("msg",(isset($id)) ? "Tipo de Compra Editada" : Session::get('msg'));
                    header("Location:index.php?c=tiposcom&a=index"); 
                    exit();
                }
            }
            $this->redirect(array('edit.php'),array(
                "tipocom" => $this->modelo->obtenerPorId(Session::get('id'))
            ));
        }       
    }
    public function delete(){
        if($this->checkUser()){
            if (isset($_GET['p'])){
                $tc = $this->modelo->obtenerPorId($_GET['p']); 
                $id = $this->modelo->eliminame($tc);
                Session::set("msg", (isset($id)) ? "Tipo de Compra Borrada" : "No se pudo borrar el tipo");
                header("Location:index.php?c=tiposcom&a=index");
            }                           
        }
        else {
            Session::set("msg","Debe ser administrador para acceder.");
            $this->redirect(array('Main','index.php'));
        }
    }
    private function checkDates(){
        if(empty($_POST['txtnom'])){
            Session::set("msg","Ingrese los datos obligatorios (*) para continuar.");
            return false;
        }
        else{
            return true;
        }
    }
    private function checkUser(){
        if(Session::get("log_in")!= null and Session::get("log_in")->getRol()->getNombre() == "admin"){
            return true;
        }
        else {
            Session::set("msg","Debe ser administrador para acceder.");
            $this->redirect(array('Main','index.php'));
        }
    }
}