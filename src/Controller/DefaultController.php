<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use App\Repository\UserRepository;

class DefaultController extends AbstractController{

  public function index(SessionInterface $session){

    if(!$session->get('current_user')){
        return $this->render('login.html.twig', []);
    }else{
      return $this->redirect("/profile");
    }

    return $this->render('index.html.twig', []);
  }


  public function login(Request $request, SessionInterface $session, UserRepository $userRepo){
      $hashed_secrets = [
        "julien" => '$2y$10$/W4/y4wKTg4YqymKBGwo6eiOH/uicSbx6d0WDjGwHk8vEJUndpcx6',
        "bob" => '$2y$10$QskYYXFt9bQueGHczrbqwuG2olId3XLc5RTZnK8qHN0nSQA4UzAz6',
        "claire" => '$2y$10$l.QvnBgbntyCCsi1h9Ed2uS5hLeHfQPUg0842H2Pvw6jVLDjDV8zu',
      ];

      try{

        if(    $request->request->has('username') 
            && $request->request->has('password') ){
          $username = $request->request->get('username');
          $password = $request->request->get('password');

          $user_in_db = $userRepo->findOneBy(['username' => $username]);

          if(is_null($user_in_db))
            throw new AuthenticationException("Account not found");

          if(!password_verify($password, $user_in_db->getPassword()))
            throw new AuthenticationException("Wrong password");

          $session->set('current_user', $username); //the use is logged in !
          return $this->redirect("/profile");
        }else{
          throw new \Exception("Missing a username or password");
        }

      }catch (\Exception $e){
        return $this->render('login.html.twig', ["error"  => $e->getMessage()]);
      }

  }

  public function logout(Request $request, SessionInterface $session){
    $session->remove('current_user'); //the use is logged out !
    return $this->redirect("/");
  }

}