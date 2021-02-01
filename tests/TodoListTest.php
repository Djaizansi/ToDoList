<?php

namespace App\Tests;

use App\Entity\Item;
use App\Entity\TodoList;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use DateTime;
use DateInterval;

class TodoListTest extends TestCase
{
    private $user;
    private $item;
    private $todolist;

    protected function setUp(): void
    {
        parent::setUp();

        $today = new DateTime('now');
        $myToday = new DateTime('now');
        $birthday = $today->sub(new DateInterval('P30Y'))->format('Y-m-d');
        $createdItemAt = $myToday->add(new DateInterval('PT45M'));

        $this->user = new User(
            'Jallali',
            'Youcef',
            'youcef.jallali@gmail.com',
            'azfezfez3',
            "$birthday"
        );

        $this->item = new Item(
            'Exercice1 à faire',
            'petit exercice à faire pour les vacances',
            $createdItemAt
        );

        $this->todolist = $this->getMockBuilder(TodoList::class)
            ->onlyMethods(['getSizeTodoList','getLastItem','sendEmailUser'])
            ->getMock();

        $this->todolist->setUser($this->user);
        $this->todolist->expects($this->any())->method('getLastItem')->willReturn($this->item);
    }

    public function testCanAddItemNominal()
    {
        $this->todolist->expects($this->any())->method('getSizeTodoList')->willReturn('1');

        $canAddItem = $this->todolist->canAddItem($this->item);

        $this->assertNotNull($canAddItem);
        $this->assertEquals('Exercice1 à faire', $canAddItem->getName());
    }

    public function testSendEmailToUser()
    {
        $this->todolist->expects($this->once())->method('getSizeTodoList')->willReturn('8');

        $send = $this->todolist->numberItemAlert();

        $this->assertTrue($send);
    }

    public function testCanAddMaxItem()
    {
        $this->todolist->expects($this->any())->method('getSizeTodoList')->willReturn('10');
        $this->expectException('Exception');
        $this->expectExceptionMessage('La todo list possède beaucoup d\'item');

        $canAddItem = $this->todolist->canAddItem($this->item);

        $this->assertTrue($canAddItem);
    }
}