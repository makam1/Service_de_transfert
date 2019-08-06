<?php
namespace App\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class UtilisateurControllerTest extends WebTestCase
{
   public function testAjoutAdmin()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'makam12',
            'PHP_AUTH_PW'=>'passer123'
        ] 
        );
        $client->request('POST', '/api/admin',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"adminpar",
            "password":"passer123",
            "nom":"doudou",
            "email":"dmg@gmail.com",
            "telephone":785056658,
            "imageName":"image.png",
            "Partenaire":1
            }'
            
    );
    $a=$client->getResponse();
    var_dump($a);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    }


   public function testAjoutUser()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'makam12',
            'PHP_AUTH_PW'=>'passer123'
        ]
        );
        $client->request('POST', '/api/user',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"user123",
            "password":"passer123",
            "nom":"test12",
            "email":"admin@gmail.com",
            "telephone":785056790,
            "imageName":"image.png",
            "Partenaire":"1"
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    } 
     public function testLogin()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'makam12',
            'PHP_AUTH_PW'=>'passer123'
        ]
        );
        $client->request('POST', '/api/connexion',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"makam12",
            "password":"passer123"
            
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(200,$client->getResponse()->getStatusCode());
    } 
    public function testCreer()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'makam12',
            'PHP_AUTH_PW'=>'passer123'
        ]
        );
        $client->request('POST', '/api/creer',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"adminsup1",
            "password":"passer123",
            "nom":"adminsup",
            "email":"admin@gmail.com",
            "imageName":"image.png",
            "telephone":785056675
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    } 
      public function testAjoutCaissier()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'makam12',
            'PHP_AUTH_PW'=>'passer123'
        ]
        );
        $client->request('POST', '/api/caissier',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"caissier1",
            "password":"passer123",
            "nom":"adminsup",
            "email":"admin@gmail.com",
            "telephone":785056123,
            "imageName":"image.png",
            "Partenaire":"1"
            }'
    );
    $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    } 
}