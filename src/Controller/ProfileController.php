<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\Portrait;
use App\Repository\PortraitRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController{

  private $current_user;

  public function __construct(SessionInterface $session, UserRepository $userRepo){
    if(!$session->get('current_user')){
        throw new AccessDeniedException("You must be logged");
    }

    $this->current_user = $userRepo->findOneBy(['username' => $session->get('current_user')]);
  }

  private function getCurrentUser(){
    return $this->current_user;
  }

  public function profile(SessionInterface $session){
    
    $user = $this->getCurrentUser();

    $photos = $user->getPortraits();

    return $this->render('profile.html.twig', ["user"  => $user,
                                               "photos" => $photos
                                             ]);

  }

  public function add_photo(Request $request, 
                            SessionInterface $session, 
                            SluggerInterface $slugger,
                            EntityManagerInterface $em
                          ){
    
    $user = $this->getCurrentUser();

    $uploadedFile = $request->files->get("photofile");

    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $slugger->slug($originalFilename);
    $fileName = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

    $target_directory = "./uploads";

    $uploadedFile->move(
      $target_directory,
      $fileName
    );

    $portrait = new Portrait();
    $portrait->setTitle($request->request->get("title") ?? "Untitled photo");
    $portrait->setDescription($request->request->get("description") ?? "No description");
    $portrait->setFilename("/uploads/".$fileName);
    $portrait->setOwner($user);

    $em->persist($portrait);
    $em->flush();
    
  
    return $this->redirect("/");

  }
}