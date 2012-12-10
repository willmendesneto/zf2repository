<?php
namespace Application\Model;

use Core\Test\ModelTestCase;
use Application\Model\Post;
use Application\Model\Comment;
use Zend\InputFilter\InputFilterInterface;

/**
 * @group Model
 */
class CommentTest extends ModelTestCase
{
    public function testGetInputFilter()
    {
        $comment = new Comment();
        $if = $comment->getInputFilter();

        $this->assertInstanceOf("Zend\InputFilter\InputFilter", $if);
        return $if;
    }

    /**
     * @depends testGetInputFilter
     */
    public function testInputFilterValid($if)
    {
        $this->assertEquals(7, $if->count());

        $this->assertTrue($if->has('id'));
        $this->assertTrue($if->has('post_id'));
        $this->assertTrue($if->has('description'));
        $this->assertTrue($if->has('name'));
        $this->assertTrue($if->has('email'));
        $this->assertTrue($if->has('webpage'));
        $this->assertTrue($if->has('comment_date'));
    }

    /**
     * @expectedException Core\Model\EntityException
     * @expectedExceptionMessage Input inválido: email =
     */
    public function testInputFilterInvalido()
    {
        $comment = new Comment();
        //email deve ser um e-mail válido
        $comment->email = 'email_invalido';
    }

    /**
     * Teste de inserção de um comment válido
     */
    public function testInsert()
    {
        $comment = $this->addComment();

        $saved = $this->getTable('Application\Model\Comment')->save($comment);

        $this->assertEquals(
            'Comentário importante alert("ok");', $saved->description
        );
        $this->assertEquals(1, $saved->id);
    }

    /**
     * @expectedException Zend\Db\Adapter\Exception\InvalidQueryException
     */
    public function testInsertInvalido()
    {
        $comment = new Comment();
        $comment->description = 'teste';
        $comment->post_id = 0;

        $saved = $this->getTable('Application\Model\Comment')->save($comment);
    }

    public function testUpdate()
    {
        $tableGateway = $this->getTable('Application\Model\Comment');
        $comment = $this->addComment();

        $saved = $tableGateway->save($comment);
        $id = $saved->id;

        $this->assertEquals(1, $id);

        $comment = $tableGateway->get($id);
        $this->assertEquals('eminetto@coderockr.com', $comment->email);

        $comment->email = 'eminetto@gmail.com';
        $updated = $tableGateway->save($comment);

        $comment = $tableGateway->get($id);
        $this->assertEquals('eminetto@gmail.com', $comment->email);

    }

    /**
     * @expectedException Zend\Db\Adapter\Exception\InvalidQueryException
     * @expectedExceptionMessage Statement could not be executed
     */
    public function testUpdateInvalido()
    {
        $tableGateway = $this->getTable('Application\Model\Comment');
        $comment = $this->addComment();

        $saved = $tableGateway->save($comment);
        $id = $saved->id;

        $comment = $tableGateway->get($id);
        $comment->post_id = 10;
        $updated = $tableGateway->save($comment);
    }

    /**
     * @expectedException Core\Model\EntityException
     * @expectedExceptionMessage Could not find row 1
     */
    public function testDelete()
    {
        $tableGateway = $this->getTable('Application\Model\Comment');
        $comment = $this->addComment();

        $saved = $tableGateway->save($comment);
        $id = $saved->id;

        $deleted = $tableGateway->delete($id);
        $this->assertEquals(1, $deleted); //numero de linhas excluidas

        $comment = $tableGateway->get($id);
    }

    private function addPost()
    {
        $post = new Post();
        $post->title = 'Apple compra a Coderockr';
        $post->description = 'A Apple compra a <b>Coderockr</b><br> ';
        $post->post_date = date('Y-m-d H:i:s');

        $saved = $this->getTable('Application\Model\Post')->save($post);

        return $saved;
    }

    private function addComment()
    {
        $post = $this->addPost();
        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->description = 'Comentário importante <script>alert("ok");</script> <br> ';
        $comment->name = 'Elton Minetto';
        $comment->email = 'eminetto@coderockr.com';
        $comment->webpage = 'http://www.eltonminetto.net';
        $comment->comment_date = date('Y-m-d H:i:s');

        return $comment;
    }
}