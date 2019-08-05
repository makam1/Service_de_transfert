<?php
namespace App\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class UtilisateurControllerTest extends WebTestCase
{
   public function testAjoutAdmin()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'mak',
            'PHP_AUTH_PW'=>'123'
        ] 
        );
        $client->request('POST', '/api/admin',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"doudou",
            "password":"passer123",
            "nom":"doudou",
            "email":"dmg@gmail.com",
            "telephone":7850566587,
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
            'PHP_AUTH_USER'=>'mak',
            'PHP_AUTH_PW'=>'123'
        ]
        );
        $client->request('POST', '/api/user',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "login":"adPzq71t",
            "password":"passer123",
            "nom":test12,
            "email":"admin@gmail.com",
            "telephone":785056,
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
            'PHP_AUTH_USER'=>'mak',
            'PHP_AUTH_PW'=>'123'
        ]
        );
        $client->request('POST', '/api/connexion',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "username":"mak",
            "password":"123"
            
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(200,$client->getResponse()->getStatusCode());
    } 
    public function testCreer()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'mak',
            'PHP_AUTH_PW'=>'123'
        ]
        );
        $client->request('POST', '/api/creer',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "login":"adminsup",
            "password":"passer123",
            "nom":adminsup,
            "email":"admin@gmail.com",
            "telephone":785056,
            "Partenaire":"1"
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    } 
    public function testAjoutCaissier()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'mak',
            'PHP_AUTH_PW'=>'123'
        ]
        );
        $client->request('POST', '/api/caissier',[],[],
        ['CONTENT_TYPE'=>"application/json"],
        '{
            "login":"caissier",
            "password":"passer123",
            "nom":adminsup,
            "email":"admin@gmail.com",
            "telephone":785056,
            "Partenaire":"1"
            }'
    );
  $re =$client->getResponse();
    var_dump($re);
    $this->assertSame(201,$client->getResponse()->getStatusCode());
    } 
}