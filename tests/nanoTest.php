<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers nano
 */
final class nanoTest extends TestCase
{

    public function testCanReplaceString(): void
    {
      $nano = new nano();
      $nano->setTemplate(
        "<p>
          {user.greeting()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>"
      );
      $nano->setData($this->_getTestData());

      $this->assertEquals(
        '<p>
          Hello Anon Ymous! 
          Your account is <strong>active</strong> 
          
        </p>', 
        $nano->render()
      );   
    }

    public function testCanReplaceStringAndShowEmpty(): void
    {
      $nano = new nano();
      $nano->setTemplate(
        "<p>
          {user.greeting()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>"
      );
      $nano->setData($this->_getTestData());
      $nano->setShowEmpty(true);

      $this->assertEquals(
        '<p>
          Hello Anon Ymous! 
          Your account is <strong>active</strong> 
          {user.nonexistingnode}
        </p>', 
        $nano->render()
      );   
    }

    public function testCanReplaceStringWithUnknownFunction(): void
    {
      $nano = new nano();
      $nano->setTemplate(
        "<p>
          {user.greetingTwo()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>"
      );
      $nano->setData($this->_getTestData());

      $this->assertEquals(
        '<p>
           Anon Ymous! 
          Your account is <strong>active</strong> 
          
        </p>', 
        $nano->render()
      );   
    }

    public function testCanReplaceStringAndShowEmptyWithUnknownFunction(): void
    {
      $nano = new nano();
      $nano->setTemplate(
        "<p>
          {user.greetingTwo()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>"
      );
      $nano->setData($this->_getTestData());
      $nano->setShowEmpty(true);

      $this->assertEquals(
        '<p>
          {user.greetingTwo()} Anon Ymous! 
          Your account is <strong>active</strong> 
          {user.nonexistingnode}
        </p>', 
        $nano->render()
      );   
    }

    public function testCanReplaceStringEmptyTemplate(): void
    {
      $nano = new nano();
      $nano->setData($this->_getTestData());
      $nano->setShowEmpty(true);

      $this->assertEquals(
        '', 
        $nano->render()
      );   
    }

    public function testCanReplaceStringEmptyData(): void
    {
      $nano = new nano();
      $nano->setTemplate(
        "<p>
          {user.greeting()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>"
      );
      $nano->setShowEmpty(true);

      $this->assertEquals(
        "<p>
          {user.greeting()} {user.first_name} {user.last name}! 
          Your account is <strong>{user.account.status}</strong> 
          {user.nonexistingnode}
        </p>", 
        $nano->render()
      );   
    }

    public function testCanReplaceStringAllEmpty(): void
    {
      $nano = new nano();

      $this->assertEquals(
        '', 
        $nano->render()
      );   
    }

    private function _getTestData(){
      return [
        "user" => [
          "login" => "demo",
          "first_name" => "Anon",
          "last name" => "Ymous",
          "account" => [
            "status" => "active",
            "expires_at" => "2016-12-31"
          ],
          "greeting" => function(){
            return 'Hello';
          }
        ]
      ];
    }
}
