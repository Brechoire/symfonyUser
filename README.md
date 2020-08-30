# symfonyUser!

symfonyUser est un projet de départ qui propose un système de connexion, inscription et profil pour commencer un site rapidement. Il est très facilement modifiable grâce à l'utilisation de service.


# Installation

   git clone git@github.com:Brechoire/symfonyUser.git

## Configuration .env.local (ou .env pour prod)

    MAILER_URL=smtp://localhost:1025 // Maildev ou mailcatcher
    DATABASE_URL=mysql://root:password@127.0.0.1:3306/bddname // base de données

## SendMailService.php
Vous souhaitez pouvoir envoyer un mail lorsqu'un utilisateur s'inscrit ? Où pour tout autre chose ? 

```
<?php    
namespace App\Service;  
class TestService  
{  
  /**  
  * @var SendMailService  
  */  
  private $mailService;  
  
  /**  
  * TestService constructor. * @param SendMailService $mailService  
  */  
  public function __construct(SendMailService $mailService)  
    {  
        $this->mailService = $mailService;  
    }  
  
    public function newRegister()  
    {  
        // code  
  
  $this->mailService->sendMail(  
            'Titre du mail',  
            'contact@mysite.fr',  
            $userEmail,  
            'admin/new_user.html.twig', [  
                'param1' => $param1,  
                'param2' => $param2,  
                'param3' => $param3,  
            ]  
        );  
  
        // code  
  }  
}
```


